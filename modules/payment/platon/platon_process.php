<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require('includes/top.php');
require (_CLASS.'order.php');

function get_var($name, $default = 'none')
{
	return (isset($_GET[$name])) ? $_GET[$name] : ((isset($_POST[$name])) ? $_POST[$name] : $default);
}

$amount = get_var('amount');
$order_id = get_var('order');
$sign = get_var('sign');
$status = get_var('status');
$email = get_var('email');
$card = get_var('card');

if ($status !== 'SALE')
	die('Incorrect status');

$my_sign = md5(strtoupper(strrev($email).MODULE_PAYMENT_PLATON_PASSWORD.$order_id.strrev(substr($card,0,6).substr($card,-4))));
if ($sign !== $my_sign)
	die("Bad sign");

$order = new order((int)$order_id);
if ($amount != round($order->info['total'], 2) || $amount <= 0)
	die("incorrect price");

$sql_data_array = array('orders_status' => MODULE_PAYMENT_PLATON_ORDER_STATUS);
os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".(int)$order_id."'");

$sql_data_arrax = array(
	'orders_id' => (int)$order_id,
	'orders_status_id' => MODULE_PAYMENT_PLATON_ORDER_STATUS,
	'date_added' => 'now()',
	'customer_notified' => '0',
	'comments' => 'Platon accepted this order payment'
);
os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

echo 'OK'.$order_id;
?>