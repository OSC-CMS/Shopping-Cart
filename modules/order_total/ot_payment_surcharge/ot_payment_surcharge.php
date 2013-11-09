<?php
class ot_payment_surcharge
{
	var $title, $output;

	function ot_payment_surcharge()
	{
		$this->code = 'ot_payment_surcharge';
		$this->title = MODULE_PAYMENT_SURCHARGE_TITLE;
		$this->description = MODULE_PAYMENT_SURCHARGE_DESCRIPTION;
		$this->enabled = MODULE_PAYMENT_SURCHARGE_STATUS;
		$this->sort_order = MODULE_PAYMENT_SURCHARGE_SORT_ORDER;
		$this->include_shipping = MODULE_PAYMENT_SURCHARGE_INC_SHIPPING;
		$this->include_tax = MODULE_PAYMENT_SURCHARGE_INC_TAX;
		$this->minimum = MODULE_PAYMENT_SURCHARGE_MINIMUM;
		$this->calculate_tax = MODULE_PAYMENT_SURCHARGE_CALC_TAX;
		$this->output = array();
	}

	function process()
	{
		global $order, $osPrice;

		$od_amount = $this->calculate_fee($this->get_order_total());

		if (!empty($od_amount) && $od_amount['price'] > 0)
		{
			$paymentLangFile = get_path('modules').'payment/'.$od_amount['module'].'/'.$_SESSION['language'].'.php';

			$moduleName = '';
			if (is_file($paymentLangFile))
			{
				$moduleName = ' "'.constant(MODULE_PAYMENT_.strtoupper($od_amount['module'])._TEXT_TITLE).'"';
			}

			$this->output[] = array(
				'title' => $this->title.$moduleName.':',
				'text' => $osPrice->format($od_amount['price'], true),
				'value' => $od_amount['price']
			);

			$order->info['total'] = $order->info['total'] + $od_amount['price'];  
		}
	}

	function calculate_fee($amount)
	{
		global $order;

		$customer_id = $_SESSION['customer_id'];
		$payment = $_SESSION['payment'];

		$od_amount = 0;

		if ($amount > $this->minimum)
		{
			$modules = explode(',', MODULE_PAYMENT_SURCHARGE_TYPE);

			if (empty($modules)) return array('module' => $payment, 'price' => $od_amount);

			$module = array();
			foreach($modules AS $m)
			{
				$aModule = explode(':', $m);
				$module[$aModule[0]] = $aModule[1];
			}
			
			foreach($module AS $p => $c)
			{
				if ($payment == $p)
				{
					if ($this->calculate_tax == 'true')
					{
						$tod_amount = round($order->info['tax']*10)/10*$c/100;
						$order->info['tax'] = $order->info['tax'] + $tod_amount;

						reset($order->info['tax_groups']);
						while (list($key, $value) = each($order->info['tax_groups']))
						{
							$god_amount = round($value*10)/10*$c/100;
							$order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] + $god_amount;
						}  
					}

					$od_amount = round($amount*10)/10*$c/100;
					$od_amount = $od_amount + $tod_amount;
				}
			}
		}
		return array('module' => $payment, 'price' => $od_amount);
	}

	function get_order_total()
	{
		global  $order;

		$cart = $_SESSION['cart'];
		$order_total = $order->info['total'];

		$products = $cart->get_products();

		for ($i=0; $i<sizeof($products); $i++)
		{
			$t_prid = os_get_prid($products[$i]['id']);
			$gv_query = os_db_query("select products_price, products_tax_class_id, products_model from ".TABLE_PRODUCTS." where products_id = '".$t_prid."'");
			$gv_result = os_db_fetch_array($gv_query);

			if (preg_match('/^GIFT/', addslashes($gv_result['products_model'])))
			{ 
				$qty = $cart->get_quantity($t_prid);
				$products_tax = os_get_tax_rate($gv_result['products_tax_class_id']);

				if ($this->include_tax =='false')
					$gv_amount = $gv_result['products_price'] * $qty;
				else
					$gv_amount = ($gv_result['products_price'] + os_calculate_tax($gv_result['products_price'],$products_tax)) * $qty;

				$order_total = $order_total - $gv_amount;
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
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_SURCHARGE_STATUS'");
			$this->check = os_db_num_rows($check_query);
		}

		return $this->check;
	}

	function keys()
	{
		return array(
			'MODULE_PAYMENT_SURCHARGE_STATUS',
			'MODULE_PAYMENT_SURCHARGE_SORT_ORDER',
			'MODULE_PAYMENT_SURCHARGE_MINIMUM',
			'MODULE_PAYMENT_SURCHARGE_TYPE',
			'MODULE_PAYMENT_SURCHARGE_INC_SHIPPING',
			'MODULE_PAYMENT_SURCHARGE_INC_TAX',
			'MODULE_PAYMENT_SURCHARGE_CALC_TAX'
		);
	}

	function install()
	{
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SURCHARGE_STATUS', 'true', '6', '1','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SURCHARGE_SORT_ORDER', '90', '6', '2', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_PAYMENT_SURCHARGE_INC_SHIPPING', 'true', '6', '5', 'os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_PAYMENT_SURCHARGE_INC_TAX', 'true', '6', '6','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_PAYMENT_SURCHARGE_CALC_TAX', 'false', '6', '5','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SURCHARGE_MINIMUM', '', '6', '2', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SURCHARGE_TYPE', 'moneyorder:1', '6', '2', now())");
	}

	function remove()
	{
		$keys = '';
		$keys_array = $this->keys();

		for ($i=0; $i<sizeof($keys_array); $i++)
		{
			$keys .= "'".$keys_array[$i]."',";
		}

		$keys = substr($keys, 0, -1);

		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in (".$keys.")");
	}
}
?>