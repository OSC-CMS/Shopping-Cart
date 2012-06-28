<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

include ('includes/top.php');
//$osTemplate = new osTemplate;

$get_params = os_get_all_get_params();
$get_params_back = os_get_all_get_params(array ('reviews_id')); // for back button
$get_params = substr($get_params, 0, -1); //remove trailing &
if (os_not_null($get_params_back)) {
	$get_params_back = substr($get_params_back, 0, -1); //remove trailing &
} else {
	$get_params_back = $get_params;
}

$product_info_query = os_db_query("select pd.products_name from ".TABLE_PRODUCTS_DESCRIPTION." pd left join ".TABLE_PRODUCTS." p on pd.products_id = p.products_id where pd.language_id = '".(int) $_SESSION['languages_id']."' and p.products_status = '1' and pd.products_id = '".(int) $_GET['products_id']."'");
if (!os_db_num_rows($product_info_query))
	os_redirect(os_href_link(FILENAME_REVIEWS));
$product_info = os_db_fetch_array($product_info_query);

$breadcrumb->add(NAVBAR_TITLE_PRODUCT_REVIEWS, os_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));

require (dir_path('includes').'header.php');

$osTemplate->assign('PRODUCTS_NAME', $product_info['products_name']);

$data_reviews = array ();
$reviews_query = os_db_query("select reviews_rating, reviews_id, customers_name, date_added, last_modified, reviews_read, status from ".TABLE_REVIEWS." where products_id = '".(int) $_GET['products_id']."' and status = '1' order by reviews_id DESC");
if (os_db_num_rows($reviews_query)) {
	$row = 0;
	while ($reviews = os_db_fetch_array($reviews_query)) {
		$row ++;
		$data_reviews[] = array ('ID' => $reviews['reviews_id'], 'AUTHOR' => '<a href="'.os_href_link(FILENAME_PRODUCT_REVIEWS_INFO, $get_params.'&reviews_id='.$reviews['reviews_id']).'">'.$reviews['customers_name'].'</a>', 'DATE' => os_date_short($reviews['date_added']), 'RATING' => os_image('themes/'.CURRENT_TEMPLATE.'/img/stars_'.$reviews['reviews_rating'].'.gif', sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating'])), 'TEXT' => os_break_string(htmlspecialchars($reviews['reviews_text']), 60, '-<br />'));

	}
}
$osTemplate->assign('module_content', $data_reviews);

 $_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link(FILENAME_PRODUCT_INFO, $get_params_back), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}
	
$osTemplate->assign('BUTTON_BACK', $_array['code']);


	$_array = array('img' => 'button_write_review.gif', 
	                                'href' => os_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, $get_params), 
									'alt' => IMAGE_BUTTON_WRITE_REVIEW,
                  /* код готовой кнопки, по умолчанию пусто */									
									'code' => ''
	);
	
	$_array = apply_filter('button_write', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}
	
$osTemplate->assign('BUTTON_WRITE', $_array['code']);

$osTemplate->assign('language', $_SESSION['language']);

// set cache ID
 if (!CacheCheck()) {
	$osTemplate->caching = 0;
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/product_reviews.html');
} else {
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_GET['products_id'];
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/product_reviews.html', $cache_id);
}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_PRODUCT_REVIEWS.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_PRODUCT_REVIEWS.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>