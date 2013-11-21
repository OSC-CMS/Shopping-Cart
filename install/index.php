<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

error_reporting(1);

session_start();

define('DS', DIRECTORY_SEPARATOR);
define('PATH', dirname(__FILE__).DS);
define('ROOT_PATH', dirname(dirname(__FILE__)).DS);

header("Content-type:text/html; charset=utf-8");
mb_internal_encoding('UTF-8');

$default_lang = 'ru';

if (isset($_REQUEST['lang']))
{
	$_SESSION['install']['lang'] = $_REQUEST['lang'];
	header('Location: '.$_SERVER['SCRIPT_NAME']);
}

$lang = ($_SESSION['install']['lang']) ? $_SESSION['install']['lang'] : $default_lang;
define('LANG', $lang);

$_www_location = 'http://'.$_SERVER['HTTP_HOST'];

if (isset($_SERVER['REQUEST_URI']) && (empty($_SERVER['REQUEST_URI']) === false))
	$_www_location .= $_SERVER['REQUEST_URI'];
else
	$_www_location .= $_SERVER['SCRIPT_FILENAME'];

$_www_location = substr($_www_location, 0, strpos($_www_location, 'install'));
define('WWW_LOCATION', $_www_location);

// пишем настройки БД в сессию
if (isset($_POST['db']) && !empty($_POST['db']))
{
	unset($_SESSION['install']['db']);
	$_SESSION['install']['db'] = $_POST['db'];
}

if (isset($_SESSION['install']['db']))
	define('DB_PREFIX', $_SESSION['install']['db']['prefix']);
else
	define('DB_PREFIX', 'cet_');

include PATH.DS.'languages'.DS.LANG.DS."language.php";
include PATH."functions.php";

$steps = array(
	array('id' => 'start', 'title' => t('step_1')),
	array('id' => 'license', 'title' => t('step_2')),
	array('id' => 'dir', 'title' => t('step_3')),
	array('id' => 'php', 'title' => t('step_4')),
	array('id' => 'database', 'title' => t('step_5')),
	array('id' => 'admin', 'title' => t('step_6')),
	array('id' => 'config', 'title' => t('step_7')),
	array('id' => 'finish', 'title' => t('step_8'))
);

$current_step = 0;

if (is_ajax_request())
{
	usleep(250000);
	$step = $steps[$_POST['step']];
	$is_submit = isset($_POST['submit']);
	echo json_encode(run_step($step, $is_submit));
	exit();
}

$step_result = run_step($steps[$current_step], false);

echo display('index', array(
	'steps' => $steps,
	'is_lang_selected' => $is_lang_selected,
	'langs' => get_langs(),
	'current_step' => $current_step,
	'step_html' => $step_result['html']
));
