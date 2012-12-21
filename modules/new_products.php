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

$fsk_lock = '';
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
	$fsk_lock = ' and p.products_fsk18!=1';

if ((!isset ($new_products_category_id)) || ($new_products_category_id == '0')) {
	if (GROUP_CHECK == 'true')
		$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";

	$new_products_query = "SELECT * FROM
	                                         ".TABLE_PRODUCTS." p,
	                                         ".TABLE_PRODUCTS_DESCRIPTION." pd WHERE
	                                         p.products_id=pd.products_id and
	                                         p.products_startpage = '1'
	                                         ".$group_check."
	                                         ".$fsk_lock."
	                                         and p.products_status = '1' and pd.language_id = '".(int) $_SESSION['languages_id']."'
	                                         order by p.products_startpage_sort ASC limit ".MAX_DISPLAY_NEW_PRODUCTS;
} 
else 
{

	if (GROUP_CHECK == 'true')
		$group_check = "and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";

	if (MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0') {
		$date_new_products = date("Y.m.d", mktime(1, 1, 1, date(m), date(d) - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date(Y)));
		$days = " and p.products_date_added > '".$date_new_products."' ";
	}
	
	$new_products_query = "SELECT * FROM
	                                        ".TABLE_PRODUCTS." p
	                                        left join ".TABLE_PRODUCTS_DESCRIPTION." pd on (p.products_id=pd.products_id and pd.language_id = '".(int) $_SESSION['languages_id']."')
	                                        left join ".TABLE_PRODUCTS_TO_CATEGORIES." p2c on (p.products_id = p2c.products_id)
	                                        left join ".TABLE_CATEGORIES." c on (p2c.categories_id = c.categories_id and c.categories_status='1' and c.parent_id = '".$new_products_category_id."')
	                                        where           
	                                        p.products_status = '1'
											".$group_check."
	                                        ".$fsk_lock."
	                                        order by p.products_date_added DESC limit ".MAX_DISPLAY_NEW_PRODUCTS;
											
}
$row = 0;
$module_content = array ();
$new_products_query = osDBquery($new_products_query);
while ($new_products = os_db_fetch_array($new_products_query, true)) 
{
	$module_content[] = $product->buildDataArray($new_products);
}
if (sizeof($module_content) >= 1) {
   $module->assign('NEW_PRODUCTS_LINK', os_href_link(FILENAME_PRODUCTS_NEW));
	$module->assign('language', $_SESSION['language']);
	$module->assign('module_content', $module_content);
	
	 if (!CacheCheck()) {
		$module->caching = 0;
		if ((!isset ($new_products_category_id)) || ($new_products_category_id == '0')) {
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/new_products_default.html');
		} else {
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/new_products_category.html');
		}
	} else {
		$module->caching = 1;
		$module->cache_lifetime = CACHE_LIFETIME;
		$module->cache_modified_check = CACHE_CHECK;
		$cache_id = $new_products_category_id.$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
		if ((!isset ($new_products_category_id)) || ($new_products_category_id == '0')) {
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/new_products_default.html', $cache_id);
		} else {
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/new_products_category.html', $cache_id);
		}
	}
	$default->assign('MODULE_new_products', $module);
}

?>
