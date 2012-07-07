<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

    $cache_error = array();

    function set_products_url_cache ()
    {
        $p_query = osDBquery("select products_id, products_page_url from ".DB_PREFIX."products where products_status = 1 and products_page_url IS NOT NULL and products_page_url <> ''");

        $p = '';

        if (os_db_num_rows($p_query,true)) 
        {
            while ($products = os_db_fetch_array($p_query,true))  
            {
                $p[$products['products_id']] = $products['products_page_url'];
            } 
        }

        save_cache('products_url', $p);

        return true;
    }

    // set_categories_count();
    function set_categories_count()
    {
        global $db;
        global $_flip_category_cache;   

        $categories_query = osDBquery("select categories_id, parent_id from ".TABLE_CATEGORIES." where categories_status = '1'");

        $category_cache = array();

        if (os_db_num_rows($categories_query,true)) 
        {
            while ($categories = os_db_fetch_array($categories_query,true))  
            {
                $category_cache [$categories['categories_id']] = $categories['parent_id'];	
            } 
        }

        $_flip_category_cache = flip_category_cache( $category_cache );
        $_array = array();

       // _os_count_products_in_category(0);

        if ( is_array( $category_cache ) &&  count($category_cache) > 0 )
        {
            foreach ($category_cache as $cat_id => $cat_parent)
            {
                $_c =  _os_count_products_in_category($cat_id);
                $db->query('update '.DB_PREFIX.'categories set categories_count="'.(int)$_c.'" where categories_id="'.(int)$cat_id.'";');
            }
        } 
    }

    function _os_count_products_in_category($category_id, $include_inactive = false) 
    {
        $products_count = 0;

        global $__count_category;
        global $_flip_category_cache;

        if (empty($__count_category))
        {
            $products_query = 'SELECT p2c.categories_id, count(*) AS total FROM '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_TO_CATEGORIES.' p2c WHERE p.products_id = p2c.products_id AND p.products_status = \'1\' GROUP BY p2c.categories_id';

            $products_query = osDBquery($products_query);

            while ($_products = os_db_fetch_array($products_query,true))
            {
                $__count_category[$_products['categories_id']] = $_products['total'];
            }

            if (empty($__count_category)) $_count_category = 1;
        }

        @$products_count += $__count_category[$category_id];

        if (!empty($_flip_category_cache))
        {
            if (isset($_flip_category_cache[$category_id]))
            { 
                foreach ($_flip_category_cache[$category_id] as $_val)
                {
                    $products_count += os_count_products_in_category($_val, $include_inactive);
                }
            }
        }

        return $products_count;
    }

    function set_categories_url_cache ()
    {
        $p_query = osDBquery("select categories_id, categories_url from ".DB_PREFIX."categories where categories_status = 1 and categories_url IS NOT NULL and categories_url <> ''");

        $p = '';

        if (os_db_num_rows($p_query,true)) 
        {
            while ($products = os_db_fetch_array($p_query,true))  
            {
                $p[$products['categories_id']] = $products['categories_url'];
            } 
        }

        save_cache('categories_url', $p);

        return true;
    }

    function set_faq_url_cache ()
    {
        $p_query = osDBquery("select faq_id, faq_page_url from ".DB_PREFIX."faq where status = 1 and faq_page_url IS NOT NULL and faq_page_url <> ''");

        $p = '';

        if (os_db_num_rows($p_query,true)) 
        {
            while ($products = os_db_fetch_array($p_query,true))  
            {
                $p[$products['faq_id']] = $products['faq_page_url'];
            } 
        }

        save_cache('faq_url', $p);

        return true;
    }

    function set_default_cache ($param = false)
    {
        $d = array();

        $p_query = osDBquery("select * from ".DB_PREFIX."currencies");

        $d['currencies'] = '';

        if (os_db_num_rows($p_query, true)) 
        {
            while ($value = os_db_fetch_array($p_query,true))  
            {
                $code = $value['code'];
                unset($value['code']);
                $d['currencies'][$code] = $value;
            } 
        }

        $d['tax_class_id'] = '';
        $p_query = osDBquery("select tax_class_id as class from ".DB_PREFIX."tax_class");

        if (os_db_num_rows($p_query, true)) 
        {
            while ($value = os_db_fetch_array($p_query,true))  
            {
                $d['tax_class_id'][$value['class']] = '';
            } 
        }

        $d['shipping_status'] = '';
        // shipping_status
        $p_query = osDBquery("SELECT shipping_status_name, shipping_status_image,language_id, shipping_status_id FROM ".TABLE_SHIPPING_STATUS);

        if (os_db_num_rows($p_query, true)) 
        {
            while ($value = os_db_fetch_array($p_query,true))  
            {
                $d['shipping_status'][$value['language_id']][$value['shipping_status_id']] = 
                array (
                'shipping_status_name' => $value['shipping_status_name'],
                'shipping_status_image' => $value['shipping_status_image']
                );
            } 

        }

        if ($param == 'true')
        {
            global $default_cache;
            $default_cache = $d;
        }

        save_cache('default', $d);

        return true;
    }

    function get_products_url_cache ()
    {
        if (is_file(_CACHE.'system/products_url.php'))
        {
            $fp = @fopen(_CACHE.'system/products_url.php', "rb");
            if ($fp) 
            {
                while (!feof($fp)) 
                {
                    $st .= fread($fp, 4096);
                }
            }
            @fclose($fp);
        }

        return unserialize ($st);
    }

    function get_cache ($filename)
    {
        if ($filename != 'configuration')
        {	

            if (is_file(_CACHE.'system/'.$filename.'.php') && DB_CACHE_PRO == 'true')
            {

                if (filesize(_CACHE.'system/'.$filename.'.php')==1)
                {
                    $_val = $filename.'_cache';
                    global $$_val;
                    $$_val = 0;

                }
                else
                {
                    $st = file_get_contents(_CACHE.'system/'.$filename.'.php'); 

                    $_val = $filename.'_cache';
                    global $$_val;

                    $$_val = unserialize ($st);
                    unset($st);
                }
            }
            else
            {
                switch($filename) 
                {
                    case 'category':
                        set_category_cache();
                        break;

                    case 'content_url':
                        set_content_url_cache(); 
                        break;                

                    case 'news_url':
                        set_news_url_cache();
                        break;				

                    case 'articles_url':
                        set_articles_url_cache();
                        break;

                    case 'topics_url':
                        set_topics_url_cache();
                        break;				

                    case 'faq_url':
                        set_faq_url_cache();
                        break;

                    case 'products_url':
                        set_products_url_cache();
                        break;	

                    case 'categories_url':
                        set_categories_url_cache();
                        break;				

                    case 'default':
                        set_default_cache('true');
                        break;
                }
            }
        }
    }

    function get_cache_all()
    {
        get_cache('default');
        get_cache('products_url');
        get_cache('categories_url');
        set_category_cache();
        get_cache('content_url');
        get_cache('news_url');
        get_cache('articles_url');
        get_cache('topics_url');
        get_cache('faq_url');

        return true;
    }

    function set_category_cache ()
    {
	    global $category_cache;

		$categories_query = osDBquery("select categories_id, parent_id from ".TABLE_CATEGORIES." where categories_status = '1' ORDER BY sort_order");

        $category_cache = array();

        if (os_db_num_rows($categories_query,true)) 
        {
            while ($categories = os_db_fetch_array($categories_query,true))  
            {
                $category_cache [$categories['categories_id']] = $categories['parent_id'];	
            } 
        }

        return true;
    }

    function set_content_url_cache ()
    {
        $p_query = osDBquery("select content_id, content_page_url from ".DB_PREFIX."content_manager where content_page_url <> '' and content_page_url IS NOT NULL");

        $p = '';
        if (os_db_num_rows($p_query,true)) 
        {
            while ($products = os_db_fetch_array($p_query,true))  
            {
                $p[$products['content_id']] = $products['content_page_url'];
            }    
        }

        save_cache('content_url', $p);

        return true;
    }

    function set_news_url_cache ()
    {
        $p_query = osDBquery("select news_id, news_page_url from ".DB_PREFIX."latest_news where news_page_url <> '' and news_page_url IS NOT NULL");

        $p = '';
        if (os_db_num_rows($p_query,true)) 
        {
            while ($products = os_db_fetch_array($p_query,true))  
            {
                $p[$products['news_id']] = $products['news_page_url'];
            } 
        }

        save_cache('news_url', $p);

        return true;
    }

    function set_topics_url_cache ()
    {
        $p_query = osDBquery("select topics_id, topics_page_url from ".DB_PREFIX."topics where topics_page_url <> '' and topics_page_url IS NOT NULL");
        $p = '';
        if (os_db_num_rows($p_query,true)) 
        {
            while ($products = os_db_fetch_array($p_query,true))  
            {
                $p[$products['topics_id']] = $products['topics_page_url'];
            } 
        }

        save_cache('topics_url', $p);

        return true;
    }

    function set_articles_url_cache ()
    {
        $p_query = osDBquery("select articles_id, articles_page_url from ".DB_PREFIX."articles where articles_page_url <> '' and articles_page_url IS NOT NULL");
        $p = '';
        if (os_db_num_rows($p_query,true)) 
        {
            while ($products = os_db_fetch_array($p_query,true))  
            {
                $p[$products['articles_id']] = $products['articles_page_url'];
            } 
        }

        save_cache('articles_url', $p);

        return true;
    }

    function save_cache ($filename, $cache, $method = 'file')
    {
        global $cache_error;

        $_cache = ''; 
        if (DB_CACHE_PRO == 'true')
        {
            if ($method == 'file')
            {
                if (empty($cache))
                {
                    $_cache = '0';
                }
                else
                {
                    $_cache = serialize($cache);
                }

                $fp = @fopen(_CACHE.'system/'.$filename.'.php', "w"); //??????? ???????????? ???? ????
                if ($fp) 
                {
                    @fwrite($fp, $_cache);
                }

                @fclose($fp);
            }	
        }
        else
        {
            $_val = $filename.'_cache';
            global $$_val;
            $$_val = $cache;
        }
        return true;
    }

    function set_all_cache ()
    {
        set_categories_url_cache();
        set_products_url_cache();
        //set_configuration_cache();
        set_category_cache();
        set_content_url_cache();
        set_news_url_cache();
        set_articles_url_cache();
        set_topics_url_cache();
        set_default_cache ();
        set_faq_url_cache();
        set_categories_count();

        return true;
    }

    function get_customers_status ($actualGroup)
    {
        global $customers_status_cache;

        if (empty($customers_status_cache))
        {
            $customers_status_query = "SELECT * FROM ".TABLE_CUSTOMERS_STATUS." WHERE customers_status_id = '".$actualGroup."' AND language_id = '".$_SESSION['languages_id']."'";
            $customers_status_query = osDBquery($customers_status_query);
            $customers_status_value = os_db_fetch_array($customers_status_query, true);
            $customers_status_cache[$_SESSION['languages_id']][$actualGroup] = $customers_status_value;

            return $customers_status_cache[$_SESSION['languages_id']][$actualGroup];
        }
        else
        {
            if (isset($customers_status_cache[$_SESSION['languages_id']][$actualGroup]))
            {
                return $customers_status_cache[$_SESSION['languages_id']][$actualGroup];
            }
            else
            {
                $customers_status_query = "SELECT * FROM ".TABLE_CUSTOMERS_STATUS." WHERE customers_status_id = '".$actualGroup."' AND language_id = '".$_SESSION['languages_id']."'";
                $customers_status_query = osDBquery($customers_status_query);
                $customers_status_value = os_db_fetch_array($customers_status_query, true);
                $customers_status_cache[$_SESSION['languages_id']][$actualGroup] = $customers_status_value;

                return $customers_status_cache[$_SESSION['languages_id']][$actualGroup];	
            }
        }


    }

    //function CheckSpecial($pID) 
    function get_checkspecial ($pID)
    {
        global $checkspecial_cache;
        global $checkspecial_count_cache;

        $pID = (int)$pID;

        if ($checkspecial_count_cache >= 1)
        {
            if (isset($checkspecial_cache[$pID])) return $checkspecial_cache[$pID]; else return false;
        }
        else
        {
            $product_query = "select products_id, specials_new_products_price from ".TABLE_SPECIALS." where status=1";

            $product_query = osDBquery($product_query);

            while ($product = os_db_fetch_array($product_query, true))
            { 
                $checkspecial_cache[$product['products_id']] = $product['specials_new_products_price'];
            }

            if (count($checkspecial_cache) == 0) 
            {
                $checkspecial_count_cache = 1;
            }
            else
            {
                $checkspecial_count_cache = count($checkspecial_cache)+1;
            }

            return @$checkspecial_cache[$pID];
        }
    }

    //function checkAttributes($pID) 
    function get_check_attributes ($pID)
    {
        global $get_check_attributes;
        global $_products_array;

        $__products_array = $_products_array;

        if (empty($get_check_attributes))
        {
            if (!empty($__products_array))
            {
                $sql = '';
                foreach ($__products_array as $val => $_val)
                {
                    if (empty($sql))
                    {
                        $sql .= $val;
                    }
                    else
                    {
                        $sql .= ','.$val;
                    }
                }

                $sql = 'patrib.products_id in ('.$sql.')';

                $product_query = osDBquery("select products_id, count(*) as total from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where ".$sql." and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."' group by products_id");

                if (os_db_num_rows($product_query,true)) 
                {
                    while ($products = os_db_fetch_array($product_query,true))  
                    {
                        $get_check_attributes[$_SESSION['languages_id']][$products['products_id']] = array('total' => $products['total']);
                    } 
                }
                else
                {
                    foreach ($__products_array as $val => $_val)
                    {
                        $get_check_attributes[$_SESSION['languages_id']][$val]  = array();
                    }
                }			 				 
            }


            return $get_check_attributes[$_SESSION['languages_id']][$pID];

        }
        else
        { 
            return $get_check_attributes[$_SESSION['languages_id']][$pID];
        }

    }

    function get_cart_products_cache ($products_id)
    {
        global $get_cart_products_cache;

        $cont = $_SESSION['cart']->contents;

        if (empty($get_cart_products_cache))
        { 
            $sql = '';
            foreach ($cont as $val => $_val)
            {
                if (empty($sql))
                {
                    $sql .= ' products_id = \''.$val.'\'';
                }
                else
                {
                    $sql .= ' or products_id = \''.$val.'\'';
                }
            }

            $product_query = osDBquery("select products_id, products_price, products_discount_allowed, products_tax_class_id, products_weight from ".TABLE_PRODUCTS." where ".$sql);

            if (os_db_num_rows($product_query,true)) 
            {
                while ($products = os_db_fetch_array($product_query,true))  
                {
                    $get_cart_products_cache[$products['products_id']] = $products;
                } 
            }
            if (empty($get_cart_products_cache)) $get_cart_products_cache = 1;

            if (isset($get_cart_products_cache[$products_id])) return $get_cart_products_cache[$products_id]; else return false;
        } 
        else
        {
            if (isset($get_cart_products_cache[$products_id])) return $get_cart_products_cache[$products_id]; else return false;
        }

    }

    function get_products_cache ($products_id)
    {
        global $get_products_cache;

        $cont = $_SESSION['cart']->contents;

        if (empty($get_products_cache))
        { 
            $sql = '';
            foreach ($cont as $val => $_val)
            {
                if (empty($sql))
                {
                    $sql .= ' p.products_id = \''.$val.'\'';
                }
                else
                {
                    $sql .= ' or p.products_id = \''.$val.'\'';
                }
            }
            $sql = '('.$sql.')';

            $product_query = osDBquery("select p.products_id, pd.products_name,p.products_shippingtime, p.products_image, p.products_model, p.products_price, p.products_discount_allowed, p.products_weight, p.products_tax_class_id from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where ".$sql." and pd.products_id = p.products_id and pd.language_id = '".$_SESSION['languages_id']."'");

            if (os_db_num_rows($product_query,true)) 
            {
                while ($products = os_db_fetch_array($product_query,true))  
                {
                    $get_products_cache[$_SESSION['languages_id']][$products['products_id']] = $products;
                } 
            }
            if (empty($get_products_cache)) $get_products_cache = 1;

            if (isset($get_products_cache[$_SESSION['languages_id']][$products_id])) return $get_products_cache[$_SESSION['languages_id']][$products_id];
            else return false;
        }
        else
        {

            return  @$get_products_cache[$_SESSION['languages_id']][$products_id];
        }

    }

    function get_short_description_cache ($products_id)
    {
        global $get_short_description;

        $cont = $_SESSION['cart']->contents;

        if (empty($get_short_description))
        { 
            $sql = '';
            foreach ($cont as $val => $_val)
            {
                if (empty($sql))
                {
                    $sql .= ' products_id = \''.$val.'\'';
                }
                else
                {
                    $sql .= ' or products_id = \''.$val.'\'';
                }
            }
            $sql = '('.$sql.')';


            $product_query = "select products_id, products_short_description from " . TABLE_PRODUCTS_DESCRIPTION . " where ".$sql." and language_id = '" . (int)$_SESSION['languages_id'] . "'";
            $product_query  = osDBquery($product_query);

            if (os_db_num_rows($product_query,true)) 
            {
                while ($products = os_db_fetch_array($product_query,true))  
                {
                    $get_short_description[$_SESSION['languages_id']][$products['products_id']] = $products;
                } 
            }

            return $get_short_description[$_SESSION['languages_id']][$products_id];
        }
        else
        {

            return $get_short_description[$_SESSION['languages_id']][$products_id];
        }

    }

    //function GetGroupPrice
    function get_groupprice ($products_id, $actualGroup, $qty)
    {
        global $get_groupprice;

        $cont = $_SESSION['cart']->contents;

        $products_id = (int) $products_id;

        if (empty($products_id)) return;

        if (empty($get_groupprice))
        { 
            $sql = '';
            foreach ($cont as $val => $_val)
            {
                if (empty($sql))
                {
                    $sql .= ' products_id = \''.$val.'\'';
                }
                else
                {
                    $sql .= ' or products_id = \''.$val.'\'';
                }
            }
            $sql = '('.$sql.')';


            $q = "SELECT products_id, max(quantity) as qty FROM ".TABLE_PERSONAL_OFFERS_BY.$actualGroup." WHERE ".$sql." AND quantity<='".$qty."'";
            $q  = osDBquery($q);

            if (os_db_num_rows($q,true)) 
            {
                while ($products = os_db_fetch_array($q,true))  
                {
                    $get_groupprice[$products['products_id']][$actualGroup][$qty] = $products['qty'];
                } 
            }

            return $get_groupprice[$products['products_id']][$actualGroup][$qty];
        }
        else
        {
            return $get_groupprice[$products['products_id']][$actualGroup][$qty];
        }

    }

    function get_categories_info ($id)
    {
        global $get_categories_info;

        if (empty($get_categories_info))
        {
            $group_check = '';
            if (GROUP_CHECK == 'true') 
            {
                $group_check = " and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
            }

            $cat_query = "select 
            c.categories_id,
            c.categories_image,
            c.products_sorting,
            c.products_sorting2,
            cd.categories_description,
            cd.categories_name,
            cd.categories_heading_title,
            cd.categories_meta_keywords,
            cd.categories_meta_description,
            cd.categories_meta_title,
            c.categories_status,
            c.listing_template
            from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
            where c.categories_id =cd.categories_id".
            $group_check."
            and cd.language_id = '".(int)$_SESSION['languages_id']."'";

            $cat_query  = osDBquery($cat_query);

            if (os_db_num_rows($cat_query,true)) 
            {
                while ($cat = os_db_fetch_array($cat_query,true))  
                {
                    $get_categories_info[$cat['categories_id']] = $cat;
                } 
            }
            if (isset($get_categories_info[$id])) return $get_categories_info[$id]; else false;
        }
        else
        {
            return @$get_categories_info[$id];
        }
    }

    //$category_cache
    function flip_category_cache ()
    {
        global $_flip_category_cache;
        global $category_cache;

        if (empty($_flip_category_cache))
        {
            $_flip_category_cache = os_array_flip ($category_cache);
            if (empty($_flip_category_cache)) $_flip_category_cache = array();
            return $_flip_category_cache;
        }
        else
        {
            return $_flip_category_cache;
        }
    }	

    //array_flip()
    function os_array_flip ($_array)
    {
        $_flip_category_cache = array();

        if (!empty($_array))
        {
            foreach ($_array as $_m => $_v)
            {
                $_flip_category_cache[$_v][] = $_m;
            }

            return $_flip_category_cache;
        }
    }  

    function os_status_count($status_id)
    {
        global $status_count_cache;

        if (empty($status_count_cache)) 
        {
            $orders_status_query = osDBquery('SELECT b.orders_status, count( * ) AS count FROM '.TABLE_ORDERS_STATUS.' a, '.TABLE_ORDERS.' b WHERE a.orders_status_id = b.orders_status GROUP BY b.orders_status');

            if (os_db_num_rows($orders_status_query,true)) 
            {
                while ($orders_status = os_db_fetch_array($orders_status_query,true)) 
                {
                    $status_count_cache[$orders_status['orders_status']] = $orders_status['count'];
                }
            }
            else
            {
                $status_count_cache = array();
            }

            return $status_count_cache[$status_id];
        }
        else
        { 
            if (!isset($status_count_cache[$status_id])) 
            {
                return 0;
            }
            else

                return $status_count_cache[$status_id];
        }
    }
?>