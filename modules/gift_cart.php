<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$gift = new osTemplate;

if (ACTIVATE_GIFT_SYSTEM == 'true') {
	$gift->assign('ACTIVATE_GIFT', 'true');
}

if (isset ($_SESSION['customer_id'])) {
	$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$_SESSION['customer_id']."'");
	$gv_result = os_db_fetch_array($gv_query);
	if ($gv_result['amount'] > 0) {
		$gift->assign('GV_AMOUNT', $osPrice->Format($gv_result['amount'], true, 0, true));
		$gift->assign('GV_SEND_TO_FRIEND_LINK', os_href_link(FILENAME_GV_SEND));
	} else {
		$gift->assign('GV_AMOUNT', 0);
	}
}

if (isset ($_SESSION['gv_id'])) {
	$gv_query = os_db_query("select coupon_amount from ".TABLE_COUPONS." where coupon_id = '".$_SESSION['gv_id']."'");
	$coupon = os_db_fetch_array($gv_query);
	$gift->assign('COUPON_AMOUNT2', $osPrice->Format($coupon['coupon_amount'], true, 0, true));
}
if (isset ($_SESSION['cc_id'])) {
	$gift->assign('COUPON_HELP_LINK', '<a style="cursor:pointer" onclick="javascript:window.open(\''.os_href_link(FILENAME_POPUP_COUPON_HELP, 'cID='.$_SESSION['cc_id']).'\', \'popup\', \'toolbar=0,scrollbars=yes, width=350, height=350\')">');

}
if (isset ($_SESSION['customer_id'])) {
	$gift->assign('C_FLAG', 'true');
}
$gift->assign('LINK_ACCOUNT', os_href_link(FILENAME_CREATE_ACCOUNT));
$gift->assign('FORM_ACTION', os_draw_form('gift_coupon', os_href_link(FILENAME_SHOPPING_CART, 'action=check_gift', 'NONSSL')));
$gift->assign('INPUT_CODE', os_draw_input_field('gv_redeem_code'));
     //buttons	
	$_array = array('img' => 'button_redeem.gif', 
	                                'href' => '', 
									'alt' => IMAGE_REDEEM_GIFT, 'code' => '');
									
	$_array = apply_filter('button_redeem', $_array);	
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], null, $_array['alt']);
	}
	
$gift->assign('BUTTON_SUBMIT', $_array['code']);


$gift->assign('language', $_SESSION['language']);
$gift->assign('FORM_END', '</form>');
$gift->caching = 0;

$osTemplate->assign('MODULE_gift_cart', $gift->fetch(CURRENT_TEMPLATE.'/module/gift_cart.html'));
?>