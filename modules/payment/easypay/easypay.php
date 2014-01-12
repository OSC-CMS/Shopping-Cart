<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*	Copyright (c) 2012 VamShop
*---------------------------------------------------------
*/

class easypay extends CartET
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
	public $name = 'cart_easypay_id';

	// class constructor
	function easypay()
	{
		global $order;

		$this->code = 'easypay';
		$this->title = MODULE_PAYMENT_EASYPAY_TEXT_TITLE;
		$this->public_title = MODULE_PAYMENT_EASYPAY_TEXT_PUBLIC_TITLE;
		$this->description = MODULE_PAYMENT_EASYPAY_TEXT_ADMIN_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_EASYPAY_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_EASYPAY_STATUS == 'True') ? true : false);
		$this->icon = 'icon.png';
		$this->icon_small = 'icon_small.png';

		$this->form_action_url = 'https://ssl.easypay.by/weborder/';
	}

	// Обновление статуса(?)
	function update_status()
	{
		global $order;

		if (($this->enabled == true) && ((int)MODULE_PAYMENT_EASYPAY_ZONE > 0))
		{
			$check_flag = false;
			$check_query = os_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_EASYPAY_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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

			$check_query = os_db_query('select orders_id from '.TABLE_ORDERS_STATUS_HISTORY.' where orders_id = "'.(int)$order_id.'" limit 1');

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
			$_SESSION['cartID'] = $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();
		}

		if (!isset($_SESSION['cartID']))
		{
			$_SESSION['cartID'] = $_SESSION['cart']->generate_cart_id();
		}
	}

	// Подтверждение заказа и его создание
	function confirmation()
	{
		global $cartID, $customer_id, $languages_id, $order, $order_total_modules;

		$this->order->confirmation($this->name, $order, $order_total_modules);

		return array('title' => MODULE_PAYMENT_EASYPAY_TEXT_DESCRIPTION);
	}

	/**
	 * Формирование данны для запроса на form_action_url
	 */
	function process_button()
	{
		global $customer_id, $order, $sendto, $osPrice, $currencies, $shipping;

		#номер заказа
		$order_no = substr($_SESSION[$this->name], strpos($_SESSION[$this->name], '-')+1);

		#получаем стоимость заказа в белорусских рублях
		$sum = ceil($order->info["total"]);

		#подготовка формы для оплаты
		$process_button_string =
			os_draw_hidden_field("EP_MerNo",			MODULE_PAYMENT_EASYPAY_MERCHNO).
			os_draw_hidden_field("EP_OrderNo",			$order_no).
			os_draw_hidden_field("EP_Sum",				$sum).
			os_draw_hidden_field("EP_Expires",			5).
			os_draw_hidden_field("EP_Comment",			$order_no).
			os_draw_hidden_field("EP_OrderInfo",		$order_no).
			os_draw_hidden_field("EP_Hash",				md5(MODULE_PAYMENT_EASYPAY_MERCHNO.MODULE_PAYMENT_EASYPAY_WEBKEY.$order_no.$sum)).
			os_draw_hidden_field("EP_Success_URL",		os_href_link(FILENAME_CHECKOUT_PROCESS)).
			os_draw_hidden_field("EP_Cancel_URL",		os_href_link(FILENAME_CHECKOUT_CONFIRMATION)).
			os_draw_hidden_field("EP_Debug",			MODULE_PAYMENT_EASYPAY_DEBUG);

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

		$this->orders->beforeProcess($order_id, $order);

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
			$check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_EASYPAY_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_EASYPAY_STATUS', 'True', '6', '3', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_EASYPAY_ALLOWED', '', '6', '4', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_EASYPAY_MERCHNO', '', '6', '5', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_EASYPAY_SORT_ORDER', '0', '6', '7', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_EASYPAY_ZONE', '0', '6', '8', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_EASYPAY_WEBKEY', '', '6', '9', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_EASYPAY_DEBUG', '0', '6', '10', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_EASYPAY_ORDER_STATUS_ID', '0', '6', '11', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove()
	{
		os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys()
	{
		return array
		(
			'MODULE_PAYMENT_EASYPAY_STATUS',
			'MODULE_PAYMENT_EASYPAY_ALLOWED',
			'MODULE_PAYMENT_EASYPAY_MERCHNO',
			'MODULE_PAYMENT_EASYPAY_SORT_ORDER',
			'MODULE_PAYMENT_EASYPAY_ZONE',
			'MODULE_PAYMENT_EASYPAY_WEBKEY',
			'MODULE_PAYMENT_EASYPAY_DEBUG',
			'MODULE_PAYMENT_EASYPAY_ORDER_STATUS_ID'
		);
	}
}
?>