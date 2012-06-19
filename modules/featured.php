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

$module = new osTemplate;
$module->assign('tpl_path', _HTTP_THEMES_C);
//fsk18 lock
$fsk_lock = '';
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
	$fsk_lock = ' and p.products_fsk18!=1';

if ((!isset ($featured_products_category_id)) || ($featured_products_category_id == '0')) {
	if (GROUP_CHECK == 'true')
		$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";

	$featured_products_query = "SELECT distinct * FROM
	                                         ".TABLE_PRODUCTS." p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id = p.products_id,
	                                         ".TABLE_FEATURED." f where
	                                         p.products_id=f.products_id ".$group_check."
	                                         ".$fsk_lock."
	                                         and p.products_status = '1' and f.status = '1' and pd.language_id = '".(int) $_SESSION['languages_id']."'
	                                         order by p.products_date_added DESC limit ".MAX_DISPLAY_FEATURED_PRODUCTS;
} else {

	if (GROUP_CHECK == 'true')
		$group_check = "and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";

	$featured_products_query = "SELECT distinct * FROM
	                                         ".TABLE_PRODUCTS." p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id = p.products_id,
	                                         ".TABLE_FEATURED." f,
	                                        ".TABLE_PRODUCTS_TO_CATEGORIES." p2c,
	                                        ".TABLE_CATEGORIES." c
	                                        where c.categories_status='1'
	                                        and p.products_id = p2c.products_id and p.products_id=f.products_id
	                                        and p2c.categories_id = c.categories_id
	                                        ".$group_check."
	                                        ".$fsk_lock."
	                                        and c.parent_id = '".$featured_products_category_id."'
	                                        and p.products_status = '1' and f.status = '1' and pd.language_id = '".(int) $_SESSION['languages_id']."'
	                                        order by p.products_date_added DESC limit ".MAX_DISPLAY_FEATURED_PRODUCTS;
}
$row = 0;
$module_content = array ();
$featured_products_query = osDBquery($featured_products_query);
while ($featured_products = os_db_fetch_array($featured_products_query, true)) {
	$module_content[] = $product->buildDataArray($featured_products);

}
if (sizeof($module_content) >= 1) {
   $module->assign('FEATURED_LINK', os_href_link(FILENAME_FEATURED));
	$module->assign('language', $_SESSION['language']);
	$module->assign('module_content', $module_content);

	 if (!CacheCheck()) {
		$module->caching = 0;
		if ((!isset ($featured_products_category_id)) || ($featured_products_category_id == '0')) {
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/featured_products_default.html');
		} else {
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/featured_products_category.html');
		}
	} else {
		$module->caching = 1;
		$module->cache_lifetime = CACHE_LIFETIME;
		$module->cache_modified_check = CACHE_CHECK;
		$cache_id = $featured_products_category_id.$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
		if ((!isset ($featured_products_category_id)) || ($featured_products_category_id == '0')) {
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/featured_products_default.html', $cache_id);
		} else {
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/featured_products_category.html', $cache_id);
		}
	}
	$default->assign('MODULE_featured_products', $module);
}
?>
