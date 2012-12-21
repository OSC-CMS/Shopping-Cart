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

$module = new osTemplate;


if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
        $orders_query = os_db_query("select orders_id, orders_status from ".TABLE_ORDERS." where customers_id = '".$_SESSION['customer_id']."' order by orders_id desc limit 1");
        $orders = os_db_fetch_array($orders_query);
        $last_order = $orders['orders_id'];
        $order_status = $orders['orders_status'];
} else {
        $last_order = (int)$_GET['order_id'];
        $orders_query = os_db_query("SELECT orders_status FROM ".TABLE_ORDERS." WHERE orders_id = '".$last_order."'");
        $orders = os_db_fetch_array($orders_query);
        $order_status = $orders['orders_status'];
}
if ($order_status < DOWNLOAD_MIN_ORDERS_STATUS) {
        $module->assign('dl_prevented', 'true');
}
$downloads_query = os_db_query("select date_format(o.date_purchased, '%Y-%m-%d') as date_purchased_day, opd.download_maxdays, op.products_name, opd.orders_products_download_id, opd.orders_products_filename, opd.download_count, opd.download_maxdays from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_ORDERS_PRODUCTS_DOWNLOAD." opd where o.customers_id = '".$_SESSION['customer_id']."' and o.orders_id = '".$last_order."' and o.orders_id = op.orders_id and op.orders_products_id = opd.orders_products_id and opd.orders_products_filename != ''");
if (os_db_num_rows($downloads_query) > 0) {
        $jj = 0;
        while ($downloads = os_db_fetch_array($downloads_query)) {
                list ($dt_year, $dt_month, $dt_day) = explode('-', $downloads['date_purchased_day']);
                $download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $downloads['download_maxdays'], $dt_year);
                $download_expiry = date('Y-m-d H:i:s', $download_timestamp);
                if (($downloads['download_count'] > 0) &&
                (file_exists(_DOWNLOAD.$downloads['orders_products_filename'])) && 
                        (($downloads['download_maxdays'] == 0) || ($download_timestamp > time())) && 
                        ($order_status >= DOWNLOAD_MIN_ORDERS_STATUS)) 
                {
                        $dl[$jj]['download_link'] = '<a href="'.os_href_link(FILENAME_DOWNLOAD, 'order='.$last_order.'&id='.$downloads['orders_products_download_id']).'">'.$downloads['products_name'].'</a>';
                        $dl[$jj]['pic_link'] = os_href_link(FILENAME_DOWNLOAD, 'order='.$last_order.'&id='.$downloads['orders_products_download_id']);
                } else {
                        $dl[$jj]['download_link'] = $downloads['products_name'];
                }
                $dl[$jj]['date'] = os_date_long($download_expiry);
                $dl[$jj]['count'] = $downloads['download_count'];
                $jj ++;
        }
}
$module->assign('dl', $dl);
$module->assign('language', $_SESSION['language']);

$module->caching = 0;
$module = $module->fetch(CURRENT_TEMPLATE.'/module/downloads.html');
$osTemplate->assign('downloads_content', $module);
?>