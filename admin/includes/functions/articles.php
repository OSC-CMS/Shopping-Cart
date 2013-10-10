<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

function os_parse_topic_path($tPath) 
{

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

function os_get_topic_name($topic_id, $language_id) 
{
    $topic_query = os_db_query("select topics_name from " . TABLE_TOPICS_DESCRIPTION . " where topics_id = '" . (int)$topic_id . "' and language_id = '" . (int)$language_id . "'");
    $topic = os_db_fetch_array($topic_query);

    return $topic['topics_name'];
}

function os_get_topic_tree($parent_id = '0', $spacing = '', $exclude = '', $topic_tree_array = '', $include_itself = false) 
{
    global $languages_id;

    if (!is_array($topic_tree_array)) $topic_tree_array = array();
    if ( (sizeof($topic_tree_array) < 1) && ($exclude != '0') ) $topic_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    if ($include_itself) {
      $topic_query = os_db_query("select cd.topics_name from " . TABLE_TOPICS_DESCRIPTION . " cd where cd.language_id = '" . (int)$_SESSION['languages_id'] . "' and cd.topics_id = '" . (int)$parent_id . "'");
      $topic = os_db_fetch_array($topic_query);
      $topic_tree_array[] = array('id' => $parent_id, 'text' => $topic['topics_name']);
    }

    $topics_query = os_db_query("select c.topics_id, cd.topics_name, c.parent_id from " . TABLE_TOPICS . " c, " . TABLE_TOPICS_DESCRIPTION . " cd where c.topics_id = cd.topics_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.topics_name");
    while ($topics = os_db_fetch_array($topics_query)) {
      if ($exclude != $topics['topics_id']) $topic_tree_array[] = array('id' => $topics['topics_id'], 'text' => $spacing . $topics['topics_name']);
      $topic_tree_array = os_get_topic_tree($topics['topics_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $topic_tree_array);
    }

    return $topic_tree_array;
}

function os_generate_topic_path($id, $from = 'topic', $topics_array = '', $index = 0) 
{
    global $languages_id;

    if (!is_array($topics_array)) $topics_array = array();

    if ($from == 'article') {
      $topics_query = os_db_query("select topics_id from " . TABLE_ARTICLES_TO_TOPICS . " where articles_id = '" . (int)$id . "'");
      while ($topics = os_db_fetch_array($topics_query)) {
        if ($topics['topics_id'] == '0') {
          $topics_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
        } else {
          $topic_query = os_db_query("select cd.topics_name, c.parent_id from " . TABLE_TOPICS . " c, " . TABLE_TOPICS_DESCRIPTION . " cd where c.topics_id = '" . (int)$topics['topics_id'] . "' and c.topics_id = cd.topics_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
          $topic = os_db_fetch_array($topic_query);
          $topics_array[$index][] = array('id' => $topics['topics_id'], 'text' => $topic['topics_name']);
          if ( (os_not_null($topic['parent_id'])) && ($topic['parent_id'] != '0') ) $topics_array = os_generate_topic_path($topic['parent_id'], 'topic', $topics_array, $index);
          $topics_array[$index] = array_reverse($topics_array[$index]);
        }
        $index++;
      }
    } elseif ($from == 'topic') {
      $topic_query = os_db_query("select cd.topics_name, c.parent_id from " . TABLE_TOPICS . " c, " . TABLE_TOPICS_DESCRIPTION . " cd where c.topics_id = '" . (int)$id . "' and c.topics_id = cd.topics_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
      $topic = os_db_fetch_array($topic_query);
      $topics_array[$index][] = array('id' => $id, 'text' => $topic['topics_name']);
      if ( (os_not_null($topic['parent_id'])) && ($topic['parent_id'] != '0') ) $topics_array = os_generate_topic_path($topic['parent_id'], 'topic', $topics_array, $index);
    }

    return $topics_array;
}

function os_output_generated_topic_path($id, $from = 'topic') 
{
    $calculated_topic_path_string = '';
    $calculated_topic_path = os_generate_topic_path($id, $from);
    for ($i=0, $n=sizeof($calculated_topic_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_topic_path[$i]); $j<$k; $j++) {
        $calculated_topic_path_string .= $calculated_topic_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
      }
      $calculated_topic_path_string = substr($calculated_topic_path_string, 0, -16) . '<br>';
    }
    $calculated_topic_path_string = substr($calculated_topic_path_string, 0, -4);

    if (strlen($calculated_topic_path_string) < 1) $calculated_topic_path_string = TEXT_TOP;

    return $calculated_topic_path_string;
}

function os_get_generated_topic_path_ids($id, $from = 'topic') 
{
    $calculated_topic_path_string = '';
    $calculated_topic_path = os_generate_topic_path($id, $from);
    for ($i=0, $n=sizeof($calculated_topic_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_topic_path[$i]); $j<$k; $j++) {
        $calculated_topic_path_string .= $calculated_topic_path[$i][$j]['id'] . '_';
      }
      $calculated_topic_path_string = substr($calculated_topic_path_string, 0, -1) . '<br>';
    }
    $calculated_topic_path_string = substr($calculated_topic_path_string, 0, -4);

    if (strlen($calculated_topic_path_string) < 1) $calculated_topic_path_string = TEXT_TOP;

    return $calculated_topic_path_string;
}

function os_get_articles_name($article_id, $language_id = 0) 
{
    global $languages_id;

    if ($language_id == 0) $language_id = $_SESSION['languages_id'];
    $article_query = os_db_query("select articles_name from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = os_db_fetch_array($article_query);

    return $article['articles_name'];
}

function os_get_articles_head_title_tag($article_id, $language_id = 0) 
{
    global $languages_id;

    if ($language_id == 0) $language_id = $_SESSION['languages_id'];
    $article_query = os_db_query("select articles_head_title_tag from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = os_db_fetch_array($article_query);

    return $article['articles_head_title_tag'];
}

  function os_get_articles_description($article_id, $language_id) {
    $article_query = os_db_query("select articles_description from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = os_db_fetch_array($article_query);

    return $article['articles_description'];
  }

  function os_get_articles_description_short($article_id, $language_id) {
    $article_query = os_db_query("select articles_description_short from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = os_db_fetch_array($article_query);

    return $article['articles_description_short'];
  }

  function os_get_articles_head_desc_tag($article_id, $language_id) {
    $article_query = os_db_query("select articles_head_desc_tag from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = os_db_fetch_array($article_query);

    return $article['articles_head_desc_tag'];
  }

  function os_get_articles_head_keywords_tag($article_id, $language_id) {
    $article_query = os_db_query("select articles_head_keywords_tag from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = os_db_fetch_array($article_query);

    return $article['articles_head_keywords_tag'];
  }

function os_get_articles_url($article_id, $language_id) 
{
    $article_query = os_db_query("select articles_url from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = os_db_fetch_array($article_query);

    return $article['articles_url'];
}


function os_articles_in_topic_count($topics_id, $include_deactivated = false) 
{
    $articles_count = 0;

    if ($include_deactivated) {
      $articles_query = os_db_query("select count(*) as total from " . TABLE_ARTICLES . " p, " . TABLE_ARTICLES_TO_TOPICS . " p2c where p.articles_id = p2c.articles_id and p2c.topics_id = '" . (int)$topics_id . "'");
    } else {
      $articles_query = os_db_query("select count(*) as total from " . TABLE_ARTICLES . " p, " . TABLE_ARTICLES_TO_TOPICS . " p2c where p.articles_id = p2c.articles_id and p.articles_status = '1' and p2c.topics_id = '" . (int)$topics_id . "'");
    }

    $articles = os_db_fetch_array($articles_query);

    $articles_count += $articles['total'];

    $childs_query = os_db_query("select topics_id from " . TABLE_TOPICS . " where parent_id = '" . (int)$topics_id . "'");
    if (os_db_num_rows($childs_query)) {
      while ($childs = os_db_fetch_array($childs_query)) {
        $articles_count += os_articles_in_topic_count($childs['topics_id'], $include_deactivated);
      }
    }

    return $articles_count;
}


function os_childs_in_topic_count($topics_id) 
{
    $topics_count = 0;

    $topics_query = os_db_query("select topics_id from " . TABLE_TOPICS . " where parent_id = '" . (int)$topics_id . "'");
    while ($topics = os_db_fetch_array($topics_query)) {
      $topics_count++;
      $topics_count += os_childs_in_topic_count($topics['topics_id']);
    }

    return $topics_count;
}

function os_remove_topic($topic_id) 
{
    $topic_image_query = os_db_query("select topics_image from " . TABLE_TOPICS . " where topics_id = '" . (int)$topic_id . "'");
    $topic_image = os_db_fetch_array($topic_image_query);

    $duplicate_image_query = os_db_query("select count(*) as total from " . TABLE_TOPICS . " where topics_image = '" . os_db_input($topic_image['topics_image']) . "'");
    $duplicate_image = os_db_fetch_array($duplicate_image_query);

    if ($duplicate_image['total'] < 2) {
      if (file_exists(dir_path('images') . $topic_image['topics_image'])) {
        @unlink(dir_path('images') . $topic_image['topics_image']);
      }
    }

    os_db_query("delete from " . TABLE_TOPICS . " where topics_id = '" . (int)$topic_id . "'");
    os_db_query("delete from " . TABLE_TOPICS_DESCRIPTION . " where topics_id = '" . (int)$topic_id . "'");
    os_db_query("delete from " . TABLE_ARTICLES_TO_TOPICS . " where topics_id = '" . (int)$topic_id . "'");

    if (USE_CACHE == 'true') {
      os_reset_cache_block('topics');
      os_reset_cache_block('also_purchased');
    }
}

function os_remove_article($article_id) 
{
    os_db_query("delete from " . TABLE_ARTICLES . " where articles_id = '" . (int)$article_id . "'");
    os_db_query("delete from " . TABLE_ARTICLES_TO_TOPICS . " where articles_id = '" . (int)$article_id . "'");
    os_db_query("delete from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "'");

    if (USE_CACHE == 'true') {
      os_reset_cache_block('topics');
      os_reset_cache_block('also_purchased');
    }
}

function os_get_topic_heading_title($topic_id, $language_id) 
{
    $topic_query = os_db_query("select topics_heading_title from " . TABLE_TOPICS_DESCRIPTION . " where topics_id = '" . $topic_id . "' and language_id = '" . $language_id . "'");
    $topic = os_db_fetch_array($topic_query);
    return $topic['topics_heading_title'];
}

function os_get_topic_description($topic_id, $language_id) 
{
    $topic_query = os_db_query("select topics_description from " . TABLE_TOPICS_DESCRIPTION . " where topics_id = '" . $topic_id . "' and language_id = '" . $language_id . "'");
    $topic = os_db_fetch_array($topic_query);
    return $topic['topics_description'];
}
?>
