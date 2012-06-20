<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

include ('includes/top.php');

//$osTemplate = new osTemplate;

  $persons_query = os_db_query("SELECT * FROM ".TABLE_PERSONS."
  					WHERE orders_id='".(int)$_GET['oID']."'");
  					
  $persons = os_db_fetch_array($persons_query);

	$osTemplate->assign('kvit_name', $persons['name']);
	$osTemplate->assign('kvit_address', $persons['address']);

// check if custmer is allowed to see this order!
$order_query_check = os_db_query("SELECT
  					customers_id
  					FROM ".TABLE_ORDERS."
  					WHERE orders_id='".(int) $_GET['oID']."'");
$oID = (int) $_GET['oID'];
$order_check = os_db_fetch_array($order_query_check);
if ($_SESSION['customer_id'] == $order_check['customers_id']) {
	// get order data

	include (_CLASS.'order.php');
	$order = new order($oID);
	$osTemplate->assign('address_label_customer', os_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
	$osTemplate->assign('address_label_shipping', os_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
	$osTemplate->assign('address_label_payment', os_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
	$osTemplate->assign('csID', $order->customer['csID']);
	// get products data
	$order_total = $order->getTotalData($oID); 
	$osTemplate->assign('order_data', $order->getOrderData($oID));
	$osTemplate->assign('order_total', $order_total['data']);
	$osTemplate->assign('final_price', $order->info['total']);

	$osTemplate->assign('1', MODULE_PAYMENT_KVITANCIA_1);
	$osTemplate->assign('2', MODULE_PAYMENT_KVITANCIA_2);
	$osTemplate->assign('3', MODULE_PAYMENT_KVITANCIA_3);
	$osTemplate->assign('4', MODULE_PAYMENT_KVITANCIA_4);
	$osTemplate->assign('5', MODULE_PAYMENT_KVITANCIA_5);
	$osTemplate->assign('6', MODULE_PAYMENT_KVITANCIA_6);
	$osTemplate->assign('7', MODULE_PAYMENT_KVITANCIA_7);
	$osTemplate->assign('8', MODULE_PAYMENT_KVITANCIA_8);

	// assign language to template for caching
	$osTemplate->assign('language', $_SESSION['language']);
   $osTemplate->assign('charset', $_SESSION['language_charset']); 
	$osTemplate->assign('oID', (int) $_GET['oID']);
	if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
		include (_MODULES.'/payment/' . $order->info['payment_method'] . '/'.$_SESSION['language'].'.php');
		$payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
	}
	$osTemplate->assign('PAYMENT_METHOD', $payment_method);
	$osTemplate->assign('COMMENT', $order->info['comments']);
	$osTemplate->assign('DATE', os_date_short($order->info['date_purchased']));
	$path = DIR_WS_CATALOG._THEMES_C;
	$osTemplate->assign('tpl_path', $path);

	// dont allow cache
	$osTemplate->caching = false;

	$osTemplate->display(CURRENT_TEMPLATE.'/module/kvitancia.html');
} else {

	$osTemplate->assign('ERROR', 'You are not allowed to view this order!');
	$osTemplate->display(CURRENT_TEMPLATE.'/module/error_message.html');
}
?>