<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

require (_CLASS.'price.php');
$osPrice = new osPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

$i = 0;
$group_query = os_db_query("SELECT
                                   customers_status_image,
                                   customers_status_id,
                                   customers_status_name
                               FROM
                                   ".TABLE_CUSTOMERS_STATUS."
                               WHERE
                                   language_id = '".$_SESSION['languages_id']."' AND customers_status_id != '0'");
while ($group_values = os_db_fetch_array($group_query)) {
        // load data into array
        $i ++;
        $group_data[$i] = array ('STATUS_NAME' => $group_values['customers_status_name'], 'STATUS_IMAGE' => $group_values['customers_status_image'], 'STATUS_ID' => $group_values['customers_status_id']);
}
?>
          <tr>
            <td><?php echo TEXT_PRODUCTS_PRICE; ?></td>
<?php
// calculate brutto price for display

if (PRICE_IS_BRUTTO == 'true') {
        $products_price = os_round($pInfo->products_price * ((100 + os_get_tax_rate($pInfo->products_tax_class_id)) / 100), PRICE_PRECISION);
} else {
        $products_price = os_round($pInfo->products_price, PRICE_PRECISION);
}
?>
            <td><?php echo os_draw_input_field('products_price', $products_price); ?>
<?php
if (PRICE_IS_BRUTTO == 'true') {
        echo TEXT_NETTO.'<b>'.$osPrice->Format($pInfo->products_price, false).'</b>  ';
}
?>
            </td>
          </tr>
<?php
for ($col = 0, $n = sizeof($group_data); $col < $n +1; $col ++) {
        if ($group_data[$col]['STATUS_NAME'] != '') {
?>
          <tr>
            <td><?php echo $group_data[$col]['STATUS_NAME']; ?></td>
<?php
                if (PRICE_IS_BRUTTO == 'true') {
                        $products_price = os_round(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id) * ((100 + os_get_tax_rate($pInfo->products_tax_class_id)) / 100), PRICE_PRECISION);
                } else {
                        $products_price = os_round(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id), PRICE_PRECISION);
                }
?>
            <td><?php
                echo os_draw_input_field('products_price_'.$group_data[$col]['STATUS_ID'], $products_price);
                if (PRICE_IS_BRUTTO == 'true' && get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id) != '0') {
                        echo TEXT_NETTO.'<b>'.$osPrice->Format(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id), false).'</b>  ';
                }
                if ($_GET['pID'] != '') {
                        echo ' '.TXT_STAFFELPREIS;
?> <img onMouseOver="javascript:this.style.cursor='hand';" alt="&darr;" src="<?php echo http_path('images_admin'); ?>arrow_down.gif" height="12" width="12" onClick="javascript:toggleBox('staffel_<?php echo $group_data[$col]['STATUS_ID']; ?>');">
<?php
                }
                if ($_GET['pID'] != '') {
                }
?><div id="staffel_<?php echo $group_data[$col]['STATUS_ID']; ?>" class="longDescription"><br><?php
                // ok, lets check if there is already a staffelpreis
                $staffel_query = os_db_query("SELECT
                                                                         products_id,
                                                                         quantity,
                                                                         personal_offer
                                                                     FROM
                                                                         ".DB_PREFIX."personal_offers_by_customers_status_".$group_data[$col]['STATUS_ID']."
                                                                     WHERE
                                                                         products_id = '".$pInfo->products_id."' AND quantity != 1
                                                                     ORDER BY quantity ASC");
                echo '<table width="247" border="0" cellpadding="0" cellspacing="0">';
                while ($staffel_values = os_db_fetch_array($staffel_query)) {
                        // load data into array
?>
              <tr>
                <td><?php echo $staffel_values['quantity']; ?></td>
                <td width="5">&nbsp;</td>
                <td nowrap>
<?php
                        if (PRICE_IS_BRUTTO == 'true') {
                                $tax_query = os_db_query("select tax_rate from ".TABLE_TAX_RATES." where tax_class_id = '".$pInfo->products_tax_class_id."' ");
                                $tax = os_db_fetch_array($tax_query);
                                $products_price = os_round($staffel_values['personal_offer'] * ((100 + $tax['tax_rate']) / 100), PRICE_PRECISION);

                        } else {
                                $products_price = os_round($staffel_values['personal_offer'], PRICE_PRECISION);
                        }
                        echo $products_price;
                        if (PRICE_IS_BRUTTO == 'true') {
                                echo ' <br>'.TEXT_NETTO.'<b>'.$osPrice->Format($staffel_values['personal_offer'], false).'</b>  ';
                        }
?>
 </td>
                <td><a class="button" href="<?php echo os_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&function=delete&quantity=' . $staffel_values['quantity'] . '&statusID=' . $group_data[$col]['STATUS_ID'] . '&action=new_product&pID=' . $_GET['pID']); ?>"><span><?php echo BUTTON_DELETE; ?></span></a></td>
              </tr>
<?php
                }
                echo '</table>';
                echo TXT_STK;
                echo os_draw_small_input_field('products_quantity_staffel_'.$group_data[$col]['STATUS_ID'], 0);
                echo TXT_PRICE;
                echo os_draw_input_field('products_price_staffel_'.$group_data[$col]['STATUS_ID'], 0);
                echo '<span class="button"><button type="submit" onClick="return confirm(\''.SAVE_ENTRY.'\')" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span>';
?><br></td>
          </tr>
<?php } } ?>
</div>
          <tr>
            <td><?php echo TEXT_PRODUCTS_DISCOUNT_ALLOWED; ?></td>
            <td><?php echo os_draw_input_field('products_discount_allowed', ($pInfo->products_discount_allowed=='' ? 100 : $pInfo->products_discount_allowed)); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_TAX_CLASS; ?></td>
            <td class="main"><?php echo os_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id); ?></td>
          </tr>
