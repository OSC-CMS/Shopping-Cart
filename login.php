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

if (isset($_SESSION['customer_id']))
{
	os_redirect(os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}

if ($session_started == false)
{
	os_redirect(os_href_link(FILENAME_COOKIE_USAGE));
}

if (isset ($_GET['action']) && ($_GET['action'] == 'process'))
{
	$result = $cartet->customer->login($_POST['email_address'], $_POST['password']);
	if ($result['login'] == false)
	{
		if ($_SESSION['cart']->count_contents() > 0)
			os_redirect(os_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
		else
			os_redirect(os_href_link(FILENAME_DEFAULT));
	}
	else
		os_redirect(os_href_link(FILENAME_LOGIN));
}

$breadcrumb->add(NAVBAR_TITLE_LOGIN, os_href_link(FILENAME_LOGIN, '', 'SSL'));
require (dir_path('includes').'header.php');

$osTemplate->assign('account_option', ACCOUNT_OPTIONS);

$osTemplate->assign('BUTTON_NEW_ACCOUNT', button_continue(  os_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL')  ) );

$_array = array('img' => 'button_login.gif', 'href' => '', 'alt' => TEXT_BUTTON_LOGIN, 'code' => '');

$_array = apply_filter('button_login', $_array);	

if (empty($_array['code']))
{
	$_array['code'] = buttonSubmit($_array['img'], null, $_array['alt']);
}

$osTemplate->assign('BUTTON_LOGIN', $_array['code']);

$osTemplate->assign('BUTTON_GUEST', button_continue(  os_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL')  ));

$osTemplate->assign('FORM_ACTION', os_draw_form('login', os_href_link(FILENAME_LOGIN, 'action=process', 'SSL')));

if ($_SESSION['captcha'] == 1)
{
	$osTemplate->assign('CAPTCHA_IMG', '<img src="'.FILENAME_DISPLAY_CAPTCHA.'" alt="captcha" name="captcha" />');    
	$osTemplate->assign('CAPTCHA_INPUT', os_draw_input_field('captcha', '', 'size="6" maxlength="6"', 'text', false));
}

$osTemplate->assign('INPUT_MAIL', os_draw_input_field('email_address'));
$osTemplate->assign('INPUT_PASSWORD', os_draw_password_field('password'));
$osTemplate->assign('LINK_LOST_PASSWORD', os_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL'));
$osTemplate->assign('FORM_END', '</form>');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/login.html');
$osTemplate->assign('main_content', $main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_LOGIN.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_LOGIN.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>