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

 defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' ); 
 
    if (@$_GET['sorting']) {
    switch ($_GET['sorting']){
        case 'sort'         : 
            $catsort    = 'c.sort_order ASC';
            $prodsort   = 'p.products_sort ASC';
            break;
        case 'sort-desc'    :
            $catsort    = 'c.sort_order DESC';
            $prodsort   = 'p.products_sort DESC';
        case 'name'         :
            $catsort    = 'cd.categories_name ASC';
            $prodsort   = 'pd.products_name ASC';
            break;
        case 'name-desc'    :
            $catsort    = 'cd.categories_name DESC';
            $prodsort   = 'pd.products_name DESC';
            break;                  
        case 'status'       :
            $catsort    = 'c.categories_status ASC';
            $prodsort   = 'p.products_status ASC';
            break;
        case 'status-desc'  :
            $catsort    = 'c.categories_status DESC';
            $prodsort   = 'p.products_status DESC';
            break;             
        case 'price'        :
            $catsort    = 'c.sort_order ASC';
            $prodsort   = 'p.products_price ASC';            
            break;
        case 'price-desc'   :
            $catsort    = 'c.sort_order ASC'; 
            $prodsort   = 'p.products_price DESC';            
            break;            
        case 'stock'        :
            $catsort    = 'c.sort_order ASC'; 
            $prodsort   = 'p.products_quantity ASC';            
            break;
        case 'stock-desc'   :
            $catsort    = 'c.sort_order ASC'; 
            $prodsort   = 'p.products_quantity DESC';            
            break; 
			case 'stocksort'        :
            $catsort    = 'c.sort_order ASC'; 
            $prodsort   = 'p.stock ASC';            
            break;
        case 'stocksort-desc'   :
            $catsort    = 'c.sort_order ASC'; 
            $prodsort   = 'p.stock DESC';            
            break;            
        case 'discount'     :
            $catsort    = 'c.sort_order ASC';
            $prodsort   = 'p.products_discount_allowed ASC';            
            break;  
        case 'discount-desc':
            $catsort    = 'c.sort_order ASC'; 
            $prodsort   = 'p.products_discount_allowed DESC';            
            break;                                   
        default             :
            $catsort    = 'cd.categories_name ASC';
            $prodsort   = 'pd.products_name ASC';
            break;
    }
    } else {
            $catsort    = 'c.sort_order, cd.categories_name ASC';
            $prodsort   = 'p.products_sort, pd.products_name ASC';
    }   
  
?>


    <tr>
     <td>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
         <td class="pageHeading">&nbsp;</td>
         <td align="right">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
             <td class="smallText" align="right">
                <?php 
                    echo os_draw_form('search', FILENAME_CATEGORIES, '', 'get'); 
                    echo HEADING_TITLE_SEARCH . ' ' . os_draw_input_field('search').os_draw_hidden_field(os_session_name(), os_session_id()); 
                ?>
                </form>
             </td>
            </tr>
            <tr>
             <td class="smallText" align="right">
                <?php 
                    echo os_draw_form('goto', FILENAME_CATEGORIES, '', 'get');
                    echo HEADING_TITLE_GOTO . ' ' . os_draw_pull_down_menu('cPath', os_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"').os_draw_hidden_field(os_session_name(), os_session_id()); 
                ?>
                </form>
             </td>
            </tr>
            </table>
        </td>
       </tr>
       </table>
     </td>
    </tr>
    <tr>
     <td>     
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
         <td valign="top">
         
            <!-- categories and products table -->
	
            <table  width="100%" cellspacing="2" cellpadding="2">
            <tr class="dataTableHeadingRow">
             <td class="dataTableHeadingContent" width="5%" align="center">
                <?php echo TABLE_HEADING_EDIT; ?>
                <input type="checkbox" onClick="javascript:SwitchCheck();">
             </td>
             <td class="dataTableHeadingContent" align="center">
                <?php echo TABLE_HEADING_CATEGORIES_PRODUCTS.os_sorting(FILENAME_CATEGORIES,'name'); ?>
             </td>
             <?php
             // check Produkt and attributes stock
             if (STOCK_CHECK == 'true') {
                    echo '<td class="dataTableHeadingContent" align="center">' . TABLE_HEADING_STOCK . os_sorting(FILENAME_CATEGORIES,'stock') . '</td>';
             }
             ?>
             <td class="dataTableHeadingContent" align="center">
                <?php echo TABLE_HEADING_STATUS.os_sorting(FILENAME_CATEGORIES,'status'); ?>
             </td>
             <td class="dataTableHeadingContent" align="center">
                <?php echo TABLE_HEADING_STARTPAGE.os_sorting(FILENAME_CATEGORIES,'startpage'); ?>
             </td>
			  <td class="dataTableHeadingContent" align="center">
                <?php echo TABLE_HEADING_STOCK.os_sorting(FILENAME_CATEGORIES,'stocksort');?>
             </td>
			  <td class="dataTableHeadingContent" align="center">
                <?php echo TABLE_HEADING_MENU; ?>
             </td>
             <td class="dataTableHeadingContent" align="center">
                <?php echo TABLE_HEADING_XML.os_sorting(FILENAME_CATEGORIES,'yandex'); ?>
             </td>
             <td class="dataTableHeadingContent" align="center">
                <?php echo TABLE_HEADING_PRICE.os_sorting(FILENAME_CATEGORIES,'price'); ?>
             </td>
             <td class="dataTableHeadingContent" align="center">
                <?php echo TABLE_HEADING_SORT.os_sorting(FILENAME_CATEGORIES,'sort'); ?>
             </td>
             <td class="dataTableHeadingContent" width="10%" align="center">
                <?php echo TABLE_HEADING_ACTION; ?>
             </td>


            </tr>
            
    <?php


 
            
    //multi-actions form STARTS
    if (os_not_null(@$_POST['multi_categories']) || os_not_null(@$_POST['multi_products'])) { 
        $action = "action=multi_action_confirm&" . os_get_all_get_params(array('cPath', 'action')) . 'cPath=' . $cPath; 
    } else {
        $action = "action=multi_action&" . os_get_all_get_params(array('cPath', 'action')) . 'cPath=' . $cPath;
    }
    echo os_draw_form('multi_action_form', FILENAME_CATEGORIES, $action, 'post', 'onsubmit="javascript:return CheckMultiForm()"');
    //add current category id in $_POST
    echo '<input type="hidden" id="cPath" name="cPath" value="' . $cPath . '">';             
    
// ----------------------------------------------------------------------------------------------------- //    
// WHILE loop to display categories STARTS
// ----------------------------------------------------------------------------------------------------- //
    $categories_count = 0;
    $rows = 0;
    if (@$_GET['search']) {
      $categories_query = os_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.yml_enable, c.categories_status, c.menu from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "' and cd.categories_name like '%" . $_GET['search'] . "%' order by " . $catsort);
    } else {
      $categories_query = os_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.yml_enable, c.categories_status, c.menu from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . $current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "' order by " . $catsort);
    } 
 
    $color = '';
    while ($categories = os_db_fetch_array($categories_query)) {
        
        $categories_count++;
        $rows++;

        if (@$_GET['search']) $cPath = @$categories['parent_id'];
        if ( ((@!$_GET['cID']) && (@!$_GET['pID']) || (@$_GET['cID'] == $categories['categories_id'])) && (@!$cInfo) && (substr(@$_GET['action'], 0, 4) != 'new_') ) {
            $cInfo = new objectInfo($categories);
        }
       $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
        if ( (is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) {
            echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'">' . "\n";
        } else {
            echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'">' . "\n";
        }
    ?>              
             <td class="categories_view_data"><input type="checkbox" name="multi_categories[]" value="<?php echo $categories['categories_id'] . '" '; if (is_array(@$_POST['multi_categories'])) { if (in_array($categories['categories_id'], $_POST['multi_categories'])) { echo 'checked="checked"'; } } ?>></td>
             <td class="categories_view_data" style="text-align: left; padding-left: 5px;">
             <?php 
                echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . os_get_path($categories['categories_id'])) . '">' . os_image(http_path('icons_admin') . 'folder.gif', ICON_FOLDER) . '<a>&nbsp;<b><a href="'.os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . os_get_path($categories['categories_id'])) .'">' . $categories['categories_name'] . '</a></b>'; 
             ?>
             </td>
        
             <?php
             if (STOCK_CHECK == 'true') {
                     echo '<td class="categories_view_data">--</td>';
             }
             ?>
        
             <td class="categories_view_data">
             <?php
             if ($categories['categories_status'] == '1') {
                 echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setcflag&flag=0&cID=' . $categories['categories_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
     
	 } else {
                 echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setcflag&flag=1&cID=' . $categories['categories_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
             }
             ?>
             </td>
             <td class="categories_view_data">--</td>
			 <td class="categories_view_data">--</td>
			 <td class="categories_view_data">
			<?php
             //show status icons (green & red circle) with links
             if ($categories['menu'] == '1') {
                 echo os_image(http_path('icons_admin').'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10).'&nbsp;&nbsp;<a href="'.os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setmenu&flag=0&cID=' . $categories['categories_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
             } else {
                 echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')).'action=setmenu&flag=1&cID=' . $categories['categories_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
             }
             ?>
			 </td>
             <td class="categories_view_data">
	                  <?php
             //show status icons (green & red circle) with links
             if ($categories['yml_enable'] == '1') {
                 echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setcxml&flag=0&cID=' . $categories['categories_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
             } else {
                 echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setcxml&flag=1&cID=' . $categories['categories_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
             }
             ?>
             </td>
	     </td>
             <td class="categories_view_data">--</td>
             <td class="categories_view_data"><?php echo $categories['sort_order']; ?></td>
             <td class="categories_view_data">
             <?php

			 	echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&cID=' . $categories['categories_id'] . "&action=edit_category") . '">' . os_image(http_path('icons_admin').'edit.gif', BUTTON_EDIT,'16','16') . '</a> ';
			 	            
                if ( (is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) 
				{ 
                    echo os_image(http_path('icons_admin').'nav_forward.png', ''); 
                } 
				else 
				{ 
                    echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
                } 
             ?>
             </td>
            </tr>

    <?php

    } 
    $products_count = 0;
    if (@$_GET['search']) {
        $products_query = os_db_query("
        SELECT
        p.products_tax_class_id,
        p.products_id,
        pd.products_name,
        p.products_sort,
        p.products_quantity,
        p.products_to_xml,
        p.products_image,
        p.products_price,
        p.products_discount_allowed,
        p.products_date_added,
        p.products_last_modified,
        p.products_date_available,
        p.products_status,
        p.products_startpage,
        p.products_startpage_sort,
        p2c.categories_id FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
        WHERE p.products_id = pd.products_id AND pd.language_id = '" . $_SESSION['languages_id'] . "' AND
        p.products_id = p2c.products_id AND (pd.products_name like '%" . $_GET['search'] . "%' OR
        p.products_model = '" . $_GET['search'] . "') ORDER BY " . $prodsort);
    } else {
	 
        $products_query = os_db_query("
        SELECT 
        p.products_tax_class_id,
        p.products_sort, 
        p.products_id, 
        pd.products_name, 
        p.products_quantity, 
        p.products_to_xml,
        p.products_image, 
        p.products_price, 
        p.products_discount_allowed, 
        p.products_date_added, 
        p.products_last_modified, 
        p.products_date_available, 
        p.products_status,
        p.products_startpage,
        p.products_startpage_sort, p2c.categories_id FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
        WHERE p.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "' AND 
        p.products_id = p2c.products_id AND p2c.categories_id = '" . $current_category_id . "' ORDER BY " . $prodsort);
		
    }

$numr = os_db_num_rows($products_query);
$products_count = 0;

if (!isset($_GET['page'])){$page=0;} else { $page = $_GET['page']; };

$max_count = MAX_DISPLAY_ADMIN_PAGE;

	if ( (isset($product_id)) and ($numr>0) ){
	$pnum=1;

	while ($row=os_db_fetch_array($products_query, true))
	{
		if ($row["products_id"]==$product_id){
								$pnum=($pnum/$max_count);
									if (strpos($pnum,".")>0){
									$pnum=substr($pnum,0,strpos($pnum,"."));
									} else{
									if ($pnum<>0){
											$pnum=$pnum-1;
												}
									}
									$page = $pnum*$max_count;
									echo $page;
								break;
								}
	$pnum++;
								
								}
	}
	
   $page = $page == 0 ? 1 : $page;
   $page = ($page-1)*MAX_DISPLAY_ADMIN_PAGE;
		
    if (@$_GET['search']) {
        $products_query = os_db_query("
        SELECT
        p.products_tax_class_id,
        p.products_id,
        pd.products_name,
        p.products_sort,
		p.stock, 
        p.products_quantity,
        p.products_to_xml,
        p.products_image,
        p.products_price,
        p.products_discount_allowed,
        p.products_date_added,
        p.products_last_modified,
        p.products_date_available,
        p.products_status,
        p.products_startpage,
        p.products_startpage_sort,
        p2c.categories_id FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
        WHERE p.products_id = pd.products_id AND pd.language_id = '" . $_SESSION['languages_id'] . "' AND
        p.products_id = p2c.products_id AND (pd.products_name like '%" . $_GET['search'] . "%' OR
        p.products_model = '" . $_GET['search'] . "') ORDER BY " . $prodsort . " limit ".$page.",".$max_count);
    } else {
        $products_query = os_db_query("
        SELECT 
        p.products_tax_class_id,
        p.products_sort, 
        p.products_id, 
        p.stock, 
        pd.products_name, 
        p.products_quantity, 
        p.products_to_xml,
        p.products_image, 
        p.products_price, 
        p.products_discount_allowed, 
        p.products_date_added, 
        p.products_last_modified, 
        p.products_date_available, 
        p.products_status,
        p.products_startpage,
        p.products_startpage_sort, p2c.categories_id FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
        WHERE p.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "' AND 
        p.products_id = p2c.products_id AND p2c.categories_id = '" . $current_category_id . "' ORDER BY " . $prodsort . " limit ".$page.",".$max_count);
		

    }

if ($numr>$max_count){
			$kn=0;
			$stp= TEXT_PAGES;

			$im=1;$nk=0;
			while ($kn<$numr){
			if ($kn<>$page){
			$stp.='<a href=categories.php?cPath='.$cPath.'&page='.$kn.(isset($_GET['search']) ? '&search='.$_GET['search'] : null).'>'.$im.'</a>&nbsp';
			}else{
			$stp.='<font color="#CC0000">['.$im.']</font>&nbsp';
			}
			$kn=$kn+$max_count;
			$nk=$nk+$max_count;
			if ($nk>=$max_count*30){$stp.='<br />';$nk=0;}
			$im++;
			}
}

    while ($products = os_db_fetch_array($products_query)) {
      $products_count++;
      $rows++;

      if (@$_GET['search']) $cPath=$products['categories_id'];

      if ( ((@!$_GET['pID']) && (@!$_GET['cID']) || (@$_GET['pID'] == @$products['products_id'])) && (@!$pInfo) && (@!$cInfo) && (substr(@$_GET['action'], 0, 4) != 'new_') ) {

        $reviews_query = os_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . $products['products_id'] . "'");
        $reviews = os_db_fetch_array($reviews_query);
        $pInfo_array = os_array_merge($products, $reviews);
        $pInfo = new objectInfo($pInfo_array);
      }
      $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
      if ( (@is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id) ) {
        echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" >' . "\n";
      } else {
        echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" >' . "\n";
      }

      ?>
      
      <?php
      unset($is_checked);
      if (is_array(@$_POST['multi_products'])) { 
        if (in_array($products['products_id'], $_POST['multi_products'])) { 
            $is_checked = ' checked="checked"'; 
        }
      } 
      ?>      
      
      <td class="categories_view_data">        
        <input type="checkbox" name="multi_products[]" value="<?php echo @$products['products_id']; ?>" <?php echo @$is_checked; ?>>
      </td>
      <td class="categories_view_data" style="text-align: left; padding-left: 8px;">
        <?php echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $products['products_id'] ) . '">' . os_image(http_path('icons_admin') . 'preview.gif', ICON_PREVIEW) . '&nbsp;</a><a href="'.os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $products['products_id']) .'">' . $products['products_name']; ?></a>
      </td>          
      <?php
      if (STOCK_CHECK == 'true') { ?>
        <td class="categories_view_data">
        <?php echo check_stock($products['products_id']); ?>
        </td>
      <?php } ?>     
      <td class="categories_view_data">
      <?php
            if ($products['products_status'] == '1') {
                echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setpflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
            } else {
                echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setpflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
            }
      ?>
      </td>
      <td class="categories_view_data">
      <?php
            if ($products['products_startpage'] == '1') {
                echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setsflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
            } else {
                echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setsflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
            }
      ?>
      </td>
	  <td class="categories_view_data">
      <?php
            if ($products['stock'] == '1') {
                echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setstock&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
            } else {
                echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setstock&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
            }
      ?>
      </td>
	  <td class="categories_view_data">--</td>
	  
      <td class="categories_view_data">
      <?php
            if ($products['products_to_xml'] == '1') {
                echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setxml&flagxml=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
            } else {
                echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'action=setxml&flagxml=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
            }
      ?>
      </td>
      <td class="categories_view_data">
      <?php
        echo $currencies->format($products['products_price']);
      ?>
      </td>
      <td class="categories_view_data">
        <?php 
        if ($current_category_id == 0){
        echo $products['products_startpage_sort'];
        } else {
        echo $products['products_sort'];
        }
         ?>
      </td>
      <td class="categories_view_data">
      <?php 
      
	  echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '&action=new_product">' . os_image(http_path('icons_admin') . 'edit.gif', BUTTON_EDIT,'16','16') . '</a> ';
	        
        if ( (@is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id) ) { echo os_image(http_path('icons_admin').'nav_forward.png', ''); } else { echo '<a href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } 
      ?>
      </td>
     </tr>    
<?php
    } 


    if ($cPath_array) {
      unset($cPath_back);
      for($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
        if ($cPath_back == '') {
          $cPath_back .= $cPath_array[$i];
        } else {
          $cPath_back .= '_' . $cPath_array[$i];
        }
      }
    }

    $cPath_back = (@$cPath_back) ? 'cPath=' . $cPath_back : '';
?>

        </tr>
        </table>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
         <td>
		  <ul class="page_menu_group"><?php 

		  if ($numr > $max_count) 
		  {
		  		$_param = array('file_name' => FILENAME_CATEGORIES,
	                            'page_name' => 'page',
	                            'param' => array('cPath' => $cPath)
						       );
				
				if (isset($_GET['search'])) $_param['param']['search'] = $_GET['search'];			   

		        echo osc_pages_menu($numr, $max_count, $_GET['page'], $_param); 
		  }
		  ?></ul>
		  </td>
		 <td align="right" class="smallText">
         <?php
         	if ($cPath) echo '<a class="button" onClick="this.blur()" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) .  $cPath_back . '&cID=' . $current_category_id) . '"><span>' . BUTTON_BACK . '</span></a>&nbsp;'; 
            echo '<a class="button" href="javascript:SwitchProducts()" onClick="this.blur()"><span>' . BUTTON_SWITCH_PRODUCTS . '</span></a>&nbsp;';
            echo '<a class="button" href="javascript:SwitchCategories()" onClick="this.blur()"><span>' . BUTTON_SWITCH_CATEGORIES . '</span></a>&nbsp;';                                           
         ?>
         </td>
		 <tr>
		  <td colspan="2" class="smallText">
		                  <?php echo TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br />' . TEXT_PRODUCTS . '&nbsp;' . $products_count; ?>
	
		  </td>
		  </tr>
        </table>                
        
     </td>
<?php
    $heading = array();
    $contents = array();
    
    switch (@$_GET['action']) {        

      case 'copy_to':
        $heading[] = array('text' => '</form><b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');

        $contents   = array('form' => os_draw_form('copy_to', FILENAME_CATEGORIES, 'action=copy_to_confirm&cPath=' . $cPath) . os_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => TEXT_INFO_COPY_TO_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENT_CATEGORIES . '<br /><b>' . os_output_generated_category_path($pInfo->products_id, 'product') . '</b>');

		if (QUICKLINK_ACTIVATED=='true') {
        $contents[] = array('text' => '<hr noshade>');
        $contents[] = array('text' => '<b>'.TEXT_MULTICOPY.'</b><br />'.TEXT_MULTICOPY_DESC);
        $cat_tree=os_get_category_tree();
        $tree='';
        for ($i=0;$n=sizeof($cat_tree),$i<$n;$i++) {
        $tree .='<input type="checkbox" name="cat_ids[]" value="'.$cat_tree[$i]['id'].'"><font size="1">'.$cat_tree[$i]['text'].'</font><br />';
        }
        $contents[] = array('text' => $tree.'<br /><hr noshade>');
        $contents[] = array('text' => '<b>'.TEXT_SINGLECOPY.'</b><br />'.TEXT_SINGLECOPY_DESC);
        }
        $contents[] = array('text' => '<br />' . TEXT_CATEGORIES . '<br />' . os_draw_pull_down_menu('categories_id', os_get_category_tree(), $current_category_id));
        $contents[] = array('text' => '<br />' . TEXT_HOW_TO_COPY . '<br />' . os_draw_radio_field('copy_as', 'link') . ' ' . TEXT_COPY_AS_LINK . '<br />' . os_draw_radio_field('copy_as', 'duplicate', true) . ' ' . TEXT_COPY_AS_DUPLICATE);
        $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_COPY . '"/>'.BUTTON_COPY.'</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
        break;
        
      case 'multi_action':

        if (os_not_null($_POST['multi_move'])) {     
            $heading[]  = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_ELEMENTS . '</b>');
            $contents[] = array('text' => '<table width="100%" border="0">');
            
            if (is_array($_POST['multi_categories'])) {
                foreach ($_POST['multi_categories'] AS $multi_category) {
                    $category_query = os_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $multi_category . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
                    $category = os_db_fetch_array($category_query);
                    $category_childs   = array('childs_count'   => $catfunc->count_category_childs($multi_category));
                    $category_products = array('products_count' => $catfunc->count_category_products($multi_category, true));
                    $cInfo_array = os_array_merge($category, $category_childs, $category_products);
                    $cInfo = new objectInfo($cInfo_array);                    
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . $cInfo->categories_name . '</b></td></tr>');
                    if ($cInfo->childs_count > 0)   $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_MOVE_WARNING_CHILDS, $cInfo->childs_count) . '</td></tr>');
                    if ($cInfo->products_count > 0) $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_MOVE_WARNING_PRODUCTS, $cInfo->products_count) . '</td></tr>');            
                }                
            }  
            
            if (is_array($_POST['multi_products'])) {
                foreach ($_POST['multi_products'] AS $multi_product) {
                
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . os_get_products_name($multi_product) . '</b></td></tr>');    
                    $product_categories_string = '';
                    $product_categories = os_output_generated_category_path($multi_product, 'product');
                    $product_categories_string = '<tr><td class="infoBoxContent">' . $product_categories . '</td></tr>';
                    $contents[] = array('text' => $product_categories_string); 
                }
            }                     
            
            $contents[] = array('text' => '<tr><td class="infoBoxContent"><strong>' . TEXT_MOVE_ALL . '</strong></td></tr><tr><td>' . os_draw_pull_down_menu('move_to_category_id', os_get_category_tree(), $current_category_id) . '</td></tr>');
            $contents[] = array('text' => '</table>');
            $contents[] = array('text' => '<input type="hidden" name="src_category_id" value="' . $current_category_id . '">');
            $contents[] = array('align' => 'center', 'text' => '<span class="button"><button type="submit" name="multi_move_confirm" value="' . BUTTON_MOVE . '">'.BUTTON_MOVE.'</button></span> <a class="button" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&cID=' . $cInfo->categories_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');            
            $contents[] = array('text' => '</form>'); 
        }
        if (os_not_null($_POST['multi_delete'])) {
            $heading[]  = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ELEMENTS . '</b>');
            $contents[] = array('text' => '<table width="100%" border="0">');
            
            if (is_array($_POST['multi_categories'])) {
                foreach ($_POST['multi_categories'] AS $multi_category) {
                    $category_query = os_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $multi_category . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
                    $category = os_db_fetch_array($category_query);
                    $category_childs   = array('childs_count'   => $catfunc->count_category_childs($multi_category));
                    $category_products = array('products_count' => $catfunc->count_category_products($multi_category, true));
                    $cInfo_array = os_array_merge($category, $category_childs, $category_products);
                    $cInfo = new objectInfo($cInfo_array);                    
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . $cInfo->categories_name . '</b></td></tr>');
                    if ($cInfo->childs_count > 0)   $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count) . '</td></tr>');
                    if ($cInfo->products_count > 0) $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count) . '</td></tr>');            
                }                
            }
            
            if (is_array($_POST['multi_products'])) {
                foreach ($_POST['multi_products'] AS $multi_product) {                
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . os_get_products_name($multi_product) . '</b></td></tr>');    
                    $product_categories_string = '';
                    $product_categories = os_generate_category_path($multi_product, 'product');
                    for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
                      $category_path = '';
                      for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++) {
                        $category_path .= $product_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
                      }
                      $category_path = substr($category_path, 0, -16);
                      $product_categories_string .= os_draw_checkbox_field('multi_products_categories['.$multi_product.'][]', $product_categories[$i][sizeof($product_categories[$i])-1]['id'], true) . '&nbsp;' . $category_path . '<br />';
                    }
                    $product_categories_string = substr($product_categories_string, 0, -4);
                    $product_categories_string = '<tr><td class="infoBoxContent">' . $product_categories_string . '</td></tr>';
                    $contents[] = array('text' => $product_categories_string); 
                }
            }
            
            $contents[] = array('text' => '</table>');            
            $contents[] = array('align' => 'center', 'text' => '<span class="button"><button type="submit" name="multi_delete_confirm" value="' . BUTTON_DELETE . '">'.BUTTON_DELETE.'</button></span> <a class="button" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&cID=' . $cInfo->categories_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
            $contents[] = array('text' => '</form>');            
        }
        if (os_not_null($_POST['multi_copy'])) {     
            $heading[]  = array('text' => '<b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');
            $contents[] = array('text' => '<table width="100%" border="0">');
            
            if (is_array($_POST['multi_categories'])) {
                foreach ($_POST['multi_categories'] AS $multi_category) {
                    $category_query = os_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $multi_category . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
                    $category = os_db_fetch_array($category_query);
                    $category_childs   = array('childs_count'   => $catfunc->count_category_childs($multi_category));
                    $category_products = array('products_count' => $catfunc->count_category_products($multi_category, true));
                    $cInfo_array = os_array_merge($category, $category_childs, $category_products);
                    $cInfo = new objectInfo($cInfo_array);                    
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . $cInfo->categories_name . '</b></td></tr>');
                    if ($cInfo->childs_count > 0)   $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_MOVE_WARNING_CHILDS, $cInfo->childs_count) . '</td></tr>');
                    if ($cInfo->products_count > 0) $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_MOVE_WARNING_PRODUCTS, $cInfo->products_count) . '</td></tr>');            
                }                
            }  
            
            if (is_array($_POST['multi_products'])) {
                foreach ($_POST['multi_products'] AS $multi_product) {
                
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . os_get_products_name($multi_product) . '</b></td></tr>');    
                    $product_categories_string = '';
                    $product_categories = os_output_generated_category_path($multi_product, 'product');
                    $product_categories_string = '<tr><td class="infoBoxContent">' . $product_categories . '</td></tr>';
                    $contents[] = array('text' => $product_categories_string); 
                }
            }                     
            
            $contents[] = array('text' => '</table>');
    		if (QUICKLINK_ACTIVATED=='true') {
                $contents[] = array('text' => '<hr noshade>');
                $contents[] = array('text' => '<b>'.TEXT_MULTICOPY.'</b><br />'.TEXT_MULTICOPY_DESC);
                $cat_tree=os_get_category_tree();
                $tree='';
                for ($i=0;$n=sizeof($cat_tree),$i<$n;$i++) {
                    $tree .= '<input type="checkbox" name="dest_cat_ids[]" value="'.$cat_tree[$i]['id'].'"><font size="1">'.$cat_tree[$i]['text'].'</font><br />';
                }
                $contents[] = array('text' => $tree.'<br /><hr noshade>');
                $contents[] = array('text' => '<b>'.TEXT_SINGLECOPY.'</b><br />'.TEXT_SINGLECOPY_DESC);
            }
            $contents[] = array('text' => '<br /><div>' . TEXT_SINGLECOPY_CATEGORY . '</div><br />' . os_draw_pull_down_menu('dest_category_id', os_get_category_tree(), $current_category_id) . '<br />');
            $contents[] = array('text' => '<strong>' . TEXT_HOW_TO_COPY . '</strong><br />' . os_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br />' . os_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE . '<br /><hr noshade>');
            $contents[] = array('align' => 'center', 'text' => '<span class="button"><button type="submit" name="multi_copy_confirm" value="' . BUTTON_COPY . '">'.BUTTON_COPY.'</button></span> <a class="button" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&cID=' . $cInfo->categories_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');            
            $contents[] = array('text' => '</form>'); 
        }                 
        break;        

      default:
        if ($rows > 0) {
          if (is_object(@$cInfo)) { 
            $heading[]  = array('align' => 'center', 'text' => '<b>' . $cInfo->categories_name . '</b>');
            $contents[] = array('align' => 'center', 'text' => '<div>' . TEXT_MARKED_ELEMENTS . '</div>');
            $contents[] = array('align' => 'center', 'text' => '<table border=0><tr><td align="center"><span class="button"><button type="submit" name="multi_delete" onClick="this.blur();" value="'. BUTTON_DELETE . '">'. BUTTON_DELETE . '</button></span></td></tr><tr><td align="center"><span class="button"><button type="submit" onClick="this.blur();" name="multi_move" value="' . BUTTON_MOVE . '">'.BUTTON_MOVE.'</button></span></td></tr><tr><td align="center"><span class="button"><button type="submit" onClick="this.blur();" name="multi_copy" value="' . BUTTON_COPY . '">'.BUTTON_COPY.'</button></span></td></tr></table>');
            $contents[] = array('align' => 'center', 'text' => '<table border=0><tr><td align="center"><span class="button"><button type="submit" name="multi_status_on" onClick="this.blur();" value="'. BUTTON_STATUS_ON . '">'. BUTTON_STATUS_ON . '</button></span></td></tr><tr><td align="center"><span class="button"><button type="submit" onClick="this.blur();" name="multi_status_off" value="' . BUTTON_STATUS_OFF . '">' . BUTTON_STATUS_OFF . '</button></span></td></tr></table>');
            $contents[] = array('align' => 'center', 'text' => '<div>' . TEXT_ACTIVE_ELEMENT . '</div>');
            $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_category') . '"><span>' . BUTTON_EDIT . '</span></a>');
            $contents[] = array('align' => 'center', 'text' => '<div>' . TEXT_INSERT_ELEMENT . '</div>');
            if (@!$_GET['search']) {
            	$contents[] = array('align' => 'center', 'text' => '<table border=0><tr><td align="center"><a class="button" onClick="this.blur()" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_category') . '"><span>' . BUTTON_NEW_CATEGORIES . '</span></a></td></tr><tr><td align="center"><a class="button" onClick="this.blur()" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_product') . '"><span>' . BUTTON_NEW_PRODUCTS . '</span></a></td></tr></table>');            
            }
            $contents[] = array('align' => 'center', 'text' => '<div>' . TEXT_INFORMATIONS . '</div>');
            $contents[] = array('text'  => '<div>' . TEXT_DATE_ADDED . ' ' . os_date_short($cInfo->date_added) . '</div>');
            if (os_not_null($cInfo->last_modified)) $contents[] = array('text' => '<div>' . TEXT_LAST_MODIFIED . ' ' . os_date_short($cInfo->last_modified) . '</div>');            
            $contents[] = array('align' => 'center', 'text' => '<div>' . os_info_image_c($cInfo->categories_image, $cInfo->categories_name)  . '</div><div>' . $cInfo->categories_image . '</div>');            
          } elseif (is_object($pInfo)) { 
            $heading[]  = array('align' => 'center', 'text' => '<b>' . os_get_products_name($pInfo->products_id, $_SESSION['languages_id']) . '</b>');
            $contents[] = array('align' => 'center', 'text' => '<div class="categories_active_element">' . TEXT_MARKED_ELEMENTS . '</div>');
            $contents[] = array('align' => 'center', 'text' => '<table border=0><tr><td align="center">' . os_button(BUTTON_DELETE, 'submit', 'name="multi_delete"').'</td></tr><tr><td>'.os_button(BUTTON_MOVE, 'submit', 'name="multi_move"').'</td></tr><tr><td align="center">'.os_button(BUTTON_COPY, 'submit', 'name="multi_copy"').'</td></tr></table>');
            $contents[] = array('align' => 'center', 'text' => '<table border=0><tr><td align="center"><span class="button"><button type="submit" name="multi_status_on" onClick="this.blur();" value="'. BUTTON_STATUS_ON . '">'. BUTTON_STATUS_ON . '</button></span></td></tr><tr><td align="center"><span class="button"><button type="submit" onClick="this.blur();" name="multi_status_off" value="' . BUTTON_STATUS_OFF . '">' . BUTTON_STATUS_OFF . '</button></span></td></tr></table>');
            $contents[] = array('text'  => '</form>');            
            //Single Product Actions
            $contents[] = array('align' => 'center', 'text' => '<div class="categories_active_element">' . TEXT_ACTIVE_ELEMENT . '</div>');
            $contents[] = array('align' => 'center', 'text' => '<table border=0><tr><td align="center"><a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') . '"><span>' . BUTTON_EDIT . '</span></a></td></tr><tr><td align="center"><form action="' . FILENAME_NEW_ATTRIBUTES . '" name="edit_attributes" method="post"><input type="hidden" name="action" value="edit"><input type="hidden" name="current_product_id" value="' . $pInfo->products_id . '"><input type="hidden" name="cpath" value="' . $cPath . '"><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_EDIT_ATTRIBUTES . '">' . BUTTON_EDIT_ATTRIBUTES . '</button></span></form></td></tr><tr><td align="center" style="text-align: center;"><form action="' . FILENAME_CATEGORIES . '" name="edit_crossselling" method="GET"><input type="hidden" name="action" value="edit_crossselling"><input type="hidden" name="current_product_id" value="' . $pInfo->products_id . '"><input type="hidden" name="cpath" value="' . $cPath  . '"><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_EDIT_CROSS_SELLING . '">' . BUTTON_EDIT_CROSS_SELLING . '</button></span></form></td></tr><tr><td align="center"></td></tr></table>');
            //Insert new Element Actions
            $contents[] = array('align' => 'center', 'text' => '<div class="categories_active_element">' . TEXT_INSERT_ELEMENT . '</div>');
            if (@!$_GET['search']) {
            	$contents[] = array('align' => 'center', 'text' => '<table border=0><tr><td align="center"><a class="button" onClick="this.blur()" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_category') . '"><span>' . BUTTON_NEW_CATEGORIES . '</span></a></td></tr><tr><td align="center"><a class="button" onClick="this.blur()" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_product') . '"><span>' . BUTTON_NEW_PRODUCTS . '</span></a></td></tr></table>');            
            }            
            $contents[] = array('align' => 'center', 'text' => '<div>' . TEXT_INFORMATIONS . '</div>');
            $contents[] = array('text'  => '<b>' . TEXT_DATE_ADDED . '</b> <br /> ' . os_date_short($pInfo->products_date_added) . '</div>');
			
            if (os_not_null($pInfo->products_last_modified))    $contents[] = array('text' => '<b>' . TEXT_LAST_MODIFIED . '</b> <br />' . os_date_short($pInfo->products_last_modified) . '');
			
            if (date('Y-m-d') < $pInfo->products_date_available) $contents[] = array('text' => '<div>' . TEXT_DATE_AVAILABLE . ' ' . os_date_short($pInfo->products_date_available) . '</div>');            
            
            $price = $pInfo->products_price;
            $price = os_round($price,PRICE_PRECISION);
			
            $price_string = '' . TEXT_PRODUCTS_PRICE_INFO . '&nbsp;' . $currencies->format($price);
			
            if (PRICE_IS_BRUTTO=='true' && ($_GET['read'] == 'only' || $_GET['action'] != 'new_product_preview') )
			{
                $price_netto = os_round($price,PRICE_PRECISION);
                $tax_query = os_db_query("select tax_rate from " . TABLE_TAX_RATES . " where tax_class_id = '" . $pInfo->products_tax_class_id . "' ");
                $tax = os_db_fetch_array($tax_query);
                $price = ($price*($tax[tax_rate]+100)/100);
                $price_string = '' . TEXT_PRODUCTS_PRICE_INFO . '&nbsp;' . $currencies->format($price) . ' - ' . TXT_NETTO . $currencies->format($price_netto);
            }
			
            $contents[] = array('text' => '<div>' . $price_string.  '</div><div><b>' . TEXT_PRODUCTS_DISCOUNT_ALLOWED_INFO . '</b> <br />' . $pInfo->products_discount_allowed . '</div><b>' .  TEXT_PRODUCTS_QUANTITY_INFO . '</b> ' . $pInfo->products_quantity . '');            
            // END IN-SOLUTION

            //$contents[] = array('text' => '<br />' . TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($pInfo->products_price) . '<br />' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity);
            $contents[] = array('text' => '<b>' . TEXT_PRODUCTS_AVERAGE_RATING . '</b><br> ' . number_format($pInfo->average_rating, 2) . ' %</div>');
            $contents[] = array('text' => '<div>' . TEXT_PRODUCT_LINKED_TO . '<br />' . os_output_generated_category_path($pInfo->products_id, 'product') . '</div>');
            $contents[] = array('align' => 'center', 'text' => '<div>' . os_product_thumb_image($pInfo->products_image, $pInfo->products_name)  . '</div><div>' . $pInfo->products_image.'</div>');
          }          
        } else { 
          $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');
          $contents[] = array('text' => sprintf(TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS, os_get_categories_name($current_category_id, $_SESSION['languages_id'])));
          $contents[] = array('align' => 'center', 'text' => '<table border=0><tr><td align="center"><a class="button" onClick="this.blur()" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_category') . '"><span>' . BUTTON_NEW_CATEGORIES . '</span></a></td></tr><tr><td align="center"><a class="button" onClick="this.blur()" href="' . os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_product') . '"><span>' . BUTTON_NEW_PRODUCTS . '</span></a></td></tr></table>');
        }
        break;
    }

    if ((os_not_null($heading)) && (os_not_null($contents))) {
      echo '<td class="right_box" valign="top">' . "\n";
      $box = new box;
      echo $box->infoBox($heading, $contents);
      echo '</td>' . "\n";
    }
?>
        </tr>
        </table>
     </td>
    </tr>
    <tr>
     <td>