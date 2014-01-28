<?php
/*
	Plugin Name: Печать квитанции
	Plugin URI: http://osc-cms.com/store/plugins/print_kvitancia
	Version: 1.0
	Description: Плагин для печати квитанции
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Print
*/

add_action('page', 'print_kvitancia_page');
add_filter('admin_print_menu', 'print_kvitancia_admin_menu');
add_filter('print_menu', 'print_kvitancia_menu');

// страница печати
function print_kvitancia_page()
{
	include 'print_kvitancia_page.php';
}

// печать в админке
function print_kvitancia_admin_menu($value)
{
	if ($value['params']['payment_method'] == 'kvitancia' && !empty($value['params']['order_id']))
	{
		$value['link'][] = array(
			'name' => 'Квитанция',
			'href' => _HTTP.'index.php?page=print_kvitancia_page&oID='.$value['params']['order_id'],
		);
	}
	return $value;
}

// печать на странице истории заказа покупателя
function print_kvitancia_menu($value)
{
	if ($value['params']['payment_method'] == 'kvitancia' && !empty($value['params']['order_id']))
	{
		$value['link'][] = array(
			'name' => 'Квитанция',
			'href' => _HTTP.'index.php?page=print_kvitancia_page&oID='.$value['params']['order_id'],
		);
	}
	return $value;
}