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


$product_info_query = os_db_query("select * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_status = '1' and p.products_id = '".(int)$_GET['products_id']."' and pd.products_id = p.products_id and pd.language_id = '".(int)$_SESSION['languages_id']."'");
$product_info = os_db_fetch_array($product_info_query);

if (isset($_SESSION['customer_id'])) { 
$account_query = os_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
$account = os_db_fetch_array($account_query);
}

$osTemplate->assign('language', $_SESSION['language']);

if (isset ($_GET['action']) && ($_GET['action'] == 'process')) {
	$error = false;

	if (isset($_SESSION['customer_id'])) { 
		$firstname = $account['customers_firstname'];
		$lastname = $account['customers_lastname'];
		$email_address = $account['customers_email_address'];
		$message = os_db_input($_POST['message_body']);
		$to_email_address = $email_address;
		$to_name = $firstname .' '. $lastname;
  } else {    
		$firstname = os_db_input($_POST['firstname']);
		$lastname = os_db_input($_POST['lastname']);
		$email_address = os_db_input($_POST['email_address']);
		$message = os_db_input($_POST['message_body']);
		$to_email_address = $email_address;
		$to_name = $firstname .' '. $lastname;
	}
	
	if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
		$error = true;
		$messageStack->add('ask_a_question', ENTRY_FIRST_NAME_ERROR);
	}

	if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
		$error = true;
		$messageStack->add('ask_a_question', ENTRY_LAST_NAME_ERROR);
	}

	if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
		$error = true;
		$messageStack->add('ask_a_question', ENTRY_EMAIL_ADDRESS_ERROR);
	}
	elseif (os_validate_email($email_address) == false) {
		$error = true;
		$messageStack->add('ask_a_question', ENTRY_EMAIL_ADDRESS_ERROR);
	} 

	if (($_POST['captcha'] != $_SESSION['captcha_keystring'])) {
		$error = true;
        $messageStack->add('ask_a_question', TEXT_WRONG_CODE);
	}

	if ($message == '') {
		$error = true;
		$messageStack->add('ask_a_question', TEXT_MESSAGE_ERROR);
	}

	//if ($messageStack->size('ask_a_question') > 0) {
//$osTemplate->assign('error', $messageStack->output('ask_a_question'));
	//}
include ('includes/header.php');
		if ($error == false) {
		$osTemplate->assign('PRODUCTS_NAME', $product_info['products_name']);
		$osTemplate->assign('PRODUCTS_MODEL', $product_info['products_model']);
		$osTemplate->assign('TEXT_MESSAGE', $_POST['message_body']);
		$osTemplate->assign('TEXT_FIRSTNAME', $firstname);
		$osTemplate->assign('TEXT_LASTNAME', $lastname);
		$osTemplate->assign('TEXT_EMAIL', $email_address);
		$osTemplate->assign('TEXT_EMAIL_SUCCESSFUL', sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, $product_info['products_name']));
		$osTemplate->assign('PRODUCT_LINK', os_href_link(FILENAME_PRODUCT_INFO, os_product_link($product->data['products_id'], $product->data['products_name'])));
		$osTemplate->caching = 0;
		$html_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/ask_a_question.html');
		$osTemplate->caching = 0;
		$txt_mail = $osTemplate->fetch(_MAIL.$_SESSION['language'].'/ask_a_question.txt');
	// send mail to admin
	os_php_mail($to_email_address, EMAIL_SUPPORT_NAME, EMAIL_SUPPORT_ADDRESS, STORE_NAME, EMAIL_SUPPORT_FORWARDING_STRING, $to_email_address, $to_name, '', '', NAVBAR_TITLE_ASK, $html_mail, $txt_mail);
	// send mail to customer
	os_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $to_email_address, $to_name, EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', NAVBAR_TITLE_ASK, $html_mail, $txt_mail);

if (!CacheCheck()) {
	$osTemplate->caching = 0;
	$osTemplate->display(CURRENT_TEMPLATE.'/module/ask_a_question_ok.html');
} else {
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'];
	$osTemplate->display(CURRENT_TEMPLATE.'/module/ask_a_question_ok.html', $cache_id);
		}
	}else{
$osTemplate->assign('PRODUCTS_NAME', $product_info['products_name']);
$osTemplate->assign('PRODUCTS_MODEL', $product_info['products_model']);

$osTemplate->assign('FORM_ACTION', os_draw_form('ask_a_question', os_href_link(FILENAME_ASK_PRODUCT_QUESTION, 'action=process&products_id='.$product->data['products_id'], 'SSL')));
$osTemplate->assign('CAPTCHA_IMG', '<img src="'.FILENAME_DISPLAY_CAPTCHA.'" alt="captcha" name="captcha" />'); 
$osTemplate->assign('CAPTCHA_INPUT', os_draw_input_field('captcha', '', 'size="6" maxlength="6"', 'text', false));

        if (isset($_SESSION['customer_id'])) { 
		//-> registered user********************************************************
$osTemplate->assign('INPUT_FIRSTNAME', $account['customers_firstname']);
$osTemplate->assign('INPUT_LASTNAME', $account['customers_lastname']);
$osTemplate->assign('INPUT_EMAIL', $account['customers_email_address']);
        }else{
		//-> guest *********************************************************  
$osTemplate->assign('INPUT_FIRSTNAME', os_draw_input_fieldNote(array ('name' => 'firstname', 'text' => '&nbsp;'. (os_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">'.ENTRY_FIRST_NAME_TEXT.'</span>' : ''))));
$osTemplate->assign('INPUT_LASTNAME', os_draw_input_fieldNote(array ('name' => 'lastname', 'text' => '&nbsp;'. (os_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">'.ENTRY_LAST_NAME_TEXT.'</span>' : ''))));
$osTemplate->assign('INPUT_EMAIL', os_draw_input_fieldNote(array ('name' => 'email_address', 'text' => '&nbsp;'. (os_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">'.ENTRY_EMAIL_ADDRESS_TEXT.'</span>' : ''))));
        }
$osTemplate->assign('INPUT_TEXT', os_draw_textarea_field('message_body', 'soft', 50, 15, stripslashes($_POST['message_body'])));
$osTemplate->assign('FORM_END', '</form>');
$osTemplate->assign('BUTTON_SUBMIT', button_continue_submit());

  	$_array = array('img' => 'button_back.gif', 
	                                'href' => 'javascript:window.close()', 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}
	
$osTemplate->assign('BUTTON_CONTINUE', $_array['code']);
include ('includes/header.php');
// set cache ID
 if (!CacheCheck()) {
	$osTemplate->caching = 0;
	$osTemplate->display(CURRENT_TEMPLATE.'/module/ask_a_question.html');
} else {
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'];
	$osTemplate->display(CURRENT_TEMPLATE.'/module/ask_a_question.html', $cache_id);
	}
}
}else{

$breadcrumb->add(NAVBAR_TITLE_ASK, os_href_link(FILENAME_ASK_PRODUCT_QUESTION, 'products_id='.$product->data['products_id'], 'SSL'));

$osTemplate->assign('PRODUCTS_NAME', $product_info['products_name']);
$osTemplate->assign('PRODUCTS_MODEL', $product_info['products_model']);
$osTemplate->assign('CAPTCHA_IMG', '<img src="'.FILENAME_DISPLAY_CAPTCHA.'" alt="captcha" name="captcha" />');    
$osTemplate->assign('CAPTCHA_INPUT', os_draw_input_field('captcha', '', 'size="6" maxlength="6"', 'text', false));

$osTemplate->assign('FORM_ACTION', os_draw_form('ask_a_question', os_href_link(FILENAME_ASK_PRODUCT_QUESTION, 'action=process&products_id='.$product->data['products_id'], 'SSL')));
        if (isset($_SESSION['customer_id'])) { 
		//-> registered user********************************************************
$osTemplate->assign('INPUT_FIRSTNAME', $account['customers_firstname']);
$osTemplate->assign('INPUT_LASTNAME', $account['customers_lastname']);
$osTemplate->assign('INPUT_EMAIL', $account['customers_email_address']);
        }else{
		//-> guest *********************************************************  
$osTemplate->assign('INPUT_FIRSTNAME', os_draw_input_fieldNote(array ('name' => 'firstname', 'text' => '&nbsp;'. (os_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">'.ENTRY_FIRST_NAME_TEXT.'</span>' : ''))));
$osTemplate->assign('INPUT_LASTNAME', os_draw_input_fieldNote(array ('name' => 'lastname', 'text' => '&nbsp;'. (os_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">'.ENTRY_LAST_NAME_TEXT.'</span>' : ''))));
$osTemplate->assign('INPUT_EMAIL', os_draw_input_fieldNote(array ('name' => 'email_address', 'text' => '&nbsp;'. (os_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">'.ENTRY_EMAIL_ADDRESS_TEXT.'</span>' : ''))));
        }
$osTemplate->assign('INPUT_TEXT', os_draw_textarea_field('message_body', 'soft', 50, 15, stripslashes($_POST['message_body'])));
$osTemplate->assign('FORM_END', '</form>');
$osTemplate->assign('BUTTON_SUBMIT', button_continue_submit() );

  	$_array = array('img' => 'button_back.gif', 
	                                'href' => 'javascript:window.close()', 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}
	
$osTemplate->assign('BUTTON_CONTINUE', $_array['code']);
include ('includes/header.php');
// set cache ID
 if (!CacheCheck()) {
	$osTemplate->caching = 0;
	$osTemplate->display(CURRENT_TEMPLATE.'/module/ask_a_question.html');
} else {
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'];
	$osTemplate->display(CURRENT_TEMPLATE.'/module/ask_a_question.html', $cache_id);
	}
}
?>