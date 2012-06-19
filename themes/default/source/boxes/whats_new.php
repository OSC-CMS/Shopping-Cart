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

$box = new osTemplate;
$box->assign('tpl_path', _HTTP_THEMES_C);
$box_content = '';

$fsk_lock = '';
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
	$fsk_lock = ' and p.products_fsk18!=1';
}
if (GROUP_CHECK == 'true') 
{
	$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
}

if ( !isset($_GET['products_id']) ) $_GET['products_id'] = 0;

if ($random_product = os_random_select("select distinct
                                           p.products_id,
                                           pd.products_name,
                                           p.products_image,
                                           p.products_tax_class_id,
                                           p.products_vpe,
				                           p.products_vpe_status,
				                           p.products_vpe_value,
                                           p.products_price
                                           from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c
                                           where p.products_status=1
                                           and p.products_id = p2c.products_id
                                           and pd.products_id = p.products_id
                                           and p.products_id !='".(int) $_GET['products_id']."'
                                           and pd.language_id = '".$_SESSION['languages_id']."'
                                           and c.categories_id = p2c.categories_id
                                           ".$group_check."
                                           ".$fsk_lock."
                                           and c.categories_status=1 order by
                                           p.products_date_added desc limit ".MAX_RANDOM_SELECT_NEW)) {

	$whats_new_price = $osPrice->GetPrice($random_product['products_id'], $format = true, 1, $random_product['products_tax_class_id'], $random_product['products_price']);
}

if ($random_product['products_name'] != '') {

	$box->assign('box_content',$product->buildDataArray($random_product));
	$box->assign('LINK_NEW_PRODUCTS',os_href_link(FILENAME_PRODUCTS_NEW));
	$box->assign('language', $_SESSION['language']);
	// set cache ID
	 if (!CacheCheck()) {
		$box->caching = 0;
		$box_whats_new = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_whatsnew.html');
	} else {
		$box->caching = 1;
		$box->cache_lifetime = CACHE_LIFETIME;
		$box->cache_modified_check = CACHE_CHECK;
		$cache_id = $_SESSION['language'].$random_product['products_id'].$_SESSION['customers_status']['customers_status_name'];
		$box_whats_new = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_whatsnew.html', $cache_id);
	}

	$osTemplate->assign('box_WHATSNEW', $box_whats_new);
}
?>