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

require ('includes/top.php');

if (ACTIVATE_GIFT_SYSTEM != 'true')
	os_redirect(FILENAME_DEFAULT);
	if (!isset ($_SESSION['customer_id'])) os_redirect(FILENAME_SHOPPING_CART);

//$osTemplate = new osTemplate;


require (dir_path('includes').'header.php');

// check for a voucher number in the url
if (isset ($_GET['gv_no'])) {
	
	
	
	$error = true;
	$gv_query = os_db_query("select c.coupon_id, c.coupon_amount from ".TABLE_COUPONS." c, ".TABLE_COUPON_EMAIL_TRACK." et where coupon_code = '".os_db_input($_GET['gv_no'])."' and c.coupon_id = et.coupon_id");
	if (os_db_num_rows($gv_query) > 0) {
		$coupon = os_db_fetch_array($gv_query);
		$redeem_query = os_db_query("select coupon_id from ".TABLE_COUPON_REDEEM_TRACK." where coupon_id = '".$coupon['coupon_id']."'");
		if (os_db_num_rows($redeem_query) == 0) {
			// check for required session variables
			$_SESSION['gv_id'] = $coupon['coupon_id'];
			$error = false;
		} else {
			$error = true;
		}
	}
} else {
	os_redirect(FILENAME_DEFAULT);
}
if ((!$error) && (isset ($_SESSION['customer_id']))) {
	// Update redeem status
	$gv_query = os_db_query("insert into  ".TABLE_COUPON_REDEEM_TRACK." (coupon_id, customer_id, redeem_date, redeem_ip) values ('".$coupon['coupon_id']."', '".$_SESSION['customer_id']."', now(),'".$REMOTE_ADDR."')");
	$gv_update = os_db_query("update ".TABLE_COUPONS." set coupon_active = 'N' where coupon_id = '".$coupon['coupon_id']."'");
	os_gv_account_update($_SESSION['customer_id'], $_SESSION['gv_id']);
	unset ($_SESSION['gv_id']);
}

$breadcrumb->add(NAVBAR_GV_REDEEM);

// if we get here then either the url gv_no was not set or it was invalid
// so output a message.
$osTemplate->assign('coupon_amount', $osPrice->Format($coupon['coupon_amount'], true));
$osTemplate->assign('error', $error);
$osTemplate->assign('LINK_DEFAULT', button_continue());
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/gv_redeem.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_GV_REDEEM.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_GV_REDEEM.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>