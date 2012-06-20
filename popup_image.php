<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require ('includes/top.php');
if ((int) $_GET['imgID'] == 0) {
	$products_query = os_db_query("select pd.products_name, p.products_image from ".TABLE_PRODUCTS." p left join ".TABLE_PRODUCTS_DESCRIPTION." pd on p.products_id = pd.products_id where p.products_status = '1' and p.products_id = '".(int) $_GET['pID']."' and pd.language_id = '".(int) $_SESSION['languages_id']."'");
	$products_values = os_db_fetch_array($products_query);
} else {
	$products_query = os_db_query("select pd.products_name, p.products_image, pi.image_name from ".TABLE_PRODUCTS_IMAGES." pi, ".TABLE_PRODUCTS." p left join ".TABLE_PRODUCTS_DESCRIPTION." pd on p.products_id = pd.products_id where p.products_status = '1' and p.products_id = '".(int) $_GET['pID']."' and pi.products_id = '".(int) $_GET['pID']."' and pi.image_nr = '".(int) $_GET['imgID']."' and pd.language_id = '".(int) $_SESSION['languages_id']."'");
	$products_values = os_db_fetch_array($products_query);
	$products_values['products_image'] = $products_values['image_name'];
}

$img = dir_path('images_popup').$products_values['products_image'];
$size = GetImageSize("$img");
$mo_images = os_get_products_mo_images((int) $_GET['pID']);
$img = dir_path('images_thumbnail').$products_values['products_image'];
$osize = GetImageSize("$img");
if ($mo_images != false) {
	$bheight = $osize[1];
	foreach ($mo_images as $mo_img) {
		$img = dir_path('images_thumbnail').$mo_img['image_name'];
		$mo_size = GetImageSize("$img");
		if ($mo_size[1] > $bheight)
			$bheight = $mo_size[1];
	}
	$bheight += 50;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>" /> 
<meta http-equiv="Content-Style-Type" content="text/css" />
<title><?php echo $products_values['products_name']; ?></title>
<base href="<?php echo (getenv('HTTPS') == 'on' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo 'themes/'.CURRENT_TEMPLATE.'/style.css'; ?>" />
<script type="text/javascript"><!--
var i=0;
function resize() {
  if (navigator.appName == 'Netscape') i=40;
  window.resizeTo(<?php echo $size[0] ?> +105, <?php echo $size[1] + $bheight ?>+150+i);
  self.focus();
}
//--></script>
</head>
<body onload="resize();">

<!-- os_image($src, $alt = '', $width = '', $height = '', $params = '') /-->
    
<!-- big image -->
<div class="page">
<b class="b1"></b><b class="b2"></b><b class="b3"></b><b class="b4"></b>
<div class="pagecontent">
<p class="center">
<span class="bold"><?php echo $products_values['products_name']; ?></span>
</p>
<p class="center">
<?php echo os_image(http_path('images_popup') . $products_values['products_image'], $products_values['products_name'], $size[0], $size[1]); ?>
</p>

<!-- thumbs -->
<p class="center">
<?php

if ($mo_images != false) {
?>
<iframe src="<?php echo 'show_product_thumbs.php?pID='.(int)$_GET['pID'].'&imgID='.(int)$_GET['imgID']; ?>" width="<?php echo $size[0]+40; ?>" height="<?php echo $bheight+5; ?>" border="0" frameborder="0">
    <a href="<?php echo 'show_product_thumbs.php?pID='.(int)$_GET['pID'].'&imgID='.(int)$_GET['imgID']; ?>">More Images</a>
</iframe><br />
<?php

}
?>
</p>
</div>
<b class="b4b"></b><b class="b3b"></b><b class="b2b"></b><b class="b1b"></b>
<div class="pagecontentfooter">
<a href="javascript:window.close()"><?php echo TEXT_CLOSE_WINDOW; ?></a>
</div>
</div>

</body>
</html>