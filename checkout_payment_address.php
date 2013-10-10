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
//$osTemplate = new osTemplate;


// if the customer is not logged on, redirect them to the login page
if (!isset ($_SESSION['customer_id']))
	os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1)
	os_redirect(os_href_link(FILENAME_SHOPPING_CART));

$error = false;
$process = false;
if (isset ($_POST['action']) && ($_POST['action'] == 'submit')) {
	// process a new billing address
	if (os_not_null($_POST['firstname']) && os_not_null($_POST['lastname']) && os_not_null($_POST['street_address'])) {
		$process = true;

		if (ACCOUNT_GENDER == 'true')
			$gender = os_db_prepare_input($_POST['gender']);
		if (ACCOUNT_COMPANY == 'true')
			$company = os_db_prepare_input($_POST['company']);
		$firstname = os_db_prepare_input($_POST['firstname']);
	if (ACCOUNT_SECOND_NAME == 'true')
	$secondname = os_db_prepare_input($_POST['secondname']);
		$lastname = os_db_prepare_input($_POST['lastname']);
      if (ACCOUNT_STREET_ADDRESS == 'true')
		$street_address = os_db_prepare_input($_POST['street_address']);
		if (ACCOUNT_SUBURB == 'true')
			$suburb = os_db_prepare_input($_POST['suburb']);
      if (ACCOUNT_POSTCODE == 'true')
		$postcode = os_db_prepare_input($_POST['postcode']);
      if (ACCOUNT_CITY == 'true')
		$city = os_db_prepare_input($_POST['city']);
      if (ACCOUNT_COUNTRY == 'true') {
	   $country = os_db_prepare_input($_POST['country']);
   	} else {
      $country = STORE_COUNTRY;
   	}
		if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
			$zone_id = os_db_prepare_input($_POST['zone_id']);
			$state = os_db_prepare_input($_POST['state']);
		}

		if (ACCOUNT_GENDER == 'true') {
			if (($gender != 'm') && ($gender != 'f')) {
				$error = true;

				$messageStack->add('checkout_address', ENTRY_GENDER_ERROR);
			}
		}

		if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_FIRST_NAME_ERROR);
		}

		if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_LAST_NAME_ERROR);
		}

   if (ACCOUNT_STREET_ADDRESS == 'true') {
		if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_STREET_ADDRESS_ERROR);
		}
     }

   if (ACCOUNT_POSTCODE == 'true') {
		if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_POST_CODE_ERROR);
		}
     }

   if (ACCOUNT_CITY == 'true') {
		if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_CITY_ERROR);
		}
     }

		if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
			$zone_id = 0;
			$check_query = os_db_query("select count(*) as total from ".TABLE_ZONES." where zone_country_id = '".(int) $country."'");
			$check = os_db_fetch_array($check_query);
			$entry_state_has_zones = ($check['total'] > 0);
			if ($entry_state_has_zones == true) {
				$zone_query = os_db_query("select distinct zone_id from ".TABLE_ZONES." where zone_country_id = '".(int) $country."' and (zone_name like '".os_db_input($state)."%' or zone_code like '%".os_db_input($state)."%')");
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

					$messageStack->add('checkout_address', ENTRY_STATE_ERROR);
				}
			}
		}

		if ((is_numeric($country) == false) || ($country < 1)) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_COUNTRY_ERROR);
		}

		if ($error == false) {
			$sql_data_array = array ('customers_id' => $_SESSION['customer_id'], 'entry_firstname' => $firstname, 'entry_secondname' => $secondname, 'entry_lastname' => $lastname, 'entry_street_address' => $street_address, 'entry_postcode' => $postcode, 'entry_city' => $city, 'entry_country_id' => $country);

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

			$_SESSION['billto'] = os_db_insert_id();

			if (isset ($_SESSION['payment']))
				unset ($_SESSION['payment']);

			os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
		}
		// process the selected billing destination
	}
	elseif (isset ($_POST['address'])) {
		$reset_payment = false;
		if (isset ($_SESSION['billto'])) {
			if ($billto != $_POST['address']) {
				if (isset ($_SESSION['payment'])) {
					$reset_payment = true;
				}
			}
		}

		$_SESSION['billto'] = os_db_prepare_input($_POST['address']);

		$check_address_query = os_db_query("select count(*) as total from ".TABLE_ADDRESS_BOOK." where customers_id = '".$_SESSION['customer_id']."' and address_book_id = '".$_SESSION['billto']."'");
		$check_address = os_db_fetch_array($check_address_query);

		if ($check_address['total'] == '1') {
			if ($reset_payment == true)
				unset ($_SESSION['payment']);
			os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
		} else {
			unset ($_SESSION['billto']);
		}
		// no addresses to select from - customer decided to keep the current assigned address
	} else {
		$_SESSION['billto'] = $_SESSION['customer_default_address_id'];

		os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
	}
}

// if no billing destination address was selected, use their own address as default
if (!isset ($_SESSION['billto'])) {
	$_SESSION['billto'] = $_SESSION['customer_default_address_id'];
}

$breadcrumb->add(NAVBAR_TITLE_1_PAYMENT_ADDRESS, os_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_PAYMENT_ADDRESS, os_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));

$addresses_count = os_count_customer_address_book_entries();
require (_INCLUDES.'header.php');

$osTemplate->assign('FORM_ACTION', os_draw_form('checkout_address', os_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'), 'post', 'onsubmit="return checkform(this);"') . os_draw_hidden_field('required', 'gender,firstname,lastname,address,postcode,city,state,country', 'id="required"'));

//if ($messageStack->size('checkout_address') > 0) {
//	$osTemplate->assign('error', $messageStack->output('checkout_address'));

//}

if ($process == false) {
	$osTemplate->assign('ADDRESS_LABEL', os_address_label($_SESSION['customer_id'], $_SESSION['billto'], true, ' ', '<br />'));

	if ($addresses_count > 1) {

		$address_content = '';
		$radio_buttons = 0;

		$addresses_query = os_db_query("select address_book_id, entry_firstname as firstname, entry_secondname as secondname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".$_SESSION['customer_id']."'");
		while ($addresses = os_db_fetch_array($addresses_query)) {
			$format_id = os_get_address_format_id($addresses['country_id']);
			$address_content .= '';
			if ($addresses['address_book_id'] == $_SESSION['billto']) {
				$address_content .= ''."\n";
			} else {
				$address_content .= ''."\n";
			}
			$address_content .= '<p><span class="bold">'.$addresses['firstname'].' '.$addresses['secondname'].' '.$addresses['lastname'].'</span>&nbsp;'.os_draw_radio_field('address', $addresses['address_book_id'], ($addresses['address_book_id'] == $_SESSION['billto'])).'</p>
			                        <p>'.os_address_format($format_id, $addresses, true, ' ', ', ').'</p>';

			$radio_buttons ++;
		}
		$address_content .= '';
		$osTemplate->assign('BLOCK_ADDRESS', $address_content);

	}
}

if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) 
{
   require(_MODULES.'checkout_new_address.php');
}
$osTemplate->assign('BUTTON_CONTINUE', os_draw_hidden_field('action', 'submit').button_continue_submit());

if ($process == true) 
{

	$_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}
	
	$osTemplate->assign('BUTTON_BACK', $_array['code']);

}
$osTemplate->assign('FORM_END', '</form>');
$osTemplate->assign('language', $_SESSION['language']);

$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/checkout_payment_address.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_CHECKOUT_PAYMENT_ADDRESS.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_CHECKOUT_PAYMENT_ADDRESS.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>