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
$box_content='';
$flag='';
$box->assign('tpl_path', _HTTP_THEMES_C);

  $orders_contents = '';
  
  $orders_status_query = osDBquery("select orders_status_name, orders_status_id from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$_SESSION['languages_id'] . "'");
 
  $orders_pending_query = osDBquery("select orders_status, count(*) as count from " . TABLE_ORDERS . " group by orders_status");
  $_orders_status = '';
  
  while ($orders_pending = os_db_fetch_array($orders_pending_query,true))
  {
     $_orders_status[$orders_pending['orders_status']] = $orders_pending['count'];
  }
   
 
 while ($orders_status = os_db_fetch_array($orders_status_query,true)) 
  {

    $orders_contents .= '<a href="' . os_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&amp;status=' . $orders_status['orders_status_id'], 'SSL') . '">' . $orders_status['orders_status_name'] . '</a>: ' . (isset($_orders_status[$orders_status['orders_status_id']])? $_orders_status[$orders_status['orders_status_id']]:'0') . '<br />';
  }
  
  $orders_contents = substr($orders_contents, 0, -6);

  $customers_query = osDBquery("select count(*) as count from " . TABLE_CUSTOMERS);
  $customers = os_db_fetch_array($customers_query,true);
  $products_query = osDBquery("select count(*) as count from " . TABLE_PRODUCTS . " where products_status = '1'");
  $products = os_db_fetch_array($products_query,true);
  $reviews_query = osDBquery("select count(*) as count from " . TABLE_REVIEWS);
  $reviews = os_db_fetch_array($reviews_query,true);
  
  			//кнопка
	$_array = array('img' => 'button_admin.gif', 
	                                'href' => os_href_link_admin(FILENAME_START,'', 'SSL'), 
									'alt' => IMAGE_BUTTON_ADMIN,								
									'code' => ''
	);
	
	$_array = apply_filter('button_admin', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="' . $_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}

	
   $admin_image = '<p class="LoginContentLeft">'.$_array['code'].'</p>';
   
   if ($product->isProduct()) 
   {
   		$_array = array('img' => 'edit_product.gif', 
			'href' => os_href_link_admin(FILENAME_EDIT_PRODUCTS, 'cPath=' . $cPath . '&amp;pID=' . $product->data['products_id']), 
			'alt' => IMAGE_BUTTON_PRODUCT_EDIT, 
			'code' => '');
	
	   $_array = apply_filter('button_edit_product', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  '<a href="' . $_array['href'] . '&amp;action=new_product' . '" onclick="window.open(this.href); return false;">' . os_image_button($_array['img'], $_array['alt']) . '</a>';
	   }
	   
    $admin_link='<p class="LoginContentLeft">'.$_array['code'].'</p>';
  }

   if (isset($_GET['articles_id'])) 
   {
   
     		$_array = array('img' => 'edit_article.gif', 
			'href' => os_href_link_admin('admin/'.FILENAME_ARTICLES, 'aID=' . $_GET['articles_id']), 
			'alt' => IMAGE_BUTTON_ARTICLE_EDIT, 
			'code' => '');
	
	   $_array = apply_filter('button_edit_article', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  '<a href="' . $_array['href'] . '&amp;action=new_article' . '" onclick="window.open(this.href); return false;">' . os_image_button($_array['img'], $_array['alt']) . '</a>';
	   }
	   
    $admin_link_article='<p class="LoginContentLeft">'.$_array['code'].'</p>';
  }

  
  $box_content= '<b>' . BOX_TITLE_STATISTICS . '</b><br />' . $orders_contents . '<br />' .
                                         BOX_ENTRY_CUSTOMERS . ' ' . $customers['count'] . '<br />' .
                                         BOX_ENTRY_PRODUCTS . ' ' . $products['count'] . '<br />' .
                                         BOX_ENTRY_REVIEWS . ' ' . $reviews['count'] .'<br />' .
                                         $admin_image . '<br />' .@$admin_link.@$admin_link_article;

    if ($flag==true) define('SEARCH_ENGINE_FRIENDLY_URLS',true);
    $box->assign('BOX_CONTENT', $box_content);

    $box->caching = 0;
    $box->assign('language', $_SESSION['language']);
    $box_admin= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_admin.html');
    $osTemplate->assign('box_ADMIN',$box_admin);

?>