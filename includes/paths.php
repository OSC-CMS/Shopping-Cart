<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

  $PATH = array();
  
  $DIR = realpath(dirname(dirname(__FILE__))).'/';
  $DIR = str_replace('\\', '/', $DIR);
  
  $HTTP = 'http://' . $_SERVER['HTTP_HOST'];
  $HTTPS = $HTTP;

  if(!isset($_SERVER['DOCUMENT_ROOT']))
  { 
       if(isset($_SERVER['SCRIPT_FILENAME']))
	   {
            $_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])));
       }
  }
  
  $root =  realpath($_SERVER['DOCUMENT_ROOT']);
  $root = str_replace('\\', '/', $root);

  if ($root != '/')
  {
     $CATALOG =  str_replace($root, '', $DIR);
  }
  else
  {
     $CATALOG = '/';
  }

  define('DIR', $DIR);
  define('HTTP', $HTTP);
  define('CATALOG', $CATALOG);

  $PATH['server'] =         array('dir' => DIR,                              'http' => HTTP); 
  $PATH['catalog'] =        array('dir' => DIR,                              'http' => HTTP.CATALOG); 
  $PATH['themes'] =         array('dir' => DIR.'themes/',                    'http' => HTTP.CATALOG.'themes/'); 
  $PATH['lang'] =           array('dir' => DIR.'langs/',                     'http' => HTTP.CATALOG.'langs/'); 
  $PATH['cache'] =          array('dir' => DIR.'cache/',                     'http' => HTTP.CATALOG.'cache/'); 
  $PATH['admin'] =          array('dir' => DIR.'admin/',                     'http' => HTTP.CATALOG.'admin/'); 
  $PATH['modules'] =        array('dir' => DIR.'modules/',                   'http' => HTTP.CATALOG.'modules/'); 
  $PATH['payment'] =        array('dir' => DIR.'modules/payment/',           'http' => HTTP.CATALOG.'modules/payment/'); 
  $PATH['icons'] =        array('dir' => DIR.'media/icons/',           'http' => HTTP.CATALOG.'media/icons/'); 
  $PATH['js'] =             array('dir' => DIR.'jscript/',                   'http' => HTTP.CATALOG.'jscript/'); 
  $PATH['pub'] =             array('dir' => DIR.'media/pub/',                'http' => HTTP.CATALOG.'media/pub/'); 
  $PATH['images'] =         array('dir' => DIR.'images/',                    'http' => HTTP.CATALOG.'images/'); 
  $PATH['img'] =  $PATH['images'];
  $PATH['images_original'] =      array('dir' => DIR.'images/product_images/original_images/',                   
                                        'http' => HTTP.CATALOG.'images/product_images/original_images/'); 
  $PATH['images_thumbnail'] =    array('dir' => DIR.'images/product_images/thumbnail_images/',                    
                                       'http' => HTTP.CATALOG.'images/product_images/thumbnail_images/'); 
  $PATH['images_info'] =         array('dir' => DIR.'images/product_images/info_images/',                    
                                       'http' => HTTP.CATALOG.'images/product_images/info_images/'); 
  $PATH['images_popup'] =         array('dir' => DIR.'images/product_images/popup_images/',                    
                                        'http' => HTTP.CATALOG.'images/product_images/popup_images/'); 
  $PATH['plug'] =           array('dir' => DIR.'modules/plugins/',           'http' => HTTP.CATALOG.'modules/plugins/'); 
  $PATH['lib'] =            array('dir' => DIR.'includes/lib/',              'http' => HTTP.CATALOG.'includes/lib/'); 
  $PATH['class'] =          array('dir' => DIR.'includes/classes/',          'http' => HTTP.CATALOG.'includes/classes/'); 
  $PATH['func'] =           array('dir' => DIR.'includes/functions/',        'http' => HTTP.CATALOG.'includes/functions/'); 
  $PATH['includes'] =       array('dir' => DIR.'includes/',                  'http' => HTTP.CATALOG.'includes/'); 
  $PATH['function'] = $PATH['func'];
  $PATH['page_admin'] =     array('dir' => DIR.'admin/includes/pages/',      'http' => HTTP.CATALOG.'admin/includes/pages/'); 
  $PATH['includes_admin'] = array('dir' => DIR.'admin/includes/',            'http' => HTTP.CATALOG.'admin/includes/'); 
  $PATH['class_admin'] =    array('dir' => DIR.'admin/includes/classes/',    'http' => HTTP.CATALOG.'admin/includes/classes/'); 
  $PATH['lang_admin'] =     array('dir' => DIR.'admin/lang/',                'http' => HTTP.CATALOG.'admin/lang/'); 
  $PATH['modules_admin'] =  array('dir' => DIR.'admin/includes/modules/',    'http' => HTTP.CATALOG.'admin/includes/modules/'); 
  $PATH['images_admin'] =   array('dir' => DIR.'admin/images/',              'http' => HTTP.CATALOG.'admin/images/'); 
  $PATH['func_admin'] =     array('dir' => DIR.'admin/includes/functions/',  'http' => HTTP.CATALOG.'admin/includes/functions/'); 
  $PATH['icons_admin'] =    array('dir' => DIR.'admin/images/icons/',        'http' => HTTP.CATALOG.'admin/images/icons/'); 
  $PATH['themes_admin'] =   array('dir' => DIR.'admin/themes/',              'http' => HTTP.CATALOG.'admin/themes/'); 
 
  define('DIR_FS_DOCUMENT_ROOT', DIR);
  
  define('HTTP_SERVER', $HTTP);
  define('HTTPS_SERVER', $HTTPS);
  define('ENABLE_SSL', false);
  
  define('DIR_WS_CATALOG', CATALOG);
  define('DIR_FS_CATALOG', $DIR);
  
    //admin
  define('HTTP_CATALOG_SERVER', $HTTP);
  define('HTTPS_CATALOG_SERVER', $HTTPS);
  define('ENABLE_SSL_CATALOG', 'false');
  define('DIR_WS_ADMIN', CATALOG.'admin/');
  define('DIR_FS_ADMIN', $DIR.'admin/'); 
  
  define('_HTTP', HTTP_SERVER.DIR_WS_CATALOG);
  define('_HTTPS', HTTPS_SERVER.DIR_WS_CATALOG);
  define('_HTTP_SERV', HTTP_SERVER.DIR_WS_CATALOG);

  define('_CATALOG', DIR_FS_CATALOG);

  define('_CATALOG_ADMIN', _CATALOG.'admin/');
  define('_ICONS', DIR_FS_DOCUMENT_ROOT . 'media/icons/');
  
  define('_INCLUDES', _CATALOG . 'includes/');
  define('_TMP', _CATALOG . 'tmp/');

  define('_IMG', _CATALOG . 'images/');
  define('_HTTP_IMG', _HTTP . 'images/');
  define('_HTTPS_IMG', _HTTPS . 'images/');
  
  define('_THEMES', _CATALOG . 'themes/');
  define('_HTTP_THEMES', _HTTP . 'themes/');

  define('_MODULES', _CATALOG . 'modules/');
  define('_MEDIA', _CATALOG . 'media/');
  define('_PLUG', _CATALOG . 'modules/plugins/');

  define('_MODULES_ADMIN', _CATALOG . 'admin/includes/modules/');
  
  define('_CLASS', _CATALOG . 'includes/classes/');
  define('_CLASS_ADMIN', _CATALOG . 'admin/includes/classes/');
  
  define('_FUNC', _CATALOG . 'includes/functions/');
  define('_FUNC_ADMIN', _CATALOG . 'admin/includes/functions/');
  define('_LIB', _CATALOG . 'includes/lib/');
  define('_PAGES', _CATALOG . 'includes/pages/');
  define('_PAGES_ADMIN', _CATALOG . 'admin/includes/pages/');
  define('_LANG', _CATALOG . 'langs/');
  define('_LANG_ADMIN', _CATALOG . 'admin/lang/');
  
  define('_CACHE', _CATALOG . 'cache/');
  
  define('_PUB',  'media/pub/');
  define('_DOWNLOAD', _CATALOG . 'media/download/');
  define('_EXPORT', _CATALOG . 'media/export/');
  define('_IMPORT', _CATALOG . 'media/import/');
  
  define('_MAIL', _CATALOG . 'media/mail/');
  
  define('_IMAGES', _CATALOG . 'images/');
  define('_HTTP_ICONS', http_path('icons'));

  define('SESSION_WRITE_DIRECTORY', 'tmp/');
  
  define('DIR_FS_LANGUAGES', $DIR.'admin/langs');
  define('DIR_FS_CATALOG_MODULES', DIR_FS_CATALOG . 'includes/modules/');
  
  define('_LIB_', _HTTP . 'includes/lib/');
  define('_JS', _HTTP . 'jscript/');
  define('_IMAGES_ADMIN', _CATALOG . 'images/');
  define('_HTTP_IMAGES', _HTTP_SERV . 'images/');
  define('_HTTP_IMAGES_ADMIN', _HTTP_SERV . 'images/');

  define('OS_PLUGIN_DIR', _CATALOG . 'modules/plugins/');
  define('_HTTP_PLUG', _HTTP . 'modules/plugins/');
  
  define('DIR_WS_IMAGES', 'images/');
  define('DIR_WS_ORIGINAL_IMAGES', DIR_WS_IMAGES .'product_images/original_images/');
  define('DIR_WS_THUMBNAIL_IMAGES', DIR_WS_IMAGES .'product_images/thumbnail_images/');
  define('DIR_WS_INFO_IMAGES', DIR_WS_IMAGES .'product_images/info_images/');
  define('DIR_WS_POPUP_IMAGES', DIR_WS_IMAGES .'product_images/popup_images/');
  define('DIR_WS_ICONS', 'media/icons/');
  define('DIR_WS_INCLUDES',DIR_FS_DOCUMENT_ROOT. 'includes/');
  define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
  define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
  define('DIR_WS_MODULES', 'modules/');
  define('DIR_WS_LANGUAGES', DIR_FS_CATALOG . 'langs/');
  define('_DOWNLOAD_PUBLIC', 'media/pub/');
  define('DIR_FS_DOWNLOAD', 'media/download/');
  define('DIR_FS_DOWNLOAD_PUBLIC', 'media/pub/');
  define('DIR_FS_FORUM_ROOT', '');
  define('DIR_FS_SITE_ROOT', '');
  define('OS_COOKIE_NAME', 'osCookie');
  define('OS_ERROR', 'false');

function http_path($name)  
{
   global $PATH;
   
   return $PATH[$name]['http'];
}

function dir_path ($name)
{
   global $PATH;
   
   return $PATH[$name]['dir'];
}

function dir_path_admin ($name)
{
   global $PATH;
   
   return $PATH[$name.'_admin']['dir'];
}

function http_path_admin ($name)
{
   global $PATH;
   
   return $PATH[$name.'_admin']['http'];
}

function get_path ($name, $param = 'dir')
{
   global $PATH;
   
   return $PATH[$name][$param];
}

/*
  $param  = dir or http
*/
function add_path ($name, $value)
{
   global $PATH;
   
   $PATH[$name]['dir'] = $value['dir'];
   $PATH[$name]['http'] = $value['http'];
}

?>