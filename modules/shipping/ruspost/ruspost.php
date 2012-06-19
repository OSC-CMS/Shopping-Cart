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

class ruspost {
    var $code, $title, $description, $icon, $enabled;

    function ruspost() {
      global $order;

      $this->code = 'ruspost';
      $this->title = MODULE_SHIPPING_RUSPOST_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_RUSPOST_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_RUSPOST_SORT_ORDER;
      $this->icon = '';
      $this->tax_class = MODULE_SHIPPING_RUSPOST_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_RUSPOST_STATUS == 'True') ? true : false);

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_RUSPOST_ZONE > 0) ) {
        $check_flag = false;
        $check_query = os_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_RUSPOST_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
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
      global $order;
       for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {       
       $weight=$weight+$order->products[$i]['qty']*$order->products[$i]['weight'];

   }

        $tax_zone_query = os_db_query("select tax_zone_id, low, high, mono from ".DB_PREFIX."post_index where low <='" . $order->customer['postcode'] ."'AND high >='" . $order->customer['postcode'] ."' OR  mono = '" . $order->customer['postcode'] ."'");
        $tax_zone = os_db_fetch_array($tax_zone_query);
        $tax_zone_id = $tax_zone['tax_zone_id'];       

       if($tax_zone_id==1&&$weight<=1000&&$order->info['subtotal']<="1000"){$tax_prise=ONE_1000_COST;}
        else if($tax_zone_id==1&&$weight<=2000&&$order->info['subtotal']<="2000"){$tax_prise=ONE_2000_COST;}
         else if($tax_zone_id==1&&$weight<=2000&&$order->info['subtotal']<="3500"){$tax_prise=ONE_3500_COST;}

       if($tax_zone_id==4&&$weight<=1000&&$order->info['subtotal']<="1000"){$tax_prise=TWO_1000_COST;}
        else if($tax_zone_id==4&&$weight<=2000&&$order->info['subtotal']<="2000"){$tax_prise=TWO_2000_COST;}      
         else if($tax_zone_id==4&&$weight<=2000&&$order->info['subtotal']<="3500"){$tax_prise=TWO_3500_COST;}     

       if($tax_zone_id==5&&$weight<=1000&&$order->info['subtotal']<="1000"){$tax_prise=THRE_1000_COST;}
        else if($tax_zone_id==5&&$weight<=2000&&$order->info['subtotal']<="2000"){$tax_prise=THRE_2000_COST;}
         else if($tax_zone_id==5&&$weight<=2000&&$order->info['subtotal']<="3500"){$tax_prise=THRE_3500_COST;}




      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_RUSPOST_TEXT_TITLE,
                            'methods' => array(array('id' => $this->code,
                                                     'title' => MODULE_SHIPPING_RUSPOST_TEXT_WAY,
                                                     'cost' => $tax_prise)));

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = os_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (os_not_null($this->icon)) $this->quotes['icon'] = os_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_RUSPOST_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_RUSPOST_STATUS', 'True', '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_RUSPOST_ALLOWED', '', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('ONE_1000_COST', '130', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('ONE_2000_COST', '170', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('ONE_3500_COST', '250', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('TWO_1000_COST', '170', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('TWO_2000_COST', '200', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('TWO_3500_COST', '300', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('THRE_1000_COST', '190', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('THRE_2000_COST', '220', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('THRE_3500_COST', '320', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_RUSPOST_TAX_CLASS', '0', '6', '0', 'os_get_tax_class_title', 'os_cfg_pull_down_tax_classes(', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_RUSPOST_ZONE', '0', '6', '0', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_RUSPOST_SORT_ORDER', '0', '6', '0', now())");

    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_SHIPPING_RUSPOST_STATUS', 'MODULE_SHIPPING_RUSPOST_ALLOWED', 'ONE_1000_COST','ONE_2000_COST','ONE_3500_COST','TWO_1000_COST','TWO_2000_COST','TWO_3500_COST','THRE_1000_COST','THRE_2000_COST','THRE_3500_COST', 'MODULE_SHIPPING_RUSPOST_TAX_CLASS', 'MODULE_SHIPPING_RUSPOST_ZONE', 'MODULE_SHIPPING_RUSPOST_SORT_ORDER');
    }
  }
?>
