<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiOrder extends CartET
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
			'date_purchased' => 'now()',
			'orders_status' => $order->info['order_status'],
			'currency' => $order->info['currency'],
			'currency_value' => $order->info['currency_value'],
			'customers_ip' => $customers_ip,
			'language' => $_SESSION['language'],
			'comments' => $order->info['comments'],
			'orig_reference' => $order->customer['orig_reference'],
			'login_reference' => $order->customer['login_reference'],
			'paid' => 0,
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

		for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++)
		{
			// Обновление количества...
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
				'allow_tax' => $_SESSION['customers_status']['customers_status_show_price_tax'],
				'bundle' => $order->products[$i]['bundle']
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
			if (isset($order->products[$i]['attributes']))
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

					// обновляем количество атрибутов
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

			$conversion_type = ($customers_logon['customers_info_number_of_logons'] == 0) ? 1 : 2;
			os_db_query("UPDATE ".TABLE_ORDERS." SET conversion_type = '".(int)$conversion_type."' WHERE orders_id = '".(int)$insert_id."'");
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

				$conversion_type = ($customers_logon['customers_info_number_of_logons'] == 0) ? 1 : 2;
				os_db_query("UPDATE ".TABLE_ORDERS." SET conversion_type = '".(int)$conversion_type."' WHERE orders_id = '".(int)$insert_id."'");
			}
		}

		return array(
			'order_id' => $insert_id
		);
	}

	/**
	 * Удаление заказа по ID
	 */
	public function deleteOrderById($data)
	{
		$oId = (is_array($data)) ? $data['order_id'] : $data;

		// пересчет товара
		if (isset($data['restock']) && $data['restock'] == 'on')
		{
			$order_query = os_db_query("SELECT products_id, products_quantity FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_id = '".(int)$oId."'");
			while ($order = os_db_fetch_array($order_query))
			{
				os_db_query("UPDATE ".TABLE_PRODUCTS." SET products_status = '1', products_quantity = products_quantity + ".$order['products_quantity'].", products_ordered = products_ordered - ".$order['products_quantity']." WHERE products_id = '".$order['products_id']."'");
			}
		}

		// Удаляем заказ
		os_db_query('DELETE FROM '.TABLE_ORDERS.' WHERE orders_id = "'.(int)$oId.'"');
		os_db_query('DELETE FROM '.TABLE_ORDERS_TOTAL.' WHERE orders_id = "'.(int)$oId.'"');
		os_db_query('DELETE FROM '.TABLE_ORDERS_STATUS_HISTORY.' WHERE orders_id = "'.(int)$oId.'"');
		os_db_query('DELETE FROM '.TABLE_ORDERS_PRODUCTS.' WHERE orders_id = "'.(int)$oId.'"');
		os_db_query('DELETE FROM '.TABLE_ORDERS_PRODUCTS_ATTRIBUTES.' WHERE orders_id = "'.(int)$oId.'"');
		os_db_query('DELETE FROM '.TABLE_ORDERS_PRODUCTS_DOWNLOAD.' WHERE orders_id = "'.(int)$oId.'"');

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
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
										'text' => $GLOBALS[$class]->output[$i]['text'].' '.$_SESSION['currencySymbol'],
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

				$_SESSION[$sessionPayment] = $_SESSION['cartID'].'-'.$aNewOrder['order_id'];
			}
			return array(
				'insert_order' => $insert_order
			);
		}
		else
			return false;
	}

	/**
	 * Уведомление покупателей о завершении заказа
	 */
	public function beforeProcess($order_id)
	{
		if (empty($order_id)) return false;

		$order = new order($order_id);

		$osTemplate = new osTemplate;

		$osTemplate->assign('address_label_customer', os_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
		$osTemplate->assign('address_label_shipping', os_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));

		if ($_SESSION['credit_covers'] != '1')
		{
			$osTemplate->assign('address_label_payment', os_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
		}

		$osTemplate->assign('csID', $order->customer['csID']);

		$semextrfields = osDBquery("SELECT * FROM ".TABLE_EXTRA_FIELDS." WHERE fields_required_email = '1'");
		$extra_fields = '';
		while($dataexfes = os_db_fetch_array($semextrfields, true))
		{
			$cusextrfields = osDBquery("SELECT * FROM ".TABLE_CUSTOMERS_TO_EXTRA_FIELDS." WHERE customers_id = '".(int)$_SESSION['customer_id']."' and fields_id = '".$dataexfes['fields_id']."'");
			$rescusextrfields = os_db_fetch_array($cusextrfields, true);

			$extrfieldsinf = osDBquery("SELECT fields_name FROM ".TABLE_EXTRA_FIELDS_INFO." WHERE fields_id = '".$dataexfes['fields_id']."' and languages_id = '".$_SESSION['languages_id']."'");

			$extrfieldsres = os_db_fetch_array($extrfieldsinf, true);
			$extra_fields .= $extrfieldsres['fields_name'].' : '.
			$rescusextrfields['value']."\n";

			$osTemplate->assign('customer_extra_fields', $extra_fields);
		}

		$order_total = $order->getTotalData($order_id);
		$osTemplate->assign('order_data', $order->getOrderData($order_id));
		$osTemplate->assign('order_total', $order_total['data']);

		$osTemplate->assign('language', $_SESSION['language']);
		$osTemplate->assign('tpl_path',_HTTP_THEMES_C);
		$osTemplate->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/img/');
		$osTemplate->assign('oID', $order_id);

		if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment')
		{
			include (_MODULES.'payment/'.$order->info['payment_method'].'/'.$_SESSION['language'].'.php');
			$payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
		}
		$osTemplate->assign('PAYMENT_METHOD', $payment_method);

		if ($order->info['shipping_method'] != '')
		{
			$shipping_method = $order->info['shipping_method'];
		}
		$osTemplate->assign('SHIPPING_METHOD', $shipping_method);

		$osTemplate->assign('DATE', os_date_long($order->info['date_purchased']));
		$osTemplate->assign('NAME', $order->customer['firstname'] . ' ' . $order->customer['lastname']);
		$osTemplate->assign('COMMENTS', $order->info['comments']);
		$osTemplate->assign('EMAIL', $order->customer['email_address']);
		$osTemplate->assign('PHONE',$order->customer['telephone']);

		// Информация по оплате
		// WebMoney
		if ($order->info['payment_method'] == 'webmoney')
		{
			$osTemplate->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_WEBMONEY_TEXT_DESCRIPTION);
			$osTemplate->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_WEBMONEY_TEXT_DESCRIPTION));
		}

		// Yandex
		if ($order->info['payment_method'] == 'yandex')
		{
			$osTemplate->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_YANDEX_TEXT_DESCRIPTION);
			$osTemplate->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_YANDEX_TEXT_DESCRIPTION));
		}

		$osTemplate->caching = false;

		$html_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/order_mail.html');
		$txt_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/order_mail.txt');

		// Тема письма
		$order_subject = str_replace('{$nr}', $order_id, EMAIL_BILLING_SUBJECT_ORDER);
		$order_subject = str_replace('{$date}', strftime(DATE_FORMAT_LONG), $order_subject);
		$order_subject = str_replace('{$lastname}', $order->customer['lastname'], $order_subject);
		$order_subject = str_replace('{$firstname}', $order->customer['firstname'], $order_subject);

		// Уведомление администратору
		os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, EMAIL_BILLING_ADDRESS, STORE_NAME, EMAIL_BILLING_FORWARDING_STRING, $order->customer['email_address'], $order->customer['firstname'], '', '', $order_subject, $html_mail, $txt_mail);

		// Уведомление покупателю
		if ($order->customer['email_address'])
			os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $order->customer['email_address'], $order->customer['firstname'].' '.$order->customer['lastname'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $order_subject, $html_mail, $txt_mail);

		// СМС уведомления
		$smsSetting = $this->sms->setting();

		if ($smsSetting['sms_status'] == 1)
		{
			$getDefaultSms = $this->sms->getDefaultSms();

			// шаблон смс письма
			$osTemplate->caching = 0;
			$smsText = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/order_mail_sms.txt');

			// уведомление администратора
			if ($getDefaultSms['phone'] && $smsSetting['sms_order_admin'] == 1)
			{
				$this->sms->send($smsText);
			}

			// уведомление покупателя
			if ($order->customer['telephone'] && $smsSetting['sms_order'] == 1)
			{
				$this->sms->send($smsText, $order->customer['telephone']);
			}
		}

		do_action('send_order');

		return true;
	}

	/**
	 * Редактирование адресов заказа
	 */
	public function editAddress($post)
	{
		$data = array();
		if (isset($post) && !empty($post))
		{
			$lang_query = os_db_query("SELECT languages_id FROM ".TABLE_LANGUAGES." WHERE directory = '".os_db_prepare_input($post['language'])."'");
			$lang = os_db_fetch_array($lang_query);

			$status_query = os_db_query("SELECT customers_status_name FROM ".TABLE_CUSTOMERS_STATUS." WHERE customers_status_id = '".(int)$post['customers_status']."' AND language_id = '".$lang['languages_id']."' ");
			$status = os_db_fetch_array($status_query);

			$sql_data_array = array(
				'customers_vat_id' => os_db_prepare_input($post['customers_vat_id']),
				'customers_status' => os_db_prepare_input($post['customers_status']),
				'customers_status_name' => os_db_prepare_input($status['customers_status_name']),
				'customers_company' => os_db_prepare_input($post['customers_company']),
				'customers_name' => os_db_prepare_input($post['customers_name']),
				'customers_street_address' => os_db_prepare_input($post['customers_street_address']),
				'customers_city' => os_db_prepare_input($post['customers_city']),
				'customers_state' => os_db_prepare_input($post['customers_state']),
				'customers_postcode' => os_db_prepare_input($post['customers_postcode']),
				'customers_country' => os_db_prepare_input($post['customers_country']),
				'customers_telephone' => os_db_prepare_input($post['customers_telephone']),
				'customers_email_address' => os_db_prepare_input($post['customers_email_address']),
				'delivery_company' => os_db_prepare_input($post['delivery_company']),
				'delivery_name' => os_db_prepare_input($post['delivery_name']),
				'delivery_street_address' => os_db_prepare_input($post['delivery_street_address']),
				'delivery_city' => os_db_prepare_input($post['delivery_city']),
				'delivery_state' => os_db_prepare_input($post['delivery_state']),
				'delivery_postcode' => os_db_prepare_input($post['delivery_postcode']),
				'delivery_country' => os_db_prepare_input($post['delivery_country']),
				'billing_company' => os_db_prepare_input($post['billing_company']),
				'billing_name' => os_db_prepare_input($post['billing_name']),
				'billing_street_address' => os_db_prepare_input($post['billing_street_address']),
				'billing_city' => os_db_prepare_input($post['billing_city']),
				'billing_state' => os_db_prepare_input($post['billing_state']),
				'billing_postcode' => os_db_prepare_input($post['billing_postcode']),
				'billing_country' => os_db_prepare_input($post['billing_country']),
			);

			$update_sql_data = array ('last_modified' => 'now()');
			$sql_data_array = os_array_merge($sql_data_array, $update_sql_data);

			os_db_perform(TABLE_ORDERS, $sql_data_array, 'update', "orders_id = '".(int)$post['order_id']."'");

			$data = array('msg' => 'Успешно обновлено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		
		return $data;
	}

	/**
	 * Быстрое редактирование данных товара
	 */
	public function quickChangeProduct($post)
	{
		$data = array();
		// Проверяем, не пусты ли значения. Не проверяем только value, так как может быть пустым
		if (is_array($post) && !empty($post['pk']) && !empty($post['name']))
		{
			$sqlOrderProducts[$post['name']] = os_db_prepare_input($post['value']);

			if (is_array($sqlOrderProducts) && !empty($sqlOrderProducts))
			{
				$result = os_db_perform(TABLE_ORDERS_PRODUCTS, $sqlOrderProducts, 'update', "orders_products_id  = '".(int)$post['pk']."'");
			}

			if ($result)
				$data = array('msg' => 'Успешно обновлено!', 'type' => 'ok');
			else
				$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		
		return $data;
	}

	/**
	 * Пересчет Итого
	 */
	public function recalculateTotalPrice($o_id, $price)
	{
		if (empty($o_id)) return false;

		$order = new order($o_id);
		$osPrice = new osPrice($order->info['currency'], isset($order->info['status']) ? $order->info['status'] : '');

		$getOrderTotal = os_db_query("SELECT * FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id = '".(int)$o_id."'");
		if (os_db_num_rows($getOrderTotal) > 0)
		{
			while($o = os_db_fetch_array($getOrderTotal))
			{
				if ($o['class'] == 'ot_total' OR $o['class'] == 'ot_subtotal')
				{
					$calculate = $o['value']+$price;

					$formatPrice = $osPrice->currencies[$order->info['currency']]['symbol_left'].' '.$osPrice->Format($calculate, true).' '.$osPrice->currencies[$order->info['currency']]['symbol_right'];

					os_db_perform(TABLE_ORDERS_TOTAL, array(
						'text' => os_db_prepare_input($formatPrice),
						'value' => os_db_input($calculate),
					), 'update', "class = '".os_db_input($o['class'])."' AND orders_id = '".(int)$o_id."'");
				}
			}
		}
	}

	/**
	 * Быстрое добавление товара к заказу
	 */
	public function addProductsToOrder($params = array())
	{
		$newProducts = array();
		if (is_array($params['new_product']))
		{
			foreach($params['new_product'] as $k => $v)
				foreach($v as $item => $value)
					$newProducts[$item][$k] = $value;

			$pIds = array();
			foreach($newProducts AS $product)
				$pIds[] = $product['products_id'];

			// Просто скидки
			$product_specials_query = os_db_query("select products_id, specials_new_products_price from ".TABLE_SPECIALS." where status = 1 AND products_id IN (".implode(',', $pIds).")");
			$checkspecial = array();
			if (os_db_num_rows($product_specials_query) > 0)
			{
				while ($product = os_db_fetch_array($product_specials_query))
				{
					$checkspecial[$product['products_id']] = $product['specials_new_products_price'];
				}
			}

			// Время доставки
			$status_query = osDBquery("SELECT shipping_status_name, shipping_status_id FROM ".TABLE_SHIPPING_STATUS." where language_id = '".(int)$_SESSION['languages_id']."'");
			$shipping_status = array();
			if (os_db_num_rows($status_query, true) > 0)
			{
				while ($status_data = os_db_fetch_array($status_query, true)) 
				{
					$shipping_status[$status_data['shipping_status_id']] = $status_data['shipping_status_name'];
				}
			}

			$total_price = '';
			foreach ($newProducts as $key => $products)
			{
				if ($products['products_price'] == $products['products_real_price'])
				{
					$products['products_price'] = ($checkspecial[$products['products_id']]) ? $checkspecial[$products['products_id']] : $products['products_price'];
				}

				$sqlOrderProducts = array(
					'orders_id' => (int)$products['orders_id'],
					'products_id' => (int)$products['products_id'],
					'products_model' => os_db_prepare_input($products['products_model']),
					'products_name' => os_db_prepare_input($products['products_name']),
					'products_price' => os_db_prepare_input($products['products_price']),
					'products_discount_made' => '',
					'products_shipping_time' => os_db_prepare_input($shipping_status[$products['products_shippingtime']]),
					'final_price' => os_db_prepare_input($products['products_price']*$products['product_qty']),
					'products_tax' => 0,
					'products_quantity' => (int)$products['product_qty'],
					'allow_tax' => (int)$products['allow_tax'],
					'bundle' => 0,
				);

				$total_price += $products['products_price']*$products['product_qty'];
				os_db_perform(TABLE_ORDERS_PRODUCTS, $sqlOrderProducts);

				// обновление количества товара
				if (STOCK_LIMITED == 'true')
				{
					$updateQty = array(
						'qty' => (int)$products['product_qty'],
						'name' => os_db_prepare_input($products['products_name']),
						'model' => os_db_prepare_input($products['products_model']),
						'tax_class_id' => 0,
						'tax' => '',
						'tax_description' => '',
						'price' => os_db_prepare_input($products['products_price']),
						'final_price' => os_db_prepare_input($products['products_price']*$products['product_qty']),
						'shipping_time' => os_db_prepare_input($shipping_status[$products['products_shippingtime']]),
						'weight' => '0.00',
						'bundle' => 0,
						'id' => (int)$products['products_id'],
					);
					$this->updateQuantity($updateQty);
				}
			}

			if ($params['recalculate'] == '1')
			{
				$this->recalculateTotalPrice($params['order_id'], $total_price);
			}
		}
	}

	/**
	 * Редактирование дополнительной информации заказа
	 */
	public function editOther($post)
	{
		$data = array();
		if (isset($post) && !empty($post))
		{
			// костыль
			require (get_path('class_admin').'order.php');
			$order = new order($post['order_id']);
			require (_CLASS.'price.php');
			$osPrice = new osPrice($order->info['currency'], isset($order->info['status']) ? $order->info['status'] : '');
			// костыль

			// Язык
			$langs = explode('_', $post['order_lang']);
			$langDir = $langs[0];
			$langId = $langs[1];

			// Новая валюта, если валюта заказа != текущей в магазине, то конвертируем
			if ($order->info['currency'] != $post['old_currencies_id'])
			{
				$curr_query = os_db_query("SELECT * FROM ".TABLE_CURRENCIES." WHERE currencies_id = '".(int)$post['order_currencies']."' ");
				$curr = os_db_fetch_array($curr_query);
			}

			// Получаем товары заказа
			$orderProducts = $this->orders->getProducts($post['order_id']);
			// Обновляем названия товара в заказе исходя из выбранного языка
			if (is_array($orderProducts) && !empty($orderProducts))
			{
				foreach ($orderProducts as $product)
				{
					// получаем название товара на нужном языке, если пришедшый язык != текущему
					if ($order->info['language'] != $langDir)
					{
						$products_query = os_db_query("SELECT products_name FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id = '".(int)$product['products_id']."' AND language_id = '".(int)$langId."' ");
						if (os_db_num_rows($products_query) > 0)
						{
							$products = os_db_fetch_array($products_query);

							// если название на пришедшем языке вообще указано
							if (!empty($products['products_name']))
							{
								$sqlOrderProducts['products_name'] = os_db_prepare_input($products['products_name']);
							}
						}
					}

					// если валюта заказа != текущей пришедшей
					if ($order->info['currency'] != $post['old_currencies_id'])
					{
						$osPrice = new osPrice($order->info['currency'], $order->info['status']);
						$p_price = $osPrice->RemoveCurr($product['products_price']);
						$f_price = $osPrice->RemoveCurr($product['final_price']);
						$osPrice = new osPrice($curr['code'], $order->info['status']);
						$products_price = $osPrice->GetPrice($product['products_id'], false, '', '', $p_price, '', $order->customer['ID']);
						$final_price = $osPrice->GetPrice($product['products_id'], false, '', '', $f_price, '', $order->customer['ID']);

						$sqlOrderProducts['products_price'] = os_db_prepare_input($products_price['price']);
						$sqlOrderProducts['final_price'] = os_db_prepare_input($final_price['price']);

						// TODO: доделать конвертирование цен атрибутов в заказе
						/*if (is_array($product['attributes']) && !empty($product['attributes']))
						{
							foreach ($product['attributes'] as $attributes)
							{
							}
						}*/
					}

					if (is_array($sqlOrderProducts) && !empty($sqlOrderProducts))
					{
						os_db_perform(TABLE_ORDERS_PRODUCTS, $sqlOrderProducts, 'update', "orders_products_id  = '".(int)$product['orders_products_id']."'");
					}
				}
			}

			// Обновление Итого
			if (is_array($post['total']) && !empty($post['total']))
			{
				foreach ($post['total'] as $class => $values)
				{
					// если название не пустое
					if (!empty($values['title']))
					{
						$sqlTotal['orders_id'] = (int)$post['order_id'];
						$sqlTotal['title'] = os_db_prepare_input($values['title']);
						$sqlTotal['text'] = os_db_prepare_input($osPrice->Format($values['value'], true));
						$sqlTotal['value'] = os_db_prepare_input($values['value']);
						$sqlTotal['class'] = os_db_prepare_input($class);
						$sqlTotal['sort_order'] = os_db_prepare_input($values['sort_order']);

						// обновляем названия, если изменили язык заказа
						if ($order->info['language'] != $langDir)
						{
							$moduleFile = _MODULES.'order_total/'.$values['class'].'/'.$langDir.'.php';
							if (is_file($moduleFile))
							{
								require ($moduleFile);
								$name = str_replace('ot_', '', $values['class']);
								$sqlTotal['title'] = os_db_prepare_input(constant(MODULE_ORDER_TOTAL_.strtoupper($name)._TITLE));
							}
						}

						// обновляем название доставки, если она != уже установленной
						if ($order->info['shipping_class'] != $values['shipping_method'].'_'.$values['shipping_method'] && $class == 'ot_shipping')
						{
							$shipping_class = $values['shipping_method'].'_'.$values['shipping_method'];
							$shipping_text = $this->shipping->getName(array('lang' => $langDir, 'method' => $values['shipping_method']));
							$sqlTotal['title'] = os_db_prepare_input($shipping_text);

							// обновим сразу доставку в заказе
							if ($order->info['shipping_class'] != $shipping_class)
							{
								$sqlOrderShipping['shipping_method'] = os_db_prepare_input($shipping_text);
								$sqlOrderShipping['shipping_class'] = os_db_prepare_input($shipping_class);
								os_db_perform(TABLE_ORDERS, $sqlOrderShipping, 'update', "orders_id  = '".(int)$post['order_id']."'");
							}
						}

						// обновляем суммы, если изменили валюту
						if ($order->info['currency'] != $post['old_currencies_id'])
						{
							$osPrice = new osPrice($order->info['currency'], $order->info['status']);
							$nvalue = $osPrice->RemoveCurr($values['value']);
							$osPrice = new osPrice($curr['code'], $order->info['status']);
							$new_value = $osPrice->GetPrice('', false, '', '', $nvalue, '', $order->customer['ID']);
							$sqlTotal['text'] = os_db_prepare_input($osPrice->Format($new_value['price'], true));
							$sqlTotal['value'] = os_db_prepare_input($new_value['price']);
						}

						// Если в итого уже есть какой-то модуль, то обновляем его
						if (isset($values['orders_total_id']) && !empty($values['orders_total_id']))
						{
							os_db_perform(TABLE_ORDERS_TOTAL, $sqlTotal, 'update', "orders_total_id = '".(int)$values['orders_total_id']."'");// AND class = '".os_db_prepare_input($class)."'
						}
						// Если в итого нет модуля, то добавляем его
						else
						{
							os_db_perform(TABLE_ORDERS_TOTAL, $sqlTotal);
						}
					}

					// если выставлено удаление
					if ($values['total_delete'] == '1')
					{
						os_db_query("DELETE FROM ".TABLE_ORDERS_TOTAL." WHERE orders_total_id = '".(int)$values['orders_total_id']."'");
					}
				}
			}

			// Обновляем валюту заказа
			if ($order->info['currency'] != $post['old_currencies_id'])
			{
				$sqlOrders['currency'] = os_db_prepare_input($curr['code']);
				$sqlOrders['currency_value'] = os_db_prepare_input($curr['value']);
			}

			// Обновляем язык заказа
			if ($order->info['language'] != $langDir)
			{
				$sqlOrders['language'] = os_db_prepare_input($langDir);
			}

			// Обновляем метод оплаты
			if ($order->info['payment_class'] != $post['payment_method'])
			{
				$sqlOrders['payment_method'] = os_db_prepare_input($post['payment_method']);
				$sqlOrders['payment_class'] = os_db_prepare_input($post['payment_method']);
			}

			// Обновляем таблицу заказа
			if (is_array($sqlOrders) && !empty($sqlOrders))
			{
				os_db_perform(TABLE_ORDERS, $sqlOrders, 'update', "orders_id  = '".(int)$post['order_id']."'");
			}

			$data = array('msg' => 'Успешно обновлено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		
		return $data;
	}

	/**
	 * Удаление товара из заказа
	 */
	public function deleteProduct($post)
	{
		if (empty($post)) return false;

		$id = (is_array($post)) ? $post['id'] : $post;

		os_db_query("DELETE FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_products_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_products_id = '".(int)$id."'");
		os_db_query("DELETE FROM ".TABLE_ORDERS_PRODUCTS_DOWNLOAD." WHERE orders_products_id = '".(int)$id."'");
		
		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление атрибута у товара в заказе
	 */
	public function deleteAttributes($post)
	{
		if (empty($post)) return false;

		$id = (is_array($post)) ? $post['id'] : $post;

		os_db_query("DELETE FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_products_attributes_id = '".(int)$id."'");
		//os_db_query("DELETE FROM ".TABLE_ORDERS_PRODUCTS_DOWNLOAD." WHERE orders_products_id = '".(int)$id."'");

		// Пересчитываем цену товара
		if ($post['recalculate'] == '1')
		{

		}

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Обновление атрибутов товара в заказе
	 */
	public function editAttributes($post)
	{
		if (!is_array($post)) return false;

		// Сохраняем данные атрибутов
		if (is_array($post['attributes']))
		{
			// Если нужно пересчитать цены
			if ($post['recalculate'] == '1')
			{
				require (_CLASS.'price.php');
				$osPrice = new osPrice($post['currency'], isset($post['status']) ? $post['status'] : '');
			}

			foreach ($post['attributes'] as $id => $attributes)
			{
				$sqlAttributes = array(
					'products_options' => os_db_prepare_input($attributes['products_options']),
					'products_options_values' => os_db_prepare_input($attributes['products_options_values']),
					'options_values_price' => os_db_prepare_input($attributes['options_values_price']),
					'price_prefix' => os_db_prepare_input($attributes['prefix']),
					'attributes_model' => os_db_prepare_input($attributes['attributes_model'])
				);

				os_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sqlAttributes, 'update', "orders_products_attributes_id = '".(int)$id."'");

				// Если нужно пересчитать цены
				if ($post['recalculate'] == '1')
				{
					$products_query = os_db_query("select op.products_id, op.products_quantity, p.products_tax_class_id from ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS." p where op.orders_products_id = '".(int)$post['opID']."' and op.products_id = p.products_id");
					$products = os_db_fetch_array($products_query);

					$products_a_query = os_db_query("select options_values_price, price_prefix from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = '".(int)$post['opID']."'");
					while ($products_a = os_db_fetch_array($products_a_query))
					{
						$ov_price += $products_a['price_prefix'].$products_a['options_values_price'];
					}

					$products_old_price = $osPrice->GetPrice($products['products_id'], false, $products['products_quantity'], '', '', '', $post['ocID']);

					$options_values_price = ($ov_price.$attributes['prefix'].$attributes['options_values_price']);
					$products_price = ($products_old_price['price'] + $options_values_price);

					$price = $osPrice->GetPrice($products['products_id'], false, $products['products_quantity'], $products['products_tax_class_id'], $products_price, '', $post['ocID']);

					$final_price = $price['price'] * $products['products_quantity'];

					$sql_data_array = array(
						'products_price' => os_db_prepare_input($price['price']),
						'final_price' => os_db_prepare_input($final_price)
					);
					os_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', "orders_products_id = '".(int)$post['opID']."'");
				}
			}

			$data = array('msg' => 'Успешно обновлено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		
		return $data;
	}

	/**
	 * Добавление атрибута к товару в заказе
	 */
	public function addAttributeToProduct($array)
	{
		// костыль
		require (get_path('class_admin').'order.php');
		$order = new order($array['o_id']);
		require (_CLASS.'price.php');
		$osPrice = new osPrice($order->info['currency'], isset($order->info['status']) ? $order->info['status'] : '');
		// костыль

		$products_attributes_query = os_db_query("select options_id, options_values_id, options_values_price, price_prefix from ".TABLE_PRODUCTS_ATTRIBUTES." where products_attributes_id = '".(int)$array['add_attr']."'");
		$products_attributes = os_db_fetch_array($products_attributes_query);

		$products_options_query = os_db_query("select products_options_name from ".TABLE_PRODUCTS_OPTIONS." where products_options_id = '".(int)$products_attributes['options_id']."' and language_id = '".(int)$_SESSION['languages_id']."'");
		$products_options = os_db_fetch_array($products_options_query);

		$products_options_values_query = os_db_query("select products_options_values_name from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".(int)$products_attributes['options_values_id']."' and language_id = '".(int)$_SESSION['languages_id']."'");
		$products_options_values = os_db_fetch_array($products_options_values_query);

		$sql_data_array = array(
			'orders_id' => (int)$array['o_id'],
			'orders_products_id' => (int)$array['op_id'],
			'products_options' => os_db_prepare_input($products_options['products_options_name']),
			'products_options_values' => os_db_prepare_input($products_options_values['products_options_values_name']),
			'options_values_price' => os_db_prepare_input($products_attributes['options_values_price']),
			'price_prefix' => os_db_prepare_input($products_attributes['price_prefix']),
			'attributes_model' => os_db_prepare_input($products_attributes['attributes_model'])
		);
		os_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

		$products_query = os_db_query("select op.products_id, op.products_quantity, p.products_tax_class_id from ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS." p where op.orders_products_id = '".(int)$array['op_id']."' and op.products_id = p.products_id");
		$products = os_db_fetch_array($products_query);

		$products_a_query = os_db_query("select options_values_price, price_prefix from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = '".(int)$array['op_id']."'");
		while ($products_a = os_db_fetch_array($products_a_query))
		{
			$options_values_price += $products_a['price_prefix'].$products_a['options_values_price'];
		};

		if (DOWNLOAD_ENABLED == 'true')
		{
			$attributes = os_db_query("
			SELECT 
				popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount, pad.products_attributes_filename
			FROM 
				".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_OPTIONS_VALUES." poval, ".TABLE_PRODUCTS_ATTRIBUTES." pa
					LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad on pa.products_attributes_id=pad.products_attributes_id
			WHERE 
				pa.products_id = '".(int)$products['products_id']."' and 
				pa.options_id = '".(int)$products_attributes['options_id']."' and 
				pa.options_id = popt.products_options_id and 
				pa.options_values_id = '".(int)$products_attributes['options_values_id']."' and 
				pa.options_values_id = poval.products_options_values_id and 
				popt.language_id = '".$_SESSION['languages_id']."' and 
				poval.language_id = '".$_SESSION['languages_id']."'
			");

			$attributes_values = os_db_fetch_array($attributes);

			if (isset ($attributes_values['products_attributes_filename']) && os_not_null($attributes_values['products_attributes_filename']))
			{
				$sql_data_array = array (
					'orders_id' => (int)$array['o_id'],
					'orders_products_id' => (int)$array['op_id'],
					'orders_products_filename' => $attributes_values['products_attributes_filename'],
					'download_maxdays' => $attributes_values['products_attributes_maxdays'],
					'download_count' => $attributes_values['products_attributes_maxcount']
				);
				os_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
			}
		}

		$products_old_price = $osPrice->GetPrice($products['products_id'], false, $products['products_quantity'], '', '', '', $order->customer['ID']);
		$products_price = ($products_old_price['price'] + $options_values_price);
		$price = $osPrice->GetPrice($products['products_id'], false, $products['products_quantity'], $products['products_tax_class_id'], $products_price, '', $order->customer['ID']);
		$final_price = $price['price'] * $products['products_quantity'];

		$sql_data_array = array(
			'products_price' => os_db_prepare_input($price['price']),
			'final_price' => os_db_prepare_input($final_price)
		);
		os_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', "orders_products_id = '".(int)$array['op_id']."'");
	}

	/**
	 * Отправка СМС
	 */
	public function addSms($params)
	{
		$smsSetting = $this->sms->setting();
		if ($smsSetting['sms_status'] == 1)
		{
			$smsText = $params['sms_text'];
			$smsPhone = $params['sms_phone'];

			if ($smsPhone && $smsText && $params['order_id'])
			{
				$this->sms->send($smsText, $_POST['sms_phone']);

				$sql_data_array = array (
					'order_id' => (int)$params['order_id'],
					'note' => os_db_prepare_input($smsText),
					'phone' => os_db_prepare_input($smsPhone),
					'date_added' => 'now()',
				);
				os_db_perform(DB_PREFIX."sms_notes", $sql_data_array);

				$data = array('msg' => 'Успешно добавлено!', 'type' => 'ok');
			}
			else
				$data = array('msg' => 'Произошла ошибка!', 'type' => 'error');
		}

		return $data;
	}

	/**
	 * Удаление СМС
	 */
	public function deleteSms($params)
	{
		if (empty($params)) return false;

		$id = (is_array($params)) ? $params['id'] : $params;

		os_db_query("delete from ".DB_PREFIX."sms_notes where id = '".(int)$id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}
}