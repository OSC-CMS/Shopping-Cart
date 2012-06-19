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

include ('includes/top.php');

if (!isset ($_SESSION['customer_id']))
	die;

if ((isset ($_GET['order']) && !is_numeric($_GET['order'])) || (isset ($_GET['id']) && !is_numeric($_GET['id']))) {
	die;
}

$downloads_query = os_db_query("select date_format(o.date_purchased, '%Y-%m-%d') as date_purchased_day, opd.download_maxdays, opd.download_count, opd.download_maxdays, opd.orders_products_filename from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_ORDERS_PRODUCTS_DOWNLOAD." opd where o.customers_id = '".$_SESSION['customer_id']."' and o.orders_id = '".(int) $_GET['order']."' and o.orders_id = op.orders_id and op.orders_products_id = opd.orders_products_id and opd.orders_products_download_id = '".(int) $_GET['id']."' and opd.orders_products_filename != ''");
if (!os_db_num_rows($downloads_query))
	die;
$downloads = os_db_fetch_array($downloads_query);
list ($dt_year, $dt_month, $dt_day) = explode('-', $downloads['date_purchased_day']);
$download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $downloads['download_maxdays'], $dt_year);

if (($downloads['download_maxdays'] != 0) && ($download_timestamp <= time()))
	die;
if ($downloads['download_count'] <= 0)
	die;
if (!file_exists(_DOWNLOAD.$downloads['orders_products_filename']))
	die;
os_db_query("update ".TABLE_ORDERS_PRODUCTS_DOWNLOAD." set download_count = download_count-1 where orders_products_download_id = '".(int) $_GET['id']."'");

header("Expires: Mon, 30 Nov 2009 00:00:00 GMT");
header("Last-Modified: ".gmdate("D,d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: Application/octet-stream");
header("Content-Length: ".filesize(_DOWNLOAD.$downloads['orders_products_filename']));
header("Content-disposition: attachment; filename=\"".$downloads['orders_products_filename']."\"");

if (DOWNLOAD_BY_REDIRECT == 'true') {
	os_unlink_temp_dir(_DOWNLOAD_PUBLIC);
	$tempdir = os_random_name();
	umask(0000);
	mkdir(_DOWNLOAD_PUBLIC.$tempdir, 0777);
	symlink(_DOWNLOAD.$downloads['orders_products_filename'], _DOWNLOAD_PUBLIC.$tempdir.'/'.$downloads['orders_products_filename']);
	os_redirect(_PUB.$tempdir.'/'.$downloads['orders_products_filename']);
} else {

	readfile(_DOWNLOAD.$downloads['orders_products_filename']);
}
?>