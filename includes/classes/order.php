<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class order
{
	var $info, $totals, $products, $customer, $delivery, $content_type;

	function order($order_id = '')
	{
		$this->info = array();
		$this->totals = array();
		$this->products = array();
		$this->customer = array();
		$this->delivery = array();

		if (os_not_null($order_id))
			$this->query($order_id);
		else
			$this->cart();
	}

	function query($order_id)
	{
		$order_id = os_db_prepare_input($order_id);

		$order_query = os_db_query("SELECT * FROM ".TABLE_ORDERS." WHERE orders_id = '".os_db_input($order_id)."'");
		$order = os_db_fetch_array($order_query);

		$totals_query = os_db_query("SELECT * FROM ".TABLE_ORDERS_TOTAL." where orders_id = '".os_db_input($order_id)."' order by sort_order");
		while ($totals = os_db_fetch_array($totals_query))
		{
			$this->totals[] = array(
				'title' => $totals['title'],
				'text' =>$totals['text'],
				'value'=>$totals['value']
			);
		}

		$order_total_query = os_db_query("select text, value from ".TABLE_ORDERS_TOTAL." where orders_id = '".$order_id."' and class = 'ot_total'");
		$order_total = os_db_fetch_array($order_total_query);
	
		$shipping_method_query = os_db_query("select title from ".TABLE_ORDERS_TOTAL." where orders_id = '".$order_id."' and class = 'ot_shipping'");
		$shipping_method = os_db_fetch_array($shipping_method_query);
	
		$order_status_query = os_db_query("select orders_status_name from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$order['orders_status']."' and language_id = '".$_SESSION['languages_id']."'");
		$order_status = os_db_fetch_array($order_status_query);
	
		$this->info = array(
			'order_id' => $order_id,
			'currency' => $order['currency'],
			'currency_value' => $order['currency_value'],
			'payment_method' => $order['payment_method'],
			'date_purchased' => $order['date_purchased'],
			'orders_status' => $order_status['orders_status_name'],
			'last_modified' => $order['last_modified'],
			'total' => strip_tags($order_total['text']),
			'total_value' => $order_total['value'],
			'shipping_method' => ((substr($shipping_method['title'], -1) == ':') ? substr(strip_tags($shipping_method['title']), 0, -1) : strip_tags($shipping_method['title'])),
			'comments' => $order['comments'],
			'paid' => $order['paid']
		);
	
		$this->customer = array(
			'id' => $order['customers_id'],
			'name' => $order['customers_name'],
			'firstname' => $order['customers_firstname'],
			'secondname' => $order['customers_secondname'],
			'lastname' => $order['customers_lastname'],
			'csID' => $order['customers_cid'],
			'company' => $order['customers_company'],
			'street_address' => $order['customers_street_address'],
			'suburb' => $order['customers_suburb'],
			'city' => $order['customers_city'],
			'postcode' => $order['customers_postcode'],
			'state' => $order['customers_state'],
			'country' => $order['customers_country'],
			'format_id' => $order['customers_address_format_id'],
			'telephone' => $order['customers_telephone'],
			'email_address' => $order['customers_email_address']
		);

		$this->delivery = array(
			'name' => $order['delivery_name'],
			'firstname' => $order['delivery_firstname'],
			'secondname' => $order['delivery_secondname'],
			'lastname' => $order['delivery_lastname'],
			'company' => $order['delivery_company'],
			'street_address' => $order['delivery_street_address'],
			'suburb' => $order['delivery_suburb'],
			'city' => $order['delivery_city'],
			'postcode' => $order['delivery_postcode'],
			'state' => $order['delivery_state'],
			'country' => $order['delivery_country'],
			'format_id' => $order['delivery_address_format_id']
		);

		if (empty($this->delivery['name']) && empty($this->delivery['street_address']))
		{
			$this->delivery = false;
		}

		$this->billing = array(
			'name' => $order['billing_name'],
			'firstname' => $order['billing_firstname'],
			'secondname' => $order['billing_secondname'],
			'lastname' => $order['billing_lastname'],
			'company' => $order['billing_company'],
			'street_address' => $order['billing_street_address'],
			'suburb' => $order['billing_suburb'],
			'city' => $order['billing_city'],
			'postcode' => $order['billing_postcode'],
			'state' => $order['billing_state'],
			'country' => $order['billing_country'],
			'format_id' => $order['billing_address_format_id']
		);

		$index = 0;
		$orders_products_query = os_db_query("SELECT * FROM ".TABLE_ORDERS_PRODUCTS." where orders_id = '".os_db_input($order_id)."'");
		while ($orders_products = os_db_fetch_array($orders_products_query))
		{
			$this->products[$index] = array(
				'qty' => $orders_products['products_quantity'],
				'id' => $orders_products['products_id'],
				'name' => $orders_products['products_name'],
				'model' => $orders_products['products_model'],
				'tax' => $orders_products['products_tax'],
				'price'=>$orders_products['products_price'],
				'shipping_time'=>$orders_products['products_shipping_time'],
				'final_price' => $orders_products['final_price'], 'bundle' => $orders_products['bundle']
			);

			$subindex = 0;
			$attributes_query = os_db_query("SELECT * FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".os_db_input($order_id)."' and orders_products_id = '".$orders_products['orders_products_id']."'");
			if (os_db_num_rows($attributes_query))
			{
				while ($attributes = os_db_fetch_array($attributes_query))
				{
					$this->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options'],
					'value' => $attributes['products_options_values'],
					'prefix' => $attributes['price_prefix'],
					'price' => $attributes['options_values_price']);
					$subindex++;
				}
			}

			$this->info['tax_groups']["{$this->products[$index]['tax']}"] = '1';

			$index++;
		}
	}

	// TODO: на удаление order->getOrderData
	function getOrderData($oID)
	{
		global $osPrice;

		$order_query = os_db_query("SELECT products_id, orders_products_id, products_model, products_name, final_price, products_shipping_time, products_quantity, bundle FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_id='".(int) $oID."'");

		$order_data = array ();
		while ($order_data_values = os_db_fetch_array($order_query))
		{
			$attributes_query = os_db_query("SELECT products_options, products_options_values, price_prefix, options_values_price FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_products_id='".$order_data_values['orders_products_id']."'");
			$attributes_data = '';
			$attributes_model = '';

			while ($attributes_data_values = os_db_fetch_array($attributes_query))
			{
				$attributes_data .= '<br />'.$attributes_data_values['products_options'].': '.$attributes_data_values['products_options_values'];
				$attributes_model .= '<br />'.os_get_attributes_model($order_data_values['products_id'], $attributes_data_values['products_options_values'],$attributes_data_values['products_options']);
			}

			//Bundle
			$products_bundle_data = '';
			if ($order_data_values['bundle'] == 1)
			{
				$bundle_query = getBundleProducts($order_data_values['products_id']);
				if (os_db_num_rows($bundle_query) > 0)
				{
					while($bundle_data = os_db_fetch_array($bundle_query))
					{
						$products_bundle_data .= $bundle_data['products_name'].'<br />';
					}
				}
			}
			//End of Bundle

			$order_data[] = array(
				'PRODUCTS_MODEL' => $order_data_values['products_model'],
				'PRODUCTS_NAME' => $order_data_values['products_name'],
				'PRODUCTS_SHIPPING_TIME' => $order_data_values['products_shipping_time'],
				'PRODUCTS_ATTRIBUTES' => $attributes_data,
				'PRODUCTS_ATTRIBUTES_MODEL' => $attributes_model,
				'PRODUCTS_PRICE' => $osPrice->Format($order_data_values['final_price'], true),
				'PRODUCTS_SINGLE_PRICE' => $osPrice->Format($order_data_values['final_price']/$order_data_values['products_quantity'], true),
				'PRODUCTS_QTY' => $order_data_values['products_quantity'],
				'PRODUCTS_BUNDLE' => $products_bundle_data
			);
		}

		return $order_data;
	}

	// TODO: на удаление order->getTotalData
	function getTotalData($oID)
	{
		global $osPrice,$db;

		$oder_total_query = os_db_query("SELECT title, text, class, value, sort_order FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id='".(int)$oID."' ORDER BY sort_order ASC");

		$order_total = array ();
		while ($oder_total_values = os_db_fetch_array($oder_total_query))
		{
			$order_total[] = array(
				'TITLE' => $oder_total_values['title'],
				'CLASS' => $oder_total_values['class'],
				'VALUE' => $oder_total_values['value'],
				'TEXT' => $oder_total_values['text']
			);

			if ($oder_total_values['class'] = 'ot_total')
				$total = $oder_total_values['value'];
		}

		return array(
			'data'=>$order_total,
			'total'=>$total
		);
	}

	function cart()
	{
		global $currencies, $osPrice, $cartet;

		$this->content_type = $_SESSION['cart']->get_content_type();

		if ($_SESSION['customer_id'])
		{
			$customer_address_query = os_db_query("select c.payment_unallowed,c.shipping_unallowed,c.customers_firstname,c.customers_secondname,c.customers_cid, c.customers_gender,c.customers_lastname, c.customers_telephone, c.customers_email_address, c.orig_reference, c.login_reference, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, co.countries_id, co.countries_name, co.countries_iso_code_2, co.countries_iso_code_3, co.address_format_id, ab.entry_state from ".TABLE_CUSTOMERS." c, ".TABLE_ADDRESS_BOOK." ab left join ".TABLE_ZONES." z on (ab.entry_zone_id = z.zone_id) left join ".TABLE_COUNTRIES." co on (ab.entry_country_id = co.countries_id) where c.customers_id = '".$_SESSION['customer_id']."' and ab.customers_id = '".$_SESSION['customer_id']."' and c.customers_default_address_id = ab.address_book_id");
			$customer_address = os_db_fetch_array($customer_address_query);

			$shipping_address_query = os_db_query("select ab.entry_firstname, ab.entry_secondname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state from ".TABLE_ADDRESS_BOOK." ab left join ".TABLE_ZONES." z on (ab.entry_zone_id = z.zone_id) left join ".TABLE_COUNTRIES." c on (ab.entry_country_id = c.countries_id) where ab.customers_id = '".$_SESSION['customer_id']."' and ab.address_book_id = '".$_SESSION['sendto']."'");
			$shipping_address = os_db_fetch_array($shipping_address_query);

			$billing_address_query = os_db_query("select ab.entry_firstname, ab.entry_secondname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state from ".TABLE_ADDRESS_BOOK." ab left join ".TABLE_ZONES." z on (ab.entry_zone_id = z.zone_id) left join ".TABLE_COUNTRIES." c on (ab.entry_country_id = c.countries_id) where ab.customers_id = '".$_SESSION['customer_id']."' and ab.address_book_id = '".$_SESSION['billto']."'");
			$billing_address = os_db_fetch_array($billing_address_query);

			$tax_address_query = os_db_query("select ab.entry_country_id, ab.entry_zone_id from ".TABLE_ADDRESS_BOOK." ab left join ".TABLE_ZONES." z on (ab.entry_zone_id = z.zone_id) where ab.customers_id = '".$_SESSION['customer_id']."' and ab.address_book_id = '".($this->content_type == 'virtual' ? $_SESSION['billto'] : $_SESSION['sendto'])."'");
			$tax_address = os_db_fetch_array($tax_address_query);
		}
		else
		{
			$state = ($_SESSION['customer_info']['state']) ? $_SESSION['customer_info']['state'] : '';
			$country = ($_SESSION['customer_info']['country']) ? $_SESSION['customer_info']['country'] : STORE_COUNTRY;

			$zone_id = STORE_ZONE;
			if ($country && $state)
			{
				$zone_query = os_db_query("select distinct zone_id, zone_name from ".TABLE_ZONES." where zone_country_id = '".(int)$country."' and zone_name = '".os_db_input($state)."'");
				if (os_db_num_rows($zone_query) > 0)
				{
					$zone = os_db_fetch_array($zone_query);
					$zone_id = $zone['zone_id'];
				}
			}

			$countries = array();
			if ($country)
			{
				$countries_query = os_db_query("select * from ".TABLE_COUNTRIES." where countries_id = '".(int)$country."'");
				if (os_db_num_rows($countries_query) > 0)
				{
					$countries = os_db_fetch_array($countries_query);
				}
			}

			$guestData = array(
				'customers_firstname' => ($_SESSION['customer_info']['firstname']) ? $_SESSION['customer_info']['firstname'] : '',
				'customers_secondname' => ($_SESSION['customer_info']['secondname']) ? $_SESSION['customer_info']['secondname'] : '',
				'customers_lastname' => ($_SESSION['customer_info']['lastname']) ? $_SESSION['customer_info']['lastname'] : '',
				'customers_cid' => '',
				'customers_gender' => '',
				'entry_company' => '',
				'entry_street_address' => ($_SESSION['customer_info']['street_address']) ? $_SESSION['customer_info']['street_address'] : '',
				'entry_suburb' => ($_SESSION['customer_info']['suburb']) ? $_SESSION['customer_info']['suburb'] : '',
				'entry_city' => ($_SESSION['customer_info']['city']) ? $_SESSION['customer_info']['city'] : '',
				'entry_postcode' => ($_SESSION['customer_info']['postcode']) ? $_SESSION['customer_info']['postcode'] : '',
				'entry_state' => $state,
				'entry_zone_id' => $zone_id,
				'countries_id' => $countries['countries_id'],
				'countries_name' => $countries['countries_name'],
				'countries_iso_code_2' => $countries['countries_iso_code_2'],
				'countries_iso_code_3' => $countries['countries_iso_code_3'],
				'address_format_id' => $countries['address_format_id'],
				'customers_telephone' => ($_SESSION['customer_info']['telephone']) ? $_SESSION['customer_info']['telephone'] : '',
				'payment_unallowed' => '',
				'shipping_unallowed' => '',
				'customers_email_address' => ($_SESSION['customer_info']['email_address']) ? $_SESSION['customer_info']['email_address'] : '',
				'orig_reference' => '',
				'login_reference' => '',
			);

			$customer_address = $guestData;
			$shipping_address = $guestData;
			$billing_address = $guestData;
			$tax_address = array();
		}

		$this->info = array(
			'order_status' => DEFAULT_ORDERS_STATUS_ID,
			'currency' => $_SESSION['currency'],
			'currency_value' => $osPrice->currencies[$_SESSION['currency']]['value'],
			'payment_method' => $_SESSION['payment'],
			'shipping_method' => $_SESSION['shipping']['title'],
			'shipping_cost' => $_SESSION['shipping']['cost'],
			'comments' => $_SESSION['comments'],
			'shipping_class'=>$_SESSION['shipping']['id'],
			'payment_class' => $_SESSION['payment'],
		);

		if (isset($_SESSION['payment']) && is_object($_SESSION['payment']))
		{
			$this->info['payment_method'] = $_SESSION['payment']->title;
			$this->info['payment_class'] = $_SESSION['payment']->title;
			if ( isset($_SESSION['payment']->order_status) && is_numeric($_SESSION['payment']->order_status) && ($_SESSION['payment']->order_status > 0) )
			{
				$this->info['order_status'] = $_SESSION['payment']->order_status;
			}
		}

		$this->customer = array(
			'firstname' => $customer_address['customers_firstname'],
			'secondname' => $customer_address['customers_secondname'],
			'lastname' => $customer_address['customers_lastname'],
			'csID' => $customer_address['customers_cid'],
			'gender' => $customer_address['customers_gender'],
			'company' => $customer_address['entry_company'],
			'street_address' => $customer_address['entry_street_address'],
			'suburb' => $customer_address['entry_suburb'],
			'city' => $customer_address['entry_city'],
			'postcode' => $customer_address['entry_postcode'],
			'state' => ((os_not_null($customer_address['entry_state'])) ? $customer_address['entry_state'] : $customer_address['zone_name']),
			'zone_id' => $customer_address['entry_zone_id'],
			'country' => array(
				'id' => $customer_address['countries_id'],
				'title' => $customer_address['countries_name'],
				'iso_code_2' => $customer_address['countries_iso_code_2'],
				'iso_code_3' => $customer_address['countries_iso_code_3']
			),
			'format_id' => $customer_address['address_format_id'],
			'telephone' => $customer_address['customers_telephone'],
			'payment_unallowed' => $customer_address['payment_unallowed'],
			'shipping_unallowed' => $customer_address['shipping_unallowed'],
			'email_address' => $customer_address['customers_email_address'],
			'orig_reference' => $customer_address['orig_reference'],
			'login_reference' => $customer_address['login_reference']
		);

		$this->delivery = array(
			'firstname' => $shipping_address['entry_firstname'],
			'secondname' => $shipping_address['entry_secondname'],
			'lastname' => $shipping_address['entry_lastname'],
			'company' => $shipping_address['entry_company'],
			'street_address' => $shipping_address['entry_street_address'],
			'suburb' => $shipping_address['entry_suburb'],
			'city' => $shipping_address['entry_city'],
			'postcode' => $shipping_address['entry_postcode'],
			'state' => ((os_not_null($shipping_address['entry_state'])) ? $shipping_address['entry_state'] : $shipping_address['zone_name']),
			'zone_id' => $shipping_address['entry_zone_id'],
			'country' => array(
				'id' => $shipping_address['countries_id'],
				'title' => $shipping_address['countries_name'],
				'iso_code_2' => $shipping_address['countries_iso_code_2'],
				'iso_code_3' => $shipping_address['countries_iso_code_3']
			),
			'country_id' => $shipping_address['entry_country_id'],
			'format_id' => $shipping_address['address_format_id']
		);

		$this->billing = array(
			'firstname' => $billing_address['entry_firstname'],
			'secondname' => $billing_address['entry_secondname'],
			'lastname' => $billing_address['entry_lastname'],
			'company' => $billing_address['entry_company'],
			'street_address' => $billing_address['entry_street_address'],
			'suburb' => $billing_address['entry_suburb'],
			'city' => $billing_address['entry_city'],
			'postcode' => $billing_address['entry_postcode'],
			'state' => ((os_not_null($billing_address['entry_state'])) ? $billing_address['entry_state'] : $billing_address['zone_name']),
			'zone_id' => $billing_address['entry_zone_id'],
			'country' => array(
				'id' => $billing_address['countries_id'],
				'title' => $billing_address['countries_name'],
				'iso_code_2' => $billing_address['countries_iso_code_2'],
				'iso_code_3' => $billing_address['countries_iso_code_3']
			),
			'country_id' => $billing_address['entry_country_id'],
			'format_id' => $billing_address['address_format_id']
		);

		$index = 0;
		$products = $_SESSION['cart']->get_products();

		$aTaxClassIds = array();
		foreach($products AS $prod)
		{
			$aTaxClassIds[] = $prod['tax_class_id'];
		}

		$getTaxRate = $cartet->price->getTaxRate(array(
			'tax_class_id' => $aTaxClassIds,
			'entry_country_id' => $tax_address['entry_country_id'],
			'entry_zone_id' => $tax_address['entry_zone_id'],
		));

		for ($i=0, $n=sizeof($products); $i<$n; $i++)
		{
			// $products_price = $osPrice->GetPrice($products[$i]['id'], false, $products[$i]['quantity'], $products[$i]['tax_class_id'], $products[$i]['real_price']);
			//$resultPrice = $products[$i]['real_price'] + $osPrice->Format($_SESSION['cart']->attributes_price($products[$i]['id']), false);

			$this->products[$index] = array(
				'qty' => $products[$i]['quantity'],
				'name' => $products[$i]['name'],
				'model' => $products[$i]['model'],
				'tax_class_id'=> $products[$i]['tax_class_id'],
				'tax' => $getTaxRate[$products[$i]['tax_class_id']]['taxId'], //'tax' => os_get_tax_rate($products[$i]['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
				'tax_description' => $getTaxRate[$products[$i]['tax_class_id']]['taxName'], //os_get_tax_description($products[$i]['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
				'price' => $products[$i]['price'],
				'final_price' => $products[$i]['price']*$products[$i]['quantity'],
				'shipping_time' => $products[$i]['shipping_time'],
				'weight' => $products[$i]['weight'],
				'bundle' => $products[$i]['bundle'],
				'id' => $products[$i]['id'],
			);

			if ($products[$i]['attributes'])
			{
				$subindex = 0;
				reset($products[$i]['attributes']);
				while (list($option, $value) = each($products[$i]['attributes']))
				{
					$attributes_query = os_db_query("select popt.products_options_name, popt.products_options_type, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_OPTIONS_VALUES." poval, ".TABLE_PRODUCTS_ATTRIBUTES." pa where pa.products_id = '".$products[$i]['id']."' and pa.options_id = '".$option."' and pa.options_id = popt.products_options_id and pa.options_values_id = '".$value."' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '".$_SESSION['languages_id']."' and poval.language_id = '".$_SESSION['languages_id']."'");
					$attributes = os_db_fetch_array($attributes_query);

					if($attributes['products_options_type']=='2' || $attributes['products_options_type']=='3')
						$attr_value = $products[$i]['attributes_values'][$option];
					else
						$attr_value = $attributes['products_options_values_name'];

					$this->products[$index]['attributes'][$subindex] = array(
						'option' => $attributes['products_options_name'],
						'value' => $attr_value,
						'option_id' => $option,
						'value_id' => $value,
						'prefix' => $attributes['price_prefix'],
						'price' => $attributes['options_values_price']
					);

					$subindex++;
				}
			}

			$shown_price = $this->products[$index]['final_price'];
			$this->info['subtotal'] += $shown_price;

			if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
			{
				$shown_price_tax = $shown_price-($shown_price/100 * $_SESSION['customers_status']['customers_status_ot_discount']);
			}

			$products_tax = $this->products[$index]['tax'];
			$products_tax_description = $this->products[$index]['tax_description'];

			if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '1')
			{
				if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
				{
					$this->info['tax'] += $shown_price_tax - ($shown_price_tax / (($products_tax < 10) ? "1.0".str_replace('.', '', $products_tax) : "1.".str_replace('.', '', $products_tax)));
					$this->info['tax_groups'][TAX_ADD_TAX."$products_tax_description"] += (($shown_price_tax /(100+$products_tax)) * $products_tax);
				}
				else
				{
					$this->info['tax'] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0".str_replace('.', '', $products_tax) : "1.".str_replace('.', '', $products_tax)));
					$this->info['tax_groups'][TAX_ADD_TAX."$products_tax_description"] += (($shown_price /(100+$products_tax)) * $products_tax);
				}
			}
			else
			{
				if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
				{
					$this->info['tax'] += ($shown_price_tax/100) * ($products_tax);
					$this->info['tax_groups'][TAX_NO_TAX."$products_tax_description"] += ($shown_price_tax/100) * ($products_tax);
				}
				else
				{
					$this->info['tax'] += ($shown_price/100) * ($products_tax);
					$this->info['tax_groups'][TAX_NO_TAX."$products_tax_description"] += ($shown_price/100) * ($products_tax);
				}
			}
			$index++;
		}

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '0')
		{
			$this->info['total'] = $this->info['subtotal'] + $osPrice->Format($this->info['shipping_cost'], false,0,true);
			if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1')
			{
				$this->info['total'] -= ($this->info['subtotal'] /100 * $_SESSION['customers_status']['customers_status_ot_discount']);
			}
		}
		else
		{
			$this->info['total'] = $this->info['subtotal']  + $osPrice->Format($this->info['shipping_cost'], false, 0, true);
			if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1')
			{
				$this->info['total'] -= ($this->info['subtotal'] /100 * $_SESSION['customers_status']['customers_status_ot_discount']);
			}
		}
	}
}