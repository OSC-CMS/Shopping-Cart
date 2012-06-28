<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

include ('includes/top.php');
//$osTemplate = new osTemplate;

$breadcrumb->add(NAVBAR_TITLE_SPECIALS, os_href_link(FILENAME_SPECIALS));

require (_INCLUDES.'header.php');

$fsk_lock = '';
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
	$fsk_lock = ' and p.products_fsk18!=1';
}
if (GROUP_CHECK == 'true') {
	$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
}
$specials_query_raw = "select p.products_id,
                                pd.products_name,
                                pd.products_short_description,
                                pd.products_description,
                                p.products_price,
                                p.products_tax_class_id,p.products_shippingtime,
                                p.products_image,p.products_vpe_status,p.products_vpe_value,p.products_vpe,p.products_fsk18,
                                s.specials_new_products_price from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_SPECIALS." s
                                where p.products_status = '1'
                                and s.products_id = p.products_id
                                and p.products_id = pd.products_id
                                ".$group_check."
                                ".$fsk_lock."
                                and pd.language_id = '".(int) $_SESSION['languages_id']."'
                                and s.status = '1' order by s.specials_date_added DESC";
$specials_split = new splitPageResults($specials_query_raw, $_GET['page'], MAX_DISPLAY_SPECIAL_PRODUCTS);

$module_content = '';
$row = 0;
$specials_query = os_db_query($specials_split->sql_query);
while ($specials = os_db_fetch_array($specials_query)) {
	$module_content[] = $product->buildDataArray($specials);
}

if (($specials_split->number_of_rows > 0)) {
	$osTemplate->assign('NAVBAR', TEXT_RESULT_PAGE.' '.$specials_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y'))));
	$osTemplate->assign('NAVBAR_PAGES', $specials_split->display_count(TEXT_DISPLAY_NUMBER_OF_SPECIALS));

}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('module_content', $module_content);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/specials.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_SPECIALS.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_SPECIALS.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>