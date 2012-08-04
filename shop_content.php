<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require ('includes/top.php');
//$osTemplate = new osTemplate;


if (GROUP_CHECK == 'true') {
	$group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
}

$shop_content_query = os_db_query("SELECT
                     content_id,
                     content_title,
                     content_group,
                     content_heading,
                     content_text,
                     content_file
                     FROM ".TABLE_CONTENT_MANAGER."
                     WHERE content_group='".(int) $_GET['coID']."' ".$group_check."
                     AND languages_id='".(int) $_SESSION['languages_id']."'");
$shop_content_data = os_db_fetch_array($shop_content_query);

$shop_content_sub_pages_query = os_db_query("SELECT
                     content_id,
                     content_title,
                     content_group,
                     content_heading,
                     content_text,
                     content_file
                     FROM ".TABLE_CONTENT_MANAGER."
                     WHERE parent_id='" . $shop_content_data['content_id'] . "' ".$group_check."
                     AND languages_id='".(int) $_SESSION['languages_id']."'");

  $sub_pages_content = array();

      while ($shop_content_sub_pages_data = os_db_fetch_array($shop_content_sub_pages_query )) {

          $sub_pages_content[]=array(
              'PAGE_ID' => $shop_content_sub_pages_data['content_id'],
              'PAGE_TITLE' => $shop_content_sub_pages_data['content_title'],
              'PAGE_HEADING'      => $shop_content_sub_pages_data['content_heading'],
              'PAGE_CONTENT'    => os_date_short($one['content_text']),
              'PAGE_LINK'    => os_href_link(FILENAME_CONTENT, 'coID='.$shop_content_sub_pages_data['content_group'])
              );
			  
	
      }

  $osTemplate->assign('sub_pages_content',$sub_pages_content);

$shop_content_link = ($shop_content_data['content_url'] != '') ? $shop_content_data['content_url'] : os_href_link(FILENAME_CONTENT, 'coID='.$shop_content_data['content_group']);

$breadcrumb->add($shop_content_data['content_title'], $shop_content_link);

if ($_GET['coID'] != 7) {
	require (dir_path('includes').'header.php');
}
if ($_GET['coID'] == 7 && @$_GET['action'] == 'success') {
	require (dir_path('includes').'header.php');
}

$osTemplate->assign('CONTENT_HEADING', $shop_content_data['content_heading']);

if ($_GET['coID'] == 7) 
{
    
	$error = false;
	if (isset ($_GET['action']) && ($_GET['action'] == 'send')) {
		if ((os_validate_email(trim($_POST['email']))) && ($_POST['captcha'] == $_SESSION['captcha_keystring'])) {

			os_php_mail($_POST['email'], $_POST['name'], CONTACT_US_EMAIL_ADDRESS, CONTACT_US_NAME, CONTACT_US_FORWARDING_STRING, $_POST['email'], $_POST['name'], '', '', CONTACT_US_EMAIL_SUBJECT, nl2br($_POST['message_body']), $_POST['message_body']);

			if (!isset ($mail_error)) {
				os_redirect(os_href_link(FILENAME_CONTENT, 'action=success&coID='.(int) $_GET['coID']));
			} else {
				$osTemplate->assign('error_message', $mail_error);

			}
		} else {
			$osTemplate->assign('error_message', ERROR_MAIL);
			$error = true;
		}

	}

	$osTemplate->assign('CONTACT_HEADING', $shop_content_data['content_title']);
	if (isset ($_GET['action']) && ($_GET['action'] == 'success')) {
		$osTemplate->assign('success', '1');
	    $osTemplate->assign('BUTTON_CONTINUE', button_continue());

	} else {
		if ($shop_content_data['content_file'] != '') 
		{
			ob_start();
			    $file_name = basename ($shop_content_data['content_file']);
    		    $isTextFile = strpos($file_name, '.txt');
			    if ($isTextFile) echo '';
			    include (DIR_FS_CATALOG.'media/content/'.$shop_content_data['content_file']);
			    if ($isTextFile) echo '';
		        $contact_content = ob_get_contents();
		    ob_end_clean();
		} 
		else 
		{
			$contact_content = $shop_content_data['content_text'];
		}
		require (dir_path('includes').'header.php');
		$osTemplate->assign('CONTACT_CONTENT', $contact_content);
		
		$osTemplate->assign('FORM_ACTION', os_draw_form('contact_us', os_href_link(FILENAME_CONTENT, 'action=send&coID='.(int) $_GET['coID'])));
		$osTemplate->assign('INPUT_NAME', os_draw_input_field('name', ($error ? $_POST['name'] : @$first_name), 'id="customer_name"'));
		$osTemplate->assign('INPUT_EMAIL', os_draw_input_field('email', ($error ? $_POST['email'] : @$email_address), 'id="email_address"'));
		$osTemplate->assign('INPUT_TEXT', os_draw_textarea_field('message_body', 'soft', '', '', @$_POST[''],''), 'id="message_body"');
		$osTemplate->assign('CAPTCHA_IMG', '<img src="'.FILENAME_DISPLAY_CAPTCHA.'" alt="captcha" name="captcha" />');    
		$osTemplate->assign('CAPTCHA_INPUT', os_draw_input_field('captcha', '', 'maxlength="6" id="captcha"', 'text', false));
		$osTemplate->assign('BUTTON_SUBMIT', button_continue_submit());
		$osTemplate->assign('FORM_END', '</form>');
	}

	$osTemplate->assign('language', $_SESSION['language']);

	$osTemplate->caching = 0;
	$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/contact_us.html');

} 
else 
{
    $content_body = '';
	if ($shop_content_data['content_file'] != '') 
	{
		ob_start();
		    $file_name = basename ($shop_content_data['content_file']);
		    $isTextFile = strpos($file_name, '.txt');
		    if ($isTextFile) echo '';
		    include (DIR_FS_CATALOG.'media/content/'.$shop_content_data['content_file']);
		    if ($isTextFile) echo '';
		    $osTemplate->assign('file', ob_get_contents());
		ob_end_clean();

	} 
	else 
	{
		$content_body = $shop_content_data['content_text'];
	}
	
	$shop_content = array();
	$shop_content['content_body']  = $content_body;
	$shop_content['content_id']  = $_GET['coID'];
	
	$shop_content['content_heading']  = $shop_content_data['content_heading'];
	
	$shop_content = apply_filter('shop_content', $shop_content);
	
	$osTemplate->assign('CONTENT_HEADING', $shop_content['content_heading']);
	$osTemplate->assign('CONTENT_BODY', $shop_content['content_body']);
  
	$_array = array('img' => 'button_back.gif', 
	                                'href' => 'javascript:history.back(1)', 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], "javascript:history.back(1)", $_array['alt']);
	}
	
	$osTemplate->assign('BUTTON_CONTINUE', $_array['code']);
	
	$osTemplate->assign('language', $_SESSION['language']);
	 if (!CacheCheck()) {
		$osTemplate->caching = 0;
		$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/content.html');
	} else {
		$osTemplate->caching = 1;
		$osTemplate->cache_lifetime = CACHE_LIFETIME;
		$osTemplate->cache_modified_check = CACHE_CHECK;
		$cache_id = $_SESSION['language'].$shop_content_data['content_id'];
		$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/content.html', $cache_id);
	}

}

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_CONTENT.'_'.$_GET['coID'].'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_CONTENT.'_'.$_GET['coID'].'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>