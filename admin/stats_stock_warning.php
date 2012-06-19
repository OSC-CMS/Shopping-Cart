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
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('stats.png',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
             <td><table width="100%">
<?php
  $products_query = os_db_query("SELECT p.products_id, p.products_quantity, pd.products_name FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE pd.language_id = '" . $_SESSION['languages_id'] . "' AND pd.products_id = p.products_id ORDER BY products_quantity");
  while ($products_values = os_db_fetch_array($products_query)) {
    echo '<tr><td width="50%" class="dataTableContent"><a href="' . os_href_link(FILENAME_CATEGORIES, 'pID=' . $products_values['products_id'] . '&action=new_product') . '"><b>' . $products_values['products_name'] . '</b></a></td><td width="50%" class="dataTableContent">';
    if ($products_values['products_quantity'] <='0') {
      echo '<font color="ff0000"><b>'.$products_values['products_quantity'].'</b></font>';
    } else {
      echo $products_values['products_quantity'];
    }
    echo '</td></tr>';

    $products_attributes_query = os_db_query("SELECT
                                                   pov.products_options_values_name,
                                                   pa.attributes_stock
                                               FROM
                                                   " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
                                               WHERE
                                                   pa.products_id = '".$products_values['products_id'] . "' AND pov.products_options_values_id = pa.options_values_id AND pov.language_id = '" . $_SESSION['languages_id'] . "' ORDER BY pa.attributes_stock");
								
    while ($products_attributes_values = os_db_fetch_array($products_attributes_query)) {
      echo '<tr><td width="50%" class="dataTableContent">&nbsp;&nbsp;&nbsp;&nbsp;-' . $products_attributes_values['products_options_values_name'] . '</td><td width="50%" class="dataTableContent">';
      if ($products_attributes_values['attributes_stock'] <= '0') {
        echo '<font color="ff0000"><b>' . $products_attributes_values['attributes_stock'] . '</b></font>';
      } else {
        echo $products_attributes_values['attributes_stock'];
      }
      echo '</td></tr>';
    }
  }
?>  
	        </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>