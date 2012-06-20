<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require ('includes/top.php');
//$osTemplate = new osTemplate;

include ('includes/header.php');


$coupon_query = os_db_query("select * from ".TABLE_COUPONS." where coupon_id = '".(int)$_GET['cID']."'");
$coupon = os_db_fetch_array($coupon_query);
$coupon_desc_query = os_db_query("select * from ".TABLE_COUPONS_DESCRIPTION." where coupon_id = '".(int)$_GET['cID']."' and language_id = '".$_SESSION['languages_id']."'");
$coupon_desc = os_db_fetch_array($coupon_desc_query);
$text_coupon_help = TEXT_COUPON_HELP_HEADER;
$text_coupon_help .= sprintf(TEXT_COUPON_HELP_NAME, $coupon_desc['coupon_name']);
if (os_not_null($coupon_desc['coupon_description']))
	$text_coupon_help .= sprintf(TEXT_COUPON_HELP_DESC, $coupon_desc['coupon_description']);
$coupon_amount = $coupon['coupon_amount'];
switch ($coupon['coupon_type']) {
	case 'F' :
		$text_coupon_help .= sprintf(TEXT_COUPON_HELP_FIXED, $osPrice->Format($coupon['coupon_amount'], true));
		break;
	case 'P' :
		$text_coupon_help .= sprintf(TEXT_COUPON_HELP_FIXED, number_format($coupon['coupon_amount'], 2).'%');
		break;
	case 'S' :
		$text_coupon_help .= TEXT_COUPON_HELP_FREESHIP;
		break;
	default :
		}

if ($coupon['coupon_minimum_order'] > 0)
	$text_coupon_help .= sprintf(TEXT_COUPON_HELP_MINORDER, $osPrice->Format($coupon['coupon_minimum_order'], true));
$text_coupon_help .= sprintf(TEXT_COUPON_HELP_DATE, os_date_short($coupon['coupon_start_date']), os_date_short($coupon['coupon_expire_date']));
$text_coupon_help .= '<b>'.TEXT_COUPON_HELP_RESTRICT.'</b>';
$text_coupon_help .= '<br /><br />'.TEXT_COUPON_HELP_CATEGORIES;
$coupon_get = os_db_query("select restrict_to_categories from ".TABLE_COUPONS." where coupon_id='".(int)$_GET['cID']."'");
$get_result = os_db_fetch_array($coupon_get);

$cat_ids = preg_split("/[,]/", $get_result['restrict_to_categories']);
for ($i = 0; $i < count($cat_ids); $i ++) {
	$result = os_db_query("SELECT * FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd WHERE c.categories_id = cd.categories_id and cd.language_id = '".$_SESSION['languages_id']."' and c.categories_id='".$cat_ids[$i]."'");
	if ($row = os_db_fetch_array($result)) {
		$cats .= '<br />'.$row["categories_name"];
	}
}
if ($cats == '')
	$cats = '<br />NONE';
$text_coupon_help .= $cats;
$text_coupon_help .= '<br /><br />'.TEXT_COUPON_HELP_PRODUCTS;
$coupon_get = os_db_query("select restrict_to_products from ".TABLE_COUPONS."  where coupon_id='".(int)$_GET['cID']."'");
$get_result = os_db_fetch_array($coupon_get);

$pr_ids = preg_split("/[,]/", $get_result['restrict_to_products']);
for ($i = 0; $i < count($pr_ids); $i ++) {
	$result = os_db_query("SELECT * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd WHERE p.products_id = pd.products_id and pd.language_id = '".$_SESSION['languages_id']."'and p.products_id = '".$pr_ids[$i]."'");
	if ($row = os_db_fetch_array($result)) {
		$prods .= '<br />'.$row["products_name"];
	}
}
if ($prods == '')
	$prods = '<br />NONE';
$text_coupon_help .= $prods;

$osTemplate->assign('TEXT_HELP', $text_coupon_help);
$osTemplate->assign('link_close', 'javascript:window.close()');
$osTemplate->assign('language', $_SESSION['language']);

$osTemplate->caching = 0;
$osTemplate->display(CURRENT_TEMPLATE.'/module/popup_coupon_help.html');
include ('includes/bottom.php');
?>