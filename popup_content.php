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

$content_query = osDBquery("SELECT * FROM ".TABLE_CONTENT_MANAGER." WHERE content_group='".(int)$_GET['coID']."' and languages_id = '".(int)$_SESSION['languages_id']."'");
$content_data = os_db_fetch_array($content_query, true);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>" /> 
<meta http-equiv="Content-Style-Type" content="text/css" />
<title><?php echo $content_data['content_heading']; ?></title>
<base href="<?php echo (getenv('HTTPS') == 'on' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo 'themes/'.CURRENT_TEMPLATE.'/style.css'; ?>" />
</head>
<body>
<h2 class="modal-page-title"><?php echo $content_data['content_heading']; ?></h2>
<div class="modal-page-body">
<?php
if ($content_data['content_file'] != '') {
	if (strpos($content_data['content_file'], '.txt'))
		echo '<pre>';

	include (DIR_FS_CATALOG.'media/content/'.$content_data['content_file']);

	if (strpos($content_data['content_file'], '.txt'))
		echo '</pre>';
} else {
	echo $content_data['content_text'];
}
?>
</div>
</body>
</html>