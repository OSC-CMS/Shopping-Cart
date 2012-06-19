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

require (_INCLUDES.'header.php');

  $article_check_query = "select count(*) as total from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_DESCRIPTION . " ad where a.articles_status = '1' and a.articles_id = '" . (int)$_GET['articles_id'] . "' and ad.articles_id = a.articles_id and ad.language_id = '" . (int)$_SESSION['languages_id'] . "'";
  $article_check_query = osDBquery($article_check_query);
  $article_check = os_db_fetch_array($article_check_query, true);

    $article_info_query = "select a.articles_id, a.articles_date_added, a.articles_date_available, a.authors_id, ad.articles_name, ad.articles_description, ad.articles_url, ad.articles_viewed, au.authors_name from " . TABLE_ARTICLES . " a left join " . TABLE_AUTHORS . " au on a.authors_id = au.authors_id, " . TABLE_ARTICLES_DESCRIPTION . " ad where a.articles_status = '1' and a.articles_id = '" . (int)$_GET['articles_id'] . "' and ad.articles_id = a.articles_id and ad.language_id = '" . (int)$_SESSION['languages_id'] . "'";
    $article_info_query = osDBquery($article_info_query);
    $article_info = os_db_fetch_array($article_info_query, true);

    osDBquery("update " . TABLE_ARTICLES_DESCRIPTION . " set articles_viewed = articles_viewed+1 where articles_id = '" . (int)$_GET['articles_id'] . "' and language_id = '" . (int)$_SESSION['languages_id'] . "'");

if ($article_check['total'] > 0) {

	$osTemplate->assign('no_article', 'false');

		$SEF_parameter_author = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter_author = '&author='.os_cleanName($article_info['authors_name']);
    
	$_article_info = array(
              'ARTICLE_NAME' => $article_info['articles_name'],
              'ARTICLE_DESCRIPTION' => $article_info['articles_description'],
              'ARTICLE_VIEWED'      => $article_info['articles_viewed'],
              'ARTICLE_DATE'    => os_date_long($article_info['articles_date_added']),
              'ARTICLE_URL'    => $article_info['articles_url'],
              'AUTHOR_NAME'    => $article_info['authors_name'],
              'AUTHOR_LINK'    => os_href_link(FILENAME_ARTICLES, 'authors_id=' . $article_info['authors_id'] . $SEF_parameter_author),
              'ARTICLE_PAGE_URL'    => os_href_link(FILENAME_ARTICLE_INFO, 'articles_id=' . $_GET['articles_id'])
              );
	
    $_article_info = apply_filter ('article_info', $_article_info);
	
	$osTemplate->assign('ARTICLE_NAME', $_article_info['ARTICLE_NAME']);
	$osTemplate->assign('ARTICLE_DESCRIPTION', $_article_info['ARTICLE_DESCRIPTION']);
	$osTemplate->assign('ARTICLE_VIEWED', $_article_info['ARTICLE_VIEWED']);
	$osTemplate->assign('ARTICLE_DATE', $_article_info['ARTICLE_DATE']);
	$osTemplate->assign('ARTICLE_URL', $_article_info['ARTICLE_URL']);
	$osTemplate->assign('AUTHOR_NAME', $_article_info['AUTHOR_NAME']);
	$osTemplate->assign('AUTHOR_LINK' , $_article_info['AUTHOR_LINK']);
	$osTemplate->assign('ARTICLE_PAGE_URL' , $_article_info['ARTICLE_PAGE_URL']);

	
include (_MODULES.FILENAME_ARTICLES_XSELL);

} 
else 
{
	$osTemplate->assign('no_article', 'true');
}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$osTemplate->assign('module_content', $module_content);
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/article_info.html');
$osTemplate->assign('main_content', $main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ARTICLE_INFO.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ARTICLE_INFO.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>