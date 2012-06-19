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

if (!isset($lng) && @!is_object($lng)) 
{
    include(_CLASS . 'language.php');
    $lng = new language;
}

  $languages_string = '';
  $count_lng='';
  reset($lng->catalog_languages);

while (list($key, $value) = each($lng->catalog_languages)) 
{
  $count_lng++;
  if ($value['status'] == 1) //Показывать только активные языки
  {
    $languages_string .=  ' <a href="' . os_href_link(basename($PHP_SELF), 'language=' . $key.'&'.os_get_all_get_params(array('language', 'currency')), $request_type) . '">' . $value['name'] . '</a> '; 
  }
}

  // dont show box if there's only 1 language
  if ($count_lng > 1 ) {

 $box = new osTemplate;
 $box->assign('tpl_path', _HTTP_THEMES_C); 
 $box_content='';
 $box->assign('BOX_CONTENT', '<center>'.$languages_string.'</center>');
 $box->assign('language', $_SESSION['language']);


    	  // set cache ID

  $box->caching = 0;
  $box_languages= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_languages.html');
  	

    $osTemplate->assign('box_LANGUAGES',$box_languages);
  }
   ?>