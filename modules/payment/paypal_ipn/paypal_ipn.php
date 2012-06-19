<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software
#  Copyright (c) 2011-2012
# http://osc-cms.com
# Ver. 1.0.0
#####################################
*/
 
class paypal_ipn {
	var $code, $title, $description, $enabled, $identifier;
	
	function paypal_ipn() {
		global $order;
		
		$this->code = 'paypal_ipn';
		$this->title = MODULE_PAYMENT_PAYPAL_IPN_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_PAYPAL_IPN_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_PAYPAL_IPN_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_PAYPAL_IPN_STATUS == 'True') ? true : false);
		$this->identifier = 'OSC-CMS PayPal IPN v1.0';
		
		if ((int)MODULE_PAYMENT_PAYPAL_IPN_PREPARE_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_PAYPAL_IPN_PREPARE_ORDER_STATUS_ID;
		}
		
		if (is_object($order))
			$this->update_status();
			
		$this->email_footer = MODULE_PAYMENT_PAYPAL_IPN_TEXT_EMAIL_FOOTER;
		
		if (MODULE_PAYMENT_PAYPAL_IPN_GATEWAY_SERVER == 'Live') {
			$this->form_action_url = 'https://www.paypal.com/cgi-bin/webscr';
		}else{
			$this->form_action_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}
	}

        
    function update_status() {
    	global $order;
    	
    	if (($this->enabled == true) && ((int)MODULE_PAYMENT_PAYPAL_IPN_ZONE > 0)) {
    		$check_flag = false;
    		$check_query = os_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_PAYPAL_IPN_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
    		
    		while ($check = os_db_fetch_array($check_query)) {
    			if ($check['zone_id'] < 1) {
    				$check_flag = true;
    				break;
    			}
    			elseif ($check['zone_id'] == $order->billing['zone_id']) {
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
    	return false;
    }
    
    
    function selection() {
    	return array('id' => $this->code,
    				 'module' => $this->title);
    }
    
    
    function pre_confirmation_check() {
    	return false;
    }
    
    
    function confirmation() {
    	global $cartID;
    	
    	if (os_session_is_registered('cartID')) {
				os_session_register('order_ident_key');
				$_SESSION['order_ident_key'] = os_input_validation(md5($_SESSION['cartID'] . '-' . $_SESSION['customer_id']),int,'');
		}
						
		return array('title' => MODULE_PAYMENT_PAYPAL_IPN_TEXT_DESCRIPTION );
	}


	function process_button() {
		global $order, $osPrice;
		
		if (MODULE_PAYMENT_PAYPAL_CURRENCY == 'Selected Currency') {
			$my_currency = $_SESSION['currency'];
		} else {
			$my_currency = substr(MODULE_PAYMENT_PAYPAL_CURRENCY, 5);
		}
		if (!in_array($my_currency, array ('CAD', 'EUR', 'GBP', 'JPY', 'USD'))) {
			$my_currency = 'EUR';
		}
		
		$parameters = array();
		
		$parameters['cmd'] = '_xclick';
		$parameters['item_name'] = STORE_NAME;

					
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$parameters['amount'] = $order->info['total'] + $order->info['tax'];
		} else {
			$parameters['amount'] = $order->info['total'];
		}
		if ($_SESSION['currency'] == $my_currency) {
			$parameters['shipping'] = round($order->info['shipping_cost'], $osPrice->get_decimal_places($my_currency));
			$parameters['amount'] = round($parameters['amount'], $osPrice->get_decimal_places($my_currency)) - $parameters['shipping'];
		} else {
			$parameters['shipping'] = round($osPrice->CalculateCurrEx($order->info['shipping_cost'], $my_currency), $osPrice->get_decimal_places($my_currency));
			$parameters['amount'] = round($osPrice->CalculateCurrEx($parameters['amount'], $my_currency), $osPrice->get_decimal_places($my_currency)) - $parameters['shipping'];
		}
	
		$parameters['business'] = MODULE_PAYMENT_PAYPAL_IPN_ID;
		$parameters['currency_code'] = $my_currency;
		$parameters['invoice'] = $_SESSION['order_ident_key'];
		$parameters['custom'] = $_SESSION['customer_id'];
		$parameters['no_shipping'] = '1';
		$parameters['no_note'] = '1';
		$parameters['notify_url'] = os_href_link('ipn.php', '', 'SSL', false, false);
		$parameters['return'] = os_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
		$parameters['cancel_return'] = os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');
		
		//  Add missing variables to prepopulate PayPal form. -- hostmistress 20050210
		$parameters['first_name'] = $order->billing['firstname'];
		$parameters['last_name'] = $order->billing['lastname'];
		$parameters['address1'] = $order->billing['street_address'];
		$parameters['address2'] = $order->billing['suburb'];
		$parameters['email'] = $order->customer['email_address'];
		$parameters['night_phone_a'] = $order->customer['telephone'];
		$parameters['city'] = $order->billing['city'];
		
		if ($order->billing['country']['iso_code_2']=='US') {
			$order->billing['state'] = os_get_zone_code($order->billing['country_id'], $order->billing['zone_id'], $order->billing['state']);
        }
        
        $parameters['state'] = $order->billing['state'];
        $parameters['zip'] = $order->billing['postcode'];
        $parameters['country'] = $order->billing['country']['iso_code_2'];
        
       
        $parameters['bn'] = $this->identifier;
        
        if(os_not_null(MODULE_PAYMENT_PAYPAL_IPN_PAGE_STYLE)) {
        	$parameters['page_style'] = MODULE_PAYMENT_PAYPAL_IPN_PAGE_STYLE;
        }
        
        if (MODULE_PAYMENT_PAYPAL_IPN_EWP_STATUS == 'True') {
        	$parameters['cert_id'] = MODULE_PAYMENT_PAYPAL_IPN_EWP_CERT_ID;
        	$random_string = rand(100000, 999999) . '-' . $customer_id . '-';
			$data = '';
			
			while (list($key, $value) = each($parameters)) {
				$data .= $key . '=' . $value . "\n";
			}
			
			$fp = fopen(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt', 'w');
			fwrite($fp, $data);
			fclose($fp);
			
			unset($data);
			
			if (function_exists('openssl_pkcs7_sign') && function_exists('openssl_pkcs7_encrypt')) {
				openssl_pkcs7_sign(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt', MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt', file_get_contents(MODULE_PAYMENT_PAYPAL_IPN_EWP_PUBLIC_KEY), file_get_contents(MODULE_PAYMENT_PAYPAL_IPN_EWP_PRIVATE_KEY), array('From' => MODULE_PAYMENT_PAYPAL_IPN_ID), PKCS7_BINARY);
				unlink(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt');
				
				// remove headers from the signature
				$signed = file_get_contents(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');
				$signed = explode("\n\n", $signed);
				$signed = base64_decode($signed[1]);
				
				$fp = fopen(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt', 'w');
				fwrite($fp, $signed);
				fclose($fp);
				
				unset($signed);
				
				openssl_pkcs7_encrypt(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt', MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt', file_get_contents(MODULE_PAYMENT_PAYPAL_IPN_EWP_PAYPAL_KEY), array('From' => MODULE_PAYMENT_PAYPAL_IPN_ID), PKCS7_BINARY);
				
				unlink(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');
				
				// remove headers from the encrypted result
				
				$data = file_get_contents(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
				$data = explode("\n\n", $data);
				$data = '-----BEGIN PKCS7-----' . "\n" . $data[1] . "\n" . '-----END PKCS7-----';
				
				unlink(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
			}else{
				exec(MODULE_PAYMENT_PAYPAL_IPN_EWP_OPENSSL . ' smime -sign -in ' . MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt -signer ' . MODULE_PAYMENT_PAYPAL_IPN_EWP_PUBLIC_KEY . ' -inkey ' . MODULE_PAYMENT_PAYPAL_IPN_EWP_PRIVATE_KEY . ' -outform der -nodetach -binary > ' . MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');
				unlink(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt');
				
				exec(MODULE_PAYMENT_PAYPAL_IPN_EWP_OPENSSL . ' smime -encrypt -des3 -binary -outform pem ' . MODULE_PAYMENT_PAYPAL_IPN_EWP_PAYPAL_KEY . ' < ' . MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt > ' . MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
				unlink(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');
				
				$fh = fopen(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt', 'rb');
				$data = fread($fh, filesize(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt'));
				fclose($fh);
				
				unlink(MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
			}
			
			$process_button_string = os_draw_hidden_field('cmd', '_s-xclick') . os_draw_hidden_field('encrypted', $data);
			
			unset($data);
		}else{
			while (list($key, $value) = each($parameters)) {
				$process_button_string.=os_draw_hidden_field($key, $value);
			}
		}
		
		return $process_button_string;
	}


	function before_process() {
		return false;
	}


	function after_process() {
		global $insert_id;
  	         if ($this->order_status)
  	         	os_db_query("UPDATE ". TABLE_ORDERS ." SET orders_status='0' WHERE orders_id='".$insert_id."'");
  	         	os_db_query("UPDATE ". TABLE_ORDERS_STATUS_HISTORY ." SET orders_status='0' WHERE orders_id='".$insert_id."'");
	}
	
	
	function output_error() {
		return false;
	}
	
	
	function check() {
		if(!isset($this->_check)) {
			$check_query = os_db_query("SELECT configuration_value
										 FROM " . TABLE_CONFIGURATION . " 
										 WHERE configuration_key = 'MODULE_PAYMENT_PAYPAL_IPN_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}
	
	
	function install() {
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_STATUS', 'False', '6', '3', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
 		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added)
					  VALUES ('MODULE_PAYMENT_PAYPAL_IPN_ID', '', '6', '4', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)
					  VALUES ('MODULE_PAYMENT_PAYPAL_IPN_CURRENCY', 'Only EUR', '6', '6', 'os_cfg_select_option(array(\'Selected Currency\',\'Only AUD\',\'Only USD\',\'Only CAD\',\'Only EUR\',\'Only GBP\',\'Only JPY\'), ', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)
					  VALUES ('MODULE_PAYMENT_PAYPAL_IPN_SORT_ORDER', '0', '6', '0', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) 
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_ALLOWED', '', '6', '0', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) 
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_ZONE', '0', '6', '2', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added)
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_PREPARE_ORDER_STATUS_ID', '0', '6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added)
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_ORDER_STATUS_ID', '0', '6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id,sort_order, set_function, use_function, date_added)
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_DENIED_ORDER_STATUS_ID', '0', '6', '0', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) 
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_GATEWAY_SERVER', 'Testing', '6', '6', 'os_cfg_select_option(array(\'Testing\',\'Live\'), ', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_PAGE_STYLE', '', '6', '4', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) 
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_DEBUG_EMAIL', '', '6', '4', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) 
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_EWP_STATUS', 'False', '6', '3', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) 
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_EWP_PRIVATE_KEY', '', '6', '4', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_EWP_PUBLIC_KEY', '', '6', '4', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) 
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_EWP_PAYPAL_KEY', '', '6', '4', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_EWP_CERT_ID', '', '6', '4', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)
                      VALUES ('MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY', '/tmp', '6', '4', now())");
		os_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) 
					  VALUES ('MODULE_PAYMENT_PAYPAL_IPN_EWP_OPENSSL', '/usr/bin/openssl', '6', '4', now())");
		os_db_query("ALTER TABLE " . TABLE_ORDERS . " ADD paypal_ipn_success INT( 1 ) DEFAULT '0' NOT NULL");
	}


	function remove() {
		os_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");
		os_db_query("ALTER TABLE " . TABLE_ORDERS . " DROP paypal_ipn_success");
	}


	function keys() {
		return array('MODULE_PAYMENT_PAYPAL_IPN_STATUS', 
					 'MODULE_PAYMENT_PAYPAL_IPN_ALLOWED', 
					 'MODULE_PAYMENT_PAYPAL_IPN_ID', 
					 'MODULE_PAYMENT_PAYPAL_IPN_CURRENCY', 
					 'MODULE_PAYMENT_PAYPAL_IPN_ZONE', 
					 'MODULE_PAYMENT_PAYPAL_IPN_PREPARE_ORDER_STATUS_ID', 
					 'MODULE_PAYMENT_PAYPAL_IPN_ORDER_STATUS_ID', 
					 'MODULE_PAYMENT_PAYPAL_IPN_DENIED_ORDER_STATUS_ID', 
					 'MODULE_PAYMENT_PAYPAL_IPN_GATEWAY_SERVER', 
					 'MODULE_PAYMENT_PAYPAL_IPN_PAGE_STYLE', 
					 'MODULE_PAYMENT_PAYPAL_IPN_DEBUG_EMAIL', 
					 'MODULE_PAYMENT_PAYPAL_IPN_SORT_ORDER', 
					 'MODULE_PAYMENT_PAYPAL_IPN_EWP_STATUS', 
					 'MODULE_PAYMENT_PAYPAL_IPN_EWP_PRIVATE_KEY', 
					 'MODULE_PAYMENT_PAYPAL_IPN_EWP_PUBLIC_KEY', 
					 'MODULE_PAYMENT_PAYPAL_IPN_EWP_PAYPAL_KEY', 
					 'MODULE_PAYMENT_PAYPAL_IPN_EWP_CERT_ID', 
					 'MODULE_PAYMENT_PAYPAL_IPN_EWP_WORKING_DIRECTORY', 
					 'MODULE_PAYMENT_PAYPAL_IPN_EWP_OPENSSL');
	} // function keys()
}
?>