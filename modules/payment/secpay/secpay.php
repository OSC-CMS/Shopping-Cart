<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/

class secpay {
	var $code, $title, $description, $enabled;

	function secpay() {
		global $order;

		$this->code = 'secpay';
		$this->title = MODULE_PAYMENT_SECPAY_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_SECPAY_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_SECPAY_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_SECPAY_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_SECPAY_TEXT_INFO;
		if ((int) MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID;
		}

		if (is_object($order))
			$this->update_status();

		$this->form_action_url = 'https://www.secpay.com/java-bin/ValCard';
	}

	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_SECPAY_ZONE > 0)) {
			$check_flag = false;
			$check_query = os_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_SECPAY_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
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

		switch (MODULE_PAYMENT_SECPAY_CURRENCY) {
			case 'Default Currency' :
				$sec_currency = DEFAULT_CURRENCY;
				break;
			case 'Any Currency' :
			default :
				$sec_currency = $_SESSION['currency'];
				break;
		}

		switch (MODULE_PAYMENT_SECPAY_TEST_STATUS) {
			case 'Always Fail' :
				$test_status = 'false';
				break;
			case 'Production' :
				$test_status = 'live';
				break;
			case 'Always Successful' :
			default :
				$test_status = 'true';
				break;
		}
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}
		$process_button_string = os_draw_hidden_field('merchant', MODULE_PAYMENT_SECPAY_MERCHANT_ID).os_draw_hidden_field('trans_id', STORE_NAME.date('Ymdhis')).os_draw_hidden_field('amount', round($osPrice->CalculateCurrEx($total, $sec_currency), $osPrice->get_decimal_places($sec_currency))).os_draw_hidden_field('bill_name', $order->billing['firstname'].' '.$order->billing['lastname']).os_draw_hidden_field('bill_addr_1', $order->billing['street_address']).os_draw_hidden_field('bill_addr_2', $order->billing['suburb']).os_draw_hidden_field('bill_city', $order->billing['city']).os_draw_hidden_field('bill_state', $order->billing['state']).os_draw_hidden_field('bill_post_code', $order->billing['postcode']).os_draw_hidden_field('bill_country', $order->billing['country']['title']).os_draw_hidden_field('bill_tel', $order->customer['telephone']).os_draw_hidden_field('bill_email', $order->customer['email_address']).os_draw_hidden_field('ship_name', $order->delivery['firstname'].' '.$order->delivery['lastname']).os_draw_hidden_field('ship_addr_1', $order->delivery['street_address']).os_draw_hidden_field('ship_addr_2', $order->delivery['suburb']).os_draw_hidden_field('ship_city', $order->delivery['city']).os_draw_hidden_field('ship_state', $order->delivery['state']).os_draw_hidden_field('ship_post_code', $order->delivery['postcode']).os_draw_hidden_field('ship_country', $order->delivery['country']['title']).os_draw_hidden_field('currency', $sec_currency).os_draw_hidden_field('callback', os_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', false).';'.os_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error='.$this->code, 'SSL', false)).os_draw_hidden_field(os_session_name(), os_session_id()).os_draw_hidden_field('options', 'test_status='.$test_status.',dups=false,cb_post=true,cb_flds='.os_session_name());

		return $process_button_string;
	}

	function before_process() {

		if ($_POST['valid'] == 'true') {
			if ($remote_host = getenv('REMOTE_HOST')) {
				if ($remote_host != 'secpay.com') {
					$remote_host = gethostbyaddr($remote_host);
				}
				if ($remote_host != 'secpay.com') {
					os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, os_session_name().'='.$_POST[os_session_name()].'&payment_error='.$this->code, 'SSL', false, false));
				}
			} else {
				os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, os_session_name().'='.$_POST[os_session_name()].'&payment_error='.$this->code, 'SSL', false, false));
			}
		}
	}

	function after_process() {
		global $insert_id;
		if ($this->order_status)
			os_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");

	}

	function get_error() {

		if (isset ($_GET['message']) && (strlen($_GET['message']) > 0)) {
			$error = stripslashes(urldecode($_GET['message']));
		} else {
			$error = MODULE_PAYMENT_SECPAY_TEXT_ERROR_MESSAGE;
		}

		return array ('title' => MODULE_PAYMENT_SECPAY_TEXT_ERROR, 'error' => $error);
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_SECPAY_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SECPAY_STATUS', 'True', '6', '1', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SECPAY_ALLOWED', '', '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SECPAY_MERCHANT_ID', 'secpay',  '6', '2', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SECPAY_CURRENCY', 'Any Currency',  '6', '3', 'os_cfg_select_option(array(\'Any Currency\', \'Default Currency\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SECPAY_TEST_STATUS', 'Always Successful','6', '4', 'os_cfg_select_option(array(\'Always Successful\', \'Always Fail\', \'Production\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SECPAY_SORT_ORDER', '0',  '6', '0', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SECPAY_ZONE', '0',  '6', '2', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID', '0',  '6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove() {
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys() {
		return array ('MODULE_PAYMENT_SECPAY_STATUS', 'MODULE_PAYMENT_SECPAY_ALLOWED', 'MODULE_PAYMENT_SECPAY_MERCHANT_ID', 'MODULE_PAYMENT_SECPAY_CURRENCY', 'MODULE_PAYMENT_SECPAY_TEST_STATUS', 'MODULE_PAYMENT_SECPAY_ZONE', 'MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID', 'MODULE_PAYMENT_SECPAY_SORT_ORDER');
	}
}
?>