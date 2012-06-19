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

  function os_get_topic_path($current_topic_id = '') {
    global $tPath_array;

    if (os_not_null($current_topic_id)) {
      $cp_size = sizeof($tPath_array);
      if ($cp_size == 0) {
        $tPath_new = $current_topic_id;
      } else {
        $tPath_new = '';
        $last_topic_query = osDBquery("select parent_id from " . TABLE_TOPICS . " where topics_id = '" . (int)$tPath_array[($cp_size-1)] . "'");
        $last_topic = os_db_fetch_array($last_topic_query,true);

        $current_topic_query = osDBquery("select parent_id from " . TABLE_TOPICS . " where topics_id = '" . (int)$current_topic_id . "'");
        $current_topic = os_db_fetch_array($current_topic_query,true);

        if ($last_topic['parent_id'] == $current_topic['parent_id']) {
          for ($i=0; $i<($cp_size-1); $i++) {
            $tPath_new .= '_' . $tPath_array[$i];
          }
        } else {
          for ($i=0; $i<$cp_size; $i++) {
            $tPath_new .= '_' . $tPath_array[$i];
          }
        }
        $tPath_new .= '_' . $current_topic_id;

        if (substr($tPath_new, 0, 1) == '_') {
          $tPath_new = substr($tPath_new, 1);
        }
      }
    } else {
      $tPath_new = implode('_', $tPath_array);
    }

    return 'tPath=' . $tPath_new;
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
  function os_get_topics($topics_array = '', $parent_id = '0', $indent = '') {
    global $languages_id;

    if (!is_array($topics_array)) $topics_array = array();

    $topics_query = osDBquery("select t.topics_id, td.topics_name from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where parent_id = '" . (int)$parent_id . "' and t.topics_id = td.topics_id and td.language_id = '" . (int)$_SESSION['languages_id'] . "' order by sort_order, td.topics_name");
    while ($topics = os_db_fetch_array($topics_query,true)) {
      $topics_array[] = array('id' => $topics['topics_id'],
                                  'text' => $indent . $topics['topics_name']);

      if ($topics['topics_id'] != $parent_id) {
        $topics_array = os_get_topics($topics_array, $topics['topics_id'], $indent . '&nbsp;&nbsp;');
      }
    }

    return $topics_array;
  }


  function os_get_authors($authors_array = '') {
    if (!is_array($authors_array)) $authors_array = array();

    $authors_query = osDBquery("select authors_id, authors_name from " . TABLE_AUTHORS . " order by authors_name");
    while ($authors = os_db_fetch_array($authors_query,true)) {
      $authors_array[] = array('id' => $authors['authors_id'], 'text' => $authors['authors_name']);
    }

    return $authors_array;
  }


  function os_get_subtopics(&$subtopics_array, $parent_id = 0) {
    $subtopics_query = osDBquery("select topics_id from " . TABLE_TOPICS . " where parent_id = '" . (int)$parent_id . "'");
    while ($subtopics = os_db_fetch_array($subtopics_query,true)) {
      $subtopics_array[sizeof($subtopics_array)] = $subtopics['topics_id'];
      if ($subtopics['topics_id'] != $parent_id) {
        os_get_subtopics($subtopics_array, $subtopics['topics_id']);
      }
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

  function os_cache_topics_box($auto_expire = false, $refresh = false) {
    global $tPath, $language, $languages_id, $tree, $tPath_array, $topics_string;

    if (($refresh == true) || !read_cache($cache_output, 'topics_box-' . $language . '.cache' . $tPath, $auto_expire)) {
      ob_start();
      include(DIR_WS_BOXES . 'articles.php');
      $cache_output = ob_get_contents();
      ob_end_clean();
      write_cache($cache_output, 'topics_box-' . $language . '.cache' . $tPath);
    }

    return $cache_output;
  }

  function os_cache_authors_box($auto_expire = false, $refresh = false) {
    global $_GET, $language;

    $authors_id = '';
    if (isset($_GET['authors_id']) && os_not_null($_GET['authors_id'])) {
      $authors_id = $_GET['authors_id'];
    }

    if (($refresh == true) || !read_cache($cache_output, 'authors_box-' . $language . '.cache' . $authors_id, $auto_expire)) {
      ob_start();
      include(DIR_WS_BOXES . 'authors.php');
      $cache_output = ob_get_contents();
      ob_end_clean();
      write_cache($cache_output, 'authors_box-' . $language . '.cache' . $authors_id);
    }

    return $cache_output;
  }

?>
