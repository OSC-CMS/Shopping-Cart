<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require('includes/top.php');

//$osTemplate = new osTemplate;


if (!isset($_SESSION['affiliate_id'])) {
    os_redirect(os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
}

if (isset($_POST['action'])) {
    $a_gender = os_db_prepare_input($_POST['a_gender']);
    $a_firstname = os_db_prepare_input($_POST['a_firstname']);
    $a_lastname = os_db_prepare_input($_POST['a_lastname']);
    $a_dob = os_db_prepare_input($_POST['a_dob']);
    $a_email_address = os_db_prepare_input($_POST['a_email_address']);
    $a_company = os_db_prepare_input($_POST['a_company']);
    $a_company_taxid = os_db_prepare_input($_POST['a_company_taxid']);
    $a_payment_check = os_db_prepare_input($_POST['a_payment_check']);
    $a_payment_paypal = os_db_prepare_input($_POST['a_payment_paypal']);
    $a_payment_bank_name = os_db_prepare_input($_POST['a_payment_bank_name']);
    $a_payment_bank_branch_number = os_db_prepare_input($_POST['a_payment_bank_branch_number']);
    $a_payment_bank_swift_code = os_db_prepare_input($_POST['a_payment_bank_swift_code']);
    $a_payment_bank_account_name = os_db_prepare_input($_POST['a_payment_bank_account_name']);
    $a_payment_bank_account_number = os_db_prepare_input($_POST['a_payment_bank_account_number']);
    $a_street_address = os_db_prepare_input($_POST['a_street_address']);
    $a_suburb = os_db_prepare_input($_POST['a_suburb']);
    $a_postcode = os_db_prepare_input($_POST['a_postcode']);
    $a_city = os_db_prepare_input($_POST['a_city']);
    $a_country = os_db_prepare_input($_POST['a_country']);
    $a_zone_id = os_db_prepare_input($_POST['a_zone_id']);
    $a_state = os_db_prepare_input($_POST['a_state']);
    $a_telephone = os_db_prepare_input($_POST['a_telephone']);
    $a_fax = os_db_prepare_input($_POST['a_fax']);
    $a_homepage = os_db_prepare_input($_POST['a_homepage']);
    $a_password = os_db_prepare_input($_POST['a_password']);
    $a_confirmation = os_db_prepare_input($_POST['a_confirmation']);
    $a_agb = os_db_prepare_input($_POST['a_agb']);

    $error = false; 

    if (ACCOUNT_GENDER == 'true') {
        if (($a_gender == 'm') || ($a_gender == 'f')) {
            $entry_gender_error = false;
        }
        else {
            $error = true;
            $entry_gender_error = true;
        }
    }

    if (strlen($a_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    	$error = true;
    	$entry_firstname_error = true;
    }
	else {
		$entry_firstname_error = false;
    }

    if (strlen($a_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    	$error = true;
    	$entry_lastname_error = true;
    }
	else {
		$entry_lastname_error = false;
    }

    if (ACCOUNT_DOB == 'true') {
    	if (checkdate(substr(os_date_short($a_dob), 4, 2), substr(os_date_short($a_dob), 6, 2), substr(os_date_short($a_dob), 0, 4))) {
    		$entry_date_of_birth_error = false;
    	}
		else {
			$error = true;
			$entry_date_of_birth_error = true;
		}
    }
  
    if (strlen($a_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    	$error = true;
    	$entry_email_address_error = true;
    }
	else {
		$entry_email_address_error = false;
    }

    if (!os_validate_email($a_email_address)) {
    	$error = true;
    	$entry_email_address_check_error = true;
    }
	else {
		$entry_email_address_check_error = false;
    }

    if (strlen($a_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
    	$error = true;
    	$entry_street_address_error = true;
    }
	else {
		$entry_street_address_error = false;
    }
  
    if (strlen($a_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
    	$error = true;
    	$entry_post_code_error = true;
    }
	else {
		$entry_post_code_error = false;
    } 

    if (strlen($a_city) < ENTRY_CITY_MIN_LENGTH) {
    	$error = true;
    	$entry_city_error = true;
    }
	else {
		$entry_city_error = false;
    }

    if (!$a_country) {
    	$error = true;
    	$entry_country_error = true;
    }
	else {
		$entry_country_error = false;
    }

    if (ACCOUNT_STATE == 'true') {
    	if ($entry_country_error) {
    		$entry_state_error = true;
    	}
		else {
			$a_zone_id = 0;
			$entry_state_error = false;
			$check_query = os_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . os_db_input($a_country) . "'");
			$check_value = os_db_fetch_array($check_query);
			$entry_state_has_zones = ($check_value['total'] > 0);
			if ($entry_state_has_zones) {
				$zone_query = os_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . os_db_input($a_country) . "' and zone_name = '" . os_db_input($a_state) . "'");
				if (os_db_num_rows($zone_query) == 1) {
					$zone_values = os_db_fetch_array($zone_query);
					$a_zone_id = $zone_values['zone_id'];
				}
				else {
					$zone_query = os_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . os_db_input($a_country) . "' and zone_code = '" . os_db_input($a_state) . "'");
					if (os_db_num_rows($zone_query) == 1) {
						$zone_values = os_db_fetch_array($zone_query);
						$a_zone_id = $zone_values['zone_id'];
					}
					else {
						$error = true;
						$entry_state_error = true;
					}
				}
			}
			else {
				if (!$a_state) {
					$error = true;
					$entry_state_error = true;
				}
			}
		}
	}

    if (strlen($a_telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
    	$error = true;
    	$entry_telephone_error = true;
    }
	else {
		$entry_telephone_error = false;
    }

    $passlen = strlen($a_password);
    if ($passlen < ENTRY_PASSWORD_MIN_LENGTH) {
    	$error = true;
    	$entry_password_error = true;
    }
	else {
		$entry_password_error = false;
    }

    if ($a_password != $a_confirmation) {
    	$error = true;
    	$entry_password_error = true;
    }

    $check_email_query = os_db_query("select count(*) as total from " . TABLE_AFFILIATE . " where affiliate_email_address = '" .  os_db_input($a_email_address) . "' and affiliate_id != '" . os_db_input($_SESSION['affiliate_id']) . "'");
    $check_email = os_db_fetch_array($check_email_query);
    if ($check_email['total'] > 0) {
    	$error = true;
    	$entry_email_address_exists = true;
    }
	else {
		$entry_email_address_exists = false;
    }

    // Check Suburb
    $entry_suburb_error = false;

    // Check Fax
    $entry_fax_error = false;

    if (!affiliate_check_url($a_homepage)) {
    	$error = true;
    	$entry_homepage_error = true;
    }
	else {
		$entry_homepage_error = false;
    }

    if (!$a_agb) {
    	$error=true;
    	$entry_agb_error=true;
    }

    // Check Company 
    $entry_company_error = false;
    $entry_company_taxid_error = false;

    // Check Payment
    $entry_payment_check_error = false;
    $entry_payment_paypal_error = false;
    $entry_payment_bank_name_error = false;
    $entry_payment_bank_branch_number_error = false;
    $entry_payment_bank_swift_code_error = false;
    $entry_payment_bank_account_name_error = false;
    $entry_payment_bank_account_number_error = false;

    if (!$error) {
    	$sql_data_array = array('affiliate_firstname' => $a_firstname,
                                'affiliate_lastname' => $a_lastname,
                                'affiliate_email_address' => $a_email_address,
                                'affiliate_payment_check' => $a_payment_check,
                                'affiliate_payment_paypal' => $a_payment_paypal,
                                'affiliate_payment_bank_name' => $a_payment_bank_name,
                                'affiliate_payment_bank_branch_number' => $a_payment_bank_branch_number,
                                'affiliate_payment_bank_swift_code' => $a_payment_bank_swift_code,
                                'affiliate_payment_bank_account_name' => $a_payment_bank_account_name,
                                'affiliate_payment_bank_account_number' => $a_payment_bank_account_number,
                                'affiliate_street_address' => $a_street_address,
                                'affiliate_postcode' => $a_postcode,
                                'affiliate_city' => $a_city,
                                'affiliate_country_id' => $a_country,
                                'affiliate_telephone' => $a_telephone,
                                'affiliate_fax' => $a_fax,
                                'affiliate_homepage' => $a_homepage,
                                'affiliate_password' => os_encrypt_password($a_password),
                                'affiliate_agb' => '1');

    	if (ACCOUNT_GENDER == 'true') $sql_data_array['affiliate_gender'] = $a_gender;
		if (ACCOUNT_DOB == 'true') $sql_data_array['affiliate_dob'] = os_date_raw($a_dob);
    	if (ACCOUNT_COMPANY == 'true') {
    		$sql_data_array['affiliate_company'] = $a_company;
    		$sql_data_array['affiliate_company_taxid'] = $a_company_taxid;
    	}
    	if (ACCOUNT_SUBURB == 'true') $sql_data_array['affiliate_suburb'] = $a_suburb;
    	if (ACCOUNT_STATE == 'true') {
    		if ($a_zone_id > 0) {
    			$sql_data_array['affiliate_zone_id'] = $a_zone_id;
    			$sql_data_array['affiliate_state'] = '';
    		}
    		else {
    			$sql_data_array['affiliate_zone_id'] = '0';
    			$sql_data_array['affiliate_state'] = $a_state;
    		}
    	}

		$sql_data_array['affiliate_date_account_last_modified'] = 'now()';

		os_db_perform(TABLE_AFFILIATE, $sql_data_array, 'update', "affiliate_id = '" . os_db_input($_SESSION['affiliate_id']) . "'");
		
        os_redirect(os_href_link(FILENAME_AFFILIATE_DETAILS_OK, '', 'SSL'));
    }
}

$breadcrumb->add(NAVBAR_TITLE, os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_DETAILS, os_href_link(FILENAME_AFFILIATE_DETAILS, '', 'SSL'));

require(dir_path('includes') . 'header.php');

$affiliate_query = os_db_query("select * from " . TABLE_AFFILIATE . " where affiliate_id = '" . $_SESSION['affiliate_id'] . "'");
$affiliate = os_db_fetch_array($affiliate_query);
$osTemplate->assign('affiliate', $affiliate);

$osTemplate->assign('FORM_ACTION', os_draw_form('affiliate_details', os_href_link(FILENAME_AFFILIATE_DETAILS, '', 'SSL'), 'post', 'onSubmit="return check_form();"'));
$osTemplate->assign('HIDDEN_ACTION', os_draw_hidden_field('action', 'process'));
$osTemplate->assign('IMAGE_TABLE_BACKGROUND', os_image(http_path('images') . 'table_background_account.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT));

require(DIR_WS_MODULES . 'affiliate_account_details.php');

$osTemplate->assign('BUTTON_SUBMIT', button_continue_submit());
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_details.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');?>
