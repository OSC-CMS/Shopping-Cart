<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

  $module= new osTemplate;

  $module->assign('language', $_SESSION['language']);
  $module->assign('ERROR',$error);
  
   $_array = array('img' => 'button_back.gif', 
	                                'href' => 'javascript:history.back(1)', 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'. os_image_button($_array['img'], $_array['alt']).'</a>';
	}
	
  $module->assign('BUTTON', $_array['code']);
	
  $module->assign('language', $_SESSION['language']);

  // search field
  $module->assign('FORM_ACTION',os_draw_form('new_find', os_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get').os_hide_session_id());
  $module->assign('INPUT_SEARCH',os_draw_input_field('keywords', '', 'size="30" maxlength="30"'));
  $module->assign('BUTTON_SUBMIT', buttonSubmit('button_quick_find.gif', null, BOX_HEADING_SEARCH));
  $module->assign('LINK_ADVANCED',os_href_link(FILENAME_ADVANCED_SEARCH));
  $module->assign('FORM_END', '</form>');



  $module->caching = 0;
  $module->caching = 0;
  $module= $module->fetch(CURRENT_TEMPLATE.'/module/error_message.html');

  if (strstr($PHP_SELF, FILENAME_PRODUCT_INFO))  $product_info=$module;

  $osTemplate->assign('main_content',$module);
  
  //header('HTTP/1.1 404 Not Found');
  
?>