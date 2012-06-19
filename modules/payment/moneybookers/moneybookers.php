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

class moneybookers {
	var $code, $title, $description, $enabled, $email_footer, $auth_num, $transaction_id;
	var $mbLanguages, $mbCurrencies, $aCurrencies, $defCurr, $defLang;

	// class constructor
	function moneybookers() {
		global $order, $language;

		$this->code = 'moneybookers';
		$this->title = MODULE_PAYMENT_MONEYBOOKERS_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_MONEYBOOKERS_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_MONEYBOOKERS_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_MONEYBOOKERS_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_MONEYBOOKERS_TEXT_INFO;
		$this->icon_small = 'icon_small.png';

		$this->auth_num = '';
		$this->transaction_id = '';

		$this->mbLanguages = array ("EN", "DE", "ES", "FR");

		$result = os_db_query("SELECT mb_currID FROM " . TABLE_MONEYBOOKERS_CURRENCIES . "");
		while (list ($currID) = mysql_fetch_row($result)) {
			$this->mbCurrencies[] = $currID;
		}

		$result = os_db_query("SELECT code FROM " . TABLE_CURRENCIES . "");
		while (list ($currID) = mysql_fetch_row($result)) {
			$this->aCurrencies[] = $currID;
		}

		$result = os_db_query("SELECT configuration_value FROM ".TABLE_CONFIGURATION." WHERE configuration_key = 'DEFAULT_CURRENCY'");
		list ($this->defCurr) = mysql_fetch_row($result);

		$result = os_db_query("SELECT configuration_value FROM ".TABLE_CONFIGURATION." WHERE configuration_key = 'DEFAULT_LANGUAGE'");
		list ($this->defLang) = mysql_fetch_row($result);
		$this->defLang = strtoupper($this->defLang);
		if (!in_array($this->defLang, $this->mbLanguages)) {
			$this->defLang = "EN";
		}

		if ((int) MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID;
		}

		if (is_object($order))
			$this->update_status();

		$this->form_action_url = 'https://www.moneybookers.com/app/payment.pl';
	}

	////
	// Status update
	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_MONEYBOOKERS_ZONE > 0)) {
			$check_flag = false;
			$check_query = os_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_IEB_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
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

	// class methods
	function javascript_validation() {
		return false;
	}

	function selection() {
		return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return false;
	}

	function process_button() {
		global $order, $order_total_modules, $currency, $languages_id, $osPrice;

		$result = os_db_query("SELECT code FROM " . TABLE_LANGUAGES . " WHERE languages_id = '".$_SESSION['languages_id']."'");
		list ($lang_code) = mysql_fetch_row($result);
		$mbLanguage = strtoupper($lang_code);
		if ($mbLanguage == "US") {
			$mbLanguage = "EN";
		}
		if (!in_array($mbLanguage, $this->mbLanguages)) {
			$mbLanguage = MODULE_PAYMENT_MONEYBOOKERS_LANGUAGE;
		}

		$mbCurrency = $currency;
		if (!in_array($currency, $this->mbCurrencies)) {
			$mbCurrency = MODULE_PAYMENT_MONEYBOOKERS_CURRENCY;
		}

		$result = os_db_query("SELECT mb_cID FROM " . TABLE_MONEYBOOKERS_COUNTRIES . ", " . TABLE_COUNTRIES . " WHERE (osc_cID = countries_id) AND (countries_id = '{$order->billing['country']['id']}')");
		list ($mbCountry) = mysql_fetch_row($result);

		$this->transaction_id = $this->generate_trid();
		$result = os_db_query("INSERT INTO " . TABLE_MONEYBOOKERS . " (mb_TRID, mb_DATE) VALUES ('{$this->transaction_id}', NOW())");
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}
		if ($_SESSION['currency'] == $mbCurrency) {
			$amount = round($total, $osPrice->get_decimal_places($mbCurrency));
		} else {
			$amount = round($osPrice->CalculateCurrEx($total, $mbCurrency), $osPrice->get_decimal_places($mbCurrency));
		}

		$process_button_string = os_draw_hidden_field('pay_to_email', MODULE_PAYMENT_MONEYBOOKERS_EMAILID).os_draw_hidden_field('transaction_id', $this->transaction_id).os_draw_hidden_field('return_url', os_href_link(FILENAME_CHECKOUT_PROCESS, 'trid='.$this->transaction_id, 'NONSSL', false)).os_draw_hidden_field('cancel_url', os_href_link(FILENAME_CHECKOUT_PAYMENT, MODULE_PAYMENT_MONEYBOOKERS_ERRORTEXT1.$this->code.MODULE_PAYMENT_MONEYBOOKERS_ERRORTEXT2, 'SSL', true, false)).os_draw_hidden_field('status_url', 'mailto:'.MODULE_PAYMENT_MONEYBOOKERS_EMAILID).os_draw_hidden_field('language', $mbLanguage).os_draw_hidden_field('pay_from_email', $order->customer['email_address']).os_draw_hidden_field('amount', $amount).os_draw_hidden_field('currency', $mbCurrency).os_draw_hidden_field('detail1_description', STORE_NAME).os_draw_hidden_field('detail1_text', MODULE_PAYMENT_MONEYBOOKERS_ORDER_TEXT.strftime(DATE_FORMAT_LONG)).os_draw_hidden_field('firstname', $order->billing['firstname']).os_draw_hidden_field('lastname', $order->billing['lastname']).os_draw_hidden_field('address', $order->billing['street_address']).os_draw_hidden_field('postal_code', $order->billing['postcode']).os_draw_hidden_field('city', $order->billing['city']).os_draw_hidden_field('state', $order->billing['state']).os_draw_hidden_field('country', $mbCountry).os_draw_hidden_field('confirmation_note', MODULE_PAYMENT_MONEYBOOKERS_CONFIRMATION_TEXT);

		if (ereg("[0-9]{6}", MODULE_PAYMENT_MONEYBOOKERS_REFID)) {
			$process_button_string .= os_draw_hidden_field('rid', MODULE_PAYMENT_MONEYBOOKERS_REFID);
		}

		// moneyboocers.com payment gateway does not accept accented characters!
		// Please feel free to add any other accented characters to the list.
		return strtr($process_button_string, "бйнуцхъьыБЙНУЦХЪЬЫ", "aeiooouuuAEIOOOUUU");
	}

	// manage returning data from moneybookers (errors, failures, success etc.)
	function before_process() {

		global $order, $_GET;

		$this->transaction_id = $_GET["trid"];
		$md5_pwd = md5(MODULE_PAYMENT_MONEYBOOKERS_PWD);
		$queryURL = "https://www.moneybookers.com/app/query.pl?email=".MODULE_PAYMENT_MONEYBOOKERS_EMAILID."&password=".$md5_pwd."&action=status_trn&trn_id=".$this->transaction_id;

		$ch = curl_init($queryURL);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);

		$result = urldecode($result);

		/********************************/
		// get the returned error code
		// 200 -- OK
		// 401 -- Login failed 
		// 402 -- Unknown action
		// 403 -- Transaction not found
		// 404 -- Missing parameter
		// 405 -- Illegal parameter value
		/********************************/
		preg_match("/\d{3}/", $result, $return_code);

		switch ($return_code[0]) {

			// query was OK, data is sent back
			case "200" :
				$result = strstr($result, "status");
				$aResult = explode("&", $result);

				/***********************************************************/
				// get the returned data
				// [status] -- (-2) => failed
				//             ( 2) => processed
				//             ( 1) => scheduled (eg. offline bank transfer)
				// [mb_transaction_id] -- transaction ID at moneybookers.com
				/***********************************************************/
				foreach ($aResult as $value) {
					list ($parameter, $pVal) = explode("=", $value);
					$aFinal["$parameter"] = $pVal;
				}

				if ($aFinal["status"] == -2) {
					$result = os_db_query("UPDATE " . TABLE_MONEYBOOKERS . " SET mb_ERRNO = '999', mb_ERRTXT = 'Transaction failed.', mb_MBTID = {$aFinal['mb_transaction_id']}, mb_STATUS = {$aFinal['status']} WHERE mb_TRID = '{$this->transaction_id}'");
					$payment_error_return = "payment_error={$this->code}&error=-2: ".MODULE_PAYMENT_MONEYBOOKERS_TRANSACTION_FAILED_TEXT;
					os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
					return false;
				} else {
					$result = os_db_query("UPDATE " . TABLE_MONEYBOOKERS . " SET mb_ERRNO = '200', mb_ERRTXT = 'OK', mb_MBTID = {$aFinal['mb_transaction_id']}, mb_STATUS = {$aFinal['status']} WHERE mb_TRID = '{$this->transaction_id}'");
					$moneybookers_payment_comment = MODULE_PAYMENT_MONEYBOOKERS_ORDER_COMMENT1.$aFinal["mb_transaction_id"]." (".MODULE_PAYMENT_MONEYBOOKERS_ORDER_COMMENT2.") ";
					$order->info['comments'] = $moneybookers_payment_comment.$order->info['comments'];
				}

				break;

				// error occured during query
				// errors documented in the moneybookers doc
			case "401" :
			case "402" :
			case "403" :
			case "404" :
			case "405" :
				preg_match("/[^\d\t]+.*/i", $result, $return_array);
				$result = os_db_query("UPDATE " . TABLE_MONEYBOOKERS . " SET mb_ERRNO = '{$return_code[0]}', mb_ERRTXT = '{$return_array[0]}' WHERE mb_TRID = '{$this->transaction_id}'");
				$payment_error_return = "payment_error={$this->code}&error={$return_code[0]}: {$return_array[0]}";
				os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
				break;

				// unknown error
			default :
				$payment_error_return = "payment_error={$this->code}&error=000: Unknown error!";
				os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
				break;

		}

	}

	function after_process() {
		global $insert_id;
		$result = os_db_query("UPDATE " . TABLE_MONEYBOOKERS . " SET mb_ORDERID = $insert_id WHERE mb_TRID = '{$this->transaction_id}'");

		if ($this->order_status)
			os_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");

	}

	function get_error() {
		global $_GET;

		$error = array ('title' => MODULE_PAYMENT_MONEYBOOKERS_TEXT_ERROR, 'error' => stripslashes(urldecode($_GET['error'])));

		return $error;
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = os_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_MONEYBOOKERS_STATUS'");
			$this->_check = os_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {

		if (!$this->check_currency($this->aCurrencies)) {
			$this->enabled = false;
			$install_error_return = 'set=payment&module=moneybookers&error='.MODULE_PAYMENT_MONEYBOOKERS_NOCURRENCY_ERROR;
			os_redirect(os_href_link(FILENAME_MODULES, $install_error_return, 'SSL', true, false));
			return false;
		}
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_STATUS', 'True',  '6', '0', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_EMAILID', '', '6', '1', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_PWD', '',  '6', '2', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_REFID', '', '6', '3', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_SORT_ORDER', '0',  '6', '4', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CURRENCY', '".$this->defCurr."', '6', '5', 'os_cfg_select_option(".$this->show_array($this->aCurrencies)."), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_LANGUAGE', '".$this->defLang."', '6', '6', 'os_cfg_select_option(".$this->show_array($this->mbLanguages)."), ', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_ZONE', '0',  '6', '7', 'os_get_zone_class_title', 'os_cfg_pull_down_zone_classes(', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID', '0',  '6', '8', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
		os_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_ALLOWED', '', '6', '0', now())");
	}

	function remove() {
		os_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys() {
		return array ('MODULE_PAYMENT_MONEYBOOKERS_STATUS', 'MODULE_PAYMENT_MONEYBOOKERS_EMAILID', 'MODULE_PAYMENT_MONEYBOOKERS_PWD', 'MODULE_PAYMENT_MONEYBOOKERS_REFID', 'MODULE_PAYMENT_MONEYBOOKERS_LANGUAGE', 'MODULE_PAYMENT_MONEYBOOKERS_CURRENCY', 'MODULE_PAYMENT_MONEYBOOKERS_SORT_ORDER', 'MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID', 'MODULE_PAYMENT_MONEYBOOKERS_ZONE');
	}

	// If there is no moneybookers accepted currency configured with the shop
	// do not allow the moneybookers payment module installation
	function check_currency($availableCurr) {
		$foundCurr = false;
		foreach ($availableCurr as $currID) {
			if (in_array($currID, $this->mbCurrencies)) {
				$foundCurr = true;
			}
		}
		return $foundCurr;
	}

	// Parse the predefinied array to be 'module install' friendly
	// as it is used for select in the module's install() function
	function show_array($aArray) {
		$aFormatted = "array(";
		foreach ($aArray as $key => $sVal) {
			$aFormatted .= "\'$sVal\', ";
		}
		$aFormatted = substr($aFormatted, 0, strlen($aFormatted) - 2);
		return $aFormatted;
	}

	function generate_trid() {

		do {
			$trid = os_create_random_value(16, "mixed");
			$result = os_db_query("SELECT mb_TRID FROM " . TABLE_MONEYBOOKERS . " WHERE mb_TRID = '$trid'");
		} while (mysql_num_rows($result));

		return $trid;

	}
}
?>