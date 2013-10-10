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

require (_CLASS.'price.php');
$osPrice = new osPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);


if ($_GET['pID']=='media') {
	$content_query=os_db_query("SELECT
 					content_file,
 					content_name,
 					file_comment
 					FROM ".TABLE_PRODUCTS_CONTENT."
 					WHERE content_id='".(int)$_GET['coID']."'");
 	$content_data=os_db_fetch_array($content_query);
	
} else {
	 $content_query=os_db_query("SELECT
 					content_title,
 					content_heading,
 					content_text,
 					content_file
 					FROM ".TABLE_CONTENT_MANAGER."
 					WHERE content_id='".(int)$_GET['coID']."'");
 	$content_data=os_db_fetch_array($content_query);
 }
 
define("HEADING_TITLE", $content_data['content_heading']); 

  //define('HEADING_TITLE', $content_data['content_heading']);
 $main->head(); 
 ?>
<div class="pageHeading"><?php echo $content_data['content_heading']; ?></div><br>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">
 <?php
 if ($content_data['content_file']!=''){
if (strpos($content_data['content_file'],'.txt')) echo '<pre>';
if ($_GET['pID']=='media') {
	if (preg_match('/.gif/i',$content_data['content_file']) or preg_match('/.jpg/i',$content_data['content_file']) or  preg_match('/.png/i',$content_data['content_file']) or  preg_match('/.tif/i',$content_data['content_file']) or  preg_match('/.bmp/i',$content_data['content_file'])) {	
	echo os_image(DIR_WS_CATALOG.'media/products/'.$content_data['content_file']);
	} else {
	include(DIR_FS_CATALOG.'media/products/'.$content_data['content_file']);	
	}
} else {
include(DIR_FS_CATALOG.'media/content/'.$content_data['content_file']);	
}
if (strpos($content_data['content_file'],'.txt')) echo '</pre>';
 } else {	      
echo $content_data['content_text'];
}
?>
</td>
          </tr>
        </table>
</body>
</html>