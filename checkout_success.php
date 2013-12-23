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


if (!isset ($_SESSION['customer_id'])) {
	os_redirect(os_href_link(FILENAME_SHOPPING_CART));
}

if (isset ($_GET['action']) && ($_GET['action'] == 'update')) {

	if ($_SESSION['account_type'] != 1) {
		os_redirect(os_href_link(FILENAME_DEFAULT));
	} else {
		os_redirect(os_href_link(FILENAME_LOGOFF));
	}
}
$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_SUCCESS);
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_SUCCESS);

require (dir_path('includes').'header.php');

$orders_query = os_db_query("select orders_id, orders_status from ".TABLE_ORDERS." where customers_id = '".$_SESSION['customer_id']."' order by orders_id desc limit 1");
$orders = os_db_fetch_array($orders_query);
$last_order = $orders['orders_id'];
$order_status = $orders['orders_status'];

$osTemplate->assign('FORM_ACTION', os_draw_form('order', os_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update', 'SSL')));
$osTemplate->assign('BUTTON_CONTINUE', button_continue_submit());

       $_array = array('img' => 'button_print.gif', 'href' => os_href_link(FILENAME_PRINT_ORDER, 'oID='.$orders['orders_id']), 'alt' => IMAGE_BUTTON_PRINT, 'code' => '');
	
	   $_array = apply_filter('button_print', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  '<img src="'.'themes/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/'.$_array['img'].'" style="cursor:pointer" onclick="window.open(\''.$_array['href'].'\', \'popup\', \'toolbar=0, scrollbars=yes, width=640, height=600\')" />';
	   }
	     
$osTemplate->assign('BUTTON_PRINT', $_array['code']);


$osTemplate->assign('FORM_END', '</form>');
$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id='".$_SESSION['customer_id']."'");
if ($gv_result = os_db_fetch_array($gv_query)) {
	if ($gv_result['amount'] > 0) {
		$osTemplate->assign('GV_SEND_LINK', os_href_link(FILENAME_GV_SEND));
	}
}

	include (dir_path('class').'order.php');
	$order = new order($orders['orders_id']);

// фильтр кнопок печати
$array = array();
$array['params'] = array('order_id' => $orders['orders_id'], 'payment_method' => $order->info['payment_method']);
$array = apply_filter('print_menu', $array);

if (is_array($array['link']) && !empty($array['link']))
{
	$osTemplate->assign('filterPrint', $array['link']);
}
// фильтр кнопок печати

if ($order->info['payment_method'] == 'kvitancia') 
{

 $_array = array('img' => 'button_print_kvitancia.gif', 
 'href' => os_href_link(FILENAME_PRINT_KVITANCIA, 'oID='.$orders['orders_id']), 
 'alt' => MODULE_PAYMENT_KVITANCIA_PRINT, 
 'code' => '');
	
	$_array = apply_filter('button_print_kvitancia', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<img alt="' . $_array['alt'] . '" src="'.'themes/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/'.$_array['img'].'" style="cursor:pointer" onclick="window.open(\''.$_array['href'].'\', \'popup\', \'toolbar=0, scrollbars=yes, width=640, height=600\')" />';
	}
	
$osTemplate->assign('BUTTON_KVITANCIA_PRINT', $_array['code']);
}

do_action('checkout_success');

if (DOWNLOAD_ENABLED == 'true') include (DIR_WS_MODULES.'downloads.php');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('PAYMENT_BLOCK', $payment_block);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/checkout_success.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_CHECKOUT_SUCCESS.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_CHECKOUT_SUCCESS.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>