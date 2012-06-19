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

require('includes/top.php');
?>
<html>
<head>
<link rel="shortcut icon" href="<?php echo HTTP_SERVER.DIR_WS_ADMIN;?>favicon.ico" />
<title>Valid Categories/Products List</title>
<style type="text/css">
<!--
h4 {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: x-small; text-align: center}
p {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
th {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
-->
</style>
<body>
<table width="550" border="1" cellspacing="1" bordercolor="gray">
<tr>
<td colspan="4">
<h4>Valid Categories List</h4>
</td>
</tr>
<?php
   $coupon_get=os_db_query("select restrict_to_categories from " . TABLE_COUPONS . " where coupon_id='".$_GET['cid']."'");
   $get_result=os_db_fetch_array($coupon_get);
   echo "<tr><th>Category ID</th><th>Category Name</th></tr><tr>";
   $cat_ids = preg_split("/[,]/", $get_result['restrict_to_categories']);
   for ($i = 0; $i < count($cat_ids); $i++) {
     $result = os_db_query("SELECT * FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd WHERE c.categories_id = cd.categories_id and cd.language_id = '" . $_SESSION['languages_id'] . "' and c.categories_id='" . $cat_ids[$i] . "'");
     if ($row = os_db_fetch_array($result)) {
       echo "<td>".$row["categories_id"]."</td>\n";
       echo "<td>".$row["categories_name"]."</td>\n";
       echo "</tr>\n";
     } 
   }
    echo "</table>\n";
?>
<br>
<table width="550" border="0" cellspacing="1">
<tr>
<td align=middle><input type="button" value="<?php echo TEXT_CLOSE; ?>" onClick="window.close()"></td>
</tr></table>
</body>
</html>