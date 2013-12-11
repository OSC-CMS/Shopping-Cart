<?php
/*
	Plugin Name: Счет
	Plugin URI: http://osc-cms.com/extend/themes
	Version: 1.0
	Description: Плагин для печати счета
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Print
*/

add_action('page', 'print_schet_page');
add_filter('admin_print_menu', 'print_schet_admin_menu');
add_filter('print_menu', 'print_schet_menu');

// страница печати
function print_schet_page()
{
	include 'print_schet_page.php';
}

// печать в админке
function print_schet_admin_menu($value)
{
	if ($value['params']['payment_method'] == 'schet' && !empty($value['params']['order_id']))
	{
		$value['link'][] = array(
			'name' => 'Счет',
			'href' => _HTTP.'index.php?page=print_schet_page&oID='.$value['params']['order_id'],
		);
	}
	return $value;
}

// печать на странице истории заказа покупателя
function print_schet_menu($value)
{
	if ($value['params']['payment_method'] == 'schet' && !empty($value['params']['order_id']))
	{
		$value['link'][] = array(
			'name' => 'Счет',
			'href' => _HTTP.'index.php?page=print_schet_page&oID='.$value['params']['order_id'],
		);
		//$value['link'][] = '<a class="btn" href="Javascript:void()" onclick="window.open(\''._HTTP.'index.php?page=print_schet_page&oID='.$value['params']['order_id'].'\', \'popup\', \'toolbar=0, width=640, height=600\')">Счет</a>';
	}
	return $value;
}