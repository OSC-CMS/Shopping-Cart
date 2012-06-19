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

@ini_set("session.gc_probability", 100);

  if (STORE_SESSIONS == 'mysql') {

    function _sess_open($save_path, $session_name) {
      return true;
    }

    function _sess_close() {
      return true;
    }

    function _sess_read($key) 
	{
	   //фильтруем key
       $key  = mysql_real_escape_string($key);
	   
	   if (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $key)) 
	   {
           die('OSC-CMS error: invalide session key.');
       }
	   
      $qid = os_db_query("select value from " . TABLE_SESSIONS . " where sesskey = '" . $key . "' and expiry > '" . time() . "'");

      $value = os_db_fetch_array($qid);
      if ($value['value']) {
        return $value['value'];
      }

      return false;
    }

    function _sess_write($key, $val) 
	{
	   //фильтруем key
       $key  = mysql_real_escape_string($key);
	   
	   if (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $key)) 
	   {
           die('OSC-CMS error: invalide session key.');
       }
	   
	   
      $expiry = time() + SESSION_TIMEOUT_CATALOG;
      $value = addslashes($val);

      $qid = os_db_query("select count(*) as total from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'");
      $total = os_db_fetch_array($qid);

      if ($total['total'] > 0) 
	  {
           return os_db_query("update " . TABLE_SESSIONS . " set expiry = '" . $expiry . "', value = '" . $value . "' where sesskey = '" . $key . "'");
      } 
	  else 
	  {
           return os_db_query("insert into " . TABLE_SESSIONS . " values ('" . $key . "', '" . $expiry . "', '" . $value . "')");
      }
      
    }

    function _sess_destroy($key) 
	{
	   //фильтруем key
       $key  = mysql_real_escape_string($key);
	   
	   if (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $key)) 
	   {
           die('OSC-CMS error: invalide session key.');
       }
        
	   return os_db_query("delete from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'");
    }

    function _sess_gc($maxlifetime) 
	{
       os_db_query("delete from " . TABLE_SESSIONS . " where expiry < '" . time() . "'");
       return true;
    }

    session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
  }

  function os_session_start() {
	@ini_set('session.gc_maxlifetime', SESSION_TIMEOUT_CATALOG);
    return session_start();
  }

  function os_session_register($variable) {
    global $session_started;

    if ($session_started == true) {
      return $_SESSION[$variable];
    }
  }

  function os_session_is_registered($variable) {
    return isset($_SESSION[$variable]);
  }

  function os_session_unregister($variable) {
    unset($_SESSION[$variable]);
  }

  function os_session_id($sessid = '') {
    if (!empty($sessid)) {
      return session_id($sessid);
    } else {
      return session_id();
    }
  }

  function os_session_name($name = '') {
    if (!empty($name)) {
      return session_name($name);
    } else {
      return session_name();
    }
  }

  function os_session_close() {
    if (function_exists('session_close')) {
      return session_close();
    }
  }

  function os_session_destroy() {
    return session_destroy();
  }

  function os_session_save_path($path = '') {
    if (!empty($path)) {
      return session_save_path($path);
    } else {
      return session_save_path();
    }
  }

  function os_session_recreate() {

      $session_backup = $_SESSION;

      unset($_COOKIE[os_session_name()]);

      os_session_destroy();

      if (STORE_SESSIONS == 'mysql') {
        session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
      }

      os_session_start();

      $_SESSION = $session_backup;
      unset($session_backup);
    
  }
?>