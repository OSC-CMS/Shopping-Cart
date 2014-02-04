<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class ot_discountshipping
{
	var $title, $output;

	function ot_discountshipping()
	{
		$this->code = 'ot_discountshipping';
		$this->title = MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_TITLE;
		$this->description = MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_DESCRIPTION;
		$this->enabled = MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_STATUS;
		$this->sort_order = MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_SORT_ORDER;
		$this->include_shipping = MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_INC_SHIPPING;
		$this->include_tax = MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_INC_TAX;
		$this->percentage = MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_PERCENTAGE;
		$this->minimum = MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_MINIMUM;
		$this->calculate_tax = MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_CALC_TAX;
		//      $this->credit_class = true;
		$this->output = array();
	}

	function process()
	{
		global $order, $osPrice;

		$od_amount = $this->calculate_fee($this->get_order_total());
		if ($od_amount>0)
		{
			$this->addition = $od_amount;
			$this->output[] = array(
				'title' => $this->title . ':',
				'text' => '<b>-' . $osPrice->format($od_amount,true) . '</b>',
				'value' => $od_amount
			);
			$order->info['total'] = $order->info['total'] - $od_amount;  
		}
	}

	function calculate_fee($amount)
	{
		global $order;
		$customer_id = $_SESSION['customer_id'];
		$shipping = $_SESSION['shipping']['id'];
		$od_amount=0;
		$od_pc = $this->percentage;// + .35; //this is percentage plus the base fee
		$do = false;
		if ($amount > $this->minimum)
		{
			$table = split("[,]" , MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_TYPE);
			for ($i = 0; $i < count($table); $i++)
			{
				if ($shipping == $table[$i])
					$do = true;
			}

			if ($do)
			{
				// Calculate tax reduction if necessary
				if ($this->calculate_tax == 'true')
				{
					// Calculate main tax reduction
					$tod_amount = round($order->info['tax']*10)/10*$od_pc/100;
					$order->info['tax'] = $order->info['tax'] + $tod_amount;

					// Calculate tax group deductions
					reset($order->info['tax_groups']);
					while (list($key, $value) = each($order->info['tax_groups']))
					{
						$god_amount = round($value*10)/10*$od_pc/100;
						$order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] + $god_amount;
					}  
				}

				$od_amount = round($amount*10)/10*$od_pc/100;
				$od_amount = $od_amount + $tod_amount;
			}
		}
		return $od_amount;
	}


	function get_order_total()
	{
		global  $order;
		$cart = $_SESSION['cart'];
		$order_total = $order->info['total'];

		// Check if gift voucher is in cart and adjust total
		$products = $cart->get_products();
		for ($i=0; $i<sizeof($products); $i++)
		{
			$t_prid = os_get_prid($products[$i]['id']);
			$gv_query = os_db_query("select products_price, products_tax_class_id, products_model from " . TABLE_PRODUCTS . " where products_id = '" . $t_prid . "'");
			$gv_result = os_db_fetch_array($gv_query);
			if (ereg('^GIFT', addslashes($gv_result['products_model'])))
			{ 
				$qty = $cart->get_quantity($t_prid);
				$products_tax = os_get_tax_rate($gv_result['products_tax_class_id']);
				if ($this->include_tax =='false')
					$gv_amount = $gv_result['products_price'] * $qty;
				else
					$gv_amount = ($gv_result['products_price'] + os_calculate_tax($gv_result['products_price'],$products_tax)) * $qty;

				$order_total=$order_total - $gv_amount;
			}
		}

		if ($this->include_tax == 'false')
			$order_total=$order_total-$order->info['tax'];

		if ($this->include_shipping == 'false')
			$order_total=$order_total-$order->info['shipping_cost'];

		return $order_total;
	}  


	function check()
	{
		if (!isset($this->check))
		{
			$check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_STATUS'");
			$this->check = os_db_num_rows($check_query);
		}

		return $this->check;
	}

	function keys()
	{
		return array('MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_STATUS', 'MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_SORT_ORDER','MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_PERCENTAGE','MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_MINIMUM', 'MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_TYPE', 'MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_INC_SHIPPING', 'MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_INC_TAX', 'MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_CALC_TAX');
	}

	function install()
	{
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_STATUS', 'true', '6', '1','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_SORT_ORDER', '92', '6', '2', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_INC_SHIPPING', 'true', '6', '5', 'os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_INC_TAX', 'true', '6', '6','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_PERCENTAGE', '3', '6', '7', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_CALC_TAX', 'false', '6', '5','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_MINIMUM', '', '6', '2', now())");
		os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_DISCOUNTSHIPPING_TYPE', 'table_table', '6', '2', now())");
	}

	function remove()
	{
		$keys = '';
		$keys_array = $this->keys();
		for ($i=0; $i<sizeof($keys_array); $i++)
		{
			$keys .= "'" . $keys_array[$i] . "',";
		}
		$keys = substr($keys, 0, -1);

		os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in (" . $keys . ")");
	}
}