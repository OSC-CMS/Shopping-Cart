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

require('includes/top.php');

//$osTemplate = new osTemplate;

$osTemplate->assign(array(
			'HTML_PARAMS' => HTML_PARAMS,
			'HREF' => (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG,
			'TITLE' => TITLE));

$osTemplate->assign('help_file', 'help8');
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;

$osTemplate->display(CURRENT_TEMPLATE . '/module/affiliate_help.html');

?>