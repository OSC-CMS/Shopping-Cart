<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

include ('includes/top.php');
//$osTemplate = new osTemplate;

if ($_GET['products_id']) {
	$cat = os_db_query("SELECT categories_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id='".(int) $_GET['products_id']."'");
	$catData = os_db_fetch_array($cat);
	if ($catData['categories_id'])
		$cPath = os_input_validation(os_get_path($catData['categories_id']), 'cPath', '');

}



if ($_GET['action'] == 'get_download') {
	os_get_download($_GET['cID']);
}

include (DIR_WS_MODULES.'product_info.php');

require (dir_path('includes').'header.php');
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');


if (is_file(_THEMES_C.FILENAME_PRODUCT_INFO.'_'.$actual_products_id.'.html'))
{
   $template = CURRENT_TEMPLATE.'/'.FILENAME_PRODUCT_INFO.'_'.$actual_products_id.'.html';
}
elseif (is_file(_THEMES_C.'product_info.html'))
{
   $template = CURRENT_TEMPLATE.'/product_info.html';
}
else
{
   $template = CURRENT_TEMPLATE.'/index.html';
}

$osTemplate->display($template);
include ('includes/bottom.php');
?>