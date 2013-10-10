<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

// Boxes
define('DIR_WS_BOXES', _THEMES_C.'source/boxes/');

if (VIS_BOX_AFFILIATE			== 'true') { include(DIR_WS_BOXES.'affiliate.php'); }
if (VIS_BOX_CATEGORIES			== 'true') { include(DIR_WS_BOXES.'categories.php'); }
if (VIS_BOX_ARTICLES			== 'true') { include(DIR_WS_BOXES.'articles.php'); }
if (VIS_BOX_ARTICLES_NEW		== 'true') { include(DIR_WS_BOXES.'articles_new.php'); }
if (VIS_BOX_LAST_VIEWED			== 'true') { include(DIR_WS_BOXES.'last_viewed.php'); }
if (VIS_BOX_SEARCH				== 'true') { include(DIR_WS_BOXES.'search.php'); }
if (VIS_BOX_CONTENT				== 'true') { include(DIR_WS_BOXES.'content.php'); }
if (VIS_BOX_INFORMATION			== 'true') { include(DIR_WS_BOXES.'information.php'); }
if (VIS_BOX_NEWS				== 'true') { include(DIR_WS_BOXES.'news.php'); }
if (VIS_BOX_FAQ					== 'true') { include(DIR_WS_BOXES.'faq.php'); }
if (VIS_BOX_LANGUAGES			== 'true') { include(DIR_WS_BOXES.'languages.php'); }
//if (VIS_BOX_INFOBOX				== 'true') { include(DIR_WS_BOXES.'infobox.php'); }
if (VIS_BOX_LOGIN				== 'true') { include(DIR_WS_BOXES.'loginbox.php'); }
if (VIS_BOX_LOGIN				== 'true') { include(DIR_WS_BOXES.'accountbox.php'); }
if (VIS_BOX_NEWSLETTER			== 'true') { include(DIR_WS_BOXES.'newsletter.php'); }
if (VIS_BOX_BEST_SELLERS		== 'true') { include(DIR_WS_BOXES.'best_sellers.php'); }
if (VIS_BOX_SPECIALS			== 'true') { include(DIR_WS_BOXES.'specials.php'); }
if (VIS_BOX_FEATURED			== 'true') { include(DIR_WS_BOXES.'featured.php'); }
if (VIS_BOX_CURRENCIES			== 'true') { include(DIR_WS_BOXES.'currencies.php'); }
if (VIS_BOX_DOWNLOADS			== 'true') { include(DIR_WS_BOXES.'download.php'); }
if (VIS_BOX_WHATSNEW			== 'true') { include(DIR_WS_BOXES.'whats_new.php'); }
if (VIS_BOX_REVIEWS				== 'true') { if ($_SESSION['customers_status']['customers_status_read_reviews'] == 1) { include(DIR_WS_BOXES.'reviews.php'); } }
if (VIS_BOX_ADMIN				== 'true') { if ($_SESSION['customers_status']['customers_status_id'] == 0) { include(DIR_WS_BOXES.'admin.php'); } }
if (VIS_BOX_CART				== 'true') { if ($_SESSION['customers_status']['customers_status_show_price'] == 1) { include(DIR_WS_BOXES.'shopping_cart.php'); } }
if (VIS_BOX_ADD_A_QUICKIE		== 'true') { if ($_SESSION['customers_status']['customers_status_show_price'] != '0') { include(DIR_WS_BOXES.'add_a_quickie.php'); } }
if (VIS_BOX_ORDER_HISTORY		== 'true') { if (isset($_SESSION['customer_id'])) { include(DIR_WS_BOXES.'order_history.php'); } }
if (VIS_BOX_MANUFACTURERS		== 'true') { include(DIR_WS_BOXES.'manufacturers.php'); }
if (VIS_BOX_MANUFACTURERS_INFO	== 'true') { if ($product->isProduct()) { include(DIR_WS_BOXES.'manufacturer_info.php'); } }

// Current Cat
function getCurrCat() {
	global $cPath;
	$full = explode('_', $cPath);
	return array_pop($full);
}
define("CURR_CAT", getCurrCat());
define("CURR_CAT_LINK", os_href_link(FILENAME_DEFAULT, os_category_link(CURR_CAT)));

?>