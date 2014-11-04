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
//require (_THEMES_C.'/source/boxes.php');

if ($_SESSION['customers_status']['customers_status_write_reviews'] == 0) {
             os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if (isset ($_GET['action']) && $_GET['action'] == 'process') {
	if (is_object($product) && $product->isProduct()) { // We got to the process but it is an illegal product, don't write

    $rating = os_db_prepare_input($_POST['rating']);
    $review = os_db_prepare_input($_POST['review']);

    $error = false;
    
    if ($_POST['captcha'] == '' or $_POST['captcha'] != $_SESSION['captcha_keystring']) {
      $error = true;
	   $osTemplate->assign('captcha_error', ENTRY_CAPTCHA_ERROR);
    }

    if (strlen($review) < REVIEW_TEXT_MIN_LENGTH) {
      $error = true;
   	$osTemplate->assign('error', ERROR_INVALID_PRODUCT);
    }

    if (($rating < 1) || ($rating > 5)) {
      $error = true;
   	$osTemplate->assign('error', ERROR_INVALID_PRODUCT);
    }

    if ($error == false) {
		$customer = os_db_query("select customers_firstname, customers_lastname from ".TABLE_CUSTOMERS." where customers_id = '".(int) $_SESSION['customer_id']."'");
		$customer_values = os_db_fetch_array($customer);
		$date_now = date('Ymd');

		$status = (USE_REVIEWS_MODERATION == 'true') ? '0' : '1';

		if ($_SESSION['customer_id'] == '')
			$customer_values['customers_lastname'] = TEXT_GUEST;
		os_db_query("insert into ".TABLE_REVIEWS." (products_id, customers_id, customers_name, reviews_rating, date_added, status) values ('".$product->data['products_id']."', '".(int) $_SESSION['customer_id']."', '".addslashes($customer_values['customers_firstname']).' '.addslashes($customer_values['customers_lastname'])."', '".addslashes($_POST['rating'])."', now(), '".$status."')");
		$insert_id = os_db_insert_id();
		os_db_query("insert into ".TABLE_REVIEWS_DESCRIPTION." (reviews_id, languages_id, reviews_text) values ('".$insert_id."', '".(int) $_SESSION['languages_id']."', '".addslashes($_POST['review'])."')");


	os_redirect(os_href_link(FILENAME_PRODUCT_INFO, $_POST['get_params']));
	}
 }
}

// lets retrieve all $HTTP_GET_VARS keys and values..
$get_params = os_get_all_get_params();
$get_params_back = os_get_all_get_params(array ('reviews_id')); // for back button
$get_params = substr($get_params, 0, -1); //remove trailing &
if (os_not_null($get_params_back)) {
	$get_params_back = substr($get_params_back, 0, -1); //remove trailing &
} else {
	$get_params_back = $get_params;
}

$breadcrumb->add(NAVBAR_TITLE_REVIEWS_WRITE, os_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));

$customer_info_query = os_db_query("select customers_firstname, customers_lastname from ".TABLE_CUSTOMERS." where customers_id = '".(int) $_SESSION['customer_id']."'");
$customer_info = os_db_fetch_array($customer_info_query);

require (dir_path('includes').'header.php');

if (!$product->isProduct())
{
	$osTemplate->assign('error', ERROR_INVALID_PRODUCT);
}
else
{
	$name = $customer_info['customers_firstname'].' '.$customer_info['customers_lastname'];
	if ($name == ' ')
		$customer_info['customers_lastname'] = TEXT_GUEST;
	$osTemplate->assign('PRODUCTS_NAME', $product->data['products_name']);
	$osTemplate->assign('AUTHOR', $customer_info['customers_firstname'].' '.$customer_info['customers_lastname']);
	$osTemplate->assign('INPUT_TEXT', os_draw_textarea_field('review', 'soft', '', '', $_POST['review'], '', false));
	$osTemplate->assign('INPUT_RATING', os_draw_radio_field('rating', '1').' '.os_draw_radio_field('rating', '2').' '.os_draw_radio_field('rating', '3').' '.os_draw_radio_field('rating', '4').' '.os_draw_radio_field('rating', '5'));
	$osTemplate->assign('FORM_ACTION', os_draw_form('product_reviews_write', os_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'action=process&'.os_product_link($product->data['products_id'],$product->data['products_name'])), 'post'));
	
	 $_array = array('img' => 'button_back.gif', 
	                                'href' => 'javascript:history.back(1)', 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	}
	
	$osTemplate->assign('BUTTON_BACK', $_array['code'] );
	
	
	$osTemplate->assign('BUTTON_SUBMIT', button_continue_submit().os_draw_hidden_field('get_params', $get_params));
	$osTemplate->assign('CAPTCHA_IMG', '<img src="'.os_href_link(FILENAME_DISPLAY_CAPTCHA).'" alt="captcha" name="captcha" />');
	$osTemplate->assign('CAPTCHA_INPUT', os_draw_input_field('captcha', '', 'size="6" id="captcha"', 'text', false));
	$osTemplate->assign('FORM_END', '</form>');
}

$osTemplate->assign('language', $_SESSION['language']);

$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/product_reviews_write.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_HTTP_THEMES_C.FILENAME_PRODUCT_REVIEWS_WRITE.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_PRODUCT_REVIEWS_WRITE.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>