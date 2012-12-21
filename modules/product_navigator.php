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

$module = new osTemplate;
$fsk_lock = '';
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
	$fsk_lock = ' and p.products_fsk18!=1';
}
$group_check = "";
if (GROUP_CHECK == 'true') {
	$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
}
$products_query = osDBquery("SELECT
                                 pc.products_id,
                                 pd.products_name
                                 FROM ".TABLE_PRODUCTS_TO_CATEGORIES." pc,
                                 ".TABLE_PRODUCTS." p,
                                 ".TABLE_PRODUCTS_DESCRIPTION." pd

                                 WHERE categories_id='".$current_category_id."'
                                 and p.products_id=pc.products_id
                                 and p.products_id = pd.products_id
                                 and pd.language_id = '".(int) $_SESSION['languages_id']."'
                                 and p.products_status=1 
                                 ".$fsk_lock.$group_check);
$i = 0;
while ($products_data = os_db_fetch_array($products_query, true)) {
	$p_data[$i] = array ('pID' => $products_data['products_id'], 'pName' => $products_data['products_name']);
	if ($products_data['products_id'] == $product->data['products_id'])
		$actual_key = $i;
	$i ++;

}

if ($actual_key == 0) {
} else {
	$prev_id = $actual_key -1;
	$prev_link = os_href_link(FILENAME_PRODUCT_INFO, os_product_link($p_data[$prev_id]['pID'], $p_data[$prev_id]['pName']));
	if ($prev_id != 0)
		$first_link = os_href_link(FILENAME_PRODUCT_INFO, os_product_link($p_data[0]['pID'], $p_data[0]['pName']));
}

if ($actual_key == (sizeof($p_data) - 1)) {
} else {
	$next_id = $actual_key +1;
	$next_link = os_href_link(FILENAME_PRODUCT_INFO, os_product_link($p_data[$next_id]['pID'], $p_data[$next_id]['pName']));
	if ($next_id != (sizeof($p_data) - 1))
		$last_link = os_href_link(FILENAME_PRODUCT_INFO, os_product_link($p_data[(sizeof($p_data) - 1)]['pID'], $p_data[(sizeof($p_data) - 1)]['pName']));

}
$module->assign('FIRST', $first_link);
$module->assign('PREVIOUS', $prev_link);
$module->assign('NEXT', $next_link);
$module->assign('LAST', $last_link);

$module->assign('PRODUCTS_COUNT', count($p_data));
$module->assign('language', $_SESSION['language']);

$module->caching = 0;
$product_navigator = $module->fetch(CURRENT_TEMPLATE.'/module/product_navigator.html');

$info->assign('PRODUCT_NAVIGATOR', $product_navigator);
?>