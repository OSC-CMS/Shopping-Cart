<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include ('includes/top.php');
//$osTemplate = new osTemplate;


//security checks
if (!isset ($_SESSION['customer_id'])) { os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL')); }
if (!isset ($_GET['order_id']) || (isset ($_GET['order_id']) && !is_numeric($_GET['order_id']))) { 
   os_redirect(os_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
}
$customer_info_query = os_db_query("select customers_id from ".TABLE_ORDERS." where orders_id = '".(int) $_GET['order_id']."'");
$customer_info = os_db_fetch_array($customer_info_query);
if ($customer_info['customers_id'] != $_SESSION['customer_id']) { os_redirect(os_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL')); }

$breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_HISTORY_INFO, os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_HISTORY_INFO, os_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
$breadcrumb->add(sprintf(NAVBAR_TITLE_3_ACCOUNT_HISTORY_INFO, (int)$_GET['order_id']), os_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id='.(int)$_GET['order_id'], 'SSL'));

require (_CLASS.'order.php');
$order = new order((int)$_GET['order_id']);
require (_INCLUDES.'header.php');

// Delivery Info
if ($order->delivery != false) {
	$osTemplate->assign('DELIVERY_LABEL', os_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'));
	if ($order->info['shipping_method']) { $osTemplate->assign('SHIPPING_METHOD', $order->info['shipping_method']); }
}

$order_total = $order->getTotalData((int)$_GET['order_id']); 

$osTemplate->assign('order_data', $order->getOrderData((int)$_GET['order_id']));
$osTemplate->assign('order_total', $order_total['data']);

// Payment Method
if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') 
{
	include (_MODULES.'payment/'.$order->info['payment_method'].'/'.$_SESSION['language'].'.php');
	$osTemplate->assign('PAYMENT_METHOD', constant(MODULE_PAYMENT_.strtoupper($order->info['payment_method'])._TEXT_TITLE));
}



// Order History
$statuses_query = os_db_query("select os.orders_status_name, osh.date_added, osh.comments from ".TABLE_ORDERS_STATUS." os, ".TABLE_ORDERS_STATUS_HISTORY." osh where osh.orders_id = '".(int) $_GET['order_id']."' and osh.orders_status_id = os.orders_status_id and os.language_id = '".(int) $_SESSION['languages_id']."' order by osh.date_added");
while ($statuses = os_db_fetch_array($statuses_query)) {
	$history_block .= os_date_short($statuses['date_added'])."\n".$statuses['orders_status_name']."\n". (empty ($statuses['comments']) ? '&nbsp;' : nl2br(htmlspecialchars($statuses['comments'])))."\n";
}
$osTemplate->assign('HISTORY_BLOCK', $history_block);

// Download-Products
if (DOWNLOAD_ENABLED == 'true') include (_MODULES.'downloads.php');

// Stuff

/*if ($order->info['payment_method'] == 'schet') 
{

   $_array = array('img' => 'button_print_schet.gif', 'href' => os_href_link(FILENAME_PRINT_SCHET, 'oID='.(int)$_GET['order_id']), 'alt' => MODULE_PAYMENT_SCHET_PRINT, 'code' => '');
	
	$_array = apply_filter('button_print_schet', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<img alt="' . MODULE_PAYMENT_SCHET_PRINT . '" src="'.'themes/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/'. $_array['img'].'" style="cursor:pointer" onclick="window.open(\''.$_array['href'].'\', \'popup\', \'toolbar=0, scrollbars=yes, width=800, height=650\')" />';
	}
	
$osTemplate->assign('BUTTON_SCHET_PRINT', $_array['code']);
}*/

/*
if ($order->info['payment_method'] == 'schet') {
$osTemplate->assign('BUTTON_PACKINGSLIP_PRINT', '<img alt="' . MODULE_PAYMENT_PACKINGSLIP_PRINT . '" src="'.'themes/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/button_print_packingslip.gif" style="cursor:pointer" onclick="window.open(\''.os_href_link(FILENAME_PRINT_PACKINGSLIP, 'oID='.(int)$_GET['order_id']).'\', \'popup\', \'toolbar=0, scrollbars=yes, width=800, height=650\')" />');
}
*/

// фильтр кнопок печати
$array = array();
$array['params'] = array('order_id' => $_GET['order_id'], 'payment_method' => $order->info['payment_method']);
$array = apply_filter('print_menu', $array);
if (is_array($array['link']) && !empty($array['link']))
{
	$osTemplate->assign('filterPrint', $array['link']);
}
// фильтр кнопок печати

if ($order->info['payment_method'] == 'kvitancia') {
 $_array = array('img' => 'button_print_kvitancia.gif', 
 'href' => os_href_link(FILENAME_PRINT_KVITANCIA, 'oID='.(int)$_GET['order_id']), 
 'alt' => MODULE_PAYMENT_KVITANCIA_PRINT, 
 'code' => '');
	
	$_array = apply_filter('button_print_kvitancia', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<img alt="' . $_array['alt'] . '" src="'.'themes/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/'.$_array['img'].'" style="cursor:pointer" onclick="window.open(\''.$_array['href'].'\', \'popup\', \'toolbar=0, scrollbars=yes, width=640, height=600\')" />';
	}
	
$osTemplate->assign('BUTTON_KVITANCIA_PRINT', $_array['code']);
}

$osTemplate->assign('ORDER_NUMBER', (int)$_GET['order_id']);
$osTemplate->assign('ORDER_DATE', os_date_long($order->info['date_purchased']));
$osTemplate->assign('ORDER_STATUS', $order->info['orders_status']);
$osTemplate->assign('BILLING_LABEL', os_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'));
$osTemplate->assign('PRODUCTS_EDIT', os_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
$osTemplate->assign('SHIPPING_ADDRESS_EDIT', os_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL'));
$osTemplate->assign('BILLING_ADDRESS_EDIT', os_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));

    $_array = array('img' => 'button_print.gif', 'href' => os_href_link(FILENAME_PRINT_ORDER, 'oID='.(int)$_GET['order_id']), 'alt' => IMAGE_BUTTON_PRINT, 'code' => '');
	
	   $_array = apply_filter('button_print', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  '<a style="cursor:pointer" onclick="javascript:window.open(\''.$_array['href'].'\', \'popup\', \'toolbar=0, scrollbars=yes, width=640, height=600\')"><img src="'.'themes/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/'.$_array['img'].'" alt="' . $_array['alt'] . '" /></a>';
	   }
	   
$osTemplate->assign('BUTTON_PRINT', $_array['code']);

$from_history = preg_match("/page=/i", os_get_all_get_params()); // referer from account_history yes/no
$back_to = $from_history ? FILENAME_ACCOUNT_HISTORY : FILENAME_ACCOUNT; // if from account_history => return to account_history

   	$_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link($back_to,os_get_all_get_params(array ('order_id')), 'SSL'), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="' . $_array['href'] . '">' . os_image_button( $_array['img'], $_array['alt']) . '</a>';
	}
	
$osTemplate->assign('BUTTON_BACK', $_array['code']);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/account_history_info.html');
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ACCOUNT_HISTORY_INFO.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ACCOUNT_HISTORY_INFO.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>