<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

  include( 'includes/top.php');

  require(_INCLUDES . 'header.php');

  $_GET['news_id'] = (int)$_GET['news_id']; if ($_GET['news_id']<1) $_GET['news_id'] = 0;

  $all_sql = "
      SELECT
          news_id,
          headline,
          content,
          date_added
      FROM " . TABLE_LATEST_NEWS . "
      WHERE
          status = '1'
          and language = '" . (int)$_SESSION['languages_id'] . "'
      ORDER BY date_added DESC
      ";
  $one_sql = "
      SELECT
          news_id,
          headline,
          content,
          date_added
      FROM " . TABLE_LATEST_NEWS . "
      WHERE
          status = '1'
          and language = '" . (int)$_SESSION['languages_id'] . "'
          and news_id = " . $_GET['news_id'] . "
      ORDER BY date_added DESC
      LIMIT 1
      ";

  $module_content = array();
  if (!empty($_GET['news_id'])) {
      $query = os_db_query($one_sql);
      if (os_db_num_rows($query) == 0) $_GET['news_id'] = 0;
  }
  if (empty($_GET['news_id'])) 
  {
      $split = new splitPageResults($all_sql, $_GET['page'], MAX_DISPLAY_LATEST_NEWS_PAGE, 'news_id');
      $query = os_db_query($split->sql_query);
      if (($split->number_of_rows > 0)) {
          $osTemplate->assign('NAVIGATION_BAR', TEXT_RESULT_PAGE.' '.$split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y'))));
          $osTemplate->assign('NAVIGATION_BAR_PAGES', $split->display_count(TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS));
      }
      $osTemplate->assign('ONE', false);
  } 
  else 
  {
      $osTemplate->assign('ONE', true);
  }

  if (os_db_num_rows($query) > 0) {
      while ($one = os_db_fetch_array($query)) {

		$SEF_parameter = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter = '&headline='.os_cleanName($one['headline']);

		  $module_content_tmp = array(
              'NEWS_HEADING' => $one['headline'],
              'NEWS_CONTENT' => $_GET['news_id']==0 ? strip_tags($one['content']):$one['content'],
              'NEWS_ID'      => $one['news_id'],
              'NEWS_DATA'    => os_date_short($one['date_added']),
              'NEWS_LINK_MORE'    => os_href_link(FILENAME_NEWS, 'news_id='.$one['news_id'] . $SEF_parameter, 'NONSSL'),
              );
			  

		  $module_content_tmp = apply_filter ('news_content', $module_content_tmp);
		  $module_content[] =  $module_content_tmp;
      }
  } else {
      $osTemplate->assign('NAVIGATION_BAR', TEXT_NO_NEWS);
  }

  $osTemplate->assign('NEWS_LINK', os_href_link(FILENAME_NEWS));
  $osTemplate->assign('language', $_SESSION['language']);
  $osTemplate->caching = 0;
  $osTemplate->assign('module_content',$module_content);
  $main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/latest_news.html');

  $osTemplate->assign('main_content',$main_content);
  $osTemplate->assign('language', $_SESSION['language']);
  $osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_NEWS.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_NEWS.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
  include ('includes/bottom.php');
?>