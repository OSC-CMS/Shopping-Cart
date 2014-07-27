<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include 'lang/'.$_SESSION['language_admin'].'/orders.php';

if (isset($_GET['o_id']) && !empty($_GET['o_id']))
{
	require (get_path('class_admin').'order.php');
	$order = new order($_GET['o_id']);
}
?>

<?php if ($_GET['action'] == 'delete') { ?>

	<?php echo $cartet->html->form('orders_form', 'ajax.php', 'ajax_action=order_deleteOrderById', 'post', array('id' => 'orders_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="order_id" value="<?php echo $_GET['o_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4><?php echo TEXT_INFO_HEADING_DELETE_ORDER; ?> #<?php echo $_GET['o_id']; ?></h4>
		</div>
		<div class="modal-body">
			<?php echo TEXT_INFO_DELETE_INTRO; ?>
			<hr>
			<label class="checkbox"><?php echo os_draw_checkbox_field('restock'); ?> <?php echo TEXT_INFO_RESTOCK_PRODUCT_QUANTITY; ?></label>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('order_delete', BUTTON_DELETE, array('class' => 'btn btn btn-danger save-form', 'data-form-action' => 'order_deleteOrderById', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'edit_address') { ?>

	<?php echo $cartet->html->form('orders_form', 'ajax.php', 'ajax_action=order_editAddress', 'post', array('id' => 'orders_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="order_id" value="<?php echo $_GET['o_id']; ?>" />
		<input type="hidden" name="customer_id" value="<?php echo $order->customer['ID']; ?>" />
		<input type="hidden" name="language" value="<?php echo $order->info['language']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4><?php echo TEXT_EDIT_ADDRESS; ?></h4>
		</div>
		<div class="modal-body">
			<table class="table table-condensed table-big-list nomargin">
				<tr>
					<th class="no-border-top"></th>
					<th class="no-border-top"><?php echo TEXT_EDIT_INVOICE_ADDRESS;?></th>
					<th class="no-border-top"><?php echo TEXT_EDIT_SHIPPING_ADDRESS;?></th>
					<th class="no-border-top"><?php echo TEXT_EDIT_BILLING_ADDRESS;?></th>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_COMPANY;?></td>
					<td><?php echo $cartet->html->input_text('customers_company', $order->customer['company']);?></td>
					<td><?php echo $cartet->html->input_text('delivery_company', $order->delivery['company']);?></td>
					<td><?php echo $cartet->html->input_text('billing_company', $order->billing['company']);?></td>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_NAME;?></td>
					<td><?php echo $cartet->html->input_text('customers_name', $order->customer['name']);?></td>
					<td><?php echo $cartet->html->input_text('delivery_name', $order->delivery['name']);?></td>
					<td><?php echo $cartet->html->input_text('billing_name', $order->billing['name']);?></td>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_STREET;?></td>
					<td><?php echo $cartet->html->input_text('customers_street_address', $order->customer['street_address']);?></td>
					<td><?php echo $cartet->html->input_text('delivery_street_address', $order->delivery['street_address']);?></td>
					<td><?php echo $cartet->html->input_text('billing_street_address', $order->billing['street_address']);?></td>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_ZIP;?></td>
					<td><?php echo $cartet->html->input_text('customers_postcode', $order->customer['postcode']);?></td>
					<td><?php echo $cartet->html->input_text('delivery_postcode', $order->delivery['postcode']);?></td>
					<td><?php echo $cartet->html->input_text('billing_postcode', $order->billing['postcode']);?></td>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_CITY;?></td>
					<td><?php echo $cartet->html->input_text('customers_city', $order->customer['city']);?></td>
					<td><?php echo $cartet->html->input_text('delivery_city', $order->delivery['city']);?></td>
					<td><?php echo $cartet->html->input_text('billing_city', $order->billing['city']);?></td>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_STATE;?></td>
					<td><?php echo $cartet->html->input_text('customers_state', $order->customer['state']);?></td>
					<td><?php echo $cartet->html->input_text('delivery_state', $order->delivery['state']);?></td>
					<td><?php echo $cartet->html->input_text('billing_state', $order->billing['state']);?></td>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_COUNTRY;?></td>
					<td><?php echo $cartet->html->input_text('customers_country', $order->customer['country']);?></td>
					<td><?php echo $cartet->html->input_text('delivery_country', $order->delivery['country']);?></td>
					<td><?php echo $cartet->html->input_text('billing_country', $order->billing['country']);?></td>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_CUSTOMER_GROUP;?></td>
					<td colspan="3"><?php echo os_draw_pull_down_menu('customers_status', os_get_customers_statuses(), $order->info['status']);?></td>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_CUSTOMER_EMAIL;?></td>
					<td colspan="3"><?php echo $cartet->html->input_text('customers_email_address', $order->customer['email_address']);?></td>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_CUSTOMER_TELEPHONE;?></td>
					<td colspan="3"><?php echo $cartet->html->input_text('customers_telephone', $order->customer['telephone']);?></td>
				</tr>
				<tr>
					<td><?php echo TEXT_EDIT_CUSTOMER_UST;?></td>
					<td colspan="3"><?php echo $cartet->html->input_text('customers_vat_id', $order->customer['vat_id']);?></td>
				</tr>
			</table>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('order_address_update', BUTTON_UPDATE, array('class' => 'btn btn btn-success save-form', 'data-form-action' => 'order_editAddress', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'edit_attributes') { ?>

	<?php echo $cartet->html->form('orders_form', 'ajax.php', 'ajax_action=order_editAttributes', 'post', array('id' => 'orders_form', 'class' => 'form-inline')); ?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4><?php echo TEXT_PRODUCT_OPTION; ?></h4>
		</div>
		<div class="modal-body">

			<?php
			require (_CLASS.'price.php');
			$osPrice = new osPrice($order->info['currency'], isset($order->info['status']) ? $order->info['status'] : '');

			$attributes_query = os_db_query("select * from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_GET['o_id']."' and orders_products_id = '".$_GET['op_id']."'");
			?>

			<input type="hidden" name="pID" value="<?php echo $_GET['p_id']; ?>" />
			<input type="hidden" name="oID" value="<?php echo $_GET['o_id']; ?>" />
			<input type="hidden" name="opID" value="<?php echo $_GET['op_id']; ?>" />

			<input type="hidden" name="ocID" value="<?php echo $order->customer['ID']; ?>" />

			<input type="hidden" name="currency" value="<?php echo $order->info['currency']; ?>" />
			<input type="hidden" name="status" value="<?php echo $order->info['status']; ?>" />

			<table class="table table-condensed table-big-list nomargin">
				<tr>
					<th class="no-border-top"><i class="icon-trash" title="<?php echo BUTTON_DELETE; ?>"></i></th>
					<th class="no-border-top"><?php echo TEXT_PRODUCT_OPTION; ?></th>
					<th class="no-border-top"><?php echo TEXT_PRODUCT_OPTION_VALUE; ?></th>
					<th class="no-border-top"><?php echo TEXT_PRICE.TEXT_SMALL_NETTO; ?></th>
					<th class="no-border-top"><?php echo TEXT_MODEL; ?></th>
					<th class="no-border-top"><?php echo TEXT_PRICE_PREFIX; ?></th>
				</tr>
				<?php while($attributes = os_db_fetch_array($attributes_query)) { ?>
				<tr>
					<td><input type="checkbox" name="attributes[<?php echo $attributes['orders_products_attributes_id']; ?>][delete]" value="1" /></td>
					<td><?php echo $cartet->html->input_text('attributes['.$attributes['orders_products_attributes_id']."][products_options]", $attributes['products_options']); ?></td>
					<td><?php echo $cartet->html->input_text('attributes['.$attributes['orders_products_attributes_id']."][products_options_values]", $attributes['products_options_values']); ?></td>
					<td><?php echo $cartet->html->input_text('attributes['.$attributes['orders_products_attributes_id']."][options_values_price]", $attributes['options_values_price']); ?></td>
					<td><?php echo $cartet->html->input_text('attributes['.$attributes['orders_products_attributes_id']."][attributes_model]", $attributes['attributes_model']); ?></td>
					<td>
						<select name="attributes[<?php echo $attributes['orders_products_attributes_id']; ?>][prefix]" class="width40px">
							<option value="+" <?php echo (($attributes['price_prefix'] == '+' ? 'selected' : '')); ?>>+</option>
							<option value="-" <?php echo (($attributes['price_prefix'] == '-' ? 'selected' : '')); ?>>-</option>
						</select>
					</td>
				</tr>
				<?php } ?>
			</table>

			<br />

			<label class="checkbox" title="<?php echo TEXT_RECALCULATE_DESC; ?>"><input type="checkbox" name="recalculate" value="1" /> <?php echo TEXT_RECALCULATE; ?></label>
			<hr>

			<?php
			$products_query = os_db_query("select * from ".TABLE_PRODUCTS_ATTRIBUTES." where products_id = '".(int)$_GET['p_id']."' order by sortorder");
			?>
			<table class="table table-condensed table-big-list nomargin">
				<tr>
					<th class="no-border-top"><?php echo TEXT_PRODUCT_ID; ?></th>
					<th class="no-border-top"><?php echo TEXT_QUANTITY; ?></th>
					<th class="no-border-top"><?php echo TEXT_PRODUCT; ?></th>
					<th class="no-border-top"><?php echo TEXT_PRICE; ?></th>
					<th class="no-border-top"></th>
				</tr>
				<?php while($products = os_db_fetch_array($products_query)) { ?>
				<tr>
					<?php
					$brutto = PRICE_IS_BRUTTO;
					if($brutto == 'true')
						$options_values_price = os_round(($products['options_values_price']*(1+($_GET['pTX']/100))), PRICE_PRECISION);
					else
						$options_values_price = os_round($products['options_values_price'], PRICE_PRECISION);
					?>
					<td><?php echo $products['products_attributes_id'];?></td>
					<td><?php echo os_get_options_name($products['options_id']);?></td>
					<td><?php echo os_get_options_values_name($products['options_values_id']);?></td>
					<td>
					<?php echo os_draw_hidden_field('options_values_price', $products['options_values_price']);?>
					<?php echo $osPrice->Format($osPrice->CalculateCurr($options_values_price),true);?>
					</td>
					<td width="40" class="tright"><a href="#" class="preload btn btn-mini" data-load-page="orders&action=edit_attributes" data-action="order_addAttributeToProduct_get" data-params="o_id=<?php echo $_GET['o_id']; ?>&p_id=<?php echo $_GET['p_id']; ?>&op_id=<?php echo $_GET['op_id']; ?>&add_attr=<?php echo $products['products_attributes_id'];?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-plus"></i></a></td>
				</tr>
				<?php } ?>
			</table>

		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('order_options_update', BUTTON_UPDATE, array('class' => 'btn btn btn-success save-form', 'data-form-action' => 'order_editAttributes', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'edit_other') { ?>

	<?php echo $cartet->html->form('orders_form', 'ajax.php', 'ajax_action=order_editOther', 'post', array('id' => 'orders_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="order_id" value="<?php echo $_GET['o_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4><?php echo TEXT_EDIT_OTHER; ?></h4>
		</div>
		<div class="modal-body">

			<div class="row-fluid">
				<div class="span4">
					<?php
					// языки
					$lang_query = os_db_query("select languages_id, name, directory from ".TABLE_LANGUAGES." ");
					$langs = array();
					while($lang = os_db_fetch_array($lang_query))
					{
						$langs[$lang['directory']] = $lang;
					}
					$currentLang = ($langs[$order->info['language']]['name']) ? $langs[$order->info['language']]['name'] : $order->info['language'];
					?>
					<strong><?php echo TEXT_LANGUAGE; ?> (<?php echo $currentLang; ?>)</strong><br />
					<select name="order_lang">
					<?php
					foreach ($langs AS $langDir => $lang)
					{
						$selected = ($langDir == $order->info['language']) ? 'selected' : '';
						?>
						<option value="<?php echo $lang['directory']; ?>_<?php echo $lang['languages_id']; ?>" <?php echo $selected; ?>><?php echo $lang['name'];?></option>
						<?php
					}
					?>
					</select>
				</div>

				<div class="span4">
					<?php $getPaymentModules = $cartet->payment->getInstalled(array('lang' => $order->info['language'])); ?>
					<strong><?php echo TEXT_PAYMENT; ?> (<?php echo ($getPaymentModules[$order->info['payment_class']]['text']) ? $getPaymentModules[$order->info['payment_class']]['text'] : $order->info['payment_class']; ?>)</strong><br />
					<?php echo os_draw_pull_down_menu('payment_method', $getPaymentModules, $order->info['payment_class']); ?>
				</div>
				<div class="span4">
					<?php
					$curr_query = os_db_query("select currencies_id, title, code, value from ".TABLE_CURRENCIES." ");
					$currencies = array();
					while($curr = os_db_fetch_array($curr_query))
					{
						$currencies[$curr['code']] = $curr;
					}
					$currentCurr = ($currencies[$order->info['currency']]['title']) ? $currencies[$order->info['currency']]['title'] : $order->info['currency'];
					?>
					<strong><?php echo TEXT_CURRENCIES; ?> (<?php echo $currentCurr; ?>)</strong><br />
					<select name="order_currencies">
					<?php
					foreach ($currencies AS $currCode => $currencie)
					{
						$selected = ($currCode == $order->info['currency']) ? 'selected' : '';
						?>
						<option value="<?php echo $currencie['currencies_id']; ?>" <?php echo $selected; ?>><?php echo $currencie['title'];?></option>
						<?php
					}
					?>
					</select>
					<input type="hidden" name="old_currencies_id" value="<?php echo $currencies[$order->info['currency']]['currencies_id']; ?>" />
				</div>
			</div>
			<script>
				/*
				 ------------------------------------------------------
				 При изменении способа доставки подставляем название в поле
				 ------------------------------------------------------
				 */
				jQuery(function(){
					$('#change_order_shipping_method').on('change', function()
					{
						$( "input[name='total[ot_shipping][title]']" ).val($('#change_order_shipping_method option:selected').text());
					});
				});
			</script>
			<h5><?php echo TEXT_ORDER_TOTAL; ?></h5>
			<table class="table table-condensed table-big-list">
				<tr>
					<th><i class="icon-trash"></i></th>
					<th></th>
					<th><?php echo TEXT_EDIT_DESC; ?></th>
					<th><?php echo TEXT_EDIT_PRICE; ?></th>
					<th></th>
				</tr>
			<?php
			$totals = preg_split('/;/', MODULE_ORDER_TOTAL_INSTALLED);
			for ($i = 0; $i < count($totals); $i++)
			{
				require(_MODULES.'order_total/'.substr($totals[$i], 0, strrpos($totals[$i], '.')).'/'.$order->info['language'].'.php');	
				$total = substr($totals[$i], 0, strrpos($totals[$i], '.'));
				$total_name = str_replace('ot_', '', $total); 

				if (defined(MODULE_ORDER_TOTAL_.strtoupper($total_name)._TITLE))
					$total_text = constant(MODULE_ORDER_TOTAL_.strtoupper($total_name)._TITLE);
				else
					$total_text = 'none';

				$ototal_query = os_db_query("SELECT * FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id = '".(int)$_GET['o_id']."' AND class = '".$total."' ");
				$ototal = os_db_fetch_array($ototal_query);
				?>
				<tr>
					<td>
						<?php if (isset($ototal['orders_total_id'])) { ?>
						<input type="checkbox" name="total[<?php echo $total; ?>][total_delete]" value="1" />
						<?php } ?>
						<?php
						echo os_draw_hidden_field('total['.$total.'][orders_total_id]', $ototal['orders_total_id']);
						echo os_draw_hidden_field('total['.$total.'][class]', $total);
						if (defined(MODULE_ORDER_TOTAL_.strtoupper($total)._SORT_ORDER))
							echo os_draw_hidden_field('total['.$total.'][sort_order]', constant(MODULE_ORDER_TOTAL_.strtoupper($total_name)._SORT_ORDER));
						else
							echo os_draw_hidden_field('total['.$total.'][sort_order]', '0');
						?>
					</td>
					<td><?php echo $total_text; ?></td>
					<td><?php echo os_draw_input_field('total['.$total.'][title]', $ototal['title']); ?></td>
					<td><?php echo os_draw_input_field('total['.$total.'][value]', $ototal['value']); ?></td>
					<td><?php if ($total == 'ot_shipping') {
							$getShippingModules = $cartet->shipping->getInstalled(array('lang' => $order->info['language']));
							$aShipping = explode('_', $order->info['shipping_class']);
							$cShipping = $aShipping[0];
							?>
							<?php echo os_draw_pull_down_menu('total['.$total.'][shipping_method]', $getShippingModules, $cShipping, 'id="change_order_shipping_method"'); ?>
						<?php } ?>
					</td>
				</tr>
				<?php 
			}
			?>
			</table>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('order_other_update', BUTTON_UPDATE, array('class' => 'btn btn btn-success save-form', 'data-form-action' => 'order_editOther', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>
<?php } ?>