<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

$module = new osTemplate;

if ((!isset($new_products_category_id)) || ($new_products_category_id == '0'))
{
	$new_products_query = $cartet->product->getList(array(
		'products_status' => 1,
		'category_status' => 1,
		'group' => 'p.products_id',
		'where' => array('p.products_startpage = 1'),
		'order' => 'p.products_startpage_sort ASC',
		'limit' => MAX_DISPLAY_NEW_PRODUCTS,
	));
} 
else 
{
	if (MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0')
	{
		$date_new_products = date("Y.m.d", mktime(1, 1, 1, date(m), date(d) - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date(Y)));
		$where[] = "p.products_date_added > '".$date_new_products."'";
	}

	$where[] = 'c.parent_id = '.$new_products_category_id;

	$new_products_query = $cartet->product->getList(array(
		'products_status' => 1,
		'category_status' => 1,
		'group' => 'p.products_id',
		'where' => $where,
		'order' => 'p.products_date_added DESC',
		'limit' => MAX_DISPLAY_NEW_PRODUCTS,
	));
}

$module_content = array ();
$new_products_query = osDBquery($new_products_query);
while ($new_products = os_db_fetch_array($new_products_query, true)) 
{
	$module_content[] = $product->buildDataArray($new_products);
}

if (sizeof($module_content) >= 1)
{
	$module->assign('NEW_PRODUCTS_LINK', os_href_link(FILENAME_PRODUCTS_NEW));
	$module->assign('language', $_SESSION['language']);
	$module->assign('module_content', $module_content);

	if (!CacheCheck())
	{
		$module->caching = 0;
		if ((!isset ($new_products_category_id)) || ($new_products_category_id == '0'))
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/new_products_default.html');
		else
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/new_products_category.html');
	}
	else
	{
		$module->caching = 1;
		$module->cache_lifetime = CACHE_LIFETIME;
		$module->cache_modified_check = CACHE_CHECK;
		$cache_id = $new_products_category_id.$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
		if ((!isset ($new_products_category_id)) || ($new_products_category_id == '0'))
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/new_products_default.html', $cache_id);
		else
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/new_products_category.html', $cache_id);
	}

	$default->assign('MODULE_new_products', $module);
}