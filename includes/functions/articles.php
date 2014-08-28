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

  function os_parse_topic_path($tPath) {
    $tPath_array = array_map('os_string_to_int', explode('_', $tPath));

    $tmp_array = array();
    $n = sizeof($tPath_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($tPath_array[$i], $tmp_array)) {
        $tmp_array[] = $tPath_array[$i];
      }
    }

    return $tmp_array;
  }

  function os_count_articles_in_topic($topic_id, $include_inactive = false) {
    $articles_count = 0;
	
    if ($include_inactive == true) {
      $articles_query = osDBquery("select count(*) as total from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_TO_TOPICS . " a2t where (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now())) and a.articles_id = a2t.articles_id and a2t.topics_id = '" . (int)$topic_id . "'");
	  
	 
    } else {
      $articles_query = osDBquery("select count(*) as total from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_TO_TOPICS . " a2t where (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now())) and a.articles_id = a2t.articles_id and a.articles_status = '1' and a2t.topics_id = '" . (int)$topic_id . "'");
    }
    $articles = os_db_fetch_array($articles_query,true);
    $articles_count += $articles['total'];
    global $topics_url_cache;
	if (!empty($topics_url_cache))
	{
	
    $child_topics_query = osDBquery("select topics_id from " . TABLE_TOPICS . " where parent_id = '" . (int)$topic_id . "'");
    if (os_db_num_rows($child_topics_query,true)) 
	{
      while ($child_topics = os_db_fetch_array($child_topics_query,true)) 
	  {
        $articles_count += os_count_articles_in_topic($child_topics['topics_id'], $include_inactive);
      }
    }
	}

    return $articles_count;
  }

  function os_has_topic_subtopics($topic_id) {
    $child_topic_query = osDBquery("select count(*) as count from " . TABLE_TOPICS . " where parent_id = '" . (int)$topic_id . "'");
    $child_topic = os_db_fetch_array($child_topic_query,true);

    if ($child_topic['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }

  function os_get_parent_topics(&$topics, $topics_id) {
    $parent_topics_query = osDBquery("select parent_id from " . TABLE_TOPICS . " where topics_id = '" . (int)$topics_id . "'");
    while ($parent_topics = os_db_fetch_array($parent_topics_query,true)) {
      if ($parent_topics['parent_id'] == 0) return true;
      $topics[sizeof($topics)] = $parent_topics['parent_id'];
      if ($parent_topics['parent_id'] != $topics_id) {
        os_get_parent_topics($topics, $parent_topics['parent_id']);
      }
    }
  }

  function os_get_article_path($articles_id) {
    $tPath = '';

    $topic_query = osDBquery("select a2t.topics_id from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_TO_TOPICS . " a2t where a.articles_id = '" . (int)$articles_id . "' and a.articles_status = '1' and a.articles_id = a2t.articles_id limit 1");
    if (os_db_num_rows($topic_query,true)) {
      $topic = os_db_fetch_array($topic_query,true);

      $topics = array();
      os_get_parent_topics($topics, $topic['topics_id']);

      $topics = array_reverse($topics);

      $tPath = implode('_', $topics);

      if (os_not_null($tPath)) $tPath .= '_';
      $tPath .= $topic['topics_id'];
    }

    return $tPath;
  }

  function os_get_articles_name($article_id, $language = '') {
    global $languages_id;

    if (empty($language)) $language = $_SESSION['languages_id'];

    $article_query = osDBquery("select articles_name from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language . "'");
    $article = os_db_fetch_array($article_query,true);

    return $article['articles_name'];
  }

?>
