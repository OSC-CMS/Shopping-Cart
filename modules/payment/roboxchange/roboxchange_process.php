<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function get_var($name, $default = 'none') {
  return (isset($_GET[$name])) ? $_GET[$name] : ((isset($_POST[$name])) ? $_POST[$name] : $default);
}

require('includes/top.php');

// logging
/*
$fp = fopen(DIR_WS_IMAGES.'ht-robox.log', 'a+');
$str=date('Y-m-d H:i:s').' - ';
foreach ($_REQUEST as $vn=>$vv) {
  $str.=$vn.'='.$vv.';';
}
fwrite($fp, $str."\n");
fclose($fp);
*/

// variables prepearing
$inv_id = get_var('InvId');
$out_summ = get_var('OutSum');
$crc = get_var('SignatureValue');

// checking and handling
if (strtoupper(md5("$out_summ:$inv_id:".MODULE_PAYMENT_ROBOXCHANGE_PASSWORD2)) == strtoupper($crc)) {
  $sql_data_array = array('orders_status' => MODULE_PAYMENT_ROBOXCHANGE_ORDER_STATUS_ID);
  os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".$inv_id."'");

  $sql_data_arrax = array('orders_id' => $inv_id,
                          'orders_status_id' => MODULE_PAYMENT_ROBOXCHANGE_ORDER_STATUS_ID,
                          'date_added' => 'now()',
                          'customer_notified' => '0',
                          'comments' => 'Roboxchange accepted this order payment');
  os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

  echo 'OK'.$inv_id;
}
?>