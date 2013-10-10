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
$box_content='';

if (isset($_SESSION['affiliate_id'])) 
{
    $box_content .= '<li><a href="' . os_href_link(FILENAME_AFFILIATE_SUMMARY, '', 'SSL') . '">' . BOX_AFFILIATE_SUMMARY . '</a></li>';
    $box_content .= '<li><a href="' . os_href_link(FILENAME_AFFILIATE_ACCOUNT, '', 'SSL'). '">' . BOX_AFFILIATE_ACCOUNT . '</a></li>';
    $box_content .= '<li><a href="' . os_href_link(FILENAME_AFFILIATE_PAYMENT, '', 'SSL'). '">' . BOX_AFFILIATE_PAYMENT . '</a></li>';
    $box_content .= '<li><a href="' . os_href_link(FILENAME_AFFILIATE_CLICKS, '', 'SSL'). '">' . BOX_AFFILIATE_CLICKRATE . '</a></li>';
    $box_content .= '<li><a href="' . os_href_link(FILENAME_AFFILIATE_SALES, '', 'SSL'). '">' . BOX_AFFILIATE_SALES . '</a></li>';
    $box_content .= '<li><a href="' . os_href_link(FILENAME_AFFILIATE_BANNERS). '">' . BOX_AFFILIATE_BANNERS . '</a></li>';
    $box_content .= '<li><a href="' . os_href_link(FILENAME_AFFILIATE_CONTACT). '">' . BOX_AFFILIATE_CONTACT . '</a></li>';
    $box_content .= '<li><a href="' . os_href_link(FILENAME_CONTENT, 'coID=11'). '">' . BOX_AFFILIATE_FAQ . '</a></li>';
    $box_content .= '<li><a href="' . os_href_link(FILENAME_AFFILIATE_LOGOUT). '">' . BOX_AFFILIATE_LOGOUT . '</a></li>';
}
else 
{
	$box_content .= '<li><a href="' . os_href_link(FILENAME_CONTENT,'coID=10'). '">' . BOX_AFFILIATE_INFO . '</a></li>';
	$box_content .= '<li><a href="' . os_href_link(FILENAME_AFFILIATE, '', 'SSL') . '">' . BOX_AFFILIATE_LOGIN . '</a></li>';
}
$box->assign('BOX_CONTENT', $box_content);
$box->assign('language', $_SESSION['language']);

$box->caching = 0;
$box_affiliate = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_affiliate.html');
$osTemplate->assign('box_AFFILIATE',$box_affiliate);
?>