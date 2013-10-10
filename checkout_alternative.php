<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include ('includes/top.php');

if (isset ($_SESSION['customer_id'])) {
		os_redirect(os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}

// check if checkout is allowed
if ($_SESSION['allow_checkout'] == 'false')
	os_redirect(os_href_link(FILENAME_SHOPPING_CART));
	

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1) {
	os_redirect(os_href_link(FILENAME_SHOPPING_CART));
}

	
// create smarty elements
//$osTemplate = new osTemplate;

$total_weight = $_SESSION['cart']->show_weight();
$total_count = $_SESSION['cart']->count_contents_virtual(); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM
// include boxes


require (_CLASS . 'shipping.php');
require (_CLASS . 'payment.php');

$breadcrumb->add(TEXT_CHECKOUT_ALTERNATIVE);

$osTemplate->assign('FORM_ACTION', os_draw_form('checkout_alternative', os_href_link(FILENAME_CHECKOUT_ALTERNATIVE, '', 'SSL'), 'post', 'onsubmit="return checkform(this);"').os_draw_hidden_field('action', 'process') . os_draw_hidden_field('required', 'gender,firstname,lastname,dob,email,address,postcode,city,state,country,telephone,pass,confirmation', 'id="required"'));
$osTemplate->assign('ADDRESS_LABEL', os_address_label($_SESSION['customer_id'], $_SESSION['sendto'], true, ' ', '<br />'));
$osTemplate->assign('FORM_END', '</form>');


$osTemplate->assign('virtual', 'false');

if ($_SESSION['cart']->content_type == 'virtual' || ($_SESSION['cart']->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_virtual() == 0)) { 

	$_SESSION['shipping'] = false;
	$_SESSION['sendto'] = false;

$osTemplate->assign('virtual', 'true');

}

$process = false;
if (isset ($_POST['action']) && ($_POST['action'] == 'process')) {
	$process = true;
$_SESSION['wm'] = $_POST['wm'];

if (isset($_POST['conditions'])) {
	$_SESSION['conditions'] = true;
}

$_SESSION['comments'] = os_db_prepare_input($_POST['comments']);

$shipping_modules = new shipping;

if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
	switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
		case 'national' :
			if ($order->delivery['country_id'] == STORE_COUNTRY)
				$pass = true;
			break;
		case 'international' :
			if ($order->delivery['country_id'] != STORE_COUNTRY)
				$pass = true;
			break;
		case 'both' :
			$pass = true;
			break;
		default :
			$pass = false;
			break;
	}

	$free_shipping = false;
	if (($pass == true) && ($order->info['total'] - $order->info['shipping_cost'] >= $osPrice->Format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, false, 0, true))) {
		$free_shipping = true;

		include (_MODULES.'order_total/ot_shipping/'.$_SESSION['language'].'.php');
	}
} else {
	$free_shipping = false;
}
// process the selected shipping method

	if ((os_count_shipping_modules() > 0) || ($free_shipping == true)) {
		if ((isset ($_POST['shipping'])) && (strpos($_POST['shipping'], '_'))) {
			$_SESSION['shipping'] = $_POST['shipping'];

			list ($module, $method) = explode('_', $_SESSION['shipping']);
			if (is_object($$module) || ($_SESSION['shipping'] == 'free_free')) {
				if ($_SESSION['shipping'] == 'free_free') {
					$quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
					$quote[0]['methods'][0]['cost'] = '0';
				} else {
					$quote = $shipping_modules->quote($method, $module);
				}
				if (isset ($quote['error'])) {
					unset ($_SESSION['shipping']);
				} else {
					if ((isset ($quote[0]['methods'][0]['title'])) && (isset ($quote[0]['methods'][0]['cost']))) {
						$_SESSION['shipping'] = array ('id' => $_SESSION['shipping'], 'title' => (($free_shipping == true) ? $quote[0]['methods'][0]['title'] : $quote[0]['module'].' ('.$quote[0]['methods'][0]['title'].')'), 'cost' => $quote[0]['methods'][0]['cost']);

                        //print "FILENAME_CHECKOUT_PAYMENT => ".FILENAME_CHECKOUT_PAYMENT;
                        //os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
					}
				}
			} else {
				unset ($_SESSION['shipping']);
			}
		}
	} else {
		$_SESSION['shipping'] = false;

        //print "redirect to ".FILENAME_CHECKOUT_PAYMENT;
		//os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
	}

if (isset ($_POST['payment']))
	$_SESSION['payment'] = os_db_prepare_input($_POST['payment']);
	if (ACCOUNT_GENDER == 'true')
		$gender = os_db_prepare_input($_POST['gender']);
	$firstname = os_db_prepare_input($_POST['firstname']);
	if (ACCOUNT_SECOND_NAME == 'true')
	$secondname = os_db_prepare_input($_POST['secondname']);
	if (ACCOUNT_LAST_NAME == 'true')
	$lastname = os_db_prepare_input($_POST['lastname']);
	if (ACCOUNT_DOB == 'true')
		$dob = os_db_prepare_input($_POST['dob']);
	$email_address = os_db_prepare_input($_POST['email_address']);
	if (ACCOUNT_COMPANY == 'true')
		$company = os_db_prepare_input($_POST['company']);
	if (ACCOUNT_COMPANY_VAT_CHECK == 'true')
		$vat = os_db_prepare_input($_POST['vat']);
   if (ACCOUNT_STREET_ADDRESS == 'true')
	$street_address = os_db_prepare_input($_POST['street_address']);
	if (ACCOUNT_SUBURB == 'true')
		$suburb = os_db_prepare_input($_POST['suburb']);
   if (ACCOUNT_POSTCODE == 'true')
	$postcode = os_db_prepare_input($_POST['postcode']);
	if (ACCOUNT_CITY == 'true')
	$city = os_db_prepare_input($_POST['city']);
	$zone_id = os_db_prepare_input($_POST['zone_id']);
	if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true')
		$state = os_db_prepare_input($_POST['state']);
   if (ACCOUNT_COUNTRY == 'true') {
	   $country = os_db_prepare_input($_POST['country']);
	} else {
      $country = STORE_COUNTRY;
	}
   if (ACCOUNT_TELE == 'true')
	$telephone = os_db_prepare_input($_POST['telephone']);
   if (ACCOUNT_FAX == 'true')
	$fax = os_db_prepare_input($_POST['fax']);
	//    $newsletter = os_db_prepare_input($_POST['newsletter']);
	$newsletter = '0';
	$password = os_db_prepare_input($_POST['password']);
	$confirmation = os_db_prepare_input($_POST['confirmation']);

	$error = false;

	if (ACCOUNT_GENDER == 'true') {
		if (($gender != 'm') && ($gender != 'f')) {
			$error = true;

			$messageStack->add('checkout_alternative', ENTRY_GENDER_ERROR.'<br>');
		}
	}

	if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
		$error = true;

		$messageStack->add('checkout_alternative', ENTRY_FIRST_NAME_ERROR.'<br>');
	}

	if (ACCOUNT_LAST_NAME == 'true')
	{
		if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_alternative', ENTRY_LAST_NAME_ERROR.'<br>');
		}
	}

	if (ACCOUNT_DOB == 'true') {
		if (checkdate(substr(os_date_raw($dob), 4, 2), substr(os_date_raw($dob), 6, 2), substr(os_date_raw($dob), 0, 4)) == false) {
			$error = true;

			$messageStack->add('checkout_alternative', ENTRY_DATE_OF_BIRTH_ERROR.'<br>');
		}
	}

// New VAT Check
	require_once(dir_path('class').'vat_validation.php');
	$vatID = new vat_validation($vat, '', '', $country,true);

	$customers_vat_id_status = 0;
//	$customers_status = $vatID->vat_info['status'];
	$customers_vat_id_status = $vatID->vat_info['vat_id_status'];
	$error = $vatID->vat_info['error'];

	if($error==1){
	$messageStack->add('checkout_alternative', ENTRY_VAT_ERROR.'<br>');
	$error = true;
  }
// New VAT CHECK END

	if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
		$error = true;

		$messageStack->add('checkout_alternative', ENTRY_EMAIL_ADDRESS_ERROR);
	}
	elseif (os_validate_email($email_address) == false) {
		$error = true;

		$messageStack->add('checkout_alternative', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
	} else {
		$check_email_query = os_db_query("select count(*) as total from ".TABLE_CUSTOMERS." where customers_email_address = '".os_db_input($email_address)."' and account_type = '0'");
		$check_email = os_db_fetch_array($check_email_query);
		if ($check_email['total'] > 0) {
			$error = true;

			$messageStack->add('checkout_alternative', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
		}
	}

   if (ACCOUNT_STREET_ADDRESS == 'true') {
	if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
		$error = true;

		$messageStack->add('checkout_alternative', ENTRY_STREET_ADDRESS_ERROR.'<br>');
	}
  }

   if (ACCOUNT_POSTCODE == 'true') {
	if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
		$error = true;

		$messageStack->add('checkout_alternative', ENTRY_POST_CODE_ERROR.'<br>');
	}
  }

   if (ACCOUNT_CITY == 'true') {
	if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
		$error = true;

		$messageStack->add('checkout_alternative', ENTRY_CITY_ERROR.'<br>');
	}
  }

   if (ACCOUNT_COUNTRY == 'true') {
	if (is_numeric($country) == false) {
		$error = true;

		$messageStack->add('checkout_alternative', ENTRY_COUNTRY_ERROR.'<br>');
	}
  }

	if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
		$zone_id = 0;
		$check_query = os_db_query("select count(*) as total from ".TABLE_ZONES." where zone_country_id = '".(int) $country."'");
		$check = os_db_fetch_array($check_query);
		$entry_state_has_zones = ($check['total'] > 0);
		if ($entry_state_has_zones == true) {
			$zone_query = os_db_query("select distinct zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and zone_name = '" . os_db_input($state) . "'");
			if (os_db_num_rows($zone_query) > 1) {
				$zone_query = os_db_query("select distinct zone_id from ".TABLE_ZONES." where zone_country_id = '".(int) $country."' and zone_name = '".os_db_input($state)."'");
			}
			if (os_db_num_rows($zone_query) >= 1) {
				$zone = os_db_fetch_array($zone_query);
				$zone_id = $zone['zone_id'];
			} else {
				$error = true;

				$messageStack->add('checkout_alternative', ENTRY_STATE_ERROR_SELECT.'<br>');
			}
		} else {
			if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
				$error = true;

				$messageStack->add('checkout_alternative', ENTRY_STATE_ERROR.'<br>');
			}
		}
	}

   if (ACCOUNT_TELE == 'true') {
	if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
		$error = true;

		$messageStack->add('checkout_alternative', ENTRY_TELEPHONE_NUMBER_ERROR.'<br>');
	}
  }

        $extra_fields_query = osDBquery("select ce.fields_id, ce.fields_input_type, ce.fields_required_status, cei.fields_name, ce.fields_status, ce.fields_input_type, ce.fields_size from " . TABLE_EXTRA_FIELDS . " ce, " . TABLE_EXTRA_FIELDS_INFO . " cei where ce.fields_status=1 and ce.fields_required_status=1 and cei.fields_id=ce.fields_id and cei.languages_id =" . $_SESSION['languages_id']);

   while($extra_fields = os_db_fetch_array($extra_fields_query,true)){

    if(strlen($_POST['fields_' . $extra_fields['fields_id'] ])<$extra_fields['fields_size']){
      $error = true;
      $string_error=sprintf(ENTRY_EXTRA_FIELDS_ERROR,$extra_fields['fields_name'],$extra_fields['fields_size']);
      $messageStack->add('checkout_alternative', $string_error.'<br>');
    }
  }

	if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
		$error = true;

		$messageStack->add('checkout_alternative', ENTRY_PASSWORD_ERROR);
	}
	elseif ($password != $confirmation) {
		$error = true;

		$messageStack->add('checkout_alternative', ENTRY_PASSWORD_ERROR_NOT_MATCHING);
	}
	if ($customers_status == 0 || !$customers_status)
		$customers_status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
//	$password = os_create_password(8);

	if (!$newsletter)
		$newsletter = 0;
	if ($error == false) {
		$sql_data_array = array ('customers_vat_id' => $vat, 'customers_vat_id_status' => $customers_vat_id_status, 'customers_status' => $customers_status, 'customers_firstname' => $firstname, 'customers_secondname' => $secondname, 'customers_lastname' => $lastname, 'customers_email_address' => $email_address, 'customers_telephone' => $telephone, 'customers_fax' => $fax, 'orig_reference' => $html_referer, 'customers_newsletter' => $newsletter, 'delete_user' => '0', 'account_type' => '0', 'customers_password' => os_encrypt_password($password),'customers_date_added' => 'now()','customers_last_modified' => 'now()');

		$_SESSION['account_type'] = '1';

		if (ACCOUNT_GENDER == 'true')
			$sql_data_array['customers_gender'] = $gender;
		if (ACCOUNT_DOB == 'true')
			$sql_data_array['customers_dob'] = os_date_raw($dob);

		os_db_perform(TABLE_CUSTOMERS, $sql_data_array);

		$_SESSION['customer_id'] = os_db_insert_id();

    $extra_fields_query = osDBquery("select ce.fields_id from " . TABLE_EXTRA_FIELDS . " ce where ce.fields_status=1 ");

		$customers_id = $_SESSION['customer_id'];

   	  	$extra_fields_query = os_db_query("select ce.fields_id from " . TABLE_EXTRA_FIELDS . " ce where ce.fields_status=1 ");
    	  while($extra_fields = os_db_fetch_array($extra_fields_query))
				{
				  if(isset($_POST['fields_' . $extra_fields['fields_id']])){
            $sql_data_array = array('customers_id' => (int)$customers_id,
                              'fields_id' => $extra_fields['fields_id'],
                              'value' => $_POST['fields_' . $extra_fields['fields_id']]);
       		}
       		else
					{
					  $sql_data_array = array('customers_id' => (int)$customers_id,
                              'fields_id' => $extra_fields['fields_id'],
                              'value' => '');
						$is_add = false;
						for($i = 1; $i <= $_POST['fields_' . $extra_fields['fields_id'] . '_total']; $i++)
						{
							if(isset($_POST['fields_' . $extra_fields['fields_id'] . '_' . $i]))
							{
							  if($is_add)
							  {
                  $sql_data_array['value'] .= "\n";
								}
								else
								{
                  $is_add = true;
								}
              	$sql_data_array['value'] .= $_POST['fields_' . $extra_fields['fields_id'] . '_' . $i];
							}
						}
					}

					os_db_perform(TABLE_CUSTOMERS_TO_EXTRA_FIELDS, $sql_data_array);
      	}

		$sql_data_array = array ('customers_id' => $_SESSION['customer_id'], 'entry_firstname' => $firstname, 'entry_lastname' => $lastname, 'entry_street_address' => $street_address, 'entry_postcode' => $postcode, 'entry_city' => $city, 'entry_country_id' => $country);

		if (ACCOUNT_GENDER == 'true')
			$sql_data_array['entry_gender'] = $gender;
		if (ACCOUNT_COMPANY == 'true')
			$sql_data_array['entry_company'] = $company;
		if (ACCOUNT_SUBURB == 'true')
			$sql_data_array['entry_suburb'] = $suburb;
		if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
			if ($zone_id > 0) {
				$sql_data_array['entry_zone_id'] = $zone_id;
				$sql_data_array['entry_state'] = '';
			} else {
				$sql_data_array['entry_zone_id'] = '0';
				$sql_data_array['entry_state'] = $state;
			}
		}

		os_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

		$address_id = os_db_insert_id();

		os_db_query("update ".TABLE_CUSTOMERS." set customers_default_address_id = '".$address_id."' where customers_id = '".(int) $_SESSION['customer_id']."'");

		os_db_query("insert into ".TABLE_CUSTOMERS_INFO." (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('".(int) $_SESSION['customer_id']."', '0', now())");

        $sql_data_array = array('login_reference' => $html_referer);
        os_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . (int) $_SESSION['customer_id'] . "'");

		if (SESSION_RECREATE == 'True') {
			os_session_recreate();
		}

		$_SESSION['customer_first_name'] = $firstname;
		$_SESSION['customer_second_name'] = $secondname;
		$_SESSION['customer_last_name'] = $lastname;
		$_SESSION['customer_default_address_id'] = $address_id;
		$_SESSION['customer_country_id'] = $country;
		$_SESSION['customer_zone_id'] = $zone_id;
		$_SESSION['customer_vat_id'] = $vat;

		// restore cart contents
		$_SESSION['cart']->restore_contents();

if (isset ($_SESSION[tracking]['refID'])){
      $campaign_check_query_raw = "SELECT *
			                            FROM ".TABLE_CAMPAIGNS."
			                            WHERE campaigns_refID = '".$_SESSION[tracking][refID]."'";
			$campaign_check_query = os_db_query($campaign_check_query_raw);
		if (os_db_num_rows($campaign_check_query) > 0) {
			$campaign = os_db_fetch_array($campaign_check_query);
			$refID = $campaign['campaigns_id'];
			} else {
			$refID = 0;
		            }

			 os_db_query("update " . TABLE_CUSTOMERS . " set
                                 refferers_id = '".$refID."'
                                 where customers_id = '".(int) $_SESSION['customer_id']."'");

			$leads = $campaign['campaigns_leads'] + 1 ;
		     os_db_query("update " . TABLE_CAMPAIGNS . " set
		                         campaigns_leads = '".$leads."'
                                 where campaigns_id = '".$refID."'");
}

if (!isset ($_SESSION['sendto'])) {
	$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
}

		os_redirect(os_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'));
	}
}

//if ($messageStack->size('checkout_alternative') > 0) {
//	$osTemplate->assign('error', $messageStack->output('checkout_alternative'));

//}

if (ACCOUNT_GENDER == 'true') {
	$osTemplate->assign('gender', '1');

	$osTemplate->assign('INPUT_MALE', os_draw_radio_field(array ('name' => 'gender', 'suffix' => MALE), 'm', '', 'id="gender" checked="checked"'));
	$osTemplate->assign('INPUT_FEMALE', os_draw_radio_field(array ('name' => 'gender', 'suffix' => FEMALE, 'text' => (os_not_null(ENTRY_GENDER_TEXT) ? '<span class="Requirement">'.ENTRY_GENDER_TEXT.'</span>' : '')), 'f', '', 'id="gender"'));
   $osTemplate->assign('ENTRY_GENDER_ERROR', ENTRY_GENDER_ERROR);

} else {
	$osTemplate->assign('gender', '0');
}

$osTemplate->assign('INPUT_FIRSTNAME', os_draw_input_fieldNote(array ('name' => 'firstname', 'text' => '&nbsp;'. (os_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="Requirement">'.ENTRY_FIRST_NAME_TEXT.'</span>' : '')), '', 'id="firstname"'));
$osTemplate->assign('ENTRY_FIRST_NAME_ERROR', ENTRY_FIRST_NAME_ERROR);
if (ACCOUNT_SECOND_NAME == 'true') {
	$osTemplate->assign('secondname', '1');
$osTemplate->assign('INPUT_SECONDNAME', os_draw_input_fieldNote(array ('name' => 'secondname', 'text' => '&nbsp;'. (os_not_null(ENTRY_SECOND_NAME_TEXT) ? '<span class="Requirement">'.ENTRY_SECOND_NAME_TEXT.'</span>' : '')), '', 'id="secondname"'));
}
if (ACCOUNT_LAST_NAME == 'true')
{
	$osTemplate->assign('INPUT_LASTNAME', os_draw_input_fieldNote(array ('name' => 'lastname', 'text' => '&nbsp;'. (os_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="Requirement">'.ENTRY_LAST_NAME_TEXT.'</span>' : '')), '', 'id="lastname"'));
	$osTemplate->assign('ENTRY_LAST_NAME_ERROR', ENTRY_LAST_NAME_ERROR);
	$osTemplate->assign('lastname', '1');
}

if (ACCOUNT_DOB == 'true') {
	$osTemplate->assign('birthdate', '1');

	$osTemplate->assign('INPUT_DOB', os_draw_input_fieldNote(array ('name' => 'dob', 'text' => '&nbsp;'. (os_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="Requirement">'.ENTRY_DATE_OF_BIRTH_TEXT.'</span>' : '')), '', 'id="dob"'));
   $osTemplate->assign('ENTRY_DATE_OF_BIRTH_ERROR', ENTRY_DATE_OF_BIRTH_ERROR);

} else {
	$osTemplate->assign('birthdate', '0');
}

$osTemplate->assign('INPUT_EMAIL', os_draw_input_fieldNote(array ('name' => 'email_address', 'text' => '&nbsp;'. (os_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="Requirement">'.ENTRY_EMAIL_ADDRESS_TEXT.'</span>' : '')), '', 'id="email"'));
$osTemplate->assign('ENTRY_EMAIL_ADDRESS_ERROR', ENTRY_EMAIL_ADDRESS_ERROR);

if (ACCOUNT_COMPANY == 'true') {
	$osTemplate->assign('company', '1');
	$osTemplate->assign('INPUT_COMPANY', os_draw_input_fieldNote(array ('name' => 'company', 'text' => '&nbsp;'. (os_not_null(ENTRY_COMPANY_TEXT) ? '<span class="Requirement">'.ENTRY_COMPANY_TEXT.'</span>' : ''))));
} else {
	$osTemplate->assign('company', '0');
}

if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
	$osTemplate->assign('vat', '1');
	$osTemplate->assign('INPUT_VAT', os_draw_input_fieldNote(array ('name' => 'vat', 'text' => '&nbsp;'. (os_not_null(ENTRY_VAT_TEXT) ? '<span class="Requirement">'.ENTRY_VAT_TEXT.'</span>' : ''))));
} else {
	$osTemplate->assign('vat', '0');
}

if (ACCOUNT_STREET_ADDRESS == 'true') {
   $osTemplate->assign('street_address', '1');
   $osTemplate->assign('INPUT_STREET', os_draw_input_fieldNote(array ('name' => 'street_address', 'text' => '&nbsp;'. (os_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="Requirement">'.ENTRY_STREET_ADDRESS_TEXT.'</span>' : '')), '', 'id="address"'));
   $osTemplate->assign('ENTRY_STREET_ADDRESS_ERROR', ENTRY_STREET_ADDRESS_ERROR);
} else {
	$osTemplate->assign('street_address', '0');
}

if (ACCOUNT_SUBURB == 'true') {
	$osTemplate->assign('suburb', '1');
	$osTemplate->assign('INPUT_SUBURB', os_draw_input_fieldNote(array ('name' => 'suburb', 'text' => '&nbsp;'. (os_not_null(ENTRY_SUBURB_TEXT) ? '<span class="Requirement">'.ENTRY_SUBURB_TEXT.'</span>' : ''))));
} else {
	$osTemplate->assign('suburb', '0');
}

if (ACCOUNT_POSTCODE == 'true') {
   $osTemplate->assign('postcode', '1');
   $osTemplate->assign('INPUT_CODE', os_draw_input_fieldNote(array ('name' => 'postcode', 'text' => '&nbsp;'. (os_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="Requirement">'.ENTRY_POST_CODE_TEXT.'</span>' : '')), '', 'id="postcode"'));
   $osTemplate->assign('ENTRY_POST_CODE_ERROR', ENTRY_POST_CODE_ERROR);
} else {
	$osTemplate->assign('postcode', '0');
}

if (ACCOUNT_CITY == 'true') {
   $osTemplate->assign('city', '1');
   $osTemplate->assign('INPUT_CITY', os_draw_input_fieldNote(array ('name' => 'city', 'text' => '&nbsp;'. (os_not_null(ENTRY_CITY_TEXT) ? '<span class="Requirement">'.ENTRY_CITY_TEXT.'</span>' : '')), '', 'id="city"'));
   $osTemplate->assign('ENTRY_CITY_ERROR', ENTRY_CITY_ERROR);
} else {
	$osTemplate->assign('city', '0');
}

if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
	$osTemplate->assign('state', '1');

	    $country = (isset($_POST['country']) ? os_db_prepare_input($_POST['country']) : STORE_COUNTRY);
	    $zone_id = 0;
		 $check_query = os_db_query("select count(*) as total from ".TABLE_ZONES." where zone_country_id = '".(int) $country."'");
		 $check = os_db_fetch_array($check_query);
		 $entry_state_has_zones = ($check['total'] > 0);
		 if ($entry_state_has_zones == true) {
			$zones_array = array ();
			$zones_query = os_db_query("select zone_name from ".TABLE_ZONES." where zone_country_id = '".(int) $country."' order by zone_name");
			while ($zones_values = os_db_fetch_array($zones_query)) {
				$zones_array[] = array ('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
			}

			$zone = os_db_query("select distinct zone_id, zone_name from ".TABLE_ZONES." where zone_country_id = '".(int) $country."' and zone_code = '".os_db_input($state)."'");

	      if (os_db_num_rows($zone) > 0) {
	        $zone_id = $zone['zone_id'];
	        $zone_name = $zone['zone_name'];

	      } else {

		   $zone = os_db_query("select distinct zone_id, zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "'");

	      if (os_db_num_rows($zone) > 0) {
	          $zone_id = $zone['zone_id'];
	          $zone_name = $zone['zone_name'];
	        }
	      }
		}

      if ($entry_state_has_zones == true) {
        $state_input = os_draw_pull_down_menuNote(array ('name' => 'state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="Requirement">'.ENTRY_STATE_TEXT.'</span>' : '')), $zones_array, os_get_zone_name(STORE_COUNTRY, STORE_ZONE,''), ' id="state"');
      } else {
		$state_input = os_draw_input_fieldNote(array ('name' => 'state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="Requirement">'.ENTRY_STATE_TEXT.'</span>' : '')), '', 'id="state"');
      }

	$osTemplate->assign('INPUT_STATE', $state_input);
   $osTemplate->assign('ENTRY_STATE_ERROR_SELECT', ENTRY_STATE_ERROR_SELECT);
} else {
	$osTemplate->assign('state', '0');
}

if ($_POST['country']) {
	$selected = $_POST['country'];
} else {
	$selected = STORE_COUNTRY;
}

if (ACCOUNT_COUNTRY == 'true') {
	$osTemplate->assign('country', '1');

   $osTemplate->assign('SELECT_COUNTRY', os_get_country_list(array ('name' => 'country', 'text' => '&nbsp;'. (os_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="Requirement">'.ENTRY_COUNTRY_TEXT.'</span>' : '')), $selected, 'id="country" onchange="document.getElementById(\'stateXML\').innerHTML = \'' . ENTRY_STATEXML_LOADING . '\';loadXMLDoc(\'loadStateXML\',{country_id: this.value});"'));


   $osTemplate->assign('ENTRY_COUNTRY_ERROR', ENTRY_COUNTRY_ERROR);
} else {
	$osTemplate->assign('country', '0');
}

if (ACCOUNT_TELE == 'true') {
   $osTemplate->assign('telephone', '1');
   $osTemplate->assign('INPUT_TEL', os_draw_input_fieldNote(array ('name' => 'telephone', 'text' => '&nbsp;'. (os_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="Requirement">'.ENTRY_TELEPHONE_NUMBER_TEXT.'</span>' : '')), '', 'id="telephone"'));
   $osTemplate->assign('ENTRY_TELEPHONE_NUMBER_ERROR', ENTRY_TELEPHONE_NUMBER_ERROR);
} else {
	$osTemplate->assign('telephone', '0');
}

if (ACCOUNT_FAX == 'true') {
   $osTemplate->assign('fax', '1');
   $osTemplate->assign('INPUT_FAX', os_draw_input_fieldNote(array ('name' => 'fax', 'text' => '&nbsp;'. (os_not_null(ENTRY_FAX_NUMBER_TEXT) ? '<span class="Requirement">'.ENTRY_FAX_NUMBER_TEXT.'</span>' : ''))));
} else {
	$osTemplate->assign('fax', '0');
}

	$osTemplate->assign('customers_extra_fileds', '1');
   $osTemplate->assign('INPUT_CUSTOMERS_EXTRA_FIELDS', os_get_extra_fields($_SESSION['customer_id'],$_SESSION['languages_id']));
	$osTemplate->assign('INPUT_PASSWORD', os_draw_password_fieldNote(array ('name' => 'password', 'text' => '&nbsp;'. (os_not_null(ENTRY_PASSWORD_TEXT) ? '<span class="Requirement">'.ENTRY_PASSWORD_TEXT.'</span>' : '')), '', 'id="pass"'));
	$osTemplate->assign('ENTRY_PASSWORD_ERROR', ENTRY_PASSWORD_ERROR);
	$osTemplate->assign('INPUT_CONFIRMATION', os_draw_password_fieldNote(array ('name' => 'confirmation', 'text' => '&nbsp;'. (os_not_null(ENTRY_PASSWORD_CONFIRMATION_TEXT) ? '<span class="Requirement">'.ENTRY_PASSWORD_CONFIRMATION_TEXT.'</span>' : '')), '', 'id="confirmation"'));
	$osTemplate->assign('ENTRY_PASSWORD_ERROR_NOT_MATCHING', ENTRY_PASSWORD_ERROR_NOT_MATCHING);
   
 /*  */
/* SHIPPING_BLOCK */
// load all enabled shipping modules

if (!isset ($_POST['action']) && ($_POST['action'] != 'process')) {
$shipping_modules = new shipping;
}

if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
	switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
		case 'national' :
			if ($order->delivery['country_id'] == STORE_COUNTRY)
				$pass = true;
			break;
		case 'international' :
			if ($order->delivery['country_id'] != STORE_COUNTRY)
				$pass = true;
			break;
		case 'both' :
			$pass = true;
			break;
		default :
			$pass = false;
			break;
	}

	$free_shipping = false;
	if (($pass == true) && ($order->info['total'] - $order->info['shipping_cost'] >= $osPrice->Format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, false, 0, true))) {
		$free_shipping = true;

		include (_MODULES.'order_total/ot_shipping/'.$_SESSION['language'].'.php');
	}
} else {
	$free_shipping = false;
}
// process the selected shipping method
if (isset ($_POST['action']) && ($_POST['action'] == 'process')) {

	if ((os_count_shipping_modules() > 0) || ($free_shipping == true)) {
		if ((isset ($_POST['shipping'])) && (strpos($_POST['shipping'], '_'))) {
			$_SESSION['shipping'] = $_POST['shipping'];

			list ($module, $method) = explode('_', $_SESSION['shipping']);
			if (is_object($$module) || ($_SESSION['shipping'] == 'free_free')) {
				if ($_SESSION['shipping'] == 'free_free') {
					$quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
					$quote[0]['methods'][0]['cost'] = '0';
				} else {
					$quote = $shipping_modules->quote($method, $module);
				}
				if (isset ($quote['error'])) {
					unset ($_SESSION['shipping']);
				} else {
					if ((isset ($quote[0]['methods'][0]['title'])) && (isset ($quote[0]['methods'][0]['cost']))) {
						$_SESSION['shipping'] = array ('id' => $_SESSION['shipping'], 'title' => (($free_shipping == true) ? $quote[0]['methods'][0]['title'] : $quote[0]['module'].' ('.$quote[0]['methods'][0]['title'].')'), 'cost' => $quote[0]['methods'][0]['cost']);

                        //print "FILENAME_CHECKOUT_PAYMENT => ".FILENAME_CHECKOUT_PAYMENT;
                        //os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
					}
				}
			} else {
				unset ($_SESSION['shipping']);
			}
		}
	} else {
		$_SESSION['shipping'] = false;

        //print "redirect to ".FILENAME_CHECKOUT_PAYMENT;
		//os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
	}
}

// get all available shipping quotes
$quotes = $shipping_modules->quote();

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
if (!isset ($_SESSION['shipping']) || (isset ($_SESSION['shipping']) && ($_SESSION['shipping'] == false) && (os_count_shipping_modules() > 1)))
	$_SESSION['shipping'] = $shipping_modules->cheapest();


if (ACCOUNT_STREET_ADDRESS == 'true') {
	$osTemplate->assign('SHIPPING_ADDRESS', 'true');
}

$module = new osTemplate;
if (os_count_shipping_modules() > 0) {

	$module->assign('FREE_SHIPPING', $free_shipping);

	# free shipping or not...

	if ($free_shipping == true) {

		$module->assign('FREE_SHIPPING_TITLE', FREE_SHIPPING_TITLE);

		$module->assign('FREE_SHIPPING_DESCRIPTION', sprintf(FREE_SHIPPING_DESCRIPTION, $osPrice->Format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, true, 0, true)).os_draw_hidden_field('shipping', 'free_free'));

		$module->assign('FREE_SHIPPING_ICON', $quotes[$i]['icon']);

	} else {

		$radio_buttons = 0;

		#loop through installed shipping methods...

		for ($i = 0, $n = sizeof($quotes); $i < $n; $i ++) {

			if (!isset ($quotes[$i]['error'])) {

				for ($j = 0, $n2 = sizeof($quotes[$i]['methods']); $j < $n2; $j ++) {

					# set the radio button to be checked if it is the method chosen

					$quotes[$i]['methods'][$j]['radio_buttons'] = $radio_buttons;

					$checked = (($quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']['id']) ? true : false);

					if (($checked == true) || ($n == 1 && $n2 == 1)) {

						$quotes[$i]['methods'][$j]['checked'] = 1;

					}

					if (($n > 1) || ($n2 > 1)) {
						if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = '';
						if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = 0;

						$quotes[$i]['methods'][$j]['price'] = $osPrice->Format(os_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true);

						$quotes[$i]['methods'][$j]['radio_field'] = os_draw_radio_field('shipping', $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'], $checked);
						$quotes[$i]['methods'][$j]['id'] = $quotes[$i]['methods'][$j]['id'];

					} else {
						if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = 0;

						$quotes[$i]['methods'][$j]['price'] = $osPrice->Format(os_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true).os_draw_hidden_field('shipping', $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id']);

					}

					$radio_buttons ++;

				}

			}

		}

		$module->assign('module_content', $quotes);

	}
	$module->caching = 0;
	$shipping_block = $module->fetch(CURRENT_TEMPLATE.'/module/checkout_shipping_block.html');
}

$osTemplate->assign('SHIPPING_BLOCK', $shipping_block);
/* END SHIPPING_BLOCK */

/* PAYMENT_BLOCK */
// load all enabled payment modules

require (dir_path('class') . 'order.php');
$order = new order();

require (dir_path('class') . 'order_total.php'); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM
$order_total_modules = new order_total(); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM

$payment_modules = new payment;

$order_total_modules->process();

$module = new osTemplate;
	if (isset ($_GET['payment_error']) && is_object(${ $_GET['payment_error'] }) && ($error = ${$_GET['payment_error']}->get_error())) {

		$osTemplate->assign('error', htmlspecialchars($error['error']));

	}

	$selection = $payment_modules->selection();

	$radio_buttons = 0;
	for ($i = 0, $n = sizeof($selection); $i < $n; $i++) {

		$selection[$i]['radio_buttons'] = $radio_buttons;
		if (($selection[$i]['id'] == $payment) || ($n == 1)) {
			$selection[$i]['checked'] = 1;
		}

		if (sizeof($selection) > 1) {
			$selection[$i]['selection'] = os_draw_radio_field('payment', $selection[$i]['id'], ($selection[$i]['id'] == $_SESSION['payment']));
		} else {
			$selection[$i]['selection'] = os_draw_hidden_field('payment', $selection[$i]['id']);
		}
			$selection[$i]['id'] = $selection[$i]['id'];

		if (isset ($selection[$i]['error'])) {

		} else {

			$radio_buttons++;
		}
	}

	$module->assign('module_content', $selection);


if (ACTIVATE_GIFT_SYSTEM == 'true') {
	$osTemplate->assign('module_gift', $order_total_modules->credit_selection());
}

$module->caching = 0;
$payment_block = $module->fetch(CURRENT_TEMPLATE . '/module/checkout_payment_block.html');

$osTemplate->assign('COMMENTS', os_draw_textarea_field('comments', 'soft', '60', '5', $_POST['comments']) . os_draw_hidden_field('comments_added', 'YES'));

$osTemplate->assign('conditions', 'false');

//check if display conditions on checkout page is true
if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {

$osTemplate->assign('conditions', 'true');

	if (GROUP_CHECK == 'true') {
		$group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
	}

	$shop_content_query = os_db_query("SELECT
	                                                content_title,
	                                                content_heading,
	                                                content_text,
	                                                content_file
	                                                FROM " . TABLE_CONTENT_MANAGER . "
	                                                WHERE content_group='3' " . $group_check . "
	                                                AND languages_id='" . $_SESSION['languages_id'] . "'");
	$shop_content_data = os_db_fetch_array($shop_content_query);

	if ($shop_content_data['content_file'] != '') {

		$conditions = '<iframe SRC="' . DIR_WS_CATALOG . 'media/content/' . $shop_content_data['content_file'] . '" width="100%" height="300">';
		$conditions .= '</iframe>';
	} else {

		$conditions = '<textarea name="blabla" cols="60" rows="10" readonly="readonly">' . strip_tags(str_replace('<br />', "\n", $shop_content_data['content_text'])) . '</textarea>';
	}

	$osTemplate->assign('AGB', $conditions);
	$osTemplate->assign('AGB_LINK', $main->getContentLink(3, MORE_INFO));

   $osTemplate->assign('AGB_checkbox', '<input type="checkbox" value="conditions" name="conditions" checked />');

}
$osTemplate->assign('BUTTON_CONTINUE', button_continue_submit());

$osTemplate->assign('PAYMENT_BLOCK', $payment_block);
/* END PAYMENT_BLOCK */
require (dir_path('includes').'header.php');
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/checkout_alternative.html');
$osTemplate->assign('main_content', $main_content);
$osTemplate->loadFilter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE.'/index.html');

?>
