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
$box_content = '';

if (!os_session_is_registered('customer_id')) {

	$box->assign('FORM_ACTION', '<form id="loginbox" method="post" action="'.os_href_link(FILENAME_LOGIN, 'action=process', 'SSL').'">');
	$box->assign('FIELD_EMAIL', os_draw_input_field('email_address', '', 'size="15" maxlength="30"'));
	$box->assign('FIELD_PWD', os_draw_password_field('password', '', 'size="15" maxlength="30"'));
	
	$_array = array('img' => 'button_login_small.gif', 'href' => '', 'alt' => IMAGE_BUTTON_LOGIN, 'code' => '');
	
	   $_array = apply_filter('button_login_small', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  os_image_submit($_array['img'], $_array['alt']);
	   }
	   
	$box->assign('BUTTON', $_array['code']);
	
	$box->assign('LINK_LOST_PASSWORD', os_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL'));
	$box->assign('LINK_NEW_ACCOUNT', os_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
	$box->assign('FORM_END', '</form>');

	$box->assign('BOX_CONTENT', isset($loginboxcontent)?$loginboxcontent:'');

	$box->caching = 0;
	$box->assign('language', $_SESSION['language']);
	$box_loginbox = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_login.html');
	$osTemplate->assign('box_LOGIN', $box_loginbox);
}
?>