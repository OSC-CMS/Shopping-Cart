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
	// set cache ID
	if (!CacheCheck()) {
	 	$cache=false;
		$box->caching = 0;
	} else {
		$cache=true;
		$box->caching = 1;
		$box->cache_lifetime = CACHE_LIFETIME;
		$box->cache_modified_check = CACHE_CHECK;
		$cache_id = $_SESSION['language'].$current_category_id;
	}

if (!$box->isCached(CURRENT_TEMPLATE.'/boxes/box_best_sellers.html', @$cache_id) || !$cache) {

$fsk_lock = '';
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
	$fsk_lock = ' and p.products_fsk18!=1';
}
if (GROUP_CHECK == 'true') {
	$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
}
if (isset ($current_category_id) && ($current_category_id > 0)) {
	$best_sellers_query = "select distinct
	                                        p.products_id,
	                                        p.products_price,
	                                        p.products_tax_class_id,
	                                        p.products_image,
                                           p.products_vpe,
				                           p.products_vpe_status,
				                           p.products_vpe_value,
	                                        pd.products_name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c
	                                        where p.products_status = '1'
	                                        and c.categories_status = '1'
	                                        and p.products_ordered > 0
	                                        and p.products_id = pd.products_id
	                                        and pd.language_id = '".(int) $_SESSION['languages_id']."'
	                                        and p.products_id = p2c.products_id
	                                        ".$group_check."
	                                        ".$fsk_lock."
	                                        and p2c.categories_id = c.categories_id and '".$current_category_id."'
	                                        in (c.categories_id, c.parent_id)
	                                        order by p.products_ordered desc limit ".MAX_DISPLAY_BESTSELLERS;
} else {
	$best_sellers_query = "select distinct
	                                        p.products_id,
	                                        p.products_image,
	                                        p.products_price,
                                           p.products_vpe,
				                           p.products_vpe_status,
				                           p.products_vpe_value,
	                                        p.products_tax_class_id,
	                                        pd.products_name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
	                                        where p.products_status = '1'
	                                        ".$group_check."
	                                        and p.products_ordered > 0
	                                        and p.products_id = pd.products_id ".$fsk_lock."
	                                        and pd.language_id = '".(int) $_SESSION['languages_id']."'
	                                        order by p.products_ordered desc limit ".MAX_DISPLAY_BESTSELLERS;
}
$best_sellers_query = osDBquery($best_sellers_query);
if (os_db_num_rows($best_sellers_query, true) >= MIN_DISPLAY_BESTSELLERS) {

	$rows = 0;
	$box_content = array ();
	while ($best_sellers = os_db_fetch_array($best_sellers_query, true)) {
		$rows ++;
		$image = '';
		
		$best_sellers = array_merge($best_sellers, array ('ID' => os_row_number_format($rows)));
		$box_content[] = $product->buildDataArray($best_sellers);
		
	}

	$box->assign('box_content', $box_content);
}

	// set cache ID
	 if (!$cache) {
	 	if ($box_content!='') {
		$box_best_sellers = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_best_sellers.html');
	 	}
	} else {
		$box_best_sellers = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_best_sellers.html', $cache_id);
	}

	$osTemplate->assign('box_BESTSELLERS', isset($box_best_sellers)?$box_best_sellers:'');

}
?>