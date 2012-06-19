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
?>


<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width="100%" colspan="3"><b><?php echo TEXT_LANGUAGE; ?></b></td>
</tr>

<?php
  echo os_draw_form('lang_edit', FILENAME_ORDERS_EDIT, 'action=lang_edit', 'post'); 
  
 $lang_query = os_db_query("select languages_id, name, directory from " . TABLE_LANGUAGES . " ");
  while($lang = os_db_fetch_array($lang_query)){

?>
<tr class="dataTableRow">
<td class="dataTableContent" align="left" width="30%"><?php echo $lang['name'];?></td>
<td class="dataTableContent" align="left" width="30%">
<?php
if ($lang['directory']==$order->info['language']){
 echo os_draw_radio_field('lang', $lang['languages_id'], 'checked');
}else{
 echo os_draw_radio_field('lang', $lang['languages_id']);	
}	
?>
</td>
<td class="dataTableContent" align="left">&nbsp;</td>
</tr>
<?php } ?>

<tr class="dataTableRow">
<td class="dataTableContent" align="left" colspan="3">
<?php
echo os_draw_hidden_field('oID', $_GET['oID']);
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>';
?></td>
</tr>

</form>
</table>

<!-- Sprachen Ende //-->


<br /><br />


<!-- WпїЅhrungen Anfang //-->

<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width="100%" colspan="3"><b><?php echo TEXT_CURRENCIES; ?></b></td>
</tr>

<?php
  echo os_draw_form('curr_edit', FILENAME_ORDERS_EDIT, 'action=curr_edit', 'post'); 
  
 $curr_query = os_db_query("select currencies_id, title, code, value from " . TABLE_CURRENCIES . " ");
  while($curr = os_db_fetch_array($curr_query)){

?>
<tr class="dataTableRow">
<td class="dataTableContent" align="left" width="30%"><?php echo $curr['title'];?></td>
<td class="dataTableContent" align="left" width="30%">
<?php
if ($curr['code']==$order->info['currency']){
 echo os_draw_radio_field('currencies_id', $curr['currencies_id'], 'checked');
}else{
 echo os_draw_radio_field('currencies_id', $curr['currencies_id']);	
}	
?>
</td>
<td class="dataTableContent" align="left">&nbsp;</td>
</tr>
<?php } ?>

<tr class="dataTableRow">
<td class="dataTableContent" align="left" colspan="3">
<?php
echo os_draw_hidden_field('old_currency', $order->info['currency']);
echo os_draw_hidden_field('oID', $_GET['oID']);
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>';
?></td>
</tr>

</form>
</table>

<!-- WпїЅhrungen Ende //-->


<br /><br />


<!-- Zahlung Anfang //-->

<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width="100%" colspan="4"><b><?php echo TEXT_PAYMENT; ?></td>
</tr>

<?php
  $payments = preg_split('/;/', MODULE_PAYMENT_INSTALLED);
  for ($i=0; $i<count($payments); $i++){
  
  if (is_file(DIR_FS_LANGUAGES . $order->info['language'] . '/modules/payment/' . $payments[$i]))
  {
     require(DIR_FS_LANGUAGES . $order->info['language'] . '/modules/payment/' . $payments[$i]);	
  }
  
  $payment = substr($payments[$i], 0, strrpos($payments[$i], '.'));	
  if (!empty($payment))
  {
     require(_MODULES.'payment/'.$payment.'/'.$order->info['language'].'.php');
     $payment_text = constant(MODULE_PAYMENT_.strtoupper($payment)._TEXT_TITLE);
  }
  else
  {
     $payment_text  = 'none';
  }
  
  $payment_array[] = array('id' => $payment,
                           'text' => $payment_text);
  }
  
  $order_payment = $order->info['payment_class'];
  
  if (!empty($order_payment))
  {
    require(_MODULES. 'payment/' . $order_payment.'/'.$order->info['language'] .'.php');	
    $order_payment_text = constant(MODULE_PAYMENT_.strtoupper($order_payment)._TEXT_TITLE);  
  }
echo os_draw_form('payment_edit', FILENAME_ORDERS_EDIT, 'action=payment_edit', 'post');
?>
<tr class="dataTableRow">
<td class="dataTableContent" align="left" width="30%">
<?php
echo TEXT_ACTUAL . $order_payment_text;
?></td>
<td class="dataTableContent" align="left" width="30%">
<?php
echo TEXT_NEW . os_draw_pull_down_menu('payment', $payment_array);
?></td>
<td class="dataTableContent" align="left">
<?php
echo os_draw_hidden_field('oID', $_GET['oID']);
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>';
?></td>
</tr>


</form>
</table>

<!-- Zahlung Ende //-->


<br /><br />


<!-- Versand Anfang //-->

<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width="100%" colspan="4"><b><?php echo TEXT_SHIPPING; ?></td>
</tr>

<?php
  $shippings = preg_split('/;/', MODULE_SHIPPING_INSTALLED);
  for ($i=0; $i<count($shippings); $i++){
  
  if (isset($shippings[$i]) && is_file(DIR_FS_LANGUAGES . $order->info['language'] . '/modules/shipping/' . $shippings[$i])) {
  require(DIR_FS_LANGUAGES . $order->info['language'] . '/modules/shipping/' . $shippings[$i]);	
  
  $shipping = substr($shippings[$i], 0, strrpos($shippings[$i], '.'));	
  $shipping_text = constant(MODULE_SHIPPING_.strtoupper($shipping)._TEXT_TITLE);
  
  $shipping_array[] = array('id' => $shipping,
                            'text' => $shipping_text);
  }
  }
  
  $order_shipping = preg_split('/_/', $order->info['shipping_class']);
  $order_shipping = $order_shipping[0];
  if (is_file(DIR_FS_LANGUAGES . $order->info['language'] . '/modules/shipping/' . $order_shipping .'.php')) {
  require(DIR_FS_LANGUAGES . $order->info['language'] . '/modules/shipping/' . $order_shipping .'.php');	
  $order_shipping_text = constant(MODULE_SHIPPING_.strtoupper($order_shipping)._TEXT_TITLE);  
  }
  
echo os_draw_form('shipping_edit', FILENAME_ORDERS_EDIT, 'action=shipping_edit', 'post');
?>
<tr class="dataTableRow">
<td class="dataTableContent" align="left" width="30%">
<?php
echo TEXT_ACTUAL . $order_shipping_text;
?></td>
<td class="dataTableContent" align="left" width="30%">
<?php
echo TEXT_NEW . os_draw_pull_down_menu('shipping', $shipping_array);
?></td>
<td class="dataTableContent" align="left">
<?php
$order_total_query = os_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $_GET['oID'] . "' and class = 'ot_shipping' ");
$order_total = os_db_fetch_array($order_total_query);
echo TEXT_PRICE . os_draw_input_field('value', $order_total['value']);
?>
</td>
<td class="dataTableContent" align="left">
<?php
echo os_draw_hidden_field('oID', $_GET['oID']);
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>';
?></td>
</tr>


</form>
</table>

<!-- Versand Ende //-->


<br /><br />


<!-- OT Module Anfang //-->

<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width="100%" colspan="5"><b><?php echo TEXT_ORDER_TOTAL; ?></b></td>
</tr>


<?php
  $totals = preg_split('/;/', MODULE_ORDER_TOTAL_INSTALLED);
  for ($i=0; $i<count($totals); $i++){
  
  require(_MODULES.'order_total/'.substr($totals[$i], 0, strrpos($totals[$i], '.')).'/'.$order->info['language'].'.php');	
  
  
  $total = substr($totals[$i], 0, strrpos($totals[$i], '.'));
  $total_name = str_replace('ot_','',$total); 

if (defined(MODULE_ORDER_TOTAL_.strtoupper($total_name)._TITLE))
{
    $total_text = constant(MODULE_ORDER_TOTAL_.strtoupper($total_name)._TITLE);
}
else
{
   $total_text = 'none';
}
  
   $ototal_query = os_db_query("select orders_total_id, title, value, class from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $_GET['oID'] . "' and class = '" . $total . "' ");
   $ototal = os_db_fetch_array($ototal_query);  

//if (($total != 'ot_subtotal')&&($total != 'ot_subtotal_no_tax')&&($total != 'ot_total')&&($total != 'ot_tax')){  
//if ($total != 'ot_shipping'){  

  echo os_draw_form('ot_edit', FILENAME_ORDERS_EDIT, 'action=ot_edit', 'post');   
?>
<tr class="dataTableRow">
<td class="dataTableContent" align="left" width="20%"><?php echo $total_text; ?></td>
<td class="dataTableContent" align="left" width="40%"><?php echo os_draw_input_field('title', $ototal['title'], 'size=40'); ?></td>
<td class="dataTableContent" align="left" width="20%"><?php echo os_draw_input_field('value', $ototal['value']); ?></td>
<td class="dataTableContent" align="left" width="20%">
<?php
echo os_draw_hidden_field('class', $total);

if (defined(MODULE_ORDER_TOTAL_.strtoupper($total_name)._SORT_ORDER))
{
   echo os_draw_hidden_field('sort_order', constant(MODULE_ORDER_TOTAL_.strtoupper($total_name)._SORT_ORDER));
}
else
{
   echo os_draw_hidden_field('sort_order', '0');
}

echo os_draw_hidden_field('oID', $_GET['oID']);
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>';
?>
</form>
</td>
<td>
<?php
echo os_draw_form('ot_delete', FILENAME_ORDERS_EDIT, 'action=ot_delete', 'post');
echo os_draw_hidden_field('oID', $_GET['oID']);
echo os_draw_hidden_field('otID', $ototal['orders_total_id']);
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span>';
?>
</form>
</td>
</tr>

<?php 
 // }
}
?>


</table>