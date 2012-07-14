<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

$box = new osTemplate;
$box->assign('tpl_path', _HTTP_THEMES_C);
$box_content = '';

$fsk_lock = '';
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
	$fsk_lock = ' and p.products_fsk18!=1';
}
if (GROUP_CHECK == 'true') {
	$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
}
if ($random_product = os_random_select("select
                                           p.products_id,
                                           pd.products_name,
                                           p.products_price,
                                           p.products_tax_class_id,
                                           p.products_image,
                                           f.expires_date,
                                           p.products_vpe,
				                           p.products_vpe_status,
				                           p.products_vpe_value
                                           from ".TABLE_PRODUCTS." p,
                                           ".TABLE_PRODUCTS_DESCRIPTION." pd,
                                           ".TABLE_FEATURED." f where p.products_status = '1'
                                           and p.products_id = f.products_id
                                           and pd.products_id = f.products_id
                                           and pd.language_id = '".$_SESSION['languages_id']."'
                                           and f.status = '1'
                                           ".$group_check."
                                           ".$fsk_lock."                                             
                                           order by f.featured_date_added
                                           desc limit ".MAX_RANDOM_SELECT_FEATURED)) {


$box->assign('box_content',$product->buildDataArray($random_product));
$box->assign('FEATURED_LINK', os_href_link(FILENAME_FEATURED));

$box->assign('language', $_SESSION['language']);
if ($random_product["products_id"] != '') {
	// set cache ID
	 if (!CacheCheck()) {
		$box->caching = 0;
		$box_featured = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_featured.html');
	} else {
		$box->caching = 1;
		$box->cache_lifetime = CACHE_LIFETIME;
		$box->cache_modified_check = CACHE_CHECK;
		$cache_id = $_SESSION['language'].$random_product["products_id"].$_SESSION['customers_status']['customers_status_name'];
		$box_featured = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_featured.html', $cache_id);
	}
	$osTemplate->assign('box_FEATURED', $box_featured);
}
}
?>