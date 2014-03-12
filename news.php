<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include( 'includes/top.php');

require(_INCLUDES . 'header.php');

// Если одна новость
if (isset($_GET['news_id']))
{
	$newsDataArray = $cartet->news->newsData;
	$aNews = apply_filter('news_content', $cartet->news->getData($newsDataArray));
	$osTemplate->assign('ONE', true);
}
// Если список новостей
elseif (empty($cartet->news->newsData) && !isset($_GET['news_id']))
{
	// получаем все новости
	$newsDataQuery = $cartet->news->getAll();

	// постраничка
	$split = new splitPageResults($newsDataQuery, $_GET['page'], MAX_DISPLAY_LATEST_NEWS_PAGE, 'news_id');
	$query = os_db_query($split->sql_query);
	
	if (($split->number_of_rows > 0))
	{
		$osTemplate->assign('PAGINATION', $split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y'))));
	}

	// массив новостей
	$newsDataArray = array();
	while($row = os_db_fetch_array($query))
	{
		$newsDataArray[] = $cartet->news->getData($row);
	}

	$aNews = apply_filter('news_content', $newsDataArray);
	$osTemplate->assign('ONE', false);
}

$osTemplate->assign('NEWS_LINK', os_href_link(FILENAME_NEWS));
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$osTemplate->assign('aNews', $aNews);
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE.'/module/latest_news.html');

$osTemplate->assign('main_content',$main_content);
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_NEWS.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_NEWS.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>