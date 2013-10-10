<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require('includes/top.php');
define('TABLE_AFFILIATE_BANNERS_HISTORY', DB_PREFIX.'affiliate_banners_history');
define('TABLE_AFFILIATE_BANNERS', DB_PREFIX.'affiliate_banners');
define('TABLE_PRODUCTS', DB_PREFIX.'products');

require('includes/affiliate_configure.php');

os_db_connect() or die('Unable to connect to database server!');

function affiliate_show_banner($pic) {
    $fp = fopen($pic, "rb");
    if (!$fp) exit();
    $img_type = substr($pic, strrpos($pic, ".") + 1);
    $pos = strrpos($pic, "/");
    if ($pos) {
    	$img_name = substr($pic, strrpos($pic, "/" ) + 1);
    }
	else {
		$img_name=$pic;
    }
    header ("Content-type: image/$img_type");
    header ("Content-Disposition: inline; filename=$img_name");
    fpassthru($fp);
    exit();
}

function affiliate_debug($banner,$sql) {
?>
    <table border=1 cellpadding=2 cellspacing=2>
      <tr><td colspan=2>Check the pathes! </td></tr>
      <tr><td>absolute path to picture:</td><td><?php echo http_path('images') . $banner; ?></td></tr>
      <tr><td>build with:</td><td> http_path('images') . $banner</td></tr>
      <tr><td>DIR_FS_DOCUMENT_ROOT</td><td><?php echo DIR_FS_DOCUMENT_ROOT; ?></td></tr>
      <tr><td>DIR_FS_CATALOG</td><td><?php echo DIR_FS_CATALOG ; ?></td></tr>
      <tr><td>images/</td><td><?php echo http_path('images'); ?></td></tr>
      <tr><td>$banner</td><td><?php echo $banner; ?></td></tr>
      <tr><td>SQL-Query used:</td><td><?php echo $sql; ?></td></tr>
      <tr><th>Try to find error:</td><td>&nbsp;</th></tr>
      <tr><td>SQL-Query:</td><td><?php if ($banner) echo "Got Result"; else echo "No result"; ?></td></tr>
      <tr><td>Locating Pic</td><td>
<?php 
    $pic = http_path('images') . $banner;
    echo $pic . "<br>";
    if (!is_file($pic)) {
      echo "failed<br>";
    } else {
      echo "success<br>";
    }
?>
      </td></tr>
    </table>
<?php
    exit();
}

$banner = '';
$products_id = '';
$banner_id ='';
$prod_banner_id = '';
if (isset($_GET['ref'])) $affiliate_id = $_GET['ref'];
if (isset($_POST['ref'])) $affiliate_id = $_POST['ref'];

if (isset($_GET['affiliate_banner_id'])) $banner_id = (int)$_GET['affiliate_banner_id'];
if (isset($_POST['affiliate_banner_id'])) $banner_id = (int)$_POST['affiliate_banner_id'];
if (isset($_GET['affiliate_pbanner_id'])) $prod_banner_id = (int)$_GET['affiliate_pbanner_id'];
if (isset($_POST['affiliate_pbanner_id'])) $prod_banner_id = (int)$_POST['affiliate_pbanner_id'];



if (!empty($banner_id)) {
	$is_banner = 'true';
    $sql = "select affiliate_banners_image, affiliate_products_id from " . TABLE_AFFILIATE_BANNERS . " where affiliate_banners_id = " . $banner_id  . " and affiliate_status = 1";
    $banner_values = os_db_query($sql);
    if ($banner_array = os_db_fetch_array($banner_values)) {
    	$banner = $banner_array['affiliate_banners_image'];
    	$products_id = $banner_array['affiliate_products_id'];
    }
}

if (!empty($prod_banner_id)) {
	$is_banner = 'false';
    $banner_id = 1;
    $sql = "select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $prod_banner_id  . "' and products_status = 1";
    $banner_values = os_db_query($sql);
    if ($banner_array = os_db_fetch_array($banner_values)) {
    	$banner = $banner_array['products_image'];
    	$products_id = $prod_banner_id;
    }
}

if (AFFILIATE_SHOW_BANNERS_DEBUG == 'true') affiliate_debug($banner,$sql);

if ($banner) {
	if($is_banner == 'true') {
		$pic = http_path('images') . $banner;
	}
	else {
		$pic = dir_path('images_thumbnail') . $banner;
	}

    if (is_file($pic)) {
    	$today = date('Y-m-d');
    	if ($affiliate_id) {
    		$banner_stats_query = os_db_query("select * from " . TABLE_AFFILIATE_BANNERS_HISTORY . " where affiliate_banners_id = '" . $banner_id  . "' and affiliate_banners_products_id = '" . $products_id ."' and affiliate_banners_affiliate_id = '" . $affiliate_id. "' and affiliate_banners_history_date = '" . $today . "'");
    		if ($banner_stats_array = os_db_fetch_array($banner_stats_query)) {
    			os_db_query("update " . TABLE_AFFILIATE_BANNERS_HISTORY . " set affiliate_banners_shown = affiliate_banners_shown + 1 where affiliate_banners_id = '" . $banner_id  . "' and affiliate_banners_affiliate_id = '" . $affiliate_id. "' and affiliate_banners_products_id = '" . $products_id ."' and affiliate_banners_history_date = '" . $today . "'");
    		}
			else {
          		os_db_query("insert into " . TABLE_AFFILIATE_BANNERS_HISTORY . " (affiliate_banners_id, affiliate_banners_products_id, affiliate_banners_affiliate_id, affiliate_banners_shown, affiliate_banners_history_date) VALUES ('" . $banner_id  . "', '" .  $products_id ."', '" . $affiliate_id. "', '1', '" . $today . "')");
          	}
        }
        affiliate_show_banner($pic);
    }
}

if (is_file(AFFILIATE_SHOW_BANNERS_DEFAULT_PIC)) {
	affiliate_show_banner(AFFILIATE_SHOW_BANNERS_DEFAULT_PIC);
}
else {
    affiliate_show_banner($pic); 
}
exit();
?>