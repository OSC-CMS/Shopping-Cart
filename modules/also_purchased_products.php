<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

$module = new osTemplate;

$data = $product->getAlsoPurchased();
if (count($data) >= MIN_DISPLAY_ALSO_PURCHASED) {

	$module->assign('language', $_SESSION['language']);
	$module->assign('module_content', $data);
	$module->caching = 0;
	$module = $module->fetch(CURRENT_TEMPLATE.'/module/also_purchased.html');

	$info->assign('MODULE_also_purchased', $module);

}
?>