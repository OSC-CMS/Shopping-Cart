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
	// Баланс сертификата
	if (ACTIVATE_GIFT_SYSTEM == 'true')
	{
		$box->assign('ACTIVATE_GIFT', true);

		if (isset($_SESSION['customer_id']))
		{
			$gv_query = os_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$_SESSION['customer_id']."'");
			$gv_result = os_db_fetch_array($gv_query);
			if ($gv_result['amount'] > 0)
				$box->assign('GV_AMOUNT', $osPrice->Format($gv_result['amount'], true, 0, true));
			else
				$box->assign('GV_AMOUNT', '0');
		}
	}

	// Информация о группе
	$box->assign('groupText', BOX_LOGINBOX_STATUS);
	$box->assign('groupName', $_SESSION['customers_status']['customers_status_name']);

	// Скидки
	if ($_SESSION['customers_status']['customers_status_discount'] != '0.00')
	{
		$box->assign('discountGroupText', BOX_LOGINBOX_DISCOUNT);
		$box->assign('discountGroup', $_SESSION['customers_status']['customers_status_discount']);
	}

	if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1  && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00')
	{
		$box->assign('discountOrderText', BOX_LOGINBOX_DISCOUNT_TEXT);
		$box->assign('discountOrder', $_SESSION['customers_status']['customers_status_ot_discount']);
	}

	$box->assign('profileLink', customerProfileLink($_SESSION['customers_username'], $_SESSION['customer_id']));

	$box->caching = 0;
	$box->assign('language', $_SESSION['language']);
	$box_accountbox = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_account.html');
	$osTemplate->assign('box_ACCOUNT', $box_accountbox);
}
?>