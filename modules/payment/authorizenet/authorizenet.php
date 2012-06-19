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

class authorizenet {

    var $code, $title, $description, $enabled;


    function authorizenet() {
      global $order;

      $this->code = 'authorizenet';
      $this->title = MODULE_PAYMENT_AUTHORIZENET_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_AUTHORIZENET_TEXT_DESCRIPTION;
      $this->enabled = ((MODULE_PAYMENT_AUTHORIZENET_STATUS == 'True') ? true : false);
      $this->sort_order = MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER;
	  $this->info=MODULE_PAYMENT_AUTHORIZENET_TEXT_INFO;
	  
      if ((int)MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://secure.authorize.net/gateway/transact.dll';
    }


function hmac ($key, $data)
{
   $b = 64; // byte length for md5
   if (strlen($key) > $b) {
       $key = pack("H*",md5($key));
   }
   $key  = str_pad($key, $b, chr(0x00));
   $ipad = str_pad('', $b, chr(0x36));
   $opad = str_pad('', $b, chr(0x5c));
   $k_ipad = $key ^ $ipad ;
   $k_opad = $key ^ $opad;

   return md5($k_opad  . pack("H*",md5($k_ipad . $data)));
}

 /**
 * Calculate and return fingerprint
 * Use when you need control on the HTML output
 *
 * @param string $loginid
 * @param string $txnkey
 * @param string $amount
 * @param string $sequence
 * @param string $tstamp
 * @param string $currency
 */
function CalculateFP ($loginid, $txnkey, $amount, $sequence, $tstamp, $currency = "") {
  return ($this->hmac ($txnkey, $loginid . "^" . $sequence . "^" . $tstamp . "^" . $amount . "^" . $currency));
}

 /**
 * Inserts the hidden variables in the HTML FORM required for SIM
 * Invokes hmac function to calculate fingerprint.
 */
function InsertFP ($loginid, $txnkey, $amount, $sequence, $currency = "") {
  $tstamp = time ();
  $fingerprint = $this->hmac ($txnkey, $loginid . "^" . $sequence . "^" . $tstamp . "^" . $amount . "^" . $currency);

  $str = os_draw_hidden_field('x_fp_sequence', $sequence) .
         os_draw_hidden_field('x_fp_timestamp', $tstamp) .
         os_draw_hidden_field('x_fp_hash', $fingerprint);

  return $str;
}

/**
 * class methods
 */
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_AUTHORIZENET_ZONE > 0) ) {
        $check_flag = false;
        $check_query = os_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_AUTHORIZENET_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = os_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      $js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
            '    var cc_owner = document.getElementById("checkout_payment").authorizenet_cc_owner.value;' . "\n" .
            '    var cc_number = document.getElementById("checkout_payment").authorizenet_cc_number.value;' . "\n" .
            '    if (cc_owner == "" || cc_owner.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_AUTHORIZENET_TEXT_JS_CC_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_AUTHORIZENET_TEXT_JS_CC_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n";

      return $js;
    }

    function selection() {
      global $order;

      for ($i=1; $i<13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => os_date_long_translate(strftime('%B',mktime(0,0,0,$i,1,2000))));
      }

      $today = getdate(); 
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }
      $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'description'=>$this->info,
                         'fields' => array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_OWNER,
                                                 'field' => os_draw_input_field('authorizenet_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                                           array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_NUMBER,
                                                 'field' => os_draw_input_field('authorizenet_cc_number')),
                                           array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES,
                                                 'field' => os_draw_pull_down_menu('authorizenet_cc_expires_month', $expires_month) . '&nbsp;' . os_draw_pull_down_menu('authorizenet_cc_expires_year', $expires_year))));

      return $selection;
    }

    function pre_confirmation_check() {

      include(dir_path('class') . 'cc_validation.php');

      $cc_validation = new cc_validation();
      $result = $cc_validation->validate($_POST['authorizenet_cc_number'], $_POST['authorizenet_cc_expires_month'], $_POST['authorizenet_cc_expires_year']);
      $error = '';
      switch ($result) {
        case -1:
          $error = sprintf(TEXT_CCVAL_ERROR_UNKNOWN_CARD, substr($cc_validation->cc_number, 0, 4));
          break;
        case -2:
        case -3:
        case -4:
          $error = TEXT_CCVAL_ERROR_INVALID_DATE;
          break;
        case false:
          $error = TEXT_CCVAL_ERROR_INVALID_NUMBER;
          break;
      }

      if ( ($result == false) || ($result < 1) ) {
        $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&authorizenet_cc_owner=' . urlencode($_POST['authorizenet_cc_owner']) . '&authorizenet_cc_expires_month=' . $_POST['authorizenet_cc_expires_month'] . '&authorizenet_cc_expires_year=' . $_POST['authorizenet_cc_expires_year'];

        os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
      }

      $this->cc_card_type = $cc_validation->cc_type;
      $this->cc_card_number = $cc_validation->cc_number;
      $this->cc_expiry_month = $cc_validation->cc_expiry_month;
      $this->cc_expiry_year = $cc_validation->cc_expiry_year;
    }

    function confirmation() {

      $confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
                            'fields' => array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_OWNER,
                                                    'field' => $_POST['authorizenet_cc_owner']),
                                              array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_NUMBER,
                                                    'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                              array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES,
                                                    'field' => strftime('%B, %Y', mktime(0,0,0,$_POST['authorizenet_cc_expires_month'], 1, '20' . $_POST['authorizenet_cc_expires_year'])))));

      return $confirmation;
    }

    function process_button() {
      global $order;

      $sequence = rand(1, 1000);
      if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
          $total=$order->info['total']+$order->info['tax'];
      } else {
          $total=$order->info['total'];
      }
      $process_button_string = os_draw_hidden_field('x_Login', MODULE_PAYMENT_AUTHORIZENET_LOGIN) .
                               os_draw_hidden_field('x_Card_Num', $this->cc_card_number) .
                               os_draw_hidden_field('x_Exp_Date', $this->cc_expiry_month . substr($this->cc_expiry_year, -2)) .
                               os_draw_hidden_field('x_Amount', round($total, 2)) .
                               os_draw_hidden_field('x_Relay_URL', os_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', false)) .
                               os_draw_hidden_field('x_Method', ((MODULE_PAYMENT_AUTHORIZENET_METHOD == 'Credit Card') ? 'CC' : 'ECHECK')) .
                               os_draw_hidden_field('x_Version', '3.0') .
                               os_draw_hidden_field('x_Cust_ID', $_SESSION['customer_id']) .
                               os_draw_hidden_field('x_Email_Customer', ((MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER == 'True') ? 'TRUE': 'FALSE')) .
                               os_draw_hidden_field('x_first_name', $order->billing['firstname']) .
                               os_draw_hidden_field('x_last_name', $order->billing['lastname']) .
                               os_draw_hidden_field('x_address', $order->billing['street_address']) .
                               os_draw_hidden_field('x_city', $order->billing['city']) .
                               os_draw_hidden_field('x_state', $order->billing['state']) .
                               os_draw_hidden_field('x_zip', $order->billing['postcode']) .
                               os_draw_hidden_field('x_country', $order->billing['country']['title']) .
                               os_draw_hidden_field('x_phone', $order->customer['telephone']) .
                               os_draw_hidden_field('x_email', $order->customer['email_address']) .
                               os_draw_hidden_field('x_ship_to_first_name', $order->delivery['firstname']) .
                               os_draw_hidden_field('x_ship_to_last_name', $order->delivery['lastname']) .
                               os_draw_hidden_field('x_ship_to_address', $order->delivery['street_address']) .
                               os_draw_hidden_field('x_ship_to_city', $order->delivery['city']) .
                               os_draw_hidden_field('x_ship_to_state', $order->delivery['state']) .
                               os_draw_hidden_field('x_ship_to_zip', $order->delivery['postcode']) .
                               os_draw_hidden_field('x_ship_to_country', $order->delivery['country']['title']) .
                               os_draw_hidden_field('x_Customer_IP', $_SERVER['REMOTE_ADDR']) .
                               $this->InsertFP(MODULE_PAYMENT_AUTHORIZENET_LOGIN, MODULE_PAYMENT_AUTHORIZENET_TXNKEY, round($total, 2), $sequence);
      if (MODULE_PAYMENT_AUTHORIZENET_TESTMODE == 'Test') $process_button_string .= os_draw_hidden_field('x_Test_Request', 'TRUE');

      $process_button_string .= os_draw_hidden_field(os_session_name(), os_session_id());

      return $process_button_string;
    }

    function before_process() {

      if ($_POST['x_response_code'] == '1') return;
      if ($_POST['x_response_code'] == '2') {
        os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_AUTHORIZENET_TEXT_DECLINED_MESSAGE), 'SSL', true, false));
      }
      // Code 3 is an error - but anything else is an error too (IMHO)
      os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR_MESSAGE), 'SSL', true, false));
    }

    function after_process() {
    global $insert_id;
        if ($this->order_status) os_db_query("UPDATE ". TABLE_ORDERS ." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");

    }

    function get_error() {

      $error = array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR,
                     'error' => stripslashes(urldecode($_GET['error'])));

      return $error;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_AUTHORIZENET_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_STATUS', 'True', '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
	  os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_ALLOWED', '', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_LOGIN', 'testing',  '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_TXNKEY', 'Test',  '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_TESTMODE', 'Test',  '6', '0', 'os_cfg_select_option(array(\'Test\', \'Production\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_METHOD', 'Credit Card',  '6', '0', 'os_cfg_select_option(array(\'Credit Card\', \'eCheck\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER', 'False',  '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER', '0',  '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_ZONE', '0',  '6', '2', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID', '0', '6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_AUTHORIZENET_STATUS','MODULE_PAYMENT_AUTHORIZENET_ALLOWED', 'MODULE_PAYMENT_AUTHORIZENET_LOGIN', 'MODULE_PAYMENT_AUTHORIZENET_TXNKEY', 'MODULE_PAYMENT_AUTHORIZENET_TESTMODE', 'MODULE_PAYMENT_AUTHORIZENET_METHOD', 'MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER', 'MODULE_PAYMENT_AUTHORIZENET_ZONE', 'MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID', 'MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER');
    }
  }
?>