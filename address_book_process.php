<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

include ('includes/top.php');

if (!isset ($_SESSION['customer_id']))
	os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));

if (isset ($_GET['action']) && ($_GET['action'] == 'deleteconfirm') && isset ($_GET['delete']) && is_numeric($_GET['delete'])) {
	os_db_query("delete from ".TABLE_ADDRESS_BOOK." where address_book_id = '".(int) $_GET['delete']."' and customers_id = '".(int) $_SESSION['customer_id']."'");

	$messageStack->add_session('addressbook', SUCCESS_ADDRESS_BOOK_ENTRY_DELETED, 'success');

	os_redirect(os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
}

// error checking when updating or adding an entry
$process = false;
if (isset ($_POST['action']) && (($_POST['action'] == 'process') || ($_POST['action'] == 'update'))) {
	$process = true;
	$error = false;

	if (ACCOUNT_GENDER == 'true')
		$gender = os_db_prepare_input($_POST['gender']);
	if (ACCOUNT_COMPANY == 'true')
		$company = os_db_prepare_input($_POST['company']);
	$firstname = os_db_prepare_input($_POST['firstname']);
	if (ACCOUNT_SECOND_NAME == 'true')
	$secondname = os_db_prepare_input($_POST['secondname']);
	if (ACCOUNT_LAST_NAME == 'true')
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

			$messageStack->add('addressbook', ENTRY_GENDER_ERROR);
		}
	}

	if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
		$error = true;

		$messageStack->add('addressbook', ENTRY_FIRST_NAME_ERROR);
	}
	if (ACCOUNT_LAST_NAME == 'true')
	{
		if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
			$error = true;
			
			$messageStack->add('addressbook', ENTRY_LAST_NAME_ERROR);
		}
	}

   if (ACCOUNT_STREET_ADDRESS == 'true') {
	if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
		$error = true;

		$messageStack->add('addressbook', ENTRY_STREET_ADDRESS_ERROR);
	}
  }

   if (ACCOUNT_POSTCODE == 'true') {
	if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
		$error = true;

		$messageStack->add('addressbook', ENTRY_POST_CODE_ERROR);
	}
  }

   if (ACCOUNT_CITY == 'true') {
	if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
		$error = true;

		$messageStack->add('addressbook', ENTRY_CITY_ERROR);
	}
  }

   if (ACCOUNT_COUNTRY == 'true') {
	if (is_numeric($country) == false) {
		$error = true;

		$messageStack->add('addressbook', ENTRY_COUNTRY_ERROR);
	}
  }

	if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
		$zone_id = 0;
		$check_query = os_db_query("select count(*) as total from ".TABLE_ZONES." where zone_country_id = '".(int) $country."'");
		$check = os_db_fetch_array($check_query);
		$entry_state_has_zones = ($check['total'] > 0);
		if ($entry_state_has_zones == true) {
			$zone_query = os_db_query("select distinct zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and zone_name = '" . os_db_input($state) . "'"); 
			if (os_db_num_rows($zone_query) == 1) {
				$zone = os_db_fetch_array($zone_query);
				$zone_id = $zone['zone_id'];
			} else {
				$error = true;

				$messageStack->add('addressbook', ENTRY_STATE_ERROR_SELECT);
			}
		} else {
			if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
				$error = true;

				$messageStack->add('addressbook', ENTRY_STATE_ERROR);
			}
		}
	}

	if ($error == false) {
		$sql_data_array = array ('entry_firstname' => $firstname, 'entry_secondname' => $secondname, 'entry_lastname' => $lastname, 'entry_street_address' => $street_address, 'entry_postcode' => $postcode, 'entry_city' => $city, 'entry_country_id' => (int) $country,'address_last_modified' => 'now()');

		if (ACCOUNT_GENDER == 'true')
			$sql_data_array['entry_gender'] = $gender;
		if (ACCOUNT_COMPANY == 'true')
			$sql_data_array['entry_company'] = $company;
		if (ACCOUNT_SUBURB == 'true')
			$sql_data_array['entry_suburb'] = $suburb;
		if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
			if ($zone_id > 0) {
				$sql_data_array['entry_zone_id'] = (int) $zone_id;
				$sql_data_array['entry_state'] = '';
			} else {
				$sql_data_array['entry_zone_id'] = '0';
				$sql_data_array['entry_state'] = $state;
			}
		}

		if ($_POST['action'] == 'update') {
			os_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "address_book_id = '".(int) $_GET['edit']."' and customers_id ='".(int) $_SESSION['customer_id']."'");

			if ((isset ($_POST['primary']) && ($_POST['primary'] == 'on')) || ($_GET['edit'] == $_SESSION['customer_default_address_id'])) {
				$_SESSION['customer_first_name'] = $firstname;
				$_SESSION['customer_second_name'] = $secondname;
				$_SESSION['customer_country_id'] = $country_id;
				$_SESSION['customer_zone_id'] = (($zone_id > 0) ? (int) $zone_id : '0');
				$_SESSION['customer_default_address_id'] = (int) $_GET['edit'];

				$sql_data_array = array ('customers_firstname' => $firstname, 'customers_secondname' => $secondname, 'customers_lastname' => $lastname, 'customers_default_address_id' => (int) $_GET['edit'],'customers_last_modified' => 'now()');

				if (ACCOUNT_GENDER == 'true')
					$sql_data_array['customers_gender'] = $gender;

				os_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '".(int) $_SESSION['customer_id']."'");
			}
		} else {
			$sql_data_array['customers_id'] = (int) $_SESSION['customer_id'];
			$sql_data_array['address_date_added'] = 'now()';
			os_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

			$new_address_book_id = os_db_insert_id();

			if (isset ($_POST['primary']) && ($_POST['primary'] == 'on')) {
				$_SESSION['customer_first_name'] = $firstname;
				$_SESSION['customer_second_name'] = $secondname;
				$_SESSION['customer_country_id'] = $country_id;
				$_SESSION['customer_zone_id'] = (($zone_id > 0) ? (int) $zone_id : '0');
				if (isset ($_POST['primary']) && ($_POST['primary'] == 'on'))
					$_SESSION['customer_default_address_id'] = $new_address_book_id;

				$sql_data_array = array ('customers_firstname' => $firstname, 'customers_secondname' => $secondname, 'customers_lastname' => $lastname,'customers_last_modified' => 'now()','customers_date_added' => 'now()');

				if (ACCOUNT_GENDER == 'true')
					$sql_data_array['customers_gender'] = $gender;
				if (isset ($_POST['primary']) && ($_POST['primary'] == 'on'))
					$sql_data_array['customers_default_address_id'] = $new_address_book_id;

				os_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '".(int) $_SESSION['customer_id']."'");
			}
		}

		$messageStack->add_session('addressbook', SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED, 'success');

		os_redirect(os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
	}
}

if (isset ($_GET['edit']) && is_numeric($_GET['edit'])) {
	$entry_query = os_db_query("select entry_gender, entry_company, entry_firstname, entry_secondname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_zone_id, entry_country_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $_SESSION['customer_id']."' and address_book_id = '".(int) $_GET['edit']."'");

	if (os_db_num_rows($entry_query) == false) {
		$messageStack->add_session('addressbook', ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY);

		os_redirect(os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
	}

	$entry = os_db_fetch_array($entry_query);
}
elseif (isset ($_GET['delete']) && is_numeric($_GET['delete'])) {
	if ($_GET['delete'] == $_SESSION['customer_default_address_id']) {
		$messageStack->add_session('addressbook', WARNING_PRIMARY_ADDRESS_DELETION, 'warning');

		os_redirect(os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
	} else {
		$check_query = os_db_query("select count(*) as total from ".TABLE_ADDRESS_BOOK." where address_book_id = '".(int) $_GET['delete']."' and customers_id = '".(int) $_SESSION['customer_id']."'");
		$check = os_db_fetch_array($check_query);

		if ($check['total'] < 1) {
			$messageStack->add_session('addressbook', ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY);

			os_redirect(os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
		}
	}
} else {
	$entry = array ();
}

if (!isset ($_GET['delete']) && !isset ($_GET['edit'])) {
	if (os_count_customer_address_book_entries() >= MAX_ADDRESS_BOOK_ENTRIES) {
		$messageStack->add_session('addressbook', ERROR_ADDRESS_BOOK_FULL);

		os_redirect(os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
	}
}

$breadcrumb->add(NAVBAR_TITLE_1_ADDRESS_BOOK_PROCESS, os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ADDRESS_BOOK_PROCESS, os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));

if (isset ($_GET['edit']) && is_numeric($_GET['edit'])) {
	$breadcrumb->add(NAVBAR_TITLE_MODIFY_ENTRY_ADDRESS_BOOK_PROCESS, os_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'edit='.$_GET['edit'], 'SSL'));
}
elseif (isset ($_GET['delete']) && is_numeric($_GET['delete'])) {
	$breadcrumb->add(NAVBAR_TITLE_DELETE_ENTRY_ADDRESS_BOOK_PROCESS, os_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'delete='.$_GET['delete'], 'SSL'));
} else {
	$breadcrumb->add(NAVBAR_TITLE_ADD_ENTRY_ADDRESS_BOOK_PROCESS, os_href_link(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL'));
}

require (dir_path('includes').'header.php');
if (isset ($_GET['delete']) == false)
	$action = os_draw_form('addressbook', os_href_link(FILENAME_ADDRESS_BOOK_PROCESS, (isset ($_GET['edit']) ? 'edit='.$_GET['edit'] : ''), 'SSL'), 'post') . os_draw_hidden_field('required', 'gender,firstname,lastname,address,postcode,city,state,country', 'id="required"');

$osTemplate->assign('FORM_ACTION', $action);
//if ($messageStack->size('addressbook') > 0) {
//	$osTemplate->assign('error', $messageStack->output('addressbook'));

//}

if (isset ($_GET['delete'])) {
	$osTemplate->assign('delete', '1');
	$osTemplate->assign('ADDRESS', os_address_label($_SESSION['customer_id'], $_GET['delete'], true, ' ', '<br />'));
	
  	$_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	}
	
	$osTemplate->assign('BUTTON_BACK', $_array['code']);
	
	$_array = array('img' => 'button_delete.gif', 
	                                'href' => os_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'delete='.$_GET['delete'].'&action=deleteconfirm', 'SSL'), 
									'alt' => IMAGE_BUTTON_DELETE,								
									'code' => ''
	);
	
	$_array = apply_filter('button_delete', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	}
	
	$osTemplate->assign('BUTTON_DELETE', $_array['code']);
} else {

	include (DIR_WS_MODULES.'address_book_details.php');

	if (isset ($_GET['edit']) && is_numeric($_GET['edit'])) 
	{
	
		$_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	}
	
		$osTemplate->assign('BUTTON_BACK', $_array['code']);
	
    //buttons	
	$_array = array('img' => 'button_update.gif', 
	                                'href' => '', 
									'alt' => IMAGE_BUTTON_UPDATE, 'code' => '');
									
	$_array = apply_filter('button_update', $_array);	
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], null, $_array['alt']);
	}
	
		$osTemplate->assign('BUTTON_UPDATE', os_draw_hidden_field('action', 'update').os_draw_hidden_field('edit', $_GET['edit']).$_array['code']);
     //--//buttons	
	} 
	else {
		if (sizeof($_SESSION['navigation']->snapshot) > 0) {
			$back_link = os_href_link($_SESSION['navigation']->snapshot['page'], os_array_to_string($_SESSION['navigation']->snapshot['get'], array (os_session_name())), $_SESSION['navigation']->snapshot['mode']);
		} else {
			$back_link = os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL');
		}
		
		$_array = array('img' => 'button_back.gif', 
	                                'href' => $back_link, 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	   );
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	}
	
		$osTemplate->assign('BUTTON_BACK', $_array['code']);
		$osTemplate->assign('BUTTON_UPDATE', os_draw_hidden_field('action', 'process').button_continue_submit());

	}
	$osTemplate->assign('FORM_END', '</form>');

}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/address_book_process.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ADDRESS_BOOK_PROCESS.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ADDRESS_BOOK_PROCESS.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>