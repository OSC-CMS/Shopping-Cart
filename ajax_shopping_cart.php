<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
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