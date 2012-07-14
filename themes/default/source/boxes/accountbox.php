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
$box->assign('tpl_path', _HTTP_THEMES_C);

if (os_session_is_registered('customer_id'))
{
	$box->caching = 0;
	$box->assign('language', $_SESSION['language']);
	$box_accountbox = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_account.html');
	$osTemplate->assign('box_ACCOUNT', $box_accountbox);
}
?>