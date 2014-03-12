<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include ('includes/top.php');
//$osTemplate = new osTemplate;


$breadcrumb->add(NAVBAR_TITLE_REVIEWS, os_href_link(FILENAME_REVIEWS));

require (_INCLUDES.'header.php');

if ($_SESSION['customers_status']['customers_status_read_reviews'] == 0) {
             os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));
}

$reviews_query_raw = "select r.reviews_id, left(rd.reviews_text, 250) as reviews_text, r.reviews_rating, r.date_added, r.status, p.products_id, pd.products_name, p.products_image, r.customers_name from ".TABLE_REVIEWS." r, ".TABLE_REVIEWS_DESCRIPTION." rd, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_status = '1' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and r.status = '1' and p.products_id = pd.products_id and pd.language_id = '".(int) $_SESSION['languages_id']."' and rd.languages_id = '".(int) $_SESSION['languages_id']."' order by r.reviews_id DESC";
$reviews_split = new splitPageResults($reviews_query_raw, $_GET['page'], MAX_DISPLAY_NEW_REVIEWS);

if ($reviews_split->number_of_rows > 0) {

	$osTemplate->assign('PAGINATION', $reviews_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y'))));

}

$module_data = array ();
if ($reviews_split->number_of_rows > 0) {
	$reviews_query = os_db_query($reviews_split->sql_query);
	while ($reviews = os_db_fetch_array($reviews_query)) {
	   $products_image = dir_path('images_thumbnail').$reviews['products_image'];
	   
if (!is_file($products_image)) $products_image = http_path('images_thumbnail').'../noimage.gif';
else $products_image = http_path('images_thumbnail').$reviews['products_image'];

		$module_data[] = array ('PRODUCTS_IMAGE' => $products_image, $reviews['products_name'], 'PRODUCTS_LINK' => os_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id='.$reviews['products_id'].'&reviews_id='.$reviews['reviews_id']), 'PRODUCTS_NAME' => $reviews['products_name'], 'AUTHOR' => $reviews['customers_name'], 'TEXT' => '('.sprintf(TEXT_REVIEW_WORD_COUNT, os_word_count($reviews['reviews_text'], ' ')).')<br />'.os_break_string(htmlspecialchars($reviews['reviews_text']), 60, '-<br />').'..', 'RATING' => os_image('themes/'.CURRENT_TEMPLATE.'/img/stars_'.$reviews['reviews_rating'].'.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])));

	}
	$osTemplate->assign('module_content', $module_data);
}

$osTemplate->assign('language', $_SESSION['language']);

 if (!CacheCheck()) {
	$osTemplate->caching = 0;
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/reviews.html');
} else {
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'];
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/reviews.html', $cache_id);
}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_REVIEWS.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_REVIEWS.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>