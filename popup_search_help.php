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

include ('includes/header.php');

$osTemplate->assign('link_close', 'javascript:window.close()');
$osTemplate->assign('language', $_SESSION['language']);

 if (!CacheCheck()) {
	$osTemplate->caching = 0;
	$osTemplate->display(CURRENT_TEMPLATE.'/module/popup_search_help.html');
} else {
	$osTemplate->caching = 1;
	$osTemplate->cache_lifetime = CACHE_LIFETIME;
	$osTemplate->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'];
	$osTemplate->display(CURRENT_TEMPLATE.'/module/popup_search_help.html', $cache_id);
}
?>