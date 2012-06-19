<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/
/*  Copyright (c) 2010 os Shop, http://osshop.com */

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

function get_var($name, $default = 'none') {
  return (isset($_GET[$name])) ? $_GET[$name] : ((isset($_POST[$name])) ? $_POST[$name] : $default);
}

require (_CLASS.'order.php');

$xml_decoded=base64_decode($_POST['xml']);

$xml = simplexml_load_string($xml_decoded);
 
// checking and handling
if ($xml->status == 'success') {
if (number_format($xml->amount,0) == number_format($order->info['total'],0)) {
  $sql_data_array = array('orders_status' => MODULE_PAYMENT_LIQPAY_ORDER_STATUS_ID);
  os_db_perform('orders', $sql_data_array, 'update', "orders_id='".$xml->order_id."'");

  $sql_data_arrax = array('orders_id' => $xml->order_id,
                          'orders_status_id' => MODULE_PAYMENT_LIQPAY_ORDER_STATUS_ID,
                          'date_added' => 'now()',
                          'customer_notified' => '0',
                          'comments' => 'LiqPAY accepted this order payment');
  os_db_perform('orders_status_history', $sql_data_arrax);

  echo 'OK'.$xml->order_id;
}
}

?>