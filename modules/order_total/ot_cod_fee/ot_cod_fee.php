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

class ot_cod_fee {
  var $title, $output;

  function ot_cod_fee() {
    global $osPrice;
      $this->code = 'ot_cod_fee';
      $this->title = MODULE_ORDER_TOTAL_COD_FEE_TITLE;
      $this->description = MODULE_ORDER_TOTAL_COD_FEE_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_COD_FEE_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_COD_FEE_SORT_ORDER;


      $this->output = array();
    }

    function process() {
      global $order, $osPrice, $cod_cost, $cod_country, $shipping;

      if (MODULE_ORDER_TOTAL_COD_FEE_STATUS == 'true') {
        $cod_country = false;
        if ($_SESSION['payment'] == 'cod') {
          //process installed shipping modules
          if ($_SESSION['shipping']['id'] == 'flat_flat') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_FLAT);
          if ($_SESSION['shipping']['id'] == 'item_item') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_ITEM);
          if ($_SESSION['shipping']['id'] == 'table_table') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_TABLE);
          if ($_SESSION['shipping']['id'] == 'zones_zones') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_ZONES);
          if ($_SESSION['shipping']['id'] == 'ap_ap') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_AP);
          if ($_SESSION['shipping']['id'] == 'dp_dp') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DP);

                    // module chp
          if ($_SESSION['shipping']['id'] == 'chp_ECO') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_CHP);
          if ($_SESSION['shipping']['id'] == 'chp_PRI') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_CHP);
          if ($_SESSION['shipping']['id'] == 'chp_URG') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_CHP);

          // module chronopost
          if ($_SESSION['shipping']['id'] == 'chronopost_chronopost') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_CHRONOPOST);

          // module DHL
          if ($_SESSION['shipping']['id'] == 'dhl_ECX') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DHL);
          if ($_SESSION['shipping']['id'] == 'dhl_DOX') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DHL);
          if ($_SESSION['shipping']['id'] == 'dhl_SDX') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DHL);
          if ($_SESSION['shipping']['id'] == 'dhl_MDX') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DHL);
          if ($_SESSION['shipping']['id'] == 'dhl_WPX') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_DHL);
         // UPS
          if ($_SESSION['shipping']['id'] == 'ups_ups') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_UPS);
          if ($_SESSION['shipping']['id'] == 'upse_upse') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_UPSE);

          // Free Shipping
          if ($_SESSION['shipping']['id'] == 'free_free') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_COD_FEE_FREE);
          if ($_SESSION['shipping']['id'] == 'freeamount_freeamount') $cod_zones = preg_split("/[:,]/", MODULE_ORDER_TOTAL_FREEAMOUNT_FREE);


            for ($i = 0; $i < count($cod_zones); $i++) {
            if ($cod_zones[$i] == $order->delivery['country']['iso_code_2']) {
                  $cod_cost = $cod_zones[$i + 1];
                  $cod_country = true;
                  break;
                } elseif ($cod_zones[$i] == '00') {
                  $cod_cost = $cod_zones[$i + 1];
                  $cod_country = true;
                  break;
                } else {
                }
              $i++;
            }
          } else {
          }

        if ($cod_country) {

            $cod_tax = os_get_tax_rate(MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
            $cod_tax_description = os_get_tax_description(MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
            $order->info['tax'] += os_add_tax($cod_cost, $cod_tax)-$cod_cost;
            $order->info['tax_groups'][TAX_ADD_TAX . "$cod_tax_description"] += os_add_tax($cod_cost, $cod_tax)-$cod_cost;
            $order->info['total'] += $cod_cost + (os_add_tax($cod_cost, $cod_tax)-$cod_cost);
            $cod_cost_value= os_add_tax($cod_cost, $cod_tax);
            $cod_cost= $osPrice->Format($cod_cost_value,true);
        }
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $order->info['tax'] += os_add_tax($cod_cost, $cod_tax)-$cod_cost;
            $order->info['tax_groups'][TAX_NO_TAX . "$cod_tax_description"] += os_add_tax($cod_cost, $cod_tax)-$cod_cost;
            $cod_cost_value=$cod_cost;
            $cod_cost= $osPrice->Format($cod_cost,true);
            $order->info['subtotal'] += $cod_cost_value;
            $order->info['total'] += $cod_cost_value;
        }
        if (!$cod_cost_value) {
           $cod_cost_value=$cod_cost;
           $cod_cost= $osPrice->Format($cod_cost,true);
           $order->info['total'] += $cod_cost_value;
        }
            $this->output[] = array('title' => $this->title . ':',
                                    'text' => $cod_cost,
                                    'value' => $cod_cost_value);
        } else {

        }
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_COD_FEE_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_COD_FEE_STATUS', 'MODULE_ORDER_TOTAL_COD_FEE_SORT_ORDER', 'MODULE_ORDER_TOTAL_COD_FEE_FLAT', 'MODULE_ORDER_TOTAL_COD_FEE_ITEM', 'MODULE_ORDER_TOTAL_COD_FEE_TABLE','MODULE_ORDER_TOTAL_COD_FEE_CHRONOPOST','MODULE_ORDER_TOTAL_COD_FEE_DHL','MODULE_ORDER_TOTAL_COD_FEE_CHP', 'MODULE_ORDER_TOTAL_COD_FEE_ZONES', 'MODULE_ORDER_TOTAL_COD_FEE_AP', 'MODULE_ORDER_TOTAL_COD_FEE_UPS', 'MODULE_ORDER_TOTAL_COD_FEE_UPSE', 'MODULE_ORDER_TOTAL_COD_FEE_DP', 'MODULE_ORDER_TOTAL_COD_FEE_FREE', 'MODULE_ORDER_TOTAL_FREEAMOUNT_FREE', 'MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS');
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_STATUS', 'true', '6', '0', 'os_cfg_select_option(array(\'true\', \'false\'), ', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_SORT_ORDER', '35', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_FLAT', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_ITEM', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_TABLE', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_ZONES', 'CA:4.50,US:3.00,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_AP', 'AT:3.63,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_DP', 'DE:4.00,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_CHP', 'CH:4.00,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_CHRONOPOST', 'FR:4.00,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_DHL', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_UPS', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_UPSE', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_FREE', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_FREEAMOUNT_FREE', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS', '0', '6', '0', 'os_get_tax_class_title', 'os_cfg_pull_down_tax_classes(', now())");
    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>