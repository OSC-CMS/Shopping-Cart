<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

  $dir = dirname(__FILE__);
  
  $_file_name = $_GET['name'];
  $_file_name = os_check_file_name($_file_name); /* удаляем лишние символы из имени файла*/
  $_file_name = str_replace('/','',$_file_name);
  $_file_name = $dir.'/'.'sql/'. $_file_name.'_'.os_check_file_name($_GET['param']).'.php';
  
  if (is_file($_file_name)) include($_file_name);

  header('Location: '.os_check_file_name($_GET['name']).'.php?gID='.os_check_file_name($_GET['param']));
?>