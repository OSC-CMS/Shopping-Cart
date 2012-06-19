<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  Ver. 1.0.0
#####################################
*/

$box = new osTemplate;
$box->assign('tpl_path', _HTTP_THEMES_C);
$box_content='';


  if ($_SESSION['customers_status']['customers_status_image']!='') {
    $loginboxcontent = os_image(http_path('icons_admin') . $_SESSION['customers_status']['customers_status_image']) . '<br />';
  }
  $loginboxcontent .= BOX_LOGINBOX_STATUS . '&nbsp;<b>' . $_SESSION['customers_status']['customers_status_name'] . '</b><br />';
  if ($_SESSION['customers_status']['customers_status_show_price'] == 0) {
    $loginboxcontent .= NOT_ALLOWED_TO_SEE_PRICES_TEXT;
  } else  {
    if ($_SESSION['customers_status']['customers_status_discount'] != '0.00') {
      $loginboxcontent.=BOX_LOGINBOX_DISCOUNT . ' ' . $_SESSION['customers_status']['customers_status_discount'] . '%<br />';
    }
    if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1  && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
      $loginboxcontent .= BOX_LOGINBOX_DISCOUNT_TEXT . ' '  . $_SESSION['customers_status']['customers_status_ot_discount'] . ' % ' . BOX_LOGINBOX_DISCOUNT_OT . '<br />';
    }
  }



    $box->assign('BOX_CONTENT', $loginboxcontent);
	$box->assign('language', $_SESSION['language']);
       	  // set cache ID
  if (!CacheCheck()) {
  $box->caching = 0;
  $box_infobox= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_infobox.html');
  } else {
  $box->caching = 1;
  $box->cache_lifetime=CACHE_LIFETIME;
  $box->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'];
  $box_infobox= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_infobox.html',$cache_id);
  }
    
    $osTemplate->assign('box_INFOBOX',$box_infobox);

    ?>