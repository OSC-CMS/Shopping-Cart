<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

include ('includes/top.php');
//$osTemplate = new osTemplate;


if (!isset ($_SESSION['customer_id']))
	os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));

$breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_HISTORY, os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_HISTORY, os_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));

require (dir_path('includes').'header.php');

$module_content = array ();
if (($orders_total = os_count_customer_orders()) > 0) {
	$history_query_raw = "select o.orders_id, o.date_purchased, o.delivery_name, o.billing_name, ot.text as order_total, s.orders_status_name from ".TABLE_ORDERS." o, ".TABLE_ORDERS_TOTAL." ot, ".TABLE_ORDERS_STATUS." s where o.customers_id = '".(int) $_SESSION['customer_id']."' and o.orders_id = ot.orders_id and ot.class = 'ot_total' and o.orders_status = s.orders_status_id and s.language_id = '".(int) $_SESSION['languages_id']."' order by orders_id DESC";
	$history_split = new splitPageResults($history_query_raw, $_GET['page'], MAX_DISPLAY_ORDER_HISTORY);
	$history_query = os_db_query($history_split->sql_query);

	while ($history = os_db_fetch_array($history_query)) {
		$products_query = os_db_query("select count(*) as count from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".$history['orders_id']."'");
		$products = os_db_fetch_array($products_query);

		if (os_not_null($history['delivery_name'])) {
			$order_type = TEXT_ORDER_SHIPPED_TO;
			$order_name = $history['delivery_name'];
		} else {
			$order_type = TEXT_ORDER_BILLED_TO;
			$order_name = $history['billing_name'];
		}
		
		$_array = array('img' => 'small_view.gif', 'href' => os_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'page='.(empty($_GET['page']) ? "1" : (int)$_GET['page']) .'&order_id='.$history['orders_id'], 'SSL'), 'alt' => SMALL_IMAGE_BUTTON_VIEW, 'code' => '');
	
	   $_array = apply_filter('button_small_view', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	   }
		
		$module_content[] = array ('ORDER_ID' => $history['orders_id'], 
		'ORDER_STATUS' => $history['orders_status_name'], 
		'ORDER_DATE' => os_date_long($history['date_purchased']), 'ORDER_PRODUCTS' => $products['count'], 
		'ORDER_TOTAL' => strip_tags($history['order_total']), 
		'ORDER_BUTTON' => $_array['code']);

	}
}

if ($orders_total > 0) {
	$osTemplate->assign('PAGINATION', $history_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y'))));
}
$osTemplate->assign('order_content', $module_content);
$osTemplate->assign('language', $_SESSION['language']);

  	$_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link(FILENAME_ACCOUNT, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	}
	
$osTemplate->assign('BUTTON_BACK', $_array['code']);

$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/account_history.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ACCOUNT_HISTORY.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ACCOUNT_HISTORY.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>