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

class ukrpost {
    var $code, $title, $description, $icon, $enabled;


    function ukrpost() {
      global $order;

      $this->code = 'ukrpost';
      $this->title = MODULE_SHIPPING_UKRPOST_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_UKRPOST_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_UKRPOST_SORT_ORDER;
      $this->icon = '';
      $this->tax_class = MODULE_SHIPPING_UKRPOST_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_UKRPOST_STATUS == 'True') ? true : false);

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_UKRPOST_ZONE > 0) ) {
        $check_flag = false;
        $check_query = os_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_UKRPOST_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = os_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }

      }
    }


    function quote($method = '') {
      global $order, $shipping_weight, $shipping_num_boxes,$osPrice;

      if (MODULE_SHIPPING_UKRPOST_MODE == 'price') {
        $order_total = $osPrice->RemoveCurr($_SESSION['cart']->show_total());
      } else {
        $order_total = $shipping_weight;
      }

      $table_cost = preg_split("/[:,]/" , MODULE_SHIPPING_UKRPOST_COST);
      $size = sizeof($table_cost);
      for ($i=0, $n=$size; $i<$n; $i+=2) {
        if ($order_total <= $table_cost[$i]) {
          $shipping = $table_cost[$i+1];
          break;
        }
      }

      if (MODULE_SHIPPING_UKRPOST_MODE == 'weight') {
        $shipping = $shipping * $shipping_num_boxes;
      }

      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_UKRPOST_TEXT_TITLE,
                            'methods' => array(array('id' => $this->code,
                                                     'title' => MODULE_SHIPPING_UKRPOST_TEXT_WAY,
                                                     'cost' => $shipping + MODULE_SHIPPING_UKRPOST_HANDLING)));

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = os_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (os_not_null($this->icon)) $this->quotes['icon'] = os_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_UKRPOST_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_SHIPPING_UKRPOST_STATUS', 'True', '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_UKRPOST_ALLOWED', '', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_UKRPOST_COST', '25:8.50,50:5.50,10000:0.00', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_UKRPOST_MODE', 'weight', '6', '0', 'os_cfg_select_option(array(\'weight\', \'price\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_UKRPOST_HANDLING', '0', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_UKRPOST_TAX_CLASS', '0', '6', '0', 'os_get_tax_class_title', 'os_cfg_pull_down_tax_classes(', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_UKRPOST_ZONE', '0', '6', '0', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_UKRPOST_SORT_ORDER', '0', '6', '0', now())");
    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_SHIPPING_UKRPOST_STATUS', 'MODULE_SHIPPING_UKRPOST_COST', 'MODULE_SHIPPING_UKRPOST_MODE', 'MODULE_SHIPPING_UKRPOST_HANDLING','MODULE_SHIPPING_UKRPOST_ALLOWED', 'MODULE_SHIPPING_UKRPOST_TAX_CLASS', 'MODULE_SHIPPING_UKRPOST_ZONE', 'MODULE_SHIPPING_UKRPOST_SORT_ORDER');
    }
  }
?>
