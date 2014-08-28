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

include ('includes/top.php');
//$osTemplate = new osTemplate;
$osTemplate->assign('language', $_SESSION['language']);

$breadcrumb->add(NAVBAR_TITLE_PRODUCTS_NEW, os_href_link(FILENAME_PRODUCTS_NEW));

require (dir_path('includes').'header.php');

$rebuild = false;
if (!CacheCheck())
{
	$cache = false;
	$osTemplate->caching = 0;
}
else
{
	$cache = true;
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].$_SESSION['currency'].$_GET['page'];
}

if (!$osTemplate->isCached(CURRENT_TEMPLATE.'/module/new_products_overview.html', $cache_id) || !$cache)
{
	$rebuild = true;

	$days = array();
	if (MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0')
	{
		$date_new_products = date("Y.m.d", mktime(1, 1, 1, date(m), date(d) - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date(Y)));
		$days = array(" p.products_date_added > '".$date_new_products."' ");
	}

	$products_new_query_raw = $cartet->product->getList(array(
		'products_status' => 1,
		'category_status' => 1,
		'where' => $days,
		'order' => 'p.products_date_added DESC',
	));

	$products_new_split = new splitPageResults($products_new_query_raw, $_GET['page'], MAX_DISPLAY_PRODUCTS_NEW, 'p.products_id');

	if (($products_new_split->number_of_rows > 0))
	{
		$osTemplate->assign('PAGINATION', $products_new_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y'))));
	}

	$module_content = '';
	if ($products_new_split->number_of_rows > 0)
	{
		$products_new = os_db_query($products_new_split->sql_query);
		while ($products_data = os_db_fetch_array($products_new))
		{
			$module_content[] = $product->buildDataArray($products_data);
		}
	}
	else
		$osTemplate->assign('ERROR', TEXT_NO_NEW_PRODUCTS);
}

if (!$cache || $rebuild) 
{
	if (count($module_content) > 0)
	{
		$osTemplate->assign('module_content', $module_content);
		if ($rebuild)
			$osTemplate->clearCache(CURRENT_TEMPLATE.'/module/new_products_overview.html', $cache_id);

		$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/new_products_overview.html', $cache_id);
	}
}
else
{
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/new_products_overview.html', $cache_id);
}

$osTemplate->assign('main_content', $main_content);

$osTemplate->caching = 0;
$template = (file_exists(_THEMES_C.FILENAME_PRODUCTS_NEW.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_PRODUCTS_NEW.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);

include ('includes/bottom.php');