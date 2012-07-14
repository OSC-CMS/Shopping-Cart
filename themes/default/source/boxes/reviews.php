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
$box_content='';

  $fsk_lock='';
  if ($_SESSION['customers_status']['customers_fsk18_display']=='0') {
  $fsk_lock=' and p.products_fsk18!=1';
  }
  $random_select = "select r.reviews_id, r.reviews_rating, r.status, p.products_id, p.products_image, pd.products_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = r.products_id ".$fsk_lock." and r.reviews_id = rd.reviews_id and r.status = '1' and rd.languages_id = '" . (int)$_SESSION['languages_id'] . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'";
  if ($product->isProduct()) {
    $random_select .= " and p.products_id = '" . $product->data['products_id'] . "'";
  }
  $random_select .= " order by r.reviews_id desc limit " . MAX_RANDOM_SELECT_REVIEWS;
  $random_product = os_random_select($random_select);


  if ($random_product) {
    // display random review box
    $review_query = "select substring(reviews_text, 1, 60) as reviews_text from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $random_product['reviews_id'] . "' and languages_id = '" . $_SESSION['languages_id'] . "'";
    $review_query = osDBquery($review_query);
    $review = os_db_fetch_array($review_query,true);

    $review = htmlspecialchars($review['reviews_text']);
    $review = os_break_string($review, 15, '-<br />');

$products_image = dir_path('images_thumbnail') . $random_product['products_image'];
if (!file_exists($products_image)) 
{
   $products_image = http_path('images_thumbnail').'../noimage.gif';
}
else
{
   $products_image = http_path('images_thumbnail').$random_product['products_image'];
}

$box_content = '<div align="center"><a href="' . os_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_product['products_id'] . '&reviews_id=' . $random_product['reviews_id']) . '">' . os_image($products_image, $random_product['products_name']) . '</a></p><a href="' . os_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_product['products_id'] . '&reviews_id=' . $random_product['reviews_id']) . '">' . $review . ' ..</a><p>' . os_image('themes/' . CURRENT_TEMPLATE . '/img/stars_' . $random_product['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $random_product['reviews_rating'])) . '</div>';

  } elseif ($product->isProduct()) {
    // display 'write a review' box
    $box_content = '<table border="0" cellspacing="0" cellpadding="2"><tr><td class="infoBoxContents"><a href="' . os_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, os_product_link($product->data['products_id'],$product->data['products_name'])) . '">' . os_image('themes/' . CURRENT_TEMPLATE . '/img/box_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a></td><td class="infoBoxContents"><a href="' . os_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, os_product_link($product->data['products_id'],$product->data['products_name'])) . '">' . BOX_REVIEWS_WRITE_REVIEW .'</a></td></tr></table>';
   }

  if ($box_content!='') {
  $box->assign('REVIEWS_LINK',os_href_link(FILENAME_REVIEWS)); 
  $box->assign('BOX_CONTENT', $box_content);
  $box->assign('language', $_SESSION['language']);
  // set cache ID
 if (!CacheCheck()) {
  $box->caching = 0;
  $box_reviews= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_reviews.html');
  } else {
  $box->caching = 1;
  $box->cache_lifetime=CACHE_LIFETIME;
  $box->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$random_product['reviews_id'].$product->data['products_id'].$_SESSION['language'];
  $box_reviews= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_reviews.html',$cache_id);
  }
  $osTemplate->assign('box_REVIEWS',$box_reviews);

  } 

?>