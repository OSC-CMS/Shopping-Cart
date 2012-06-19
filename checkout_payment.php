<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

include ('includes/top.php');
//$osTemplate = new osTemplate;


unset ($_SESSION['tmp_oID']);
// if the customer is not logged on, redirect them to the login page
if (!isset ($_SESSION['customer_id'])) {
	if (ACCOUNT_OPTIONS == 'guest') {
		os_redirect(os_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'));
	} else {
		os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));
	}
}

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1)
	os_redirect(os_href_link(FILENAME_SHOPPING_CART));

// if no shipping method has been selected, redirect the customer to the shipping method selection page
if (!isset ($_SESSION['shipping']))
	os_redirect(os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset ($_SESSION['cart']->cartID) && isset ($_SESSION['cartID'])) {
	if ($_SESSION['cart']->cartID != $_SESSION['cartID'])
		os_redirect(os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}

if (isset ($_SESSION['credit_covers']))
	unset ($_SESSION['credit_covers']); //ICW ADDED FOR CREDIT CLASS SYSTEM
// Stock Check
if ((STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true')) {
	$products = $_SESSION['cart']->get_products();
	$any_out_of_stock = 0;
	for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
		if (os_check_stock($products[$i]['id'], $products[$i]['quantity']))
			$any_out_of_stock = 1;
	}
	if ($any_out_of_stock == 1)
		os_redirect(os_href_link(FILENAME_SHOPPING_CART));

}

// if no billing destination address was selected, use the customers own address as default
if (!isset ($_SESSION['billto'])) {
	$_SESSION['billto'] = $_SESSION['customer_default_address_id'];
} else {
	// verify the selected billing address
	$check_address_query = os_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int) $_SESSION['customer_id'] . "' and address_book_id = '" . (int) $_SESSION['billto'] . "'");
	$check_address = os_db_fetch_array($check_address_query);

	if ($check_address['total'] != '1') {
		$_SESSION['billto'] = $_SESSION['customer_default_address_id'];
		if (isset ($_SESSION['payment']))
			unset ($_SESSION['payment']);
	}
}

if (!isset ($_SESSION['sendto']) || $_SESSION['sendto'] == "")
	$_SESSION['sendto'] = $_SESSION['billto'];

require (_CLASS.'order.php');
$order = new order();
require (_CLASS.'order_total.php'); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM
$order_total_modules = new order_total(); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM

$total_weight = $_SESSION['cart']->show_weight();

//  $total_count = $_SESSION['cart']->count_contents();
$total_count = $_SESSION['cart']->count_contents_virtual(); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM

if ($order->billing['country']['iso_code_2'] != '')
	$_SESSION['delivery_zone'] = $order->billing['country']['iso_code_2'];

// load all enabled payment modules
require (_CLASS.'payment.php');
$payment_modules = new payment;

$order_total_modules->process();
// redirect if Coupon matches ammount

$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_PAYMENT, os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_PAYMENT, os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));

if (ACCOUNT_STREET_ADDRESS == 'true') {
$osTemplate->assign('BILLING_ADDRESS', 'true');
}
$osTemplate->assign('FORM_ACTION', os_draw_form('checkout_payment', os_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post'));
$osTemplate->assign('ADDRESS_LABEL', os_address_label($_SESSION['customer_id'], $_SESSION['billto'], true, ' ', '<br />'));

$_array = array('img' => 'button_change_address.gif', 
	                                'href' => os_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_CHANGE_ADDRESS,
                  /* код готовой кнопки, по умолчанию пусто */									
									'code' => ''
	);
	
	$_array = apply_filter('button_change_address', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}
	

$osTemplate->assign('BUTTON_ADDRESS', $_array['code']);


$osTemplate->assign('BUTTON_CONTINUE', button_continue_submit());
$osTemplate->assign('FORM_END', '</form>');

require (_INCLUDES.'header.php');
$module = new osTemplate;
//if ($order->info['total'] > 0 || $_SESSION['cart']->get_content_type() == 'virtual') {
	if (isset ($_GET['payment_error']) && is_object(${ $_GET['payment_error'] }) && ($error = ${$_GET['payment_error']}->get_error())) {

		$osTemplate->assign('error', htmlspecialchars($error['error']));

	}

	$selection = $payment_modules->selection();

	$radio_buttons = 0;
	for ($i = 0, $n = sizeof($selection); $i < $n; $i++) {

		$selection[$i]['radio_buttons'] = $radio_buttons;

		if (($selection[$i]['id'] == $payment) || ($n == 1)) {
			$selection[$i]['checked'] = 1;
		}

		if (sizeof($selection) > 1) {
			$selection[$i]['selection'] = os_draw_radio_field('payment', $selection[$i]['id'], ($selection[$i]['id'] == $_SESSION['payment']));
		} else {
			$selection[$i]['selection'] = os_draw_hidden_field('payment', $selection[$i]['id']);
		}
			$selection[$i]['id'] = $selection[$i]['id'];

		if (isset ($selection[$i]['error'])) {

		} else {

			$radio_buttons++;
		}
	}

	$module->assign('module_content', $selection);

//} else {
//	$osTemplate->assign('GV_COVER', 'true');
//}

if (ACTIVATE_GIFT_SYSTEM == 'true') {
	$osTemplate->assign('module_gift', $order_total_modules->credit_selection());
}

$module->caching = 0;
$payment_block = $module->fetch(CURRENT_TEMPLATE.'/module/checkout_payment_block.html');

$osTemplate->assign('COMMENTS', os_draw_textarea_field('comments', 'soft', '60', '5', $_SESSION['comments']) . os_draw_hidden_field('comments_added', 'YES'));

$osTemplate->assign('conditions', 'false');

//check if display conditions on checkout page is true
if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {

$osTemplate->assign('conditions', 'true');

	if (GROUP_CHECK == 'true') {
		$group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
	}

	$shop_content_query = os_db_query("SELECT
	                                                content_title,
	                                                content_heading,
	                                                content_text,
	                                                content_file
	                                                FROM " . TABLE_CONTENT_MANAGER . "
	                                                WHERE content_group='3' " . $group_check . "
	                                                AND languages_id='" . $_SESSION['languages_id'] . "'");
	$shop_content_data = os_db_fetch_array($shop_content_query);

	if ($shop_content_data['content_file'] != '') {

		$conditions = '<iframe SRC="' . DIR_WS_CATALOG . 'media/content/' . $shop_content_data['content_file'] . '" width="100%" height="300">';
		$conditions .= '</iframe>';
	} else {

		$conditions = '<textarea name="blabla" cols="60" rows="10" readonly="readonly">' . strip_tags(str_replace('<br />', "\n", $shop_content_data['content_text'])) . '</textarea>';
	}

	$osTemplate->assign('AGB', $conditions);
	$osTemplate->assign('AGB_LINK', $main->getContentLink(3, MORE_INFO));
	// LUUPAY ZAHLUNGSMODUL
	if (isset ($_GET['step']) && $_GET['step'] == 'step2') {
		$osTemplate->assign('AGB_checkbox', '<input type="checkbox" value="conditions" name="conditions" checked />');
	} else {
		$osTemplate->assign('AGB_checkbox', '<input type="checkbox" value="conditions" name="conditions" />');
	}
	// LUUPAY END

}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('PAYMENT_BLOCK', $payment_block);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE . '/module/checkout_payment.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_CHECKOUT_PAYMENT.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_CHECKOUT_PAYMENT.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>