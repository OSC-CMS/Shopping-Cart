<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function get_var($name, $default = 'none')
{
	return (isset($_GET[$name])) ? $_GET[$name] : ((isset($_POST[$name])) ? $_POST[$name] : $default);
}

require('includes/top.php');
require (_CLASS.'order.php');

// logging
//$fp = fopen('paymaster.log', 'a+');
//$str=date('Y-m-d H:i:s').' - ';
//foreach ($_REQUEST as $vn=>$vv) {
  //$str.=$vn.'='.$vv.';';
//}

//fwrite($fp, $str."\n");
//fclose($fp);

// variables prepearing
$crc = get_var('LMI_HASH');

$inv_id = get_var('LMI_PAYMENT_NO');
$order = new order($inv_id);
$order_sum = $order->info['total_value'];

$hash = base64_encode(md5($_POST['LMI_MERCHANT_ID'].';'.$_POST['LMI_PAYMENT_NO'].';'.$_POST['LMI_SYS_PAYMENT_ID'].';'.$_POST['LMI_SYS_PAYMENT_DATE'].';'.$_POST['LMI_PAYMENT_AMOUNT'].';'.$_POST['LMI_CURRENCY'].';'.$_POST['LMI_PAID_AMOUNT'].';'. $_POST['LMI_PAID_CURRENCY'].';'.$_POST['LMI_PAYMENT_SYSTEM'].';'.$_POST['LMI_SIM_MODE'].';'.MODULE_PAYMENT_PAYMASTER_SECRET_KEY, true));

// checking and handling
if ($hash == $crc)
{
	if (number_format($_POST['LMI_PAYMENT_AMOUNT'],0) == number_format($order->info['total_value'],0))
	{
		$sql_data_array = array('orders_status' => MODULE_PAYMENT_PAYMASTER_ORDER_STATUS_ID);
		os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".$inv_id."'");

		$sql_data_arrax = array(
			'orders_id' => $inv_id,
			'orders_status_id' => MODULE_PAYMENT_PAYMASTER_ORDER_STATUS_ID,
			'date_added' => 'now()',
			'customer_notified' => '0',
			'comments' => 'PayMaster accepted this order payment'
		);
		os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

		echo 'YES';
	}
}