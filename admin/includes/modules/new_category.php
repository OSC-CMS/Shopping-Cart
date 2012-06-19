<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

    if ( ($_GET['cID']) && (!$_POST) ) {
        $category_query = os_db_query("select * from " .
        TABLE_CATEGORIES . " c, " .
        TABLE_CATEGORIES_DESCRIPTION . " cd
        where c.categories_id = cd.categories_id
        and c.categories_id = '" . $_GET['cID'] . "'");

        $category = os_db_fetch_array($category_query);

        $cInfo = new objectInfo($category);
    } elseif ($_POST) {
        $cInfo = new objectInfo($_POST);
        $categories_name = $_POST['categories_name'];
        $categories_heading_title = $_POST['categories_heading_title'];
        $categories_description = $_POST['categories_description'];
        $categories_meta_title = $_POST['categories_meta_title'];
        $categories_meta_description = $_POST['categories_meta_description'];
        $categories_meta_keywords = $_POST['categories_meta_keywords'];
    } else {
        $cInfo = new objectInfo(array());
    }

    $languages = os_get_languages();

    $text_new_or_edit = ($_GET['action']=='new_category_ACD') ? TEXT_INFO_HEADING_NEW_CATEGORY : TEXT_INFO_HEADING_EDIT_CATEGORY;
?>
<tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="pageHeading"><?php echo sprintf($text_new_or_edit, os_output_generated_category_path($current_category_id)); ?></td>
            </tr>
        </table></td>
</tr>

<tr>
    <td>
        <?php
            $form_action = ($_GET['cID']) ? 'update_category' : 'insert_category'; 

            echo os_draw_form('new_category', FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID'] . '&action='.$form_action, 'post', 'enctype="multipart/form-data" cf="true"'); ?>

        <span class="button"><button type="submit"  value="<?php echo BUTTON_SAVE; ?>" cf="false"><?php echo BUTTON_SAVE; ?></button></span>&nbsp;&nbsp;<a class="button" href="<?php echo os_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID']) . '"><span>' . BUTTON_CANCEL . '</span></a>'; ?>

        <div class="tabber">

        <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { 
                if($languages[$i]['status']==1) {

                    if (SEO_URL_CATEGORIES_GENERATOR == 'true') $seo_input_field = ' onKeyPress="onchange_categories_url()"  onChange="onchange_categories_url()"'; else $seo_input_field = '';

                ?>

                <!-- категории -->
                <div class="tabbertab">
                <h3><?php echo $languages[$i]['name']; ?></h3>
                <table border="0" class="main">

                <tr>
                <td valign="top" class="main"><?php echo TEXT_EDIT_CATEGORIES_NAME; ?></td>
                <td valign="top" class="main"><?php echo os_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : os_get_categories_name($cInfo->categories_id, $languages[$i]['id'])), 'id="categories_name"'.$seo_input_field); ?></td>
                </tr>

                <tr>
                <td valign="top" class="main"><?php echo TEXT_EDIT_CATEGORIES_HEADING_TITLE; ?></td>
                <td valign="top" class="main"><?php echo os_draw_input_field('categories_heading_title[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : os_get_categories_heading_title($cInfo->categories_id, $languages[$i]['id']))); ?></td>
                </tr>

                <tr>
                <td valign="top" class="main"><?php echo TEXT_EDIT_CATEGORIES_DESCRIPTION; ?></td>
                <td valign="top" class="main"><?php echo os_draw_textarea_field('categories_description[' . $languages[$i]['id'] . ']', 'soft', '103', '25', (($categories_description[$languages[$i]['id']]) ? stripslashes($categories_description[$languages[$i]['id']]) : os_get_categories_description($cInfo->categories_id, $languages[$i]['id']))); ?><br /><a href="javascript:toggleHTMLEditor('<?php echo 'categories_description[' . $languages[$i]['id'] . ']';?>');" class="code" ><?php echo TEXT_EDIT_E;?></a></td>
                </tr>

                <tr>
                <td valign="top" class="main"><?php echo TEXT_META_TITLE; ?></td>
                <td valign="top" class="main"><?php echo os_draw_input_field('categories_meta_title[' . $languages[$i]['id'] . ']',(($categories_meta_title[$languages[$i]['id']]) ? stripslashes($categories_meta_title[$languages[$i]['id']]) : os_get_categories_meta_title($cInfo->categories_id, $languages[$i]['id'])), 'size=50'); ?></td>
                </tr>

                <tr>
                <td valign="top" class="main"><?php echo TEXT_META_DESCRIPTION; ?></td>
                <td valign="top" class="main"><?php echo os_draw_input_field('categories_meta_description[' . $languages[$i]['id'] . ']', (($categories_meta_description[$languages[$i]['id']]) ? stripslashes($categories_meta_description[$languages[$i]['id']]) : os_get_categories_meta_description($cInfo->categories_id, $languages[$i]['id'])),'size=50'); ?></td>
                </tr>

                <tr>
                <td valign="top" class="main"><?php echo TEXT_META_KEYWORDS; ?></td>
                <td valign="top" class="main"><?php echo os_draw_input_field('categories_meta_keywords[' . $languages[$i]['id'] . ']',(($categories_meta_keywords[$languages[$i]['id']]) ? stripslashes($categories_meta_keywords[$languages[$i]['id']]) : os_get_categories_meta_keywords($cInfo->categories_id, $languages[$i]['id'])),'size=50'); ?></td>
                </tr>

                </table>
                </div>
                <?php } }?>
        <div class="tabbertab">
        <h3><?php echo TEXT_PRODUCTS_DATA; ?></h3>
        <table border="0" class="main">
        <tr>
        <td valign="top" class="main"><?php echo TEXT_EDIT_CATEGORY_URL; ?></td>
        <td valign="top" class="main"><?php echo os_draw_input_field('categories_url', $cInfo->categories_url, 'id="categories_url" size="40"'); ?></td>
        </tr>
        <tr>

        <?php
            $files=array();

            if ($dir= opendir(DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/product_listing/')){
                while  (($file = readdir($dir)) !==false) {
                    if (is_file(DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/product_listing/'.$file) and ($file !="index.html")){
                        $files[]=array(
                        'id' => $file,
                        'text' => $file);
                    }
                } 
                closedir($dir);
            }
            $default_array=array();
            // set default value in dropdown!
            if ($content['content_file']=='') {
                $default_array[]=array('id' => 'default','text' => TEXT_SELECT);
                $default_value=$cInfo->listing_template;
                $files=array_merge($default_array,$files);
            } else {
                $default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
                $default_value=$cInfo->listing_template;
                $files=array_merge($default_array,$files);
            }
            echo '<td valign="top" class="main">'.TEXT_CHOOSE_INFO_TEMPLATE_LISTING.':</td>';
            echo '<td><span class="main">'.os_draw_pull_down_menu('listing_template',$files,$default_value);
        ?>
        </span></td>
        </tr>
        <tr>
        <?php
            $files=array();
            if ($dir= opendir(DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/categorie_listing/')){
                while  (($file = readdir($dir)) !==false) {
                    if (is_file( DIR_FS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/module/categorie_listing/'.$file) and ($file !="index.html")){
                        $files[]=array(
                        'id' => $file,
                        'text' => $file);
                    }
                } 
                closedir($dir);
            }
            $default_array=array();
            if ($content['content_file']=='') {
                $default_array[]=array('id' => 'default','text' => TEXT_SELECT);
                $default_value=$cInfo->categories_template;
                $files=array_merge($default_array,$files);
            } else {
                $default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
                $default_value=$cInfo->categories_template;
                $files=array_merge($default_array,$files);
            }
            echo '<td valign="top" class="main">'.TEXT_CHOOSE_INFO_TEMPLATE_CATEGORIE.':</td>';
            echo '<td><span class="main">'.os_draw_pull_down_menu('categories_template',$files,$default_value);
        ?>
        </span></td>
        </tr>
        <tr>
        <?php
            $order_array='';
            $order_array=array(
            array('id' => 'p.products_price','text'=>TXT_PRICES),
            array('id' => 'pd.products_name','text'=>TXT_NAME),
            array('id' => 'p.products_ordered','text'=>TXT_ORDERED),
            array('id' => 'p.products_sort','text'=>TXT_SORT),
            array('id' => 'p.products_weight','text'=>TXT_WEIGHT),
            array('id' => 'p.products_quantity','text'=>TXT_QTY),
            array('id' => 'p.products_date_added','text'=>TXT_DATE_ADD)

            );
            $default_value='pd.products_name';
        ?>
        <td valign="top" class="main"><?php echo TEXT_EDIT_PRODUCT_SORT_ORDER; ?>:</td>
        <td valign="top" class="main"><?php echo os_draw_pull_down_menu('products_sorting',$order_array,$cInfo->products_sorting); ?></td>
        </tr>
        <tr>
        <?php
            $order_array='';
            $order_array=array(array('id' => 'ASC','text'=>'ASC (1 first)'),
            array('id' => 'DESC','text'=>'DESC (1 last)'));
        ?>
        <td valign="top" class="main"><?php echo TEXT_EDIT_PRODUCT_SORT_ORDER; ?>:</td>
        <td valign="top" class="main"><?php echo os_draw_pull_down_menu('products_sorting2',$order_array,$cInfo->products_sorting2); ?></td>
        </tr>
        <tr>
        <td valign="top" class="main"><?php echo TEXT_EDIT_SORT_ORDER; ?></td>
        <td valign="top" class="main"><?php echo os_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'); ?></td>
        </tr>
        <tr>
        <td class="main"><?php echo TEXT_EDIT_STATUS; ?>:</td>
        <td class="main"><?php echo os_draw_selection_field('status', 'checkbox', '1',$cInfo->categories_status==1 ? true : false); ?></td>
        </tr>

        <tr>
        <td valign="top" colspan="2" class="main"><?php echo TEXT_YANDEX_MARKET; ?></td>
        </tr>

        <tr>
        <td valign="top" class="main"><?php echo TEXT_YANDEX_MARKET_BID; ?></td>
        <td valign="top" class="main"><?php echo os_draw_input_field('yml_bid', $cInfo->yml_bid, 'size="2"'); ?></td>
        </tr>
        <tr>
        <td valign="top" class="main"><?php echo TEXT_YANDEX_MARKET_CBID; ?></td>
        <td valign="top" class="main"><?php echo os_draw_input_field('yml_cbid', $cInfo->yml_cbid, 'size="2"'); ?></td>
        </tr>

        </table>
        </div>
        <!-- info -->

        <!-- картинка -->
        <div class="tabbertab">
        <h3><?php echo TEXT_TAB_CATEGORIES_IMAGE; ?></h3>
        <table border="0">

        <tr>
        <td class="main" width="200" valign="top"><?php echo TEXT_EDIT_CATEGORIES_IMAGE; ?></td>
        <td class="top"><?php echo os_draw_file_field('categories_image') . '<br />' . os_draw_hidden_field('categories_previous_image', $cInfo->categories_image); ?>
        <?php
            if ($cInfo->categories_image) {
            ?>
            <br><img src="<?php echo DIR_WS_CATALOG.'images/categories/'.$cInfo->categories_image; ?>">
            <br><?php echo '&nbsp;' .$cInfo->categories_image;
                echo os_draw_selection_field('del_cat_pic', 'checkbox', 'yes').TEXT_DELETE;

        } ?>
        </td>
        </tr>

        </table>
        </div>
<?php 
         /*
               $array = array(1 => array(
               'tab_name' => '?????', 
               'tab_content' => '')
               );
         */
           $array = array();
           
           $array['param'] = array('category_id' => @$_GET['cID'] );
           
           $array = apply_filter('news_category_add_tabs', $array);
           
           if (isset($array['values']) && is_array($array['values']) )
           {
              foreach ($array['values'] as $num => $value)
              { 
                 echo '<div class="tabbertab"><h3>'.$value['tab_name'].'</h3>';
                 echo $value['tab_content'];
                 echo '</div>';
              }
           }
        ?>
        <?php

            if (GROUP_CHECK=='true') 
            {
                $customers_statuses_array = os_get_customers_statuses();
                $customers_statuses_array=array_merge(array(array('id'=>'all','text'=>TXT_ALL)),$customers_statuses_array);
            ?>
            <div class="tabbertab">
            <h3><?php echo ENTRY_CUSTOMERS_ACCESS; ?></h3>
            <table border="0">

            <tr>
            <td class="main" width="200" valign="top">        
            <?php

                for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {

                    if ($category['group_permission_'.$customers_statuses_array[$i]['id']] == 1) {

                        $checked='checked ';
                    } else {
                        $checked='';
                    }
                    echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.$customers_statuses_array[$i]['text'].'<br />';
                }
            ?>
        </td>
    </tr>
    <?php
    }
?>

          </table>
        </div>

</div>

   
</td>
      </tr>

        	<?php echo os_draw_hidden_field('categories_date_added', (($cInfo->date_added) ? $cInfo->date_added : date('Y-m-d'))) . os_draw_hidden_field('parent_id', $cInfo->parent_id); ?> 
        	<?php echo os_draw_hidden_field('categories_id', $cInfo->categories_id); ?> 

</form>