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
//$osTemplate = new osTemplate;


if (isset ($_GET['action']) && ($_GET['action'] == 'process')) {
	$vlcode = os_random_charcode(32);
	$link = os_href_link(FILENAME_NEWSLETTER, 'action=activate&email='.os_db_input($_POST['email']).'&key='.$vlcode, 'NONSSL');
	$osTemplate->assign('language', $_SESSION['language']);
	$osTemplate->assign('logo_path', _HTTP_THEMES_C.'img/');
	$osTemplate->assign('EMAIL', os_db_input($_POST['email']));
	$osTemplate->assign('LINK', $link);
	$osTemplate->caching = false;
	$html_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/newsletter_mail.html');
	$txt_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/newsletter_mail.txt');

	if (($_POST['check'] == 'inp') && (isset($_SESSION['customer_id']) or ($_POST['captcha'] == $_SESSION['captcha_keystring']))) 
	{

		$check_mail_query = os_db_query("select customers_email_address, mail_status from ".TABLE_NEWSLETTER_RECIPIENTS." where customers_email_address = '".os_db_input($_POST['email'])."'");
		
		if (!os_db_num_rows($check_mail_query)) 
		{

			if (isset ($_SESSION['customer_id'])) 
			{
				$customers_id = $_SESSION['customer_id'];
				$customers_status = $_SESSION['customers_status']['customers_status_id'];
				$customers_firstname = $_SESSION['customer_first_name'];
				$customers_lastname = $_SESSION['customer_last_name'];
			} 
			else 
			{
			    if ($_POST['captcha'] == $_SESSION['captcha_keystring'])
				{
				$check_customer_mail_query = os_db_query("select customers_id, customers_status, customers_firstname, customers_lastname, customers_email_address from ".TABLE_CUSTOMERS." where customers_email_address = '".os_db_input($_POST['email'])."'");
				if (!os_db_num_rows($check_customer_mail_query)) {
					$customers_id = '0';
					$customers_status = '1';
					$customers_firstname = TEXT_CUSTOMER_GUEST;
					$customers_lastname = '';
				} else {
					$check_customer = os_db_fetch_array($check_customer_mail_query);
					$customers_id = $check_customer['customers_id'];
					$customers_status = $check_customer['customers_status'];
					$customers_firstname = $check_customer['customers_firstname'];
					$customers_lastname = $check_customer['customers_lastname'];
				}
                }
				else
				{
				    $info_message = TEXT_WRONG_CODE;
				}
			}

			$sql_data_array = array ('customers_email_address' => os_db_input($_POST['email']), 'customers_id' => os_db_input($customers_id), 'customers_status' => os_db_input($customers_status), 'customers_firstname' => os_db_input($customers_firstname), 'customers_lastname' => os_db_input($customers_lastname), 'mail_status' => '0', 'mail_key' => os_db_input($vlcode), 'date_added' => 'now()');
			os_db_perform(TABLE_NEWSLETTER_RECIPIENTS, $sql_data_array);

			$info_message = TEXT_EMAIL_INPUT;

			if (SEND_EMAILS == true) 
			{
				os_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, os_db_input($_POST['email']), '', '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', TEXT_EMAIL_SUBJECT, $html_mail, $txt_mail);
			}

		} 
		else 
		{
			$check_mail = os_db_fetch_array($check_mail_query);

			if ($check_mail['mail_status'] == '0') 
			{

				$info_message = TEXT_EMAIL_EXIST_NO_NEWSLETTER;

				if (SEND_EMAILS == true) 
				{
					os_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, os_db_input($_POST['email']), '', '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', TEXT_EMAIL_SUBJECT, $html_mail, $txt_mail);
				}

			} 
			else 
			{
				$info_message = TEXT_EMAIL_EXIST_NEWSLETTER;
			}

		}

	} 
	else 
	{
	    if ($_POST['captcha'] != $_SESSION['captcha_keystring']) $info_message = TEXT_WRONG_CODE;
	}

	if ($_POST['check'] == 'del' && ((isset($_SESSION['customer_id'])) or ($_POST['captcha'] == $_SESSION['captcha_keystring']))) {

		$check_mail_query = os_db_query("select customers_email_address from ".TABLE_NEWSLETTER_RECIPIENTS." where customers_email_address = '".os_db_input($_POST['email'])."'");
		
		if (!os_db_num_rows($check_mail_query)) 
		{
			$info_message = TEXT_EMAIL_NOT_EXIST;
		} 
		else {
			$del_query = os_db_query("delete from ".TABLE_NEWSLETTER_RECIPIENTS." where customers_email_address ='".os_db_input($_POST['email'])."'");
			$info_message = TEXT_EMAIL_DEL;
		}
	}
}

if (isset ($_GET['action']) && ($_GET['action'] == 'activate')) {
	$check_mail_query = os_db_query("select mail_key from ".TABLE_NEWSLETTER_RECIPIENTS." where customers_email_address = '".os_db_input($_GET['email'])."'");
	if (!os_db_num_rows($check_mail_query)) {
		$info_message = TEXT_EMAIL_NOT_EXIST;
	} else {
		$check_mail = os_db_fetch_array($check_mail_query);
		if (!$check_mail['mail_key'] == $_GET['key']) {
			$info_message = TEXT_EMAIL_ACTIVE_ERROR;
		} else {
			os_db_query("update ".TABLE_NEWSLETTER_RECIPIENTS." set mail_status = '1' where customers_email_address = '".os_db_input($_GET['email'])."'");
			$info_message = TEXT_EMAIL_ACTIVE;
		}
	}
}

if (isset ($_GET['action']) && ($_GET['action'] == 'remove')) {
	$check_mail_query = os_db_query("select customers_email_address, mail_key from ".TABLE_NEWSLETTER_RECIPIENTS." where customers_email_address = '".os_db_input($_GET['email'])."' and mail_key = '".os_db_input($_GET['key'])."'");
	if (!os_db_num_rows($check_mail_query)) {
		$info_message = TEXT_EMAIL_NOT_EXIST;
	} else {
		$check_mail = os_db_fetch_array($check_mail_query);
		if (!os_validate_password($check_mail['customers_email_address'], $_GET['key'])) {
			$info_message = TEXT_EMAIL_DEL_ERROR;
		} else {
			$del_query = os_db_query("delete from ".TABLE_NEWSLETTER_RECIPIENTS." where  customers_email_address ='".os_db_input($_GET['email'])."' and mail_key = '".os_db_input($_GET['key'])."'");
			$info_message = TEXT_EMAIL_DEL;
		}
	}
}

$breadcrumb->add(NAVBAR_TITLE_NEWSLETTER, os_href_link(FILENAME_NEWSLETTER, '', 'NONSSL'));

require (dir_path('includes').'header.php');

$osTemplate->assign('CAPTCHA_IMG', '<img src="'.os_href_link(FILENAME_DISPLAY_CAPTCHA).'" alt="captcha" name="captcha" />');
$osTemplate->assign('CAPTCHA_INPUT', os_draw_input_field('captcha', '', 'size="6"', 'text', false));

$osTemplate->assign('text_newsletter', TEXT_NEWSLETTER);
$osTemplate->assign('info_message', $info_message);
$osTemplate->assign('FORM_ACTION', os_draw_form('sign', os_href_link(FILENAME_NEWSLETTER, 'action=process', 'NONSSL')));
$osTemplate->assign('INPUT_EMAIL', os_draw_input_field('email', os_db_input($_POST['email'])));
$osTemplate->assign('CHECK_INP', os_draw_radio_field('check', 'inp'));
$osTemplate->assign('CHECK_DEL', os_draw_radio_field('check', 'del'));

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
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/newsletter.html');
$osTemplate->assign('main_content', $main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_NEWSLETTER.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_NEWSLETTER.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>