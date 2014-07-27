<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include ('includes/top.php');

$breadcrumb->add(NAVBAR_TITLE_SPECIALS, os_href_link(FILENAME_SPECIALS));

require (_INCLUDES.'header.php');

$listing_sql = $cartet->product->getList(array(
	'products_status' => 1,
	'where' => array('s.status = 1'),
	'order' => 's.specials_date_added ASC',
));

$specials_split = new splitPageResults($listing_sql, $_GET['page'], MAX_DISPLAY_SPECIAL_PRODUCTS);

$module_content = '';
if (($specials_split->number_of_rows > 0))
{
	$specials_query = os_db_query($specials_split->sql_query);
	while ($specials = os_db_fetch_array($specials_query))
	{
		$module_content[] = $product->buildDataArray($specials);
	}
	$osTemplate->assign('PAGINATION', $specials_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y'))));
}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('module_content', $module_content);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/specials.html');
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_SPECIALS.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_SPECIALS.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);

include ('includes/bottom.php');