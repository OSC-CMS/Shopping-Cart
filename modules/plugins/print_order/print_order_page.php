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
	include (_CLASS.'order.php');
	$order = new order($oID);

	$osTemplate->assign('address_label_customer', os_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
	$osTemplate->assign('address_label_shipping', os_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
	$osTemplate->assign('address_label_payment', os_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
	$osTemplate->assign('csID', $order->customer['csID']);
	$order_total = $order->getTotalData($oID);
	$osTemplate->assign('order_data', $order->getOrderData($oID));
	$osTemplate->assign('order_total', $order_total['data']);
	$osTemplate->assign('language', $_SESSION['language']);
	$osTemplate->assign('oID', (int) $_GET['oID']);
	if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment')
	{
		include (DIR_FS_DOCUMENT_ROOT.'/modules/payment/'.$order->info['payment_method'].'/'.$_SESSION['language'].'.php');
		$payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
	}
	$osTemplate->assign('PAYMENT_METHOD', $payment_method);
	if ($order->info['shipping_method'] != '') {
		$shipping_method = $order->info['shipping_method'];
	}
	$osTemplate->assign('SHIPPING_METHOD', $shipping_method);
	$osTemplate->assign('COMMENT', $order->info['comments']);
	$osTemplate->assign('DATE', os_date_long($order->info['date_purchased']));
	$osTemplate->assign('tpl_path', _THEMES_C);
	$osTemplate->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'images/');
}
else
{
	$osTemplate->assign('ERROR', 'You are not allowed to view this order!');
}

$osTemplate->caching = false;
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->display(dirname(__FILE__).'/print_order_page.html');