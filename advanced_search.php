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


$breadcrumb->add(NAVBAR_TITLE_ADVANCED_SEARCH, os_href_link(FILENAME_ADVANCED_SEARCH));

require (dir_path('includes').'header.php');

$osTemplate->assign('FORM_ACTION', os_draw_form('advanced_search', os_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get', 'onsubmit="return check_form(this);"').os_hide_session_id());

$osTemplate->assign('INPUT_KEYWORDS', os_draw_input_field('keywords', '', ''));
$osTemplate->assign('HELP_LINK', 'javascript:popupWindow(\''.os_href_link(FILENAME_POPUP_SEARCH_HELP).'\')');

   $_array = array('img' => 'button_search.gif', 'href' => '', 'alt' => IMAGE_BUTTON_SEARCH,'code' => '');
	
	$_array = apply_filter('button_search', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], null, $_array['alt']);
	}
	
   $osTemplate->assign('BUTTON_SUBMIT', $_array['code']);


$osTemplate->assign('SELECT_CATEGORIES',os_draw_pull_down_menu('categories_id', os_get_categories(array (array ('id' => '', 'text' => TEXT_ALL_CATEGORIES)))));
$osTemplate->assign('ENTRY_SUBCAT',os_draw_checkbox_field('inc_subcat', '1', true));
$osTemplate->assign('SELECT_MANUFACTURERS',os_draw_pull_down_menu('manufacturers_id', os_get_manufacturers(array (array ('id' => '', 'text' => TEXT_ALL_MANUFACTURERS)))));
$osTemplate->assign('SELECT_PFROM',os_draw_input_field('pfrom'));
$osTemplate->assign('SELECT_PTO',os_draw_input_field('pto'));


$error = '';
if (isset ($_GET['errorno'])) {
	if (($_GET['errorno'] & 1) == 1) {
		$error .= str_replace('\n', '<br />', JS_AT_LEAST_ONE_INPUT);
	}
	if (($_GET['errorno'] & 10) == 10) {
		$error .= str_replace('\n', '<br />', JS_INVALID_FROM_DATE);
	}
	if (($_GET['errorno'] & 100) == 100) {
		$error .= str_replace('\n', '<br />', JS_INVALID_TO_DATE);
	}
	if (($_GET['errorno'] & 1000) == 1000) {
		$error .= str_replace('\n', '<br />', JS_TO_DATE_LESS_THAN_FROM_DATE);
	}
	if (($_GET['errorno'] & 10000) == 10000) {
		$error .= str_replace('\n', '<br />', JS_PRICE_FROM_MUST_BE_NUM);
	}
	if (($_GET['errorno'] & 100000) == 100000) {
		$error .= str_replace('\n', '<br />', JS_PRICE_TO_MUST_BE_NUM);
	}
	if (($_GET['errorno'] & 1000000) == 1000000) {
		$error .= str_replace('\n', '<br />', JS_PRICE_TO_LESS_THAN_PRICE_FROM);
	}
	if (($_GET['errorno'] & 10000000) == 10000000) {
		$error .= str_replace('\n', '<br />', JS_INVALID_KEYWORDS);
	}
}

$osTemplate->assign('error', $error);
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('FORM_END', '</form>');

$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/advanced_search.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ADVANCED_SEARCH.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ADVANCED_SEARCH.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>