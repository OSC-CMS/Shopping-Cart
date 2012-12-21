<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

class apiOrder extends OscCms
{
	/**
	 * Формирование нового заказа
	 */
	public function newOrder($order, $order_totals, $order_total_modules)
	{
		// Скидки
		if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
			$discount = $_SESSION['customers_status']['customers_status_ot_discount'];
		else
			$discount = '0.00';

		// IP покупателя
		if ($_SERVER["HTTP_X_FORWARDED_FOR"])
			$customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else
			$customers_ip = $_SERVER["REMOTE_ADDR"];

		// Создаем заказ
		$sql_data_array = array
		(
			'customers_id' => $_SESSION['customer_id'],
			'customers_name' => $order->customer['firstname'].' '.$order->customer['secondname'].' '.$order->customer['lastname'],
			'customers_firstname' => $order->customer['firstname'],
			'customers_secondname' => $order->customer['secondname'],
			'customers_lastname' => $order->customer['lastname'],
			'customers_cid' => $order->customer['csID'],
			'customers_vat_id' => $_SESSION['customer_vat_id'],
			'customers_company' => $order->customer['company'],
			'customers_status' => $_SESSION['customers_status']['customers_status_id'],
			'customers_status_name' => $_SESSION['customers_status']['customers_status_name'],
			'customers_status_image' => $_SESSION['customers_status']['customers_status_image'],
			'customers_status_discount' => $discount,
			'customers_street_address' => $order->customer['street_address'],
			'customers_suburb' => $order->customer['suburb'],
			'customers_city' => $order->customer['city'],
			'customers_postcode' => $order->customer['postcode'],
			'customers_state' => $order->customer['state'],
			'customers_country' => $order->customer['country']['title'],
			'customers_telephone' => $order->customer['telephone'],
			'customers_email_address' => $order->customer['email_address'],
			'customers_address_format_id' => $order->customer['format_id'],
			'delivery_name' => $order->delivery['firstname'].' '.$order->delivery['secondname'].' '.$order->delivery['lastname'],
			'delivery_firstname' => $order->delivery['firstname'],
			'delivery_secondname' => $order->delivery['secondname'],
			'delivery_lastname' => $order->delivery['lastname'],
			'delivery_company' => $order->delivery['company'],
			'delivery_street_address' => $order->delivery['street_address'],
			'delivery_suburb' => $order->delivery['suburb'],
			'delivery_city' => $order->delivery['city'],
			'delivery_postcode' => $order->delivery['postcode'],
			'delivery_state' => $order->delivery['state'],
			'delivery_country' => $order->delivery['country']['title'],
			'delivery_country_iso_code_2' => $order->delivery['country']['iso_code_2'],
			'delivery_address_format_id' => $order->delivery['format_id'],
			'billing_name' => (($_SESSION['credit_covers'] != '1') ? $order->billing['firstname'].' '.$order->billing['secondname'].' '.$order->billing['lastname'] : ''),
			'billing_firstname' => (($_SESSION['credit_covers'] != '1') ? $order->billing['firstname'] : ''),
			'billing_secondname' => (($_SESSION['credit_covers'] != '1') ? $order->billing['secondname'] : ''),
			'billing_lastname' => (($_SESSION['credit_covers'] != '1') ? $order->billing['lastname'] : ''),
			'billing_company' => (($_SESSION['credit_covers'] != '1') ? $order->billing['company'] : ''),
			'billing_street_address' => (($_SESSION['credit_covers'] != '1') ? $order->billing['street_address'] : ''),
			'billing_suburb' => (($_SESSION['credit_covers'] != '1') ? $order->billing['suburb'] : ''),
			'billing_city' => (($_SESSION['credit_covers'] != '1') ? $order->billing['city'] : ''),
			'billing_postcode' => (($_SESSION['credit_covers'] != '1') ? $order->billing['postcode'] : ''),
			'billing_state' => (($_SESSION['credit_covers'] != '1') ? $order->billing['state'] : ''),
			'billing_country' => (($_SESSION['credit_covers'] != '1') ? $order->billing['country']['title'] : ''),
			'billing_country_iso_code_2' => (($_SESSION['credit_covers'] != '1') ? $order->billing['country']['iso_code_2'] : ''),
			'billing_address_format_id' => (($_SESSION['credit_covers'] != '1') ? $order->billing['format_id'] : ''),
			'payment_method' => $order->info['payment_method'],
			'payment_class' => $order->info['payment_class'],
			'shipping_method' => $order->info['shipping_method'],
			'shipping_class' => $order->info['shipping_class'],
			'cc_type' => $order->info['cc_type'],
			'cc_owner' => $order->info['cc_owner'],
			'cc_number' => $order->info['cc_number'],
			'cc_expires' => $order->info['cc_expires'],
			'cc_start' => (($_SESSION['credit_covers'] != '1') ? $order->info['cc_start'] : ''),
			'cc_cvv' => (($_SESSION['credit_covers'] != '1') ? $order->info['cc_cvv'] : ''),
			'cc_issue' => (($_SESSION['credit_covers'] != '1') ? $order->info['cc_issue'] : ''),
			'date_purchased' => 'now()',
			'orders_status' => $order->info['order_status'],
			'currency' => $order->info['currency'],
			'currency_value' => $order->info['currency_value'],
			'customers_ip' => $customers_ip,
			'language' => $_SESSION['language'],
			'comments' => $order->info['comments'],
			'orig_reference' => $order->customer['orig_reference'],
			'login_reference' => $order->customer['login_reference']
		);
		os_db_perform(TABLE_ORDERS, $sql_data_array);
		$insert_id = os_db_insert_id();

		// задает ID заказа
		$_SESSION['tmp_oID'] = $insert_id;

		// Создаем ИТОГО
		for ($i = 0, $n = sizeof($order_totals); $i < $n; $i ++)
		{
			$sql_data_array = array
			(
				'orders_id' => $insert_id,
				'title' => $order_totals[$i]['title'],
				'text' => $order_totals[$i]['text'],
				'value' => $order_totals[$i]['value'],
				'class' => $order_totals[$i]['code'],
				'sort_order' => $order_totals[$i]['sort_order']
			);
			os_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
		}

		// Создаем статус заказа
		$sql_data_array = array
		(
			'orders_id' => $insert_id,
			'orders_status_id' => $order->info['order_status'],
			'date_added' => 'now()',
			'customer_notified' => ((SEND_EMAILS == 'true') ? '1' : '0'),
			'comments' => $order->info['comments']
		);
		os_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

		// Обновление количества...
		for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++)
		{
			$this->updateQuantity($order->products[$i]);

			// Создаем товары заказа
			$sql_data_array = array
			(
				'orders_id' => $insert_id,
				'products_id' => os_get_prid($order->products[$i]['id']),
				'products_model' => $order->products[$i]['model'],
				'products_name' => $order->products[$i]['name'],
				'products_shipping_time' => $order->products[$i]['shipping_time'],
				'products_price' => $order->products[$i]['price'],
				'final_price' => $order->products[$i]['final_price'],
				'products_tax' => $order->products[$i]['tax'],
				'products_discount_made' => $order->products[$i]['discount_allowed'],
				'products_quantity' => $order->products[$i]['qty'],
				'allow_tax' => $_SESSION['customers_status']['customers_status_show_price_tax']
			);
			os_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
			$order_products_id = os_db_insert_id();

			// Aenderung Specials Quantity Anfang
			$specials_result = os_db_query("SELECT products_id, specials_quantity from ".TABLE_SPECIALS." WHERE products_id = '".os_get_prid($order->products[$i]['id'])."' ");
			if (os_db_num_rows($specials_result))
			{
				$spq = os_db_fetch_array($specials_result);

				$new_sp_quantity = ($spq['specials_quantity'] - $order->products[$i]['qty']);

				if ($new_sp_quantity >= 1)
				{
					os_db_query("UPDATE ".TABLE_SPECIALS." SET specials_quantity = '".$new_sp_quantity."' WHERE products_id = '".os_get_prid($order->products[$i]['id'])."' ");
				}
				else
				{
					os_db_query("UPDATE ".TABLE_SPECIALS." SET status = '0', specials_quantity = '".$new_sp_quantity."' WHERE products_id = '".os_get_prid($order->products[$i]['id'])."' ");
				}
			}
			// Aenderung Ende

			$order_total_modules->update_credit_account($i); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM
			//------insert customer choosen option to order--------
			$attributes_exist = '0';
			if (isset ($order->products[$i]['attributes']))
			{
				$attributes_exist = '1';
				for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j ++)
				{
					if (DOWNLOAD_ENABLED == 'true')
					{
						$attributes = os_db_query("
						SELECT 
							popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.attributes_model, pad.products_attributes_maxdays, pad.products_attributes_maxcount, pad.products_attributes_filename
						FROM 
							".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_OPTIONS_VALUES." poval, ".TABLE_PRODUCTS_ATTRIBUTES." pa
								LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad ON (pa.products_attributes_id = pad.products_attributes_id)
						WHERE 
							pa.products_id = '".$order->products[$i]['id']."' AND 
							pa.options_id = '".$order->products[$i]['attributes'][$j]['option_id']."' AND 
							pa.options_id = popt.products_options_id AND 
							pa.options_values_id = '".$order->products[$i]['attributes'][$j]['value_id']."' AND 
							pa.options_values_id = poval.products_options_values_id AND 
							popt.language_id = '".$_SESSION['languages_id']."' AND 
							poval.language_id = '".$_SESSION['languages_id']."'
						");
					}
					else
					{
						$attributes = os_db_query("
						SELECT 
							popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.attributes_model
						FROM 
							".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_OPTIONS_VALUES." poval, ".TABLE_PRODUCTS_ATTRIBUTES." pa
						WHERE 
							pa.products_id = '".$order->products[$i]['id']."' AND 
							pa.options_id = '".$order->products[$i]['attributes'][$j]['option_id']."' AND 
							pa.options_id = popt.products_options_id AND 
							pa.options_values_id = '".$order->products[$i]['attributes'][$j]['value_id']."' AND 
							pa.options_values_id = poval.products_options_values_id AND 
							popt.language_id = '".$_SESSION['languages_id']."' AND 
							poval.language_id = '".$_SESSION['languages_id']."'
						");
					}

					// update attribute stock
					os_db_query("UPDATE ".TABLE_PRODUCTS_ATTRIBUTES." SET attributes_stock=attributes_stock - '".$order->products[$i]['qty']."' WHERE products_id='".$order->products[$i]['id']."' AND options_values_id='".$order->products[$i]['attributes'][$j]['value_id']."' AND options_id='".$order->products[$i]['attributes'][$j]['option_id']."'");

					$attributes_values = os_db_fetch_array($attributes);

					// Создаем атрибуты товара в заказе
					$sql_data_array = array
					(
						'orders_id' => $insert_id,
						'orders_products_id' => $order_products_id,
						'products_options' => $attributes_values['products_options_name'],
						'products_options_values' => $order->products[$i]['attributes'][$j]['value'],
						'options_values_price' => $attributes_values['options_values_price'],
						'price_prefix' => $attributes_values['price_prefix'],
						'attributes_model' => $attributes_values['attributes_model'],
					);
					os_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

					if ((DOWNLOAD_ENABLED == 'true') && isset ($attributes_values['products_attributes_filename']) && os_not_null($attributes_values['products_attributes_filename']))
					{
						// Если файл, то создаем еще и атрибубт file в заказе
						$sql_data_array = array
						(
							'orders_id' => $insert_id,
							'orders_products_id' => $order_products_id,
							'orders_products_filename' => $attributes_values['products_attributes_filename'],
							'download_maxdays' => $attributes_values['products_attributes_maxdays'],
							'download_count' => $attributes_values['products_attributes_maxcount']
						);
						os_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
					}
				}
			}
		}

		if (isset($_SESSION['tracking']['refID']))
		{
			os_db_query("UPDATE ".TABLE_ORDERS." SET refferers_id = '".$_SESSION['tracking']['refID']."' WHERE orders_id = '".(int)$insert_id."'");

			// check if late or direct sale                         
			$customers_logon_query = os_db_query("SELECT customers_info_number_of_logons FROM ".TABLE_CUSTOMERS_INFO." WHERE customers_info_id  = '".(int)$_SESSION['customer_id']."'");
			$customers_logon = os_db_fetch_array($customers_logon_query);

			if ($customers_logon['customers_info_number_of_logons'] == 0)
			{
				// direct sale
				os_db_query("UPDATE ".TABLE_ORDERS." SET conversion_type = '1' WHERE orders_id = '".(int)$insert_id."'");
			}
			else
			{
				// late sale
				os_db_query("UPDATE ".TABLE_ORDERS." SET conversion_type = '2' WHERE orders_id = '".(int)$insert_id."'");
			}
		}
		else
		{
			$customers_query = os_db_query("SELECT refferers_id as ref FROM ".TABLE_CUSTOMERS." WHERE customers_id='".(int)$_SESSION['customer_id']."'");
			$customers_data = os_db_fetch_array($customers_query);
			if (os_db_num_rows($customers_query))
			{
				os_db_query("UPDATE ".TABLE_ORDERS." SET refferers_id = '".$customers_data['ref']."' WHERE orders_id = '".(int)$insert_id."'");
				// check if late or direct sale                         
				$customers_logon_query = os_db_query("SELECT customers_info_number_of_logons FROM ".TABLE_CUSTOMERS_INFO." WHERE customers_info_id  = '".(int)$_SESSION['customer_id']."'");
				$customers_logon = os_db_fetch_array($customers_logon_query);

				if ($customers_logon['customers_info_number_of_logons'] == 0)
				{
					// direct sale
					os_db_query("UPDATE ".TABLE_ORDERS." SET conversion_type = '1' WHERE orders_id = '".(int)$insert_id."'");
				}
				else
				{
					// late sale
					os_db_query("UPDATE ".TABLE_ORDERS." SET conversion_type = '2' WHERE orders_id = '".(int)$insert_id."'");
				}
			}
		}

		return array(
			'insert_id' => $insert_id
		);
	}

	/**
	 * Удаление заказа по ID
	 */
	public function deleteOrderById($order_id)
	{
		os_db_query('DELETE FROM '.TABLE_ORDERS.' WHERE orders_id = "'.(int)$order_id.'"');
		os_db_query('DELETE FROM '.TABLE_ORDERS_TOTAL.' WHERE orders_id = "'.(int)$order_id.'"');
		os_db_query('DELETE FROM '.TABLE_ORDERS_STATUS_HISTORY.' WHERE orders_id = "'.(int)$order_id.'"');
		os_db_query('DELETE FROM '.TABLE_ORDERS_PRODUCTS.' WHERE orders_id = "'.(int)$order_id.'"');
		os_db_query('DELETE FROM '.TABLE_ORDERS_PRODUCTS_ATTRIBUTES.' WHERE orders_id = "'.(int)$order_id.'"');
		os_db_query('DELETE FROM '.TABLE_ORDERS_PRODUCTS_DOWNLOAD.' WHERE orders_id = "'.(int)$order_id.'"');
	}

	/**
	 * Обновление количества товара
	 */
	public function updateQuantity($orderProducts)
	{
		if (STOCK_LIMITED == 'true')
		{
			// Если включено скачивание
			if (DOWNLOAD_ENABLED == 'true')
			{
				$stock_query_raw = "
				SELECT 
					products_quantity, pad.products_attributes_filename
				FROM 
					".TABLE_PRODUCTS." p
						LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES." pa ON (p.products_id = pa.products_id)
						LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad ON (pa.products_attributes_id = pad.products_attributes_id)
				WHERE 
					p.products_id = '".os_get_prid($orderProducts['id'])."'
				";
				// Will work with only one option for downloadable products
				// otherwise, we have to build the query dynamically with a loop
				$products_attributes = $orderProducts['attributes'];
				if (is_array($products_attributes))
				{
					$stock_query_raw .= " AND pa.options_id = '".$products_attributes[0]['option_id']."' AND pa.options_values_id = '".$products_attributes[0]['value_id']."'";
				}
				$stock_query = os_db_query($stock_query_raw);
			}
			else
			{
				$stock_query = os_db_query("SELECT products_quantity, products_bundle FROM ".TABLE_PRODUCTS." WHERE products_id = '".os_get_prid($orderProducts['id'])."'");
			}

			if (os_db_num_rows($stock_query) > 0)
			{
				$stock_values = os_db_fetch_array($stock_query);

				// Наборы
				if($stock_values['products_bundle'] == '1')
				{
					$bundle_query = os_db_query("
					SELECT 
						pb.subproduct_id, pb.subproduct_qty, p.products_model, p.products_quantity, p.products_bundle 
					FROM 
						".DB_PREFIX."products_bundles pb 
							LEFT JOIN ".TABLE_PRODUCTS." p ON (p.products_id = pb.subproduct_id) 
					WHERE 
						pb.bundle_id = '".os_get_prid($orderProducts['id'])."'
					");

					while($bundle_data = os_db_fetch_array($bundle_query))
					{
						if($bundle_data['products_bundle'] == "1")
						{
							// Уменьшаем количество у товаров в наборе
							$bundle_query_nested = os_db_query("SELECT pb.subproduct_id, pb.subproduct_qty, p.products_model, p.products_quantity, p.products_bundle FROM ".DB_PREFIX."products_bundles pb LEFT JOIN ".TABLE_PRODUCTS." p ON p.products_id = pb.subproduct_id WHERE pb.bundle_id = '".$bundle_data['subproduct_id']."'");
							while($bundle_data_nested = os_db_fetch_array($bundle_query_nested))
							{
								$stock_left = $bundle_data_nested['products_quantity'] - $bundle_data_nested['subproduct_qty'] * $orderProducts['qty'];
								os_db_query("UPDATE ".TABLE_PRODUCTS." SET products_quantity = '".$stock_left."' WHERE products_id = '".$bundle_data_nested['subproduct_id']."'");
							}
						}
						else
						{
							$stock_left = $bundle_data['products_quantity'] - $bundle_data['subproduct_qty'] * $orderProducts['qty'];
							os_db_query("UPDATE ".TABLE_PRODUCTS." SET products_quantity = '".$stock_left."' WHERE products_id = '".$bundle_data['subproduct_id']."'");
						}
					}
				}
				// Наборы

				// do not decrement quantities if products_attributes_filename exists
				if ((DOWNLOAD_ENABLED != 'true') || (!$stock_values['products_attributes_filename']))
					$stock_left = $stock_values['products_quantity'] - $orderProducts['qty'];
				else
					$stock_left = $stock_values['products_quantity'];

				os_db_query("UPDATE ".TABLE_PRODUCTS." SET products_quantity = '".$stock_left."' WHERE products_id = '".os_get_prid($orderProducts['id'])."'");

				if (($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false'))
				{
					os_db_query("UPDATE ".TABLE_PRODUCTS." SET products_status = '0' WHERE products_id = '".os_get_prid($orderProducts['id'])."'");
				}
			}
		}

		// Прибавляем +1 к продажам
		os_db_query("UPDATE ".TABLE_PRODUCTS." SET products_ordered = products_ordered + ".sprintf('%d', $orderProducts['qty'])." WHERE products_id = '".os_get_prid($orderProducts['id'])."'");
	}

	/**
	 * Подтверждение заказа и его создание
	 */
	public function confirmation($sessionPayment, $order, $order_total_modules)
	{
		if (isset($_SESSION['cartID']))
		{
			$insert_order = false;

			if (isset($_SESSION[$sessionPayment]))
			{
				$order_id = substr($_SESSION[$sessionPayment], strpos($_SESSION[$sessionPayment], '-')+1);
				$curr_check = os_db_query("SELECT currency FROM ".TABLE_ORDERS." WHERE orders_id = '".(int)$order_id."'");
				$curr = os_db_fetch_array($curr_check);

				if (($curr['currency'] != $order->info['currency']) || ($_SESSION['cartID'] != substr($_SESSION[$sessionPayment], 0, strlen($_SESSION['cartID']))))
				{
					$check_query = os_db_query('SELECT orders_id FROM '.TABLE_ORDERS_STATUS_HISTORY.' WHERE orders_id = "'.(int)$order_id.'" limit 1');

					if (os_db_num_rows($check_query) < 1)
					{
						$this->deleteOrderById($order_id);
					}
					$insert_order = true;
				}
			}
			else
				$insert_order = true;

			if ($insert_order == true)
			{
				$order_totals = array();
				if (is_array($order_total_modules->modules))
				{
					reset($order_total_modules->modules);
					while (list(, $value) = each($order_total_modules->modules))
					{
						$class = substr($value, 0, strrpos($value, '.'));
						if ($GLOBALS[$class]->enabled)
						{
							for ($i=0, $n=sizeof($GLOBALS[$class]->output); $i<$n; $i++)
							{
								if (os_not_null($GLOBALS[$class]->output[$i]['title']) && os_not_null($GLOBALS[$class]->output[$i]['text']))
								{
									$order_totals[] = array(
										'code' => $GLOBALS[$class]->code,
										'title' => $GLOBALS[$class]->output[$i]['title'],
										'text' => $GLOBALS[$class]->output[$i]['text'],
										'value' => $GLOBALS[$class]->output[$i]['value'],
										'sort_order' => $GLOBALS[$class]->sort_order
									);
								}
							}
						}
					}
				}

				// Формируем заказ и считаем товары...
				$aNewOrder = $this->newOrder($order, $order_totals, $order_total_modules);

				$_SESSION[$sessionPayment] = $_SESSION['cartID'].'-'.$aNewOrder['insert_id'];
			}
			return array(
				'insert_order' => $insert_order
			);
		}
		else
			return false;
	}
}
?>