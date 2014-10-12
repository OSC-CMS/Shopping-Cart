<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$box = new osTemplate;
$box_content = '';

$box->assign('language', $_SESSION['language']);

if (!CacheCheck())
{
	$cache = false;
	$box->caching = 0;
}
else
{
	$cache = true;
	$box->caching = 1;
	$box->cache_lifetime = CACHE_LIFETIME;
	$box->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$current_category_id;
}

if (!$box->isCached(CURRENT_TEMPLATE.'/boxes/box_best_sellers.html', @$cache_id) || !$cache)
{
	if (isset ($current_category_id) && ($current_category_id > 0))
	{
		$best_sellers_query = $cartet->product->getList(array(
			'products_status' => 1,
			'category_status' => 1,
			'where' => array('p.products_ordered > 0', '\''.$current_category_id.'\' in (c.categories_id, c.parent_id)'),
			'order' => 'p.products_ordered desc',
			'limit' => MAX_DISPLAY_BESTSELLERS,
		));
	}
	else
	{
		$best_sellers_query = $cartet->product->getList(array(
			'products_status' => 1,
			'category_status' => 1,
			'where' => array('p.products_ordered > 0'),
			'order' => 'p.products_ordered desc',
			'limit' => MAX_DISPLAY_BESTSELLERS,
		));
	}

	$best_sellers_query = osDBquery($best_sellers_query);
	if (os_db_num_rows($best_sellers_query, true) >= MIN_DISPLAY_BESTSELLERS)
	{
		$rows = 0;
		$box_content = array ();
		while ($best_sellers = os_db_fetch_array($best_sellers_query, true))
		{
			$rows ++;
			$best_sellers = array_merge($best_sellers, array ('ID' => os_row_number_format($rows)));
			$box_content[] = $product->buildDataArray($best_sellers);
		}

		$box->assign('box_content', $box_content);
	}

	// set cache ID
	if (!$cache)
	{
		if ($box_content != '')
		{
			$box_best_sellers = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_best_sellers.html');
		}
	}
	else
	{
		$box_best_sellers = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_best_sellers.html', $cache_id);
	}

	$osTemplate->assign('box_BESTSELLERS', isset($box_best_sellers) ? $box_best_sellers : '');
}