<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

global $cartet, $osTemplate;

// Order ID
$oID = $_GET['oID'];

$order_query_check = os_db_query("SELECT customers_id FROM ".TABLE_ORDERS." WHERE orders_id='".(int)$oID."'");
$order_check = os_db_fetch_array($order_query_check);

if (($_SESSION['customer_id'] == $order_check['customers_id']) OR ($_SESSION['customers_status']['customers_status_id'] == 0))
{
	$persons_query = os_db_query("SELECT * FROM ".TABLE_PERSONS." WHERE orders_id='".(int)$oID."'");
	$persons = os_db_fetch_array($persons_query);

	$osTemplate->assign('kvit_name', $persons['name']);
	$osTemplate->assign('kvit_address', $persons['address']);

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

	$osTemplate->assign('module_1', MODULE_PAYMENT_KVITANCIA_1);
	$osTemplate->assign('module_2', MODULE_PAYMENT_KVITANCIA_2);
	$osTemplate->assign('module_3', MODULE_PAYMENT_KVITANCIA_3);
	$osTemplate->assign('module_4', MODULE_PAYMENT_KVITANCIA_4);
	$osTemplate->assign('module_5', MODULE_PAYMENT_KVITANCIA_5);
	$osTemplate->assign('module_6', MODULE_PAYMENT_KVITANCIA_6);
	$osTemplate->assign('module_7', MODULE_PAYMENT_KVITANCIA_7);
	$osTemplate->assign('module_8', MODULE_PAYMENT_KVITANCIA_8);

	// assign language to template for caching
	$osTemplate->assign('language', $_SESSION['language']);
	$osTemplate->assign('charset', $_SESSION['language_charset']);
	$osTemplate->assign('oID', (int)$oID);

	$payment_method = '';
	if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment')
	{
		include (_MODULES.'/payment/' . $order->info['payment_method'] . '/'.$_SESSION['language'].'.php');
		$payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
	}
	$osTemplate->assign('PAYMENT_METHOD', $payment_method);
	$osTemplate->assign('COMMENT', $order->info['comments']);
	$osTemplate->assign('DATE', os_date_short($order->info['date_purchased']));
}
else
{
	$osTemplate->assign('ERROR', 'You are not allowed to view this order!');
}

$osTemplate->caching = false;
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->display(dirname(__FILE__).'/print_kvitancia_page.html');