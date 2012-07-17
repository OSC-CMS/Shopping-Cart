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

function os_db_install($database, $sql_file) 
{
    global $db_error;

    $db_error = false;

    if (!@os_db_select_db($database)) 
	{
      if (@os_db_query_installer('create database ' . $database)) 
	  {
        os_db_select_db($database);
      } 
	  else 
	  {
        $db_error = mysql_error();
      }
    }

    if (!$db_error) 
	{
      if (file_exists($sql_file)) 
	  {
        $fd = fopen($sql_file, 'rb');
        $restore_query = fread($fd, filesize($sql_file));
        fclose($fd);
      } 
	  else 
	  {
        $db_error = 'SQL file does not exist: ' . $sql_file;
        return false;
      }

      $sql_array = array();
      $sql_length = strlen($restore_query);
      $pos = strpos($restore_query, ';');
      for ($i=$pos; $i<$sql_length; $i++) 
	  {
        if ($restore_query[0] == '#') 
		{
          $restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
          $sql_length = strlen($restore_query);
          $i = strpos($restore_query, ';')-1;
          continue;
        }
        if ($restore_query[($i+1)] == "\n") 
		{
          for ($j=($i+2); $j<$sql_length; $j++) 
		  {
            if (trim($restore_query[$j]) != '') 
			{
              $next = substr($restore_query, $j, 6);
              if ($next[0] == '#') 
			  {
// find out where the break position is so we can remove this line (#comment line)
                for ($k=$j; $k<$sql_length; $k++) 
				{
                  if ($restore_query[$k] == "\n") break;
                }
                $query = substr($restore_query, 0, $i+1);
                $restore_query = substr($restore_query, $k);
// join the query before the comment appeared, with the rest of the dump
                $restore_query = $query . $restore_query;
                $sql_length = strlen($restore_query);
                $i = strpos($restore_query, ';')-1;
                continue 2;
              }
              break;
            }
          }
          if ($next == '') 
		  { // get the last insert query
            $next = 'insert';
          }
         if ( (preg_match('/create/i', $next)) || (preg_match('/insert/i', $next)) || (preg_match('/drop t/i', $next)) ) 
		  {
            $next = '';
            $sql_array[] = substr($restore_query, 0, $i);
            $restore_query = ltrim(substr($restore_query, $i+1));
            $sql_length = strlen($restore_query);
            $i = strpos($restore_query, ';')-1;
          }
        }
      }

      for ($i=0; $i<sizeof($sql_array); $i++) 
	  {
        os_db_query_installer($sql_array[$i]);
      }
    } 
	else 
	{
      return false;
    }
  }

function os_db_test_connection($database) 
{
    global $db_error;
    $db_error = false;

    if (!$db_error) 
	{
      if (!@os_db_select_db($database)) 
	  {
        $db_error = mysql_error();
      } 
	  else 
	  {
        if (!@os_db_query_installer("select count(*) from " . TABLE_CONFIGURATION . "")) 
		{
          $db_error = mysql_error();
        }
      }
    }

    if ($db_error) {
      return false;
    } else {
      return true;
    }
}

function os_draw_checkbox_field_installer($name, $value = '', $checked = false) 
{
    return os_draw_selection_field_installer($name, 'checkbox', $value, $checked);
}

function os_draw_selection_field_installer($name, $type, $value = '', $checked = false) 
{
    $selection = '<input type="' . $type . '" name="' . $name . '"';
    if ($value != '') $selection .= ' value="' . $value . '"';
	
    if ( ($checked == true) || (isset($GLOBALS[$name]) && $GLOBALS[$name] == 'on') || ($value == 'on') || (isset($value) && isset($GLOBALS[$name]) && $GLOBALS[$name] == $value) ) 
	{
      $selection .= ' checked="checked"';
    }
    $selection .= ' />';

    return $selection;
}

function os_draw_input_field_installer($name, $text = '', $type = 'text', $parameters = '', $reinsert_value = true) 
{
    $field = '<input class="round" type="' . $type . '" name="' . $name . '"';
	
    if ( ($key = @$GLOBALS[$name]) || ($key = @$_GET[$name]) || ($key = @$_POST[$name]) || ($key = @$_SESSION[$name]) && (isset($reinsert_value)) ) 
	{
      $field .= ' value="' . $key . '"';
    } 
	elseif ($text != '') 
	{
      $field .= ' value="' . $text . '"';
    }
    if ($parameters) $field.= ' ' . $parameters;
    $field .= ' />';

    return $field;
}

   
function db_test_create_db_permission($database) 
{
//Проверка базы данных на чтение-запись
    global $db_error;

    $db_created = false;
    $db_error = false;

    if (!$database) 
	{
      $db_error = 'No Database selected.';
      return false;
    }

    if (!$db_error) {
      if (!os_db_select_db($database)) 
	  {
	  
        $db_created = true;
      } 
	  else 
	  {
        $db_error = mysql_error();
		echo $db_error;
      }
      if (!$db_error) 
	  {
        if (os_db_select_db($database)) 
		{
          if (os_db_query_installer('create table temp ( temp_id int(5) )')) 
		  {
            if (os_db_query_installer('drop table temp')) 
			{
              if ($db_created) 
			  {
                if (os_db_query_installer('drop database ' . $database)) 
				{
                } 
				else 
				{
                  $db_error = mysql_error();
                }
              }
            } 
			else 
			{
              $db_error = mysql_error();
            }
          } 
		  else 
		  {
            $db_error = mysql_error();
          }
        } 
		else 
		{
          $db_error = mysql_error();
        }
      }
    }

    if ($db_error) 
	{
      return false;
    } 
	else
	{
      return true;
    }
  }
 
function os_draw_radio_field_installer($name, $value = '', $checked = false) 
{
    return os_draw_selection_field_installer($name, 'radio', $value, $checked);
} 

function os_db_query_installer($query, $link = 'db_link') 
{
    global $$link;
    return mysql_query($query, $$link);
}
  
function os_draw_hidden_field_installer($name, $value) 
{
    return '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
}

function os_draw_password_field_installer($name, $text = '') 
{
    return os_draw_input_field_installer($name, $text, 'password', '', false);
}

function os_in_array($value, $array) 
{
    if (!$array) $array = array();
    if (function_exists('in_array')) 
	{
      if (is_array($value)) 
	  {
        for ($i=0; $i<sizeof($value); $i++) 
		{
          if (in_array($value[$i], $array)) return true;
        }
        return false;
      } 
	  else 
	  {
        return in_array($value, $array);
      }
    } 
	else 
	{
      reset($array);
      while (list(,$key_value) = each($array)) 
	  {
        if (is_array($value)) 
		{
          for ($i=0; $i<sizeof($value); $i++) 
		  {
            if ($key_value == $value[$i]) return true;
          }
          return false;
        } 
		else 
		{
          if ($key_value == $value) return true;
        }
      }
    }
    return false;
}

function os_db_connect_installer($server, $username, $password, $link = 'db_link') 
{
    global $$link, $db_error;
    $db_error = false;

    if (!$server) 
	{
      $db_error = 'No Server selected.';
      return false;
    }

    $$link = @mysql_connect($server, $username, $password) or $db_error = mysql_error();
    
	@mysql_query("SET SQL_MODE= ''");
    @mysql_query("SET CHARACTER SET utf8");
    @mysql_query("SET NAMES utf8");
    @mysql_query("SET COLLATION utf8_general_ci");

    return $$link;
}

function os_db_prepare_input($string) 
{
    if (is_string($string)) 
	{
         return trim(stripslashes($string));
    } 
	elseif (is_array($string)) 
	{
         reset($string);
         while (list($key, $value) = each($string)) 
		 {
              $string[$key] = os_db_prepare_input($value);
         }
         return $string;
    } 
	else 
	{
         return $string;
    }
}

function os_redirect($url) 
{
	header('Location: ' . preg_replace("/[\r\n]+(.*)$/i", "", $url));
    os_exit(); 
}

function os_exit() 
{
    os_session_close();
    exit();
}

function os_session_close() 
{
    if (function_exists('session_close')) {
      return session_close();
    }
}

function os_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true)
  {
    $param_array = array();
    $params = '';
    $action = '';
    $products_id = '';
    $sort = '';
    $direction = '';
    $filter_id = '';
    $on_page = '';
    $language = '';
    $currency = '';
    $page_num = '';
    $matches = array();

    if ($page == FILENAME_DEFAULT) {
      if (strpos($parameters, 'cat') === false) {
        return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
      } else {
        $categories_id = -1;
        $param_array = explode('&', $parameters);

        for ($i = 0, $n = sizeof($param_array); $i < $n; $i++) {
          $parsed_param = explode('=', $param_array[$i]);
          if ($parsed_param[0] === 'cat') {
            $pos = strrpos($parsed_param[1], '_');
            if ($pos === false) {
              $categories_id = $parsed_param[1];
            } else {  
              if (preg_match('/^c(.*)_/', $parsed_param[1], $matches)) {
                $categories_id = $matches[1];
              }
            }
          } elseif ($parsed_param[0] === 'action') {
            $action = $parsed_param[1];
          } elseif ($parsed_param[0] === 'BUYproducts_id') {
            $products_id = $parsed_param[1];
          } elseif ($parsed_param[0] === 'sort') {
            $sort = $parsed_param[1];
          } elseif ($parsed_param[0] === 'direction') {
            $direction = $parsed_param[1];
          } elseif ($parsed_param[0] === 'filter_id') {
            $filter_id = $parsed_param[1];
          } elseif ($parsed_param[0] === 'language') {
            $language = $parsed_param[1];
          } elseif ($parsed_param[0] === 'currency') {
            $currency = $parsed_param[1];
          } elseif ($parsed_param[0] === 'on_page') {
            if (os_not_null($parsed_param[1])) {
              $on_page = $parsed_param[1];
            } else {
              $on_page = -1;
            }
          } elseif ($parsed_param[0] === 'page') {
            $page_num = $parsed_param[1];
          }
        }

        $categories_url = os_db_query('select categories_url from ' . TABLE_CATEGORIES . ' where categories_id="' . $categories_id . '"');
        $categories_url = os_db_fetch_array($categories_url);
        $categories_url = $categories_url['categories_url'];

        if ($categories_url == '') {
          return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
        } else {

          if ($connection == 'NONSSL') {
            $link = HTTP_SERVER;
          } elseif ($connection == 'SSL') {
            if (ENABLE_SSL == 'true') {
              $link = HTTPS_SERVER ;
            } else {
              $link = HTTP_SERVER;
            }
          } else {
            die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
          }

          if ($connection == 'SSL' && ENABLE_SSL == 'true') {
            $link .= DIR_WS_HTTPS_CATALOG;
          } else {
            $link .= DIR_WS_CATALOG;
          }

          if (os_not_null($action)) {
            $params .= '&action=' . $action;
          }

          if (os_not_null($products_id)) {
            $params .= '&BUYproducts_id=' . $products_id;
          }

          if (os_not_null($sort)) {
            $params .= '&sort=' . $sort;
          }

          if (os_not_null($direction)) {
            $params .= '&direction=' . $direction;
          }

          if (os_not_null($filter_id)) {
            $params .= '&filter_id=' . $filter_id;
          }

          if (os_not_null($language)) {
            $params .= '&language=' . $language;
          }

          if (os_not_null($currency)) {
            $params .= '&currency=' . $currency;
          }

          if ($on_page === -1) {
            $params .= '&on_page=';
          } elseif ($on_page > 0) {
            $params .= '&on_page=' . $on_page;
          }

          if (os_not_null($page_num)) {
            $params .= '&page=' . $page_num;
          }


          if (os_not_null($params)) {
            if (strpos($params, '&') === 0) {
              $params = substr($params, 1);
            }
            
            $params = str_replace('&', '&amp;', $params);

            $categories_url .= '?' . $params;
          }

          $link_ajax = '';

          if (AJAX_CART == 'true') {
            if( os_not_null($parameters) && preg_match("/buy_now/i", $parameters) && $page != 'ajax_shopping_cart.php'){
              $link_ajax = '" onclick="doBuyNowGet(\'' . os_href_link( 'ajax_shopping_cart.php', $parameters, $connection, $add_session_id, $search_engine_safe) . '\'); return false;';
            }
          }


          return $link . $categories_url . $link_ajax;
        }
      }
    } elseif ($page == FILENAME_PRODUCT_INFO) {

      $products_id = -1;
      $action = '';
      $language = '';
      $currency = '';
      $param_array = explode('&', $parameters);

      for ($i = 0, $n = sizeof($param_array); $i < $n; $i++) {
        $parsed_param = explode('=', $param_array[$i]);
        if ($parsed_param[0] === 'products_id') {
          $products_id = $parsed_param[1];
        } elseif ($parsed_param[0] === 'action') {
          $action = $parsed_param[1];
        } elseif ($parsed_param[0] === 'language') {
          $language = $parsed_param[1];
        } elseif ($parsed_param[0] === 'currency') {
          $currency = $parsed_param[1];
        } elseif ($parsed_param[0] === 'info') {
          if (preg_match('/^p(.*)_/', $parsed_param[1], $matches)) {
            $products_id = $matches[1];
          }
        }
      }

      $products_page_url = os_db_query('select products_page_url from ' . TABLE_PRODUCTS . ' where products_id="' . $products_id . '"');
      $products_page_url = os_db_fetch_array($products_page_url);
      $products_page_url = $products_page_url['products_page_url'];

      if ($products_page_url == '') {
        return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
      } else {

          if ($connection == 'NONSSL') {
            $link = HTTP_SERVER;
          } elseif ($connection == 'SSL') {
            if (ENABLE_SSL == 'true') {
              $link = HTTPS_SERVER ;
            } else {
              $link = HTTP_SERVER;
            }
          } else {
            die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
          }

          if ($connection == 'SSL' && ENABLE_SSL == 'true') {
            $link .= DIR_WS_HTTPS_CATALOG;
          } else {
            $link .= DIR_WS_CATALOG;
          }

          if (os_not_null($action)) {
            $products_page_url .= '?action=' . $action;
          }

          if (os_not_null($language)) {
            $products_page_url .= '?language=' . $language;
          }

          if (os_not_null($currency)) {
            $products_page_url .= '?currency=' . $currency;
          }

          return $link . $products_page_url;
      }

    } elseif ($page == FILENAME_ARTICLE_INFO) {

      $a_id = -1;
      $param_array = explode('&', $parameters);

      for ($i = 0, $n = sizeof($param_array); $i < $n; $i++) {
        $parsed_param = explode('=', $param_array[$i]);
        if ($parsed_param[0] === 'articles_id') {
          $a_id = $parsed_param[1];
        } 
      }

      $a_url = os_db_query('select articles_page_url from ' . TABLE_ARTICLES . ' where articles_id="' . $a_id . '"');
      $a_url = os_db_fetch_array($a_url);
      $a_url = $a_url['articles_page_url'];

      if ($a_url == '') {
        return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
      } else {

          if ($connection == 'NONSSL') {
            $link = HTTP_SERVER;
          } elseif ($connection == 'SSL') {
            if (ENABLE_SSL == 'true') {
              $link = HTTPS_SERVER ;
            } else {
              $link = HTTP_SERVER;
            }
          } else {
            die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
          }

          if ($connection == 'SSL' && ENABLE_SSL == 'true') {
            $link .= DIR_WS_HTTPS_CATALOG;
          } else {
            $link .= DIR_WS_CATALOG;
          }

          return $link . $a_url;
      }

    } elseif ($page == FILENAME_NEWS) {

      $n_id = -1;
      $param_array = explode('&', $parameters);

      for ($i = 0, $n = sizeof($param_array); $i < $n; $i++) {
        $parsed_param = explode('=', $param_array[$i]);
        if ($parsed_param[0] === 'news_id') {
          $n_id = $parsed_param[1];
        } 
      }

      $n_url = os_db_query('select news_page_url from ' . TABLE_LATEST_NEWS . ' where news_id="' . $n_id . '"');
      $n_url = os_db_fetch_array($n_url);
      $n_url = $n_url['news_page_url'];

      if ($n_url == '') {
        return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
      } else {

          if ($connection == 'NONSSL') {
            $link = HTTP_SERVER;
          } elseif ($connection == 'SSL') {
            if (ENABLE_SSL == 'true') {
              $link = HTTPS_SERVER ;
            } else {
              $link = HTTP_SERVER;
            }
          } else {
            die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
          }

          if ($connection == 'SSL' && ENABLE_SSL == 'true') {
            $link .= DIR_WS_HTTPS_CATALOG;
          } else {
            $link .= DIR_WS_CATALOG;
          }

          return $link . $n_url;
      }

    } elseif ($page == FILENAME_ARTICLES) {

      $t_id = -1;
      $param_array = explode('&', $parameters);

      for ($i = 0, $n = sizeof($param_array); $i < $n; $i++) {
        $parsed_param = explode('=', $param_array[$i]);
        if ($parsed_param[0] === 'tPath') {
          $t_id = $parsed_param[1];
        } 
      }


  
      $t_url = os_db_query('select topics_page_url from ' . TABLE_TOPICS . ' where topics_id="' . $t_id . '"');
      $t_url = os_db_fetch_array($t_url);
      $t_url = $t_url['topics_page_url'];

      if ($t_url == '') {
        return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
      } else {

          if ($connection == 'NONSSL') {
            $link = HTTP_SERVER;
          } elseif ($connection == 'SSL') {
            if (ENABLE_SSL == 'true') {
              $link = HTTPS_SERVER ;
            } else {
              $link = HTTP_SERVER;
            }
          } else {
            die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
          }

          if ($connection == 'SSL' && ENABLE_SSL == 'true') {
            $link .= DIR_WS_HTTPS_CATALOG;
          } else {
            $link .= DIR_WS_CATALOG;
          }

          return $link . $t_url;
      }

    } elseif ($page == FILENAME_CONTENT) {

      $co_id = -1;
      $param_array = explode('&', $parameters);

      for ($i = 0, $n = sizeof($param_array); $i < $n; $i++) {
        $parsed_param = explode('=', $param_array[$i]);
        if ($parsed_param[0] === 'coID') {
          $co_id = $parsed_param[1];
        } 
      }

      $co_url = os_db_query('select content_page_url from ' . TABLE_CONTENT_MANAGER . ' where content_id="' . $co_id . '"');
      $co_url = os_db_fetch_array($co_url);
      $co_url = $co_url['content_page_url'];

      if ($co_url == '') {
        return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
      } else {

          if ($connection == 'NONSSL') {
            $link = HTTP_SERVER;
          } elseif ($connection == 'SSL') {
            if (ENABLE_SSL == 'true') {
              $link = HTTPS_SERVER ;
            } else {
              $link = HTTP_SERVER;
            }
          } else {
            die('</td></tr></table></td></tr></table><br /><br /><strong class="note">Error!<br /><br />Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
          }

          if ($connection == 'SSL' && ENABLE_SSL == 'true') {
            $link .= DIR_WS_HTTPS_CATALOG;
          } else {
            $link .= DIR_WS_CATALOG;
          }

          return $link . $co_url;
      }
    } else {
      return os_href_link_original($page, $parameters, $connection, $add_session_id, $search_engine_safe);
    } 
  }

  
  function os_href_link_original($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) 
  {
    global $request_type, $session_started, $http_domain, $https_domain,$truncate_session_id;

    if (!os_not_null($page)) {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine the page link!<br /><br />');
    }

    if ($connection == 'NONSSL') {
      $link = HTTP_SERVER . DIR_WS_CATALOG;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL == true) {
        $link = HTTPS_SERVER . DIR_WS_CATALOG;
      } else {
        $link = HTTP_SERVER . DIR_WS_CATALOG;
      }
    } else {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</b><br /><br />');
    }

    if (os_not_null($parameters)) {
      $link .= $page . '?' . $parameters;
      $separator = '&';
    } else {
      $link .= $page;
      $separator = '?';
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
    if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
      if (defined('SID') && os_not_null(SID)) {
        $sid = SID;
      } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
        if ($http_domain != $https_domain) {
          $sid = session_name() . '=' . session_id();
        }
      }        
    }
        
        // remove session if useragent is a known Spider
    if ($truncate_session_id) $sid=NULL;

    if (isset($sid)) {
      $link .= $separator . $sid;
    }

    $link_ajax = '';


    return $link . $link_ajax;
}

function os_not_null($value) 
{
    if (is_array($value)) 
	{
      if (sizeof($value) > 0) 
	  {
        return true;
      } 
	  else 
	  {
        return false;
      }
    } 
	else 
	{
      if (($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) 
	  {
        return true;
      } 
	  else 
	  {
        return false;
      }
    }
}
    
function os_db_select_db($database) 
{
    return mysql_select_db($database);
}		

function os_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link', $use_pconnect = 'false', $new_link = false) 
{
   $password = trim($password);
   $server= trim($server);
   $username= trim($username);
   $database= trim($database);
   
    global $$link;

    if ($use_pconnect == 'true') 
	{
       $$link = mysql_pconnect($server, $username, $password);
    } 
	else 
	{
       $$link = @mysql_connect($server, $username, $password, $new_link); 
    }

    if ($$link)
	{
       @mysql_select_db($database);
       @mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
    }

    if (!$$link) 
	{
       os_db_error("connect", mysql_errno(), mysql_error());
    }
    return $$link;
}

function os_db_query($query, $link = 'db_link') 
{
    global $$link;
    global $query_counts;
 	
    $query_counts++; 

    $result = mysql_query($query, $$link) or os_db_error($query, mysql_errno(), mysql_error());

    if (!$result) 
	{
       os_db_error($query, mysql_errno(), mysql_error());
    }

    return $result;
}
 
function os_db_fetch_array(&$db_query,$cq=false) 
{
   global $db;
      if ($db->DB_CACHE=='true' && $cq) 
	  {
          if (!count($db_query)) return false;
          if (is_array($db_query)) 
		  {
             $curr = current($db_query);
             next($db_query);
          }
          return $curr;
      } 
	  else 
	  {
          if (is_array($db_query)) 
		  {
             $curr = current($db_query);
             next($db_query);
             return $curr;
          }
          return mysql_fetch_array($db_query, MYSQL_ASSOC);
      }
}

 
function os_db_error($query, $errno, $error) 
{
    global $db;
		echo 'db error!<br /><br />';
	echo '<textarea style="width:100%;height:100px;border: 1px solid #F00;   -moz-border-radius:4px;border-radius:4px;-webkit-border-radius:4px;-khtml-border-radius:4px;">';
	echo $query."\n".$error;
	echo '</textarea><br />';
	echo '<div class="install-body">
				

					<div class="clr"></div>
					
				</div>
	
			
			
			
		<div class="newsection"></div>
				
		
		
		</div>
		<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
		</div>
		</div>
	</div>
</div>

<div class="clr"></div>




			</div>
		</div>
		<div id="footer1">
			<div id="footer2">
				<div id="footer3"></div>
			</div>
		</div>';
	die('');
}

 function os_validate_email($email) {
    $valid_address = true;

    $mail_pat = '/^(.+)@(.+)$/i';
    $valid_chars = "[^] \(\)<>@,;:\.\\\"\[]";
    $atom = "$valid_chars+";
    $quoted_user='(\"[^\"]*\")';
    $word = "($atom|$quoted_user)";
    $user_pat = "/^$word(\.$word)*$/i";
    $ip_domain_pat='/^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$/i';
    $domain_pat = "/^$atom(\.$atom)*$/i";

    if (preg_match($mail_pat, $email, $components)) {
      $user = $components[1];
      $domain = $components[2];
      // validate user
      if (preg_match($user_pat, $user)) {
        // validate domain
        if (preg_match($ip_domain_pat, $domain, $ip_components)) {
          // this is an IP address
      	  for ($i=1;$i<=4;$i++) {
      	    if ($ip_components[$i] > 255) {
      	      $valid_address = false;
      	      break;
      	    }
          }
        } else {
          // Domain is a name, not an IP
          if (preg_match($domain_pat, $domain)) {
            /* domain name seems valid, but now make sure that it ends in a valid TLD or ccTLD
               and that there's a hostname preceding the domain or country. */
            $domain_components = explode(".", $domain);
            // Make sure there's a host name preceding the domain.
            if (sizeof($domain_components) < 2) {
              $valid_address = false;
            } else {
              $top_level_domain = strtolower($domain_components[sizeof($domain_components)-1]);
            }
          } else {
      	    $valid_address = false;
      	  }
      	}
      } else {
        $valid_address = false;
      }
    } else {
      $valid_address = false;
    }
   /* if ($valid_address) {
      if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
        $valid_address = false;
      }
    }
	*/
    return $valid_address;
}

function os_db_input($string, $link = 'db_link') 
{
  global $$link;

  if (function_exists('mysql_real_escape_string')) 
  {
      return mysql_real_escape_string($string, $$link);
  } 
  elseif (function_exists('mysql_escape_string')) 
  {
      return mysql_escape_string($string);
  }
  return addslashes($string);
}

function os_db_num_rows($db_query,$cq=false) 
{
    if (DB_CACHE=='true' && $cq) 
	{
        if (!count($db_query)) return false;
        return count($db_query);
    } 
	else 
	{
        if (!is_array($db_query)) return mysql_num_rows($db_query);
    }
}

function os_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link') 
{
   $php4_3_10 = (0 == version_compare(phpversion(), "4.3.10"));
   
   if (!defined('PHP4_3_10')) define('PHP4_3_10', $php4_3_10);
   
    reset($data);

    if ($action == 'insert') {
      $query = 'insert into ' . $table . ' (';
      while (list($columns, ) = each($data)) {
        $query .= $columns . ', ';
      }
      $query = substr($query, 0, -2) . ') values (';
      reset($data);
      while (list(, $value) = each($data)) {
      	 $value = (is_Float($value) & PHP4_3_10) ? sprintf("%.F",$value) : (string)($value);
        switch ($value) {
          case 'now()':
            $query .= 'now(), ';
            break;
          case 'null':
            $query .= 'null, ';
            break;
          default:
            $query .= '\'' . os_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ')';
    } elseif ($action == 'update') {
      $query = 'update ' . $table . ' set ';
      while (list($columns, $value) = each($data)) {
         $value = (is_Float($value) & PHP4_3_10) ? sprintf("%.F",$value) : (string)($value);
      	switch ($value) {
          case 'now()':
            $query .= $columns . ' = now(), ';
            break;
          case 'null':
            $query .= $columns .= ' = null, ';
            break;
          default:
            $query .= $columns . ' = \'' . os_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ' where ' . $parameters;
    }

    return os_db_query($query, $link);
}
  
function os_encrypt_password($plain) 
{
    $password=md5($plain);
    return $password;
}  

function os_get_country_list($name, $selected = '', $parameters = '') 
{
   $countries_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));
//    Probleme mit register_globals=off -> erstmal nur auskommentiert. Kann u.U. gelС†scht werden.
    $countries = os_get_countriesList();

    for ($i=0, $n=sizeof($countries); $i<$n; $i++) {
      $countries_array[] = array('id' => $countries[$i]['countries_id'], 'text' => $countries[$i]['countries_name']);
    }
	if (is_array($name)) return os_draw_pull_down_menuNote($name, $countries_array, $selected, $parameters);
    return os_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
}
 
 
function os_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . os_parse_input_field_data($name, array('"' => '&quot;')) . '"';

    if (os_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if (empty($default) && isset($GLOBALS[$name])) $default = $GLOBALS[$name];

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '<option value="' . os_parse_input_field_data($values[$i]['id'], array('"' => '&quot;')) . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' selected="selected"';
      }

      $field .= '>' . os_parse_input_field_data($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>';

    if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

  
  function os_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . os_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . os_parse_input_field_data($name, array('"' => '&quot;')) . '"';

    if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
      $field .= ' value="' . os_parse_input_field_data($GLOBALS[$name], array('"' => '&quot;')) . '"';
    } elseif (os_not_null($value)) {
      $field .= ' value="' . os_parse_input_field_data($value, array('"' => '&quot;')) . '"';
    }
    if (os_not_null($parameters)) $field .= ' ' . $parameters;
    $field .= ' />';
    return $field;
  }
function os_get_countriesList($countries_id = '', $with_iso_codes = false) 
{
    $countries_array = array();
    if (os_not_null($countries_id)) {
      if ($with_iso_codes == true) {
        $countries = os_db_query("select countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . $countries_id . "' and status = '1' order by countries_name");
        $countries_values = os_db_fetch_array($countries);
        $countries_array = array('countries_name' => $countries_values['countries_name'],
                                 'countries_iso_code_2' => $countries_values['countries_iso_code_2'],
                                 'countries_iso_code_3' => $countries_values['countries_iso_code_3']);
      } else {
        $countries = os_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . $countries_id . "' and status = '1'");
        $countries_values = os_db_fetch_array($countries);
        $countries_array = array('countries_name' => $countries_values['countries_name']);
      }
    } else {
      $countries = os_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . " where status = '1' order by countries_name");
      while ($countries_values = os_db_fetch_array($countries)) {
        $countries_array[] = array('countries_id' => $countries_values['countries_id'],
                                   'countries_name' => $countries_values['countries_name']);
      }
    }

    return $countries_array;
}

function os_parse_input_field_data($data, $parse) 
{
    return strtr(trim($data), $parse);
}
  
?>