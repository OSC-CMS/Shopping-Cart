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

  class ot_subtotal_no_tax {

    var $title, $output;

    function ot_subtotal_no_tax() {
    	global $osPrice;
      $this->code = 'ot_subtotal_no_tax';
      $this->title = MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_TITLE;
      $this->description = MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER;


      $this->output = array();
    }

    function process() {
      global $order, $osPrice;

      if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
          $sub_total_price = $order->info['subtotal'] - ($order->info['subtotal'] / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
	} else {
	  $sub_total_price = $order->info['subtotal'];
	}
        $this->output[] = array('title' => $this->title . ':',
                                'text' => '<b>' . $osPrice->Format($sub_total_price+($osPrice->Format($order->info['shipping_cost'], false,0,true)), true).'</b>',
                                'value' => $osPrice->Format($sub_total_price+($osPrice->Format($order->info['shipping_cost'], false,0,true)), false));
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS', 'MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER');
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS', 'true', '6', '1','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER', '4','6', '2', now())");
    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>