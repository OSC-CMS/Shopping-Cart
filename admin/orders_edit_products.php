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
<table border="0" width="100%" cellspacing="2" cellpadding="2">
       <tr class="dataTableHeadingRow">
           <td class="dataTableHeadingContent"><b><?php echo TEXT_PRODUCT_ID;?></b></td>
           <td class="dataTableHeadingContent"><b><?php echo TEXT_QUANTITY;?></b></td>
           <td class="dataTableHeadingContent"><b><?php echo TEXT_PRODUCT;?></b></td>
           <td class="dataTableHeadingContent"><b><?php echo TEXT_PRODUCTS_MODEL;?></b></td>
           <td class="dataTableHeadingContent"><b><?php echo TEXT_TAX;?></b></td>
           <td class="dataTableHeadingContent"><b><?php echo TEXT_PRICE;?></b></td>
           <td class="dataTableHeadingContent"><b><?php echo TEXT_FINAL;?></b></td>
           <td class="dataTableHeadingContent">&nbsp;</td>
           <td class="dataTableHeadingContent">&nbsp;</td>
       </tr>

<?php
for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
?>
<tr class="dataTableRow">
<?php
echo os_draw_form('product_edit', FILENAME_ORDERS_EDIT, 'action=product_edit', 'post');
echo os_draw_hidden_field('oID', $_GET['oID']);
echo os_draw_hidden_field('opID', $order->products[$i]['opid']);
?>
<td class="dataTableContent"><?php echo os_draw_input_field('products_id', $order->products[$i]['id'], 'size="5"');?></td>
<td class="dataTableContent"><?php echo os_draw_input_field('products_quantity', $order->products[$i]['qty'], 'size="2"');?></td>
<td class="dataTableContent"><?php echo os_draw_input_field('products_name', $order->products[$i]['name'], 'size="20"');?></td>
<td class="dataTableContent"><?php echo os_draw_input_field('products_model', $order->products[$i]['model'], 'size="10"');?></td>
<td class="dataTableContent"><?php echo os_draw_input_field('products_tax', $order->products[$i]['tax'], 'size="6"');?></td>
<td class="dataTableContent"><?php echo os_draw_input_field('products_price', $order->products[$i]['price'], 'size="10"');?></td>
<td class="dataTableContent"><?php echo $order->products[$i]['final_price'];?></td>
<td class="dataTableContent">
<?php
echo os_draw_hidden_field('allow_tax', $order->products[$i]['allow_tax']);
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</buttom></span>';
?>
</form>
</td>

<td class="dataTableContent">
<?php
echo os_draw_form('product_delete', FILENAME_ORDERS_EDIT, 'action=product_delete', 'post');
echo os_draw_hidden_field('oID', $_GET['oID']);
echo os_draw_hidden_field('opID', $order->products[$i]['opid']);
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span>';
?>
</form>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" colspan="8">

<?php

//Bundle
$products_bundle = '';
if ($order->products[$i]['bundle'] == 1)
{
	$bundle_query = getBundleProducts($order->products[$i]['id']);
	
	if (os_db_num_rows($bundle_query) > 0)
	{
		while($bundle_data = os_db_fetch_array($bundle_query))
		{
			$products_bundle_data .= ' - <a href="'.os_href_link(FILENAME_CATEGORIES, 'pID='.$bundle_data['products_id'].'&action=new_product').'">'.$bundle_data['products_name'].'</a><br />';
		}
	}
	$products_bundle = (!empty($products_bundle_data)) ? '<div class="bundles-products-block">'.$products_bundle_data.'</div>' : '';
}
echo $products_bundle;
//End of Bundle

?>

</td>

<td class="dataTableContent">
<?php
echo os_draw_form('select_options', FILENAME_ORDERS_EDIT, '', 'GET');
echo os_draw_hidden_field('edit_action', 'options');
echo os_draw_hidden_field('pID', $order->products[$i]['id']);
echo os_draw_hidden_field('oID', $_GET['oID']);
echo os_draw_hidden_field('opID', $order->products[$i]['opid']);
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_PRODUCT_OPTIONS . '"/>' . BUTTON_PRODUCT_OPTIONS . '</button></span>';
?>
</form>
</td>
</tr>

<?php
}
?>
</table>
<br /><br />
<table border="0" width="100%" cellspacing="0" cellpadding="2">

<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" colspan="2"><b><?php echo TEXT_PRODUCT_SEARCH;?></b></td>
</tr>

<tr class="dataTableRow">
<?php
echo os_draw_form('product_search', FILENAME_ORDERS_EDIT, '', 'get');
echo os_draw_hidden_field('edit_action', 'products');
echo os_draw_hidden_field('action', 'product_search');
echo os_draw_hidden_field('oID', $_GET['oID']);
echo os_draw_hidden_field('cID', $_POST['cID']);
?>
<td class="dataTableContent" width="40"><?php echo os_draw_input_field('search', '', 'size="30"');?></td>
<td class="dataTableContent">
<?php
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SEARCH . '"/>' . BUTTON_SEARCH . '</button></span>';
?>
</td>
</form>
</tr>
</table>
<br /><br />
<?php
if ($_GET['action'] =='product_search') {

     $products_query = os_db_query("select
     p.products_id,
     p.products_model,
     pd.products_name,
     p.products_image,
     p.products_status
     from
     " . TABLE_PRODUCTS . " p,
     " . TABLE_PRODUCTS_DESCRIPTION . " pd
     where
     p.products_id = pd.products_id
     and pd.language_id = '" . $_SESSION['languages_id'] . "' and
     (pd.products_name like '%" . $_GET['search'] . "%' OR p.products_model = '" . $_GET['search'] . "') order by pd.products_name");

?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">

<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent"><b><?php echo TEXT_PRODUCT_ID;?></b></td>
<td class="dataTableHeadingContent"><b><?php echo TEXT_QUANTITY;?></b></td>
<td class="dataTableHeadingContent"><b><?php echo TEXT_PRODUCT;?></b></td>
<td class="dataTableHeadingContent"><b><?php echo TEXT_PRODUCTS_MODEL;?></b></td>
<td class="dataTableHeadingContent">&nbsp;</td>
</tr>

<?php
while($products = os_db_fetch_array($products_query)) {
?>
<tr class="dataTableRow">
<?php
echo os_draw_form('product_ins', FILENAME_ORDERS_EDIT, 'action=product_ins', 'post');
echo os_draw_hidden_field('cID', $_POST['cID']);
echo os_draw_hidden_field('oID', $_GET['oID']);
echo os_draw_hidden_field('products_id', $products[products_id]);
?>
<td class="dataTableContent"><?php echo $products[products_id];?></td>
<td class="dataTableContent"><?php echo os_draw_input_field('products_quantity', $products[products_quantity], 'size="2"');?></td>
<td class="dataTableContent"><?php echo $products[products_name];?></td>
<td class="dataTableContent"><?php echo $products[products_model];?></td>
<td class="dataTableContent">
<?php
echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span>';
?>
</form>
</td>
</tr>
<?php
}
?>
</table>
<?php } ?>
<br /><br />