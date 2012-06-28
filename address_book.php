<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

include ('includes/top.php');

if (!isset ($_SESSION['customer_id']))
os_redirect(os_href_link(FILENAME_LOGIN, '', 'SSL'));

$breadcrumb->add(NAVBAR_TITLE_1_ADDRESS_BOOK, os_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ADDRESS_BOOK, os_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));

require (dir_path('includes').'header.php');

if ($messageStack->size('addressbook') > 0)
	$osTemplate->assign('error', $messageStack->output('addressbook'));

$osTemplate->assign('ADDRESS_DEFAULT', os_address_label($_SESSION['customer_id'], $_SESSION['customer_default_address_id'], true, ' ', '<br />'));

$addresses_data = array ();
$addresses_query = os_db_query("select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $_SESSION['customer_id']."' order by firstname, lastname");
while ($addresses = os_db_fetch_array($addresses_query)) 
{
	$format_id = os_get_address_format_id($addresses['country_id']);
	
	if ($addresses['address_book_id'] == $_SESSION['customer_default_address_id']) 
	{
		$primary = 1;
	} 
	else 
	{
		$primary = 0;
	}
	
	//button delete
			$_array = array('img' => 'small_delete.gif', 
			'href' => os_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'delete='.$addresses['address_book_id'], 'SSL'), 
			'alt' => SMALL_IMAGE_BUTTON_DELETE, 
			'code' => '');
	
	   $_array = apply_filter('button_small_delete', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	   }
		
		//button edit
	$_array_insert = array('img' => 'small_edit.gif', 
			'href' => os_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'edit='.$addresses['address_book_id'], 'SSL'), 
			'alt' => SMALL_IMAGE_BUTTON_EDIT, 
			'code' => '');
	
	   $_array_insert = apply_filter('button_small_edit', $_array_insert);	
	
	   if (empty($_array_insert['code']))
 	   {
	       $_array_insert['code'] =  '<a href="'.$_array_insert['href'].'">'.os_image_button($_array_insert['img'], $_array_insert['alt']).'</a>';
	   }
		
		
	$addresses_data[] = array ('NAME' => $addresses['firstname'].' '.$addresses['lastname'], 
	'BUTTON_EDIT' => $_array_insert['code'], 
	'BUTTON_DELETE' => $_array['code'], 
	'ADDRESS' => os_address_format($format_id, $addresses, true, ' ', '<br />'), 'PRIMARY' => $primary);

}
$osTemplate->assign('addresses_data', $addresses_data);

 	$_array = array('img' => 'button_back.gif', 
	                                'href' => os_href_link(FILENAME_ACCOUNT, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_BACK,								
									'code' => ''
	);
	
	$_array = apply_filter('button_back', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}
	
$osTemplate->assign('BUTTON_BACK', $_array['code']);

if (os_count_customer_address_book_entries() < MAX_ADDRESS_BOOK_ENTRIES) 
{
      $_array = array('img' => 'button_add_address.gif', 
	                                'href' => os_href_link(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL'), 
									'alt' => IMAGE_BUTTON_CHECKOUT, 'code' => '');
									
	$_array = apply_filter('button_add_address', $_array);	
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}
	
	$osTemplate->assign('BUTTON_NEW', $_array['code']);
}

$osTemplate->assign('ADDRESS_COUNT', sprintf(TEXT_MAXIMUM_ENTRIES, MAX_ADDRESS_BOOK_ENTRIES));

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/address_book.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_ADDRESS_BOOK.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_ADDRESS_BOOK.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);
include ('includes/bottom.php');
?>