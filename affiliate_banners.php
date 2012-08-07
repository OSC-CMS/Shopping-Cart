<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require('includes/top.php');

//$osTemplate = new osTemplate;


if (!isset($_SESSION['affiliate_id'])) {
    os_redirect(os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
}

$breadcrumb->add(NAVBAR_TITLE, os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_BANNERS, os_href_link(FILENAME_AFFILIATE_BANNERS));

$affiliate_banners_values = os_db_query("select * from " . TABLE_AFFILIATE_BANNERS . " order by affiliate_banners_title");

require(dir_path('includes') . 'header.php');

$osTemplate->assign('FORM_ACTION', os_draw_form('individual_banner', os_href_link(FILENAME_AFFILIATE_BANNERS)));
$osTemplate->assign('INPUT_BANNER_ID', os_draw_input_field('individual_banner_id', '', 'size="5"'));
$osTemplate->assign('BUTTON_SUBMIT', button_continue_submit());

if (os_not_null($_POST['individual_banner_id']) || os_not_null($_GET['individual_banner_id'])) {
    if (os_not_null($_POST['individual_banner_id'])) $individual_banner_id = $_POST['individual_banner_id'];
    if ($_GET['individual_banner_id']) $individual_banner_id = $_GET['individual_banner_id'];
    $affiliate_pbanners_values = os_db_query("select p.products_image, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . $individual_banner_id . "' and pd.products_id = '" . $individual_banner_id . "' and p.products_status = '1' and pd.language_id = '" . $_SESSION['languages_id'] . "'");
    if ($affiliate_pbanners = os_db_fetch_array($affiliate_pbanners_values)) {
        switch (AFFILIATE_KIND_OF_BANNERS) {
            case 1:
                $link = '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_PRODUCT_INFO . '?ref=' . $_SESSION['affiliate_id'] . '&products_id=' . $individual_banner_id . '&affiliate_banner_id=1" target="_blank"><img src="' . http_path('images') . $affiliate_pbanners['affiliate_banners_image'] . '" border="0" alt="' . $affiliate_pbanners['products_name'] . '"></a>';
                break;
            case 2: 
                $link = '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_PRODUCT_INFO . '?ref=' . $_SESSION['affiliate_id'] . '&products_id=' . $individual_banner_id . '&affiliate_banner_id=1" target="_blank"><img src="' . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_SHOW_BANNER . '?ref=' . $_SESSION['affiliate_id'] . '&affiliate_pbanner_id=' . $individual_banner_id . '" border="0" alt="' . $affiliate_pbanners['products_name'] . '"></a>';
                break;
        }
    }
    $osTemplate->assign('link', $link);
    $osTemplate->assign('TEXTAREA_AFFILIATE_BANNER1', os_draw_textarea_field('affiliate_banner', 'soft', '60', '6', $link));
}

if (os_db_num_rows($affiliate_banners_values)) {
	$aBanners = array();
    while ($affiliate_banners = os_db_fetch_array($affiliate_banners_values)) {
        $affiliate_products_query = os_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $affiliate_banners['affiliate_products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
        $affiliate_products = os_db_fetch_array($affiliate_products_query);
        $prod_id = $affiliate_banners['affiliate_products_id'];
        $ban_id = $affiliate_banners['affiliate_banners_id'];
        switch (AFFILIATE_KIND_OF_BANNERS) {
            case 1: 
                if ($prod_id > 0) {
                    $link = '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_PRODUCT_INFO . '?ref=' . $_SESSION['affiliate_id'] . '&products_id=' . $prod_id . '&affiliate_banner_id=' . $ban_id . '" target="_blank"><img src="' . HTTP_SERVER . DIR_WS_CATALOG . http_path('images') . $affiliate_banners['affiliate_banners_image'] . '" border="0" alt="' . $affiliate_products['products_name'] . '"></a>';
                }
                else {
                    $link = '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_DEFAULT . '?ref=' . $_SESSION['affiliate_id'] . '&affiliate_banner_id=' . $ban_id . '" target="_blank"><img src="' . HTTP_SERVER . DIR_WS_CATALOG . http_path('images') . $affiliate_banners['affiliate_banners_image'] . '" border="0" alt="' . $affiliate_banners['affiliate_banners_title'] . '"></a>';
                }
                break;
            case 2:
                if ($prod_id > 0) {
                    $link = '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_PRODUCT_INFO . '?ref=' . $_SESSION['affiliate_id'] . '&products_id=' . $prod_id . '&affiliate_banner_id=' . $ban_id . '" target="_blank"><img src="' . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_SHOW_BANNER . '?ref=' . $_SESSION['affiliate_id'] . '&affiliate_banner_id=' . $ban_id . '" border="0" alt="' . $affiliate_products['products_name'] . '"></a>';
                }
                else { 
                    $link = '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_DEFAULT . '?ref=' . $_SESSION['affiliate_id'] . '&affiliate_banner_id=' . $ban_id . '" target="_blank"><img src="' . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_AFFILIATE_SHOW_BANNER . '?ref=' . $_SESSION['affiliate_id'] . '&affiliate_banner_id=' . $ban_id . '" border="0" alt="' . $affiliate_banners['affiliate_banners_title'] . '"></a>';
                }
                break;
        }
		$aBanners[] = array(
			'textName' => TEXT_AFFILIATE_NAME,
			'textBannerName' => $affiliate_banners['affiliate_banners_title'],
			'link' => $link,
			'textInfo' => TEXT_AFFILIATE_INFO,
			'textarea' => os_draw_textarea_field('affiliate_banner', 'soft', '', '', $link),
		);
    }
    $osTemplate->assign('aBanners', $aBanners);
}
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_banners.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');?>
