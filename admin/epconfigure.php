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

$tempdir = "media/export/";
$tempdir2 = "/media/export/";

global $maxrecs;
$maxrecs = 50; 
global $default_images, $default_image_manufacturer, $default_image_product, $default_image_category;
$default_image_manufacturer = '';
$default_image_product = '';
$default_image_category = '';
global $active, $inactive, $zero_qty_inactive, $deleteit;
$active = 'Active';
$inactive = 'Inactive';
$zero_qty_inactive = false;
global $modelsize;
$modelsize = 200;
global $price_with_tax;
$price_with_tax = false;
global $replace_quotes;
$replace_quotes = true;
global $separator;
$separator = "\t";
global $max_categories;
$max_categories = 7;
global $products_with_attributes;
$products_with_attributes = true;
global $attribute_options_select;
global $froogle_product_info_path;
$froogle_product_info_path =  HTTP_SERVER . DIR_WS_CATALOG . "product_info.php";
global $froogle_image_path;
$froogle_image_path =   HTTP_SERVER . DIR_WS_CATALOG . "images/";
global $froogle_SEF_urls;
$froogle_SEF_urls = true;
?>