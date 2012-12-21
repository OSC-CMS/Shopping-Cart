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

$data = $product->getAlsoPurchased();
if (count($data) >= MIN_DISPLAY_ALSO_PURCHASED) {

	$module->assign('language', $_SESSION['language']);
	$module->assign('module_content', $data);
	$module->caching = 0;
	$module = $module->fetch(CURRENT_TEMPLATE.'/module/also_purchased.html');

	$info->assign('MODULE_also_purchased', $module);

}
?>