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

defined('_VALID_OS') or die('Прямой доступ не допускается.');
$adminImages = DIR_WS_CATALOG . "langs/". $_SESSION['language_admin'] ."/admin/images/buttons/";
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="SELECT_PRODUCT" method="post"><input type="hidden" name="action" value="edit">
<?php
echo os_draw_hidden_field(os_session_name(), os_session_id());
  echo "<TR>";
  echo "<TD class=\"main\"><br /><B>".SELECT_PRODUCT."<br /></TD>";
  echo "</TR>";
  echo "<TR>";
  echo "<TD class=\"main\"><SELECT NAME=\"current_product_id\">";

  $query = "SELECT * FROM  ".TABLE_PRODUCTS_DESCRIPTION."  where products_id LIKE '%' AND language_id = '" . $_SESSION['languages_id'] . "' ORDER BY products_name ASC";

  $result = os_db_query($query);

  $matches = os_db_num_rows($result);

  if ($matches) {
    while ($line = os_db_fetch_array($result)) {
      $title = $line['products_name'];
      $current_product_id = $line['products_id'];

      echo "<OPTION VALUE=\"" . $current_product_id . "\">" . $title;
    }
  } else {
    echo "You have no products at this time.";
  }

  echo "</SELECT>";
  echo "</TD></TR>";

  echo "<TR>";
  echo "<TD class=\"main\">";
  echo os_button(BUTTON_EDIT);

  echo "</TD>";
  echo "</TR>";
?>
<br /><br />
<?php
  echo "<TR>";
  echo "<TD class=\"main\"><br /><B>".SELECT_COPY."<br /></TD>";
  echo "</TR>";
  echo "<TR>";
  echo "<TD class=\"main\"><SELECT NAME=\"copy_product_id\">";

  $copy_query = os_db_query("SELECT pd.products_name, pd.products_id FROM  ".TABLE_PRODUCTS_DESCRIPTION."  pd, ".TABLE_PRODUCTS_ATTRIBUTES." pa where pa.products_id = pd.products_id AND pd.products_id LIKE '%' AND pd.language_id = '" . $_SESSION['languages_id'] . "' GROUP BY pd.products_id ORDER BY pd.products_name ASC");
  $copy_count = os_db_num_rows($copy_query);

  if ($copy_count) {
      echo '<option value="0">no copy</option>';
      while ($copy_res = os_db_fetch_array($copy_query)) {
          echo '<option value="' . $copy_res['products_id'] . '">' . $copy_res['products_name'] . '</option>';
      }
  }
  else {
      echo 'No products to copy attributes from';
  }
  echo '</select></td></tr>';
  echo "<TR>";
  echo "<TD class=\"main\">".os_button(BUTTON_EDIT)."</TD>";
  echo "</TR>";

?>
</form>