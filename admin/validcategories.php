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
<?php $main->favicon();?>
<title>Valid Categories/Products List</title>
<?php 
   $main->style('style'); 
   $main->style('menu');
?>
</head>
<body>
<table width="550" cellspacing="2">
<tr>
<td class="pageHeading" colspan="3">
<?php echo TEXT_VALID_CATEGORIES_LIST; ?>
</td>
</tr>
<?php
    echo "<tr><th class=\"dataTableHeadingContent\">" . TEXT_VALID_CATEGORIES_ID . "</th><th class=\"dataTableHeadingContent\">" . TEXT_VALID_CATEGORIES_NAME . "</th></tr><tr>";
    $result = os_db_query("SELECT * FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd WHERE c.categories_id = cd.categories_id and cd.language_id = '" . $_SESSION['languages_id'] . "' ORDER BY c.categories_id");
    if ($row = os_db_fetch_array($result)) {
        do {
            echo "<td class=\"dataTableHeadingContent\">".$row["categories_id"]."</td>\n";
            echo "<td class=\"dataTableHeadingContent\">".$row["categories_name"]."</td>\n";
            echo "</tr>\n";
        }
        while($row = os_db_fetch_array($result));
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