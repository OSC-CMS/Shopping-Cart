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
	os_redirect(os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}
//$osTemplate = new osTemplate;


if ($session_started == false) {
	os_redirect(os_href_link(FILENAME_COOKIE_USAGE));
}

if (isset ($_GET['action']) && ($_GET['action'] == 'process')) {
	$email_address = os_db_prepare_input($_POST['email_address']);
	$password = os_db_prepare_input($_POST['password']);

	// Check if email exists
	$check_customer_query = os_db_query("select customers_id, customers_vat_id, customers_firstname,customers_lastname, customers_gender, customers_password, customers_email_address, login_tries, login_time, customers_default_address_id, customers_username from ".TABLE_CUSTOMERS." where customers_email_address = '".os_db_input($email_address)."' and account_type = '0'");
	if (!os_db_num_rows($check_customer_query)) {
		$_GET['login'] = 'fail';
		$info_message = TEXT_NO_EMAIL_ADDRESS_FOUND;
	} else {
		$check_customer = os_db_fetch_array($check_customer_query);

// Check the login is blocked while login_tries is more than 5 and blocktime is not over
	$blocktime = LOGIN_TIME; 	 																 			// time to block the login in seconds
	$time = time();  																				// time now as a timestamp
	$logintime = strtotime($check_customer['login_time']);  // conversion from the ISO date format to a timestamp
	$difference = $time - $logintime; 											// The difference time in seconds between the last login and now
  if ($check_customer['login_tries'] >= LOGIN_NUM and $difference < $blocktime) {
		// Action for bцse ?
    $osTemplate->assign('CAPTCHA_IMG', '<img src="'.FILENAME_DISPLAY_CAPTCHA.'" alt="captcha" name="captcha" />');    
    $osTemplate->assign('CAPTCHA_INPUT', os_draw_input_field('captcha', '', 'size="6" maxlength="6"', 'text', false));
    if ($_POST['captcha'] == $_SESSION['captcha_keystring']){
    // code ok
		// Check that password is good
		if (!os_validate_password($password, $check_customer['customers_password'])) {
			$_GET['login'] = 'fail';
      // Login tries + 1
		  os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '".os_db_input($email_address)."'");		
			$info_message = TEXT_LOGIN_ERROR;
		} else {
			if (SESSION_RECREATE == 'True') {
				os_session_recreate();
			}
      // Login tries = 0			$date_now = date('Ymd');
		  os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = 0, login_time = now() WHERE customers_email_address = '".os_db_input($email_address)."'");		
		  
			$check_country_query = os_db_query("select entry_country_id, entry_zone_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $check_customer['customers_id']."' and address_book_id = '".$check_customer['customers_default_address_id']."'");
			$check_country = os_db_fetch_array($check_country_query);

			$_SESSION['customer_gender'] = $check_customer['customers_gender'];
			$_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
			$_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
			$_SESSION['customer_id'] = $check_customer['customers_id'];
			$_SESSION['customer_vat_id'] = $check_customer['customers_vat_id'];
			$_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
			$_SESSION['customer_country_id'] = $check_country['entry_country_id'];
			$_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];



			os_db_query("update ".TABLE_CUSTOMERS_INFO." SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");
			os_write_user_info((int) $_SESSION['customer_id']);
			// restore cart contents
			$_SESSION['cart']->restore_contents();

			if ($_SESSION['cart']->count_contents() > 0) {
				os_redirect(os_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
			} else {
				os_redirect(os_href_link(FILENAME_DEFAULT));
			}

		}
    }else{
    // code falsch
    $info_message = TEXT_WRONG_CODE;
    // Login tries + 1
		os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '".os_db_input($email_address)."'");		
    }		
	} else {
		// Check that password is good
		if (!os_validate_password($password, $check_customer['customers_password'])) {
			$_GET['login'] = 'fail';
      // Login tries + 1
		  os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '".os_db_input($email_address)."'");		
			$info_message = TEXT_LOGIN_ERROR;
		} else {
			if (SESSION_RECREATE == 'True') {
				os_session_recreate();
			}
      // Login tries = 0			$date_now = date('Ymd');
		  os_db_query("update ".TABLE_CUSTOMERS." SET login_tries = 0, login_time = now() WHERE customers_email_address = '".os_db_input($email_address)."'");		
		  
			$check_country_query = os_db_query("select entry_country_id, entry_zone_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $check_customer['customers_id']."' and address_book_id = '".$check_customer['customers_default_address_id']."'");
			$check_country = os_db_fetch_array($check_country_query);

			$_SESSION['customer_gender'] = $check_customer['customers_gender'];
			$_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
			$_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
			$_SESSION['customer_id'] = $check_customer['customers_id'];
			$_SESSION['customer_vat_id'] = $check_customer['customers_vat_id'];
			$_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
			$_SESSION['customer_country_id'] = $check_country['entry_country_id'];
			$_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];
			$_SESSION['customers_username'] = $check_customer['customers_username'];



			os_db_query("update ".TABLE_CUSTOMERS_INFO." SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");
			os_write_user_info((int) $_SESSION['customer_id']);
			// restore cart contents
			$_SESSION['cart']->restore_contents();

			if ($_SESSION['cart']->count_contents() > 0) {
				os_redirect(os_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
			} else {
				os_redirect(os_href_link(FILENAME_DEFAULT));
			}

		}
	 }
	}
}

$breadcrumb->add(NAVBAR_TITLE_LOGIN, os_href_link(FILENAME_LOGIN, '', 'SSL'));
require (dir_path('includes').'header.php');

//if ($_GET['info_message']) $info_message = $_GET['info_message'];
$osTemplate->assign('info_message', $info_message);
$osTemplate->assign('account_option', ACCOUNT_OPTIONS);

$osTemplate->assign('BUTTON_NEW_ACCOUNT', button_continue(  os_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL')  ) );

       $_array = array('img' => 'button_login.gif', 'href' => '', 'alt' => TEXT_BUTTON_LOGIN, 'code' => '');
	
	   $_array = apply_filter('button_login', $_array);	
	
	   if (empty($_array['code']))
 	   {
		   $_array['code'] = buttonSubmit($_array['img'], null, $_array['alt']);
	   }
	     
	   
$osTemplate->assign('BUTTON_LOGIN', $_array['code']);

$osTemplate->assign('BUTTON_GUEST', button_continue(  os_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL')  ));

$osTemplate->assign('FORM_ACTION', os_draw_form('login', os_href_link(FILENAME_LOGIN, 'action=process', 'SSL')));
$osTemplate->assign('INPUT_MAIL', os_draw_input_field('email_address'));
$osTemplate->assign('INPUT_PASSWORD', os_draw_password_field('password'));
$osTemplate->assign('LINK_LOST_PASSWORD', os_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL'));
$osTemplate->assign('FORM_END', '</form>');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/login.html');
$osTemplate->assign('main_content', $main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_LOGIN.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_LOGIN.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>