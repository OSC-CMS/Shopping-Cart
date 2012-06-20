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

if (ACTIVATE_GIFT_SYSTEM != 'true')
	os_redirect(FILENAME_DEFAULT);

require (_CLASS.'http_client.php');

//$osTemplate = new osTemplate;

if (!isset ($_SESSION['customer_id'])) {
	os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if (($_POST['back_x']) || ($_POST['back_y'])) {
	$_GET['action'] = '';
}
if ($_GET['action'] == 'send') {
	$error = false;
	if (!os_validate_email(trim($_POST['email']))) {
		$error = true;
		$error_email = ERROR_ENTRY_EMAIL_ADDRESS_CHECK;
	}
	$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$_SESSION['customer_id']."'");
	$gv_result = os_db_fetch_array($gv_query);
	$customer_amount = $gv_result['amount'];
	$gv_amount = trim(str_replace(",", ".", $_POST['amount']));
	if (ereg('[^0-9/.]', $gv_amount)) {
		$error = true;
		$error_amount = ERROR_ENTRY_AMOUNT_CHECK;
	}
	if ($gv_amount > $customer_amount || $gv_amount == 0) {
		$error = true;
		$error_amount = ERROR_ENTRY_AMOUNT_CHECK;
	}
}
if ($_GET['action'] == 'process') {
	$id1 = create_coupon_code($mail['customers_email_address']);
	$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id='".$_SESSION['customer_id']."'");
	$gv_result = os_db_fetch_array($gv_query);
	$new_amount = $gv_result['amount'] - str_replace(",", ".", $_POST['amount']);
	$new_amount = str_replace(",", ".", $new_amount);
	if ($new_amount < 0) {
		$error = true;
		$error_amount = ERROR_ENTRY_AMOUNT_CHECK;
		$_GET['action'] = 'send';
	} else {
		$gv_query = os_db_query("update ".TABLE_COUPON_GV_CUSTOMER." set amount = '".$new_amount."' where customer_id = '".$_SESSION['customer_id']."'");
		$gv_query = os_db_query("select customers_firstname, customers_lastname from ".TABLE_CUSTOMERS." where customers_id = '".$_SESSION['customer_id']."'");
		$gv_customer = os_db_fetch_array($gv_query);
		$gv_query = os_db_query("insert into ".TABLE_COUPONS." (coupon_type, coupon_code, date_created, coupon_amount) values ('G', '".$id1."', NOW(), '".str_replace(",", ".", os_db_input($_POST['amount']))."')");
		$insert_id = os_db_insert_id($gv_query);
		$gv_query = os_db_query("insert into ".TABLE_COUPON_EMAIL_TRACK." (coupon_id, customer_id_sent, sent_firstname, sent_lastname, emailed_to, date_sent) values ('".$insert_id."' ,'".$_SESSION['customer_id']."', '".addslashes($gv_customer['customers_firstname'])."', '".addslashes($gv_customer['customers_lastname'])."', '".os_db_input($_POST['email'])."', now())");

		$gv_email_subject = sprintf(EMAIL_GV_TEXT_SUBJECT, stripslashes($_POST['send_name']));

		$osTemplate->assign('language', $_SESSION['language']);
		$osTemplate->assign('tpl_path', _HTTP_THEMES_C);
		$osTemplate->assign('logo_path', _HTTP_THEMES_C.'img/');
		$osTemplate->assign('GIFT_LINK', os_href_link(FILENAME_GV_REDEEM, 'gv_no='.$id1, 'NONSSL', false));
		$osTemplate->assign('AMMOUNT', $osPrice->Format(str_replace(",", ".", $_POST['amount']), true));
		$osTemplate->assign('GIFT_CODE', $id1);
		$osTemplate->assign('MESSAGE', $_POST['message']);
		$osTemplate->assign('NAME', $_POST['to_name']);
		$osTemplate->assign('FROM_NAME', $_POST['send_name']);

		// dont allow cache
		$osTemplate->caching = false;

		$html_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/send_gift_to_friend.html');
		$txt_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/send_gift_to_friend.txt');

		// send mail
		os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $_POST['email'], $_POST['to_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $gv_email_subject, $html_mail, $txt_mail);

	}
}
$breadcrumb->add(NAVBAR_GV_SEND);

require (_INCLUDES.'header.php');

if ($_GET['action'] == 'process') {
	$osTemplate->assign('action', 'process');
	$osTemplate->assign('LINK_DEFAULT', button_continue());
}
if ($_GET['action'] == 'send' && !$error) {
	$osTemplate->assign('action', 'send');
	// validate entries
	$gv_amount = (double) $gv_amount;
	$gv_query = os_db_query("select customers_firstname, customers_lastname from ".TABLE_CUSTOMERS." where customers_id = '".$_SESSION['customer_id']."'");
	$gv_result = os_db_fetch_array($gv_query);
	$send_name = $gv_result['customers_firstname'].' '.$gv_result['customers_lastname'];
	$osTemplate->assign('FORM_ACTION', '<form action="'.os_href_link(FILENAME_GV_SEND, 'action=process', 'NONSSL').'" method="post">');
	$osTemplate->assign('MAIN_MESSAGE', sprintf(MAIN_MESSAGE, $osPrice->Format(str_replace(",", ".", $_POST['amount']), true), stripslashes($_POST['to_name']), $_POST['email'], stripslashes($_POST['to_name']), $osPrice->Format(str_replace(",", ".", $_POST['amount']), true), $send_name));
	if ($_POST['message']) {
		$osTemplate->assign('PERSONAL_MESSAGE', sprintf(PERSONAL_MESSAGE, $gv_result['customers_firstname']));
		$osTemplate->assign('POST_MESSAGE', stripslashes($_POST['message']));
	}
	$osTemplate->assign('HIDDEN_FIELDS', os_draw_hidden_field('send_name', $send_name).os_draw_hidden_field('to_name', stripslashes($_POST['to_name'])).os_draw_hidden_field('email', $_POST['email']).os_draw_hidden_field('amount', $gv_amount).os_draw_hidden_field('message', stripslashes($_POST['message'])));
	
	$_array = array('img' => 'button_back.gif', 
	                                'href' => '', 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = os_image_submit($_array['img'], $_array['alt'], 'name=back').'</a>';
	}
	
	$osTemplate->assign('LINK_BACK', $_array['code']);
	
	$_array = array('img' => 'button_send.gif', 
	                                'href' => '', 
									'alt' => IMAGE_BUTTON_CONTINUE, 'code' => '');
									
	$_array = apply_filter('button_send', $_array);	
	
	if (empty($_array['code']))
	{
	   $_array['code'] = os_image_submit($_array['img'], $_array['alt']);
	}
	
	$osTemplate->assign('LINK_SUBMIT', $_array['code']);
}
elseif ($_GET['action'] == '' || $error) {
	$osTemplate->assign('action', '');
	$osTemplate->assign('FORM_ACTION', '<form action="'.os_href_link(FILENAME_GV_SEND, 'action=send', 'NONSSL').'" method="post">');
	$osTemplate->assign('LINK_SEND', os_href_link(FILENAME_GV_SEND, 'action=send', 'NONSSL'));
	$osTemplate->assign('INPUT_TO_NAME', os_draw_input_field('to_name', stripslashes($_POST['to_name'])));
	$osTemplate->assign('INPUT_EMAIL', os_draw_input_field('email', $_POST['email']));
	$osTemplate->assign('ERROR_EMAIL', $error_email);
	$osTemplate->assign('INPUT_AMOUNT', os_draw_input_field('amount', $_POST['amount'], '', 'text', false));
	$osTemplate->assign('ERROR_AMOUNT', $error_amount);
	$osTemplate->assign('TEXTAREA_MESSAGE', os_draw_textarea_field('message', 'soft', 50, 15, stripslashes($_POST['message'])));
	$osTemplate->assign('LINK_SUBMIT', button_continue_submit());
}
$osTemplate->assign('FORM_END', '</form>');
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/gv_send.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_GV_SEND.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_GV_SEND.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>