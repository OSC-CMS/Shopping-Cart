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

  require('includes/top.php');
  require_once(dir_path('class').'JsHttpRequest.php');
  
  unset($JsHttpRequest);
  
  $JsHttpRequest = new JsHttpRequest('');
  foreach( $_REQUEST as $key => $value) $_POST[$key]=$value;
  $JsHttpRequest->setEncoding($_SESSION['language_charset']);

  require(dir_path('themes_c').'source/boxes/shopping_cart.php');
  
  echo $box_shopping_cart;
?>