<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

$_www_location = 'http://' . $_SERVER['HTTP_HOST'];

if (isset($_SERVER['REQUEST_URI']) && (empty($_SERVER['REQUEST_URI']) === false)) 
{
	$_www_location .= $_SERVER['REQUEST_URI'];
}
else 
{
	$_www_location .= $_SERVER['SCRIPT_FILENAME'];
}


if ( is_file( dirname(dirname(dirname(__FILE__))).'/'.'VERSION' ) )
{
	$_version = @ file_get_contents ( dirname(dirname(dirname(__FILE__))).'/'.'VERSION' );
	$_var_array = explode('/', $_version);
	//ревизия
	$_rev = $_var_array[2];
	//версия
	$_version = $_var_array[1];;
}
else
{
	$_version = '1.0.0';
}

$_www_location = substr($_www_location, 0, strpos($_www_location, 'install'));
$dir = dirname(__FILE__) . '/../../';
$_dir_fs_www_root = str_replace('\\', '/', realpath($dir)).'/';

define('_VALID_OS', 'true');

if (!defined('_LANG')) define('_LANG', $_dir_fs_www_root."langs/");
if (!defined('DIR_WS_LANGUAGES')) define('DIR_WS_LANGUAGES',$_dir_fs_www_root."langs/");
//print(_CATALOG);
if (!defined('_CATALOG')) define('_CATALOG',$_dir_fs_www_root);
if (!defined('DIR_FS_CATALOG')) define('DIR_FS_CATALOG',$_dir_fs_www_root);

require(_CATALOG.'includes/classes/message_stack.php');
require(_CATALOG.'includes/default.php');
require(_CATALOG.'includes/filenames.php');

require(dirname(__FILE__).'/func.php');

session_start();
@ header("Content-Type: text/html; charset=utf-8"); 
@ error_reporting(E_ALL & ~E_NOTICE);

if (!defined('_ICONS')) define('_ICONS','images/');

function os_check_version($mini='5.2')
{
	$dummy=phpversion();
	sscanf($dummy,"%d.%d.%d%s",$v1,$v2,$v3,$v4);
	sscanf($mini,"%d.%d.%d%s",$m1,$m2,$m3,$m4);
	if($v1>$m1) return(1);	
	elseif($v1<$m1) return(0);
	if($v2>$m2) return(1);
	elseif($v2<$m2) return(0);
	if($v3>$m3) return(1);
	elseif($v3<$m3) return(0);
	if((!$v4)&&(!$m4)) return(1);
	if(($v4)&&(!$m4))
	{
		$dummy=strpos($v4,"pl");
		if(is_integer($dummy)) return(1);
		return(0);
	}
	elseif((!$v4)&&($m4))
	{
		$dummy=strpos($m4,"rc");
		if(is_integer($dummy)) return(1);
		return(0);
	}
	return(0);
}

function _copy()
{
	echo '&copy; '.date('Y').' <a href="http://osc-cms.com" title="Купить интернет-магазин OSC-CMS">OSC-CMS</a></center>';
}

function lang_menu() //Панель с языками
{
	//return $text;
}

function lang_menu_index() //Панель с языками для index.php
{
	$langs = get_install_langs();
	$text = '<div class="pull-right lang_menu" id="lang_menu">';
	foreach($langs as $lang)
	{
		if ($_SESSION['language'] == $lang)
		{
			$text .= '<a href="javascript:;" onclick="document.getElementById(\'lang_a\').value=\''.$lang.'\';document.getElementById(\'action\').value=\'\';document.language.submit();" title="'.$lang.'"><img src="lang/'.$lang.'/lang.gif" border="0" /></a>';
		}
		else
		{
			$text .= '<a href="javascript:;" onclick="document.getElementById(\'lang_a\').value=\''.$lang.'\';document.getElementById(\'action\').value=\'\';document.language.submit();" title="'.$lang.'"><img src="lang/'.$lang.'/lang.gif" border="0" /></a>';
		}
	}
	$text .= '</div>';
	return $text;
}

function get_install_langs() //Список языков установщика
{
	if ($dir = opendir(_CATALOG.'install/lang/')) 
	{
		while (($langs = readdir($dir)) !== false) 
		{
			if (is_dir(_CATALOG.'install/lang/'."//".$langs) && $langs != "." && $langs != ".." && $langs != ".svn") 
			{
				$langs_array[] = $langs;
			}
		}
		closedir($dir);
		rsort($langs_array);
	}

	return $langs_array;
}

?>