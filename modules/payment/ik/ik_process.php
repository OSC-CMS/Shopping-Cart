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

// logging
/*
$fp = fopen('ik.log', 'a+');
$str=date('Y-m-d H:i:s').' - ';
foreach ($_POST as $vn=>$vv){ $str.='_POST '.$vn.'='.$vv.';'; }
fwrite($fp, $str."\n");
fclose($fp);
*/

$ik_shop_id = $_POST['ik_shop_id'];
$ik_payment_amount = $_POST['ik_payment_amount'];
$ik_payment_id = $_POST['ik_payment_id'];
$ik_payment_desc = $_POST['ik_payment_desc'];
$ik_paysystem_alias = $_POST['ik_paysystem_alias'];
$ik_baggage_fields = $_POST['ik_baggage_fields'];
$ik_payment_state = $_POST['ik_payment_state'];
$ik_trans_id = $_POST['ik_trans_id'];
$ik_currency_exch = $_POST['ik_currency_exch'];
$ik_fees_payer = $_POST['ik_fees_payer'];
$ik_sign_hash = $_POST['ik_sign_hash'];

$ik_sign_hash_str = $ik_shop_id.':'.$ik_payment_amount.':'.$ik_payment_id.':'.$ik_paysystem_alias.':'.$ik_baggage_fields.':'.$ik_payment_state.':'.$ik_trans_id.':'.$ik_currency_exch.':'.$ik_fees_payer.':'.MODULE_PAYMENT_IK_SECRET_KEY;
$hash = md5($ik_sign_hash_str);

// если статус success - оплачено
if ($ik_payment_state == 'success')
{
	if (strtoupper($ik_sign_hash) == strtoupper($hash))
	{
		$sql_data_array = array
		(
			'orders_status' => MODULE_PAYMENT_IK_ORDER_STATUS_ID
		);
		os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".(int)$ik_payment_id."'");

		$sql_data_arrax = array
		(
			'orders_id' => (int)$ik_payment_id,
			'orders_status_id' => MODULE_PAYMENT_IK_ORDER_STATUS_ID,
			'date_added' => 'now()',
			'customer_notified' => '0',
			'comments' => 'InterKassa accepted this order payment'
		);
		os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

		echo 'OK'.$ik_payment_id;
	}
}
?>