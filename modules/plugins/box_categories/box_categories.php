<?php
/*
	Plugin Name: Категории
	Plugin URI: http://osc-cms.com/store/plugins/box-categories
	Version: 1.3
	Description: Выводит блок категорий
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Boxes
*/

add_action('box', 'box_categories_func');
add_filter('head_array_detail', 'box_categories_js');

function box_categories_js($value)
{
	if (get_option('menuJSType') == 'accordion')
	{
		add_style(plugurl().'js/menu_accordion.css', $value, 'categories');
		add_js(plugurl().'js/menu_accordion.js', $value, 'categories');
	}

	return $value;
}

function box_categories_func()
{
	global $osTemplate, $cartet;
	$box = new osTemplate;

	$box->assign('aCategories', $cartet->product->getCategoriesTree());
	$box->assign('current', $cartet->product->getCurrentCategory());
	$box->assign('current_in', $cartet->product->getCurrentCategory(true));
	$box->assign('image', get_option('showCatImages'));
	$box->assign('imageWidth', get_option('cImgWidth'));
	$box->assign('imageHeight', get_option('cImgHeight'));
	$box->assign('counts', get_option('countProducts'));

	$box->assign('plugDir', dirname(__FILE__).'/themes');
	$box->assign('language', $_SESSION['language']);
	$box->template_dir = plugdir();

	if (!CacheCheck())
	{
		$box->caching = 0;
		$_box_value = $box->fetch(dirname(__FILE__).'/themes/categories.html');
	}
	else
	{
		$box->caching = 1;
		$box->cache_lifetime = CACHE_LIFETIME;
		$box->cache_modified_check = CACHE_CHECK;
		$cache_id = $_SESSION['language'];
		$_box_value = $box->fetch(dirname(__FILE__).'/themes/categories.html', $cache_id);
	}

	$osTemplate->assign('box_CATEGORIES', $_box_value);
}

function box_categories_install()
{
	add_option('countProducts',		'false', 'radio', "array('true', 'false')");
	add_option('subCategories',		'true', 'radio', "array('true', 'false')");
	add_option('maxSubCategories',	'5', 'input');
	add_option('showCatImages',		'false', 'radio', "array('true', 'false')");
	add_option('cImgWidth',			'30', 'input');
	add_option('cImgHeight',		'30', 'input');
	add_option('menuJSType',		'none', 'radio', "array('none', 'accordion')");
}
?>