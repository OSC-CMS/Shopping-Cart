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

class banktransfer {

    var $code, $title, $description, $enabled;


    function banktransfer() {
      global $order;

      $this->code = 'banktransfer';
      $this->title = MODULE_PAYMENT_BANKTRANSFER_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_BANKTRANSFER_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER;
      $this->min_order = MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER;
      $this->enabled = ((MODULE_PAYMENT_BANKTRANSFER_STATUS == 'True') ? true : false);
$this->info=MODULE_PAYMENT_BANKTRANSFER_TEXT_INFO;
      if ((int)MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID;
      }
      if (is_object($order)) $this->update_status();

      if ($_POST['banktransfer_fax'] == "on")
        $this->email_footer = MODULE_PAYMENT_BANKTRANSFER_TEXT_EMAIL_FOOTER;
    }


    function update_status() {
      global $order;


       $check_order_query = os_db_query("select count(*) as count from " . TABLE_ORDERS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
       $order_check = os_db_fetch_array($check_order_query);


       if ($order_check['count'] < MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER) {
       $check_flag = false;
       $this->enabled = false;

       }else{
       $check_flag = true;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_BANKTRANSFER_ZONE > 0) ) {
        $check_flag = false;
        $check_query = os_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_BANKTRANSFER_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = os_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }
      }
        if ($check_flag == false) {
          $this->enabled = false;
        }
      }

    }

    function javascript_validation() {
      $js = 'if (payment_value == "' . $this->code . '") {' . "\n" .
            '  var banktransfer_blz = document.getElementById("checkout_payment").banktransfer_blz.value;' . "\n" .
            '  var banktransfer_number = document.getElementById("checkout_payment").banktransfer_number.value;' . "\n" .
            '  var banktransfer_owner = document.getElementById("checkout_payment").banktransfer_owner.value;' . "\n" .
            '  if (document.getElementById("checkout_payment").banktransfer_fax) { ' . "\n" .
            '    var banktransfer_fax = document.getElementById("checkout_payment").banktransfer_fax.checked;' . "\n" .
            '  } else { var banktransfer_fax = false; } ' . "\n" .            
            '  if (banktransfer_fax == false) {' . "\n" .
            '    if (banktransfer_blz == "") {' . "\n" .
            '      error_message = error_message + "' . JS_BANK_BLZ . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (banktransfer_number == "") {' . "\n" .
            '      error_message = error_message + "' . JS_BANK_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (banktransfer_owner == "") {' . "\n" .
            '      error_message = error_message + "' . JS_BANK_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n" .
            '}' . "\n";
      return $js;
    }

    function selection() {
      global $order;



      $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'description'=>$this->info,
      	                 'fields' => array(array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE,
      	                                         'field' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_INFO),
      	                                   array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER,
      	                                         'field' => os_draw_input_field('banktransfer_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
      	                                   array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BLZ,
      	                                         'field' => os_draw_input_field('banktransfer_blz', $_GET['banktransfer_blz'], 'size="8" maxlength="8"')),
      	                                   array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NUMBER,
      	                                         'field' => os_draw_input_field('banktransfer_number',  $_GET['banktransfer_number'], 'size="16" maxlength="32"')),
      	                                   array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NAME,
      	                                         'field' => os_draw_input_field('banktransfer_bankname')),
      	                                   array('title' => '',
      	                                         'field' => os_draw_hidden_field('recheckok', $_POST['recheckok']))
      	                                   ));

      if (MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION =='true'){
        $selection['fields'][] = array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE,
      	                               'field' => MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE2 . '<a href="' . MODULE_PAYMENT_BANKTRANSFER_URL_NOTE . '" target="_blank"><b>' . MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE3 . '</b></a>' . MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE4);
      	$selection['fields'][] = array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_FAX,
      	                               'field' => os_draw_checkbox_field('banktransfer_fax', 'on'));

      }

      return $selection;
    }

    function pre_confirmation_check(){


      if ($_POST['banktransfer_fax'] == false  && $_POST['recheckok'] != 'true') {
        include(dir_path('class') . 'banktransfer_validation.php');

        $banktransfer_validation = new AccountCheck;
        $banktransfer_result = $banktransfer_validation->CheckAccount($_POST['banktransfer_number'], $_POST['banktransfer_blz']);


		if ($banktransfer_validation->Bankname != '') {
                       $this->banktransfer_bankname =  $banktransfer_validation->Bankname;
                } else {
                         $this->banktransfer_bankname = os_db_prepare_input($_POST['banktransfer_bankname']);
                }

                if ($_POST['banktransfer_owner'] == '') {
                        $banktransfer_result = 10;
                }

          
          
          switch ($banktransfer_result) {
                        case 0: // payment o.k.
                                $error = 'O.K.';
                                $recheckok = 'false';
                                break;
                        case 1: // number & blz not ok
                                $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1;
                                $recheckok = 'false';
                                break;
                        case 2: // account number has no calculation method
                                $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2;
                                $recheckok = 'true';
                                break;
                        case 3: // No calculation method implemented
                                $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_3;
                                $recheckok = 'true';
                                break;
                        case 4: // Number cannot be checked
                                $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_4;
                                $recheckok = 'true';
                                break;
                        case 5: // BLZ not found
                                $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_5;
                                $recheckok = 'false'; // Set "true" if you have not the latest BLZ table!
                                break;
                        case 8: // no BLZ entered
                                $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_8;
                                $recheckok = 'false';
                                break;
                        case 9: // no number entered
                                $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_9;
                                $recheckok = 'false';
                                break;
                        case 10: // no account holder entered
                                $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_10;
                                $recheckok = 'false';
                                break;
                        case 128: // Internal error
                                $error = 'Internal error, please check again to process your payment';
                                $recheckok = 'true';
                                break;
                        default:
                                $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_4;
                                $recheckok = 'true';
                                break;
                }
         
          

          if ($banktransfer_result > 0 && $_POST['recheckok'] != 'true') {
            $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&banktransfer_owner=' . urlencode($_POST['banktransfer_owner']) . '&banktransfer_number=' . urlencode($_POST['banktransfer_number']) . '&banktransfer_blz=' . urlencode($_POST['banktransfer_blz']) . '&banktransfer_bankname=' . urlencode($_POST['banktransfer_bankname']) . '&recheckok=' . $recheckok;
            os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
          }
     	$this->banktransfer_owner = os_db_prepare_input($_POST['banktransfer_owner']);
        $this->banktransfer_blz = os_db_prepare_input($_POST['banktransfer_blz']);
        $this->banktransfer_number = os_db_prepare_input($_POST['banktransfer_number']);
        $this->banktransfer_prz = $banktransfer_validation->PRZ;
        $this->banktransfer_status = $banktransfer_result;

      }
    }
    function confirmation() {
      global $banktransfer_val, $banktransfer_owner, $banktransfer_bankname, $banktransfer_blz, $banktransfer_number, $checkout_form_action, $checkout_form_submit;

      if (!$_POST['banktransfer_owner'] == '') {
        $confirmation = array('title' => $this->title,
                              'fields' => array(array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER,
                                                      'field' => $this->banktransfer_owner),
                                                array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BLZ,
                                                      'field' => $this->banktransfer_blz),
                                                array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NUMBER,
                                                      'field' => $this->banktransfer_number),
                                                array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NAME,
                                                      'field' => $this->banktransfer_bankname)
                                                ));
      }
      if ($_POST['banktransfer_fax'] == "on") {
        $confirmation = array('fields' => array(array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_FAX)));
        $this->banktransfer_fax = "on";
      }
      return $confirmation;
    }

    function process_button() {
      global $_POST;

      $process_button_string = os_draw_hidden_field('banktransfer_blz', $this->banktransfer_blz) .
                               os_draw_hidden_field('banktransfer_bankname', $this->banktransfer_bankname).
                               os_draw_hidden_field('banktransfer_number', $this->banktransfer_number) .
                               os_draw_hidden_field('banktransfer_owner', $this->banktransfer_owner) .
                               os_draw_hidden_field('banktransfer_status', $this->banktransfer_status) .
                               os_draw_hidden_field('banktransfer_prz', $this->banktransfer_prz) .
                               os_draw_hidden_field('banktransfer_fax', $this->banktransfer_fax);

      return $process_button_string;

    }

    function before_process() {
    	//fp implement checking the post vars
    	 $this->pre_confirmation_check();
    	 $this->banktransfer_bankname = os_db_prepare_input($_POST['banktransfer_bankname']);
    	 $this->banktransfer_fax = os_db_prepare_input($_POST['banktransfer_fax']);
    	return false;
    }

    function after_process() {
      global $insert_id, $_POST, $banktransfer_val, $banktransfer_owner, $banktransfer_bankname, $banktransfer_blz, $banktransfer_number, $banktransfer_status, $banktransfer_prz, $banktransfer_fax, $checkout_form_action, $checkout_form_submit;
      os_db_query("INSERT INTO os_banktransfer (orders_id, banktransfer_blz, banktransfer_bankname, banktransfer_number, banktransfer_owner, banktransfer_status, banktransfer_prz) VALUES ('" . $insert_id . "', '" . $this->banktransfer_blz . "', '" . $this->banktransfer_bankname . "', '" . $this->banktransfer_number . "', '" . $this->banktransfer_owner ."', '" . $this->banktransfer_status ."', '" . $this->banktransfer_prz ."')");
      if ($_POST['banktransfer_fax'])
        os_db_query("update os_banktransfer set banktransfer_fax = '" . $this->banktransfer_fax ."' where orders_id = '" . $insert_id . "'");
        if ($this->order_status) os_db_query("UPDATE ". TABLE_ORDERS ." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
    }

    function get_error() {

      $error = array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR,
                     'error' => stripslashes(urldecode($_GET['error'])));

      return $error;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_BANKTRANSFER_STATUS'");
        $this->_check = os_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_STATUS', 'True', '6', '1', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_ZONE', '0',  '6', '2', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
	  os_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_ALLOWED', '', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER', '0', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID', '0',  '6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION', 'false',  '6', '2', 'os_cfg_select_option(array(\'true\', \'false\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ', 'false', '6', '0', 'os_cfg_select_option(array(\'true\', \'false\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_URL_NOTE', 'fax.html', '6', '0', now())");
      os_db_query("CREATE TABLE IF NOT EXISTS os_banktransfer (orders_id int(11) NOT NULL default '0', banktransfer_owner varchar(64) default NULL, banktransfer_number varchar(24) default NULL, banktransfer_bankname varchar(255) default NULL, banktransfer_blz varchar(8) default NULL, banktransfer_status int(11) default NULL, banktransfer_prz char(2) default NULL, banktransfer_fax char(2) default NULL, KEY orders_id(orders_id))");
      os_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER', '0',  '6', '0', now())");
    }


    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_BANKTRANSFER_STATUS','MODULE_PAYMENT_BANKTRANSFER_ALLOWED', 'MODULE_PAYMENT_BANKTRANSFER_ZONE', 'MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID', 'MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER', 'MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ', 'MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION', 'MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER', 'MODULE_PAYMENT_BANKTRANSFER_URL_NOTE');
    }
  }
?>