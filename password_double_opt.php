<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require ('includes/top.php');

$case = double_opt;
$info_message = '';
if (isset ($_GET['action']) && ($_GET['action'] == 'first_opt_in')) {

	$check_customer_query = os_db_query("select customers_email_address, customers_id from ".TABLE_CUSTOMERS." where customers_email_address = '".os_db_input($_POST['email'])."'");
	$check_customer = os_db_fetch_array($check_customer_query);

	$vlcode = os_random_charcode(32);
	$link = os_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=verified&customers_id='.$check_customer['customers_id'].'&key='.$vlcode, 'NONSSL');

	$osTemplate->assign('language', $_SESSION['language']);
	$osTemplate->assign('logo_path', _HTTP_THEMES_C.'/img/');
	$osTemplate->assign('EMAIL', $check_customer['customers_email_address']);
	$osTemplate->assign('LINK', $link);
	$osTemplate->caching = false;
	$html_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/password_verification_mail.html');
	$txt_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/password_verification_mail.txt');

	if ($_POST['captcha'] == $_SESSION['captcha_keystring']) {
		if (!os_db_num_rows($check_customer_query)) {
			$case = wrong_mail;
			$info_message = TEXT_EMAIL_ERROR;
		} else {
			$case = first_opt_in;
			os_db_query("update ".TABLE_CUSTOMERS." set password_request_key = '".$vlcode."' where customers_id = '".$check_customer['customers_id']."'");
			os_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $check_customer['customers_email_address'], '', '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', TEXT_EMAIL_PASSWORD_FORGOTTEN, $html_mail, $txt_mail);

		}
	} else {
		$case = code_error;
		$info_message = TEXT_CODE_ERROR;
	}
}

if (isset ($_GET['action']) && ($_GET['action'] == 'verified')) {
	$check_customer_query = os_db_query("select customers_id, customers_email_address, password_request_key from ".TABLE_CUSTOMERS." where customers_id = '".(int)$_GET['customers_id']."' and password_request_key = '".os_db_input($_GET['key'])."'");
	$check_customer = os_db_fetch_array($check_customer_query);
	if (!os_db_num_rows($check_customer_query) || $_GET['key']=="") {

		$case = no_account;
		$info_message = TEXT_NO_ACCOUNT;
	} else {

		$newpass = os_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
		$crypted_password = os_encrypt_password($newpass);

		os_db_query("update ".TABLE_CUSTOMERS." set customers_password = '".$crypted_password."' where customers_email_address = '".$check_customer['customers_email_address']."'");
		os_db_query("update ".TABLE_CUSTOMERS." set password_request_key = '' where customers_id = '".$check_customer['customers_id']."'");
		$osTemplate->assign('language', $_SESSION['language']);
		$osTemplate->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'themes/'.CURRENT_TEMPLATE.'/img/');
		$osTemplate->assign('EMAIL', $check_customer['customers_email_address']);
		$osTemplate->assign('NEW_PASSWORD', $newpass);
		$osTemplate->caching = false;
		$html_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/new_password_mail.html');
		$txt_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/new_password_mail.txt');

		os_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $check_customer['customers_email_address'], '', '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', TEXT_EMAIL_PASSWORD_NEW_PASSWORD, $html_mail, $txt_mail);
		if (!isset ($mail_error)) {
			os_redirect(os_href_link(FILENAME_LOGIN, 'info_message='.urlencode(TEXT_PASSWORD_SENT), 'SSL', true, false));
		}
	}
}

$breadcrumb->add(NAVBAR_TITLE_PASSWORD_DOUBLE_OPT, os_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'NONSSL'));

require (dir_path('includes').'header.php');

switch ($case) {
	case first_opt_in :
		$osTemplate->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$osTemplate->assign('info_message', $info_message);
		$osTemplate->assign('info_message', TEXT_LINK_MAIL_SENDED);
		$osTemplate->assign('language', $_SESSION['language']);
		$osTemplate->caching = 0;
		$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/password_messages.html');

		break;
	case second_opt_in :
		$osTemplate->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$osTemplate->assign('info_message', $info_message);
		$osTemplate->assign('language', $_SESSION['language']);
		$osTemplate->caching = 0;
		$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/password_messages.html');
		break;
	case code_error :

		$osTemplate->assign('CAPTCHA_IMG', '<img src="'.os_href_link(FILENAME_DISPLAY_CAPTCHA).'" alt="captcha" name="captcha" />');
		$osTemplate->assign('CAPTCHA_INPUT', os_draw_input_field('captcha', '', 'size="6"', 'text', false));
		$osTemplate->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$osTemplate->assign('info_message', $info_message);
		$osTemplate->assign('FORM_ACTION', os_draw_form('sign', os_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=first_opt_in', 'NONSSL')));
		$osTemplate->assign('INPUT_EMAIL', os_draw_input_field('email', os_db_input($_POST['email'])));
		
		   //buttons	
	$_array = array('img' => 'button_send.gif', 
	                                'href' => '', 
									'alt' => IMAGE_BUTTON_LOGIN, 'code' => '');
									
	$_array = apply_filter('button_send', $_array);	
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], null, $_array['alt']);
	}
	
		$osTemplate->assign('BUTTON_SEND', $_array['code']);
		$osTemplate->assign('FORM_END', '</form>');
		$osTemplate->assign('language', $_SESSION['language']);
		$osTemplate->caching = 0;
		$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/password_double_opt_in.html');

		break;
	case wrong_mail :

		$osTemplate->assign('CAPTCHA_IMG', '<img src="'.os_href_link(FILENAME_DISPLAY_CAPTCHA).'" alt="captcha" name="captcha" />');
		$osTemplate->assign('CAPTCHA_INPUT', os_draw_input_field('captcha', '', 'size="6"', 'text', false));
		$osTemplate->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$osTemplate->assign('info_message', $info_message);
		$osTemplate->assign('FORM_ACTION', os_draw_form('sign', os_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=first_opt_in', 'NONSSL')));
		$osTemplate->assign('INPUT_EMAIL', os_draw_input_field('email', os_db_input($_POST['email'])));
		
				   //buttons	
	$_array = array('img' => 'button_send.gif', 
	                                'href' => '', 
									'alt' => IMAGE_BUTTON_LOGIN, 'code' => '');
									
	$_array = apply_filter('button_send', $_array);	
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], null, $_array['alt']);
	}
	
		$osTemplate->assign('BUTTON_SEND',  $_array['code']);
		
		$osTemplate->assign('FORM_END', '</form>');
		$osTemplate->assign('language', $_SESSION['language']);
		$osTemplate->caching = 0;
		$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/password_double_opt_in.html');

		break;
	case no_account :
		$osTemplate->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$osTemplate->assign('info_message', $info_message);
		$osTemplate->assign('language', $_SESSION['language']);
		$osTemplate->caching = 0;
		$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/password_messages.html');

		break;
	case double_opt :

		$osTemplate->assign('CAPTCHA_IMG', '<img src="'.os_href_link(FILENAME_DISPLAY_CAPTCHA).'" alt="captcha" name="captcha" />');
		$osTemplate->assign('CAPTCHA_INPUT', os_draw_input_field('captcha', '', 'size="6"', 'text', false));
		$osTemplate->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$osTemplate->assign('FORM_ACTION', os_draw_form('sign', os_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=first_opt_in', 'NONSSL')));
		$osTemplate->assign('INPUT_EMAIL', os_draw_input_field('email', os_db_input($_POST['email'])));
		$osTemplate->assign('BUTTON_SEND', button_continue_submit());
		$osTemplate->assign('FORM_END', '</form>');
		$osTemplate->assign('language', $_SESSION['language']);
		$osTemplate->caching = 0;
		$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/password_double_opt_in.html');

		break;
}

$osTemplate->assign('main_content', $main_content);
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_PASSWORD_DOUBLE_OPT.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_PASSWORD_DOUBLE_OPT.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>