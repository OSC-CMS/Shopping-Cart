<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require ('includes/top.php');

if (ACCOUNT_OPTIONS == 'account')
	os_redirect(FILENAME_DEFAULT);

if (isset ($_SESSION['customer_id'])) {
	os_redirect(os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}

// create template elements
//$osTemplate = new osTemplate;
// include boxes


$process = false;
if (isset ($_POST['action']) && ($_POST['action'] == 'process')) {
	$process = true;

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
	if (ACCOUNT_STATE == 'true')
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

			$messageStack->add('create_account', ENTRY_GENDER_ERROR);
		}
	}

	if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_FIRST_NAME_ERROR);
	}

	if (ACCOUNT_LAST_NAME == 'true')
	{
		if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
			$error = true;

			$messageStack->add('create_account', ENTRY_LAST_NAME_ERROR);
		}
	}

	if (ACCOUNT_DOB == 'true') {
		if (checkdate(substr(os_date_raw($dob), 4, 2), substr(os_date_raw($dob), 6, 2), substr(os_date_raw($dob), 0, 4)) == false) {
			$error = true;

			$messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);
		}
	}

// New VAT Check
	require_once(_CLASS.'vat_validation.php');
	$vatID = new vat_validation($vat, '', '', $country,true);

	$customers_status = $vatID->vat_info['status'];
	$customers_vat_id_status = $vatID->vat_info['vat_id_status'];
	$error = $vatID->vat_info['error'];

	if($error==1){
	$messageStack->add('create_account', ENTRY_VAT_ERROR);
	$error = true;
  }
// New VAT CHECK END

   if (ACCOUNT_STREET_ADDRESS == 'true') {
	if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_STREET_ADDRESS_ERROR);
	}
  }

   if (ACCOUNT_POSTCODE == 'true') {
	if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_POST_CODE_ERROR);
	}
  }

   if (ACCOUNT_CITY == 'true') {
	if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_CITY_ERROR);
	}
  }

   if (ACCOUNT_COUNTRY == 'true') {
	if (is_numeric($country) == false) {
		$error = true;

		$messageStack->add('create_account', ENTRY_COUNTRY_ERROR);
	}
  }

	if (ACCOUNT_STATE == 'true') {
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

				$messageStack->add('create_account', ENTRY_STATE_ERROR_SELECT);
			}
		} else {
			if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
				$error = true;

				$messageStack->add('create_account', ENTRY_STATE_ERROR);
			}
		}
	}

   if (ACCOUNT_TELE == 'true') {
	if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_TELEPHONE_NUMBER_ERROR);
	}
  }

        $extra_fields_query = osDBquery("select ce.fields_id, ce.fields_input_type, ce.fields_required_status, cei.fields_name, ce.fields_status, ce.fields_input_type, ce.fields_size from " . TABLE_EXTRA_FIELDS . " ce, " . TABLE_EXTRA_FIELDS_INFO . " cei where ce.fields_status=1 and ce.fields_required_status=1 and cei.fields_id=ce.fields_id and cei.languages_id =" . $_SESSION['languages_id']);

   while($extra_fields = os_db_fetch_array($extra_fields_query,true)){
   
    if(strlen($_POST['fields_' . $extra_fields['fields_id'] ])<$extra_fields['fields_size']){
      $error = true;
      $string_error=sprintf(ENTRY_EXTRA_FIELDS_ERROR,$extra_fields['fields_name'],$extra_fields['fields_size']);
      $messageStack->add('create_account', $string_error);
    }
  }

	if ($customers_status == 0 || !$customers_status)
		$customers_status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
	$password = os_create_password(8);

	if (!$newsletter)
		$newsletter = 0;
	if ($error == false) {
		$sql_data_array = array ('customers_vat_id' => $vat, 'customers_vat_id_status' => $customers_vat_id_status, 'customers_status' => $customers_status, 'customers_firstname' => $firstname, 'customers_secondname' => $secondname, 'customers_lastname' => $lastname, 'customers_email_address' => $email_address, 'customers_telephone' => $telephone, 'customers_fax' => $fax, 'orig_reference' => $html_referer, 'customers_newsletter' => $newsletter, 'account_type' => '1', 'customers_password' => os_encrypt_password($password),'customers_date_added' => 'now()','customers_last_modified' => 'now()'); 

		$_SESSION['account_type'] = '1';

		if (ACCOUNT_GENDER == 'true')
			$sql_data_array['customers_gender'] = $gender;
		if (ACCOUNT_DOB == 'true')
			$sql_data_array['customers_dob'] = os_date_raw($dob);

		os_db_perform(TABLE_CUSTOMERS, $sql_data_array);

		$_SESSION['customer_id'] = os_db_insert_id();

    $extra_fields_query = osDBquery("select ce.fields_id from " . TABLE_EXTRA_FIELDS . " ce where ce.fields_status=1 ");

		$_SESSION['customer_id'] = os_db_insert_id();
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

		$sql_data_array = array ('customers_id' => $_SESSION['customer_id'], 'entry_firstname' => $firstname, 'entry_secondname' => $secondname, 'entry_lastname' => $lastname, 'entry_street_address' => $street_address, 'entry_postcode' => $postcode, 'entry_city' => $city, 'entry_country_id' => $country);

		if (ACCOUNT_GENDER == 'true')
			$sql_data_array['entry_gender'] = $gender;
		if (ACCOUNT_COMPANY == 'true')
			$sql_data_array['entry_company'] = $company;
		if (ACCOUNT_SUBURB == 'true')
			$sql_data_array['entry_suburb'] = $suburb;
		if (ACCOUNT_STATE == 'true') {
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

		os_redirect(os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
	}
}

$breadcrumb->add(NAVBAR_TITLE_CREATE_GUEST_ACCOUNT, os_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'));

require (dir_path('includes').'header.php');

if ($messageStack->size('create_account') > 0) {
	$osTemplate->assign('error', $messageStack->output('create_account'));

}
$osTemplate->assign('FORM_ACTION', os_draw_form('create_account', os_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'), 'post', 'onsubmit="return checkform(this);"').os_draw_hidden_field('action', 'process') . os_draw_hidden_field('required', 'gender,firstname,lastname,dob,email,address,postcode,city,state,country,telephone', 'id="required"'));

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
	$osTemplate->assign('lastname', '1');
	$osTemplate->assign('INPUT_LASTNAME', os_draw_input_fieldNote(array ('name' => 'lastname', 'text' => '&nbsp;'. (os_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="Requirement">'.ENTRY_LAST_NAME_TEXT.'</span>' : '')), '', 'id="lastname"'));
	$osTemplate->assign('ENTRY_LAST_NAME_ERROR', ENTRY_LAST_NAME_ERROR);
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

if (ACCOUNT_STATE == 'true') {
	$osTemplate->assign('state', '1');

//	if ($process == true) {

//    if ($process != true) {
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
//	}

      if ($entry_state_has_zones == true) {
        $state_input = os_draw_pull_down_menuNote(array ('name' => 'state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="Requirement">'.ENTRY_STATE_TEXT.'</span>' : '')), $zones_array, os_get_zone_name(STORE_COUNTRY, STORE_ZONE,''), ' id="state"');
//        $state_input = os_draw_pull_down_menu('state', $zones_array, $zone_name . ' id="state"');
      } else {
		$state_input = os_draw_input_fieldNote(array ('name' => 'state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="Requirement">'.ENTRY_STATE_TEXT.'</span>' : '')), '', 'id="state"');
//        $state_input = os_draw_input_field('state', '', ' id="state"');
      }
		
			
//			$state_input = os_draw_pull_down_menuNote(array ('name' => 'state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">'.ENTRY_STATE_TEXT.'</span>' : '')), $zones_array);
//		} else {
//			$state_input = os_draw_input_fieldNote(array ('name' => 'state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">'.ENTRY_STATE_TEXT.'</span>' : '')));
//		}
//	} else {
//		$state_input = os_draw_input_fieldNote(array ('name' => 'state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">'.ENTRY_STATE_TEXT.'</span>' : '')));
//	}

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
//   $osTemplate->assign('SELECT_COUNTRY', os_get_country_list(array ('name' => 'country', 'text' => '&nbsp;'. (os_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">'.ENTRY_COUNTRY_TEXT.'</span>' : '')), $selected));

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

$osTemplate->assign('FORM_END', '</form>');
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$osTemplate->assign('BUTTON_SUBMIT', button_continue_submit());
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/create_account_guest.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_CREATE_GUEST_ACCOUNT.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_CREATE_GUEST_ACCOUNT.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>