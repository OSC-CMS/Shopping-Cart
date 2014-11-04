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


if (!isset ($_SESSION['customer_id']))
	os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));

if (isset ($_POST['action']) && ($_POST['action'] == 'process')) {
	$password_current = os_db_prepare_input($_POST['password_current']);
	$password_new = os_db_prepare_input($_POST['password_new']);
	$password_confirmation = os_db_prepare_input($_POST['password_confirmation']);

	$error = false;

	if (strlen($password_current) < ENTRY_PASSWORD_MIN_LENGTH) {
		$error = true;

		$messageStack->add('account_password', ENTRY_PASSWORD_CURRENT_ERROR);
	}
	elseif (strlen($password_new) < ENTRY_PASSWORD_MIN_LENGTH) {
		$error = true;

		$messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR);
	}
	elseif ($password_new != $password_confirmation) {
		$error = true;

		$messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING);
	}

	if ($error == false) {
		$check_customer_query = os_db_query("select customers_password from ".TABLE_CUSTOMERS." where customers_id = '".(int) $_SESSION['customer_id']."'");
		$check_customer = os_db_fetch_array($check_customer_query);

		if (os_validate_password($password_current, $check_customer['customers_password'])) {
			os_db_query("UPDATE ".TABLE_CUSTOMERS." SET customers_password = '".os_encrypt_password($password_new)."', customers_last_modified=now() WHERE customers_id = '".(int) $_SESSION['customer_id']."'");

			os_db_query("UPDATE ".TABLE_CUSTOMERS_INFO." SET customers_info_date_account_last_modified = now() WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");

			$messageStack->add_session('account', SUCCESS_PASSWORD_UPDATED, 'success');

			os_redirect(os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
		} else {
			$error = true;

			$messageStack->add('account_password', ERROR_CURRENT_PASSWORD_NOT_MATCHING);
		}
	}
}

$breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_PASSWORD, os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_PASSWORD, os_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL'));

require (dir_path('includes').'header.php');

//if ($messageStack->size('account_password') > 0)
//	$osTemplate->assign('error', $messageStack->output('account_password'));

$osTemplate->assign('FORM_ACTION', os_draw_form('account_password', os_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL'), 'post').os_draw_hidden_field('action', 'process') . os_draw_hidden_field('required', 'password_current,password_new,password_confirmation', 'id="required"'));

$osTemplate->assign('INPUT_ACTUAL', os_draw_password_fieldNote(array ('name' => 'password_current', 'text' => '&nbsp;'. (os_not_null(ENTRY_PASSWORD_CURRENT_TEXT) ? '<span class="Requirement">'.ENTRY_PASSWORD_CURRENT_TEXT.'</span>' : '')), '', 'id="password_current"'));
$osTemplate->assign('ENTRY_PASSWORD_CURRENT_ERROR', ENTRY_PASSWORD_CURRENT_ERROR);
$osTemplate->assign('INPUT_NEW', os_draw_password_fieldNote(array ('name' => 'password_new', 'text' => '&nbsp;'. (os_not_null(ENTRY_PASSWORD_NEW_TEXT) ? '<span class="Requirement">'.ENTRY_PASSWORD_NEW_TEXT.'</span>' : '')), '', 'id="password_new"'));
$osTemplate->assign('ENTRY_PASSWORD_NEW_ERROR', ENTRY_PASSWORD_NEW_ERROR);
$osTemplate->assign('INPUT_CONFIRM', os_draw_password_fieldNote(array ('name' => 'password_confirmation', 'text' => '&nbsp;'. (os_not_null(ENTRY_PASSWORD_CONFIRMATION_TEXT) ? '<span class="Requirement">'.ENTRY_PASSWORD_CONFIRMATION_TEXT.'</span>' : '')), '', 'id="password_confirmation"'));
$osTemplate->assign('ENTRY_PASSWORD_ERROR_NOT_MATCHING', ENTRY_PASSWORD_ERROR_NOT_MATCHING);

  	$_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link(FILENAME_ACCOUNT, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
	}
	
	
$osTemplate->assign('BUTTON_BACK', $_array['code']);

$osTemplate->assign('BUTTON_SUBMIT', button_continue_submit());
$osTemplate->assign('FORM_END', '</form>');

$osTemplate->assign('language', $_SESSION['language']);

$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/account_password.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ACCOUNT_PASSWORD.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ACCOUNT_PASSWORD.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>