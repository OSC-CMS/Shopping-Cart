<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

class ot_gv {
	var $title, $output;

	function ot_gv() {
		global $osPrice;
		$this->code = 'ot_gv';
		$this->title = MODULE_ORDER_TOTAL_GV_TITLE;
		$this->header = MODULE_ORDER_TOTAL_GV_HEADER;
		$this->description = MODULE_ORDER_TOTAL_GV_DESCRIPTION;
		$this->user_prompt = MODULE_ORDER_TOTAL_GV_USER_PROMPT;
		$this->enabled = MODULE_ORDER_TOTAL_GV_STATUS;
		$this->sort_order = MODULE_ORDER_TOTAL_GV_SORT_ORDER;
		$this->include_shipping = MODULE_ORDER_TOTAL_GV_INC_SHIPPING;
		$this->include_tax = MODULE_ORDER_TOTAL_GV_INC_TAX;
		$this->calculate_tax = MODULE_ORDER_TOTAL_GV_CALC_TAX;
		$this->credit_tax = MODULE_ORDER_TOTAL_GV_CREDIT_TAX;
		$this->tax_class = MODULE_ORDER_TOTAL_GV_TAX_CLASS;
		$this->show_redeem_box = MODULE_ORDER_TOTAL_GV_REDEEM_BOX;
		$this->credit_class = true;
		$this->checkbox = $this->user_prompt.'<input type="checkbox" onClick="submitFunction()" name="'.'c'.$this->code.'">';
		$this->output = array ();

	}

	function process() {
		global $order, $osPrice;
		if (isset ($_SESSION['cot_gv']) && $_SESSION['cot_gv'] == true) {
			$order_total = $this->get_order_total();

			$od_amount = $this->calculate_credit($order_total);
			if ($this->calculate_tax != "None") {
				$tod_amount = $this->calculate_tax_deduction($order_total, $od_amount, $this->calculate_tax);
				$od_amount = $this->calculate_credit($order_total);
			}

			$this->deduction = $od_amount;

			$order->info['total'] = $order->info['total'] - $od_amount;

			if ($od_amount > 0) {
				$this->output[] = array ('title' => $this->title.':', 'text' => '<b><font color="ff0000">-'.$osPrice->Format($od_amount, true).'</font></b>', 'value' => $osPrice->Format($od_amount, false));
			}
		
		}
	}

	function selection_test() {

		if ($this->user_has_gv_account($_SESSION['customer_id'])) {
			return true;
		} else {
			return false;
		}
	}

	function pre_confirmation_check($order_total) {
		global $order;
		$od_amount = 0; 
		if (isset ($_SESSION['cot_gv']) && $_SESSION['cot_gv'] == true) {

			if ($this->include_tax == 'false') {
				$order_total = $order_total - $order->info['tax'];
			}
			if ($this->include_shipping == 'false') {
				$order_total = $order_total - $order->info['shipping_cost'];
			}
			$od_amount = $this->calculate_credit($order_total);

			if ($this->calculate_tax != "None") {
				$tod_amount = $this->calculate_tax_deduction($order_total, $od_amount, $this->calculate_tax);
				$od_amount = $this->calculate_credit($order_total) + $tod_amount;
			}
		}
		return $od_amount;
	}

	function use_credit_amount() {
		$_SESSION['cot_gv'] = false;
		if ($this->selection_test()) {
			$output_string .= '    <td nowrap align="right" class="main">';
			$output_string .= '<b>'.$this->checkbox.'</b>'.'</td>'."\n";
		}
		return $output_string;
	}

	function update_credit_account($i) {
		global $order, $insert_id, $REMOTE_ADDR;
		if (preg_match('/^GIFT/', addslashes($order->products[$i]['model']))) {
			$gv_order_amount = ($order->products[$i]['final_price']);
			if ($this->credit_tax == 'true')
				$gv_order_amount = $gv_order_amount * (100 + $order->products[$i]['tax']) / 100;
			$gv_order_amount = $gv_order_amount * 100 / 100;
			if (MODULE_ORDER_TOTAL_GV_QUEUE == 'false') {
				$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$_SESSION['customer_id']."'");
				$customer_gv = false;
				$total_gv_amount = 0;
				if ($gv_result = os_db_fetch_array($gv_query)) {
					$total_gv_amount = $gv_result['amount'];
					$customer_gv = true;
				}
				$total_gv_amount = $total_gv_amount + $gv_order_amount;
				if ($customer_gv) {
					$gv_update = os_db_query("update ".TABLE_COUPON_GV_CUSTOMER." set amount = '".$total_gv_amount."' where customer_id = '".$_SESSION['customer_id']."'");
				} else {
					$gv_insert = os_db_query("insert into ".TABLE_COUPON_GV_CUSTOMER." (customer_id, amount) values ('".$_SESSION['customer_id']."', '".$total_gv_amount."')");
				}
			} else {
				$gv_insert = os_db_query("insert into ".TABLE_COUPON_GV_QUEUE." (customer_id, order_id, amount, date_created, ipaddr) values ('".$_SESSION['customer_id']."', '".$insert_id."', '".$gv_order_amount."', NOW(), '".$REMOTE_ADDR."')");
			}
		}
	}

	function credit_selection() {
		global $currencies;
		$selection_string = '';
		$gv_query = os_db_query("select coupon_id from ".TABLE_COUPONS." where coupon_type = 'G' and coupon_active='Y'");

		return $selection_string;
	}

	function apply_credit() {
		global $order, $coupon_no,$osPrice, $insert_id;
		if (isset ($_SESSION['cot_gv']) && $_SESSION['cot_gv'] == true) {
			$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$_SESSION['customer_id']."'");
			$gv_result = os_db_fetch_array($gv_query);
			$gv_payment_amount = $this->deduction;
			$gv_amount = $gv_result['amount'] - $osPrice->RemoveCurr($gv_payment_amount);
			$gv_amount = str_replace(",", ".", $gv_amount);
			$gv_update = os_db_query("update ".TABLE_COUPON_GV_CUSTOMER." set amount = '".$gv_amount."' where customer_id = '".$_SESSION['customer_id']."'");
			
				if ($gv_amount >= $order->info['total'] && MODULE_ORDER_TOTAL_GV_ORDER_STATUS_ID != 0) {
					os_db_query("update " . TABLE_ORDERS  . " set orders_status = " . MODULE_ORDER_TOTAL_GV_ORDER_STATUS_ID . " where orders_id = '" . $insert_id . "'");
				}			
			
		}
		return $gv_payment_amount;
	}

	function collect_posts() {
		global $osPrice, $coupon_no, $REMOTE_ADDR;
		if ($_POST['gv_redeem_code']) {
			$gv_query = os_db_query("select coupon_id, coupon_type, coupon_amount from ".TABLE_COUPONS." where coupon_code = '".$_POST['gv_redeem_code']."'");
			$gv_result = os_db_fetch_array($gv_query);
			if (os_db_num_rows($gv_query) != 0) {
				$redeem_query = os_db_query("select * from ".TABLE_COUPON_REDEEM_TRACK." where coupon_id = '".$gv_result['coupon_id']."'");
				if ((os_db_num_rows($redeem_query) != 0) && ($gv_result['coupon_type'] == 'G')) {
					os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='.urlencode(ERROR_NO_INVALID_REDEEM_GV), 'SSL'));
				}
			}
			if ($gv_result['coupon_type'] == 'G') {
				$gv_amount = $gv_result['coupon_amount'];
				$gv_amount_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$_SESSION['customer_id']."'");
				$customer_gv = false;
				$total_gv_amount = $gv_amount;
				if ($gv_amount_result = os_db_fetch_array($gv_amount_query)) {
					$total_gv_amount = $gv_amount_result['amount'] + $gv_amount;
					$customer_gv = true;
				}
				$gv_update = os_db_query("update ".TABLE_COUPONS." set coupon_active = 'N' where coupon_id = '".$gv_result['coupon_id']."'");
				$gv_redeem = os_db_query("insert into  ".TABLE_COUPON_REDEEM_TRACK." (coupon_id, customer_id, redeem_date, redeem_ip) values ('".$gv_result['coupon_id']."', '".$SESSION['customer_id']."', now(),'".$REMOTE_ADDR."')");
				if ($customer_gv) {
					$gv_update = os_db_query("update ".TABLE_COUPON_GV_CUSTOMER." set amount = '".$total_gv_amount."' where customer_id = '".$_SESSION['customer_id']."'");
				} else {
					$gv_insert = os_db_query("insert into ".TABLE_COUPON_GV_CUSTOMER." (customer_id, amount) values ('".$_SESSION['customer_id']."', '".$total_gv_amount."')");
				}
			}
		}
		if ($_POST['submit_redeem_x'] && $gv_result['coupon_type'] == 'G')
			os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='.urlencode(ERROR_NO_REDEEM_CODE), 'SSL'));
	}

	function calculate_credit($amount) {
		global $order;
		$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$_SESSION['customer_id']."'");
		$gv_result = os_db_fetch_array($gv_query);
		$gv_payment_amount = $gv_result['amount'];
		$gv_amount = $gv_payment_amount;
		$save_total_cost = $amount;
		$full_cost = $save_total_cost - $gv_payment_amount;
		if ($full_cost <= 0) {
			$full_cost = 0;
			$gv_payment_amount = $save_total_cost;
		}
		return $gv_payment_amount;
	}

	function calculate_tax_deduction($amount, $od_amount, $method) {
		global $order;
		switch ($method) {
			case 'Standard' :
				$ratio1 = number_format($od_amount / $amount, 2);
				$tod_amount = 0;
				reset($order->info['tax_groups']);
				while (list ($key, $value) = each($order->info['tax_groups'])) {
					$tax_rate = os_get_tax_rate_from_desc($key);
					$total_net += $tax_rate * $order->info['tax_groups'][$key];
				}
				if ($od_amount > $total_net)
					$od_amount = $total_net;
				reset($order->info['tax_groups']);
				while (list ($key, $value) = each($order->info['tax_groups'])) {
					$tax_rate = os_get_tax_rate_from_desc($key);
					$net = $tax_rate * $order->info['tax_groups'][$key];
					if ($net > 0) {
						$god_amount = $order->info['tax_groups'][$key] * $ratio1;
						$tod_amount += $god_amount;
						$order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount;
					}
				}
				$order->info['tax'] -= $tod_amount;
				$order->info['total'] -= $tod_amount;
				break;
			case 'Credit Note' :
				$tax_rate = os_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$tax_desc = os_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$tod_amount = $this->deduction / (100 + $tax_rate) * $tax_rate;
				$order->info['tax_groups'][$tax_desc] -= $tod_amount;
				break;
			default :
				}
		return $tod_amount;
	}

	function user_has_gv_account($c_id) {
		$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$c_id."'");
		if ($gv_result = os_db_fetch_array($gv_query)) {
			if ($gv_result['amount'] > 0) {
				return true;
			}
		}
		return false;
	}

	function get_order_total() {
		global $order;
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 0)
			$order_total = $order->info['total'];
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
			$order_total = $order->info['tax'] + $order->info['total'];
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0)
			$order_total = $order->info['total'];
		if ($this->include_tax == 'false')
			$order_total = $order_total - $order->info['tax'];
		if ($this->include_shipping == 'false')
			$order_total = $order_total - $order->info['shipping_cost'];
		return $order_total;
	}

	function check() {
		if (!isset ($this->check)) {
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_ORDER_TOTAL_GV_STATUS'");
			$this->check = os_db_num_rows($check_query);
		}

		return $this->check;
	}

	function keys() {
		return array ('MODULE_ORDER_TOTAL_GV_STATUS', 'MODULE_ORDER_TOTAL_GV_SORT_ORDER', 'MODULE_ORDER_TOTAL_GV_QUEUE', 'MODULE_ORDER_TOTAL_GV_INC_SHIPPING', 'MODULE_ORDER_TOTAL_GV_INC_TAX', 'MODULE_ORDER_TOTAL_GV_CALC_TAX', 'MODULE_ORDER_TOTAL_GV_TAX_CLASS', 'MODULE_ORDER_TOTAL_GV_CREDIT_TAX', 'MODULE_ORDER_TOTAL_GV_ORDER_STATUS_ID');
	}

	function install() {
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('', 'MODULE_ORDER_TOTAL_GV_STATUS', 'true', '6', '1','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('', 'MODULE_ORDER_TOTAL_GV_SORT_ORDER', '80', '6', '2', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('', 'MODULE_ORDER_TOTAL_GV_QUEUE', 'true', '6', '3','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('', 'MODULE_ORDER_TOTAL_GV_INC_SHIPPING', 'true', '6', '5', 'os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('', 'MODULE_ORDER_TOTAL_GV_INC_TAX', 'true', '6', '6','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('', 'MODULE_ORDER_TOTAL_GV_CALC_TAX', 'None', '6', '7','os_cfg_select_option(array(\'None\', \'Standard\', \'Credit Note\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('', 'MODULE_ORDER_TOTAL_GV_TAX_CLASS', '0', '6', '0', 'os_get_tax_class_title', 'os_cfg_pull_down_tax_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('', 'MODULE_ORDER_TOTAL_GV_CREDIT_TAX', 'false', '6', '8','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
   	os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('', 'MODULE_ORDER_TOTAL_GV_ORDER_STATUS_ID', '0', '6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
	}

	function remove() {
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}
}
?>