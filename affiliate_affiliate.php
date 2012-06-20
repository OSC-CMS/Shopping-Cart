<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require('includes/top.php');

//$osTemplate = new osTemplate;



if (isset($_SESSION['affiliate_id'])) {
    os_redirect(os_href_link(FILENAME_AFFILIATE_SUMMARY, '', 'SSL'));
}

if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
    $affiliate_username = os_db_prepare_input($_POST['affiliate_username']);
    $affiliate_password = os_db_prepare_input($_POST['affiliate_password']);
    
    // Check if username exists
    $check_affiliate_query = os_db_query("select affiliate_id, affiliate_firstname, affiliate_password, affiliate_email_address from " . TABLE_AFFILIATE . " where affiliate_email_address = '" . os_db_input($affiliate_username) . "'");
    if (!os_db_num_rows($check_affiliate_query)) {
        $_GET['login'] = 'fail';
    }
    else {
        $check_affiliate = os_db_fetch_array($check_affiliate_query);
        // Check that password is good
        if (!os_validate_password($affiliate_password, $check_affiliate['affiliate_password'])) {
            $_GET['login'] = 'fail';
        }
        else {
            $_SESSION['affiliate_id'] = $check_affiliate['affiliate_id'];

            $date_now = date('Ymd');
            
            os_db_query("update " . TABLE_AFFILIATE . " set affiliate_date_of_last_logon = now(), affiliate_number_of_logons = affiliate_number_of_logons + 1 where affiliate_id = '" . $_SESSION['affiliate_id'] . "'");
            os_redirect(os_href_link(FILENAME_AFFILIATE_SUMMARY,'','SSL'));
        }
    }
}

$breadcrumb->add(NAVBAR_TITLE, os_href_link(FILENAME_AFFILIATE, '', 'SSL'));

require(dir_path('includes') . 'header.php');

if (isset($_GET['login']) && ($_GET['login'] == 'fail')) {
    $info_message = 'true';
}
else {
    $info_message = 'false';
}

$osTemplate->assign('info_message', $info_message);

$osTemplate->assign('FORM_ACTION', os_draw_form('login', os_href_link(FILENAME_AFFILIATE, 'action=process', 'SSL')));
$osTemplate->assign('LINK_TERMS', '<a  href="' . os_href_link(FILENAME_CONTENT,'coID=9', 'SSL') . '">');
$osTemplate->assign('INPUT_AFFILIATE_USERNAME', os_draw_input_field('affiliate_username'));
$osTemplate->assign('INPUT_AFFILIATE_PASSWORD', os_draw_password_field('affiliate_password'));
$osTemplate->assign('LINK_PASSWORD_FORGOTTEN', '<a href="' . os_href_link(FILENAME_AFFILIATE_PASSWORD_FORGOTTEN, '', 'SSL') . '">');
$osTemplate->assign('LINK_SIGNUP', button_continue(os_href_link(FILENAME_AFFILIATE_SIGNUP, '', 'SSL')));

       $_array = array('img' => 'button_login.gif', 'href' => '', 'alt' => IMAGE_BUTTON_LOGIN, 'code' => '');
	
	   $_array = apply_filter('button_login', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  os_image_submit($_array['img'], $_array['alt']);
	   }
	   
$osTemplate->assign('BUTTON_LOGIN', $_array['code']);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_affiliate.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');?>
