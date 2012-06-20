<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

define('AJAX_APPLICATION_RUNNING', true);

require('includes/classes/JsHttpRequest.php');

unset($JsHttpRequest);
$JsHttpRequest = new JsHttpRequest('');
require('includes/top.php');
$JsHttpRequest->setEncoding($_SESSION['language_charset']);

if (!isset($_GET['ajax_page']) || !os_not_null($_GET['ajax_page']) || !is_file(_MODULES . 'ajax/' . $_GET['ajax_page'] . '.php')) die('***ERROR*** Ajax page "' . $_GET['ajax_page'] . '" not define or not exist!!!');
if(is_file(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . $_GET['ajax_page'] . '.php'))
require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . $_GET['ajax_page'] . '.php');
require(_MODULES . 'ajax/' . $_GET['ajax_page'] . '.php');
exit;
?>