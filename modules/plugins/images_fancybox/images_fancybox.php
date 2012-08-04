<?php
/*
	Plugin Name: FancyBox
	Plugin URI: http://osc-cms.com/extend/themes
	Version: 1.0
	Description: Плагин увеличения картинок используя FancyBox
	Author: OSC-CMS
	Author URI: http://osc-cms.com
	Plugin Group: Products
*/
/*
	Лицензия на использование http://fancyapps.com/fancybox/#license
*/

if (is_page('product_info'))
{
	add_filter('products_image_block', 'products_image_block_fancybox');
	add_filter('products_mo_image_block', 'products_mo_image_block_fancybox');
	add_filter('head_array_detail', 'head_array_detail_fancybox_head');

	function head_array_detail_fancybox_head($_value)
	{
		add_js(plugurl().'fancybox/jquery.fancybox.pack.js', $_value,  'fancybox');
		add_style(plugurl().'fancybox/jquery.fancybox.css', $_value, 'fancybox');
		add_style(plugurl().'fancybox/helpers/jquery.fancybox-buttons.css', $_value, 'fancybox');
		add_js(plugurl().'fancybox/helpers/jquery.fancybox-buttons.js', $_value, 'fancybox');
		add_js(plugurl().'fancybox/jquery.fancybox.js.js', $_value, 'fancybox');
		return $_value;
	}

	function products_image_block_fancybox($_value)
	{
		global $image_pop;
		global $product;
		global $image;
		
		$_value = '<a class="fancybox-buttons" data-fancybox-group="button" href="'.$image_pop.'" title="'.$product->data['products_name'].'" target="_blank"><img src="'.$image.'"  alt="'.$product->data['products_name'].'" /></a>';

		return $_value;
	}

	function products_mo_image_block_fancybox($_value)
	{
		global $product; 

		$_value['PRODUCTS_MO_IMAGE_BLOCK'] =  '<a class="fancybox-buttons" data-fancybox-group="button" href="'.$_value['PRODUCTS_MO_POPUP_IMAGE'].'" title="'.$product->data['products_name'].'" target="_blank"><img src="'.$_value['PRODUCTS_MO_IMAGE'].'"  alt="'.$product->data['products_name'].'" /></a>';

		return $_value;
	}
}
?>