<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require_once ('includes/top.php');
require_once(_FUNC_ADMIN.'trumbnails_add_funcs.php');

$i = (int)$_POST["index"];
$product_image = $_POST["product_image"]; 
$product_dir = $_POST["product_image"];
$product_subdir = $_POST["product_subdir"];

$upload_dir_image = "upload_dir_image_0";
$get_file_image = "get_file_image_0";

if($i > 0)
{
    $upload_dir_image = "mo_pics_upload_dir_image_".($i-1);
    $get_file_image = "mo_pics_get_file_image_".($i-1);
}
$file_list = os_array_merge(array('0' => array('id' => '', 'text' => '')), os_get_files_in_dir(dir_path('images_original').$product_subdir));

echo os_draw_pull_down_menu($get_file_image, $file_list, $product_image);
?>