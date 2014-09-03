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

function returnMsgCheck($code, $request, $txt = '')
{
	return '<?xml version="1.0" encoding="UTF-8"?>
	<checkOrderResponse
		performedDatetime="'.$request['requestDatetime'].'"
		code="'.$code.'"
		invoiceId="'.$request['invoiceId'].'"
		shopId="'.MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID.'"
		'.(($txt) ? ' message="'.$txt.'" techMessage="'.$txt.'" ' : '').'
	"/>';
}

$request = $_POST;

/*$fp = fopen('_yandex_money.log', 'a+');
$str=date('Y-m-d H:i:s').' - ';
foreach ($_REQUEST as $vn=>$vv) {
  $str.=$vn.'='.$vv.';';
}
fwrite($fp, $str."\n");
fclose($fp);*/

// Проверка id магазина
if(MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID !== $request['shopId'])
{
	echo returnMsgCheck(1, $request, 'error: shopId');
	die();
}

$md5 = strtoupper(md5($request['action'].';'.$request['orderSumAmount'].';'.$request['orderSumCurrencyPaycash'].';'.$request['orderSumBankPaycash'].';'.$request['shopId'].';'.$request['invoiceId'].';'.$request['customerNumber'].';'.MODULE_PAYMENT_YANDEX_MERCHANT_SECRET_KEY));

if ($md5 == $request['md5'])
{
	// данные заказа
	$order = new order((int)$request['orderNumber']);

	global $osPrice;
	$orderTotal = number_format($osPrice->CalculateCurrEx($order->info['total'], 'RUR'), 2, '.', '');

	if ($action == 'checkOrder')
	{
		// Проверяем стоимость
		if ($request['orderSumAmount'] != $orderTotal OR $request['orderSumAmount'] <= 0)
			echo returnMsgCheck(100, $request);
		else
			echo returnMsgCheck(0, $request);

	}
	elseif ($action == 'paymentAviso')
	{
		$sql_data_array = array
		(
			'orders_status' => MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID,
			'paid' => 1
		);
		os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".(int)$request['orderNumber']."'");

		$sql_data_arrax = array
		(
			'orders_id' => (int)$request['orderNumber'],
			'orders_status_id' => MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID,
			'date_added' => 'now()',
			'customer_notified' => '0',
			'comments' => 'Yandex.Money accepted this order payment '
		);
		os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

		echo '<?xml version="1.0" encoding="UTF-8"?><paymentAvisoResponse performedDatetime="'.$request['requestDatetime'].'" code="0" invoiceId="'.$request['invoiceId'].'" shopId="'.MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID.'"/>';
	}
}
else
	echo returnMsgCheck(1, $request, 'error: md5 or checkOrder');