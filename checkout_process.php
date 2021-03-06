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

$payment_modules->before_process();