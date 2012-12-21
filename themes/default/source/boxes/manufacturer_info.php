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
$box_content='';


    $manufacturer_query = osDBquery("select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url, m.manufacturers_page_url from " . TABLE_MANUFACTURERS . " m left join " . TABLE_MANUFACTURERS_INFO . " mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$_SESSION['languages_id'] . "'), " . TABLE_PRODUCTS . " p  where p.products_id = '" . $product->data['products_id'] . "' and p.manufacturers_id = m.manufacturers_id");
    if (os_db_num_rows($manufacturer_query,true)) {
      $manufacturer = os_db_fetch_array($manufacturer_query,true);

      $image='';
      if (os_not_null($manufacturer['manufacturers_image'])) $image=http_path('images') . $manufacturer['manufacturers_image'];
      $box->assign('IMAGE',$image);
      $box->assign('NAME',$manufacturer['manufacturers_name']);
      
        if ($manufacturer['manufacturers_url'] != '')
			$box->assign('URL','<a href="' . os_href_link(FILENAME_REDIRECT, 'action=manufacturer&'.os_manufacturer_link($manufacturer['manufacturers_id'],$manufacturer['manufacturers_name'])) . '" onclick="window.open(this.href); return false;">' . sprintf(BOX_MANUFACTURER_INFO_HOMEPAGE, $manufacturer['manufacturers_name']) . '</a>');

		if ($manufacturer['manufacturers_page_url'] != '')
			$box->assign('LINK_MORE','<a href="' . os_href_link($manufacturer['manufacturers_page_url']) . '">' . BOX_MANUFACTURER_INFO_OTHER_PRODUCTS . '</a>');
		else
			$box->assign('LINK_MORE','<a href="' . os_href_link(FILENAME_DEFAULT, os_manufacturer_link($manufacturer['manufacturers_id'],$manufacturer['manufacturers_name'])) . '">' . BOX_MANUFACTURER_INFO_OTHER_PRODUCTS . '</a>');

    }
  



 	$box->assign('language', $_SESSION['language']);
    	  // set cache ID
   if (!CacheCheck()) {
  $box->caching = 0;
  $box_manufacturers_info= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_manufacturers_info.html');
  } else {
  $box->caching = 1;	
  $box->cache_lifetime=CACHE_LIFETIME;
  $box->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$product->data['products_id'];
  $box_manufacturers_info= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_manufacturers_info.html',$cache_id);
  }
    if ($manufacturer['manufacturers_name']!='')  $osTemplate->assign('box_MANUFACTURERS_INFO',$box_manufacturers_info);
    
?>