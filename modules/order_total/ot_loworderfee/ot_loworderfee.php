<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

  class ot_loworderfee {
    var $title, $output;

    function ot_loworderfee() {
    	global $osPrice;
      $this->code = 'ot_loworderfee';
      $this->title = MODULE_ORDER_TOTAL_LOWORDERFEE_TITLE;
      $this->description = MODULE_ORDER_TOTAL_LOWORDERFEE_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $osPrice;
      
      
      if (MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE == 'true') {
        switch (MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION) {
          case 'national':
            if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true; break;
          case 'international':
            if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true; break;
          case 'both':
            $pass = true; break;
          default:
            $pass = false; break;
        }

        if ( ($pass == true) && ( ($order->info['total'] - $order->info['shipping_cost']) < MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER) ) {
          $tax = os_get_tax_rate(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $tax_description = os_get_tax_description(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);



          

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
		  $order->info['tax'] += os_calculate_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax);
          $order->info['tax_groups'][TAX_ADD_TAX . "$tax_description"] += os_calculate_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax);
          $order->info['total'] += MODULE_ORDER_TOTAL_LOWORDERFEE_FEE + os_calculate_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax);
          $low_order_fee=os_add_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax);
        }
        
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
		$low_order_fee=MODULE_ORDER_TOTAL_LOWORDERFEE_FEE;
		$order->info['tax'] += os_calculate_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax);
        $order->info['tax_groups'][TAX_NO_TAX . "$tax_description"] += os_calculate_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax);
		$order->info['subtotal'] += $low_order_fee;
        $order->info['total'] += $low_order_fee;
        }
        
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] != 1) {
		$low_order_fee=MODULE_ORDER_TOTAL_LOWORDERFEE_FEE;
		$order->info['subtotal'] += $low_order_fee;
        $order->info['total'] += $low_order_fee;
        }



          $this->output[] = array('title' => $this->title . ':',
                                  'text' => $osPrice->Format($low_order_fee, true),
                                  'value' => $low_order_fee);
        }
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS', 'MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER', 'MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE', 'MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER', 'MODULE_ORDER_TOTAL_LOWORDERFEE_FEE', 'MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION', 'MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS');
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS', 'true', '6', '1','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER', '4', '6', '2', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE', 'false', '6', '3', 'os_cfg_select_option(array(\'true\', \'false\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER', '50','6', '4', 'currencies->format', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_FEE', '5','6', '5', 'currencies->format', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION', 'both','6', '6', 'os_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS', '0','6', '7', 'os_get_tax_class_title', 'os_cfg_pull_down_tax_classes(', now())");
    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>