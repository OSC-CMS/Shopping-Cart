<?php
/*
	Plugin Name: Печать заказа
	Plugin URI: http://osc-cms.com/store/plugins/print_order
	Version: 1.0
	Description: Плагин для печати заказа
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Print
*/

add_action('page', 'print_order_page');
add_filter('admin_print_menu', 'print_order_admin_menu');
add_filter('print_menu', 'print_order_menu');

// страница печати
function print_order_page()
{
	include 'print_order_page.php';
}

// печать в админке
function print_order_admin_menu($value)
{
	if (!empty($value['params']['order_id']))
	{
		$value['link'][] = array(
			'name' => 'Заказ',
			'href' => _HTTP.'index.php?page=print_order_page&oID='.$value['params']['order_id'],
		);
	}
	return $value;
}

// печать на странице истории заказа покупателя
function print_order_menu($value)
{
	if (!empty($value['params']['order_id']))
	{
		$value['link'][] = array(
			'name' => 'Заказ',
			'href' => _HTTP.'index.php?page=print_order_page&oID='.$value['params']['order_id'],
		);
	}
	return $value;
}