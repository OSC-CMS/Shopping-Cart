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

function ym_return_msg($text = '', $code = 200)
{
	$shopId = (int)$_POST['shopId'];
	$invoiceId = (int)$_POST['invoiceId'];

	$dt = new DateTime();
	$performedDatetime = $dt->format('c');

	$responce = ($_POST['action'] === 'paymentAviso') ? 'paymentAvisoResponse' : 'checkOrderResponse';
	$text = (!empty($text)) ? 'message="'.$text.'"' : '';

	echo '<?xml version="1.0" encoding="UTF-8"?> 
	<'.$responce.' performedDatetime="'.$performedDatetime.'" 
	code="'.$code.'" invoiceId="'.$invoiceId.'" 
	'.$text.' shopId="'.$shopId.'"/>';

	die();
}

if (MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID !== $_POST['shopId'])
{
	echo ym_return_msg('error: shopId');
	die();
}

$md5 = strtoupper(md5($_POST['action'].';'.$_POST['orderSumAmount'].';'.$_POST['orderSumCurrencyPaycash'].';'.$_POST['orderSumBankPaycash'].';'.MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID.';'.$_POST['invoiceId'].';'.$_POST['customerNumber'].';'.MODULE_PAYMENT_YANDEX_MERCHANT_KEY));
if ($md5 !== $_POST['md5'])
	ym_return_msg("error: md5");

$order = new order((int)$_POST['orderNumber']);

global $osPrice;
$orderTotal = number_format($osPrice->CalculateCurrEx($order->info['total'], 'RUR'), 2, '.', '');

if (floatval($orderTotal) !== floatval($_POST['orderSumAmount']))
	ym_return_msg("error: orderSumAmount");

if ($_POST['action'] == 'paymentAviso')
{
	$sql_data_array = array
	(
		'orders_status' => MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID
	);
	os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".(int)$_POST['orderNumber']."'");

	$sql_data_arrax = array
	(
		'orders_id' => (int)$_POST['orderNumber'],
		'orders_status_id' => MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID,
		'date_added' => 'now()',
		'customer_notified' => '0',
		'comments' => 'Yandex.Money accepted this order payment '
	);
	os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

	ym_return_msg('', '0');
}
elseif ($_POST['action'] == 'checkOrder')
{
	ym_return_msg('', '0');
}