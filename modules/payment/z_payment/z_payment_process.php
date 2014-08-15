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

//Проверяем номер магазина
if (get_var('LMI_PAYEE_PURSE') != MODULE_PAYMENT_Z_PAYMENT_ID)
	die("ERR: Id магазина не соответсвует настройкам сайта!");

// данные заказа
$order = new order(get_var('LMI_PAYMENT_NO'));

if ($order->info['total_value'] != get_var('LMI_PAYMENT_AMOUNT'))
	die("ERR: Сумма оплаты не соответсвует сумме заказа!");

$CalcHash = md5(get_var('LMI_PAYEE_PURSE').get_var('LMI_PAYMENT_AMOUNT').get_var('LMI_PAYMENT_NO').get_var('LMI_MODE').get_var('LMI_SYS_INVS_NO').get_var('LMI_SYS_TRANS_NO').get_var('LMI_SYS_TRANS_DATE').get_var('MODULE_PAYMENT_Z_PAYMENT_SECRET_KEY').get_var('LMI_PAYER_PURSE').get_var('LMI_PAYER_WM'));

//Сравниваем значение расчетного хеша с полученным
if (get_var('LMI_HASH') == strtoupper($CalcHash))
{
	//Все прошло успешно
	$sql_data_array = array(
		'orders_status' => MODULE_PAYMENT_Z_PAYMENT_ORDER_STATUS_ID
	);
	os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".(int)get_var('LMI_PAYMENT_NO')."'");

	$sql_data_arrax = array(
		'orders_id' => (int)get_var('LMI_PAYMENT_NO'),
		'orders_status_id' => MODULE_PAYMENT_Z_PAYMENT_ORDER_STATUS_ID,
		'date_added' => 'now()',
		'customer_notified' => '0',
		'comments' => 'Z-Payment accepted this order payment'
	);
	os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

	echo 'YES';
}