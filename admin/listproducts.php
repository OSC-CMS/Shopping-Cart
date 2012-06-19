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
</head>
<body>
<table width="550" border="1" cellspacing="1" bordercolor="gray">
<tr>
<td colspan="3">
<h4>Valid Products List</h4>
</td>
</tr>
<?php
   $coupon_get=os_db_query("select restrict_to_products,restrict_to_categories from " . TABLE_COUPONS . "  where coupon_id='".$_GET['cid']."'");
   $get_result=os_db_fetch_array($coupon_get);

    echo "<tr><th>Product ID</th><th>Product Name</th><th>Product Size</th></tr><tr>";
    $pr_ids = preg_split("/[,]/", $get_result['restrict_to_products']);
    for ($i = 0; $i < count($pr_ids); $i++) {
      $result = os_db_query("SELECT * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd WHERE p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'and p.products_id = '" . $pr_ids[$i] . "'");
      if ($row = os_db_fetch_array($result)) {
            echo "<td>".$row["products_id"]."</td>\n";
            echo "<td>".$row["products_name"]."</td>\n";
            echo "<td>".$row["products_model"]."</td>\n";
            echo "</tr>\n";
      }
    }
      echo "</table>\n";
?>
<br>
<table width="550" border="0" cellspacing="1">
<tr>
<td align=middle><span class="button"><button type="button" value="<?php echo TEXT_CLOSE; ?>" onClick="window.close()"><?php echo TEXT_CLOSE; ?></button></span></td>
</tr></table>
</body>
</html>