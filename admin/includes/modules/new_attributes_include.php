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

   require(_CLASS.'price.php');
   $osPrice = new osPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="SUBMIT_ATTRIBUTES" enctype="multipart/form-data"><input type="hidden" name="current_product_id" value="<?php echo $_POST['current_product_id']; ?>"><input type="hidden" name="action" value="change">
<?php
echo os_draw_hidden_field(os_session_name(), os_session_id());
  if ($cPath) echo '<input type="hidden" name="cPathID" value="' . $cPath . '">';

  require(_MODULES_ADMIN . 'new_attributes_functions.php');
  $tempTextID = '1999043';
  $query = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS." where products_options_id LIKE '%' AND language_id = '" . $_SESSION['languages_id'] . "'";
  $result = os_db_query($query);
  $matches = os_db_num_rows($result);

  if ($matches) {
    while ($line = os_db_fetch_array($result)) {
      $current_product_option_name = $line['products_options_name'];
      $current_product_option_id = $line['products_options_id'];
      echo "<TR class=\"dataTableHeadingRow\">";
      echo "<TD class=\"dataTableHeadingContent\"><B>" . $current_product_option_name . "</B></TD>";
      echo "<TD class=\"dataTableHeadingContent\"><B>".SORT_ORDER."</B></TD>";
      echo "<TD class=\"dataTableHeadingContent\"><B>".ATTR_MODEL."</B></TD>";
      echo "<TD class=\"dataTableHeadingContent\"><B>".ATTR_STOCK."</B></TD>";
      echo "<TD class=\"dataTableHeadingContent\"><B>".ATTR_WEIGHT."</B></TD>";
      echo "<TD class=\"dataTableHeadingContent\"><B>".ATTR_PREFIXWEIGHT."</B></TD>";
      echo "<TD class=\"dataTableHeadingContent\"><B>".ATTR_PRICE."</B></TD>";
      echo "<TD class=\"dataTableHeadingContent\"><B>".ATTR_PREFIXPRICE."</B></TD>";

      echo "</TR>";

      // Find all of the Current Option's Available Values
      $query2 = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." povto
	  LEFT JOIN ".TABLE_PRODUCTS_OPTIONS_VALUES." pov ON povto.products_options_values_id=pov.products_options_values_id 
	  WHERE povto.products_options_id = '" . $current_product_option_id . "' AND language_id = '" . $_SESSION['languages_id'] . "' ORDER BY pov.products_options_values_name ASC";
      $result2 = os_db_query($query2);
      $matches2 = os_db_num_rows($result2);

      if ($matches2) {
        $i = '0';
        while ($line = os_db_fetch_array($result2)) {
          $i++;
          $rowClass = rowClass($i);
          $current_value_id = $line['products_options_values_id'];
          $isSelected = checkAttribute($current_value_id, $_POST['current_product_id'], $current_product_option_id);
          if ($isSelected) {
            $CHECKED = ' CHECKED';
          } else {
            $CHECKED = '';
          }

          $query3 = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = '" . $current_value_id . "' AND language_id = '" . $_SESSION['languages_id'] . "'";
          $result3 = os_db_query($query3);
          while($line = os_db_fetch_array($result3)) {
            $current_value_name = $line['products_options_values_name'];
            echo "<TR class=\"" . $rowClass . "\">";
            echo "<TD class=\"main\">";
            echo "<input type=\"checkbox\" name=\"optionValues[]\" value=\"" . $current_value_id . "\"" . $CHECKED . ">&nbsp;&nbsp;" . $current_value_name . "&nbsp;&nbsp;";
            echo "</TD>";
            echo "<TD class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_sortorder\" value=\"" . $sortorder . "\" size=\"4\"></TD>";
            echo "<TD class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_model\" value=\"" . $attribute_value_model . "\" size=\"15\"></TD>";
            echo "<TD class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_stock\" value=\"" . $attribute_value_stock . "\" size=\"4\"></TD>";
            echo "<TD class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_weight\" value=\"" . $attribute_value_weight . "\" size=\"10\"></TD>";
            echo "<TD class=\"main\" align=\"left\"><SELECT name=\"" . $current_value_id . "_weight_prefix\"><OPTION value=\"+\"" . $posCheck_weight . ">+<OPTION value=\"-\"" . $negCheck_weight . ">-</SELECT></TD>";

            if (PRICE_IS_BRUTTO=='true'){
            $attribute_value_price_calculate = $osPrice->Format(os_round($attribute_value_price*((100+(os_get_tax_rate(os_get_tax_class_id($_POST['current_product_id']))))/100),PRICE_PRECISION),false);
            } else {
            $attribute_value_price_calculate = os_round($attribute_value_price,PRICE_PRECISION);
            }
            echo "<TD class=\"main\" align=\"left\"><input type=\"text\" name=\"" . $current_value_id . "_price\" value=\"" . $attribute_value_price_calculate . "\" size=\"10\">";
            if (PRICE_IS_BRUTTO=='true'){
             echo TEXT_NETTO .'<b>'.$osPrice->Format(os_round($attribute_value_price,PRICE_PRECISION),true).'</b>  ';
            }

            echo "</TD>";

              echo "<TD class=\"main\" align=\"left\"><SELECT name=\"" . $current_value_id . "_prefix\"> <OPTION value=\"+\"" . $posCheck . ">+<OPTION value=\"-\"" . $negCheck . ">-</SELECT></TD>";



            echo "</TR>";
            if(DOWNLOAD_ENABLED == 'true') {

                $file_list = os_array_merge(array('0' => array('id' => '', 'text' => SELECT_FILE)),os_getFiles(_DOWNLOAD));

                echo "<tr>";
                echo "<td colspan=\"2\" class=\"main\">&nbsp;" . DL_FILE . "<br>" . os_draw_pull_down_menu($current_value_id . '_download_file',$file_list,$attribute_value_download_filename)."</td>";                echo "<td class=\"main\">&nbsp;". DL_COUNT . "<br><input type=\"text\" name=\"" . $current_value_id . "_download_count\" value=\"" . $attribute_value_download_count . "\"></td>";
                echo "<td class=\"main\">&nbsp;". DL_EXPIRE . "<br><input type=\"text\" name=\"" . $current_value_id . "_download_expire\" value=\"" . $attribute_value_download_expire . "\"></td>";
                echo "</tr>";
            }
          }
          if ($i == $matches2 ) $i = '0';
        }
      } else {
        echo "<TR>";
        echo "<TD class=\"main\"><SMALL>".OS_NO_VALUES."</SMALL></TD>";
        echo "</TR>";
      }
    }
  }
?>
  <tr>
    <td colspan="10" class="main"><br>
<?php
echo os_button(BUTTON_SAVE) . '&nbsp;';
echo os_button_link(BUTTON_CANCEL,'javascript:history.back()');
?>
</td>
  </tr>
</form>
</table>