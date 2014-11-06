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

require ("includes/top.php");
$cart_empty = false;

$breadcrumb->add(NAVBAR_TITLE_SHOPPING_CART, os_href_link(FILENAME_SHOPPING_CART));

if (isset($_GET['info_message']) && !empty($_GET['info_message']))
	$messageStack->add('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));

require (_INCLUDES.'header.php');

require (DIR_WS_MODULES.'order_details_cart.php');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_SHOPPING_CART.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_SHOPPING_CART.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>