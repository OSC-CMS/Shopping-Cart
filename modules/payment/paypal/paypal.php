<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

class paypal {
	var $code, $title, $description, $enabled;

	var $icon = 'icon.gif';
	
	function paypal() {
		global $order;

		$this->code = 'paypal';
		$this->title = MODULE_PAYMENT_PAYPAL_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_PAYPAL_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_PAYPAL_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_PAYPAL_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_PAYPAL_TEXT_INFO;
		$this->tmpOrders = true;
		$this->tmpStatus = MODULE_PAYMENT_PAYPAL_TMP_STATUS_ID;
		if ((int) MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID;
		}

		if (is_object($order))
			$this->update_status();

		$this->form_action_url = 'https://www.paypal.com/cgi-bin/webscr';
	}

	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_PAYPAL_ZONE > 0)) {
			$check_flag = false;
			$check_query = os_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_PAYPAL_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
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
		return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info,'icon' =>  os_image(HTTP_SERVER.DIR_WS_CATALOG.'modules/payment/'.__CLASS__.'/'.$this->icon, $this->title));
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return false;
	}
	
	function process_button() {
		return false;
	}

	function before_process() {
		return false;
	}
	
	function payment_action() {
		global $order, $osPrice;

		if (MODULE_PAYMENT_PAYPAL_CURRENCY == 'Selected Currency') {
			$my_currency = $_SESSION['currency'];
		} else {
			$my_currency = substr(MODULE_PAYMENT_PAYPAL_CURRENCY, 5);
		}
		if (!in_array($my_currency, array ('CAD', 'EUR', 'GBP', 'JPY', 'USD'))) {
			$my_currency = 'EUR';
		}

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}
		if ($_SESSION['currency'] == $my_currency) {
			$amount = round($total, $osPrice->get_decimal_places($my_currency));
			$shipping = round($order->info['shipping_cost'], $osPrice->get_decimal_places($my_currency));
		} else {
			$amount = round($osPrice->CalculateCurrEx($total, $my_currency), $osPrice->get_decimal_places($my_currency));
			$shipping = round($osPrice->CalculateCurrEx($order->info['shipping_cost'], $my_currency), $osPrice->get_decimal_places($my_currency));
		}
		
		$dataString = 'cmd=_xclick&business='.MODULE_PAYMENT_PAYPAL_ID.'&item_name='.STORE_NAME.'-OID:'.$_SESSION['tmp_oID'].'&amount='. ($amount - $shipping).'&shipping='.$shipping.'&currency_code='.$my_currency.'&return='.os_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL').'&cancel_return='.os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');
		
		if (MODULE_PAYMENT_PAYPAL_USE_CURL == 'True') {
			$url = $this->form_action_url;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString."&");
			curl_exec($ch);
			curl_close($ch);
		}
		else {
			os_redirect($this->form_action_url.'?'.$dataString);
		}

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
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_PAYPAL_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PAYPAL_STATUS', 'True', '6', '3', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PAYPAL_ALLOWED', '', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PAYPAL_ID', 'you@yourbusiness.com',  '6', '4', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PAYPAL_CURRENCY', 'Selected Currency',  '6', '6', 'os_cfg_select_option(array(\'Selected Currency\',\'Only USD\',\'Only CAD\',\'Only EUR\',\'Only GBP\',\'Only JPY\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PAYPAL_SORT_ORDER', '0', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_PAYPAL_ZONE', '0', '6', '2', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID', '0',  '6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_PAYPAL_TMP_STATUS_ID', '0',  '6', '8', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PAYPAL_USE_CURL', 'True', '6', '9', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
	}

	function remove() {
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys() {
		return array ('MODULE_PAYMENT_PAYPAL_STATUS', 'MODULE_PAYMENT_PAYPAL_ALLOWED', 'MODULE_PAYMENT_PAYPAL_ID', 'MODULE_PAYMENT_PAYPAL_CURRENCY', 'MODULE_PAYMENT_PAYPAL_ZONE', 'MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID', 'MODULE_PAYMENT_PAYPAL_SORT_ORDER','MODULE_PAYMENT_PAYPAL_USE_CURL','MODULE_PAYMENT_PAYPAL_TMP_STATUS_ID');
	}
}
?>