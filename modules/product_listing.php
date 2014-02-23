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

require_once (_FUNC.'params_filters.php');

$module = new osTemplate;
$result = true;

if (isset($_GET['on_page']) && is_numeric($_GET['on_page']))
	$num_page = $_GET['on_page'];
else
	$num_page = MAX_DISPLAY_SEARCH_RESULTS;

$module->assign('LINK_PAGE', os_href_link(basename($PHP_SELF), os_get_all_get_params(array('page','on_page','sort', 'direction', 'info','x','y')).'on_page='));

$listing_sql = str_replace("where p.products_status = '1'", "where ".@$search_by_params_ids." p.products_status = '1'", $listing_sql);

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

$listing_sql = get_params_listing_sql($listing_sql, @(int)($_GET['cat']), @$selectedGroups);

$listing_split = new splitPageResults($listing_sql, @(int)$_GET['page'], $num_page, 'p.products_id');

$module_content = array ();
if ($listing_split->number_of_rows > 0)
{
	$navigation = $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y')));

	if (GROUP_CHECK == 'true')
	{
		$group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
	}

	$category = $cartet->product->getCategory($current_category_id);

	$image = '';
	if ($category['categories_image'] != '')
		$image = http_path('images').'categories/'.$category['categories_image'];

	$module->assign('CATEGORIES_NAME', $category['categories_name']);
	$module->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);
	$module->assign('CATEGORIES_IMAGE', $image);
	$module->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);

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
				$_PRODUCTS_ID = $m['PRODUCTS_ID']; else $_PRODUCTS_ID= 0;

			if (isset($p_list[$_PRODUCTS_ID]))
				$module_content[$k]['params'] = $p_list[$_PRODUCTS_ID];
			else
				$module_content[$k]['params'] = '';
		}
	}
	// Parameters end

}
else
	$result = false;// no product found

// get default template
if (@$category['listing_template'] == '' or @$category['listing_template'] == 'default')
{
	$files = array ();
	if ($dir = opendir(_THEMES_C.'module/product_listing/'))
	{
		while (($file = readdir($dir)) !== false)
		{
			if (is_file(_THEMES_C.'module/product_listing/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
				$files[] = $file;
			}
		}
		closedir($dir);
	}
	sort($files);
	$category['listing_template'] = $files[0];
}

if ($result != false)
{
	$module->assign('param_filter', apply_filter('param_filter', '') );
	$module->assign('MANUFACTURER_DROPDOWN', @$manufacturer_dropdown);
	$module->assign('manufacturers', @$manufacturer_sort);
	$module->assign('manufacturer', $manufacturerData);
	$module->assign('all_manufacturers', @$all_manufacturers);
	$module->assign('language', $_SESSION['language']);
	$module->assign('module_content', $module_content);
	$module->assign('LINK_sort_name_asc',os_href_link(basename($PHP_SELF),os_get_all_get_params(array ('page','sort', 'direction', 'info','x','y')) . 'sort=name&direction=asc'));
	$module->assign('LINK_sort_name_desc',os_href_link(basename($PHP_SELF),os_get_all_get_params(array ('page','sort', 'direction', 'info','x','y')) . 'sort=name&direction=desc'));
	$module->assign('LINK_sort_price_asc',os_href_link(basename($PHP_SELF),os_get_all_get_params(array ('page','sort', 'direction', 'info','x','y')) . 'sort=price&direction=asc'));
	$module->assign('LINK_sort_price_desc',os_href_link(basename($PHP_SELF),os_get_all_get_params(array ('page','sort', 'direction', 'info','x','y')) . 'sort=price&direction=desc'));
	$module->assign('PAGINATION', $navigation);

	// set cache ID
	if (!CacheCheck())
	{
		$module->caching = 0;
		$module = $module->fetch(CURRENT_TEMPLATE.'/module/product_listing/'.$category['listing_template']);
	}
	else
	{
		$module->caching = 1;
		$module->cache_lifetime = CACHE_LIFETIME;
		$module->cache_modified_check = CACHE_CHECK;
		$cache_id = $current_category_id.'_'.$_SESSION['language'].'_'.$_SESSION['customers_status']['customers_status_name'].'_'.$_SESSION['currency'].'_'.$_GET['manufacturers_id'].'_'.$_GET['filter_id'].'__'.$_GET['q'].'_'.$_GET['price_min'].'_'.$_GET['price_max'].'_'.$_GET['on_page'].'_'.$_GET['page'].'_'.$_GET['keywords'].'_'.$_GET['categories_id'].'_'.$_GET['pfrom'].'_'.$_GET['pto'].'_'.$_GET['x'].'_'.$_GET['y'];
		$module = $module->fetch(CURRENT_TEMPLATE.'/module/product_listing/'.$category['listing_template'], $cache_id);
	}
	$module = apply_filter('main_content', $module);
	$osTemplate->assign('main_content', $module);
}
else
{
	$error = TEXT_PRODUCT_NOT_FOUND;
	include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);
}