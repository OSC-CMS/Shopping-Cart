<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require ('includes/top.php');
require_once (_CLASS.'kcaptcha.php');

$captcha = new KCAPTCHA();
$_SESSION['captcha_keystring'] = $captcha->getKeyString();


?>