<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Прямой доступ  не допускается.');

// check if customer is allowed to send this order!
$order_query_check = os_db_query("SELECT customers_id FROM ".TABLE_ORDERS." WHERE orders_id='".$insert_id."'");
$order_check = os_db_fetch_array($order_query_check);

if ($_SESSION['customer_id'] == $order_check['customers_id'])
{
	//    global $order;
	$order = new order($insert_id);

	$osTemplate->assign('address_label_customer', os_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
	$osTemplate->assign('address_label_shipping', os_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
	if ($_SESSION['credit_covers'] != '1')
	{
		$osTemplate->assign('address_label_payment', os_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
	}
	$osTemplate->assign('csID', $order->customer['csID']);

	$semextrfields = osDBquery("select * from ".TABLE_EXTRA_FIELDS." where fields_required_email = '1'");
	while($dataexfes = os_db_fetch_array($semextrfields,true)) 
	{
		$cusextrfields = osDBquery("select * from " . TABLE_CUSTOMERS_TO_EXTRA_FIELDS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "' and fields_id = '" . $dataexfes['fields_id'] . "'");
		$rescusextrfields = os_db_fetch_array($cusextrfields,true);

		$extrfieldsinf = osDBquery("select fields_name from " . TABLE_EXTRA_FIELDS_INFO . " where fields_id = '" . $dataexfes['fields_id'] . "' and languages_id = '" . $_SESSION['languages_id'] . "'");

		$extrfieldsres = os_db_fetch_array($extrfieldsinf,true);
		$extra_fields .= $extrfieldsres['fields_name'] . ' : ' .
		$rescusextrfields['value'] . "\n";
		$osTemplate->assign('customer_extra_fields', $extra_fields);
	}

	$order_total = $order->getTotalData($insert_id);
	$osTemplate->assign('order_data', $order->getOrderData($insert_id));
	$osTemplate->assign('order_total', $order_total['data']);

	// assign language to template for caching
	$osTemplate->assign('language', $_SESSION['language']);
	$osTemplate->assign('logo_path', _HTTP_THEMES_C.'img/');
	$osTemplate->assign('oID', $insert_id);
	if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') 
	{
		include (_MODULES.'payment/'.$order->info['payment_method'].'/'.$_SESSION['language'].'.php');
		$payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
	}
	$osTemplate->assign('PAYMENT_METHOD', $payment_method);
	if ($order->info['shipping_method'] != '')
	{
		$shipping_method = $order->info['shipping_method'];
	}
	$osTemplate->assign('SHIPPING_METHOD', $shipping_method);
	$osTemplate->assign('DATE', os_date_long($order->info['date_purchased']));
	$osTemplate->assign('NAME', $order->customer['name']);
	$osTemplate->assign('COMMENTS', $order->info['comments']);
	$osTemplate->assign('EMAIL', $order->customer['email_address']);
	$osTemplate->assign('PHONE',$order->customer['telephone']);

	// PAYMENT MODUL TEXTS
	// EU Bank Transfer
	if ($order->info['payment_method'] == 'eustandardtransfer')
	{
		$osTemplate->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION);
		$osTemplate->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION));
	}

	// MONEYORDER
	if ($order->info['payment_method'] == 'moneyorder')
	{
		$osTemplate->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION);
		$osTemplate->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION));
	}

	// WebMoney
	if ($order->info['payment_method'] == 'webmoney')
	{
		$osTemplate->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_WEBMONEY_TEXT_DESCRIPTION);
		$osTemplate->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_WEBMONEY_TEXT_DESCRIPTION));
	}

	// Yandex
	if ($order->info['payment_method'] == 'yandex')
	{
		$osTemplate->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_YANDEX_TEXT_DESCRIPTION);
		$osTemplate->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_YANDEX_TEXT_DESCRIPTION));
	}

	// dont allow cache
	$osTemplate->caching = false;

	$html_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/order_mail.html');
	$txt_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/order_mail.txt');

	// create subject
	$order_subject = str_replace('{$nr}', $insert_id, EMAIL_BILLING_SUBJECT_ORDER);
	$order_subject = str_replace('{$date}', strftime(DATE_FORMAT_LONG), $order_subject);
	$order_subject = str_replace('{$lastname}', $order->customer['lastname'], $order_subject);
	$order_subject = str_replace('{$firstname}', $order->customer['firstname'], $order_subject);

	// send mail to admin
	os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, EMAIL_BILLING_ADDRESS, STORE_NAME, EMAIL_BILLING_FORWARDING_STRING, $order->customer['email_address'], $order->customer['firstname'], '', '', $order_subject, $html_mail, $txt_mail);

	// send mail to customer
	if ($order->customer['email_address'])
		os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $order->customer['email_address'], $order->customer['firstname'].' '.$order->customer['lastname'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $order_subject, $html_mail, $txt_mail);

	// СМС уведомления
	$smsSetting = $cartet->sms->setting();

	if ($smsSetting['sms_status'] == 1)
	{
		$getDefaultSms = $cartet->sms->getDefaultSms();

		// шаблон смс письма
		$osTemplate->caching = 0;
		$smsText = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/order_mail_sms.txt');

		// уведомление администратора
		if ($getDefaultSms['phone'] && $smsSetting['sms_order_admin'] == 1)
		{
			$cartet->sms->send($smsText);
		}

		// уведомление покупателя
		if ($order->customer['telephone'] && $smsSetting['sms_order'] == 1)
		{
			$cartet->sms->send($smsText, $order->customer['telephone']);
		}
	}

	if (AFTERBUY_ACTIVATED == 'true')
	{
		require_once (dir_path('class').'afterbuy.php');
		$aBUY = new os_afterbuy_functions($insert_id);
		if ($aBUY->order_send())
			$aBUY->process_order();
	}
}
else
{
	$osTemplate->assign('language', $_SESSION['language']);
	$osTemplate->assign('ERROR', 'You are not allowed to view this order!');
	$osTemplate->display(CURRENT_TEMPLATE.'/module/error_message.html');
}
?>