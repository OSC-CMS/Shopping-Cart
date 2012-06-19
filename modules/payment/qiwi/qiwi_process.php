<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/
/*  Copyright (c) 2010 VaM Shop, http://vamshop.com */

require_once(dirname(__FILE__) .'/class/nusoap.php');
        
$server = new nusoap_server;
$server->register('updateBill');
$server->service($HTTP_RAW_POST_DATA);

function updateBill($login, $password, $txn, $status) {

//обработка возможных ошибок авторизации
if ( $login != MODULE_PAYMENT_QIWI_ID )
return 150;

if ( !empty($password) && $password != md5(MODULE_PAYMENT_QIWI_SECRET_KEY) )
return 150;

// получаем номер заказа
$transaction = intval($transaction);

// меняем статус заказа при условии оплаты счёта
if ( $status == 60 ) {
	
  $sql_data_array = array('orders_status' => MODULE_PAYMENT_QIWI_ORDER_STATUS_ID);
  os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".$transaction."'");

  $sql_data_arrax = array('orders_id' => $transaction,
                          'orders_status_id' => MODULE_PAYMENT_QIWI_ORDER_STATUS_ID,
                          'date_added' => 'now()',
                          'customer_notified' => '0',
                          'comments' => 'QIWI accepted this order payment');
  os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

// Отправляем письмо клиенту и админу о смене статуса заказа

	require_once(_CLASS . 'order.php');
  
  	$order = new order($transaction);
  	$osTemplate = new osTemplate;

				// assign language to template for caching
				$osTemplate->assign('language', $_SESSION['language']);
				$osTemplate->caching = false;

				$osTemplate->assign('tpl_path', http_path('themes').CURRENT_TEMPLATE.'/');
				$osTemplate->assign('logo_path', http_path('themes').CURRENT_TEMPLATE.'/img/');

				$osTemplate->assign('NAME', $order->customer['firstname'].' '.$order->customer['lastname']);
				$osTemplate->assign('ORDER_NR', $transaction);
				$osTemplate->assign('ORDER_LINK', os_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id='.$transaction, 'SSL'));
				$osTemplate->assign('ORDER_DATE', os_date_long($order->info['date_purchased']));

			  $lang_query = os_db_query("select languages_id from " . TABLE_LANGUAGES . " where directory = '" . $_SESSION['language'] . "'");
			  $lang = os_db_fetch_array($lang_query);
			  $lang=$lang['languages_id'];
			
			  if (!isset($lang)) $lang=$_SESSION['languages_id'];

				$orders_status_array = array ();
				$orders_status_query = os_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$lang."'");
				while ($orders_status = os_db_fetch_array($orders_status_query)) {
					$orders_statuses[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
					$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
				}

				$osTemplate->assign('ORDER_STATUS', $orders_status_array[MODULE_PAYMENT_QIWI_ORDER_STATUS_ID]);

				$html_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language'].'/change_order_mail.html');
				$txt_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language'].'/change_order_mail.txt');

				include_once (dirname(__FILE__).'/'.$_SESSION['language'].'.php');

            // create subject
           $order_subject = str_replace('{$nr}', $transaction, MODULE_PAYMENT_QIWI_EMAIL_SUBJECT);

	// send mail to admin
	os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, EMAIL_BILLING_ADDRESS, STORE_NAME, EMAIL_BILLING_FORWARDING_STRING, $order->customer['email_address'], $order->customer['firstname'], '', '', $order_subject, $html_mail, $txt_mail);

	// send mail to customer
	os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $order->customer['email_address'], $order->customer['firstname'].' '.$order->customer['lastname'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $order_subject, $html_mail, $txt_mail);

	
}

}
?>