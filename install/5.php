<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require('includes/top.php');
if (!isset($_SESSION['language']) )
{
   $_SESSION['language'] = 'ru';
}   
if (isset($_POST['LANGUAGE']))
{
   $_SESSION['language'] = $_POST['LANGUAGE'];
}
include('lang/'.$_SESSION['language'].'/lang.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr" >
<head>
<meta http-equiv=Content-Type content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE_STEP5; ?></title>
<link rel="shortcut icon" href="favicon.ico" />
<style type='text/css' media='all'>@import url('includes/style.css');</style>
<script type="text/javascript" src="includes/include.js"></script>
</head>
<body>
<form action="" method="post" name="language">
<input type="hidden" name="LANGUAGE" id="lang_a" value="" />
</form> 


		<div id="content-box">
			<div id="content-pad">
				

	<div id="stepbar">
		<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">

			<h1><?php echo STEPS ;?></h1>
<div class="step-off"><a href="index.php"><?php echo START; ?></a></div>
<div class="step-off"><?php echo STEP1; ?></div>
<div class="step-off"><a><?php echo STEP2; ?></a></div>
<div class="step-off"><a><?php echo STEP3; ?></a></div>
<div class="step-off"><a><?php echo STEP4; ?></a></div>
<div class="step-on"><a><?php echo STEP5; ?></a></div>
<div class="step-off"><a><?php echo STEP6; ?></a></div>
<div class="step-off"><a><?php echo END; ?></a></div>

		<div class="box"></div>
  	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
  </div>






<div id="right">
	<div id="rightpad">
		<div id="step">
			<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
		</div>
		<div class="m">

				<div class="far-right">
					
	<div class="button1-right"><div class="prev"><a href="index.php" alt="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a></div></div>
<div class="button1-left"><div class="next"><a href="6.php"
 alt="<?php echo IMAGE_CONTINUE;?>"><?php echo IMAGE_CONTINUE;?></a></div></div>
				<?php echo lang_menu(); ?>		
				</div>
				<span class="step"><?php echo TITLE_STEP5; ?></span>
			</div>
		<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
		</div>
	</div>

	<div id="installer">
			<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
		</div>
		<div class="m">



<?php echo TEXT_WELCOME_STEP5; ?>



<?php echo TITLE_WEBSERVER_CONFIGURATION; ?>


<?php
    reset($_POST);
    while (list($key, $value) = each($_POST)) 
	{
       if ($key != 'x' && $key != 'y') 
	   {
          if (is_array($value)) 
		  {
             for ($i=0; $i<sizeof($value); $i++) 
		     {
                echo os_draw_hidden_field_installer($key . '[]', $value[$i]);
             }
          }  
	      else 
	      {
             echo os_draw_hidden_field_installer($key, $value);
          }
       }
    }
   
   // paths
   
   $dir_fs_document_root = $_POST['DIR_FS_WWW_ROOT'];
  if ((substr($dir_fs_document_root, -1) != '\\') && (substr($dir_fs_document_root, -1) != '/')) {
    if (strrpos($dir_fs_document_root, '\\') !== false) {
      $dir_fs_document_root .= '\\';
    } else {
      $dir_fs_document_root .= '/';
    }
  }

  $http_url = parse_url($_POST['WWW_ADDRESS']);
  $http_server = $http_url['scheme'] . '://' . $http_url['host'];
  $http_catalog = $http_url['path'];
  if (isset($http_url['port']) && !empty($http_url['port'])) {
    $http_server .= ':' . $http_url['port'];
  }

  if (substr($http_catalog, -1) != '/') {
    $http_catalog .= '/';
  }
   
   
    $file_contents = '<?php' . "\n" .
"#####################################". "\n" .
"#  OSC-CMS: Shopping Cart Software.". "\n" .
"#  Copyright (c) 2011-2012". "\n" .
"#  http://osc-cms.com". "\n" .
"#  http://osc-cms.com/forum". "\n" .
"#  Ver. 2.5.6". "\n" .
"#####################################". "\n". "\n" .

                     '  define(\'DB_SERVER\', \'' . trim(stripslashes($_POST['DB_SERVER'])) . '\');' . "\n" .
                     '  define(\'DB_SERVER_USERNAME\', \'' . trim(stripslashes($_POST['DB_SERVER_USERNAME'])) . '\');' . "\n" .
                     '  define(\'DB_SERVER_PASSWORD\', \'' . trim(stripslashes($_POST['DB_SERVER_PASSWORD'])). '\');' . "\n" .
                     '  define(\'DB_DATABASE\', \'' . trim(stripslashes($_POST['DB_DATABASE'])). '\');' . "\n" .
                     '  define(\'DB_PREFIX\', \'' . trim(stripslashes($_SESSION['DB_PREFIX'])). '\');' . "\n" .
                     '  define(\'USE_PCONNECT\', \'' . ((isset($_POST['USE_PCONNECT']) && $_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\');' . "\n" .
                     '  define(\'STORE_SESSIONS\', \'' . (($_POST['STORE_SESSIONS'] == 'files') ? '' : 'mysql') . '\'); ' . 
					 "\n" ."\n".'  include(\'includes/paths.php\');'.
					 "\n" .'?>';
    $fp = fopen(DIR_FS_CATALOG . 'config.php', 'w');
    fputs($fp, $file_contents);
    fclose($fp);

    
  $file_contents = ''.
'AddDefaultCharset utf-8'. "\n" .
'' . "\n" .
'RewriteEngine On' . "\n" .
'RewriteBase '.$http_catalog. "\n" .
'' . "\n" .
'RewriteRule ^product_reviews_write\.php\/info\/p(.*)_.*\.html product_reviews_write\.php\?products_id=$1 [L]'. "\n" .
'RewriteRule ^product_reviews_write\.php\/action\/process\/info\/p([0-9]*)_.*\.html product_reviews_write\.php\?action=process\&products_id=$1 [L]'. "\n" .
'' . "\n" .
'RewriteRule ^product_info\.php\/info\/p(.*)_.*\/action\/add_product product_info\.php\?products_id=$1\&action=add_product\ [L]'. "\n" .
'RewriteRule ^shopping_cart\.php\/products_id\/([0-9]*)\/info\/p([0-9]*)_.*\.html shopping_cart\.php\?products_id=$1 [L]'. "\n" .
'' . "\n" .
'RewriteRule ^(product_info|index|shop_content).php(.*)$ redirector.php [L]'. "\n" .
'' . "\n" .
'RewriteRule ^.*\.gif|\.jpg|\.png|\.css|\.php|\.js$ - [L]'. "\n" .
'RewriteRule ^(.*)$ manager.php [L]'. "\n" .
'' . "\n" .
'# PHP 5, Apache 1 and 2.'. "\n" .
'<IfModule mod_php5.c>'. "\n" .
'php_value magic_quotes_gpc                0'. "\n" .
'php_value register_globals                0'. "\n" .
'php_value session.auto_start              0'. "\n" .
'php_value mbstring.http_input             pass'. "\n" .
'php_value mbstring.http_output            pass'. "\n" .
'php_value mbstring.encoding_translation   0'. "\n" .
'php_value default_charset UTF-8'. "\n" .
'php_value mbstring.internal_encoding UTF-8'. "\n" .
'</IfModule>    '. "\n" . '';

 if (!is_file(DIR_FS_CATALOG.'.htaccess'))
 {
    $fp = @ fopen(DIR_FS_CATALOG . 'htaccess.txt', 'w');
    @ fputs($fp, $file_contents);
    @ fclose($fp);
	
   @ rename(DIR_FS_CATALOG . 'htaccess.txt', DIR_FS_CATALOG . '.htaccess');   
	
	if (is_file(DIR_FS_CATALOG . '.htaccess'))
	{
	   echo "<div class='os-ok-content'>Файл .htaccess успешно создан.</div>";
	}
	else
	{
	  echo "<div class='os-error-content'>Файл .htaccess не был создан. <b>Переименуйте /htaccess.txt в /.htaccess.</b></div>";
	}
 }   
?>

<p>
<div class='os-ok-content'>
<?php echo TEXT_WS_CONFIGURATION_SUCCESS; ?>
</div>
</p>


		
				<div class="install-body">
				

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

		
<?php echo _copy(); ?>
</body>
</html>