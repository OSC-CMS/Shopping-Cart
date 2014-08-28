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

if (!isset($_SESSION['customer_id']))
{
	os_redirect(os_href_link(FILENAME_DEFAULT));
}

if (isset ($_GET['action']) && ($_GET['action'] == 'update'))
{
	if ($_SESSION['account_type'] != 1)
		os_redirect(os_href_link(FILENAME_DEFAULT));
	else
		os_redirect(os_href_link(FILENAME_LOGOFF));
}

$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_SUCCESS);
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_SUCCESS);

require (dir_path('includes').'header.php');

$osTemplate->assign('FORM_ACTION', os_draw_form('order', os_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update', 'SSL')));
$osTemplate->assign('BUTTON_CONTINUE', button_continue_submit());
$osTemplate->assign('FORM_END', '</form>');

$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id='".$_SESSION['customer_id']."'");
if ($gv_result = os_db_fetch_array($gv_query))
{
	if ($gv_result['amount'] > 0)
	{
		$osTemplate->assign('GV_SEND_LINK', os_href_link(FILENAME_GV_SEND));
	}
}

$order_id = $_GET['order_id'];

include (dir_path('class').'order.php');
$order = new order($order_id);

$osTemplate->assign('order', $order);

// Оплата
$payment_method = $order->info['payment_method'];

require (_CLASS.'payment.php');
$payment_modules = new payment($payment_method);

// Если метод оплаты требует перехода на сайт сервиса, то посылаем на form_action_url,
// в противном случае форма отправляет на окончательное формирование заказа
if (isset ($$payment_method->form_action_url) && !$$payment_method->tmpOrders)
{
	// Заполенные поля методов оплаты(если есть)
	if (is_array($payment_modules->modules))
	{
		if ($confirmation = $payment_modules->confirmation())
		{
			$payment_info = $confirmation['title'];
			for ($i = 0, $n = sizeof($confirmation['fields']); $i < $n; $i++)
			{
				$payment_info .= '<table>
			<tr><td>'.$confirmation['fields'][$i]['title'] . '</td>
			<td>'.stripslashes($confirmation['fields'][$i]['field']).'</td>
			</tr></table>';
			}
			$osTemplate->assign('PAYMENT_INFORMATION', $payment_info);
		}
	}

	$form_action_url = $$payment_method->form_action_url;
	$osTemplate->assign('PAID', true);

	if (isset($$payment_method->form_action_method) && !empty($$payment_method->form_action_method))
		$form_action_method = $$payment_method->form_action_method;
	else
		$form_action_method = 'post';

	$osTemplate->assign('CHECKOUT_FORM', os_draw_form('checkout_confirmation', $form_action_url, $form_action_method));
	$osTemplate->assign('CHECKOUT_FORM_END', '</form>');

	// метод класса оплаты process_button
	$payment_button = '';
	if (is_array($payment_modules->modules))
	{
		$payment_button .= $payment_modules->process_button();
	}
	$osTemplate->assign('MODULE_BUTTONS', $payment_button);
	$osTemplate->assign('CHECKOUT_BUTTON', buttonSubmit('button_confirm_order.gif', null, TEXT_BUTTON_PAY_NOW));
}
else
	$osTemplate->assign('PAID', false);

// фильтр кнопок печати
$array = array();
$array['params'] = array('order_id' => $order_id, 'payment_method' => $order->info['payment_method']);
$array = apply_filter('print_menu', $array);

if (is_array($array['link']) && !empty($array['link']))
{
	$osTemplate->assign('filterPrint', $array['link']);
}
// фильтр кнопок печати

do_action('checkout_success');

if (DOWNLOAD_ENABLED == 'true')
{
	include (DIR_WS_MODULES.'downloads.php');
}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/checkout_success.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_CHECKOUT_SUCCESS.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_CHECKOUT_SUCCESS.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');