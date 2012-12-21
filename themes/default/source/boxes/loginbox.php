<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

$box = new osTemplate;

if (!os_session_is_registered('customer_id'))
{
	$box->assign('FORM_ACTION', '<form class="form-horizontal" id="loginbox" method="post" action="'.os_href_link(FILENAME_LOGIN, 'action=process', 'SSL').'">');
	$box->assign('FIELD_EMAIL', os_draw_input_field('email_address', '', 'id="login-email" class="input-medium"'));
	$box->assign('FIELD_PWD', os_draw_password_field('password', '', 'id="login-password" class="input-medium"'));
	
	$_array = array(
		'img' => 'button_login_small.gif',
		'href' => '',
		'alt' => TEXT_BUTTON_LOGIN,
		'code' => ''
	);
	
	$_array = apply_filter('button_login_small', $_array);	

	if (empty($_array['code']))
	{
		$_array['code'] = buttonSubmit($_array['img'], null, $_array['alt'], null, 'btn-primary btn-large');
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