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

  reset($_GET);
  while (list($key, ) = each($_GET)) {
    switch ($key) {
      case 'banner':
        $banners_id = os_db_prepare_input($_GET['banner']);

        $banner_query = os_db_query("select banners_title, banners_image, banners_html_text from " . TABLE_BANNERS . " where banners_id = '" . os_db_input($banners_id) . "'");
        $banner = os_db_fetch_array($banner_query);

        $page_title = $banner['banners_title'];

        if ($banner['banners_html_text']) {
          $image_source = $banner['banners_html_text'];
        } elseif ($banner['banners_image']) {
          $image_source = os_image(http_path('images') . 'banner/' . $banner['banners_image'], $page_title);
        }
        break;
    }
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<?php $main->favicon();?>
<title><?php echo $page_title; ?></title>
<script type="text/javascript"><!--
var i=0;

function resize() {
  if (navigator.appName == 'Netscape') i = 40;
  window.resizeTo(document.images[0].width + 30, document.images[0].height + 60 - i);
}
//--></script>
</head>
<body onload="resize();">
<?php echo $image_source; ?>
</body>
</html>