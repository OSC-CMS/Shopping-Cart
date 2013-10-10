<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$module = new osTemplate;

 function get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false, $cPath = '') {
if ($parent_id == 0){ $cPath = ''; } else { $cPath .= $parent_id . '_'; }
   if (!is_array($category_tree_array)) $category_tree_array = array();
   if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

   if ($include_itself) {
     $category_query = "select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . $_SESSION['languages_id'] . "' and c.categories_status = '1' and cd.categories_id = '" . $parent_id . "'";
     $category_query = osDBquery($category_query);
     $category = os_db_fetch_array($category_query,true);
     $category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
   }

   $categories_query = "select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . $_SESSION['languages_id'] . "' and c.parent_id = '" . $parent_id . "' and c.categories_status = '1' order by c.sort_order, cd.categories_name";
   $categories_query = osDBquery($categories_query);
   while ($categories = os_db_fetch_array($categories_query,true)) {
   
     $SEF_link = os_href_link(FILENAME_DEFAULT, os_category_link($categories['categories_id'],$categories['categories_name']));
    
     if ($exclude != $categories['categories_id'])
      $category_tree_array[] = array('id' => $categories['categories_id'],
      				     'text' => $spacing . $categories['categories_name'],
				     'link'  => $SEF_link);
      $category_tree_array = get_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array, false, $cPath);
   }

   return $category_tree_array;
 }
 
 
 
 
 
 if (GROUP_CHECK == 'true') {
 	$group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
 }
 
 $categories_query = "select c.categories_image, c.categories_id, cd.categories_name FROM " . TABLE_CATEGORIES . " c left join "
      . TABLE_CATEGORIES_DESCRIPTION ." cd on c.categories_id = cd.categories_id WHERE c.categories_status = '1' and cd.language_id = ".$_SESSION['languages_id']
      ." and c.parent_id = '0' ".$group_check." order by c.sort_order ASC";

 $categories_query = osDBquery($categories_query);
 $module_content = array();
 while ($categories = os_db_fetch_array($categories_query,true)) {
   
   $SEF_link = os_href_link(FILENAME_DEFAULT, os_category_link($categories['categories_id'],$categories['categories_name']));
 
   $module_content[]=array('ID'  => $categories['categories_id'],
                           'CAT_NAME'  => $categories['categories_name'],
                           'CAT_IMAGE' => http_path('images') . 'categories/' . $categories['categories_image'],
                           'CAT_LINK'  => $SEF_link,
			   'SCATS'  => get_category_tree($categories['categories_id'], '',0));
 }

 if (sizeof($module_content)>=1)
 {
    $module->assign('language', $_SESSION['language']);
    $module->assign('module_content',$module_content);
    echo $module->fetch(CURRENT_TEMPLATE.'/module/sitemap.html');

 }
?>