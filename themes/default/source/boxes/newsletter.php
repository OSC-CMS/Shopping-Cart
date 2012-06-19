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

$box = new osTemplate;
$box->assign('tpl_path', _HTTP_THEMES_C); 
$box_content='';


$box->assign('FORM_ACTION', os_draw_form('sign_in', os_href_link(FILENAME_NEWSLETTER, '', 'NONSSL')));
$box->assign('FIELD_EMAIL',os_draw_input_field('email', '', 'size="15" maxlength="50"'));

       $_array = array('img' => 'button_add_quick.gif', 'href' => '', 'alt' => IMAGE_BUTTON_LOGIN, 'code' => '');
	
	   $_array = apply_filter('button_login_small', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  os_image_submit($_array['img'], $_array['alt']);
	   }
	   
$box->assign('BUTTON', $_array['code']);

$box->assign('FORM_END','</form>');
	$box->assign('language', $_SESSION['language']);
       	  // set cache ID
   if (!CacheCheck()) {
  $box->caching = 0;
  $box_newsletter= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_newsletter.html');
  } else {
  $box->caching = 1;	
  $box->cache_lifetime=CACHE_LIFETIME;
  $box->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'];
  $box_newsletter= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_newsletter.html',$cache_id);
  }

    $osTemplate->assign('box_NEWSLETTER',$box_newsletter);
?>