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

$parameters = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
  	$parameters .= '&' . $key . '=' . urlencode(stripslashes($value));
}
  
if(MODULE_PAYMENT_PAYPAL_IPN_GATEWAY_SERVER == 'Live') {
	$server = 'www.paypal.com';
}else{
	$server = 'www.sandbox.paypal.com';
}

$fsocket = false;
$curl = false;
$result = false;

if ((PHP_VERSION >= 4.3) && ($fp = @fsockopen('ssl://' . $server, 443, $errno, $errstr, 30)))	{
  	$fsocket = true;
}
elseif (function_exists('curl_exec')) {
	$curl = true;
}
elseif ($fp = @fsockopen($server, 80, $errno, $errstr, 30)) {
    $fsocket = true;
}

if ($fsocket == true) {
    $header = 'POST /cgi-bin/webscr HTTP/1.0' . "\r\n" .
              'Host: ' . $server . "\r\n" .
              'Content-Type: application/x-www-form-urlencoded' . "\r\n" .
              'Content-Length: ' . strlen($parameters) . "\r\n" .
              'Connection: close' . "\r\n\r\n";

    @fputs($fp, $header . $parameters);

    $string = '';
    while (!@feof($fp)) {
      $res = @fgets($fp, 1024);
      $string .= $res;

      if (($res == 'VERIFIED') || ($res == 'INVALID')) {
        $result = $res;
        break;
      }
    }

    @fclose($fp);
}
elseif ($curl == true) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://' . $server . '/cgi-bin/webscr');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);

    curl_close($ch);
}
  
if(isset($_POST['invoice']) && is_numeric($_POST['invoice']) && ($_POST['invoice'] > 0)) {
  	$order_query = os_db_query("SELECT	currency, currency_value
  								 FROM " . TABLE_ORDERS . "
  								 WHERE orders_ident_key = '" . os_db_prepare_input($_POST['invoice']) . "' 
								 AND customers_id = '" . (int)$_POST['custom'] . "'");
								 
	if(os_db_num_rows($order_query) > 0) {
		$order = os_db_fetch_array($order_query);
		$total_query = os_db_query("SELECT value
									 FROM " . TABLE_ORDERS_TOTAL . " 
									 WHERE orders_ident_key = '" . os_db_prepare_input($_POST['invoice']) . "' 
									 AND class = 'ot_total' limit 1");
		
		$total = os_db_fetch_array($total_query);
		
		$comment_status = os_db_prepare_input($_POST['payment_status']) . ' ' . os_db_prepare_input($_POST['mc_gross']) . os_db_prepare_input($_POST['mc_currency']) . '.';
		$comment_status .= ' ' . os_db_prepare_input($_POST['first_name']) . ' ' . os_db_prepare_input($_POST['last_name']) . ' ' . os_db_prepare_input($_POST['payer_email']);
		
		if(isset($_POST['payer_status'])) {
			$comment_status .= ' is ' . os_db_prepare_input($_POST['payer_status']);
		}
		
		$comment_status .= '.' . $crlf . $crlf . ' [';
		
		if(isset($_POST['test_ipn']) && is_numeric($_POST['test_ipn']) && ($_POST['test_ipn'] > 0)) {
			$debug = '(Sandbox-Test Mode) ';
		}
		
		$comment_status .= $crlf . 'Fee=' . os_db_prepare_input($_POST['mc_fee']) . os_db_prepare_input($_POST['mc_currency']);
		
		if(isset($_POST['pending_reason'])) {
			$comment_status .= $crlf . ' Pending Reason=' . os_db_prepare_input($_POST['pending_reason']);
		}
		
		if(isset($_POST['reason_code'])) {
			$comment_status .= $crlf . ' Reason Code=' . os_db_prepare_input($_POST['reason_code']);
		}
		
		$comment_status .= $crlf . ' Payment=' . os_db_prepare_input($_POST['payment_type']);
		$comment_status .= $crlf . ' Date=' . os_db_prepare_input($_POST['payment_date']);
		
		if(isset($_POST['parent_txn_id'])) {
			$comment_status .= $crlf . ' ParentID=' . os_db_prepare_input($_POST['parent_txn_id']);
		}
		
		$comment_status .= $crlf . ' ID=' . os_db_prepare_input($_POST['txn_id']);
		
		$order_status_id = MODULE_PAYMENT_PAYPAL_IPN_PREPARE_ORDER_STATUS_ID;
		
		if($result == 'VERIFIED') {
			if(($_POST['payment_status'] == 'Completed') AND ($_POST['business'] == MODULE_PAYMENT_PAYPAL_IPN_ID) AND ($_POST['mc_gross'] == number_format($total['value'] * $order['currency_value'], $osPrice->get_decimal_places($order['currency'])))) {
				if (MODULE_PAYMENT_PAYPAL_IPN_ORDER_STATUS_ID > 0) {
					$order_status_id = MODULE_PAYMENT_PAYPAL_IPN_ORDER_STATUS_ID;
				}
			}
			elseif(($_POST['payment_status'] == 'Denied') OR ($_POST['payment_status'] == 'Failed') OR ($_POST['payment_status'] == 'Refunded') OR ($_POST['payment_status'] == 'Reversed')) {
				$order_status_id = MODULE_PAYMENT_PAYPAL_IPN_DENIED_ORDER_STATUS_ID;
			} 
		}else{
			$debug .= '[INVALID VERIFIED FAILED] ';
			$order_status_id = MODULE_PAYMENT_PAYPAL_IPN_DENIED_ORDER_STATUS_ID;
			$error_reason = 'Received INVALID responce but invoice and Customer matched.' ;
		}
		
		$comment_status .= ']' ;
		
		os_db_query("UPDATE " . TABLE_ORDERS . " 
					  SET orders_status = '" . $order_status_id . "', 
						  last_modified = now() 
					  WHERE orders_id = '" . os_db_prepare_input($_POST['invoice']) . "'");
		
		$sql_data_array = array('orders_id' => os_db_prepare_input($_POST['invoice']),
								'orders_status_id' => $order_status_id,
								'date_added' => 'now()',
								'customer_notified' => '0',
								'comments' => 'PayPal IPN ' . $debug . $comment_status . '');
		
		os_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
	}else{
		$error_reason = 'No order found for invoice=' . os_db_prepare_input($_POST['invoice']) . ' with customer=' . (int)$_POST['custom'] . '.' ;
	}
}else{
		$error_reason = 'No invoice id found on received data.' ;
}

if(os_not_null(MODULE_PAYMENT_PAYPAL_IPN_DEBUG_EMAIL) && strlen($error_reason)) {
	$email_body = $error_reason . "\n\n";
	$email_body .= $_SERVER["REQUEST_METHOD"] . " - " .$_SERVER["REMOTE_ADDR"] . " - " .$_SERVER["HTTP_REFERER"] . " - " .$_SERVER["HTTP_ACCEPT"] . "\n\n";
	$email_body .= '$_POST:' . "\n\n";

	foreach($_POST as $key => $value) {
		$email_body .= $key . '=' . $value . "\n";
	}
		
	$email_body .= "\n" . '$_GET:' . "\n\n";

	foreach ($_GET as $key => $value) {
		$email_body .= $key . '=' . $value . "\n";
	}

	os_php_mail(
		EMAIL_BILLING_ADDRESS,
		EMAIL_BILLING_NAME,
		MODULE_PAYMENT_PAYPAL_IPN_DEBUG_EMAIL,
		MODULE_PAYMENT_PAYPAL_IPN_DEBUG_EMAIL,
		'',
		EMAIL_BILLING_ADDRESS,
		EMAIL_BILLING_NAME,
		false,
		false,
		'PayPal IPN Invalid Process',
		$email_body,
		$email_body
	);
}
require('includes/bottom.php');
?>