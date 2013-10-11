<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$cart_empty = false;
require ("includes/top.php");

$breadcrumb->add(NAVBAR_TITLE_SHOPPING_CART, os_href_link(FILENAME_SHOPPING_CART));

require (_INCLUDES.'header.php');

require (DIR_WS_MODULES.'order_details_cart.php');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_SHOPPING_CART.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_SHOPPING_CART.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>