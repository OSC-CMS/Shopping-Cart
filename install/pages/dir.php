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
	return array(
		'admin/backups/' => ROOT_PATH.'admin/backups/',
		'cache/' => ROOT_PATH.'cache/',
		'cache/cache/' => ROOT_PATH.'cache/cache/',
		'cache/compiled/' => ROOT_PATH.'cache/compiled/',
		'cache/database/' => ROOT_PATH.'cache/database/',
		'cache/system/' => ROOT_PATH.'cache/system/',
		'cache/url/' => ROOT_PATH.'cache/url/',
		'images/' => ROOT_PATH.'images/',
		'images/articles/' => ROOT_PATH.'images/articles/',
		'images/attribute_images/' => ROOT_PATH.'images/attribute_images/',
		'images/attribute_images/mini/' => ROOT_PATH.'images/attribute_images/mini/',
		'images/attribute_images/original/' => ROOT_PATH.'images/attribute_images/original/',
		'images/attribute_images/thumbs/' => ROOT_PATH.'images/attribute_images/thumbs/',
		'images/avatars/' => ROOT_PATH.'images/avatars/',
		'images/banner/' => ROOT_PATH.'images/banner/',
		'images/categories/' => ROOT_PATH.'images/categories/',
		'images/groups/' => ROOT_PATH.'images/groups/',
		'images/manufacturers/' => ROOT_PATH.'images/manufacturers/',
		'images/news/' => ROOT_PATH.'images/news/',
		'images/product_images/info_images/' => ROOT_PATH.'images/product_images/info_images/',
		'images/product_images/original_images/' => ROOT_PATH.'images/product_images/original_images/',
		'images/product_images/popup_images/' => ROOT_PATH.'images/product_images/popup_images/',
		'images/product_images/thumbnail_images/' => ROOT_PATH.'images/product_images/thumbnail_images/',
		'images/shipping_status/' => ROOT_PATH.'images/shipping_status/',
		'media/export/' => ROOT_PATH.'media/export/',
		'media/import/' => ROOT_PATH.'media/import/',
		'media/products/' => ROOT_PATH.'media/products/',
		'tmp/' => ROOT_PATH.'tmp/',
		'config.php' => ROOT_PATH.'config.php',
		'htaccess.txt' => ROOT_PATH.'htaccess.txt',
	);
}

















