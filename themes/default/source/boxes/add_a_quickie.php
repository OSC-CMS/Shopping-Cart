<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  Ver. 1.0.0
#####################################
*/

  $box = new osTemplate;
  $box_content='';
  $box->assign('tpl_path', _HTTP_THEMES_C);

  $box->assign('FORM_ACTION','<form id="quick_add" method="post" action="' . os_href_link(basename($PHP_SELF), os_get_all_get_params(array('action')) . 'action=add_a_quickie', 'NONSSL') . '">');
  
  $box->assign('INPUT_FIELD',os_draw_input_field('quickie','','size="15" onkeyup="ajaxAddQuickieUp(this);" id="quick_add_quickie"'));
  $box->assign('SUBMIT_BUTTON',os_image_submit('button_add_quick.gif', BOX_HEADING_ADD_PRODUCT_ID));
  $box->assign('FORM_END','</form>');
  $box->assign('BOX_CONTENT', $box_content);
  $box->assign('language', $_SESSION['language']);
  // set cache ID
  if (!CacheCheck()) 
  {
     $box->caching = 0;
     $box_add_a_quickie= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_add_a_quickie.html');
  } 
  else 
  {
     $box->caching = 1;	
     $box->cache_lifetime=CACHE_LIFETIME;
     $box->cache_modified_check=CACHE_CHECK;
     $cache_id = $_SESSION['language'];
     $box_add_a_quickie= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_add_a_quickie.html',$cache_id);
  }
  
  $osTemplate->assign('box_ADD_QUICKIE',$box_add_a_quickie);
 ?>