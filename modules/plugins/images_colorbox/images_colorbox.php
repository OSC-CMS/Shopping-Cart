<?php
/*
	Plugin Name: ColorBox
	Plugin URI: http://osc-cms.com/extend/themes
	Version: 1.0
	Description: Плагин увеличения картинок используя ColorBox
	Author: OSC-CMS
	Author URI: http://osc-cms.com
	Plugin Group: Products
*/

if (is_page('product_info'))
{
	add_filter('products_image_block', 'products_image_block_colorbox');
	add_filter('products_mo_image_block', 'products_mo_image_block_colorbox');
	add_filter('head_array_detail', 'head_array_detail_colorbox_head');

	function head_array_detail_colorbox_head($_value)
	{
		add_style(plugurl().'colorbox/colorbox.css', $_value, 'colorbox');
		add_js(plugurl().'colorbox/jquery.colorbox.js', $_value, 'colorbox');
		add_js(plugurl().'colorbox/jquery.colorbox.js.js', $_value, 'colorbox');
		return $_value;
	}

	function products_image_block_colorbox($_value)
	{
		global $image_pop;
		global $product;
		global $image;
		
		$_value = '<a class="colorbox-group" href="'.$image_pop.'" title="'.$product->data['products_name'].'" target="_blank"><img src="'.$image.'"  alt="'.$product->data['products_name'].'" /></a>';

		return $_value;
	}

	function products_mo_image_block_colorbox($_value)
	{
		global $product; 

		$_value['PRODUCTS_MO_IMAGE_BLOCK'] =  '<a class="colorbox-group" href="'.$_value['PRODUCTS_MO_POPUP_IMAGE'].'" title="'.$product->data['products_name'].'" target="_blank"><img src="'.$_value['PRODUCTS_MO_IMAGE'].'"  alt="'.$product->data['products_name'].'" /></a>';

		return $_value;
	}
}
?>