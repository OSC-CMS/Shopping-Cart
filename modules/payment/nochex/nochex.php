<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

class nochex {
	var $code, $title, $description, $enabled;

	function nochex() {
		global $order;

		$this->code = 'nochex';
		$this->title = MODULE_PAYMENT_NOCHEX_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_NOCHEX_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_NOCHEX_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_NOCHEX_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_NOCHEX_TEXT_INFO;
		if ((int) MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID;
		}

		if (is_object($order))
			$this->update_status();

		$this->form_action_url = 'https://www.nochex.com/nochex.dll/checkout';
	}

	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOCHEX_ZONE > 0)) {
			$check_flag = false;
			$check_query = os_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_NOCHEX_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
			while ($check = os_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->billing['zone_id']) {
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
		return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return false;
	}

	function process_button() {
		global $order, $osPrice;
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}
		$process_button_string = os_draw_hidden_field('cmd', '_xclick').os_draw_hidden_field('email', MODULE_PAYMENT_NOCHEX_ID).os_draw_hidden_field('amount', round($osPrice->CalculateCurrEx($total, 'GBP'), $osPrice->get_decimal_places('GBP'))).os_draw_hidden_field('ordernumber', $_SESSION['customer_id'].'-'.date('Ymdhis')).os_draw_hidden_field('returnurl', os_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL')).os_draw_hidden_field('cancel_return', os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));

		return $process_button_string;
	}

	function before_process() {
		return false;
	}

	function after_process() {
		global $insert_id;
		if ($this->order_status)
			os_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");

	}

	function output_error() {
		return false;
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOCHEX_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOCHEX_STATUS', 'True', '6', '3', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOCHEX_ALLOWED', '', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOCHEX_ID', 'you@yourbuisness.com', '6', '4', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOCHEX_SORT_ORDER', '0', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_NOCHEX_ZONE', '0',  '6', '2', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID', '0', '6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove() {
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys() {
		return array ('MODULE_PAYMENT_NOCHEX_STATUS', 'MODULE_PAYMENT_NOCHEX_ALLOWED', 'MODULE_PAYMENT_NOCHEX_ID', 'MODULE_PAYMENT_NOCHEX_ZONE', 'MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOCHEX_SORT_ORDER');
	}
}
?>