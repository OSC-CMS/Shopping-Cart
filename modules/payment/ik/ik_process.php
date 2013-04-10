<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require('includes/top.php');
require (_CLASS.'order.php');

// Данные от интеркассы
$ik_shop_id = $_POST['ik_shop_id'];
$ik_payment_amount = $_POST['ik_payment_amount'];
$ik_payment_id = $_POST['ik_payment_id'];
$ik_payment_desc = $_POST['ik_payment_desc'];
$ik_paysystem_alias = $_POST['ik_paysystem_alias'];
$ik_baggage_fields = $_POST['ik_baggage_fields'];
$ik_payment_timestamp = $_POST['ik_payment_timestamp'];
$ik_payment_state = $_POST['ik_payment_state'];
$ik_trans_id = $_POST['ik_trans_id'];
$ik_currency_exch = $_POST['ik_currency_exch'];
$ik_fees_payer = $_POST['ik_fees_payer'];
$ik_sign_hash = $_POST['ik_sign_hash'];

///////////////////////////////////////////////////////

// Проверка id магазина
if(MODULE_PAYMENT_IK_SHOP_ID !== $ik_shop_id)
	die('error: ik_shop_id');

// проверяем хэши
$ik_sign_hash_str = $ik_shop_id.':'.$ik_payment_amount.':'.$ik_payment_id.':'.$ik_paysystem_alias.':'.$ik_baggage_fields.':'.$ik_payment_state.':'.$ik_trans_id.':'.$ik_currency_exch.':'.$ik_fees_payer.':'.MODULE_PAYMENT_IK_SECRET_KEY;

if (strtoupper($ik_sign_hash) !== strtoupper(md5($ik_sign_hash_str)))
	die('error: ik_sign_hash');

///////////////////////////////////////////////////////

// данные заказа
$order = new order((int)$ik_payment_id);

// Сверяем стоимость заказа и что вернула там интеркасса
global $osPrice;
$orderTotal = number_format($osPrice->CalculateCurrEx($order->info['total'], MODULE_PAYMENT_IK_CURRENCY), 2, '.', '');

// Проверяем стоимость
if($ik_payment_amount != $orderTotal OR $ik_payment_amount <= 0)
	die("incorrect ik_payment_amount");

// если статус success
if ($ik_payment_state == 'success')
{
	$sql_data_array = array(
		'orders_status' => MODULE_PAYMENT_IK_ORDER_STATUS_ID
	);
	os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".(int)$ik_payment_id."'");

	$sql_data_arrax = array(
		'orders_id' => (int)$ik_payment_id,
		'orders_status_id' => MODULE_PAYMENT_IK_ORDER_STATUS_ID,
		'date_added' => 'now()',
		'customer_notified' => '0',
		'comments' => 'InterKassa accepted this order payment'
	);
	os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

	echo 'OK'.$ik_payment_id;
}

?>