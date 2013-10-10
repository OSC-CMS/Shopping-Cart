<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$module = new osTemplate;
$module_content = array ();
$fsk_lock = '';
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
	$fsk_lock = ' and p.products_fsk18!=1';

if (GROUP_CHECK == 'true')
	$group_check = "and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";

$expected_query = osDBquery("select p.products_id,
                                  pd.products_name,
                                  products_date_available as date_expected from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
                                  where to_days(products_date_available) >= to_days(now())
                                  and p.products_id = pd.products_id
                                  ".$group_check."
                                  ".$fsk_lock."
                                  and pd.language_id = '".(int) $_SESSION['languages_id']."'
                                  order by ".EXPECTED_PRODUCTS_FIELD." ".EXPECTED_PRODUCTS_SORT."
                                  limit ".MAX_DISPLAY_UPCOMING_PRODUCTS);
if (os_db_num_rows($expected_query,true) > 0) {

	$row = 0;
	while ($expected = os_db_fetch_array($expected_query,true)) {
		$row ++;
		$module_content[] = array ('PRODUCTS_LINK' => os_href_link(FILENAME_PRODUCT_INFO, os_product_link($expected['products_id'], $expected['products_name'])), 'PRODUCTS_NAME' => $expected['products_name'], 'PRODUCTS_DATE' => os_date_short($expected['date_expected']));

	}

	$module->assign('language', $_SESSION['language']);
	$module->assign('module_content', $module_content);
	$module->caching = 0;
	$module = $module->fetch(CURRENT_TEMPLATE.'/module/upcoming_products.html');

	$default->assign('MODULE_upcoming_products', $module);
}
?>