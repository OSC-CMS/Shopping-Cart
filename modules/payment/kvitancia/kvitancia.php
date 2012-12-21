<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

class kvitancia extends OscCms
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

	function kvitancia()
	{
		$this->code = 'kvitancia';
		$this->title = MODULE_PAYMENT_KVITANCIA_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_KVITANCIA_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_KVITANCIA_SORT_ORDER;
		$this->info = MODULE_PAYMENT_KVITANCIA_TEXT_INFO;
		$this->enabled = ((MODULE_PAYMENT_KVITANCIA_STATUS == 'True') ? true : false);
		$this->icon = 'icon.png';
		$this->icon_small = 'icon_small.png';

		if ((int) MODULE_PAYMENT_KVITANCIA_ORDER_STATUS_ID > 0)
		{
			$this->order_status = MODULE_PAYMENT_KVITANCIA_ORDER_STATUS_ID;
		}
	}

	function update_status()
	{
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_KVITANCIA_ZONE > 0))
		{
			$check_flag = false;
			$check_query = os_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_KVITANCIA_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
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

	function javascript_validation()
	{
		return false;
	}

	function selection()
	{
		global $order;

		if (os_not_null($this->icon)) $icon = os_image(http_path('payment').$this->code.'/'.$this->icon, $this->title);

		return array(
			'id' => $this->code,
			'icon' => $icon,
			'module' => $this->title,
			'description'=>$this->info,
			'fields' => array(
				array(
					'title' => MODULE_PAYMENT_KVITANCIA_NAME_TITLE,
					'field' => MODULE_PAYMENT_KVITANCIA_NAME_DESC
				),
				array(
					'title' => MODULE_PAYMENT_KVITANCIA_NAME,
					'field' => os_draw_input_field(
						'kvit_name', $order->customer['firstname'] . ' ' . $order->customer['lastname']
					)
				),
				array(
					'title' => MODULE_PAYMENT_KVITANCIA_ADDRESS,
					'field' => os_draw_input_field('kvit_address').MODULE_PAYMENT_KVITANCIA_ADDRESS_HELP
				),
			)
		);
	}

	function pre_confirmation_check()
	{
		$this->name = os_db_prepare_input($_POST['kvit_name']);
		$this->address = os_db_prepare_input($_POST['kvit_address']);
	}

	function confirmation()
	{
		global $_POST;

		$confirmation = array(
			'title' => $this->title.': '.$this->check,
			'fields' => array(
				array(
					'title' => MODULE_PAYMENT_KVITANCIA_TEXT_DESCRIPTION
				)
			),
			'description' => $this->info
		);

		return $confirmation;
	}

	function process_button()
	{
		$process_button_string = 
			os_draw_hidden_field('kvit_name', $this->name) .
			os_draw_hidden_field('kvit_address', $this->address);

		return $process_button_string;
	}

	function before_process()
	{
		$this->pre_confirmation_check();
		return false;
	}

	function after_process()
	{
		global $insert_id, $name, $address, $checkout_form_action, $checkout_form_submit;
		os_db_query("INSERT INTO ".TABLE_PERSONS." (orders_id, name, address) VALUES ('" . os_db_input($insert_id) . "', '" . os_db_input($this->name) . "', '" . os_db_input($this->address) ."')");

		if ($this->order_status)
		os_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
	}

	function output_error()
	{
		return false;
	}

	function check()
	{
		if (!isset ($this->check))
		{
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_KVITANCIA_STATUS'");
			$this->check = os_db_num_rows($check_query);
		}
		return $this->check;
	}

	function install()
	{
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_KVITANCIA_ALLOWED', '', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_KVITANCIA_STATUS', 'True', '6', '3', 'os_cfg_select_option(array(\'True\', \'False\'), ', now());");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_KVITANCIA_1', '---',  '6', '1', now());");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_KVITANCIA_2', '---', '6', '1', now());");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_KVITANCIA_3', '---',  '6', '1', now());");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_KVITANCIA_4', '---',  '6', '1', now());");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_KVITANCIA_5', '---',  '6', '1', now());");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_KVITANCIA_6', '---',  '6', '1', now());");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_KVITANCIA_7', '---',  '6', '1', now());");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_KVITANCIA_8', 'Заказ номер',  '6', '1', now());");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_KVITANCIA_SORT_ORDER', '0',  '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_KVITANCIA_ZONE', '0',  '6', '2', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_KVITANCIA_ORDER_STATUS_ID', '0', '6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove()
	{
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys()
	{
		return array(
			'MODULE_PAYMENT_KVITANCIA_STATUS',
			'MODULE_PAYMENT_KVITANCIA_ALLOWED',
			'MODULE_PAYMENT_KVITANCIA_1',
			'MODULE_PAYMENT_KVITANCIA_2',
			'MODULE_PAYMENT_KVITANCIA_3',
			'MODULE_PAYMENT_KVITANCIA_4',
			'MODULE_PAYMENT_KVITANCIA_5',
			'MODULE_PAYMENT_KVITANCIA_6',
			'MODULE_PAYMENT_KVITANCIA_7',
			'MODULE_PAYMENT_KVITANCIA_8',
			'MODULE_PAYMENT_KVITANCIA_SORT_ORDER',
			'MODULE_PAYMENT_KVITANCIA_ZONE',
			'MODULE_PAYMENT_KVITANCIA_ORDER_STATUS_ID'
		);
	}
}
?>