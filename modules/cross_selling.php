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
$data = $product->getCrossSells();
if (count($data) > 0) {
    $module->assign('language', $_SESSION['language']);
    $module->assign('module_content', $data);
    $module->caching = 0;
    $module = $module->fetch(CURRENT_TEMPLATE.'/module/cross_selling.html');
    $info->assign('MODULE_cross_selling', $module);
}
if (ACTIVATE_REVERSE_CROSS_SELLING=='true') {
$module = new osTemplate;
$ids = array();
if (count($data) > 0) {
foreach ($data as $v1) {
        foreach($v1[PRODUCTS] as $val){
                              $ids[$val[PRODUCTS_ID]] = true;
                              }
        }
}
        $data = array();
$datarev = $product->getReverseCrossSells();
if (count($datarev) > 0) {
foreach ($datarev as $val) {
        if (!isset($ids[$val[PRODUCTS_ID]])) {
           $data[] = $val;
           }
        }
}
if (count($data) > 0) {
    $module->assign('language', $_SESSION['language']);
    $module->assign('module_content', $data);
    $module->caching = 0;
    $module = $module->fetch(CURRENT_TEMPLATE.'/module/reverse_cross_selling.html');
    $info->assign('MODULE_reverse_cross_selling', $module);
}
}

?>