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

$products = $cartet->product->getList(array(
	'products_status' => 1,
	'where' => array('s.status = 1'),
	'order' => 's.specials_date_added ASC',
	'limit' => MAX_RANDOM_SELECT_SPECIALS,
));

if ($random_product = os_random_select($products))
{
	$box->assign('box_content',$product->buildDataArray($random_product));
	$box->assign('SPECIALS_LINK', os_href_link(FILENAME_SPECIALS));

	$box->assign('language', $_SESSION['language']);
	if ($random_product["products_id"] != '')
	{
		if (!CacheCheck())
		{
			$box->caching = 0;
			$box_specials = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_specials.html');
		}
		else
		{
			$box->caching = 1;
			$box->cache_lifetime = CACHE_LIFETIME;
			$box->cache_modified_check = CACHE_CHECK;
			$cache_id = $_SESSION['language'].$random_product["products_id"].$_SESSION['customers_status']['customers_status_name'];
			$box_specials = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_specials.html', $cache_id);
		}

		$osTemplate->assign('box_SPECIALS', $box_specials);
	}
}