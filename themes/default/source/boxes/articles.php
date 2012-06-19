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

$box = new osTemplate;
$content_string = '';

$box->assign('language', $_SESSION['language']);
// set cache ID
if (!CacheCheck()) {
	$cache=false;
	$box->caching = 0;
} else {
	$cache=true;
	$box->caching = 1;
	$box->cache_lifetime = CACHE_LIFETIME;
	$box->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].$tPath;
}

if (!$box->is_cached(CURRENT_TEMPLATE.'/boxes/box_articles.html', @$cache_id) || !$cache) {

	$box->assign('tpl_path', _HTTP_THEMES_C);

  function os_show_topic($counter) {
    global $tree, $topics_string, $tPath_array;

    for ($i=0; $i<$tree[$counter]['level']; $i++) {
      $topics_string .= "&nbsp;&nbsp;";
    }

    $topics_string .= '<a href="';

//    if ($tree[$counter]['parent'] == 0) {
      $tPath_new = 'tPath=' . $counter;
//    } else {
//      $tPath_new = 'tPath=' . $tree[$counter]['path'];
//    }

		$SEF_parameter_cat = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter_cat = '&category='.os_cleanName($tree[$counter]['name']);

    $topics_string .= os_href_link(FILENAME_ARTICLES, $tPath_new . $SEF_parameter_cat) . '">';

    if (isset($tPath_array) && in_array($counter, $tPath_array)) {
      $topics_string .= '<b>';
    }

// display topic name
    $topics_string .= $tree[$counter]['name'];

    if (isset($tPath_array) && in_array($counter, $tPath_array)) {
      $topics_string .= '</b>';
    }

    if (os_has_topic_subtopics($counter)) {
      $topics_string .= ' -&gt;';
    }

    $topics_string .= '</a>';

    if (SHOW_ARTICLE_COUNTS == 'true') {
      $articles_in_topic = os_count_articles_in_topic($counter);
      if ($articles_in_topic > 0) {
        $topics_string .= '&nbsp;(' . $articles_in_topic . ')';
      }
    }

    $topics_string .= '<br />' . "\n";

    if ($tree[$counter]['next_id'] != false) {
      os_show_topic($tree[$counter]['next_id']);
    }
  }
?>
<?php


  $topics_string = '';
  $tree = array();

  $topics_query = "select t.topics_id, td.topics_name, t.parent_id from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.parent_id = '0' and t.topics_id = td.topics_id and td.language_id = '" . (int)$_SESSION['languages_id'] . "' order by sort_order, td.topics_name";
  $topics_query = osDBquery($topics_query);
  while ($topics = os_db_fetch_array($topics_query,true))  {
    $tree[$topics['topics_id']] = array('name' => $topics['topics_name'],
                                        'parent' => $topics['parent_id'],
                                        'level' => 0,
                                        'path' => $topics['topics_id'],
                                        'next_id' => false);

    if (isset($parent_id)) {
      $tree[$parent_id]['next_id'] = $topics['topics_id'];
    }

    $parent_id = $topics['topics_id'];

    if (!isset($first_topic_element)) {
      $first_topic_element = $topics['topics_id'];
    }
  }

  //------------------------
  if (os_not_null($tPath)) {
    $new_path = '';
    reset($tPath_array);
    while (list($key, $value) = each($tPath_array)) {
      unset($parent_id);
      unset($first_id);
      $topics_query = "select t.topics_id, td.topics_name, t.parent_id from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.parent_id = '" . (int)$value . "' and t.topics_id = td.topics_id and td.language_id = '" . (int)$_SESSION['languages_id'] . "' order by sort_order, td.topics_name";
      $topics_query = osDBquery($topics_query);
      if (os_db_num_rows($topics_query,true)) {
        $new_path .= $value;
        while ($row = os_db_fetch_array($topics_query,true)) {
          $tree[$row['topics_id']] = array('name' => $row['topics_name'],
                                           'parent' => $row['parent_id'],
                                           'level' => $key+1,
                                           'path' => $new_path . '_' . $row['topics_id'],
                                           'next_id' => false);

          if (isset($parent_id)) {
            $tree[$parent_id]['next_id'] = $row['topics_id'];
          }

          $parent_id = $row['topics_id'];

          if (!isset($first_id)) {
            $first_id = $row['topics_id'];
          }

          $last_id = $row['topics_id'];
        }
        $tree[$last_id]['next_id'] = $tree[$value]['next_id'];
        $tree[$value]['next_id'] = $first_id;
        $new_path .= '_';
      } else {
        break;
      }
    }
  }
  os_show_topic($first_topic_element);

  $new_articles_string = '';
  $all_articles_string = '';

  if (DISPLAY_NEW_ARTICLES=='true') {
    if (SHOW_ARTICLE_COUNTS == 'true') {
      $articles_new_query = "select a.articles_id from " . TABLE_ARTICLES . " a left join " . TABLE_AUTHORS . " au on a.authors_id = au.authors_id, " . TABLE_ARTICLES_TO_TOPICS . " a2t left join " . TABLE_TOPICS_DESCRIPTION . " td on a2t.topics_id = td.topics_id, " . TABLE_ARTICLES_DESCRIPTION . " ad where (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now())) and a.articles_id = a2t.articles_id and a.articles_status = '1' and a.articles_id = ad.articles_id and ad.language_id = '" . (int)$_SESSION['languages_id'] . "' and td.language_id = '" . (int)$_SESSION['languages_id'] . "' and a.articles_date_added > SUBDATE(now( ), INTERVAL '" . NEW_ARTICLES_DAYS_DISPLAY . "' DAY)";
     $articles_new_query = osDBquery($articles_new_query);
      $articles_new_count = ' (' . os_db_num_rows($articles_new_query,true) . ')';
    } else {
      $articles_new_count = '';
    }

    if (strstr($PHP_SELF,FILENAME_ARTICLES_NEW) or strstr($PHP_SELF,FILENAME_ARTICLES_NEW)) {
      $new_articles_string = '<b>';
    }

    $new_articles_string .= '<a href="' . os_href_link(FILENAME_ARTICLES_NEW, '', 'NONSSL') . '">' . BOX_NEW_ARTICLES . '</a>';

    if (strstr($PHP_SELF,FILENAME_ARTICLES_NEW) or strstr($PHP_SELF,FILENAME_ARTICLES_NEW)) {
      $new_articles_string .= '</b>';
    }

    $new_articles_string .= $articles_new_count . '<br />' . "\n";

  }

  if (DISPLAY_ALL_ARTICLES=='true') {
    if (SHOW_ARTICLE_COUNTS == 'true') {
      $articles_all_query = "select a.articles_id from " . TABLE_ARTICLES . " a left join " . TABLE_AUTHORS . " au on a.authors_id = au.authors_id, " . TABLE_ARTICLES_TO_TOPICS . " a2t left join " . TABLE_TOPICS_DESCRIPTION . " td on a2t.topics_id = td.topics_id, " . TABLE_ARTICLES_DESCRIPTION . " ad where (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now())) and a.articles_id = a2t.articles_id and a.articles_status = '1' and a.articles_id = ad.articles_id and ad.language_id = '" . (int)$_SESSION['languages_id'] . "' and td.language_id = '" . (int)$_SESSION['languages_id'] . "'";
     $articles_all_query = osDBquery($articles_all_query);
      $articles_all_count = ' (' . os_db_num_rows($articles_all_query,true) . ')';
    } else {
      $articles_all_count = '';
    }

    if (isset($topic_depth) && $topic_depth == 'top') 
	{
      $all_articles_string = '<b>';
    }

    $all_articles_string .= '<a href="' . os_href_link(FILENAME_ARTICLES, '', 'NONSSL') . '">' . BOX_ALL_ARTICLES . '</a>';

    if (isset($topic_depth) &&$topic_depth == 'top') {
      $all_articles_string .= '</b>';
    }

    $all_articles_string .= $articles_all_count . '<br />' . "\n";

  }


  $box_content = $new_articles_string . $all_articles_string . $topics_string;

    $box->assign('BOX_CONTENT', $box_content);

}

if (!$cache) 
{
	$box_articles = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_articles.html');
} 
else 
{
	$box_articles = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_articles.html', $cache_id);
}

    $osTemplate->assign('box_ARTICLES',$box_articles);

?>