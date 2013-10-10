<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function step($is_submit){

    $checkWritables = checkWritables();

    $result = array('html' => display('dir', array('checkWritables' => $checkWritables)));

    return $result;
}

function checkWritables()
{
	$dir = dirname(__FILE__).'/../../';
	$_dir = str_replace('\\', '/', realpath($dir)).'/';

	$arr = array(
		'admin/backups/' => $_dir.'admin/backups/',
		'cache/' => $_dir.'cache/',
		'cache/system/' => $_dir.'cache/system/',
		'images/' => $_dir.'images/',
		'images/articles/' => $_dir.'images/articles/',
		'images/attribute_images/' => $_dir.'images/attribute_images/',
		'images/attribute_images/mini/' => $_dir.'images/attribute_images/mini/',
		'images/attribute_images/original/' => $_dir.'images/attribute_images/original/',
		'images/attribute_images/thumbs/' => $_dir.'images/attribute_images/thumbs/',
		'images/avatars/' => $_dir.'images/avatars/',
		'images/banner/' => $_dir.'images/banner/',
		'images/categories/' => $_dir.'images/categories/',
		'images/groups/' => $_dir.'images/groups/',
		'images/manufacturers/' => $_dir.'images/manufacturers/',
		'images/news/' => $_dir.'images/news/',
		'images/product_images/info_images/' => $_dir.'images/product_images/info_images/',
		'images/product_images/original_images/' => $_dir.'images/product_images/original_images/',
		'images/product_images/popup_images/' => $_dir.'images/product_images/popup_images/',
		'images/product_images/thumbnail_images/' => $_dir.'images/product_images/thumbnail_images/',
		'images/shipping_status/' => $_dir.'images/shipping_status/',
		'media/export/' => $_dir.'media/export/',
		'media/import/' => $_dir.'media/import/',
		'media/products/' => $_dir.'media/products/',
		'tmp/' => $_dir.'tmp/',
	);

	$type = $_SESSION['install']['type'];
	if ($type == '1')
	{
		$arr['config.php'] = $_dir.'config.php';
		$arr['htaccess.txt'] = $_dir.'htaccess.txt';
	}

	return $arr;
}

















