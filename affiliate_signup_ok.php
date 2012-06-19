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



$breadcrumb->add(NAVBAR_TITLE, os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_SIGNUP_OK);

require(_INCLUDES . 'header.php');

$osTemplate->assign('LINK_SUMMARY', button_continue(  os_href_link(FILENAME_AFFILIATE_SUMMARY, '', 'SSL')  ) );

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_signup_ok.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');
?>