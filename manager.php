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

   include("includes/functions/include.php");
   include("includes/functions/admin.include.php");
   
  $URI_elements = explode("?", ltrim($_SERVER['REQUEST_URI'], '/'));

  $requests = array();
  if (isset($URI_elements[1]) && (strlen($URI_elements[1]) > 0)) {
    $requests = explode("&", $URI_elements[1]);
  }

  if (sizeof($requests) > 0) {
    for ($i = 0, $n = sizeof($requests); $i < $n; $i++) {
      $param = explode("=", $requests[$i]);
      $_GET[$param[0]] = $param[1];
    } 
  }

  if (isset($URI_elements[0]) && (strlen($URI_elements[0]) > 0)) {

    require_once('config.php');
    require_once('includes/database.php');
    $categories_array = array();

    $path_elements = explode("/", $URI_elements[0]);
    $URI_elements[0] = $path_elements[sizeof($path_elements) - 1];
    
    $db_l = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
    mysql_select_db(DB_DATABASE);

    $query = 'select categories_id from ' . TABLE_CATEGORIES . ' where categories_url="' . os_db_prepare_input($URI_elements[0]) . '"';
    $result = mysql_query($query);
    if (mysql_num_rows($result) > 0) {
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      $cId = $row['categories_id'];
      $matched = true;
    } else {
      $matched = false;
    }

    if ($matched) {
      $HTTP_GET_VARS['cat'] = $cId;
      $_GET['cat'] = $cId;

      mysql_free_result($result);
      mysql_close();
      $PHP_SELF = '/index.php';
      include('index.php');
    } else {
      mysql_free_result($result);
      $query = 'select products_id from ' . TABLE_PRODUCTS . ' where products_page_url="' . os_db_prepare_input($URI_elements[0]) . '"';
      $result = mysql_query($query);
      if (mysql_num_rows($result) > 0) {
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $pId = $row['products_id'];
        $matched = true;
      } else {
        $matched = false;
      }
      if ($matched) {
        $HTTP_GET_VARS['products_id']  = $pId;
        $_GET['products_id']  = $pId;
        
        mysql_free_result($result);
        mysql_close();
        $PHP_SELF = '/product_info.php';
        include('product_info.php');
      } else {
        mysql_free_result($result);
        $query = 'select content_id from ' . TABLE_CONTENT_MANAGER . ' where content_page_url="' . os_db_prepare_input($URI_elements[0]) . '"';
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
          $row = mysql_fetch_array($result, MYSQL_ASSOC);
          $coID = $row['content_id'];
          $matched = true;
        } else {
          $matched = false;
        }
        if ($matched) 
		{
          $HTTP_GET_VARS['coID']  = $coID;
          $_GET['coID']  = $coID;
          mysql_free_result($result);
          mysql_close();
          $PHP_SELF = '/shop_content.php';
          include('shop_content.php');
        } 
		else {
        
        mysql_free_result($result);
        $query = 'select articles_id from ' . TABLE_ARTICLES . ' where articles_page_url="' . os_db_prepare_input($URI_elements[0]) . '"';
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
          $row = mysql_fetch_array($result, MYSQL_ASSOC);
          $aID = $row['articles_id'];
          $matched = true;
        } else {
          $matched = false;
        }
        if ($matched) {
          $HTTP_GET_VARS['articles_id']  = $aID;
          $_GET['articles_id']  = $aID;
          mysql_free_result($result);
          mysql_close();
          $PHP_SELF = '/article_info.php';
          include('article_info.php');
        } else {
        

        mysql_free_result($result);
        $query = 'select topics_id from ' . TABLE_TOPICS . ' where topics_page_url="' . os_db_prepare_input($URI_elements[0]) . '"';
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
          $row = mysql_fetch_array($result, MYSQL_ASSOC);
          $tID = $row['topics_id'];
          $matched = true;
        } else {
          $matched = false;
        }
        if ($matched) {
          $HTTP_GET_VARS['tPath']  = $tID;
          $_GET['tPath']  = $tID;
          mysql_free_result($result);
          mysql_close();
          $PHP_SELF = '/articles.php';
          include('articles.php');
        } else {

        mysql_free_result($result);
        $query = 'select news_id from ' . TABLE_LATEST_NEWS . ' where news_page_url="' . os_db_prepare_input($URI_elements[0]) . '"';
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
          $row = mysql_fetch_array($result, MYSQL_ASSOC);
          $nID = $row['news_id'];
          $matched = true;
        } else {
          $matched = false;
        }
        if ($matched) {
          $HTTP_GET_VARS['news_id']  = $nID;
          $_GET['news_id']  = $nID;
          mysql_free_result($result);
          mysql_close();
          $PHP_SELF = '/news.php';
          include('news.php');
        } else {
       
          mysql_free_result($result);
          mysql_close();
          header('HTTP/1.1 404 Not Found');
          $PHP_SELF = '/index.php';
          include('index.php');
          
          }
        }        
      }
         
        }
      }
    }
  } else {
    $PHP_SELF = '/index.php';
    include('index.php');
  }



  function get_parent_categories(&$categories, $categories_id) {
    $parent_categories_query = "select parent_id
                                from " . TABLE_CATEGORIES . "
                                where categories_id = '" . (int)$categories_id . "'";

    $result = mysql_query($parent_categories_query);

    while ($parent_categories = mysql_fetch_array($result, MYSQL_ASSOC)) {
      if ($parent_categories['parent_id'] == 0) return true;
      $categories[sizeof($categories)] = $parent_categories['parent_id'];
      if ($parent_categories['parent_id'] != $categories_id) {
        get_parent_categories($categories, $parent_categories['parent_id']);
      }
    }
  }



  function product_path($products_id) {
    $cPath = '';

    $category_query = "select p2c.categories_id
                       from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
                       where p.products_id = '" . (int)$products_id . "'
                       and p.products_status = '1'
                       and p.products_id = p2c.products_id limit 1";

    $category = mysql_query($category_query);

    if (mysql_num_rows($category) > 0) {

      $category = mysql_fetch_array($category, MYSQL_ASSOC);

      $categories = array();
      get_parent_categories($categories, $category['categories_id']);

      $categories = array_reverse($categories);

      $cPath = implode('_', $categories);

      if (not_null($cPath)) $cPath .= '_';
      $cPath .= $category['categories_id'];
    }

    return $cPath;
  }


  function not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }
