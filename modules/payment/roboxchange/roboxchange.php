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

  class roboxchange {
    var $code, $title, $description, $enabled;

// class constructor
    function roboxchange() {
      global $order;

      $this->code = 'roboxchange';
      $this->title = MODULE_PAYMENT_ROBOXCHANGE_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_ROBOXCHANGE_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_ROBOXCHANGE_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_ROBOXCHANGE_STATUS == 'True') ? 1 : false);
      $this->icon = 'robox.gif';
      $this->icon_small = 'icon.png';

//      $this->form_action_url = 'https://www.roboxchange.com/ssl/calc.asp';
    }

// class methods
    function update_status() {
      return false;
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
		$icon = os_image(http_path('payment').$this->code.'/'.$this->icon, $this->title);
      return array('id' => $this->code,
                   'module' => $this->title,
                   'description' => $this->description,
				   'icon' => $icon,
				   
				   );
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return false;
    }

    function process_button() {
/*
      global $order, $currencies, $language;

      $inv_id='0';
      $inv_desc='';
      $out_summ=$order->info['total'];

      $crc = md5(MODULE_PAYMENT_ROBOXCHANGE_LOGIN.':'.$out_summ.':'.$inv_id.':'.MODULE_PAYMENT_ROBOXCHANGE_PASSWORD1);

      $process_button_string = os_draw_hidden_field('mrh', MODULE_PAYMENT_ROBOXCHANGE_LOGIN) .
                               os_draw_hidden_field('out_summ', $out_summ) .
                               os_draw_hidden_field('inv_id', $inv_id) .
                               os_draw_hidden_field('inv_desc', $inv_desc) .
                               os_draw_hidden_field('p', 'vecher') .
                               os_draw_hidden_field('lang', (($language=='russian')?'ru':'en')) .
                               os_draw_hidden_field('crc', $crc);

      return $process_button_string;
*/
      return false;
    }

    function before_process() {
      return false;
    }

    function after_process() {
      global $insert_id, $osPrice, $order, $language, $cart;
      $inv_id=$insert_id;
//      $out_summ=$order->info['total_value'];
      $out_summ=number_format($order->info['total'],0,'.',''); 
      $crc = md5(MODULE_PAYMENT_ROBOXCHANGE_LOGIN.':'.$out_summ.':'.$inv_id.':'.MODULE_PAYMENT_ROBOXCHANGE_PASSWORD1);

      $_SESSION['cart']->reset(true);
      //os_session_unregister('sendto');
      //os_session_unregister('billto');
      //os_session_unregister('shipping');
      //os_session_unregister('payment');
      os_session_unregister('comments');
      os_redirect('https://www.roboxchange.com/ssl/calc.asp?mrh='.MODULE_PAYMENT_ROBOXCHANGE_LOGIN.'&out_summ='.$out_summ.'&inv_id='.$inv_id.'&lang='.(($_SESSION['language']=='ru')?'ru':'en').'&crc='.$crc.'&p=vecher');
    }

    function output_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_ROBOXCHANGE_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_ROBOXCHANGE_STATUS', 'True', '6', '3', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
      os_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_ROBOXCHANGE_ALLOWED', '', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_ROBOXCHANGE_LOGIN', '', '6', '4', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_ROBOXCHANGE_PASSWORD1', '', '6', '5', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_ROBOXCHANGE_SORT_ORDER', '0', '6', '7', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_ROBOXCHANGE_PASSWORD2', '', '6', '5', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_ROBOXCHANGE_ORDER_STATUS', '0', '6', '8', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_ROBOXCHANGE_STATUS', 'MODULE_PAYMENT_ROBOXCHANGE_ALLOWED', 'MODULE_PAYMENT_ROBOXCHANGE_LOGIN', 'MODULE_PAYMENT_ROBOXCHANGE_PASSWORD1', 'MODULE_PAYMENT_ROBOXCHANGE_ORDER_STATUS', 'MODULE_PAYMENT_ROBOXCHANGE_PASSWORD2', 'MODULE_PAYMENT_ROBOXCHANGE_SORT_ORDER');
    }
  }
?>
