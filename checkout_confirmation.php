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

if (!isset ($_SESSION['customer_id']))
	os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));

if ($_SESSION['cart']->count_contents() < 1)
	os_redirect(os_href_link(FILENAME_SHOPPING_CART));

if (isset ($_SESSION['cart']->cartID) && isset ($_SESSION['cartID'])) {
	if ($_SESSION['cart']->cartID != $_SESSION['cartID'])
		os_redirect(os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}

if (!isset ($_SESSION['shipping']))
	os_redirect(os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

if (isset ($_POST['payment']))
	$_SESSION['payment'] = os_db_prepare_input($_POST['payment']);

if ($_POST['comments_added'] != '')
	$_SESSION['comments'] = os_db_prepare_input($_POST['comments']);

if (isset ($_POST['cot_gv']))
	$_SESSION['cot_gv'] = true;

if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
	if (isset($_POST['conditions'])) {
		$_SESSION['conditions'] = true;
	}

$_SESSION['wm'] = $_POST['wm'];

	if ($_SESSION['conditions'] == false) {
		$error = str_replace('\n', '<br />', ERROR_CONDITIONS_NOT_ACCEPTED);
		os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($error), 'SSL', true, false));
	}
}

require (_CLASS . 'payment.php');
if (isset ($_SESSION['credit_covers']))
	$_SESSION['payment'] = 'no_payment'; 
$payment_modules = new payment($_SESSION['payment']);

require (_CLASS.'order_total.php');
require (_CLASS.'order.php');
$order = new order();

$payment_modules->update_status();
$order_total_modules = new order_total();
$order_total_modules->collect_posts();
$order_total_modules->pre_confirmation_check();
if ((is_array($payment_modules->modules) && (sizeof($payment_modules->modules) > 1) && (!is_object($$_SESSION['payment'])) && (!isset ($_SESSION['credit_covers']))) || (is_object($$_SESSION['payment']) && ($$_SESSION['payment']->enabled == false))) {
	os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
}

if (is_array($payment_modules->modules))
	$payment_modules->pre_confirmation_check();

require (_CLASS.'shipping.php');
$shipping_modules = new shipping($_SESSION['shipping']);

$any_out_of_stock = false;
if (STOCK_CHECK == 'true') {
	for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
		if (os_check_stock($order->products[$i]['id'], $order->products[$i]['qty']))
			$any_out_of_stock = true;
	}
	if ((STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true))
		os_redirect(os_href_link(FILENAME_SHOPPING_CART));
}

$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_CONFIRMATION, os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_CONFIRMATION);

require (_INCLUDES.'header.php');

if (ACCOUNT_STREET_ADDRESS == 'true') {
$osTemplate->assign('SHIPPING_ADDRESS', 'true');
}

if (SHOW_IP_LOG == 'true') {
	$osTemplate->assign('IP_LOG', 'true');
	if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
		$customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$customers_ip = $_SERVER["REMOTE_ADDR"];
	}
	$osTemplate->assign('CUSTOMERS_IP', $customers_ip);
}
$osTemplate->assign('DELIVERY_LABEL', os_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'));
if ($_SESSION['credit_covers'] != '1') {
	$osTemplate->assign('BILLING_LABEL', os_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'));
}
$osTemplate->assign('PRODUCTS_EDIT', os_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
$osTemplate->assign('SHIPPING_ADDRESS_EDIT', os_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL'));
$osTemplate->assign('BILLING_ADDRESS_EDIT', os_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));

if ($_SESSION['sendto'] != false) {

	if ($order->info['shipping_method']) {
		$osTemplate->assign('SHIPPING_METHOD', $order->info['shipping_method']);
		$osTemplate->assign('SHIPPING_EDIT', os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

	}

}

if (sizeof($order->info['tax_groups']) > 1) {

	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {

	}

} else {

}

$productsArray = array();
for ($i = 0, $n = sizeof($order->products); $i < $n; $i++)
{
	$shipping_time_title = '';
	$shipping_time = '';
	if (ACTIVATE_SHIPPING_STATUS == 'true')
	{
		$shipping_time_title = SHIPPING_TIME;
		$shipping_time = $order->products[$i]['shipping_time'];
	}

	if ((isset ($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0))
	{
		$productAtrArray = array();
		for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++)
		{
			$productAtrArray[] = array(
				'option' => $order->products[$i]['attributes'][$j]['option'],
				'value' => $order->products[$i]['attributes'][$j]['value'],
			);
		}
	}

	$product_tax = '';
	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
	{
		if (sizeof($order->info['tax_groups']) > 1)
			$product_tax = os_display_tax_value($order->products[$i]['tax']) . '%';
	}

	$productsArray[] = array(
		'pQty' => $order->products[$i]['qty'],
		'pName' => $order->products[$i]['name'],
		'pPrice' => $osPrice->Format($order->products[$i]['final_price'], true),
		'pShippingTitle' => $shipping_time_title,
		'pShipping' => $shipping_time,
		'pAttributesArray' => $productAtrArray,
		'pTax' => $product_tax,
	);
}

$osTemplate->assign('productsArray', $productsArray);


if ($order->info['payment_method'] != 'no_payment' && $order->info['payment_method'] != '') {
	include (_MODULES.'payment/'.$order->info['payment_method'].'/'.$_SESSION['language'].'.php');
	
	
	$osTemplate->assign('PAYMENT_METHOD', constant(MODULE_PAYMENT_ . strtoupper($order->info['payment_method']) . _TEXT_TITLE));
}
$osTemplate->assign('PAYMENT_EDIT', os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));

$totalArray = '';
if (MODULE_ORDER_TOTAL_INSTALLED)
{
	$order_total_modules->process();
	$totalArray = $order_total_modules->output();
}

$osTemplate->assign('totalArray', $totalArray);

if (is_array($payment_modules->modules)) {
	if ($confirmation = $payment_modules->confirmation()) {

		$payment_info = $confirmation['title'];
		for ($i = 0, $n = sizeof($confirmation['fields']); $i < $n; $i++) {

			$payment_info .= '<table>
								<tr><td class="main">' . $confirmation['fields'][$i]['title'] . '</td>
						            <td class="main">' . stripslashes($confirmation['fields'][$i]['field']) . '</td>
						        </tr></table>';

		}
		$osTemplate->assign('PAYMENT_INFORMATION', $payment_info);

	}
}

if (os_not_null($order->info['comments'])) {
	$osTemplate->assign('ORDER_COMMENTS', nl2br(htmlspecialchars($order->info['comments'])) . os_draw_hidden_field('comments', $order->info['comments']));

}

if (isset ($$_SESSION['payment']->form_action_url) && !$$_SESSION['payment']->tmpOrders) {

	$form_action_url = $$_SESSION['payment']->form_action_url;

} else {
	$form_action_url = os_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
}
$osTemplate->assign('CHECKOUT_FORM', os_draw_form('checkout_confirmation', $form_action_url, 'post'));
$osTemplate->assign('CHECKOUT_FORM_END', '</form>');
$payment_button = '';
if (is_array($payment_modules->modules)) {
	$payment_button .= $payment_modules->process_button();
}
$osTemplate->assign('MODULE_BUTTONS', $payment_button);

    $_array = array('img' => 'button_confirm_order.gif', 'href' => '', 'alt' => TEXT_BUTTON_CONFIRM_ORDER, 'code' => '');
	
	   $_array = apply_filter('button_confirm_order', $_array);	
	
	   if (empty($_array['code']))
 	   {
		   $_array['code'] = buttonSubmit($_array['img'], null, $_array['alt']);
	   }
	   
$osTemplate->assign('CHECKOUT_BUTTON', $_array['code']. '' . "\n");

if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {

	if (GROUP_CHECK == 'true') {
		$group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
	}

	$shop_content_query = "SELECT
		                                                content_title,
		                                                content_heading,
		                                                content_text,
		                                                content_file
		                                                FROM " . TABLE_CONTENT_MANAGER . "
		                                                WHERE content_group='" . REVOCATION_ID . "' " . $group_check . "
		                                                AND languages_id='" . $_SESSION['languages_id'] . "'";

	$shop_content_query = os_db_query($shop_content_query);
	$shop_content_data = os_db_fetch_array($shop_content_query);

	if ($shop_content_data['content_file'] != '') {
		ob_start();
		if (strpos($shop_content_data['content_file'], '.txt'))
			echo '<pre>';
		include (DIR_FS_CATALOG . 'media/content/' . $shop_content_data['content_file']);
		if (strpos($shop_content_data['content_file'], '.txt'))
			echo '</pre>';
		$revocation = ob_get_contents();
		ob_end_clean();
	} else {
		$revocation = $shop_content_data['content_text'];
	}

	$osTemplate->assign('REVOCATION', $revocation);
	$osTemplate->assign('REVOCATION_TITLE', $shop_content_data['content_heading']);
	$osTemplate->assign('REVOCATION_LINK', $main->getContentLink(REVOCATION_ID, MORE_INFO));
	
	$shop_content_query = "SELECT
		                                                content_title,
		                                                content_heading,
		                                                content_text,
		                                                content_file
		                                                FROM " . TABLE_CONTENT_MANAGER . "
		                                                WHERE content_group='3' " . $group_check . "
		                                                AND languages_id='" . $_SESSION['languages_id'] . "'";

	$shop_content_query = os_db_query($shop_content_query);
	$shop_content_data = os_db_fetch_array($shop_content_query);
	
	$osTemplate->assign('AGB_TITLE', $shop_content_data['content_heading']);
	$osTemplate->assign('AGB_LINK', $main->getContentLink(3, MORE_INFO));

}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('PAYMENT_BLOCK', $payment_block);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE . '/module/checkout_confirmation.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_CHECKOUT_CONFIRMATION.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_CHECKOUT_CONFIRMATION.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>