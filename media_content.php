<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

require ('includes/top.php');

$content_query = os_db_query("SELECT
	 				content_name,
	 				content_read,
 					content_file
 					FROM ".TABLE_PRODUCTS_CONTENT."
 					WHERE content_id='".(int) $_GET['coID']."'");
$content_data = os_db_fetch_array($content_query);

os_db_query("UPDATE 
			".TABLE_PRODUCTS_CONTENT." 
			SET content_read='". ($content_data['content_read'] + 1)."'
			WHERE content_id='".(int) $_GET['coID']."'");
?>

<html <?php echo HTML_PARAMS; ?>>
<head>
<title><?php echo $content_data['content_name']; ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo 'themes/'.CURRENT_TEMPLATE.'/style.css'; ?>">
<script type="text/javascript"><!--
var i=0;
function resize() {
  if (navigator.appName == 'Netscape') i=40;
  if (document.images[0]) window.resizeTo(document.images[0].width +30, document.images[0].height+60-i);
  self.focus();
}
//--></script>
</head>
<body onload="resize();">
 <?php

if ($content_data['content_file'] != '') {
	if (strpos($content_data['content_file'], '.txt'))
		echo '<pre>';

	if (preg_match('/.gif/i', $content_data['content_file']) or preg_match('/.jpg/i', $content_data['content_file']) or preg_match('/.png/i', $content_data['content_file']) or preg_match('/.tif/i', $content_data['content_file']) or preg_match('/.bmp/i', $content_data['content_file'])) {
		echo '<table align="center" valign="middle" width="100%" height="100%" border=0><tr><td class="main" align="middle" valign="middle">';

		echo os_image(DIR_WS_CATALOG.'media/products/'.$content_data['content_file']);
		echo '</td></tr></table>';
	} else {
		echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">
		          <tr>
		            <td class="main">';
		include (DIR_FS_CATALOG.'media/products/'.$content_data['content_file']);
		echo '</td>
		          </tr>
		        </table>';
	}

	if (strpos($content_data['content_file'], '.txt'))
		echo '</pre>';
}
?>
</body>
</html>