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

  class ot_subtotal {
    var $title, $output;

    function ot_subtotal() {
    	global $osPrice;
      $this->code = 'ot_subtotal';
      $this->title = MODULE_ORDER_TOTAL_SUBTOTAL_TITLE;
      $this->description = MODULE_ORDER_TOTAL_SUBTOTAL_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_SUBTOTAL_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER;
 

      $this->output = array();
    }

    function process() {
      global $order, $osPrice;
      if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
        $this->output[] = array('title' => MODULE_ORDER_TOTAL_SUBTOTAL_TITLE_NO_TAX . ':',
                                'text' => $osPrice->Format($order->info['subtotal'],true),
                                'value' => $order->info['subtotal']);
      }
      if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
        $this->output[] = array('title' => $this->title . ':',
                                'text' => $osPrice->Format($order->info['subtotal'],true),
                                'value' => $order->info['subtotal']);
      } 
      if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
        $this->output[] = array('title' => $this->title . ':',
                                'text' => $osPrice->Format($order->info['subtotal'],true),
                                'value' => $order->info['subtotal']);
      }


    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_SUBTOTAL_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_SUBTOTAL_STATUS', 'MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER');
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_SUBTOTAL_STATUS', 'true', '6', '1','os_cfg_select_option(array(\'true\', \'false\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER', '1', '6', '2', now())");
    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>