<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.1
#####################################
*/
/*
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(2003/06/02); www.oscommerce.com 
  (c) 2003	 nextcommerce (2003/08/18); www.nextcommerce.org
  (c) 2004	 xt:Commerce (2003/08/18); xt-commerce.com
  (c) 2010	 VamShop; vamshop.com
*/
  
  class qiwi extends db {
    var $code, $title, $description, $enabled;

// class constructor
    function qiwi() {
      global $order;

      $this->code = 'qiwi';
      $this->title = MODULE_PAYMENT_QIWI_TEXT_TITLE;
      $this->public_title = MODULE_PAYMENT_QIWI_TEXT_PUBLIC_TITLE;
      $this->description = MODULE_PAYMENT_QIWI_TEXT_ADMIN_DESCRIPTION;
      $this->icon = 'logo_qiwi.png';
      $this->icon_small = 'qiwi.png';
      $this->sort_order = MODULE_PAYMENT_QIWI_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_QIWI_STATUS == 'True') ? true : false);
		
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_QIWI_ZONE > 0) ) {
        $check_flag = false;
        $check_query = $this->query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_QIWI_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = $this->fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {

      if (isset($_SESSION['cart_qiwi_id'])) {
        $order_id = substr($_SESSION['cart_qiwi_id'], strpos($_SESSION['cart_qiwi_id'], '-')+1);

        $check_query = $this->query('select orders_id from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . (int)$order_id . '" limit 1');

        if ($this->num_rows($check_query) < 1) {
          $this->query('delete from ' . TABLE_ORDERS . ' where orders_id = "' . (int)$order_id . '"');
          $this->query('delete from ' . TABLE_ORDERS_TOTAL . ' where orders_id = "' . (int)$order_id . '"');
          $this->query('delete from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . (int)$order_id . '"');
          $this->query('delete from ' . TABLE_ORDERS_PRODUCTS . ' where orders_id = "' . (int)$order_id . '"');
          $this->query('delete from ' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ' where orders_id = "' . (int)$order_id . '"');
          $this->query('delete from ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' where orders_id = "' . (int)$order_id . '"');

          unset($_SESSION['cart_qiwi_id']);
        }
      }

      if (os_not_null($this->icon)) $icon = os_image(http_path('payment').$this->code.'/'.$this->icon, $this->title);

      return array('id' => $this->code,
                         'module' => $this->title,
               		    'icon' => $icon,
                         'description'=>$this->info,
      	                 'fields' => array(array('title' => MODULE_PAYMENT_QIWI_NAME_TITLE,
      	                                         'field' => MODULE_PAYMENT_QIWI_NAME_DESC),
      	                                   array('title' => MODULE_PAYMENT_QIWI_TELEPHONE,
      	                                         'field' => os_draw_input_field('qiwi_telephone',$order->customer['telephone']) . MODULE_PAYMENT_QIWI_TELEPHONE_HELP,
      	                                   )));

    }

    function pre_confirmation_check() {
      global $cartID, $cart;

      if (empty($_SESSION['cart']->cartID)) {
        $cartID = $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();
      }

      if (!isset($_SESSION['cartID'])) {
        $_SESSION['cartID'] = $cartID;
      }
    }

    function confirmation() {
      global $cartID, $cart_qiwi_id, $customer_id, $languages_id, $order, $order_total_modules;

      if (isset($_SESSION['cartID'])) {
        $insert_order = false;

        if (isset($_SESSION['cart_qiwi_id'])) {
          $order_id = substr($_SESSION['cart_qiwi_id'], strpos($_SESSION['cart_qiwi_id'], '-')+1);
          $curr_check = $this->query("select currency from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
          $curr = $this->fetch_array($curr_check);

          if ( ($curr['currency'] != $order->info['currency']) || ($cartID != substr($cart_qiwi_id, 0, strlen($cartID))) ) {
            $check_query = $this->query('select orders_id from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . (int)$order_id . '" limit 1');

            if ($this->num_rows($check_query) < 1) {
              $this->query('delete from ' . TABLE_ORDERS . ' where orders_id = "' . (int)$order_id . '"');
              $this->query('delete from ' . TABLE_ORDERS_TOTAL . ' where orders_id = "' . (int)$order_id . '"');
              $this->query('delete from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . (int)$order_id . '"');
              $this->query('delete from ' . TABLE_ORDERS_PRODUCTS . ' where orders_id = "' . (int)$order_id . '"');
              $this->query('delete from ' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ' where orders_id = "' . (int)$order_id . '"');
              $this->query('delete from ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' where orders_id = "' . (int)$order_id . '"');
            }

            $insert_order = true;
          }
        } else {
          $insert_order = true;
        }

        if ($insert_order == true) {
          $order_totals = array();
          if (is_array($order_total_modules->modules)) {
            reset($order_total_modules->modules);
            while (list(, $value) = each($order_total_modules->modules)) {
              $class = substr($value, 0, strrpos($value, '.'));
              if ($GLOBALS[$class]->enabled) {
                for ($i=0, $n=sizeof($GLOBALS[$class]->output); $i<$n; $i++) {
                  if (os_not_null($GLOBALS[$class]->output[$i]['title']) && os_not_null($GLOBALS[$class]->output[$i]['text'])) {
                    $order_totals[] = array('code' => $GLOBALS[$class]->code,
                                            'title' => $GLOBALS[$class]->output[$i]['title'],
                                            'text' => $GLOBALS[$class]->output[$i]['text'],
                                            'value' => $GLOBALS[$class]->output[$i]['value'],
                                            'sort_order' => $GLOBALS[$class]->sort_order);
                  }
                }
              }
            }
          }

if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
	$discount = $_SESSION['customers_status']['customers_status_ot_discount'];
} else {
	$discount = '0.00';
}

if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
	$customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {
	$customers_ip = $_SERVER["REMOTE_ADDR"];
}

          $sql_data_array = array('customers_id' => $_SESSION['customer_id'],
                                  'customers_name' => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
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
                                  'delivery_name' => $order->delivery['firstname'] . ' ' . $order->delivery['lastname'],
                                  'delivery_company' => $order->delivery['company'],
                                  'delivery_street_address' => $order->delivery['street_address'],
                                  'delivery_suburb' => $order->delivery['suburb'],
                                  'delivery_city' => $order->delivery['city'],
                                  'delivery_postcode' => $order->delivery['postcode'],
                                  'delivery_state' => $order->delivery['state'],
                                  'delivery_country' => $order->delivery['country']['title'],
                                  'delivery_address_format_id' => $order->delivery['format_id'],
                                  'billing_name' => $order->billing['firstname'] . ' ' . $order->billing['lastname'],
                                  'billing_company' => $order->billing['company'],
                                  'billing_street_address' => $order->billing['street_address'],
                                  'billing_suburb' => $order->billing['suburb'],
                                  'billing_city' => $order->billing['city'],
                                  'billing_postcode' => $order->billing['postcode'],
                                  'billing_state' => $order->billing['state'],
                                  'billing_country' => $order->billing['country']['title'],
                                  'billing_address_format_id' => $order->billing['format_id'],
                                  'payment_method' => $order->info['payment_method'],
                                  'payment_class' => $order->info['payment_class'],
                                  'shipping_method' => $order->info['shipping_method'],
                                  'shipping_class' => $order->info['shipping_class'],
                                  'language' => $_SESSION['language'],
                                  'customers_ip' => $customers_ip,
                                  'orig_reference' => $order->customer['orig_reference'],
                                  'login_reference' => $order->customer['login_reference'],
                                  'cc_type' => $order->info['cc_type'],
                                  'cc_owner' => $order->info['cc_owner'],
                                  'cc_number' => $order->info['cc_number'],
                                  'cc_expires' => $order->info['cc_expires'],
                                  'date_purchased' => 'now()',
                                  'orders_status' => $order->info['order_status'],
                                  'currency' => $order->info['currency'],
                                  'currency_value' => $order->info['currency_value']);

          $this->perform(TABLE_ORDERS, $sql_data_array);

          $insert_id = $this->insert_id();

          for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
            $sql_data_array = array('orders_id' => $insert_id,
                                    'title' => $order_totals[$i]['title'],
                                    'text' => $order_totals[$i]['text'],
                                    'value' => $order_totals[$i]['value'],
                                    'class' => $order_totals[$i]['code'],
                                    'sort_order' => $order_totals[$i]['sort_order']);

            $this->perform(TABLE_ORDERS_TOTAL, $sql_data_array);
          }

          for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
            $sql_data_array = array('orders_id' => $insert_id,
                                    'products_id' => os_get_prid($order->products[$i]['id']),
                                    'products_model' => $order->products[$i]['model'],
                                    'products_name' => $order->products[$i]['name'],
                                    'products_price' => $order->products[$i]['price'],
                                    'final_price' => $order->products[$i]['final_price'],
                                    'products_tax' => $order->products[$i]['tax'],
                                    'products_quantity' => $order->products[$i]['qty']);

            $this->perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);

            $order_products_id = $this->insert_id();

            $attributes_exist = '0';
            if (isset($order->products[$i]['attributes'])) {
              $attributes_exist = '1';
              for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
                if (DOWNLOAD_ENABLED == 'true') {
                  $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename
                                       from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                       left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                       on pa.products_attributes_id=pad.products_attributes_id
                                       where pa.products_id = '" . $order->products[$i]['id'] . "'
                                       and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                       and pa.options_id = popt.products_options_id
                                       and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                       and pa.options_values_id = poval.products_options_values_id
                                       and popt.language_id = '" . $_SESSION['languages_id'] . "'
                                       and poval.language_id = '" . $_SESSION['languages_id'] . "'";
                  $attributes = $this->query($attributes_query);
                } else {
                  $attributes = $this->query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $order->products[$i]['id'] . "' and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $_SESSION['languages_id'] . "' and poval.language_id = '" . $_SESSION['languages_id'] . "'");
                }

			// update attribute stock
			$this->query("UPDATE ".TABLE_PRODUCTS_ATTRIBUTES." set
						                               attributes_stock=attributes_stock - '".$order->products[$i]['qty']."'
						                               where
						                               products_id='".$order->products[$i]['id']."'
						                               and options_values_id='".$order->products[$i]['attributes'][$j]['value_id']."'
						                               and options_id='".$order->products[$i]['attributes'][$j]['option_id']."'
						                               ");

                $attributes_values = $this->fetch_array($attributes);

                $sql_data_array = array('orders_id' => $insert_id,
                                        'orders_products_id' => $order_products_id,
                                        'products_options' => $attributes_values['products_options_name'],
                                        'products_options_values' => $attributes_values['products_options_values_name'],
                                        'options_values_price' => $attributes_values['options_values_price'],
                                        'price_prefix' => $attributes_values['price_prefix']);

                $this->perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

                if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && os_not_null($attributes_values['products_attributes_filename'])) {
                  $sql_data_array = array('orders_id' => $insert_id,
                                          'orders_products_id' => $order_products_id,
                                          'orders_products_filename' => $attributes_values['products_attributes_filename'],
                                          'download_maxdays' => $attributes_values['products_attributes_maxdays'],
                                          'download_count' => $attributes_values['products_attributes_maxcount']);

                  $this->perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
                }
              }
            }
          }

          $cart_qiwi_id = $cartID . '-' . $insert_id;
          $_SESSION['cart_qiwi_id'] = $cart_qiwi_id;
        }

// Выписываем qiwi счёт для покупателя

        if ($insert_order == true) {
        	
        require_once(dirname(__FILE__).'/class/nusoap.php');

			$client = new nusoap_client("https://mobw.ru/services/ishop", false); // создаем клиента для отправки запроса на QIWI
			$error = $client->getError();
			
			//if ( !empty($error) ) {
			// обрабатываем возможные ошибки и в случае их возникновения откатываем транзакцию в своей системе
			//echo -1;
			//exit();
			//}
			
			$client->useHTTPPersistentConnection();
			
			// Параметры для передачи данных о платеже:
			// login - Ваш ID в системе QIWI
			// password - Ваш пароль
			// user - Телефон покупателя (10 символов, например 916820XXXX)
			// amount - Сумма платежа в рублях
			// comment - Комментарий, который пользователь увидит в своем личном кабинете или платежном автомате
			// txn - Наш внутренний уникальный номер транзакции
			// lifetime - Время жизни платежа до его автоматической отмены
			// alarm - Оповещать ли клиента через СМС или звонком о выписанном счете
			// create - 0 - только для зарегистрированных пользователей QIWI, 1 - для всех
			$params = array(
			'login' => MODULE_PAYMENT_QIWI_ID,
			'password' => MODULE_PAYMENT_QIWI_SECRET_KEY,
			'user' => $_POST['qiwi_telephone'],
			'amount' => number_format($order->info['total'],0),
			'comment' => substr($cart_qiwi_id, strpos($cart_qiwi_id, '-')+1),
			'txn' => substr($cart_qiwi_id, strpos($cart_qiwi_id, '-')+1),
			'lifetime' => date("d.m.Y H:i:s", strtotime("+2 weeks")),
			'alarm' => 1,
			'create' => 1
			);
			
			// собственно запрос:
			$result = $client->call('createBill', $params, "http://server.ishop.mw.ru/");
			
			//if ($client->fault) {
			//echo -1;
			//exit();
			//} else {
			//$err = $client->getError();
			//if ($err) {
			//echo -1;
			//exit();
			//} else {
			//echo $result;
			//exit();
			//}
			//}

	      $this->query("INSERT INTO ".TABLE_PERSONS." (orders_id, name, address) VALUES ('" . $this->prepare_input((int)substr($cart_qiwi_id, strpos($cart_qiwi_id, '-')+1)) . "', '" . $this->prepare_input($_POST['kvit_name']) . "', '" . $this->prepare_input($_POST['qiwi_telephone']) ."')");

        }

      }

      return array('title' => MODULE_PAYMENT_QIWI_TEXT_DESCRIPTION);
    }

	function process_button() {
		return false;
	}
	
    function before_process() {
      global $customer_id, $order, $osPrice, $order_totals, $sendto, $billto, $languages_id, $payment, $currencies, $cart, $cart_qiwi_id;
      global $$payment;

      $order_id = substr($_SESSION['cart_qiwi_id'], strpos($_SESSION['cart_qiwi_id'], '-')+1);

// initialized for the email confirmation
      $products_ordered = '';
      $subtotal = 0;
      $total_tax = 0;

      for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
// Stock Update - Joao Correia
        if (STOCK_LIMITED == 'true') {
          if (DOWNLOAD_ENABLED == 'true') {
            $stock_query_raw = "SELECT products_quantity, pad.products_attributes_filename
                                FROM " . TABLE_PRODUCTS . " p
                                LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                ON p.products_id=pa.products_id
                                LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                ON pa.products_attributes_id=pad.products_attributes_id
                                WHERE p.products_id = '" . os_get_prid($order->products[$i]['id']) . "'";
// Will work with only one option for downloadable products
// otherwise, we have to build the query dynamically with a loop
            $products_attributes = $order->products[$i]['attributes'];
            if (is_array($products_attributes)) {
              $stock_query_raw .= " AND pa.options_id = '" . $products_attributes[0]['option_id'] . "' AND pa.options_values_id = '" . $products_attributes[0]['value_id'] . "'";
            }
            $stock_query = $this->query($stock_query_raw);
          } else {
            $stock_query = $this->query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . os_get_prid($order->products[$i]['id']) . "'");
          }
          if ($this->num_rows($stock_query) > 0) {
            $stock_values = $this->fetch_array($stock_query);
// do not decrement quantities if products_attributes_filename exists
            if ((DOWNLOAD_ENABLED != 'true') || (!$stock_values['products_attributes_filename'])) {
              $stock_left = $stock_values['products_quantity'] - $order->products[$i]['qty'];
            } else {
              $stock_left = $stock_values['products_quantity'];
            }
            $this->query("update " . TABLE_PRODUCTS . " set products_quantity = '" . $stock_left . "' where products_id = '" . os_get_prid($order->products[$i]['id']) . "'");
            if ( ($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false') ) {
              $this->query("update " . TABLE_PRODUCTS . " set products_status = '0' where products_id = '" . os_get_prid($order->products[$i]['id']) . "'");
            }
          }
        }

// Update products_ordered (for bestsellers list)
        $this->query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $order->products[$i]['qty']) . " where products_id = '" . os_get_prid($order->products[$i]['id']) . "'");

//------insert customer choosen option to order--------
        $attributes_exist = '0';
        $products_ordered_attributes = '';
        if (isset($order->products[$i]['attributes'])) {
          $attributes_exist = '1';
          for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
            if (DOWNLOAD_ENABLED == 'true') {
              $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename
                                   from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                   left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                   on pa.products_attributes_id=pad.products_attributes_id
                                   where pa.products_id = '" . $order->products[$i]['id'] . "'
                                   and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                   and pa.options_id = popt.products_options_id
                                   and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                   and pa.options_values_id = poval.products_options_values_id
                                   and popt.language_id = '" . $_SESSION['languages_id'] . "'
                                   and poval.language_id = '" . $_SESSION['languages_id'] . "'";
              $attributes = $this->query($attributes_query);
            } else {
              $attributes = $this->query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $order->products[$i]['id'] . "' and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $_SESSION['languages_id'] . "' and poval.language_id = '" . $_SESSION['languages_id'] . "'");
            }
            $attributes_values = $this->fetch_array($attributes);

            $products_ordered_attributes .= "\n\t" . $attributes_values['products_options_name'] . ' ' . $attributes_values['products_options_values_name'];
          }
        }
//------insert customer choosen option eof ----
        $total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
        $total_tax += os_calculate_tax($total_products_price, $products_tax) * $order->products[$i]['qty'];
        $total_cost += $total_products_price;

        $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ') = ' . $osPrice->Format($order->products[$i]['final_price'], true) . $products_ordered_attributes . "\n";
      }

$osTemplate = new osTemplate;

	$osTemplate->assign('address_label_customer', os_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
	$osTemplate->assign('address_label_shipping', os_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
	if ($_SESSION['credit_covers'] != '1') {
		$osTemplate->assign('address_label_payment', os_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
	}
	$osTemplate->assign('csID', $order->customer['csID']);

  $it=0;
	$semextrfields = osDBquery("select * from " . TABLE_EXTRA_FIELDS . " where fields_required_email = '1'");
	while($dataexfes = $this->fetch_array($semextrfields,true)) {
	$cusextrfields = osDBquery("select * from " . TABLE_CUSTOMERS_TO_EXTRA_FIELDS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "' and fields_id = '" . $dataexfes['fields_id'] . "'");
	$rescusextrfields = $this->fetch_array($cusextrfields,true);

	$extrfieldsinf = osDBquery("select fields_name from " . TABLE_EXTRA_FIELDS_INFO . " where fields_id = '" . $dataexfes['fields_id'] . "' and languages_id = '" . $_SESSION['languages_id'] . "'");

	$extrfieldsres = $this->fetch_array($extrfieldsinf,true);
	$extra_fields .= $extrfieldsres['fields_name'] . ' : ' .
	$rescusextrfields['value'] . "\n";
	$osTemplate->assign('customer_extra_fields', $extra_fields);
  }
	
	$order_total = $order->getTotalData($order_id);
		$osTemplate->assign('order_data', $order->getOrderData($order_id));
		$osTemplate->assign('order_total', $order_total['data']);

	// assign language to template for caching
	$osTemplate->assign('language', $_SESSION['language']);
	$osTemplate->assign('tpl_path', http_path('themes').CURRENT_TEMPLATE.'/');
	$osTemplate->assign('logo_path', http_path('themes').CURRENT_TEMPLATE.'/img/');
	$osTemplate->assign('oID', $order_id);
	
	if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
		include (dirname(__FILE__).'/'.$_SESSION['language'].'.php');
		$payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
	}
	$osTemplate->assign('PAYMENT_METHOD', $payment_method);
	if ($order->info['shipping_method'] != '') {
		$shipping_method = $order->info['shipping_method'];
	}
	$osTemplate->assign('SHIPPING_METHOD', $shipping_method);
	$osTemplate->assign('DATE', os_date_long($order->info['date_purchased']));

	$osTemplate->assign('NAME', $order->customer['firstname'] . ' ' . $order->customer['lastname']);
	$osTemplate->assign('COMMENTS', $order->info['comments']);
	$osTemplate->assign('EMAIL', $order->customer['email_address']);
	$osTemplate->assign('PHONE',$order->customer['telephone']);

	// dont allow cache
	$osTemplate->caching = false;

	$html_mail = $osTemplate->fetch(_MEDIA.'mail/'.$_SESSION['language'].'/order_mail.html');
	$txt_mail = $osTemplate->fetch(_MEDIA.'mail/'.$_SESSION['language'].'/order_mail.txt');

	// create subject
	$order_subject = str_replace('{$nr}', $order_id, EMAIL_BILLING_SUBJECT_ORDER);
	$order_subject = str_replace('{$date}', strftime(DATE_FORMAT_LONG), $order_subject);
	$order_subject = str_replace('{$lastname}', $order->customer['lastname'], $order_subject);
	$order_subject = str_replace('{$firstname}', $order->customer['firstname'], $order_subject);

	// send mail to admin
	os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, EMAIL_BILLING_ADDRESS, STORE_NAME, EMAIL_BILLING_FORWARDING_STRING, $order->customer['email_address'], $order->customer['firstname'], '', '', $order_subject, $html_mail, $txt_mail);

	// send mail to customer
	os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $order->customer['email_address'], $order->customer['firstname'].' '.$order->customer['lastname'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $order_subject, $html_mail, $txt_mail);

// load the after_process function from the payment modules
      $this->after_process();


      $_SESSION['cart']->reset(true);

// unregister session variables used during checkout
      unset($_SESSION['sendto']);
      unset($_SESSION['billto']);
      unset($_SESSION['shipping']);
      unset($_SESSION['payment']);
      unset($_SESSION['comments']);

      unset($_SESSION['cart_qiwi_id']);

      os_redirect(os_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
    }

	function after_process() 
	{
      return false;
	}

    function output_error() 
	{
      return false;
    }

    function check() 
	{
      if (!isset($this->_check)) 
	  {
        $check_query = $this->query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_QIWI_STATUS'");
        $this->_check = $this->num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {

      $this->query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_QIWI_STATUS', 'True', '6', '1', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $this->query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_QIWI_ALLOWED', '', '6', '2', now())");
      $this->query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_QIWI_ID', '', '6', '3', now())");
      $this->query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_QIWI_SORT_ORDER', '0', '6', '4', now())");
      $this->query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_QIWI_ZONE', '0', '6', '5', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
      $this->query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_QIWI_SECRET_KEY', '', '6', '6', now())");
      $this->query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_QIWI_ORDER_STATUS_ID', '0', '6', '7', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
    }

    function remove() {
      $this->query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_QIWI_STATUS', 'MODULE_PAYMENT_QIWI_ALLOWED', 'MODULE_PAYMENT_QIWI_ID', 'MODULE_PAYMENT_QIWI_SORT_ORDER', 'MODULE_PAYMENT_QIWI_ZONE', 'MODULE_PAYMENT_QIWI_SECRET_KEY', 'MODULE_PAYMENT_QIWI_ORDER_STATUS_ID');
    }

  }
?>