<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

  class ot_shipping {
    var $title, $output;

    function ot_shipping() {
    	global $osPrice;
      $this->code = 'ot_shipping';
      $this->title = MODULE_ORDER_TOTAL_SHIPPING_TITLE;
      $this->description = MODULE_ORDER_TOTAL_SHIPPING_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_SHIPPING_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER;


      $this->output = array();
    }

    function process() {
      global $order, $osPrice;

      if (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true') {
        switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
          case 'national':
            if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true; break;
          case 'international':
            if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true; break;
          case 'both':
            $pass = true; break;
          default:
            $pass = false; break;
        }

        if ( ($pass == true) && ( ($order->info['total'] - $order->info['shipping_cost']) >= $osPrice->Format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER,false,0,true)) ) {
          $order->info['shipping_method'] = $this->title;
          $order->info['total'] -= $order->info['shipping_cost'];
          $order->info['shipping_cost'] = 0;
        }
      }

      $module = substr($_SESSION['shipping']['id'], 0, strpos($_SESSION['shipping']['id'], '_'));

      if (os_not_null($order->info['shipping_method'])) {
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {

          $shipping_tax = os_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $shipping_tax_description = os_get_tax_description($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $tax = $osPrice->Format(os_add_tax($order->info['shipping_cost'], $shipping_tax),false,0,false)-$order->info['shipping_cost'];
          $tax = $osPrice->Format($tax,false,0,true);
          $order->info['shipping_cost'] = os_add_tax($order->info['shipping_cost'], $shipping_tax);
          $order->info['tax'] += $tax;
          $order->info['tax_groups'][TAX_ADD_TAX . "$shipping_tax_description"] += $tax;
          $order->info['total'] += $tax;

        } else {

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {

          $shipping_tax = os_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $shipping_tax_description = os_get_tax_description($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $tax = $osPrice->Format(os_add_tax($order->info['shipping_cost'], $shipping_tax),false,0,false)-$order->info['shipping_cost'];
		 $tax = $osPrice->Format($tax,false,0,true);

          $order->info['tax'] = $order->info['tax'] += $tax;
          $order->info['tax_groups'][TAX_NO_TAX . "$shipping_tax_description"] = $order->info['tax_groups'][TAX_NO_TAX . "$shipping_tax_description"] += $tax;
        }
        }
        $this->output[] = array('title' => $order->info['shipping_method'] . ':',
                                'text' => $osPrice->Format($order->info['shipping_cost'], true,0,true),
                                'value' => $osPrice->Format($order->info['shipping_cost'], false,0,true));
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_SHIPPING_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER', 'MODULE_ORDER_TOTAL_SHIPPING_DESTINATION', 'MODULE_ORDER_TOTAL_SHIPPING_TAX_CLASS');
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'true','6', '1','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', '3','6', '2', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING', 'false','6', '3', 'os_cfg_select_option(array(\'true\', \'false\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER', '50', '6', '4', 'currencies->format', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_DESTINATION', 'national','6', '5', 'os_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_TAX_CLASS', '0','6', '7', 'os_get_tax_class_title', 'os_cfg_pull_down_tax_classes(', now())");      
    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>