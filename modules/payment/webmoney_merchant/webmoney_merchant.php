<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class webmoney_merchant extends CartET
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
	public $name = 'cart_webmoney_id';

	// class constructor
	function webmoney_merchant()
	{
		global $order;

		$this->code = 'webmoney_merchant';
		$this->title = MODULE_PAYMENT_WEBMONEY_MERCHANT_TEXT_TITLE;
		$this->public_title = MODULE_PAYMENT_WEBMONEY_MERCHANT_TEXT_PUBLIC_TITLE;
		$this->description = MODULE_PAYMENT_WEBMONEY_MERCHANT_TEXT_ADMIN_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_WEBMONEY_MERCHANT_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_WEBMONEY_MERCHANT_STATUS == 'True') ? true : false);
		$this->icon = 'icon.png';
		$this->icon_small = 'icon_small.png';

		// URL куда идем после нажатия Подтвердить
		$this->form_action_url = 'https://merchant.webmoney.ru/lmi/payment.asp';

		if ((int)MODULE_PAYMENT_WEBMONEY_MERCHANT_ORDER_STATUS_ID > 0)
		{
			$this->order_status = MODULE_PAYMENT_WEBMONEY_MERCHANT_ORDER_STATUS_ID;
		}
	}

	// Обновление статуса(?)
	function update_status()
	{
		global $order;

		if (($this->enabled == true) && ((int)MODULE_PAYMENT_WEBMONEY_MERCHANT_ZONE > 0))
		{
			$check_flag = false;
			$check_query = os_db_query("SELECT zone_id from ".TABLE_ZONES_TO_GEO_ZONES." WHERE geo_zone_id = '".MODULE_PAYMENT_WEBMONEY_MERCHANT_ZONE."' AND zone_country_id = '".$order->billing['country']['id']."' ORDER BY zone_id");
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

			$check_query = os_db_query("SELECT orders_id FROM ".TABLE_ORDERS_STATUS_HISTORY." WHERE orders_id = '".(int)$order_id."' LIMIT 1");

			if (os_db_num_rows($check_query) < 1)
			{
				$this->order->deleteOrderById($order_id);

				unset($_SESSION[$this->name]);
			}
		}

		if (os_not_null($this->icon)) $icon = os_image(http_path('payment').$this->code.'/'.$this->icon, $this->title);

		return array
		(
			'id' => $this->code,
			'icon' => $icon,
			'module' => $this->public_title,
			'fields' => array(
				array(
					'title' => MODULE_PAYMENT_WEBMONEYMERCHANT_TEXT_TYPE,
					'field' => os_draw_pull_down_menu('wm', array(
						array('id' => 'wmr', 'text' => MODULE_PAYMENT_WEBMONEYMERCHANT_TEXT_WMR), 
						array('id' => 'wmz', 'text' => MODULE_PAYMENT_WEBMONEYMERCHANT_TEXT_WMZ),
						array('id' => 'wmb', 'text' => MODULE_PAYMENT_WEBMONEYMERCHANT_TEXT_WMB),
						array('id' => 'wmu', 'text' => MODULE_PAYMENT_WEBMONEYMERCHANT_TEXT_WMU),
						array('id' => 'uah', 'text' => MODULE_PAYMENT_WEBMONEYMERCHANT_TEXT_UAH)
					), 'wmr')
				)
			)
		);
	}

	function pre_confirmation_check()
	{
		global $cartID;

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
		global $order, $order_total_modules;

		$this->order->confirmation($this->name, $order, $order_total_modules);

		return array('title' => MODULE_PAYMENT_WEBMONEY_MERCHANT_TEXT_DESCRIPTION);
	}

	/**
	 * Формирование данны для запроса на form_action_url
	 */
	function process_button()
	{
		global $order, $osPrice;

		$order_id = substr($_SESSION[$this->name], strpos($_SESSION[$this->name], '-')+1);

		$process_button_string = '';

		if ($_SESSION['wm'] == 'wmr')
		{
			$purse = MODULE_PAYMENT_WEBMONEY_MERCHANT_WMR;
			$order_sum = $order->info['total'];
		}
		else
		{
			$purse = MODULE_PAYMENT_WEBMONEY_MERCHANT_WMZ;
			$order_sum = number_format($osPrice->CalculateCurrEx($order->info['total'], 'USD'),2);
		}

		$process_button_string = 
			os_draw_hidden_field('LMI_PAYMENT_NO', $order_id).
			os_draw_hidden_field('LMI_PAYEE_PURSE', $purse).
			os_draw_hidden_field('LMI_PAYMENT_DESC', $order_id).
			os_draw_hidden_field('LMI_PAYMENT_AMOUNT', $order_sum).
			os_draw_hidden_field('LMI_SIM_MODE', '0');

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
			$check_query = os_db_query("SELECT configuration_value FROM ".TABLE_CONFIGURATION." WHERE configuration_key = 'MODULE_PAYMENT_WEBMONEY_MERCHANT_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_STATUS', 'True', '6', '3', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_ALLOWED', '', '6', '4', now())");
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_ID', '', '6', '5', now())");
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_WMZ', '', '6', '6', now())");
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_WMR', '', '6', '7', now())");
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_SORT_ORDER', '0', '6', '8', now())");
		//wmu
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_WMU', '', '6', '12', now())");
		//wmb
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_WMB', '', '6', '13', now())");
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_UAH', '', '6', '14', now())");
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_ZONE', '0', '6', '9', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_SECRET_KEY', '', '6', '10', now())");
		os_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_WEBMONEY_MERCHANT_ORDER_STATUS_ID', '0', '6', '11', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove()
	{
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys()
	{
		return array
		(
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_STATUS',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_ALLOWED',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_ID',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_WMZ',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_WMR',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_WMU',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_WMB',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_UAH',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_SORT_ORDER',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_ZONE',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_SECRET_KEY',
			'MODULE_PAYMENT_WEBMONEY_MERCHANT_ORDER_STATUS_ID'
		);
	}
}
?>