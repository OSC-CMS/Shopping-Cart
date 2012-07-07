<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

  define('PAGE_PARSE_START_TIME', microtime());
  error_reporting(E_ALL & ~E_NOTICE);

  define('PROJECT_VERSION', 'OSC-CMS');

  $request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';
if (!isset($PHP_SELF)) $PHP_SELF = $_SERVER['PHP_SELF'];
  require(dir_path('includes') . 'filenames.php');
  require(dir_path('includes') . 'database_tables.php');
  define('STORE_DB_TRANSACTIONS', 'false');

  os_db_connect() or die('Unable to connect to database server!');

  $configuration_query = os_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
  while ($configuration = os_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }

  if ( (GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && (PHP_VERSION >= '4') ) {
    if (($ini_zlib_output_compression = (int)ini_get('zlib.output_compression')) < 1) {
      ob_start('ob_gzhandler');
    } else {
      ini_set('zlib.output_compression_level', GZIP_LEVEL);
    }
  }

require_once(_CLASS.'template.php');

?>