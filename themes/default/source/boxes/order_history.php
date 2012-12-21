<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

$box = new osTemplate;

if (isset($_SESSION['customer_id']))
{
	$orders_query = os_db_query("select distinct op.products_id from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_PRODUCTS . " p where o.customers_id = '" . (int)$_SESSION['customer_id'] . "' and o.orders_id = op.orders_id and op.products_id = p.products_id and p.products_status = '1' group by products_id order by o.date_purchased desc limit " . MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX);
	if (os_db_num_rows($orders_query))
	{
		$product_ids = '';
		while ($orders = os_db_fetch_array($orders_query))
		{
			$product_ids .= $orders['products_id'] . ',';
		}
		$product_ids = substr($product_ids, 0, -1);

		$products_query = os_db_query("select products_id, products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id in (".$product_ids.") and language_id = '".(int)$_SESSION['languages_id']."' order by products_name");

		$oHistory = array();
		while ($products = os_db_fetch_array($products_query))
		{
			$oHistory[] = array
			(
				'p_name'	=> $products['products_name'],
				'p_link'	=> os_href_link(FILENAME_PRODUCT_INFO, os_product_link($products['products_id'],$products['products_name'])),
				'p_buy'		=> buttonSubmit('oh_cart.gif', os_href_link(basename($PHP_SELF), 'action=cust_order&pid=' . $products['products_id']), ICON_CART),
			);
		}
	}
}

$box->assign('oHistory', $oHistory);

$box->caching = 0;
$box->assign('language', $_SESSION['language']);
$box_order_history= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_order_history.html');
$osTemplate->assign('box_HISTORY',$box_order_history);
?>