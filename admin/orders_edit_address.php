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

<?php if ($_GET['edit_action']=='address'){

 echo os_draw_form('adress_edit', FILENAME_ORDERS_EDIT, 'action=address_edit', 'post');
 echo os_draw_hidden_field('oID', $_GET['oID']);
 echo os_draw_hidden_field('cID', $order->customer['ID']);
?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width="10%" align="left">&nbsp;</td>
<td class="dataTableHeadingContent" width="30%" align="left"><?php echo TEXT_INVOICE_ADDRESS;?></td>
<td class="dataTableHeadingContent" width="30%" align="left"><?php echo TEXT_SHIPPING_ADDRESS;?></td>
<td class="dataTableHeadingContent" width="30%" align="left"><?php echo TEXT_BILLING_ADDRESS;?></td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_COMPANY;?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('customers_company', $order->customer['company']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('delivery_company', $order->delivery['company']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('billing_company', $order->billing['company']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_NAME;?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('customers_name', $order->customer['name']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('delivery_name', $order->delivery['name']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('billing_name', $order->billing['name']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_STREET;?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('customers_street_address', $order->customer['street_address']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('delivery_street_address', $order->delivery['street_address']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('billing_street_address', $order->billing['street_address']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_ZIP;?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('customers_postcode', $order->customer['postcode']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('delivery_postcode', $order->delivery['postcode']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('billing_postcode', $order->billing['postcode']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_CITY;?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('customers_city', $order->customer['city']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('delivery_city', $order->delivery['city']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('billing_city', $order->billing['city']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_STATE;?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('customers_state', $order->customer['state']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('delivery_state', $order->delivery['state']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('billing_state', $order->billing['state']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_COUNTRY;?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('customers_country', $order->customer['country']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('delivery_country', $order->delivery['country']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo os_draw_input_field('billing_country', $order->billing['country']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left" colspan="4">
&nbsp;
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_CUSTOMER_GROUP;?>
</td>
<td class="dataTableContent" align="left" colspan="3">
<?php echo os_draw_pull_down_menu('customers_status', os_get_customers_statuses(), $order->info['status']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_CUSTOMER_EMAIL;?>
</td>
<td class="dataTableContent" align="left" colspan="3">
<?php echo os_draw_input_field('customers_email_address', $order->customer['email_address']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_CUSTOMER_TELEPHONE;?>
</td>
<td class="dataTableContent" align="left" colspan="3">
<?php echo os_draw_input_field('customers_telephone', $order->customer['telephone']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_CUSTOMER_UST;?>
</td>
<td class="dataTableContent" align="left" colspan="3">
<?php echo os_draw_input_field('customers_vat_id', $order->customer['vat_id']);?>
</td>
</tr>


<tr class="dataTableRow">
<td class="dataTableContent" align="left" colspan="4">
&nbsp;
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left" colspan="4">
<?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span>'; ?>
</td>
</tr>

<tr>
<td class="dataTableHeadingContent" width="10%" align="left">&nbsp;</td>
<td class="dataTableHeadingContent" width="30%" align="left">&nbsp;</td>
<td class="dataTableHeadingContent" width="30%" align="left">&nbsp;</td>
<td class="dataTableHeadingContent" width="30%" align="left">&nbsp;</td>
</tr>
</table>
</form>
<br /><br />
<?php } ?>