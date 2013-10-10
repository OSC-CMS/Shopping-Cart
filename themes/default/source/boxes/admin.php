<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$box = new osTemplate;
$box_content='';


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

    $orders_contents .= '<li><a href="'.os_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&amp;status=' . $orders_status['orders_status_id'], 'SSL') . '">' . $orders_status['orders_status_name'].'<span class="pull-right">'.(isset($_orders_status[$orders_status['orders_status_id']]) ? $_orders_status[$orders_status['orders_status_id']] : '0').'</span></a></li>';
  }
  
  //$orders_contents = substr($orders_contents, 0, -6);

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
	   $_array['code'] = '<li><a href="' . $_array['href'].'">'.$_array['alt'].'</a></li>';
	}

	
   $admin_image = $_array['code'];
   $admin_link = '';
   if ($product->isProduct()) 
   {
   		$_array = array('img' => 'edit_product.gif', 
			'href' => os_href_link_admin(FILENAME_EDIT_PRODUCTS, 'cPath=' . $cPath . '&amp;pID=' . $product->data['products_id']), 
			'alt' => IMAGE_BUTTON_PRODUCT_EDIT, 
			'code' => '');
	
	   $_array = apply_filter('button_edit_product', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  '<li><a href="' . $_array['href'] . '&amp;action=new_product' . '" onclick="window.open(this.href); return false;">' . $_array['alt'] . '</a></li>';
	   }
	   
    $admin_link = $_array['code'];
  }
  $admin_link_article = '';
   if (isset($_GET['articles_id'])) 
   {
   
     		$_array = array('img' => 'edit_article.gif', 
			'href' => os_href_link_admin('admin/'.FILENAME_ARTICLES, 'aID=' . $_GET['articles_id']), 
			'alt' => IMAGE_BUTTON_ARTICLE_EDIT, 
			'code' => '');
	
	   $_array = apply_filter('button_edit_article', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  '<li><a href="' . $_array['href'] . '&amp;action=new_article' . '" onclick="window.open(this.href); return false;">' . $_array['alt'] . '</a></li>';
	   }
	   
    $admin_link_article = $_array['code'];
  }

  
$box_content = $admin_image;
$box_content .= $admin_link;
$box_content .= $admin_link_article;
$box_content .= '<li class="divider"></li>';
$box_content .= $orders_contents;
$box_content .= '<li class="divider"></li>';
$box_content .= '<li><a href="'.os_href_link_admin('admin/customers.php').'">'.BOX_ENTRY_CUSTOMERS.' <span class="pull-right">'.$customers['count'].'</span></a></li>';
$box_content .= '<li><a href="'.os_href_link_admin('admin/categories.php').'">'.BOX_ENTRY_PRODUCTS.' <span class="pull-right">'.$products['count'].'</span></a></li>';
$box_content .= '<li><a href="'.os_href_link_admin('admin/reviews.php').'">'.BOX_ENTRY_REVIEWS.' <span class="pull-right">'.$reviews['count'].'</span></a></li>';



    $box->assign('boxTitle', IMAGE_BUTTON_ADMIN);
    $box->assign('BOX_CONTENT', $box_content);

    $box->caching = 0;
    $box->assign('language', $_SESSION['language']);
    $box_admin= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_admin.html');
    $osTemplate->assign('box_ADMIN',$box_admin);

?>