<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class soglas extends CartET
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

	function soglas()
	{
		global $order,$osPrice;

		$this->code = 'soglas';
		$this->title = MODULE_PAYMENT_SOGLAS_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_SOGLAS_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_SOGLAS_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_SOGLAS_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_SOGLAS_TEXT_INFO;
		$this->cost;
		$this->icon = 'icon.png';
		$this->icon_small = 'icon_small.png';

		if ((int) MODULE_PAYMENT_SOGLAS_ORDER_STATUS_ID > 0)
		{
			$this->order_status = MODULE_PAYMENT_SOGLAS_ORDER_STATUS_ID;
		}

		if (is_object($order))
			$this->update_status();
	}

	function update_status()
	{
		global $order;
		if ($_SESSION['shipping']['id'] == 'selfpickup_selfpickup')
		{
			$this->enabled = false;
		}
		if (($this->enabled == true) && ((int) MODULE_PAYMENT_SOGLAS_ZONE > 0))
		{
			$check_flag = false;
			$check_query = os_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_SOGLAS_ZONE."' and zone_country_id = '".$order->delivery['country']['id']."' order by zone_id");
			while ($check = os_db_fetch_array($check_query))
			{
				if ($check['zone_id'] < 1)
				{
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->delivery['zone_id'])
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
		global $osPrice,$order;

		if (MODULE_ORDER_TOTAL_COD_FEE_STATUS == 'true')
		{
			$cod_country = false;

			//process installed shipping modules
			if ($_SESSION['shipping']['id'] == 'flat_flat') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_FLAT);
			if ($_SESSION['shipping']['id'] == 'item_item') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_ITEM);
			if ($_SESSION['shipping']['id'] == 'table_table') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_TABLE);
			if ($_SESSION['shipping']['id'] == 'zones_zones') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_ZONES);
			if ($_SESSION['shipping']['id'] == 'ap_ap') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_AP);
			if ($_SESSION['shipping']['id'] == 'dp_dp') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DP);
			if ($_SESSION['shipping']['id'] == 'chp_ECO') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_CHP);
			if ($_SESSION['shipping']['id'] == 'chp_PRI') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_CHP);
			if ($_SESSION['shipping']['id'] == 'chp_URG') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_CHP);
			if ($_SESSION['shipping']['id'] == 'chronopost_chronopost') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_CHRONOPOST);
			if ($_SESSION['shipping']['id'] == 'dhl_ECX') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DHL);
			if ($_SESSION['shipping']['id'] == 'dhl_DOX') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DHL);
			if ($_SESSION['shipping']['id'] == 'dhl_SDX') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DHL);
			if ($_SESSION['shipping']['id'] == 'dhl_MDX') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DHL);
			if ($_SESSION['shipping']['id'] == 'dhl_WPX') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DHL);
			if ($_SESSION['shipping']['id'] == 'ups_ups') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_UPS);
			if ($_SESSION['shipping']['id'] == 'upse_upse') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_UPSE);
			if ($_SESSION['shipping']['id'] == 'free_free') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_FREE);
			if ($_SESSION['shipping']['id'] == 'freeamount_freeamount') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_FREEAMOUNT_FREE);

			for ($i = 0; $i < count($cod_zones); $i++)
			{
				if ($cod_zones[$i] == $order->delivery['country']['iso_code_2'])
				{
					$cod_cost = $cod_zones[$i + 1];
					$cod_country = true;
					break;
				}
				elseif ($cod_zones[$i] == '00')
				{
					$cod_cost = $cod_zones[$i + 1];
					$cod_country = true;
					break;
				}
				else {}
				$i++;
			}
		}
		else {}

		if ($cod_country)
		{
			$cod_tax = os_get_tax_rate(MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
			$cod_tax_description = os_get_tax_description(MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
			if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1)
			{
				$cod_cost_value= os_add_tax($cod_cost, $cod_tax);
				$cod_cost= $osPrice->Format($cod_cost_value,true);
			}
			if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
			{
				$cod_cost_value=$cod_cost;
				$cod_cost= $osPrice->Format($cod_cost,true);
			}
			if (!$cod_cost_value)
			{
				$cod_cost_value=$cod_cost;
				$cod_cost= $osPrice->Format($cod_cost,true);
			}
			$this->cost = '+ '.$cod_cost;
		}

		if (os_not_null($this->icon)) $icon = os_image(http_path('payment').$this->code.'/'.$this->icon, $this->title);

		return array(
			'id' => $this->code,
			'icon' => $icon,
			'module' => $this->title,
			'description' => $this->info,
			'module_cost'=>$this->cost
		);
	}

	function pre_confirmation_check()
	{
		return false;
	}

	function confirmation()
	{
		return false;
	}

	function process_button()
	{
		return false;
	}

	function before_process()
	{
		return false;
	}

	function after_process()
	{
		global $insert_id;
		if ($this->order_status)
			os_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
	}

	function get_error()
	{
		return false;
	}

	function check()
	{
		if (!isset ($this->_check))
		{
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_SOGLAS_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SOGLAS_STATUS', 'True',  '6', '1', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOGLAS_ALLOWED', '', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SOGLAS_ZONE', '0', '6', '2', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOGLAS_SORT_ORDER', '0',  '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SOGLAS_ORDER_STATUS_ID', '0','6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove()
	{
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys()
	{
		return array(
			'MODULE_PAYMENT_SOGLAS_STATUS',
			'MODULE_PAYMENT_SOGLAS_ALLOWED',
			'MODULE_PAYMENT_SOGLAS_ZONE',
			'MODULE_PAYMENT_SOGLAS_ORDER_STATUS_ID',
			'MODULE_PAYMENT_SOGLAS_SORT_ORDER'
		);
	}
}
?>