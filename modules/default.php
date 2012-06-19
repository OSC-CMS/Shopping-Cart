<?php
    /*
    #####################################
    #  OSC-CMS: Shopping Cart Software.
    #  Copyright (c) 2011-2012
    #  http://osc-cms.com
    #  http://osc-cms.com/forum
    #  Ver. 1.0.0
    #####################################
    */

    $default = new osTemplate;
    $default->assign('tpl_path', _HTTP_THEMES_C);
    $default->assign('session', session_id());
    $main_content = '';

    if (os_check_categories_status($current_category_id) >= 1) {

        $error = CATEGORIE_NOT_FOUND;
        include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);

    } 
    else {


        if ($category_depth == 'nested') 
        {

            if (GROUP_CHECK == 'true') 
            {
                $group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
            }

            $category_query = "select
            cd.categories_description,
            cd.categories_name,
            cd.categories_heading_title,       
            c.categories_template,
            c.categories_image from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
            where c.categories_id = '".$current_category_id."'
            and cd.categories_id = '".$current_category_id."'
            ".$group_check."
            and cd.language_id = '".(int) $_SESSION['languages_id']."'";

            $category_query = osDBquery($category_query);

            $category = os_db_fetch_array($category_query, true);

            if (isset ($cPath) && preg_match('/_/', $cPath)) 
            {
                $category_links = array_reverse($cPath_array);


                for ($i = 0, $n = sizeof($category_links); $i < $n; $i ++) 
                {
                    if (GROUP_CHECK == 'true') 
                    {
                        $group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
                    }

                    $recursive_check="";


                    $categories_query = "select      cd.categories_description,
                    c.categories_id,
                    cd.categories_name,
                    cd.categories_heading_title,
                    c.categories_image,
                    c.parent_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
                    where c.categories_status = '1'
                    and c.parent_id = '".$category_links[$i]."'
                    and c.categories_id = cd.categories_id
                    ".$recursive_check."
                    ".$group_check."
                    and cd.language_id = '".(int) $_SESSION['languages_id']."'
                    order by sort_order, cd.categories_name";

                    $categories_query = osDBquery($categories_query);

                    if (os_db_num_rows($categories_query, true) < 1) {
                        // do nothing, go through the loop
                    } else 
                    {
                        break; // we've found the deepest category the customer is in

                    }
                }

            } 
            else 
            {

                if (GROUP_CHECK == 'true') 
                {
                    $group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
                }


                $recursive_check="";

                $categories_query = "select      cd.categories_description,
                c.categories_id,
                cd.categories_name,
                cd.categories_heading_title,
                c.categories_image,
                c.parent_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
                where c.categories_status = '1'
                and c.parent_id = '".$current_category_id."'
                and c.categories_id = cd.categories_id
                ".$recursive_check."
                ".$group_check."
                and cd.language_id = '".(int) $_SESSION['languages_id']."'
                order by sort_order, cd.categories_name";

                $categories_query = osDBquery($categories_query);
            }

            $rows = 0;

            while ($categories = os_db_fetch_array($categories_query, true)) 
            {

                $rows ++;

                $cPath_new = os_category_link($categories['categories_id'],$categories['categories_name']);

                $width = (int) (100 / MAX_DISPLAY_CATEGORIES_PER_ROW).'%';

                $image = '';

                if ($categories['categories_image'] != '' && is_file( dir_path('images').'categories/'.$categories['categories_image'] ) )
                {
                    $image = http_path('images').'categories/'.$categories['categories_image'];
                }
                else
                {
                    $image = http_path('images').'product_images/noimage.gif';
                }

                $categories_content[] = array ('CATEGORIES_NAME' => $categories['categories_name'], 'CATEGORIES_HEADING_TITLE' => $categories['categories_heading_title'], 'CATEGORIES_IMAGE' => $image, 'CATEGORIES_LINK' => os_href_link(FILENAME_DEFAULT, $cPath_new), 'CATEGORIES_DESCRIPTION' => $categories['categories_description']);

            }


            $new_products_category_id = $current_category_id;
            include (DIR_WS_MODULES.FILENAME_NEW_PRODUCTS);

            $featured_products_category_id = $current_category_id;
            include (DIR_WS_MODULES.FILENAME_FEATURED);

            $image = '';

            if ($category['categories_image'] != '' &&  is_file( dir_path('images').'categories/'.$category['categories_image'] ) ) 
            {
                $image = http_path('images').'categories/'.$category['categories_image'];
            }

            $default->assign('CATEGORIES_NAME', $category['categories_name']);
            $default->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);

            $default->assign('CATEGORIES_IMAGE', $image);
            $default->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);

            $default->assign('language', $_SESSION['language']);

            $categories_content = apply_filter('categories_content', $categories_content);
            $default->assign('module_content', $categories_content);

            // get default template
            if ($category['categories_template'] == '' or $category['categories_template'] == 'default') {
                $files = array ();
                if ($dir = opendir(_THEMES_C.'module/categorie_listing/')) 
                {
                    while (($file = readdir($dir)) !== false) 
                    {
                        if (is_file(_THEMES_C.'module/categorie_listing/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) 
                        {
                            $files[] = $file;
                        } 
                    } 
                    sort($files);

                    closedir($dir);
                }

                $category['categories_template'] = $files[0];
            }

            $default->caching = 0;
            $main_content = $default->fetch(CURRENT_TEMPLATE.'/module/categorie_listing/'.$category['categories_template']);
            $main_content = apply_filter('main_content', $main_content);
            $osTemplate->assign('main_content', $main_content);
        }
        elseif ($category_depth == 'products' || isset($_GET['manufacturers_id'])) 
        {

            //fsk18 lock
            $fsk_lock = '';
            if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
                $fsk_lock = ' and p.products_fsk18!=1';
            }
            // show the products of a specified manufacturer
            if (isset ($_GET['manufacturers_id'])) {
                if (isset ($_GET['filter_id']) && os_not_null($_GET['filter_id'])) {

                    // sorting query
                    /* $sorting_query = osDBquery("SELECT products_sorting,
                    products_sorting2 FROM ".TABLE_CATEGORIES."
                    where categories_id='".(int) $_GET['filter_id']."'");

                    $sorting_data = os_db_fetch_array($sorting_query,true);*/

                    $sorting_data =  get_categories_info($_GET['filter_id']);

                    my_sorting_products($sorting_data);
                    if (!$sorting_data['products_sorting'])
                        $sorting_data['products_sorting'] = 'pd.products_name';
                    $sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
                    // We are asked to show only a specific category
                    if (GROUP_CHECK == 'true') {
                        $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
                    }
                    $listing_sql = "select DISTINCT p.products_fsk18,
                    p.products_shippingtime,
                    p.products_model,
                    pd.products_name,
                    p.products_ean,
                    p.stock,
                    p.products_price,
                    p.products_tax_class_id,
                    m.manufacturers_name,
                    p.products_quantity,
                    p.products_image,
                    p.products_weight,
                    pd.products_short_description,
                    pd.products_description,
                    p.products_id,
                    p.manufacturers_id,
                    p.products_price,
                    p.products_vpe,
                    p.products_vpe_status,
                    p.products_vpe_value,
                    p.products_discount_allowed,
                    p.products_tax_class_id
                    from ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_MANUFACTURERS." m, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p left join ".TABLE_SPECIALS." s on p.products_id = s.products_id
                    where p.products_status = '1'
                    and p.manufacturers_id = m.manufacturers_id
                    and m.manufacturers_id = '".(int) $_GET['manufacturers_id']."'
                    and p.products_id = p2c.products_id
                    and pd.products_id = p2c.products_id
                    ".$group_check."
                    ".$fsk_lock."
                    and pd.language_id = '".(int) $_SESSION['languages_id']."'
                    and p2c.categories_id = '".(int) $_GET['filter_id']."'".$sorting;
                } else {
                    // We show them all
                    if (GROUP_CHECK == 'true') {
                        $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
                    }
                    $listing_sql = "select p.products_fsk18,
                    p.products_shippingtime,
                    p.products_model,
                    p.products_ean,
                    pd.products_name,
                    p.products_id,
					 p.stock,
                    p.products_price,
                    m.manufacturers_name,
                    p.products_quantity,
                    p.products_image,
                    p.products_weight,
                    pd.products_short_description,
                    pd.products_description,
                    p.manufacturers_id,
                    p.products_vpe,
                    p.products_vpe_status,
                    p.products_vpe_value,     
                    p.products_discount_allowed,
                    p.products_tax_class_id
                    from ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_MANUFACTURERS." m, ".TABLE_PRODUCTS." p left join ".TABLE_SPECIALS." s on p.products_id = s.products_id
                    where p.products_status = '1'
                    and pd.products_id = p.products_id
                    ".$group_check."
                    ".$fsk_lock."
                    and pd.language_id = '".(int) $_SESSION['languages_id']."'
                    and p.manufacturers_id = m.manufacturers_id
                    and m.manufacturers_id = '".(int) $_GET['manufacturers_id']."'";

                }
            } else {
                // show the products in a given categorie
                if (isset ($_GET['filter_id']) && os_not_null($_GET['filter_id'])) {

                    // sorting query
                    /* $sorting_query = osDBquery("SELECT products_sorting,
                    products_sorting2 FROM ".TABLE_CATEGORIES."
                    where categories_id='".$current_category_id."'");
                    $sorting_data = os_db_fetch_array($sorting_query,true);*/


                    $sorting_data = get_categories_info ($current_category_id);

                    my_sorting_products($sorting_data);
                    if (!$sorting_data['products_sorting'])
                        $sorting_data['products_sorting'] = 'pd.products_name';
                    $sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
                    // We are asked to show only specific catgeory
                    if (GROUP_CHECK == 'true') {
                        $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
                    }

                    $recursive_check="and p2c.categories_id = '".$current_category_id."'";
                    $recursive_table_categories="";

                    $listing_sql = "select p.products_fsk18,
                    p.products_shippingtime,
                    p.products_model,
                    p.products_ean,
                    pd.products_name,
                    p.products_id,
                    m.manufacturers_name,
                    p.products_quantity,
                    p.products_image,
					 p.stock,
                    p.products_weight,
                    pd.products_short_description,
                    pd.products_description,
                    p.manufacturers_id,
                    p.products_price,
                    p.products_vpe,
                    p.products_vpe_status,
                    p.products_vpe_value,                           
                    p.products_discount_allowed,
                    p.products_tax_class_id
                    from  ".$recursive_table_categories.TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_MANUFACTURERS." m, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p left join ".TABLE_SPECIALS." s on p.products_id = s.products_id
                    where p.products_status = '1'
                    and p.manufacturers_id = m.manufacturers_id
                    and m.manufacturers_id = '".(int) $_GET['filter_id']."'
                    and p.products_id = p2c.products_id
                    and pd.products_id = p2c.products_id
                    ".$group_check."
                    ".$fsk_lock."
                    and pd.language_id = '".(int) $_SESSION['languages_id']."' "
                    .$recursive_check
                    .$sorting;
                } else {

                    // sorting query
                    /*$sorting_query = osDBquery("SELECT products_sorting,
                    products_sorting2 FROM ".TABLE_CATEGORIES."
                    where categories_id='".$current_category_id."'");
                    $sorting_data = os_db_fetch_array($sorting_query,true);*/

                    $sorting_data =  get_categories_info($current_category_id);

                    my_sorting_products($sorting_data);
                    if (!$sorting_data['products_sorting'])
                        $sorting_data['products_sorting'] = 'pd.products_name';
                    $sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
                    // We show them all
                    if (GROUP_CHECK == 'true') {
                        $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
                    }

                    $recursive_check="and p2c.categories_id = '".$current_category_id."'";
                    $recursive_table_categories="";

                    $_where_filter = '';
                    $param_select = '';
                    $param_heving = '';
                    $param_where = '';

                    if (function_exists('get_where_param_filter'))
                    {
                        $_where_filter = get_where_param_filter();
                        if (!empty($_where_filter) ) 
                        {
                            $param_join = "left join 
    (
        select p3.product_id, count(*) as param_count from os_param p3 where ".$_where_filter['where']." group by p3.product_id

    ) t
    ON t.product_id = p.products_id";
    

                            $param_select = '';
                            $param_heving  = '';
                            $param_where = 'param_count ='.($_where_filter['count']);
                            $param_where .=' and';
                        }    
                    }


                    $listing_sql = "select p.products_fsk18,
                    p.products_shippingtime,
                    p.products_model,
                    p.products_ean,
                    pd.products_name,
                    m.manufacturers_name,
                    p.products_quantity,
                    p.products_image,
                    p.products_weight,
					 p.stock,
                    pd.products_short_description,
                    pd.products_description,
                    p.products_id,
                    p.manufacturers_id,
                    p.products_price,
                    p.products_vpe,
                    p.products_vpe_status,
                    p.products_vpe_value,                             
                    p.products_discount_allowed,
                    p.products_tax_class_id ".$param_select."
                    from  ".$recursive_table_categories.TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p left join ".TABLE_MANUFACTURERS." m on p.manufacturers_id = m.manufacturers_id
                    left join ".TABLE_SPECIALS." s on p.products_id = s.products_id ".$param_join."
                    where $param_where p.products_status = '1'
                    and p.products_id = p2c.products_id
                    and pd.products_id = p2c.products_id
                    ".$group_check."
                    ".$fsk_lock."                             
                    and pd.language_id = '".(int) $_SESSION['languages_id']."' "
                    .$recursive_check
                    .$sorting.$param_heving;
                }
            }
            // optional Product List Filter
            if (PRODUCT_LIST_FILTER > 0) {
                if (isset ($_GET['manufacturers_id'])) {
                    $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where p.products_status = '1' and c.categories_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '".(int) $_SESSION['languages_id']."' and p.manufacturers_id = '".(int) $_GET['manufacturers_id']."' order by cd.categories_name";
                } 
                else 
                {
                    $filterlist_sql = "select distinct m.manufacturers_id as id, m.manufacturers_name as name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_MANUFACTURERS." m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = '".$current_category_id."' order by m.manufacturers_name";
                }
                $filterlist_query = osDBquery($filterlist_sql);
                if (os_db_num_rows($filterlist_query, true) > 1) {
                    $manufacturer_dropdown = os_draw_form('filter', FILENAME_DEFAULT, 'get');
                    if (isset ($_GET['manufacturers_id'])) {
                        $manufacturer_dropdown .= os_draw_hidden_field('manufacturers_id', (int)$_GET['manufacturers_id']);
                        $options = array (array ('text' => TEXT_ALL_CATEGORIES));
                    } else {
                        $manufacturer_dropdown .= os_draw_hidden_field('cat', $_GET['cat']);
                        $options = array (array ('text' => TEXT_ALL_MANUFACTURERS));
                    }
                    $manufacturer_dropdown .= os_draw_hidden_field('sort', $_GET['sort']);
                    $manufacturer_dropdown .= os_draw_hidden_field(os_session_name(), os_session_id());

                    while ($filterlist = os_db_fetch_array($filterlist_query, true)) {
                        $options[] = array ('id' => $filterlist['id'], 'text' => $filterlist['name']);
                        if (isset($_GET['manufacturers_id']))
                        {
                            $manufacturer_sort .= '<a href="'.os_href_link(FILENAME_DEFAULT, 'manufacturers_id='.$_GET['manufacturers_id'].'&filter_id='.$filterlist['id']).'">' . $filterlist['name'] . '</a> ';
                        }
                        else
                        {
                            $manufacturer_sort .= '<a href="'.os_href_link(FILENAME_DEFAULT, 'cat='.$current_category_id.'&filter_id='.$filterlist['id']).'">' . $filterlist['name'] . '</a> ';
                        }

                    }
                    if (isset($_GET['cat']))
                    {
                        $manufacturer_sort .= '<a href="'.os_href_link(FILENAME_DEFAULT, 'cat='.$current_category_id).'">' . TEXT_ALL_MANUFACTURERS . '</a> ';
                    }
                    $manufacturer_dropdown .= os_draw_pull_down_menu('filter_id', $options, $_GET['filter_id'], 'onchange="this.form.submit()"');
                    $manufacturer_dropdown .= '</form>'."\n"; 
                }
            }

            // Get the right image for the top-right
            $image = http_path('images').'table_background_list.gif';
            if (isset ($_GET['manufacturers_id'])) 
            {
                $image = osDBquery("select manufacturers_image from ".TABLE_MANUFACTURERS." where manufacturers_id = '".(int) $_GET['manufacturers_id']."'");
                $image = os_db_fetch_array($image,true);
                $image = $image['manufacturers_image'];
            }
            elseif ($current_category_id) 
            {
                $cat_array = get_categories_info($current_category_id);
                //$image = osDBquery("select categories_image from ".TABLE_CATEGORIES." where categories_id = '".$current_category_id."'");
                //$image = os_db_fetch_array($image,true);
                //$image = $image['categories_image'];
                $image = $cat_array['categories_image'];
            }

            //print_r($listing_sql);
            include (DIR_WS_MODULES.FILENAME_PRODUCT_LISTING);

        } else { // default page
            $group_check = '';
            if (GROUP_CHECK == 'true') {
                $group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
            }
            $shop_content_query = osDBquery("SELECT
            content_title,
            content_heading,
            content_text,
            content_file
            FROM ".TABLE_CONTENT_MANAGER."
            WHERE content_group='5'
            ".$group_check."
            AND languages_id='".$_SESSION['languages_id']."'");
            $shop_content_data = os_db_fetch_array($shop_content_query,true);

            $default->assign('title', $shop_content_data['content_heading']);
            include (dir_path('includes').FILENAME_CENTER_MODULES);

            if ($shop_content_data['content_file'] != '') 
            {
                ob_start();
                if (strpos($shop_content_data['content_file'], '.txt')) echo '<pre>';
                include (_CATALOG.'media/content/'.$shop_content_data['content_file']);
                if (strpos($shop_content_data['content_file'], '.txt')) echo '</pre>';
                $shop_content_data['content_text'] = ob_get_contents();
                ob_end_clean();
            }

            $default->assign('greeting', os_customer_greeting());
            $default->assign('text', $shop_content_data['content_text']);
            $default->assign('language', $_SESSION['language']);

            global $os_action;

            if (isset($os_action['main_content']) && !empty($os_action['main_content']))
            {   
                global $os_action_plug;
                global $_plug_name;

                $_box = array();
                foreach ($os_action['main_content'] as $_tag => $priority)
                {
                    if (function_exists($_tag))
                    {
                        $_plug_name = $os_action_plug[ $_tag ];
                        $p->name = $os_action_plug[ $_tag ];
                        $p->group = $p->info[$p->name]['group'];
                        $p->set_dir();
                        $_box = $_tag();

                        if (!isset($_box['template']))
                        {
                            if (!empty($_box) && isset($_box['content']) )
                            {
                                $default->assign('BOX_TITLE', $_box['title']);
                                $default->assign('BOX_CONTENT', $_box['content']);
                                $_box_value = $default->fetch(CURRENT_TEMPLATE.'/boxes/box.html');
                                $osTemplate->assign($_tag, $_box_value);
                            }
                        }
                    }
                }
            }

            // set cache ID
            if (!CacheCheck()) {
                $default->caching = 0;
                $main_content = $default->fetch(CURRENT_TEMPLATE.'/module/main_content.html');
            } else {
                $default->caching = 1;
                $default->cache_lifetime = CACHE_LIFETIME;
                $default->cache_modified_check = CACHE_CHECK;
                $cache_id = $_SESSION['language'].$_SESSION['currency'].$_SESSION['customer_id'];
                $main_content = $default->fetch(CURRENT_TEMPLATE.'/module/main_content.html', $cache_id);
            }

            global $os_filter;

            $_main_content = apply_filter('main_content', $main_content);

            $osTemplate->assign('main_content', $_main_content );
            $osTemplate->assign('default', true);

        }
    }

?>
