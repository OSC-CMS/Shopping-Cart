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

require ('includes/top.php');

require (get_path('class_admin').'order.php');

if (!$_GET['oID'])
	$_GET['oID'] = $_POST['oID'];
$order = new order($_GET['oID']);

require (_CLASS.'price.php');
$osPrice = new osPrice($order->info['currency'], isset($order->info['status'])?$order->info['status']:'');

if (isset($_GET['action']) && $_GET['action'] == "address_edit") {

	$lang_query = os_db_query("select languages_id from ".TABLE_LANGUAGES." where directory = '".$order->info['language']."'");
	$lang = os_db_fetch_array($lang_query);

	$status_query = os_db_query("select customers_status_name from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = '".$_POST['customers_status']."' and language_id = '".$lang['languages_id']."' ");
	$status = os_db_fetch_array($status_query);

	$sql_data_array = array ('customers_vat_id' => os_db_prepare_input($_POST['customers_vat_id']), 'customers_status' => os_db_prepare_input($_POST['customers_status']), 'customers_status_name' => os_db_prepare_input($status['customers_status_name']), 'customers_company' => os_db_prepare_input($_POST['customers_company']), 'customers_name' => os_db_prepare_input($_POST['customers_name']), 'customers_street_address' => os_db_prepare_input($_POST['customers_street_address']), 'customers_city' => os_db_prepare_input($_POST['customers_city']), 'customers_state' => os_db_prepare_input($_POST['customers_state']), 'customers_postcode' => os_db_prepare_input($_POST['customers_postcode']), 'customers_country' => os_db_prepare_input($_POST['customers_country']), 'customers_telephone' => os_db_prepare_input($_POST['customers_telephone']), 'customers_email_address' => os_db_prepare_input($_POST['customers_email_address']), 'delivery_company' => os_db_prepare_input($_POST['delivery_company']), 'delivery_name' => os_db_prepare_input($_POST['delivery_name']), 'delivery_street_address' => os_db_prepare_input($_POST['delivery_street_address']), 'delivery_city' => os_db_prepare_input($_POST['delivery_city']), 'delivery_state' => os_db_prepare_input($_POST['delivery_state']), 'delivery_postcode' => os_db_prepare_input($_POST['delivery_postcode']), 'delivery_country' => os_db_prepare_input($_POST['delivery_country']), 'billing_company' => os_db_prepare_input($_POST['billing_company']), 'billing_name' => os_db_prepare_input($_POST['billing_name']), 'billing_street_address' => os_db_prepare_input($_POST['billing_street_address']), 'billing_city' => os_db_prepare_input($_POST['billing_city']), 'billing_state' => os_db_prepare_input($_POST['billing_state']), 'billing_postcode' => os_db_prepare_input($_POST['billing_postcode']), 'billing_country' => os_db_prepare_input($_POST['billing_country']));

	$update_sql_data = array ('last_modified' => 'now()');
	$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
	os_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = \''.os_db_input($_POST['oID']).'\'');

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=address&oID='.$_POST['oID']));
}
if (isset($_GET['action']) && $_GET['action'] == "product_edit") {
	$status_query = os_db_query("select customers_status_show_price_tax from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = '".$order->info['status']."'");
	$status = os_db_fetch_array($status_query);

	$final_price = $_POST['products_price'] * $_POST['products_quantity'];

	$sql_data_array = array ('orders_id' => os_db_prepare_input($_POST['oID']), 'products_id' => os_db_prepare_input($_POST['products_id']), 'products_name' => os_db_prepare_input($_POST['products_name']), 'products_price' => os_db_prepare_input($_POST['products_price']), 'products_discount_made' => '', 'final_price' => os_db_prepare_input($final_price), 'products_tax' => os_db_prepare_input($_POST['products_tax']), 'products_quantity' => os_db_prepare_input($_POST['products_quantity']), 'allow_tax' => os_db_prepare_input($status['customers_status_show_price_tax']));

	$update_sql_data = array ('products_model' => os_db_prepare_input($_POST['products_model']));
	$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
	os_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id = \''.os_db_input($_POST['opID']).'\'');

    os_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
	products_quantity = products_quantity - " . $_POST['products_quantity'] . ",
	products_ordered = products_ordered + " . $_POST['products_quantity'] . " 
	WHERE products_id = '" . $_POST['products_id'] . "'");

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID='.$_POST['oID']));
}

if (isset($_GET['action']) && $_GET['action'] == "product_ins") {

if (0==round($_POST['products_quantity']))
	{
		$_POST['products_quantity'] = 1;
	}

	$status_query = os_db_query("select customers_status_show_price_tax from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = '".$order->info['status']."'");
	$status = os_db_fetch_array($status_query);

	$product_query = os_db_query("select p.products_model, p.products_tax_class_id, pd.products_name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = '".$_POST['products_id']."' and pd.products_id = p.products_id and pd.language_id = '".$_SESSION['languages_id']."'");
	$product = os_db_fetch_array($product_query);

	$c_info = os_customer_infos($order->customer['ID']);
	$tax_rate = os_get_tax_rate($product['products_tax_class_id'], $c_info['country_id'], $c_info['zone_id']);

	$price = $osPrice->getPrice($_POST['products_id'], $format = false, $_POST['products_quantity'], $product['products_tax_class_id'], '', '', $order->customer['ID']);

	$final_price = $price * $_POST['products_quantity'];

	$sql_data_array = array ('orders_id' => os_db_prepare_input($_POST['oID']), 'products_id' => os_db_prepare_input($_POST['products_id']), 'products_name' => os_db_prepare_input($product['products_name']), 'products_price' => os_db_prepare_input($price), 'products_discount_made' => '', 'final_price' => os_db_prepare_input($final_price), 'products_tax' => os_db_prepare_input($tax_rate), 'products_quantity' => os_db_prepare_input($_POST['products_quantity']), 'allow_tax' => os_db_prepare_input($status['customers_status_show_price_tax']));

	$insert_sql_data = array ('products_model' => os_db_prepare_input($product['products_model']));
	$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
	os_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);

    os_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
	products_quantity = products_quantity - " . $_POST['products_quantity'] . ",
	products_ordered = products_ordered + " . $_POST['products_quantity'] . " 
	WHERE products_id = '" . $_POST['products_id'] . "'");

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID='.$_POST['oID']));
}

if (isset($_GET['action']) && $_GET['action'] == "product_option_edit") {

	$sql_data_array = array ('products_options' => os_db_prepare_input($_POST['products_options']), 'products_options_values' => os_db_prepare_input($_POST['products_options_values']), 'options_values_price' => os_db_prepare_input($_POST['options_values_price']));

	$update_sql_data = array ('price_prefix' => os_db_prepare_input($_POST['prefix']));
	$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
	os_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array, 'update', 'orders_products_attributes_id = \''.os_db_input($_POST['opAID']).'\'');

	$products_query = os_db_query("select op.products_id, op.products_quantity, p.products_tax_class_id from ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS." p where op.orders_products_id = '".$_POST['opID']."' and op.products_id = p.products_id");
	$products = os_db_fetch_array($products_query);

	$products_a_query = os_db_query("select options_values_price, price_prefix from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = '".$_POST['opID']."'");
	while ($products_a = os_db_fetch_array($products_a_query)) {
		$ov_price += $products_a['price_prefix'].$products_a['options_values_price'];
	};

	$products_old_price = $osPrice->GetPrice($products['products_id'], $format = false, $products['products_quantity'], '', '', '', $order->customer['ID']);

	$options_values_price = ($ov_price.$_POST['prefix'].$_POST['options_values_price']);
	$products_price = ($products_old_price + $options_values_price);

	$price = $osPrice->GetPrice($products['products_id'], $format = false, $products['products_quantity'], $products['products_tax_class_id'], $products_price, '', $order->customer['ID']);

	$final_price = $price * $products['products_quantity'];

	$sql_data_array = array ('products_price' => os_db_prepare_input($price));
	$update_sql_data = array ('final_price' => os_db_prepare_input($final_price));
	$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
	os_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id = \''.os_db_input($_POST['opID']).'\'');

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=options&oID='.$_POST['oID'].'&pID='.$products['products_id'].'&opID='.$_POST['opID']));
}

if (isset($_GET['action']) && $_GET['action'] == "product_option_ins") {

	$products_attributes_query = os_db_query("select options_id, options_values_id, options_values_price, price_prefix from ".TABLE_PRODUCTS_ATTRIBUTES." where products_attributes_id = '".$_POST['aID']."'");
	$products_attributes = os_db_fetch_array($products_attributes_query);

	$products_options_query = os_db_query("select products_options_name from ".TABLE_PRODUCTS_OPTIONS." where products_options_id = '".$products_attributes['options_id']."' and language_id = '".$_SESSION['languages_id']."'");
	$products_options = os_db_fetch_array($products_options_query);

	$products_options_values_query = os_db_query("select products_options_values_name from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".$products_attributes['options_values_id']."' and language_id = '".$_SESSION['languages_id']."'");
	$products_options_values = os_db_fetch_array($products_options_values_query);

	$sql_data_array = array ('orders_id' => os_db_prepare_input($_POST['oID']), 'orders_products_id' => os_db_prepare_input($_POST['opID']), 'products_options' => os_db_prepare_input($products_options['products_options_name']), 'products_options_values' => os_db_prepare_input($products_options_values['products_options_values_name']), 'options_values_price' => os_db_prepare_input($products_attributes['options_values_price']));

	$insert_sql_data = array ('price_prefix' => os_db_prepare_input($products_attributes['price_prefix']));
	$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
	os_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

	$products_query = os_db_query("select op.products_id, op.products_quantity, p.products_tax_class_id from ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS." p where op.orders_products_id = '".$_POST['opID']."' and op.products_id = p.products_id");
	$products = os_db_fetch_array($products_query);

	$products_a_query = os_db_query("select options_values_price, price_prefix from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = '".$_POST['opID']."'");
	while ($products_a = os_db_fetch_array($products_a_query)) {
		$options_values_price += $products_a['price_prefix'].$products_a['options_values_price'];
	};

	if (DOWNLOAD_ENABLED == 'true') {
		$attributes_query = "select popt.products_options_name,
										                               poval.products_options_values_name,
										                               pa.options_values_price,
										                               pa.price_prefix,
										                               pad.products_attributes_maxdays,
										                               pad.products_attributes_maxcount,
										                               pad.products_attributes_filename
										                               from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_OPTIONS_VALUES." poval, ".TABLE_PRODUCTS_ATTRIBUTES." pa
										                               left join ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad
										                                on pa.products_attributes_id=pad.products_attributes_id
										                               where pa.products_id = '".$products['products_id']."'
										                                and pa.options_id = '".$products_attributes['options_id']."'
										                                and pa.options_id = popt.products_options_id
										                                and pa.options_values_id = '".$products_attributes['options_values_id']."'
										                                and pa.options_values_id = poval.products_options_values_id
										                                and popt.language_id = '".$_SESSION['languages_id']."'
										                                and poval.language_id = '".$_SESSION['languages_id']."'";
		$attributes = os_db_query($attributes_query);

		$attributes_values = os_db_fetch_array($attributes);

		if (isset ($attributes_values['products_attributes_filename']) && os_not_null($attributes_values['products_attributes_filename'])) {
			$sql_data_array = array ('orders_id' => os_db_prepare_input($_POST['oID']), 'orders_products_id' => os_db_prepare_input($_POST['opID']), 'orders_products_filename' => $attributes_values['products_attributes_filename'], 'download_maxdays' => $attributes_values['products_attributes_maxdays'], 'download_count' => $attributes_values['products_attributes_maxcount']);

			os_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
		}

	}

	$products_old_price = $osPrice->GetPrice($products['products_id'], $format = false, $products['products_quantity'], '', '', '', $order->customer['ID']);

	$products_price = ($products_old_price + $options_values_price);

	$price = $osPrice->GetPrice($products['products_id'], $format = false, $products['products_quantity'], $products['products_tax_class_id'], $products_price, '', $order->customer['ID']);

	$final_price = $price * $products['products_quantity'];

	$sql_data_array = array ('products_price' => os_db_prepare_input($price));
	$update_sql_data = array ('final_price' => os_db_prepare_input($final_price));
	$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
	os_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id = \''.os_db_input($_POST['opID']).'\'');

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=options&oID='.$_POST['oID'].'&pID='.$products['products_id'].'&opID='.$_POST['opID']));
}

if (isset($_GET['action']) && $_GET['action'] == "payment_edit") {

	$sql_data_array = array ('payment_method' => os_db_prepare_input($_POST['payment']), 'payment_class' => os_db_prepare_input($_POST['payment']),);
	os_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = \''.os_db_input($_POST['oID']).'\'');

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID='.$_POST['oID']));
}

if (isset($_GET['action']) && $_GET['action'] == "shipping_edit") {
if (!empty($_POST['shipping']))
{
	$module = $_POST['shipping'].'.php';
	require (_MODULES.'shipping/'.$module.'/'.$order->info['language'].'.php');
	$shipping_text = constant(MODULE_SHIPPING_.strtoupper($_POST['shipping'])._TEXT_TITLE);
	$shipping_class = $_POST['shipping'].'_'.$_POST['shipping'];

	$text = $osPrice->Format($_POST['value'], true);

	$sql_data_array = array ('orders_id' => os_db_prepare_input($_POST['oID']), 'title' => os_db_prepare_input($shipping_text), 'text' => os_db_prepare_input($text), 'value' => os_db_prepare_input($_POST['value']), 'class' => 'ot_shipping');

	$check_shipping_query = os_db_query("select class from ".TABLE_ORDERS_TOTAL." where orders_id = '".$_POST['oID']."' and class = 'ot_shipping'");
	if (os_db_num_rows($check_shipping_query)) {
		os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', 'orders_id = \''.os_db_input($_POST['oID']).'\' and class="ot_shipping"');
	} else {
		os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
	}

	$sql_data_array = array ('shipping_method' => os_db_prepare_input($shipping_text), 'shipping_class' => os_db_prepare_input($shipping_class),);
	os_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = \''.os_db_input($_POST['oID']).'\'');

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID='.$_POST['oID']));
}
	}

if (isset($_GET['action']) && $_GET['action'] == "ot_edit") {

	$check_total_query = os_db_query("select orders_total_id from ".TABLE_ORDERS_TOTAL." where orders_id = '".$_POST['oID']."' and class = '".$_POST['class']."'");
	if (os_db_num_rows($check_total_query)) {

		$check_total = os_db_fetch_array($check_total_query);

		$text = $osPrice->Format($_POST['value'], true);

		$sql_data_array = array ('title' => os_db_prepare_input($_POST['title']), 'text' => os_db_prepare_input($text), 'value' => os_db_prepare_input($_POST['value']),);
		os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', 'orders_total_id = \''.os_db_input($check_total['orders_total_id']).'\'');

	} else {

		$text = $osPrice->Format($_POST['value'], true);

		$sql_data_array = array ('orders_id' => os_db_prepare_input($_POST['oID']), 'title' => os_db_prepare_input($_POST['title']), 'text' => os_db_prepare_input($text), 'value' => os_db_prepare_input($_POST['value']), 'class' => os_db_prepare_input($_POST['class']), 'sort_order' => os_db_prepare_input($_POST['sort_order']),);

		os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
	}

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID='.$_POST['oID']));
}

if (isset($_GET['action']) && $_GET['action'] == "lang_edit") {

	$lang_query = os_db_query("select languages_id, name, directory from ".TABLE_LANGUAGES." where languages_id = '".$_POST['lang']."'");
	$lang = os_db_fetch_array($lang_query);
	$order_products_query = os_db_query("select orders_products_id , products_id from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".$_POST['oID']."'");
	while ($order_products = os_db_fetch_array($order_products_query)) {

		$products_query = os_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$order_products['products_id']."' and language_id = '".$_POST['lang']."' ");
		$products = os_db_fetch_array($products_query);

		$sql_data_array = array ('products_name' => os_db_prepare_input($products['products_name']));
		os_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id  = \''.os_db_input($order_products['orders_products_id']).'\'');
	};
	$order_total_query = os_db_query("select orders_total_id, title, class from ".TABLE_ORDERS_TOTAL." where orders_id = '".$_POST['oID']."'");
	while ($order_total = os_db_fetch_array($order_total_query)) {

		require (_MODULES.'order_total/'.$order_total['class'].'/'.$lang['directory'].'.php');
	
		$name = str_replace('ot_', '', $order_total['class']);
		$text = constant(MODULE_ORDER_TOTAL_.strtoupper($name)._TITLE);

		$sql_data_array = array ('title' => os_db_prepare_input($text));
		os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', 'orders_total_id  = \''.os_db_input($order_total['orders_total_id']).'\'');

	}

	$sql_data_array = array ('language' => os_db_prepare_input($lang['directory']));
	os_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id  = \''.os_db_input($_POST['oID']).'\'');

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID='.$_POST['oID']));
}

if (isset($_GET['action']) && $_GET['action'] == "curr_edit") {

	$curr_query = os_db_query("select currencies_id, title, code, value from ".TABLE_CURRENCIES." where currencies_id = '".$_POST['currencies_id']."' ");
	$curr = os_db_fetch_array($curr_query);

	$old_curr_query = os_db_query("select currencies_id, title, code, value from ".TABLE_CURRENCIES." where code = '".$_POST['old_currency']."' ");
	$old_curr = os_db_fetch_array($old_curr_query);

	$sql_data_array = array ('currency' => os_db_prepare_input($curr['code']),'currency_value'=>os_db_prepare_input($curr['value']));
	os_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id  = \''.os_db_input($_POST['oID']).'\'');

	$order_products_query = os_db_query("select orders_products_id , products_id, products_price, final_price from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".$_POST['oID']."'");
	while ($order_products = os_db_fetch_array($order_products_query)) {

		if ($old_curr['code'] == DEFAULT_CURRENCY) {

			$osPrice = new osPrice($curr['code'], $order->info['status']);

			$products_price = $osPrice->GetPrice($order_products['products_id'], $format = false, '', '', $order_products['products_price'], '', $order->customer['ID']);

			$final_price = $osPrice->GetPrice($order_products['products_id'], $format = false, '', '', $order_products['final_price'], '', $order->customer['ID']);
		} else {

			$osPrice = new osPrice($old_curr['code'], $order->info['status']);

			$p_price = $osPrice->RemoveCurr($order_products['products_price']);

			$f_price = $osPrice->RemoveCurr($order_products['final_price']);

			$osPrice = new osPrice($curr['code'], $order->info['status']);

			$products_price = $osPrice->GetPrice($order_products['products_id'], $format = false, '', '', $p_price, '', $order->customer['ID']);

			$final_price = $osPrice->GetPrice($order_products['products_id'], $format = false, '', '', $f_price, '', $order->customer['ID']);
		}
		$sql_data_array = array ('products_price' => os_db_prepare_input($products_price), 'final_price' => os_db_prepare_input($final_price));

		os_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id  = \''.os_db_input($order_products['orders_products_id']).'\'');
	};
	$order_total_query = os_db_query("select orders_total_id, value from ".TABLE_ORDERS_TOTAL." where orders_id = '".$_POST['oID']."'");
	while ($order_total = os_db_fetch_array($order_total_query)) {

		if ($old_curr['code'] == DEFAULT_CURRENCY) {

			$osPrice = new osPrice($curr['code'], $order->info['status']);

			$value = $osPrice->GetPrice('', $format = false, '', '', $order_total['value'], '', $order->customer['ID']);

		} else {

			$osPrice = new osPrice($old_curr['code'], $order->info['status']);

			$nvalue = $osPrice->RemoveCurr($order_total['value']);

			$osPrice = new osPrice($curr['code'], $order->info['status']);

			$value = $osPrice->GetPrice('', $format = false, '', '', $nvalue, '', $order->customer['ID']);
		}

		$text = $text = $osPrice->Format($value, true);

		$sql_data_array = array ('text' => os_db_prepare_input($text), 'value' => os_db_prepare_input($value));

		os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', 'orders_total_id  = \''.os_db_input($order_total['orders_total_id']).'\'');
	};
	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID='.$_POST['oID']));
}

if (isset($_GET['action']) && $_GET['action'] == "product_delete") {

	os_db_query("delete from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = '".os_db_input($_POST['opID'])."'");
	os_db_query("delete from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".os_db_input($_POST['oID'])."' and orders_products_id = '".os_db_input($_POST['opID'])."'");

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID='.$_POST['oID']));
}
if (isset($_GET['action']) && $_GET['action'] == "product_option_delete") {

	os_db_query("delete from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_attributes_id = '".os_db_input($_POST['opAID'])."'");

	$products_query = os_db_query("select op.products_id, op.products_quantity, p.products_tax_class_id from ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS." p where op.orders_products_id = '".$_POST['opID']."' and op.products_id = p.products_id");
	$products = os_db_fetch_array($products_query);

	$products_a_query = os_db_query("select options_values_price, price_prefix from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = '".$_POST['opID']."'");
	while ($products_a = os_db_fetch_array($products_a_query)) {
		$options_values_price += $products_a['price_prefix'].$products_a['options_values_price'];
	};

	$products_old_price = $osPrice->GetPrice($products['products_id'], $format = false, $products['products_quantity'], '', '', '', $order->customer['ID']);

	$products_price = ($products_old_price + $options_values_price);

	$price = $osPrice->GetPrice($products['products_id'], $format = false, $products['products_quantity'], $products['products_tax_class_id'], $products_price, '', $order->customer['ID']);

	$final_price = $price * $products['products_quantity'];

	$sql_data_array = array ('products_price' => os_db_prepare_input($price));
	$update_sql_data = array ('final_price' => os_db_prepare_input($final_price));
	$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);
	os_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id = \''.os_db_input($_POST['opID']).'\'');

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=options&oID='.$_POST['oID'].'&pID='.$products['products_id'].'&opID='.$_POST['opID']));
}

if (isset($_GET['action']) && $_GET['action'] == "ot_delete") {

	os_db_query("delete from ".TABLE_ORDERS_TOTAL." where orders_total_id = '".os_db_input($_POST['otID'])."'");

	os_redirect(os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID='.$_POST['oID']));
}

if (isset($_GET['action']) && $_GET['action'] == "save_order") {

	$products_query = os_db_query("select SUM(final_price) as subtotal_final from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".$_POST['oID']."' ");
	$products = os_db_fetch_array($products_query);
	$subtotal_final = $products['subtotal_final'];
	$subtotal_text = $osPrice->Format($subtotal_final, true);

	os_db_query("update ".TABLE_ORDERS_TOTAL." set text = '".$subtotal_text."', value = '".$subtotal_final."' where orders_id = '".$_POST['oID']."' and class = 'ot_subtotal' ");

	$check_no_tax_value_query = os_db_query("select count(*) as count from ".TABLE_ORDERS_TOTAL." where orders_id = '".$_POST['oID']."' and class = 'ot_subtotal_no_tax'");
	$check_no_tax_value = os_db_fetch_array($check_no_tax_value_query);

	if ($check_no_tax_value_query['count'] != '0') {
		$subtotal_no_tax_value_query = os_db_query("select SUM(value) as subtotal_no_tax_value from ".TABLE_ORDERS_TOTAL." where orders_id = '".$_POST['oID']."' and class != 'ot_tax' and class != 'ot_total' and class != 'ot_subtotal_no_tax' and class != 'ot_coupon' and class != 'ot_gv'");
		$subtotal_no_tax_value = os_db_fetch_array($subtotal_no_tax_value_query);
		$subtotal_no_tax_final = $subtotal_no_tax_value['subtotal_no_tax_value'];
		$subtotal_no_tax_text = $osPrice->Format($subtotal_no_tax_final, true);
		os_db_query("update ".TABLE_ORDERS_TOTAL." set text = '".$subtotal_no_tax_text."', value = '".$subtotal_no_tax_final."' where orders_id = '".$_POST['oID']."' and class = 'ot_subtotal_no_tax' ");
	}

	$subtotal_query = os_db_query("select SUM(value) as value from ".TABLE_ORDERS_TOTAL." where orders_id = '".$_POST['oID']."' and class != 'ot_subtotal_no_tax' and class != 'ot_tax' and class != 'ot_total'");
	$subtotal = os_db_fetch_array($subtotal_query);

	$subtotal_final = $subtotal['value'];
	$subtotal_text = $osPrice->Format($subtotal_final, true);
	os_db_query("update ".TABLE_ORDERS_TOTAL." set text = '".$subtotal_text."', value = '".$subtotal_final."' where orders_id = '".$_POST['oID']."' and class = 'ot_total'");

	$products_query = os_db_query("select final_price, products_tax, allow_tax from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".$_POST['oID']."' ");
	while ($products = os_db_fetch_array($products_query)) {

		$tax_rate = $products['products_tax'];
		$multi = (($products['products_tax'] / 100) + 1);

		if ($products['allow_tax'] == '1') {
			$bprice = $products['final_price'];
			$nprice = $osPrice->RemoveTax($bprice, $tax_rate);
			$tax = $osPrice->calcTax($nprice, $tax_rate);
		} else {
			$nprice = $products['final_price'];
			$bprice = $osPrice->AddTax($nprice, $tax_rate);
			$tax = $osPrice->calcTax($nprice, $tax_rate);
		}

		$sql_data_array = array ('orders_id' => os_db_prepare_input($_POST['oID']), 'n_price' => os_db_prepare_input($nprice), 'b_price' => os_db_prepare_input($bprice), 'tax' => os_db_prepare_input($tax), 'tax_rate' => os_db_prepare_input($products['products_tax']));

		$insert_sql_data = array ('class' => 'products');
		$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
		os_db_perform(TABLE_ORDERS_RECALCULATE, $sql_data_array);
	}

	$module_query = os_db_query("select value, class from ".TABLE_ORDERS_TOTAL." where orders_id = '".$_POST['oID']."' and class!='ot_total' and class!='ot_subtotal_no_tax' and class!='ot_tax' and class!='ot_subtotal'");
	while ($module_value = os_db_fetch_array($module_query)) {
		;

		$module_name = str_replace('ot_', '', $module_value['class']);

		if ($module_name != 'discount') {
			if ($module_name != 'shipping') {
				$module_tax_class = @constant(MODULE_ORDER_TOTAL_.strtoupper($module_name)._TAX_CLASS);
			} else {
				$module_tmp_name = preg_split('/_/', $order->info['shipping_class']);
				$module_tmp_name = $module_tmp_name[0];
				if ($module_tmp_name != 'selfpickup') {
					$module_tax_class = @constant(MODULE_SHIPPING_.strtoupper($module_tmp_name)._TAX_CLASS);
				} else {
					$module_tax_class = '';
				}
			}
		} else {
			$module_tax_class = '0';
		}

		$cinfo = os_customer_infos($order->customer['ID']);
		$module_tax_rate = os_get_tax_rate($module_tax_class, $cinfo['country_id'], $cinfo['zone_id']);

		$status_query = os_db_query("select customers_status_show_price_tax from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = '".$order->info['status']."'");
		$status = os_db_fetch_array($status_query);

		if ($status['customers_status_show_price_tax'] == 1) {
			$module_b_price = $module_value['value'];
			if ($module_tax == '0') {
				$module_n_price = $module_value['value'];
			} else {
				$module_n_price = $osPrice->RemoveTax($module_b_price, $module_tax_rate);
			}
			$module_tax = $osPrice->calcTax($module_n_price, $module_tax_rate);
		} else {
			$module_n_price = $module_value['value'];
			$module_b_price = $osPrice->AddTax($module_n_price, $module_tax_rate);
			$module_tax = $osPrice->calcTax($module_n_price, $module_tax_rate);
		}

		$sql_data_array = array ('orders_id' => os_db_prepare_input($_POST['oID']), 'n_price' => os_db_prepare_input($module_n_price), 'b_price' => os_db_prepare_input($module_b_price), 'tax' => os_db_prepare_input($module_tax), 'tax_rate' => os_db_prepare_input($module_tax_rate));

		$insert_sql_data = array ('class' => $module_value['class']);
		$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
		os_db_perform(TABLE_ORDERS_RECALCULATE, $sql_data_array);
	}
	os_db_query("delete from ".TABLE_ORDERS_TOTAL." where orders_id = '".os_db_input($_POST['oID'])."' and class='ot_tax'");

	$ust_query = os_db_query("select tax_rate, SUM(tax) as tax_value_new from ".TABLE_ORDERS_RECALCULATE." where orders_id = '".$_POST['oID']."' and tax !='0' GROUP by tax_rate ");
	while ($ust = os_db_fetch_array($ust_query)) {

		$ust_desc_query = os_db_query("select tax_description from ".TABLE_TAX_RATES." where tax_rate = '".$ust['tax_rate']."'");
		$ust_desc = os_db_fetch_array($ust_desc_query);

		$title = $ust_desc['tax_description'];

		if ($ust['tax_value_new']) {
			$text = $osPrice->Format($ust['tax_value_new'], true);

			$sql_data_array = array ('orders_id' => os_db_prepare_input($_POST['oID']), 'title' => os_db_prepare_input($title), 'text' => os_db_prepare_input($text), 'value' => os_db_prepare_input($ust['tax_value_new']), 'class' => 'ot_tax');

			$insert_sql_data = array ('sort_order' => MODULE_ORDER_TOTAL_TAX_SORT_ORDER);
			$sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
			os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
		}

	}

	os_db_query("delete from ".TABLE_ORDERS_RECALCULATE." where orders_id = '".os_db_input($_POST['oID'])."'");

	os_redirect(os_href_link(FILENAME_ORDERS, 'action=edit&oID='.$_POST['oID']));
}
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
<td>
<br /><br />

<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
<td class="main">
<b>
<?php


if (isset($_GET['text']) && $_GET['text'] == 'address') {
	echo TEXT_EDIT_ADDRESS_SUCCESS;
}
?>
</b>
</td>
</tr>
</table>

<?php


if (isset($_GET['edit_action']) && $_GET['edit_action'] == 'address') {
	include ('orders_edit_address.php');
}
elseif (isset($_GET['edit_action']) && $_GET['edit_action'] == 'products') {
	include ('orders_edit_products.php');
}
elseif (isset($_GET['edit_action']) && $_GET['edit_action'] == 'other') {
	include ('orders_edit_other.php');
}
elseif (isset($_GET['edit_action']) && $_GET['edit_action'] == 'options') {
	include ('orders_edit_options.php');
}
?>

<br /><br />
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr class="dataTableRow">
<td class="dataTableContent" align="right">
<?php


echo TEXT_SAVE_ORDER;
echo os_draw_form('save_order', FILENAME_ORDERS_EDIT, 'action=save_order', 'post');
echo os_draw_hidden_field('customers_status_id', isset($address[customers_status])?$address[customers_status]:'');
echo os_draw_hidden_field('oID', $_GET['oID']);
echo os_draw_hidden_field('cID', $_GET[cID]);
echo '<span class="button"><button type="submit" onClick="this.blur();" value="'.BUTTON_SAVE.'"/>'.BUTTON_SAVE.'</button></span>';
?>
</form>
</td>
</tr>

</table>
<br /><br />
</td>
<?php


$heading = array ();
$contents = array ();
switch (@$_GET['action']) {

	default :
		if (is_object($order)) {
			$heading[] = array ('text' => '<b>'.TABLE_HEADING_ORDER.$_GET['oID'].'</b>');

			$contents[] = array ('align' => 'center', 'text' => '<br />'.TEXT_EDIT_ADDRESS.'<br /><a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=address&oID='.$_GET['oID']).'"><span>'.BUTTON_EDIT.'</span></a><br /><br />');
			$contents[] = array ('align' => 'center', 'text' => '<br />'.TEXT_EDIT_PRODUCTS.'<br /><a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID='.$_GET['oID']).'"><span>'.BUTTON_EDIT.'</span></a><br /><br />');
			$contents[] = array ('align' => 'center', 'text' => '<br />'.TEXT_EDIT_OTHER.'<br /><a class="button" onClick="this.blur();" href="'.os_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID='.$_GET['oID']).'"><span>'.BUTTON_EDIT.'</span></a><br /><br />');

		}
		break;
}

if ((os_not_null($heading)) && (os_not_null($contents))) {
	echo '            <td width="20%" valign="top">'."\n";

	$box = new box;
	echo $box->infoBox($heading, $contents);

	echo '            </td>'."\n";
}
?>
  </tr>

        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>