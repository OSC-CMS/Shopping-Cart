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

$_GET['faq_id'] = (int)$_GET['faq_id']; if ($_GET['faq_id']<1) $_GET['faq_id'] = 0;

  $all_sql = "
      SELECT
          faq_id,
          question,
          answer,
          date_added
      FROM " . TABLE_FAQ . "
      WHERE
          status = '1'
          and language = '" . (int)$_SESSION['languages_id'] . "'
      ORDER BY date_added DESC
      ";
  if ($_GET['akeywords'] != ""){
  //TODO: faq - akeywords для поиска?
  $_GET['akeywords'] = urldecode($_GET['akeywords']);
  
    $all_sql = "SELECT
          faq_id,
          question,
          answer,
          date_added
      FROM " . TABLE_FAQ . "
      WHERE status = '1' and language = '" . (int)$_SESSION['languages_id'] . "' and (question like '%" . $_GET['akeywords'] . "%' or answer like '%" . $_GET['akeywords'] . "%') order by date_added DESC";

 }      
  $one_sql = "
      SELECT
          faq_id,
          question,
          answer,
          date_added
      FROM " . TABLE_FAQ . "
      WHERE
          status = '1'
          and language = '" . (int)$_SESSION['languages_id'] . "'
          and faq_id = " . $_GET['faq_id'] . "
      ORDER BY date_added DESC
      LIMIT 1
      ";

$breadcrumb->add(NAVBAR_TITLE_FAQ, os_href_link(FILENAME_FAQ));

require(dir_path('includes') . 'header.php');

  $module_content = array();
  if (!empty($_GET['faq_id'])) {
      $query = os_db_query($one_sql);
      if (os_db_num_rows($query) == 0) $_GET['faq_id'] = 0;
  }
  if (empty($_GET['faq_id'])) {
      $split = new splitPageResults($all_sql, $_GET['page'], MAX_DISPLAY_FAQ_PAGE, 'faq_id');
      $query = os_db_query($split->sql_query);
      if (($split->number_of_rows > 0)) {
          $osTemplate->assign('PAGINATION', $split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array ('page', 'info', 'x', 'y'))));
      }
      $osTemplate->assign('ONE', false);
  } else {
      $osTemplate->assign('ONE', true);
  }

  if (os_db_num_rows($query) > 0) {
      while ($one = os_db_fetch_array($query)) {

		$SEF_parameter = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter = '&question='.os_cleanName($one['question']);

          $module_content[]=array(
              'FAQ_QUESTION' => $one['question'],
              'FAQ_ANSWER' => $one['answer'],
              'FAQ_ID'      => $one['faq_id'],
              'FAQ_DATA'    => os_date_short($one['date_added']),
              'FAQ_LINK_MORE'    => os_href_link(FILENAME_FAQ, 'faq_id='.$one['faq_id'] . $SEF_parameter, 'NONSSL'),
              );
      }
  }

  $osTemplate->assign('FAQ_LINK', os_href_link(FILENAME_FAQ));
  $osTemplate->assign('language', $_SESSION['language']);
  $osTemplate->caching = 0;
  $osTemplate->assign('module_content',$module_content);
  $main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/faq.html');

  $osTemplate->assign('main_content',$main_content);
  $osTemplate->assign('language', $_SESSION['language']);
  $osTemplate->caching = 0;
  
      $osTemplate->loadFilter('output', 'trimhitespace');
  $osTemplate->display(CURRENT_TEMPLATE.'/index.html');
  include ('includes/bottom.php');
?>