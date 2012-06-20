<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

include ('includes/top.php');
//$osTemplate = new osTemplate;

$breadcrumb->add(BOX_NEW_ARTICLES, os_href_link(FILENAME_ARTICLES_NEW));

require (dir_path('includes').'header.php');

  $articles_new_array = array();
  $articles_new_query_raw = "select a.articles_id, a.sort_order, a.articles_date_added, ad.articles_name, ad.articles_head_desc_tag, au.authors_id, au.authors_name, td.topics_id, td.topics_name from " . TABLE_ARTICLES . " a left join " . TABLE_AUTHORS . " au on a.authors_id = au.authors_id, " . TABLE_ARTICLES_TO_TOPICS . " a2t left join " . TABLE_TOPICS_DESCRIPTION . " td on a2t.topics_id = td.topics_id, " . TABLE_ARTICLES_DESCRIPTION . " ad where (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now())) and a.articles_id = a2t.articles_id and a.articles_status = '1' and a.articles_id = ad.articles_id and ad.language_id = '" . (int) $_SESSION['languages_id'] . "' and td.language_id = '" . (int) $_SESSION['languages_id'] . "' and a.articles_date_added > SUBDATE(now( ), INTERVAL '" . NEW_ARTICLES_DAYS_DISPLAY . "' DAY) order by a.articles_date_added";

$articles_new_split = new splitPageResults($articles_new_query_raw, $_GET['page'], MAX_NEW_ARTICLES_PER_PAGE);

if (($articles_new_split->number_of_rows > 0)) {
	$osTemplate->assign('NAVIGATION_BAR', TEXT_RESULT_PAGE.' '.$articles_new_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y'))));
	$osTemplate->assign('NAVIGATION_BAR_PAGES', $articles_new_split->display_count(TEXT_DISPLAY_NUMBER_OF_ARTICLES_NEW));

}

$module_content = '';
if ($articles_new_split->number_of_rows > 0) {

	$osTemplate->assign('no_new_articles', 'false');

	$articles_new_query = os_db_query($articles_new_split->sql_query);
	while ($articles_new = os_db_fetch_array($articles_new_query)) {

		$SEF_parameter = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter = '&article='.os_cleanName($articles_new['articles_name']);

		$SEF_parameter_author = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter_author = '&author='.os_cleanName($articles_new['authors_name']);

		$SEF_parameter_category = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter_category = '&category='.os_cleanName($articles_new['topics_name']);

		$module_content[] = array (
		
		'ARTICLE_NAME' => $articles_new['articles_name'],
		'ARTICLE_SHORT_DESCRIPTION' => $articles_new['articles_head_desc_tag'], 
		'ARTICLE_DATE' => os_date_long($articles_new['articles_date_added']), 
		'ARTICLE_LINK' => os_href_link(FILENAME_ARTICLE_INFO, 'articles_id=' . $articles_new['articles_id'] . $SEF_parameter), 
		'AUTHOR_NAME' => $articles_new['authors_name'], 
		'AUTHOR_LINK' =>  os_href_link(FILENAME_ARTICLES, 'authors_id=' . $articles_new['authors_id'] . $SEF_parameter_author), 
		'ARTICLE_CATEGORY_NAME' => $articles_new['topics_name'],
		'ARTICLE_CATEGORY_LINK' => os_href_link(FILENAME_ARTICLES, 'tPath=' . $articles_new['topics_id'] . $SEF_parameter_category)
		
		);

	}
} else {

	$osTemplate->assign('no_new_articles', 'true');

}
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$osTemplate->assign('module_content', $module_content);
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/articles_new.html');
$osTemplate->assign('main_content', $main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ARTICLES_NEW.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ARTICLES_NEW.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>