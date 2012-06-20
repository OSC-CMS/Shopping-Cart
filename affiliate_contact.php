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



require_once(_LIB.'phpmailer/class.phpmailer.php');

if (!isset($_SESSION['affiliate_id'])) {
    os_redirect(os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
}

$error = false;
if (isset($_GET['action']) && ($_GET['action'] == 'send')) {
    if (os_validate_email(trim($_POST['email']))) {
        os_php_mail($_POST['email'], $_POST['name'], AFFILIATE_EMAIL_ADDRESS, STORE_OWNER, '', $_POST['email'], $_POST['name'], '', '', EMAIL_SUBJECT, $_POST['enquiry'], $_POST['enquiry']);
        if (!isset($mail_error)) {
            os_redirect(os_href_link(FILENAME_AFFILIATE_CONTACT, 'action=success'));
        }
        else {
            echo $mail_error;
        }
    }
    else {
        $error = true;
    }
}

$breadcrumb->add(NAVBAR_TITLE, os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_CONTACT, os_href_link(FILENAME_AFFILIATE_CONTACT));

require(dir_path('includes') . 'header.php');

if (isset($_GET['action']) && ($_GET['action'] == 'success')) {

	$osTemplate->assign('SUMMARY_LINK', button_continue( os_href_link(FILENAME_AFFILIATE_SUMMARY) ) );
}
else {
	// Get some values of the Affiliate
	$affili_sql = os_db_query("SELECT affiliate_firstname, affiliate_lastname, affiliate_email_address FROM " . TABLE_AFFILIATE . " WHERE affiliate_id = " . $_SESSION['affiliate_id']);
	$affili_res = os_db_fetch_array($affili_sql);
	
    $osTemplate->assign('FORM_ACTION', os_draw_form('contact_us', os_href_link(FILENAME_AFFILIATE_CONTACT, 'action=send')));
    $osTemplate->assign('INPUT_NAME', os_draw_input_field('name', $affili_res['affiliate_firstname'] . ' ' . $affili_res['affiliate_lastname']));
    $osTemplate->assign('INPUT_EMAIL', os_draw_input_field('email', $affili_res['affiliate_email_address']));
    $osTemplate->assign('error', $error);
    $osTemplate->assign('TEXTAREA_ENQUIRY', os_draw_textarea_field('enquiry', 'soft', 50, 15, $_POST['enquiry']));
    $osTemplate->assign('BUTTON_SUBMIT', button_continue_submit() );
}
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_contact.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');?>
