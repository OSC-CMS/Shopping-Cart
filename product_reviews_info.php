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

$get_params = os_get_all_get_params(array ('reviews_id'));
$get_params = substr($get_params, 0, -1); //remove trailing &

$reviews_query = "select rd.reviews_text, r.reviews_rating, r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, r.status, p.products_id, pd.products_name, p.products_image from ".TABLE_REVIEWS." r left join ".TABLE_PRODUCTS." p on (r.products_id = p.products_id) left join ".TABLE_PRODUCTS_DESCRIPTION." pd on (p.products_id = pd.products_id and pd.language_id = '".(int) $_SESSION['languages_id']."'), ".TABLE_REVIEWS_DESCRIPTION." rd where r.reviews_id = '".(int) $_GET['reviews_id']."' and r.reviews_id = rd.reviews_id and r.status = '1' and p.products_status = '1'";
$reviews_query = os_db_query($reviews_query);

if (!os_db_num_rows($reviews_query))
	os_redirect(os_href_link(FILENAME_REVIEWS));
$reviews = os_db_fetch_array($reviews_query);

$breadcrumb->add(NAVBAR_TITLE_PRODUCT_REVIEWS, os_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));

os_db_query("update ".TABLE_REVIEWS." set reviews_read = reviews_read+1 where reviews_id = '".$reviews['reviews_id']."'");

$reviews_text = os_break_string(htmlspecialchars($reviews['reviews_text']), 60, '-<br />');

require ( dir_path('includes') .'header.php');

$osTemplate->assign('PRODUCTS_NAME', $reviews['products_name']);
$osTemplate->assign('AUTHOR', $reviews['customers_name']);
$osTemplate->assign('DATE', os_date_long($reviews['date_added']));
$osTemplate->assign('REVIEWS_TEXT', nl2br($reviews_text));
$osTemplate->assign('RATING', os_image( http_path('themes_c').'/img/stars_'.$reviews['reviews_rating'].'.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])));
$osTemplate->assign('PRODUCTS_LINK', os_href_link(FILENAME_PRODUCT_INFO, os_product_link($reviews['products_id'], $reviews['products_name'])));

 $_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link(FILENAME_PRODUCT_REVIEWS, $get_params), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	}
	
$osTemplate->assign('BUTTON_BACK', $_array['code']);


 $_array = array('img' => 'button_in_cart.gif', 
                         'href' => os_href_link(FILENAME_DEFAULT, 'action=buy_now&BUYproducts_id='.$reviews['products_id']), 
                         'alt' => IMAGE_BUTTON_IN_CART, 
                         'code' => '');
	
	     $_array = apply_filter('button_in_cart', $_array);
	
	     if (empty($_array['code']))
	     {
			 $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	     }
		 
$osTemplate->assign('BUTTON_BUY_NOW', $_array['code']);

$products_image = dir_path('images_thumbnail').$reviews['products_image'];

if (!is_file($products_image)) $products_image = http_path('images_thumbnail').'../noimage.gif';
else $products_image = http_path('images_thumbnail').$reviews['products_image'];

$osTemplate->assign('IMAGE', $products_image);

$osTemplate->assign('language', $_SESSION['language']);

// set cache ID
 if (!CacheCheck()) {
	$osTemplate->caching = 0;
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/product_reviews_info.html');
} else {
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$reviews['reviews_id'];
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/product_reviews_info.html', $cache_id);
}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_PRODUCT_REVIEWS_INFO.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_PRODUCT_REVIEWS_INFO.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>