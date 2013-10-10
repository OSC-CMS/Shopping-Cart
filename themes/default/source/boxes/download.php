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

  if (!strstr($_SERVER['SCRIPT_NAME'], FILENAME_ACCOUNT_HISTORY_INFO) && isset($_SESSION['customer_id'])) 
  {
    $orders_query_raw = "SELECT orders_id FROM " . TABLE_ORDERS . " WHERE customers_id = '" . $_SESSION['customer_id'] . "' ORDER BY orders_id DESC LIMIT 1";
    $orders_query = osDBquery($orders_query_raw);
    $orders_values = os_db_fetch_array($orders_query,true);
    $last_order = $orders_values['orders_id'];
  } 
  else 
  {
    $last_order = isset($_GET['order_id'])? $_GET['order_id']:'';
  }

   if (isset($_SESSION['customer_id']))
   {
  $downloads_query_raw = "SELECT DATE_FORMAT(date_purchased, '%Y-%m-%d') as date_purchased_day, opd.download_maxdays, op.products_name, opd.orders_products_download_id, opd.orders_products_filename, opd.download_count, opd.download_maxdays
                          FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " opd
                          WHERE customers_id = '" . $_SESSION['customer_id'] . "'
                          AND o.orders_id = '" . $last_order . "'
                          AND o.orders_status >= " . DOWNLOAD_MIN_ORDERS_STATUS . "
                          AND op.orders_id = '" . $last_order . "'
                          AND opd.orders_products_id=op.orders_products_id
                          AND opd.orders_products_filename<>''";
  $downloads_query = osDBquery($downloads_query_raw);
  
// Don't display if there is no downloadable product
  if (os_db_num_rows($downloads_query,true) > 0) {

    while ($downloads_values = os_db_fetch_array($downloads_query,true)) {

// MySQL 3.22 does not have INTERVAL
    	list($dt_year, $dt_month, $dt_day) = explode('-', $downloads_values['date_purchased_day']);
    	$download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $downloads_values['download_maxdays'], $dt_year);
  	    $download_expiry = date('Y-m-d H:i:s', $download_timestamp);


      if (($downloads_values['download_count'] > 0) &&
          (file_exists(_DOWNLOAD . $downloads_values['orders_products_filename'])) &&
          (($downloads_values['download_maxdays'] == 0) ||
           ($download_timestamp > time()))) {

$box_content .= BOX_TEXT_DOWNLOAD . '<br /><br /><a href="' . os_href_link(FILENAME_DOWNLOAD, 'order=' . $last_order . '&id=' . $downloads_values['orders_products_download_id']) . '">' . $downloads_values['products_name'] . '</a><br /><a href="' . os_href_link(FILENAME_DOWNLOAD, 'order=' . $last_order . '&id=' . $downloads_values['orders_products_download_id']) . '"><span class="Requirement"><strong>' . BOX_TEXT_DOWNLOAD_NOW . '</strong></span></a><br /><br />';
      } else {

$box_content .= $downloads_values['products_name'];

      }

$box_content .= TABLE_HEADING_DOWNLOAD_DATE . os_date_short($download_expiry) . '<br />';

$box_content .= TABLE_HEADING_DOWNLOAD_COUNT . $downloads_values['download_count'] . '<br /><br />';

 }

  }
if (!strstr($_SERVER['SCRIPT_NAME'], FILENAME_ACCOUNT_HISTORY_INFO)) {

$box_content .= TEXT_FOOTER_DOWNLOAD . '<a href="' . os_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . TEXT_DOWNLOAD_MY_ACCOUNT . '</a>';

   }

$box->assign('BOX_CONTENT', $box_content);

$box->caching = 0;
$box->assign('language', $_SESSION['language']);
$box_download= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_download.html');
$osTemplate->assign('box_DOWNLOADS',$box_download);

}

// /downloads

?>