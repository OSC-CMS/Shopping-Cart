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

if ((!isset ($featured_products_category_id)) || ($featured_products_category_id == '0'))
{
	$featured_products_query = $cartet->product->getList(array(
		'products_status' => 1,
		'category_status' => 1,
		'group' => 'p.products_id',
		'where' => array('f.products_id = p.products_id', 'f.status = 1'),
		'order' => 'p.products_date_added DESC',
		'limit' => MAX_DISPLAY_FEATURED_PRODUCTS,
	));
}
else
{
	$featured_products_query = $cartet->product->getList(array(
		'products_status' => 1,
		'category_status' => 1,
		'group' => 'p.products_id',
		'where' => array('f.products_id = p.products_id', 'f.status = 1', 'c.parent_id = '.$featured_products_category_id),
		'order' => 'p.products_date_added DESC',
		'limit' => MAX_DISPLAY_FEATURED_PRODUCTS,
	));
}

$module_content = array ();
$featured_products_query = osDBquery($featured_products_query);
while ($featured_products = os_db_fetch_array($featured_products_query, true))
{
	$module_content[] = $product->buildDataArray($featured_products);
}

if (sizeof($module_content) >= 1)
{
	$module->assign('FEATURED_LINK', os_href_link(FILENAME_FEATURED));
	$module->assign('language', $_SESSION['language']);
	$module->assign('module_content', $module_content);

	if (!CacheCheck())
	{
		$module->caching = 0;
		if ((!isset ($featured_products_category_id)) || ($featured_products_category_id == '0'))
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/featured_products_default.html');
		else
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/featured_products_category.html');
	}
	else
	{
		$module->caching = 1;
		$module->cache_lifetime = CACHE_LIFETIME;
		$module->cache_modified_check = CACHE_CHECK;
		$cache_id = $featured_products_category_id.$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
		if ((!isset ($featured_products_category_id)) || ($featured_products_category_id == '0'))
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/featured_products_default.html', $cache_id);
		else
			$module = $module->fetch(CURRENT_TEMPLATE.'/module/featured_products_category.html', $cache_id);
	}

	$default->assign('MODULE_featured_products', $module);
}