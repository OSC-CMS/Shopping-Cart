<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require('includes/top.php');
//$osTemplate = new osTemplate;



$breadcrumb->add(NAVBAR_TITLE, os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_DETAILS_OK, os_href_link(FILENAME_AFFILIATE_DETAILS_OK));

require(dir_path('includes') . 'header.php');

$osTemplate->assign('LINK_SUMMARY', button_continue(  os_href_link(FILENAME_AFFILIATE_SUMMARY)  ));

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_details_ok.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');

?>