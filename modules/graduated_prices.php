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
$module_content = array ();

$staffel_data = $product->getGraduated();

if (sizeof($staffel_data) > 1) {
	$module->assign('language', $_SESSION['language']);
	$module->assign('module_content', $staffel_data);
	$module->caching = 0;
	$module = $module->fetch(CURRENT_TEMPLATE.'/module/graduated_price.html');

	$info->assign('MODULE_graduated_price', $module);
}
?>