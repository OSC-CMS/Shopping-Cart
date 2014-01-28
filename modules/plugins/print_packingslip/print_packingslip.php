<?php
/*
	Plugin Name: Печать накладной
	Plugin URI: http://osc-cms.com/store/plugins/print_packingslip
	Version: 1.0
	Description: Плагин для печати накладной
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Print
*/

add_action('page', 'print_packingslip_page');
add_filter('admin_print_menu', 'print_packingslip_admin_menu');
add_filter('print_menu', 'print_packingslip_menu');

// страница печати
function print_packingslip_page()
{
	include 'print_packingslip_page.php';
}

// печать в админке
function print_packingslip_admin_menu($value)
{
	if (!empty($value['params']['order_id']))
	{
		$value['link'][] = array(
			'name' => 'Накладная',
			'href' => _HTTP.'index.php?page=print_packingslip_page&oID='.$value['params']['order_id'],
		);
	}
	return $value;
}

// печать на странице истории заказа покупателя
function print_packingslip_menu($value)
{
	if (!empty($value['params']['order_id']))
	{
		$value['link'][] = array(
			'name' => 'Накладная',
			'href' => _HTTP.'index.php?page=print_packingslip_page&oID='.$value['params']['order_id'],
		);
	}
	return $value;
}