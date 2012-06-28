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

require_once(_LIB . 'phpmailer/class.phpmailer.php');

if (isset($_GET['action']) && ($_GET['action'] == 'process')) 
{
	$check_affiliate_query = os_db_query("select affiliate_firstname, affiliate_lastname, affiliate_password, affiliate_id from " . TABLE_AFFILIATE . " where affiliate_email_address = '" . $_POST['email_address'] . "'");
    if (os_db_num_rows($check_affiliate_query)) 
	      {
    	     $check_affiliate = os_db_fetch_array($check_affiliate_query);
    	     // Crypted password mods - create a new password, update the database and mail it to them
    	     $newpass = os_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
    	     $crypted_password = os_encrypt_password($newpass);
    	     os_db_query("update " . TABLE_AFFILIATE . " set affiliate_password = '" . $crypted_password . "' where affiliate_id = '" . $check_affiliate['affiliate_id'] . "'");
    	
    	     os_php_mail(AFFILIATE_EMAIL_ADDRESS, STORE_OWNER, $_POST['email_address'], $check_affiliate['affiliate_firstname'] . " " . $check_affiliate['affiliate_lastname'], '', AFFILIATE_EMAIL_ADDRESS, STORE_OWNER, '', '', EMAIL_PASSWORD_REMINDER_SUBJECT, nl2br(sprintf(EMAIL_PASSWORD_REMINDER_BODY, $newpass)), nl2br(sprintf(EMAIL_PASSWORD_REMINDER_BODY, $newpass)));
             if (!isset($mail_error)) 
			    {
                   os_redirect(os_href_link(FILENAME_AFFILIATE, 'info_message=' . urlencode(TEXT_PASSWORD_SENT), 'SSL', true, false));
                }
             else 
		        {
                   echo $mail_error;
                }
          }
	   else 
	      {
		      os_redirect(os_href_link(FILENAME_AFFILIATE_PASSWORD_FORGOTTEN, 'email=nonexistent', 'SSL'));
          }
   }
else 
   {
	   $breadcrumb->add(NAVBAR_TITLE, os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
	   $breadcrumb->add(NAVBAR_TITLE_PASSWORD_FORGOTTEN, os_href_link(FILENAME_AFFILIATE_PASSWORD_FORGOTTEN, '', 'SSL'));

	   require(dir_path('includes') . 'header.php');

	   $osTemplate->assign('FORM_ACTION', os_draw_form('password_forgotten', os_href_link(FILENAME_AFFILIATE_PASSWORD_FORGOTTEN, 'action=process', 'SSL')));
	   $osTemplate->assign('INPUT_EMAIL', os_draw_input_field('email_address', '', 'maxlength="96"'));
	   
	   	$_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link(FILENAME_AFFILIATE, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="' . $_array['href'] . '">' . os_image_button($_array['img'], $_array['alt']) . '</a>';
	}
	
	   $osTemplate->assign('LINK_AFFILIATE', $_array['code']);
	   
	   $osTemplate->assign('BUTTON_SUBMIT', button_continue_submit());
	
	   if (isset($_GET['email']) && ($_GET['email'] == 'nonexistent')) 
	   {
		   $osTemplate->assign('email_nonexistent', 'true');
	   }
   }
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_password_forgotten.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');

?>
