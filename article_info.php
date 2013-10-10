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

require (_INCLUDES.'header.php');

$article_info = $cartet->articles->articlesData;

if (!empty($cartet->articles->articlesData) && isset($_GET['articles_id']))
{
	$osTemplate->assign('no_article', 'false');

	$aArticles = apply_filter('article_info', $cartet->articles->getData($article_info));

	include (_MODULES.FILENAME_ARTICLES_XSELL);
} 
else 
	$osTemplate->assign('no_article', 'true');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$osTemplate->assign('aArticles', $aArticles);
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/article_info.html');
$osTemplate->assign('main_content', $main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ARTICLE_INFO.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ARTICLE_INFO.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>