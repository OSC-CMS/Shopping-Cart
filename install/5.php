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
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TEXT_SETUP_INDEX; ?></title>
<link rel="shortcut icon" href="favicon.ico" />
<style type='text/css' media='all'>@import url('includes/install.css');</style>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<script src="bootstrap/jquery.js"></script>
<script src="bootstrap/js/bootstrap-transition.js"></script>
<script src="bootstrap/js/bootstrap-alert.js"></script>
<script src="bootstrap/js/bootstrap-modal.js"></script>
<script src="bootstrap/js/bootstrap-dropdown.js"></script>
<script src="bootstrap/js/bootstrap-scrollspy.js"></script>
<script src="bootstrap/js/bootstrap-tab.js"></script>
<script src="bootstrap/js/bootstrap-tooltip.js"></script>
<script src="bootstrap/js/bootstrap-popover.js"></script>
<script src="bootstrap/js/bootstrap-button.js"></script>
<script src="bootstrap/js/bootstrap-collapse.js"></script>
<script src="bootstrap/js/bootstrap-carousel.js"></script>
<script src="bootstrap/js/bootstrap-typeahead.js"></script>
</head>
<body>
<form action="" method="post" name="language">
<input type="hidden" name="LANGUAGE" id="lang_a" value="" />
</form>


<div class="container">
	<p></p>
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>

				<a class="brand" href=""><?php echo TEXT_SETUP_INDEX; ?></a>

				<?php echo lang_menu(); ?>
			</div>
		</div>
	</div>





	<div class="page-header">

		<span class="pull-right">
			<a class="btn" href="index.php" title="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a>
			<a class="btn btn-success" href="6.php" title="<?php echo IMAGE_CONTINUE; ?>"><?php echo IMAGE_CONTINUE;?></a>
		</span>

		<h1><?php echo STEPS ;?> <?php echo STEP5; ?></h1>
	</div>

	<div class="progress progress-striped active">
		<div class="bar" style="width:75%;"></div>
	</div>

	
	<p><h4><?php echo TITLE_STEP5; ?></h4></p>
	<hr>

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
                     'define(\'DB_SERVER\', \'' . trim(stripslashes($_POST['DB_SERVER'])) . '\');' . "\n" .
                     'define(\'DB_SERVER_USERNAME\', \'' . trim(stripslashes($_POST['DB_SERVER_USERNAME'])) . '\');' . "\n" .
                     'define(\'DB_SERVER_PASSWORD\', \'' . trim(stripslashes($_POST['DB_SERVER_PASSWORD'])). '\');' . "\n" .
                     'define(\'DB_DATABASE\', \'' . trim(stripslashes($_POST['DB_DATABASE'])). '\');' . "\n" .
                     'define(\'DB_PREFIX\', \'' . trim(stripslashes($_SESSION['DB_PREFIX'])). '\');' . "\n" .
                     'define(\'USE_PCONNECT\', \'' . ((isset($_POST['USE_PCONNECT']) && $_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\');' . "\n" .
                     'define(\'STORE_SESSIONS\', \'' . (($_POST['STORE_SESSIONS'] == 'files') ? '' : 'mysql') . '\'); ' . 
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
'RewriteRule ^.*\.gif|\.jpg|\.png|\.css|\.js$ - [L]' . "\n" .
'RewriteRule ^(.*).html(.*)$ manager.php [L]' . "\n" .
'RewriteRule ^.*\.gif|\.jpg|\.png|\.css|\.php|\.js$ - [L]' . "\n" .
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
	   echo "<div class=\"alert alert-success\">Файл .htaccess успешно создан.</div>";
	}
	else
	{
	  echo "<div class=\"alert alert-error\">Файл .htaccess не был создан. <b>Переименуйте /htaccess.txt в /.htaccess.</b></div>";
	}
 }   
?>
<div class="alert alert-success"><?php echo TEXT_WS_CONFIGURATION_SUCCESS; ?></div>






	<span class="pull-right">
		<a class="btn" href="index.php" title="<?php echo IMAGE_BACK;?>"><?php echo IMAGE_BACK;?></a>
		<a class="btn btn-success" href="6.php" title="<?php echo IMAGE_CONTINUE; ?>"><?php echo IMAGE_CONTINUE;?></a>
	</span>
	<div class="clear"></div>



	<hr>

	<footer>
		<p><?php echo _copy(); ?></p>
	</footer>
</div>


</body>
</html>