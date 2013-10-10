<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

  class item {
    var $code, $title, $description, $icon, $enabled;


    function item() {
      global $order;

      $this->code = 'item';
      $this->title = MODULE_SHIPPING_ITEM_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_ITEM_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_ITEM_SORT_ORDER;
      $this->icon = '';
      $this->tax_class = MODULE_SHIPPING_ITEM_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_ITEM_STATUS == 'True') ? true : false);

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_ITEM_ZONE > 0) ) {
        $check_flag = false;
        $check_query = os_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_ITEM_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
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
      global $order, $total_count;

      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_ITEM_TEXT_TITLE,
                            'methods' => array(array('id' => $this->code,
                                                     'title' => MODULE_SHIPPING_ITEM_TEXT_WAY,
                                                     'cost' => (MODULE_SHIPPING_ITEM_COST * $total_count) + MODULE_SHIPPING_ITEM_HANDLING)));

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = os_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (os_not_null($this->icon)) $this->quotes['icon'] = os_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_ITEM_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_ITEM_STATUS', 'True', '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_ITEM_ALLOWED', '', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_ITEM_COST', '2.50', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_ITEM_HANDLING', '0', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_ITEM_TAX_CLASS', '0', '6', '0', 'os_get_tax_class_title', 'os_cfg_pull_down_tax_classes(', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_ITEM_ZONE', '0', '6', '0', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_ITEM_SORT_ORDER', '0', '6', '0', now())");
    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_SHIPPING_ITEM_STATUS', 'MODULE_SHIPPING_ITEM_COST', 'MODULE_SHIPPING_ITEM_HANDLING','MODULE_SHIPPING_ITEM_ALLOWED', 'MODULE_SHIPPING_ITEM_TAX_CLASS', 'MODULE_SHIPPING_ITEM_ZONE', 'MODULE_SHIPPING_ITEM_SORT_ORDER');
    }
  }
?>
