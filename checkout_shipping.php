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


require (_CLASS.'http_client.php');

// check if checkout is allowed
if ($_SESSION['cart']->show_total() > 0 ) {
 if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order'] ) {
  $_SESSION['allow_checkout'] = 'false';
 }
 if  ($_SESSION['customers_status']['customers_status_max_order'] != 0) {
  if ($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order'] ) {
  $_SESSION['allow_checkout'] = 'false';
  }
 }
}

if ($_SESSION['allow_checkout'] == 'false')
	os_redirect(os_href_link(FILENAME_SHOPPING_CART));

// if the customer is not logged on, redirect them to the login page
if (!isset ($_SESSION['customer_id'])) {
	if (ACCOUNT_OPTIONS == 'guest') {
		os_redirect(os_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'));
	} else {
	if (QUICK_CHECKOUT == 'false') {
		os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));
   } else {
		os_redirect(os_href_link(FILENAME_CHECKOUT_ALTERNATIVE, '', 'SSL'));
   }
	}
}
 
// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1) {
	os_redirect(os_href_link(FILENAME_SHOPPING_CART));
}

// if no shipping destination address was selected, use the customers own address as default
if (!isset ($_SESSION['sendto'])) {
	$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
} else {
	// verify the selected shipping address
	$check_address_query = os_db_query("select count(*) as total from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $_SESSION['customer_id']."' and address_book_id = '".(int) $_SESSION['sendto']."'");
	$check_address = os_db_fetch_array($check_address_query);

	if ($check_address['total'] != '1') {
		$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
		if (isset ($_SESSION['shipping']))
			unset ($_SESSION['shipping']);
	}
}

require (dir_path('class').'order.php');
$order = new order();

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
$_SESSION['cartID'] = $_SESSION['cart']->cartID;

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
if ($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_virtual() == 0)) { // GV Code added
	$_SESSION['shipping'] = false;
	$_SESSION['sendto'] = false;
	os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

$total_weight = $_SESSION['cart']->show_weight();
$total_count = $_SESSION['cart']->count_contents();

if ($order->delivery['country']['iso_code_2'] != '') {
	$_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
}
// load all enabled shipping modules
require (dir_path('class').'shipping.php');
$shipping_modules = new shipping;

if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
	switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
		case 'national' :
			if ($order->delivery['country_id'] == STORE_COUNTRY)
				$pass = true;
			break;
		case 'international' :
			if ($order->delivery['country_id'] != STORE_COUNTRY)
				$pass = true;
			break;
		case 'both' :
			$pass = true;
			break;
		default :
			$pass = false;
			break;
	}

	$free_shipping = false;
	if (($pass == true) && ($order->info['total'] - $order->info['shipping_cost'] >= $osPrice->Format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, false, 0, true))) {
		$free_shipping = true;

		include (DIR_FS_DOCUMENT_ROOT.'/modules/order_total/ot_shipping/'.$_SESSION['language'].'.php');
	}
} else {
	$free_shipping = false;
}

// process the selected shipping method
if (isset ($_POST['action']) && ($_POST['action'] == 'process')) {

	if ((os_count_shipping_modules() > 0) || ($free_shipping == true)) {
		if ((isset ($_POST['shipping'])) && (strpos($_POST['shipping'], '_'))) {
			$_SESSION['shipping'] = $_POST['shipping'];

			list ($module, $method) = explode('_', $_SESSION['shipping']);
			if (is_object($$module) || ($_SESSION['shipping'] == 'free_free')) {
				if ($_SESSION['shipping'] == 'free_free') {
					$quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
					$quote[0]['methods'][0]['cost'] = '0';
				} else {
					$quote = $shipping_modules->quote($method, $module);
				}
				if (isset ($quote['error'])) {
					unset ($_SESSION['shipping']);
				} else {
					if ((isset ($quote[0]['methods'][0]['title'])) && (isset ($quote[0]['methods'][0]['cost']))) {
						$_SESSION['shipping'] = array ('id' => $_SESSION['shipping'], 'title' => (($free_shipping == true) ? $quote[0]['methods'][0]['title'] : $quote[0]['module'].' ('.$quote[0]['methods'][0]['title'].')'), 'cost' => $quote[0]['methods'][0]['cost']);

						os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
					}
				}
			} else {
				unset ($_SESSION['shipping']);
			}
		}
	} else {
		$_SESSION['shipping'] = false;

		os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
	}
}

// get all available shipping quotes
$quotes = $shipping_modules->quote();

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
if (!isset ($_SESSION['shipping']) || (isset ($_SESSION['shipping']) && ($_SESSION['shipping'] == false) && (os_count_shipping_modules() > 1)))
	$_SESSION['shipping'] = $shipping_modules->cheapest();

$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_SHIPPING, os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_SHIPPING, os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

require (dir_path('includes').'header.php');

if (ACCOUNT_STREET_ADDRESS == 'true') {
	$osTemplate->assign('SHIPPING_ADDRESS', 'true');
}

$osTemplate->assign('FORM_ACTION', os_draw_form('checkout_address', os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')).os_draw_hidden_field('action', 'process'));
$osTemplate->assign('ADDRESS_LABEL', os_address_label($_SESSION['customer_id'], $_SESSION['sendto'], true, ' ', '<br />'));

$_array = array('img' => 'button_change_address.gif', 
	                                'href' => os_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_CHANGE_ADDRESS,
                  /* код готовой кнопки, по умолчанию пусто */									
									'code' => ''
	);
	
	$_array = apply_filter('button_change_address', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	}
	
$osTemplate->assign('BUTTON_ADDRESS', $_array['code']);


$osTemplate->assign('BUTON_CONTINUE', button_continue_submit());
$osTemplate->assign('FORM_END', '</form>');

$module = new osTemplate;
if (os_count_shipping_modules() > 0) {

	$showtax = $_SESSION['customers_status']['customers_status_show_price_tax'];

	$module->assign('FREE_SHIPPING', $free_shipping);

	# free shipping or not...

	if ($free_shipping == true) {

		$module->assign('FREE_SHIPPING_TITLE', FREE_SHIPPING_TITLE);

		$module->assign('FREE_SHIPPING_DESCRIPTION', sprintf(FREE_SHIPPING_DESCRIPTION, $osPrice->Format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, true, 0, true)).os_draw_hidden_field('shipping', 'free_free'));

		$module->assign('FREE_SHIPPING_ICON', $quotes[$i]['icon']);

	} else {

		$radio_buttons = 0;

		#loop through installed shipping methods...

		for ($i = 0, $n = sizeof($quotes); $i < $n; $i ++) {

			if (!isset ($quotes[$i]['error'])) {

				for ($j = 0, $n2 = sizeof($quotes[$i]['methods']); $j < $n2; $j ++) {

					# set the radio button to be checked if it is the method chosen

					$quotes[$i]['methods'][$j]['radio_buttons'] = $radio_buttons;

					$checked = (($quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']['id']) ? true : false);

					if (($checked == true) || ($n == 1 && $n2 == 1)) {

						$quotes[$i]['methods'][$j]['checked'] = 1;

					}

					if (($n > 1) || ($n2 > 1)) {
						if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = '';
						if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = 0;

						$quotes[$i]['methods'][$j]['price'] = $osPrice->Format(os_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true);

						$quotes[$i]['methods'][$j]['radio_field'] = os_draw_radio_field('shipping', $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'], $checked);
						$quotes[$i]['methods'][$j]['id'] = $quotes[$i]['methods'][$j]['id'];

					} else {
						if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = 0;

						$quotes[$i]['methods'][$j]['price'] = $osPrice->Format(os_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true).os_draw_hidden_field('shipping', $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id']);

					}

					$radio_buttons ++;

				}

			}

		}

		$module->assign('module_content', $quotes);

	}
	$module->caching = 0;
	$shipping_block = $module->fetch(CURRENT_TEMPLATE.'/module/checkout_shipping_block.html');

}


$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('SHIPPING_BLOCK', $shipping_block);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/checkout_shipping.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;

$template = (file_exists(_THEMES_C.FILENAME_CHECKOUT_SHIPPING.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_CHECKOUT_SHIPPING.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>