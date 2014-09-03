<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
*	(c) 2002-2003 osCommerce(2003/06/02); www.oscommerce.com 
*	(c) 2003	 nextcommerce (2003/08/18); www.nextcommerce.org
*	(c) 2004	 xt:Commerce (2003/08/18); xt-commerce.com
*	(c) 2010	 VamShop; vamshop.com
*
*---------------------------------------------------------
*/

class qiwi extends CartET
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
	public $name = 'cart_qiwi_id';

	// class constructor
	function qiwi()
	{
		global $order;

		$this->code = 'qiwi';
		$this->title = MODULE_PAYMENT_QIWI_TEXT_TITLE;
		$this->public_title = MODULE_PAYMENT_QIWI_TEXT_PUBLIC_TITLE;
		$this->description = MODULE_PAYMENT_QIWI_TEXT_ADMIN_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_QIWI_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_QIWI_STATUS == 'True') ? true : false);
		$this->icon = 'icon.png';
		$this->icon_small = 'icon_small.png';
	}

	// class methods
	function update_status()
	{
		global $order;

		if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_QIWI_ZONE > 0) )
		{
			$check_flag = false;
			$check_query = os_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_QIWI_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
			'module' => $this->title,
			'description'=>$this->info,
			'fields' => array(
				array(
					'title' => MODULE_PAYMENT_QIWI_NAME_TITLE,
					'field' => MODULE_PAYMENT_QIWI_NAME_DESC
				),
				array(
					'title' => MODULE_PAYMENT_QIWI_TELEPHONE,
					'field' => os_draw_input_field('qiwi_telephone',$order->customer['telephone']).MODULE_PAYMENT_QIWI_TELEPHONE_HELP,
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

	function confirmation()
	{
		global $order, $order_total_modules;

		$result = $this->order->confirmation($this->name, $order, $order_total_modules);

		if (is_array($result) && $result['insert_order'] == true)
		{
			// Выписываем qiwi счёт для покупателя
			require_once(dirname(__FILE__).'/class/nusoap.php');

			$client = new nusoap_client("https://mobw.ru/services/ishop", false); // создаем клиента для отправки запроса на QIWI
			$error = $client->getError();

			//if ( !empty($error) ) {
			// обрабатываем возможные ошибки и в случае их возникновения откатываем транзакцию в своей системе
			//echo -1;
			//exit();
			//}

			$client->useHTTPPersistentConnection();

			$order_id = substr($_SESSION[$this->name], strpos($_SESSION[$this->name], '-')+1);
			$OrderID = ($order_id) ? $order_id : (($this->request->get('order_id')) ? $this->request->get('order_id') : '');

			// Параметры для передачи данных о платеже
			$params = array(
				'login' => MODULE_PAYMENT_QIWI_ID, // login - Ваш ID в системе QIWI
				'password' => MODULE_PAYMENT_QIWI_SECRET_KEY, // password - Ваш пароль
				'user' => $this->request->get('qiwi_telephone'), // user - Телефон покупателя (10 символов, например 916820XXXX)
				'amount' => number_format($order->info['total'],0), // amount - Сумма платежа в рублях
				'comment' => $OrderID, // comment - Комментарий, который пользователь увидит в своем личном кабинете или платежном автомате
				'txn' => $OrderID, // txn - Наш внутренний уникальный номер транзакции
				'lifetime' => date("d.m.Y H:i:s", strtotime("+2 weeks")), // lifetime - Время жизни платежа до его автоматической отмены
				'alarm' => 1, // alarm - Оповещать ли клиента через СМС или звонком о выписанном счете
				'create' => 1 // create - 0 - только для зарегистрированных пользователей QIWI, 1 - для всех
			);

			// запрос:
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

			os_db_query("INSERT INTO ".TABLE_PERSONS." (orders_id, name, address) VALUES ('" . os_db_prepare_input((int)$OrderID) . "', '" . os_db_prepare_input($this->request->get('kvit_name')) . "', '" . os_db_prepare_input($this->request->get('qiwi_telephone')) ."')");
		}

		return array('title' => MODULE_PAYMENT_QIWI_TEXT_DESCRIPTION);
	}

	function process_button()
	{
		return false;
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

		$this->order->beforeProcess($order_id);

		$this->after_process();

		require_once(DIR_WS_INCLUDES . 'affiliate_checkout_process.php');

		$_SESSION['cart']->reset(true);

		unset($_SESSION['sendto']);
		unset($_SESSION['billto']);
		unset($_SESSION['shipping']);
		unset($_SESSION['payment']);
		unset($_SESSION['comments']);
		unset($_SESSION['last_order']);
		unset($_SESSION['tmp_oID']);
		unset($_SESSION[$this->name]);

		os_redirect(os_href_link(FILENAME_CHECKOUT_SUCCESS, 'order_id='.$order_id, 'SSL'));
	}

	function after_process() 
	{
		return false;
	}

	function check() 
	{
		if (!isset($this->_check)) 
		{
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_QIWI_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_QIWI_STATUS', 'True', '6', '1', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_QIWI_ALLOWED', '', '6', '2', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_QIWI_ID', '', '6', '3', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_QIWI_SORT_ORDER', '0', '6', '4', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_QIWI_ZONE', '0', '6', '5', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_QIWI_SECRET_KEY', '', '6', '6', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_QIWI_ORDER_STATUS_ID', '0', '6', '7', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove()
	{
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys()
	{
		return array(
			'MODULE_PAYMENT_QIWI_STATUS',
			'MODULE_PAYMENT_QIWI_ALLOWED',
			'MODULE_PAYMENT_QIWI_ID',
			'MODULE_PAYMENT_QIWI_SORT_ORDER',
			'MODULE_PAYMENT_QIWI_ZONE',
			'MODULE_PAYMENT_QIWI_SECRET_KEY',
			'MODULE_PAYMENT_QIWI_ORDER_STATUS_ID'
		);
	}
}
?>