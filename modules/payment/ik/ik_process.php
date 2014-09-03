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

function ikGetSign($post)
{
	$aParams = array();
	foreach ($post as $key => $value)
	{
		if (!preg_match('/ik_/', $key))
			continue;
		$aParams[$key] = $value;
	}

	unset($aParams['ik_sign']);

	if ($aParams['ik_pw_via'] == 'test_interkassa_test_xts')
		$key = MODULE_PAYMENT_IK_TEST_KEY;
	else
		$key = MODULE_PAYMENT_IK_SECRET_KEY;

	ksort ($aParams, SORT_STRING);
	array_push($aParams, $key);
	$signString = implode(':', $aParams);
	$sign = base64_encode(md5($signString, true));
	return $sign;
}

/*$fp = fopen('_ik.log', 'a+');
$str=date('Y-m-d H:i:s').' - ';
foreach ($_REQUEST as $vn=>$vv) {
  $str.=$vn.'='.$vv.';';
}
fwrite($fp, $str."\n");
fclose($fp);*/

$ik_co_id = $_POST['ik_co_id'];
$ik_inv_st = $_POST['ik_inv_st'];
$ik_pm_no = $_POST['ik_pm_no'];
$ik_pw_via = $_POST['ik_pw_via'];
$ik_am = $_POST['ik_am'];
$ik_sign = $_POST['ik_sign'];

// Проверка id магазина
if(MODULE_PAYMENT_IK_CO_ID !== $ik_co_id)
	die('error: ik_co_id');

$sign = ikGetSign($_POST);

if ($ik_sign === $sign && $ik_inv_st == 'success')
{
	// данные заказа
	$order = new order((int)$ik_pm_no);
	$ikCurrency = (MODULE_PAYMENT_IK_CURRENCY == 'RUB') ? 'RUR' : MODULE_PAYMENT_IK_CURRENCY;

	global $osPrice;
	$orderTotal = number_format($osPrice->CalculateCurrEx($order->info['total'], $ikCurrency), 2, '.', '');

	// Проверяем стоимость
	if($ik_am != $orderTotal OR $ik_am <= 0)
		die("error: ik_am");

	$sql_data_array = array
	(
		'orders_status' => MODULE_PAYMENT_IK_ORDER_STATUS_ID,
		'paid' => 1
	);
	os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".(int)$ik_pm_no."'");

	$sql_data_arrax = array
	(
		'orders_id' => (int)$ik_pm_no,
		'orders_status_id' => MODULE_PAYMENT_IK_ORDER_STATUS_ID,
		'date_added' => 'now()',
		'customer_notified' => '0',
		'comments' => 'InterKassa accepted this order payment '.os_db_prepare_input($ik_pw_via)
	);
	os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

	echo 'OK'.$ik_pm_no;
}
else
	die('error: ik_sign or ik_inv_st');