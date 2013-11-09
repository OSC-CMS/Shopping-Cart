<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$box = new osTemplate;

if ($_SESSION['cart']->count_contents() > 0)
{
	$getCartInfo = $_SESSION['cart']->getCartInfo();

	$products = $_SESSION['cart']->get_products();
	$products_in_cart = array ();
	$qty = 0;
	for ($i = 0, $n = sizeof($products); $i < $n; $i ++)
	{
		$qty += $products[$i]['quantity'];
		$products_in_cart[] = array (
			'QTY' => $products[$i]['quantity'], 
			'LINK' => os_href_link(FILENAME_PRODUCT_INFO, os_product_link($products[$i]['id'],$products[$i]['name'])), 
			'NAME' => $products[$i]['name']
		);
	}

	$total = $getCartInfo['show_total'];
	if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1' && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00')
	{
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
			$price = $total-$_SESSION['cart']->show_tax(false);
		else
			$price = $total;

		$discount = $osPrice->GetDC($price, $_SESSION['customers_status']['customers_status_ot_discount']);
		$box->assign('DISCOUNT', $osPrice->Format(($discount * (-1)), $price_special = 1, $calculate_currencies = false));
	}

	if ($_SESSION['customers_status']['customers_status_show_price'] == '1')
	{
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0)
			$total-=$discount;

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
			$total-=$discount;

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1)
			@$total-=$discount;

		$box->assign('TOTAL', $osPrice->Format($total, true));
	} 

	$box->assign('UST', $_SESSION['cart']->show_tax());

	if (SHOW_SHIPPING=='true')
	{ 
		$box->assign('SHIPPING_INFO',' '.SHIPPING_EXCL.'<a class="shippingInfo" href="javascript:newWin=void(window.open(\''.os_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS).'\', \'popup\', \'toolbar=0, width=640, height=600\'))"> '.SHIPPING_COSTS.'</a>');	
	}

	$box->assign('PRODUCTS', $qty);
	$box->assign('empty', 'false');
}
else
	$box->assign('empty', 'true');

if (ACTIVATE_GIFT_SYSTEM == 'true')
{
	$box->assign('ACTIVATE_GIFT', 'true');
}

// GV Code Start
if (isset($_SESSION['customer_id']))
{
	$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$_SESSION['customer_id']."'");
	$gv_result = os_db_fetch_array($gv_query);
	if ($gv_result['amount'] > 0)
	{
		$box->assign('GV_AMOUNT', $osPrice->Format($gv_result['amount'], true, 0, true));
		$box->assign('GV_SEND_TO_FRIEND_LINK', '<a href="'.os_href_link(FILENAME_GV_SEND).'">');
	}
}

if (isset ($_SESSION['gv_id']))
{
	$gv_query = os_db_query("select coupon_amount from ".TABLE_COUPONS." where coupon_id = '".$_SESSION['gv_id']."'");
	$coupon = os_db_fetch_array($gv_query);
	$box->assign('COUPON_AMOUNT2', $osPrice->Format($coupon['coupon_amount'], true, 0, true));
}

if (isset ($_SESSION['cc_id']))
{
	$box->assign('COUPON_HELP_LINK', '<a href="javascript:popupWindow(\''.os_href_link(FILENAME_POPUP_COUPON_HELP, 'cID='.$_SESSION['cc_id']).'\')">');
}

// GV Code End
$box->assign('LINK_CART', os_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
$box->assign('LINK_CHECKOUT', os_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$box->assign('products', isset($products_in_cart) ? $products_in_cart : '');

$box->caching = 0;
$box->assign('language', $_SESSION['language']);
$box_shopping_cart = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_cart.html');
$osTemplate->assign('box_CART', $box_shopping_cart);
?>