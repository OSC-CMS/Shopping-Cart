<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include ('includes/top.php');

// Если нет сессии покупателя, то перекидываем на страницу входа
if (!isset($_SESSION['customer_id']))
{
	os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));
}

// Если пользователю не выводит цены, то перекидываем на главную
if ($_SESSION['customers_status']['customers_status_show_price'] != '1')
{
	os_redirect(os_href_link(FILENAME_DEFAULT, '', ''));
}

if (!isset($_SESSION['sendto']))
{
	os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

if ((os_not_null(MODULE_PAYMENT_INSTALLED)) && (!isset($_SESSION['payment'])))
{
	os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID']))
{
	if ($_SESSION['cart']->cartID != $_SESSION['cartID'])
	{
		os_redirect(os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
	}
}

// load selected payment module
require (_CLASS.'payment.php');
$payment_modules = new payment($_SESSION['payment']);

//require (_CLASS.'shipping.php');
//$shippingModules = new shipping($_SESSION['shipping']);

require (_CLASS.'order.php');
$order = new order();

$payment_modules->before_process();

require (_CLASS.'order_total.php');
$order_total_modules = new order_total();

$order_totals = $order_total_modules->process();

// check if tmp order id exists
if (isset ($_SESSION['tmp_oID']) && is_int($_SESSION['tmp_oID']))
{
	$tmp = false;
	$insert_id = $_SESSION['tmp_oID'];
}
else
{
	// check if tmp order need to be created
	if (isset($$_SESSION['payment']->form_action_url) && $$_SESSION['payment']->tmpOrders)
	{
		$tmp = true;
		$tmp_status = $$_SESSION['payment']->tmpStatus;
	}
	else
	{
		$tmp = false;
		$tmp_status = $order->info['order_status'];
	}

	// Формируем заказ
	$aNewOrder = $cartet->order->newOrder($order, $order_totals, $order_total_modules);

	// redirect to payment service
	if ($tmp)
		$payment_modules->payment_action();
}

if (!$tmp)
{
	$insert_id = $aNewOrder['insert_id'];

	// NEW EMAIL configuration !
	$order_totals = $order_total_modules->apply_credit();

	do_action('send_order');

	include ('send_order.php');

	require_once(_INCLUDES.'affiliate_checkout_process.php');

	// load the after_process function from the payment modules
	$payment_modules->after_process();

	$_SESSION['cart']->reset(true);

	// unregister session variables used during checkout
	unset ($_SESSION['sendto']);
	unset ($_SESSION['billto']);
	unset ($_SESSION['shipping']);
	unset ($_SESSION['payment']);
	unset ($_SESSION['comments']);
	unset ($_SESSION['last_order']);
	unset ($_SESSION['tmp_oID']);
	unset ($_SESSION['cc']);
	$last_order = $insert_id;

	//GV Code Start
	if (isset ($_SESSION['credit_covers']))
		unset ($_SESSION['credit_covers']);

	$order_total_modules->clear_posts(); //ICW ADDED FOR CREDIT CLASS SYSTEM
	// GV Code End

	os_redirect(os_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
}
?>