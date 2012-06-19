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

include ('includes/top.php');


//$osTemplate = new osTemplate;


$order_query_check = os_db_query("SELECT
                                        customers_id
                                        FROM ".TABLE_ORDERS."
                                        WHERE orders_id='".(int) $_GET['oID']."'");
$oID = (int) $_GET['oID'];
$order_check = os_db_fetch_array($order_query_check);
if ($_SESSION['customer_id'] == $order_check['customers_id']) {
        include (_CLASS.'order.php');
        $order = new order($oID);
        $osTemplate->assign('address_label_customer', os_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
        $osTemplate->assign('address_label_shipping', os_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
        $osTemplate->assign('address_label_payment', os_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
        $osTemplate->assign('csID', $order->customer['csID']);
        $order_total = $order->getTotalData($oID); 
        $osTemplate->assign('order_data', $order->getOrderData($oID));
        $osTemplate->assign('order_total', $order_total['data']);
        $osTemplate->assign('language', $_SESSION['language']);
        $osTemplate->assign('oID', (int) $_GET['oID']);
        if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') 
        {
                include (DIR_FS_DOCUMENT_ROOT.'/modules/payment/'.$order->info['payment_method'].'/'.$_SESSION['language'].'.php');
                $payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
        }
        $osTemplate->assign('PAYMENT_METHOD', $payment_method);
        if ($order->info['shipping_method'] != '') {
                $shipping_method = $order->info['shipping_method'];
        }
        $osTemplate->assign('SHIPPING_METHOD', $shipping_method);
        $osTemplate->assign('COMMENT', $order->info['comments']);
        $osTemplate->assign('DATE', os_date_long($order->info['date_purchased']));
        $path = _THEMES_C;
        $osTemplate->assign('tpl_path', $path);
        $osTemplate->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'images/');
        $osTemplate->assign('charset', $_SESSION['language_charset']);
        $osTemplate->caching = false;


        $osTemplate->display(CURRENT_TEMPLATE.'/module/print_order.html');
} else {


        $osTemplate->assign('ERROR', 'You are not allowed to view this order!');
        $osTemplate->display(CURRENT_TEMPLATE.'/module/error_message.html');
}
?>