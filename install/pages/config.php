<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function step($is_submit)
{
    $root = $_SESSION['install']['paths']['root'];
    $path = $_SERVER['DOCUMENT_ROOT'].$root.'/';

    if ($is_submit)
    {
        return create_config($path);
    }

    $result = array('html' => display('config', array('path' => $path )) );

    return $result;
}

function create_config($path)
{
	$type = $_SESSION['install']['type'];

	if ($type == '1')
	{
	    if (!is_writable($path.'config.php')){
	        return array(
	            'error' => true,
	            'message' => t('config_4')
	        );
	    }
	    if (!is_writable($path.'htaccess.txt')){
	        return array(
	            'error' => true,
	            'message' => t('config_5')
	        );
	    }

		write_files($path);
	}

    return array(
        'error' => false,
    );
}

function write_files($path)
{
	// config.php
	$file_contents = '<?php'."\n".
	'define(\'DB_SERVER\', \''.trim(stripslashes($_SESSION['install']['db']['host'])).'\');'."\n".
	'define(\'DB_SERVER_USERNAME\', \''.trim(stripslashes($_SESSION['install']['db']['user'])).'\');'."\n".
	'define(\'DB_SERVER_PASSWORD\', \''.trim(stripslashes($_SESSION['install']['db']['pass'])). '\');'."\n".
	'define(\'DB_DATABASE\', \''.trim(stripslashes($_SESSION['install']['db']['base'])). '\');'."\n".
	'define(\'DB_PREFIX\', \''.trim(stripslashes($_SESSION['install']['db']['prefix'])). '\');'."\n".
	'define(\'USE_PCONNECT\', \''.'false'.'\');'."\n" .
	'define(\'STORE_SESSIONS\', \''.(($_SESSION['install']['db']['sessions'] == 'files') ? '' : 'mysql').'\'); '.
	"\n"."\n".'include(\'includes/paths.php\');'.
	"\n".'?>';

	$fp = fopen($path.'config.php', 'w');
	fputs($fp, $file_contents);
	fclose($fp);

	// htaccess
	$http_url = parse_url(WWW_LOCATION);
	$http_catalog = $http_url['path'];
	if (substr($http_catalog, -1) != '/')
	{
		$http_catalog .= '/';
	}

	$file_contents = ''.
	'AddDefaultCharset utf-8'."\n".
	'Options -Indexes'."\n".
	'RewriteEngine On'."\n".
	'RewriteBase '.$http_catalog."\n".
	''."\n".
	'RewriteRule ^product_reviews_write\.php\/info\/p(.*)_.*\.html product_reviews_write\.php\?products_id=$1 [L]'."\n".
	'RewriteRule ^product_reviews_write\.php\/action\/process\/info\/p([0-9]*)_.*\.html product_reviews_write\.php\?action=process\&products_id=$1 [L]'."\n".
	''."\n".
	'RewriteRule ^product_info\.php\/info\/p(.*)_.*\/action\/add_product product_info\.php\?products_id=$1\&action=add_product\ [L]'."\n".
	'RewriteRule ^shopping_cart\.php\/products_id\/([0-9]*)\/info\/p([0-9]*)_.*\.html shopping_cart\.php\?products_id=$1 [L]'."\n".
	''."\n".
	'RewriteRule ^(product_info|index|shop_content).php(.*)$ redirector.php [L]'."\n".
	''."\n".
	'RewriteRule ^.*\.gif|\.jpg|\.png|\.css|\.js$ - [L]'."\n".
	'RewriteRule ^(.*).html(.*)$ manager.php [L]'."\n".
	'RewriteRule ^.*\.gif|\.jpg|\.png|\.css|\.php|\.js$ - [L]'."\n".
	''."\n".
	'# PHP 5, Apache 1 and 2.'."\n".
	'<IfModule mod_php5.c>'."\n".
	'php_value magic_quotes_gpc                0'."\n".
	'php_value register_globals                0'."\n".
	'php_value session.auto_start              0'."\n".
	'php_value mbstring.http_input             pass'."\n".
	'php_value mbstring.http_output            pass'."\n".
	'php_value mbstring.encoding_translation   0'."\n".
	'php_value default_charset UTF-8'. "\n".
	'php_value mbstring.internal_encoding UTF-8'."\n".
	'</IfModule>'. "\n".'';

	if (!is_file($path.'.htaccess'))
	{
		$fp = @fopen($path.'htaccess.txt', 'w');
		@fputs($fp, $file_contents);
		@fclose($fp);
		@rename($path.'htaccess.txt', $path.'.htaccess');
	}
}