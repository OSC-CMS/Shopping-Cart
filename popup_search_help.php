<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
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