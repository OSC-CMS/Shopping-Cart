<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

do_action('action_products_listing');

$result = true;

if (isset($_GET['on_page']) && is_numeric($_GET['on_page'])) {
	$num_page =  $_GET['on_page'];
} else {
	$num_page =  MAX_DISPLAY_SEARCH_RESULTS;
}

$default->assign('LINK_PAGE',os_href_link(basename($PHP_SELF),os_get_all_get_params(array ('page','on_page','sort', 'direction', 'info','x','y')) . 'on_page='));

if (isset($_GET['status']))
{

	if ('all' === $_GET['status'])
	{
		$listing_sql = str_replace("p.products_status = '1'", "1", $listing_sql);
	}
	else
	{
		$_GET['status'] = (int)($_GET['status']);
		$listing_sql = str_replace("p.products_status = '1'", "p.products_status = '".$_GET['status']."'", $listing_sql);
	}
}

$listing_split = new splitPageResults($listing_sql, @(int)$_GET['page'], $num_page, 'p.products_id');

$module_content = array ();
if ($listing_split->number_of_rows > 0) {

	$navigation = $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y')));

	if (GROUP_CHECK == 'true') {
		$group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
	}

	$rows = 0;

	$listing_query = osDBquery($listing_split->sql_query);

	/*
	получение массива товаров
	*/
	while ($listing = os_db_fetch_array($listing_query, true))
	{
		//$rows ++;
		$_products_array[$listing['products_id']] = $listing;
		//$module_content[] =  $product->buildDataArray($listing);
		//$ids[] = $module_content[sizeof($module_content) - 1]['PRODUCTS_ID'];
	}

	global $_products_array;

	if (!empty($_products_array))
	{
		foreach ($_products_array as $_products_id => $_products_value)
		{
			$rows ++;
			$module_content[] = apply_filter('products_listing', $product->buildDataArray($_products_value) );
			$ids[] = $module_content[sizeof($module_content) - 1]['PRODUCTS_ID'];
		}
	}

	// Parameters start
	if (is_array($ids) && sizeof($ids) > 0)
	{
		$cats = osDBquery("SELECT products_id, categories_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id IN (".implode(", ", $ids).")");
		$temp = array();

		while ($c = os_db_fetch_array($cats, true))
		{
			if (isset($temp[$c['products_id']]) && $temp[$c['products_id']] < 1)
				$temp[$c['products_id']] =  $c['categories_id'];
		}

		$p_list = array();
		foreach($module_content as $k => $m)
		{
			if (isset($m['PRODUCTS_ID']))
				$_PRODUCTS_ID = $m['PRODUCTS_ID'];
			else
				$_PRODUCTS_ID= 0;

			if (isset($p_list[$_PRODUCTS_ID]))
				$module_content[$k]['params'] = $p_list[$_PRODUCTS_ID];
			else
				$module_content[$k]['params'] = '';
		}
	}
	// Parameters end
}
else
	$result = false;

if ($result != false)
{
	$default->assign('param_filter', apply_filter('param_filter', '') );
	$default->assign('MANUFACTURER_DROPDOWN', @$manufacturer_dropdown);
	$default->assign('MANUFACTURER_SORT', @$manufacturer_sort);
	$default->assign('module_content', $module_content);
	$default->assign('LINK_sort_name_asc',os_href_link(basename($PHP_SELF),os_get_all_get_params(array ('page','sort', 'direction', 'info','x','y')) . 'sort=name&direction=asc'));
	$default->assign('LINK_sort_name_desc',os_href_link(basename($PHP_SELF),os_get_all_get_params(array ('page','sort', 'direction', 'info','x','y')) . 'sort=name&direction=desc'));
	$default->assign('LINK_sort_price_asc',os_href_link(basename($PHP_SELF),os_get_all_get_params(array ('page','sort', 'direction', 'info','x','y')) . 'sort=price&direction=asc'));
	$default->assign('LINK_sort_price_desc',os_href_link(basename($PHP_SELF),os_get_all_get_params(array ('page','sort', 'direction', 'info','x','y')) . 'sort=price&direction=desc'));

	$default->assign('PAGINATION', $navigation);

	$module = apply_filter('main_content', $module);
}
else
{

	$error = TEXT_PRODUCT_NOT_FOUND;
	include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);
}