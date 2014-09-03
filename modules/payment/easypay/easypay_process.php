<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*	Copyright (c) 2012 VamShop
*---------------------------------------------------------
*/

function get_var($name, $default = 'none')
{
	return (isset($_GET[$name])) ? $_GET[$name] : ((isset($_POST[$name])) ? $_POST[$name] : $default);
}

require('includes/top.php');
require (_CLASS.'order.php');

#Сбор параметров
$status		= 2;
$web_key	= MODULE_PAYMENT_EASYPAY_WEBKEY;

$params		= array(
	"date"				=> date("d-m-Y H:i:s"),
	"ip"				=> $_SERVER["REMOTE_ADDR"],
	"order_mer_code"	=> $_POST["order_mer_code"],
	"sum"				=> $_POST["sum"],
	"mer_no"			=> $_POST["mer_no"],
	"card"				=> $_POST["card"],
	"purch_date"		=> $_POST["purch_date"],
	"notify_signature"	=> $_POST["notify_signature"]
);

#сравнение электронных подписей
$check = md5($params["order_mer_code"].$params["sum"].$params["mer_no"].$params["card"].$params["purch_date"].$web_key) == $params["notify_signature"];
$check = true;
if($check)
{
	// checking and handling
	if (number_format($params["sum"], 0) == number_format($order->info['total_value'],0))
	{
		$sql_data_array = array('orders_status' => MODULE_PAYMENT_EASYPAY_ORDER_STATUS_ID, 'paid' => 1);
		os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".$inv_id."'");

		$sql_data_arrax = array(
			'orders_id' => $inv_id,
			'orders_status_id' => MODULE_PAYMENT_EASYPAY_ORDER_STATUS_ID,
			'date_added' => 'now()',
			'customer_notified' => '0',
			'comments' => 'EasyPay accepted this order payment'
		);
		os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

		echo 'OK'.$inv_id;
	}
}
?>