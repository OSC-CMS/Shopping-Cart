<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

class liqpay extends CartET
{
	/**
	 * Системный идентификатор модуля
	 */
	public $code;

	/**
	 * Название модуля
	 */
	public $title;

	/**
	 * Описание модуля
	 */
	public $description;

	/**
	 * Статус модуля
	 */
	public $enabled;

	/**
	 * Сессионная переменная модуля
	 */
	public $name = 'cart_liqpay_id';

	// class constructor
	function liqpay()
	{
		global $order;

		$this->code = 'liqpay';
		$this->title = MODULE_PAYMENT_LIQPAY_TEXT_TITLE;
		$this->public_title = MODULE_PAYMENT_LIQPAY_TEXT_PUBLIC_TITLE;
		$this->description = MODULE_PAYMENT_LIQPAY_TEXT_ADMIN_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_LIQPAY_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_LIQPAY_STATUS == 'True') ? true : false);
		$this->icon = 'icon.png';
		$this->icon_small = 'icon_small.png';

		$this->form_action_url = 'https://liqpay.com/?do=clickNbuy';
	}

	// Обновление статуса(?)
	function update_status()
	{
		global $order;

		if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_LIQPAY_ZONE > 0) )
		{
			$check_flag = false;
			$check_query = os_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_LIQPAY_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
			while ($check = os_db_fetch_array($check_query))
			{
				if ($check['zone_id'] < 1)
				{
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->billing['zone_id'])
				{
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false)
			{
				$this->enabled = false;
			}
		}
	}

	// Возможность валидации поле использую JS.
	function javascript_validation()
	{
		return false;
	}

	// Выбор метода оплаты в списке
	function selection()
	{
		if (isset($_SESSION[$this->name]))
		{
			$order_id = substr($_SESSION[$this->name], strpos($_SESSION[$this->name], '-')+1);

			$check_query = os_db_query('select orders_id from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . (int)$order_id . '" limit 1');

			if (os_db_num_rows($check_query) < 1)
			{
				$this->order->deleteOrderById($order_id);

				unset($_SESSION[$this->name]);
			}
		}

		if (os_not_null($this->icon)) $icon = os_image(http_path('payment').$this->code.'/'.$this->icon, $this->title);

		return array(
			'id' => $this->code,
			'icon' => $icon,
			'module' => $this->public_title
		);

	}

	function pre_confirmation_check()
	{
		global $cartID, $cart;

		if (empty($_SESSION['cart']->cartID))
		{
			$cartID = $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();
		}

		if (!isset($_SESSION['cartID']))
		{
			$_SESSION['cartID'] = $cartID;
		}
	}

	// Подтверждение заказа и его создание
	function confirmation()
	{
		global $cartID, $cart_liqpay_id, $customer_id, $languages_id, $order, $order_total_modules;

		$this->order->confirmation($this->name, $order, $order_total_modules);

		return array('title' => MODULE_PAYMENT_LIQPAY_TEXT_DESCRIPTION);
	}

	/**
	 * Формирование данны для запроса на form_action_url
	 */
	function process_button()
	{
		global $customer_id, $order, $sendto, $osPrice, $currencies, $cart_liqpay_id, $shipping;

		$order_sum = $order->info['total'];

		$order_id = substr($_SESSION[$this->name], strpos($_SESSION[$this->name], '-')+1);
		$curr_check = os_db_query("select currency from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
		$curr = os_db_fetch_array($curr_check);

		$xml = '<?xml version="1.0" encoding="utf-8"?>
		<request>
		<version>1.2</version>
		<merchant_id>'.MODULE_PAYMENT_LIQPAY_ID.'</merchant_id>
		<result_url>'.os_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL').'</result_url>
		<server_url>'.os_href_link('liqpay.php', '', 'SSL').'</server_url>
		<order_id>'.$order_id.'</order_id>
		<amount>'.$order_sum.'</amount>
		<currency>'.$curr['currency'].'</currency>
		<description>'.$order_id.'</description>
		<default_phone></default_phone>
		<pay_way>card</pay_way>
		</request>';

		$merchant_pass = MODULE_PAYMENT_LIQPAY_SECRET_KEY; 

		$operation_xml = base64_encode($xml);
		$signature = base64_encode(sha1($merchant_pass.$xml.$merchant_pass, 1));        

		$process_button_string = os_draw_hidden_field('operation_xml', $operation_xml) . 
		os_draw_hidden_field('signature', $signature);

		return $process_button_string;
	}

	function before_process()
	{
		global $order;

		$order_id = substr($_SESSION[$this->name], strpos($_SESSION[$this->name], '-')+1);

		// Обновляем количество товаров
		for ($i=0, $n=sizeof($order->products); $i<$n; $i++)
		{
			$this->order->updateQuantity($order->products[$i]);
		}

		$this->order->beforeProcess($order_id, $order);

		$this->after_process();

		require_once(DIR_WS_INCLUDES . 'affiliate_checkout_process.php');

		$_SESSION['cart']->reset(true);

		// unregister session variables used during checkout
		unset($_SESSION['sendto']);
		unset($_SESSION['billto']);
		unset($_SESSION['shipping']);
		unset($_SESSION['payment']);
		unset($_SESSION['comments']);
		unset($_SESSION[$this->name]);

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
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_LIQPAY_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_LIQPAY_STATUS', 'True', '6', '1', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_LIQPAY_ALLOWED', '', '6', '2', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_LIQPAY_ID', '', '6', '3', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_LIQPAY_SORT_ORDER', '0', '6', '4', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_LIQPAY_ZONE', '0', '6', '5', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_LIQPAY_SECRET_KEY', '', '6', '6', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_LIQPAY_ORDER_STATUS_ID', '0', '6', '7', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove()
	{
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys()
	{
		return array(
			'MODULE_PAYMENT_LIQPAY_STATUS',
			'MODULE_PAYMENT_LIQPAY_ALLOWED',
			'MODULE_PAYMENT_LIQPAY_ID',
			'MODULE_PAYMENT_LIQPAY_SORT_ORDER',
			'MODULE_PAYMENT_LIQPAY_ZONE',
			'MODULE_PAYMENT_LIQPAY_SECRET_KEY',
			'MODULE_PAYMENT_LIQPAY_ORDER_STATUS_ID'
		);
	}
}
?>