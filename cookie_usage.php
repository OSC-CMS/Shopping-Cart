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

$breadcrumb->add(NAVBAR_TITLE_COOKIE_USAGE, os_href_link(FILENAME_COOKIE_USAGE));

require (dir_path('includes').'header.php');

$osTemplate->assign('BUTTON_CONTINUE', button_continue() );
$osTemplate->assign('language', $_SESSION['language']);

// set cache ID
 if (!CacheCheck()) {
	$osTemplate->caching = 0;
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/cookie_usage.html');
} else {
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'];
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/cookie_usage.html', $cache_id);
}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_COOKIE_USAGE.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_COOKIE_USAGE.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>