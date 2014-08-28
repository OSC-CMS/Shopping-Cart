<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
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

// выбранный метод оплаты
require (_CLASS.'payment.php');
$payment_modules = new payment($_SESSION['payment']);

// выбранный метод доставки
require (_CLASS.'shipping.php');
$shippingModules = new shipping($_SESSION['shipping']);

require (_CLASS.'order.php');
$order = new order();

if (!$$_SESSION['payment']->form_action_url)
	$payment_modules->before_process();

require (_CLASS.'order_total.php');
$order_total_modules = new order_total();

$order_totals = $order_total_modules->process();

// Формируем заказ
$aNewOrder = $cartet->order->newOrder($order, $order_totals, $order_total_modules);
$order_id = $aNewOrder['order_id'];

$order_totals = $order_total_modules->apply_credit();

$cartet->order->beforeProcess($order_id);

require_once(_INCLUDES.'affiliate_checkout_process.php');

$payment_modules->after_process();

// чистим корзину
$_SESSION['cart']->reset(true);

// очищаем сессионные данные
unset($_SESSION['sendto']);
unset($_SESSION['billto']);
unset($_SESSION['shipping']);
unset($_SESSION['payment']);
unset($_SESSION['comments']);
unset($_SESSION['last_order']);
unset($_SESSION['tmp_oID']);

//GV Code Start
if (isset($_SESSION['credit_covers']))
	unset($_SESSION['credit_covers']);

$order_total_modules->clear_posts(); //ICW ADDED FOR CREDIT CLASS SYSTEM
// GV Code End

os_redirect(os_href_link(FILENAME_CHECKOUT_SUCCESS, 'order_id='.$order_id, 'SSL'));