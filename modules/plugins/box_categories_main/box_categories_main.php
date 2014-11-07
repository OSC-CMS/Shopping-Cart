<?php
/*
	Plugin Name: Категории на главной
	Plugin URI: http://osc-cms.com/store/plugins/box-categories-main
	Version: 1.1
	Description: Выводит блок категорий на главной
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Boxes
*/

add_action('box', 'box_categories_main_func');
add_filter('head_array_detail', 'box_categories_main_css');

function box_categories_main_css($value)
{
	$theme = (file_exists(plugdir().'themes/'.CURRENT_TEMPLATE)) ? CURRENT_TEMPLATE : 'default';
	add_style(plugurl().'themes/'.$theme.'/css/box_categories_main.css', $value, 'categories_main');

	return $value;
}

function box_categories_main_func()
{
	global $osTemplate, $cartet;
	$box = new osTemplate;

	$box->assign('aCategories', $cartet->product->getCategoriesTree());
	$box->assign('current', $cartet->product->getCurrentCategory());
	$box->assign('current_in', $cartet->product->getCurrentCategory(true));
	$box->assign('image', get_option('showCatImagesMain'));
	$box->assign('imageWidth', get_option('cImgWidthMain'));
	$box->assign('imageHeight', get_option('cImgHeightMain'));
	$box->assign('counts', get_option('countProductsMain'));
	$box->assign('subcats', get_option('subCategoriesMain'));

	$theme = (file_exists(plugdir().'themes/'.CURRENT_TEMPLATE)) ? CURRENT_TEMPLATE : 'default';

	$box->assign('plugDir', dirname(__FILE__).'/themes/'.$theme);
	$box->assign('language', $_SESSION['language']);
	$box->template_dir = plugdir();

	$box->caching = 0;
	$_box_value = $box->fetch(dirname(__FILE__).'/themes/'.$theme.'/categories.html');
	$osTemplate->assign('box_CATEGORIES_MAIN', $_box_value);
}

function box_categories_main_install()
{
	add_option('countProductsMain',		'false', 'radio', "array('true', 'false')");
	add_option('subCategoriesMain',		'true', 'radio', "array('true', 'false')");
	add_option('showCatImagesMain',		'false', 'radio', "array('true', 'false')");
	add_option('cImgWidthMain',			'30', 'input');
	add_option('cImgHeightMain',		'30', 'input');
}