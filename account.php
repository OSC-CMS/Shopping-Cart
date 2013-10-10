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

$breadcrumb->add(NAVBAR_TITLE_ACCOUNT, os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
require (dir_path('includes').'header.php');

//if ($messageStack->size('account') > 0)
//	$osTemplate->assign('error_message', $messageStack->output('account'));

$i = 0;
$max = count($_SESSION['tracking']['products_history']);

while ($i < $max) 
{

	$product_history_query = osDBquery("select * from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id=pd.products_id and pd.language_id='".(int)$_SESSION['languages_id']."' and p.products_status = '1' and p.products_id = '".$_SESSION['tracking']['products_history'][$i]."'");
	$history_product = os_db_fetch_array($product_history_query, true);
$cpath = os_get_product_path($_SESSION['tracking']['products_history'][$i]);
	if ($history_product['products_status'] != 0) {

		$history_product = array_merge($history_product,array('cat_url' => os_href_link(FILENAME_DEFAULT, 'cat='.$cpath)));
		$products_history[] = $product->buildDataArray($history_product);
	}
	$i ++;
}

$order_content = '';
if (os_count_customer_orders() > 0) {

	$orders_query = os_db_query("select
	                                  o.orders_id,
	                                  o.date_purchased,
	                                  o.delivery_name,
	                                  o.delivery_country,
	                                  o.billing_name,
	                                  o.billing_country,
	                                  ot.text as order_total,
	                                  s.orders_status_name
	                                  from ".TABLE_ORDERS." o, ".TABLE_ORDERS_TOTAL."
	                                  ot, ".TABLE_ORDERS_STATUS." s
	                                  where o.customers_id = '".(int) $_SESSION['customer_id']."'
	                                  and o.orders_id = ot.orders_id
	                                  and ot.class = 'ot_total'
	                                  and o.orders_status = s.orders_status_id
	                                  and s.language_id = '".(int) $_SESSION['languages_id']."'
	                                  order by orders_id desc limit 3");

	while ($orders = os_db_fetch_array($orders_query)) {
		if (os_not_null($orders['delivery_name'])) {
			$order_name = $orders['delivery_name'];
			$order_country = $orders['delivery_country'];
		} else {
			$order_name = $orders['billing_name'];
			$order_country = $orders['billing_country'];
		}
		
		$_array = array('img' => 'small_view.gif', 'href' => os_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id='.$orders['orders_id'], 'SSL'), 'alt' => SMALL_IMAGE_BUTTON_VIEW, 'code' => '');
	
	   $_array = apply_filter('button_small_view', $_array);	
	
	   if (empty($_array['code']))
 	   {
		   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	   }
	   
			$order_content[] = array ('ORDER_ID' => $orders['orders_id'], 
			'ORDER_DATE' => os_date_short($orders['date_purchased']), 
			'ORDER_STATUS' => $orders['orders_status_name'], 
			'ORDER_TOTAL' => $orders['order_total'], 
			'ORDER_LINK' => os_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id='.$orders['orders_id'], 'SSL'), 
			'ORDER_BUTTON' => $_array['code']);
	}

}
$osTemplate->assign('LINK_EDIT', os_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
$osTemplate->assign('LINK_ADDRESS', os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
$osTemplate->assign('LINK_PASSWORD', os_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL'));
if (!isset ($_SESSION['customer_id']))
	$osTemplate->assign('LINK_LOGIN', os_href_link(FILENAME_LOGIN, '', 'SSL'));
$osTemplate->assign('LINK_ORDERS', os_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
$osTemplate->assign('LINK_NEWSLETTER', os_href_link(FILENAME_NEWSLETTER, '', 'SSL'));
$osTemplate->assign('LINK_ALL', os_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
$osTemplate->assign('order_content', $order_content);
$osTemplate->assign('products_history', $products_history);
$osTemplate->assign('also_purchased_history', $also_purchased_history);
$osTemplate->assign('profileLink', customerProfileLink($_SESSION['customers_username'], $_SESSION['customer_id']));
$osTemplate->assign('language', $_SESSION['language']);

$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/account.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
$osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ACCOUNT.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ACCOUNT.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>