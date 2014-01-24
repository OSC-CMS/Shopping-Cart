<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class ik extends CartET
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
	public $name = 'cart_interkassa_id';

	// class constructor
	function ik()
	{
		global $order;

		$this->code = 'ik';
		$this->title = MODULE_PAYMENT_IK_TEXT_TITLE;
		$this->public_title = MODULE_PAYMENT_IK_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_IK_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_IK_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_IK_STATUS == 'True') ? true : false);
		$this->icon = 'icon.png';
		$this->icon_small = 'icon_small.png';

		if ((int)MODULE_PAYMENT_IK_ORDER_STATUS_ID > 0)
		{
			$this->order_status = MODULE_PAYMENT_IK_ORDER_STATUS_ID;
		}

		if (is_object($order))
			$this->update_status();

		//$this->form_action_url = 'https://interkassa.com/lib/payment.php';
		$this->form_action_url = 'https://sci.interkassa.com/';
	}

	// Обновление статуса(?)
	function update_status()
	{
		global $order;

		if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_IK_ZONE > 0) )
		{
			$check_flag = false;
			$check_query = os_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_IK_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
		global $order, $order_total_modules;

		$this->order->confirmation($this->name, $order, $order_total_modules);

		return array('title' => MODULE_PAYMENT_IK_TEXT_DESCRIPTION);
	}

	/**
	 * Формирование данны для запроса на form_action_url
	 */
	function process_button()
	{
		global $order, $osPrice;

		$ikCurrency = (MODULE_PAYMENT_IK_CURRENCY == 'RUB') ? 'RUR' : MODULE_PAYMENT_IK_CURRENCY;

		$OrderID = substr($_SESSION[$this->name], strpos($_SESSION[$this->name], '-')+1);
		$TotalAmount = number_format($osPrice->CalculateCurrEx($order->info['total'], $ikCurrency), 2, '.', '');

		$result = array(
			'ik_am' => $TotalAmount, // Сумма платежа
			'ik_pm_no' => $OrderID, // Номер заказа
			'ik_desc' => 'Order-'.$OrderID, // Описание платежа
			'ik_cur' => MODULE_PAYMENT_IK_CURRENCY, // Валюта платежа
			'ik_co_id' => MODULE_PAYMENT_IK_CO_ID, // Идентификатор кассы
		);

		if (MODULE_PAYMENT_IK_TEST == 'True')
		{
			$result['ik_act'] = 'process';
			$result['ik_pw_via'] = 'test_interkassa_test_xts';
		}

		// Формируем подпись
		$result['ik_sign'] = $this->getSign($result);

		$process_button_string = '';
		foreach ($result as $k => $val)
		{
			$process_button_string .= os_draw_hidden_field($k, $val);
		}

		return $process_button_string;
	}

	// Формируем подпись
	private function getSign($aParams)
	{
		ksort ($aParams, SORT_STRING);
		array_push($aParams, MODULE_PAYMENT_IK_SECRET_KEY);
		$signString = implode(':', $aParams);
		$sign = base64_encode(md5($signString, true));
		return $sign;
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
			$check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_IK_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IK_ALLOWED', '', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_IK_STATUS', 'True', '6', '1', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IK_CO_ID', '', '6', '2', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IK_SECRET_KEY', '', '6', '3', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IK_CURRENCY', 'UAH', '6', '4', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_IK_ZONE', '0', '6', '5', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IK_SORT_ORDER', '1', '6', '6', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_IK_ORDER_STATUS_ID', '0', '6', '7', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove()
	{
		os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys()
	{
		return array(
			'MODULE_PAYMENT_IK_STATUS',
			'MODULE_PAYMENT_IK_ALLOWED',
			'MODULE_PAYMENT_IK_CO_ID',
			'MODULE_PAYMENT_IK_SECRET_KEY',
			'MODULE_PAYMENT_IK_CURRENCY',
			'MODULE_PAYMENT_IK_ZONE',
			'MODULE_PAYMENT_IK_SORT_ORDER',
			'MODULE_PAYMENT_IK_ORDER_STATUS_ID'
		);
	}
}
?>