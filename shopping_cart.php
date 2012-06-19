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

$cart_empty = false;
require ("includes/top.php");

$breadcrumb->add(NAVBAR_TITLE_SHOPPING_CART, os_href_link(FILENAME_SHOPPING_CART));

require (_INCLUDES.'header.php');
include (_MODULES.'gift_cart.php');

if ($_SESSION['cart']->count_contents() > 0) {

	$osTemplate->assign('FORM_ACTION', os_draw_form('cart_quantity', os_href_link(FILENAME_SHOPPING_CART, 'action=update_product')));
	$osTemplate->assign('FORM_END', '</form>');
	$hidden_options = '';
	$_SESSION['any_out_of_stock'] = 0;

	$products = $_SESSION['cart']->get_products();
	for ($i = 0, $n = sizeof($products); $i < $n; $i ++) {
		// Push all attributes information in an array
		if (isset ($products[$i]['attributes'])) {
			while (list ($option, $value) = each($products[$i]['attributes'])) {
				//$hidden_options .= os_draw_hidden_field('id['.$products[$i]['id'].']['.$option.']', $value);
				$attributes = os_db_query("select popt.products_options_name, popt.products_options_type, poval.products_options_values_name, pa.options_values_price, pa.price_prefix,pa.attributes_stock,pa.products_attributes_id,pa.attributes_model
				                                      from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_OPTIONS_VALUES." poval, ".TABLE_PRODUCTS_ATTRIBUTES." pa
				                                      where pa.products_id = '".$products[$i]['id']."'
				                                       and pa.options_id = '".$option."'
				                                       and pa.options_id = popt.products_options_id
				                                       and pa.options_values_id = '".$value."'
				                                       and pa.options_values_id = poval.products_options_values_id
				                                       and popt.language_id = '".(int) $_SESSION['languages_id']."'
				                                       and poval.language_id = '".(int) $_SESSION['languages_id']."'");
				$attributes_values = os_db_fetch_array($attributes);

				if($attributes_values['products_options_type']=='2' || $attributes_values['products_options_type']=='3'){
					$hidden_options .= os_draw_hidden_field('id[' . $products[$i]['id'] . '][txt_' . $option . '_'.$value.']',  $products[$i]['attributes_values'][$option]);
				    $attr_value = $products[$i]['attributes_values'][$option];
				}else{
					$hidden_options .= os_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
				    $attr_value = $attributes_values['products_options_values_name'];
				}

				$products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
				$products[$i][$option]['options_values_id'] = $value;
				$products[$i][$option]['products_options_values_name'] = $attr_value;
				$products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
				$products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
				$products[$i][$option]['weight_prefix'] = $attributes_values['weight_prefix'];
				$products[$i][$option]['options_values_weight'] = $attributes_values['options_values_weight'];
				$products[$i][$option]['attributes_stock'] = $attributes_values['attributes_stock'];
				$products[$i][$option]['products_attributes_id'] = $attributes_values['products_attributes_id'];
				$products[$i][$option]['products_attributes_model'] = $attributes_values['products_attributes_model'];
			}
		}
	}

	$osTemplate->assign('HIDDEN_OPTIONS', $hidden_options);
	
	require (DIR_WS_MODULES.'order_details_cart.php');
	
   $_SESSION['allow_checkout'] = 'true';
   
	if (STOCK_CHECK == 'true') {
		if ($_SESSION['any_out_of_stock'] == 1) {
			if (STOCK_ALLOW_CHECKOUT == 'true') {
				// write permission in session
				$_SESSION['allow_checkout'] = 'true';

				$osTemplate->assign('info_message', OUT_OF_STOCK_CAN_CHECKOUT);

			} else {
				$_SESSION['allow_checkout'] = 'false';
				$osTemplate->assign('info_message', OUT_OF_STOCK_CANT_CHECKOUT);

			}
		} else {
			$_SESSION['allow_checkout'] = 'true';
		}
	}
// minimum/maximum order value
$checkout = true;
if ($_SESSION['cart']->show_total() > 0 ) {
 if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order'] ) {
  $_SESSION['allow_checkout'] = 'false';
  $more_to_buy = $_SESSION['customers_status']['customers_status_min_order'] - $_SESSION['cart']->show_total();
  $order_amount=$osPrice->Format($more_to_buy, true);
  $min_order=$osPrice->Format($_SESSION['customers_status']['customers_status_min_order'], true);
  $osTemplate->assign('info_message_1', MINIMUM_ORDER_VALUE_NOT_REACHED_1);
  $osTemplate->assign('info_message_2', MINIMUM_ORDER_VALUE_NOT_REACHED_2);
  $osTemplate->assign('order_amount', $order_amount);
  $osTemplate->assign('min_order', $min_order);
 }
 if  ($_SESSION['customers_status']['customers_status_max_order'] != 0) {
  if ($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order'] ) {
  $_SESSION['allow_checkout'] = 'false';
  $less_to_buy = $_SESSION['cart']->show_total() - $_SESSION['customers_status']['customers_status_max_order'];
  $max_order=$osPrice->Format($_SESSION['customers_status']['customers_status_max_order'], true);
  $order_amount=$osPrice->Format($less_to_buy, true);
  $osTemplate->assign('info_message_1', MAXIMUM_ORDER_VALUE_REACHED_1);
  $osTemplate->assign('info_message_2', MAXIMUM_ORDER_VALUE_REACHED_2);
  $osTemplate->assign('order_amount', $order_amount);
  $osTemplate->assign('min_order', $max_order);
  }
 }
}
	if (@$_GET['info_message'])
		$osTemplate->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));

	/* кнопка */	
	$_array = array('img' => 'button_update_cart.gif', 
	                                'href' => '', 
									'alt' => IMAGE_BUTTON_UPDATE_CART, 'code' => '');
									
	$_array = apply_filter('button_update_cart', $_array);	
	
	if (empty($_array['code']))
	{
	   $_array['code'] = os_image_submit($_array['img'], $_array['alt']);
	}
	
	$osTemplate->assign('BUTTON_RELOAD', $_array['code']);
	
	
	//buttons
	$_array = array('img' => 'button_checkout.gif', 
	                                'href' => os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_CHECKOUT, 'code' => '');
									
	$_array = apply_filter('button_checkout', $_array);	
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}	
									
	$osTemplate->assign('BUTTON_CHECKOUT', $_array['code']);	
    ///////- -- buttons
	
} else {

	// empty cart
	$cart_empty = true;
	if ($_GET['info_message'])
		$osTemplate->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));
	$osTemplate->assign('cart_empty', $cart_empty);
	$osTemplate->assign('BUTTON_CONTINUE', button_continue());

}
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/shopping_cart.html');
$osTemplate->assign('main_content', $main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_SHOPPING_CART.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_SHOPPING_CART.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>