<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

require ('includes/top.php');
require_once (_CLASS.'kcaptcha.php');

$captcha = new KCAPTCHA();
$_SESSION['captcha_keystring'] = $captcha->getKeyString();


?>