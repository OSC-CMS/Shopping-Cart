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

// logging
/*$fp = fopen('yandex_merchant_process.log', 'a+');
$str=date('Y-m-d H:i:s').' - ';
foreach ($_REQUEST as $vn=>$vv) {
  $str .= ' ---- '.$vn.'='.$vv.';'."\n";
}
fwrite($fp, $str."\n");
fclose($fp);*/

$order_id = $_POST['orderNumber'];
$invoiceId = $_POST['invoiceId'];

function ym_return_msg($t)
{
	$dt = new DateTime();
	$performedDatetime = $dt->format('c');

	$action = $_POST['action'];
	if ($action === 'paymentAviso')
		$responce = 'paymentAvisoResponse';
	elseif($action === 'checkOrder')
		$responce = 'checkOrderResponse';
	else
		$responce = '';

	print '<?xml version="1.0" encoding="UTF-8"?> 
	<'.$responce.' performedDatetime="'.$performedDatetime.'" 
	code="200" invoiceId="'.(int)$_POST['invoiceId'].'" 
	message="'.$t.'" shopId="'.(int)$_POST['shopId'].'"/>';

	die();
}

// Проверяем ID магазина
if (MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID !== $_POST['shopId'])
{
	ym_return_msg('error: shopId');
}

// Проверяем подпись
$md5 = strtoupper(md5($_POST['action'].';'.$_POST['orderSumAmount'].';'.$_POST['orderSumCurrencyPaycash'].';'.$_POST['orderSumBankPaycash'].';'.MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID.';'.$invoiceId.';'.$_POST['customerNumber'].';'.MODULE_PAYMENT_YANDEX_MERCHANT_KEY));
if ($md5 !== $_POST['md5'])
{
	ym_return_msg("error: md5");
}

require (_CLASS.'order.php');
$order = new order($order_id);

// Проверяем сумму заказа
$orderTotal = number_format($order->info['total_value'], 2, '.', '');

if ($orderTotal !== $_POST['orderSumAmount'])
{
	ym_return_msg('error: orderSumAmount');
}

if ($_POST['action'] == 'paymentAviso')
{
	$sql_data_array = array
	(
		'orders_status' => MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID,
		'paid' => 1
	);
	os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".(int)$order_id."'");

	$sql_data_arrax = array
	(
		'orders_id' => (int)$order_id,
		'orders_status_id' => MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID,
		'date_added' => 'now()',
		'customer_notified' => '0',
		'comments' => 'Yandex.Money accepted this order payment '
	);
	os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

	$datetime = new DateTime();
	$performedDatetime = $datetime->format('c');
	print '<?xml version="1.0" encoding="UTF-8"?> 
	<paymentAvisoResponse performedDatetime="'.$performedDatetime.'" 
	code="0" invoiceId="'.$invoiceId.'" 
	shopId="'.MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID.'"/>';
}
elseif ($_POST['action'] == 'checkOrder')
{
	$datetime = new DateTime();
	$performedDatetime = $datetime->format('c');
	print '<?xml version="1.0" encoding="UTF-8"?> 
	<checkOrderResponse performedDatetime="'.$performedDatetime.'" 
	code="0" invoiceId="'.$invoiceId.'" 
	shopId="'.MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID.'"/>';
}