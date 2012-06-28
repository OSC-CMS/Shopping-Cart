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

$breadcrumb->add(NAVBAR_TITLE_SSL_CHECK, os_href_link(FILENAME_SSL_CHECK));

require (dir_path('includes').'header.php');

$osTemplate->assign('BUTTON_CONTINUE', button_continue());

$osTemplate->assign('language', $_SESSION['language']);

 if (!CacheCheck()) {
	$osTemplate->caching = 0;
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/ssl_check.html');
} else {
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'];
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/ssl_check.html', $cache_id);
}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_SSL_CHECK.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_SSL_CHECK.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>