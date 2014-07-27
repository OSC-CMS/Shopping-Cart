<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class yandex_merchant extends CartET
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
	public $name = 'cart_yandex_id';

	// class constructor
	function yandex_merchant()
	{
		global $order;

		$this->code = 'yandex_merchant';
		$this->title = MODULE_PAYMENT_YANDEX_MERCHANT_TEXT_TITLE;
		$this->public_title = MODULE_PAYMENT_YANDEX_MERCHANT_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_YANDEX_MERCHANT_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_YANDEX_MERCHANT_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_YANDEX_MERCHANT_STATUS == 'True') ? true : false);
		$this->icon = 'icon.png';
		$this->icon_small = 'icon_small.png';

		if ((int)MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID > 0)
		{
			$this->order_status = MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID;
		}

		if (is_object($order))
			$this->update_status();

		if (MODULE_PAYMENT_YANDEX_MERCHANT_TEST == 'True')
			$this->form_action_url = 'https://demomoney.yandex.ru/eshop.xml';
		else
			$this->form_action_url = 'https://money.yandex.ru/eshop.xml';
	}

	function update_status()
	{
		return false;
	}

	function javascript_validation()
	{
		return false;
	}

	private function payment_types()
	{
		return array(
			'AC' => MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_AC,
			'PC' => MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_PC,
			'MC' => MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_MC,
			'GP' => MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_GP,
			'WM' => MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_WM,
			'SB' => MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE_SB,
		);
	}

	/**
	 * Выбор способа оплаты
	 */
	function selection()
	{
		if (os_not_null($this->icon)) $icon = os_image(http_path('payment').$this->code.'/'.$this->icon, $this->title);

		$payment_types = explode(',', MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE);

		$payment_types_select = array();
		foreach($this->payment_types() AS $key => $val)
		{
			if (in_array($key, $payment_types))
				$payment_types_select[] = array('id' => $key, 'text' => $val);
		}

		$selected = ($_SESSION['yamoney_payment']) ? $_SESSION['yamoney_payment'] : 'AC';

		return array
		(
			'id' => $this->code,
			'icon' => $icon,
			'module' => $this->title,
			'description' => $this->info,
			'fields' => array(
				array(
					'title' => MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_SELECT,
					'field' => os_draw_pull_down_menu('yandex_payment_type', $payment_types_select, $selected),
				)
			)
		);
	}

	function pre_confirmation_check()
	{
		return false;
	}

	/**
	 * Подтверждение заказа и его создание
	 */
	function confirmation()
	{
		global $order, $order_total_modules;

		$this->order->confirmation($this->name, $order, $order_total_modules);

		return array('title' => MODULE_PAYMENT_YANDEX_MERCHANT_TEXT_DESCRIPTION);
	}

	/**
	 * Формирование данны для запроса на form_action_url
	 */
	function process_button()
	{
		global $order, $osPrice;

		$OrderID = substr($_SESSION[$this->name], strpos($_SESSION[$this->name], '-')+1);
		$TotalAmount = number_format($osPrice->CalculateCurrEx($order->info['total'], 'RUR'), 2, '.', '');

		$_SESSION['yamoney_payment'] = $_POST['yandex_payment_type'];

		$payment_types = explode(',', MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE);
		if (in_array($_SESSION['yamoney_payment'], $payment_types))
			$payment_type = $_SESSION['yamoney_payment'];
		else
			$payment_type = 'AC';

		$process_button_string = os_draw_hidden_field('shopId', MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID).
		os_draw_hidden_field('scid', MODULE_PAYMENT_YANDEX_MERCHANT_SCID).
		os_draw_hidden_field('sum', $TotalAmount).
		os_draw_hidden_field('orderNumber', $OrderID).
		os_draw_hidden_field('shopSuccessURL', os_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL')).
		os_draw_hidden_field('shopFailURL', os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL')).
		os_draw_hidden_field('customerNumber', $_SESSION['customer_id']).
		os_draw_hidden_field('cps_email', $order->customer['email_address']).
		os_draw_hidden_field('cps_phone', $order->customer['telephone']).
		os_draw_hidden_field('cms_name', 'CartET').
		os_draw_hidden_field('paymentType', $payment_type);

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

		unset($_SESSION['sendto']);
		unset($_SESSION['billto']);
		unset($_SESSION['shipping']);
		unset($_SESSION['payment']);
		unset($_SESSION['comments']);
		unset($_SESSION['yamoney_payment']);

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
			$check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_YANDEX_MERCHANT_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_YANDEX_MERCHANT_ALLOWED', '', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_YANDEX_MERCHANT_STATUS', 'True', '6', '1', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID', '', '6', '2', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_YANDEX_MERCHANT_SCID', '', '6', '3', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_YANDEX_MERCHANT_KEY', '', '6', '4', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE', 'AC,PC,MC,GP,WM,SB', '6', '5', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_YANDEX_MERCHANT_TEST', 'True', '6', '6', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_YANDEX_MERCHANT_ZONE', '0', '6', '7', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_YANDEX_MERCHANT_SORT_ORDER', '1', '6', '8', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID', '0', '6', '9', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove()
	{
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys()
	{
		return array(
			'MODULE_PAYMENT_YANDEX_MERCHANT_STATUS',
			'MODULE_PAYMENT_YANDEX_MERCHANT_ALLOWED',
			'MODULE_PAYMENT_YANDEX_MERCHANT_SHOP_ID',
			'MODULE_PAYMENT_YANDEX_MERCHANT_SCID',
			'MODULE_PAYMENT_YANDEX_MERCHANT_KEY',
			'MODULE_PAYMENT_YANDEX_MERCHANT_PAYMENT_TYPE',
			'MODULE_PAYMENT_YANDEX_MERCHANT_TEST',
			'MODULE_PAYMENT_YANDEX_MERCHANT_ZONE',
			'MODULE_PAYMENT_YANDEX_MERCHANT_SORT_ORDER',
			'MODULE_PAYMENT_YANDEX_MERCHANT_ORDER_STATUS_ID'
		);
	}
}