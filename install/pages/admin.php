<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function step($is_submit)
{
    if ($is_submit)
    {
        return create_admin();
    }

    $result = array('html' => display('admin', array()));

    return $result;
}

function create_admin()
{
	$email = $_POST['EMAIL_ADRESS'];
	$pass = $_POST['PASSWORD'];

	if (!$email || !$pass){
		return array(
			'error' => true,
			'message' => t('admin_3')
		);
	}

	$db = $_SESSION['install']['db'];
	os_db_connect_installer($db['host'], $db['user'], $db['pass']);
	os_db_select_db($db['base']);

	$firstname = t('admin_default_firstname');
	$lastname = t('admin_default_lastname');
	$email_address = os_db_prepare_input($_POST['EMAIL_ADRESS']);
	$street_address = t('admin_default_street_address');
	$postcode = t('admin_default_postcode');
	$city = t('admin_default_city');
	$zone_id = 98;
	$state = t('admin_default_state');
	$country = 176;
	$telephone = t('admin_default_telephone');
	$password = os_db_prepare_input($_POST['PASSWORD']);
	$store_name = t('admin_default_store_name');
	$email_from = $email_address;
	$company = t('admin_default_company');

	os_db_perform(DB_PREFIX.'customers', array(
			'customers_id' => '1',
			'customers_status' => '0',
			'customers_firstname' => $firstname,
			'customers_lastname' => $lastname,
			'customers_email_address' => $email_address,
			'customers_default_address_id' => '1',
			'customers_telephone' => $telephone,
			'customers_password' => os_encrypt_password($password),
			'delete_user' => '0',
			'customers_date_added' => 'now()',
			'customers_last_modified' => 'now()',)
	);

	os_db_perform(DB_PREFIX.'customers_info', array(
			'customers_info_id' => '1',
			'customers_info_number_of_logons' => '0',
			'customers_info_date_account_created' => 'now()',
			'customers_info_date_account_last_modified' => 'now()')
	);

	os_db_perform(DB_PREFIX.'address_book', array(
			'customers_id' => '1',
			'entry_company' => ($company),
			'entry_firstname' => ($firstname),
			'entry_lastname' => ($lastname),
			'entry_street_address' => ($street_address),
			'entry_postcode' => ($postcode),
			'entry_city' => ($city),
			'entry_state' => ($state),
			'entry_country_id' => ($country),
			'entry_zone_id' => ($zone_id),
			'address_date_added' => 'now()',
			'address_last_modified' => 'now()')
	);

	os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".($email_address)."' WHERE configuration_key = 'STORE_OWNER_EMAIL_ADDRESS'");
	os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".($store_name)."' WHERE configuration_key = 'STORE_NAME'");
	os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".($email_from)."' WHERE configuration_key = 'EMAIL_FROM'");
	os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".($country)."' WHERE configuration_key = 'SHIPPING_ORIGIN_COUNTRY'");
	os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".($postcode)."' WHERE configuration_key = 'SHIPPING_ORIGIN_ZIP'");
	os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".($company)."' WHERE configuration_key = 'STORE_OWNER'");
	os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".($email_from)."' WHERE configuration_key = 'EMAIL_BILLING_FORWARDING_STRING'");
	os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".($email_from)."' WHERE configuration_key = 'EMAIL_BILLING_ADDRESS'");
	os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".($email_from)."' WHERE configuration_key = 'CONTACT_US_EMAIL_ADDRESS'");
	os_db_query("UPDATE ".DB_PREFIX."configuration SET configuration_value='".($email_from)."' WHERE configuration_key = 'EMAIL_SUPPORT_ADDRESS'");

	return array(
		'error' => false,
	);
}