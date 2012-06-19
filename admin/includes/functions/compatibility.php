<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.1
#####################################
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

function do_magic_quotes_gpc(&$ar) 
{
    if (!is_array($ar)) return;

    while (list($key, $value) = each($ar)) {
      if (is_array($value)) {
        do_magic_quotes_gpc($value);
      } else {
        $ar[$key] = addslashes($value);
      }
    }
  }

  if (!is_array($_GET)) $_GET = array();
  if (!is_array($_POST)) $_POST = array();
  if (!is_array($_COOKIE)) $_COOKIE = array();

  if (!get_magic_quotes_gpc()) {
    do_magic_quotes_gpc($_GET);
    do_magic_quotes_gpc($_POST);
    do_magic_quotes_gpc($_COOKIE);
  }

  if (!function_exists('is_numeric')) {
    function is_numeric($param) {
      return ereg("^[0-9]{1,50}.?[0-9]{0,50}$", $param);
    }
  }

  if (!function_exists('is_uploaded_file')) {
    function is_uploaded_file($filename) {
      if (!$tmp_file = get_cfg_var('upload_tmp_dir')) {
        $tmp_file = dirname(tempnam('', ''));
      }

      if (strchr($tmp_file, '/')) {
        if (substr($tmp_file, -1) != '/') $tmp_file .= '/';
      } elseif (strchr($tmp_file, '\\')) {
        if (substr($tmp_file, -1) != '\\') $tmp_file .= '\\';
      }

      return file_exists($tmp_file . basename($filename));
    }
  }

  if (!function_exists('move_uploaded_file')) {
    function move_uploaded_file($file, $target) {
      return copy($file, $target);
    }
  }

  if (!function_exists('checkdnsrr')) {
    function checkdnsrr($host, $type) {
      if(os_not_null($host) && os_not_null($type)) {
        @exec("nslookup -type=$type $host", $output);
        while(list($k, $line) = each($output)) {
          if(preg_match("/^$host/i", $line)) {
            return true;
          }
        }
      }
      return false;
    }
  }
?>