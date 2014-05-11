<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include 'lang/'.$_SESSION['language_admin'].'/specials.php';

require(_CLASS.'price.php');
$osPrice = new osPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

// Если редактируем скидку
if (isset($_GET['s_id']) && !empty($_GET['s_id']))
{
	$special = $cartet->specials->getById($_GET['s_id']);

	$price = $special['products_price'];
	$new_price = $special['specials_new_products_price'];
	if (PRICE_IS_BRUTTO == 'true')
	{
		//$price_netto = os_round($price, PRICE_PRECISION);
		//$new_price_netto = os_round($new_price, PRICE_PRECISION);
		$price = ($price * (os_get_tax_rate($special['products_tax_class_id']) + 100) / 100);
		$new_price = ($new_price * (os_get_tax_rate($special['products_tax_class_id']) + 100) / 100);
	}
	$price = os_round($price, PRICE_PRECISION);
	$new_price = os_round($new_price, PRICE_PRECISION);
}
else
{
	$special = array();
	$allCategories = $cartet->products->getCategories(array(array('id' => '', 'text' => CATEGORIES_LIST)));
}

if ($_GET['action'] == 'edit' OR $_GET['action'] == 'new')
{
?>
	<?php echo $cartet->html->form('specials_form', 'ajax.php', 'ajax_action=specials_save', 'post', array('id' => 'specials_form', 'class' => 'form-inline')); ?>

		<?php if (isset($_GET['s_id']) && !empty($_GET['s_id'])) { ?>
		<input type="hidden" name="specials_id" value="<?php echo $special['specials_id']; ?>" />
		<input type="hidden" name="products_price" value="<?php echo $special['products_price']; ?>" />
		<input type="hidden" name="products_id" value="<?php echo $special['products_id']; ?>" />
		<?php } ?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php
				if (isset($_GET['s_id']) && !empty($_GET['s_id']))
					echo SPECIALS_EDIT_PRODUCT;
				else
					echo SPECIALS_ADD_PRODUCT;
				?>
			</h4>
		</div>
		<div class="modal-body">

			<?php if (!isset($_GET['s_id']) && empty($_GET['s_id'])) { ?>
			<div class="control-group">
				<label class="control-label" for=""><?php echo TEXT_SPECIALS_PRODUCT; ?></label>
				<div class="controls">
					<?php echo $cartet->html->select('categories_select', $allCategories, '', array('class' => 'change_select', 'data-ajax-action' => 'load_products', 'data-sub-select' => 'products_id', 'data-sub-select-value' => 'products_id', 'data-sub-select-title' => 'products_name')); ?>
					 
					<?php echo $cartet->html->select('products_id', array(), '', array('id' => 'products_id', 'disabled' => 'disabled')); ?>
				</div>
			</div>
			<?php } ?>

			<?php if (isset($_GET['s_id']) && !empty($_GET['s_id'])) { ?>
			<div class="row-fluid">
				<div class="span4">
					<strong><?php echo $special['products_name']; ?></strong><br />
					<?php echo TEXT_INFO_ORIGINAL_PRICE; ?>: <?php echo $osPrice->Format($price, true); ?>
				</div>
				<div class="span4">
					<span class="label label-success"><?php echo TEXT_INFO_NEW_PRICE . ' ' . $osPrice->Format($special['specials_new_products_price'], true); ?></span>
					<br />
					<?php
					if ($special['products_price'] != 0)
						echo TEXT_INFO_PERCENTAGE . ' ' . number_format(100 - (($special['specials_new_products_price'] / $special['products_price']) * 100)) . '%';
					else 
						echo TEXT_INFO_PERCENTAGE . ' ' . number_format(100) . '%';
					?>
				</div>
			</div>
			<hr>
			<?php } ?>

			<div class="row-fluid">
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('expires_date', $special['expires_date'], array(
								'class' => 'form_datetime',
								'data-date-format' => 'yyyy-mm-dd hh:ii:ss',
								'data-date-today-btn' => 'true',
								'data-date-autoclose' => 'true'
								))
							; ?>
						</div>
					</div>
				</div>
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_SPECIALS_SPECIAL_PRICE; ?></label>
						<div class="controls">
							<?php echo os_draw_input_field('specials_price', $new_price);?>
						</div>
					</div>
				</div>
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_SPECIALS_SPECIAL_QUANTITY; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input('specials_quantity', $special['specials_quantity'], array('type' => 'number')); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for=""><?php echo TABLE_HEADING_STATUS; ?></label>
				<div class="controls">
					<select name="status">
						<option value="1" <?php echo ($special['status'] == 1 OR !isset($special['status'])) ? 'selected' : ''; ?>><?php echo ON; ?></option>
						<option value="0" <?php echo (isset($special['status']) && $special['status'] == 0) ? 'selected' : ''; ?>><?php echo OFF; ?></option>
					</select>
				</div>
			</div>
			<hr>
			<div class="alert alert-info nomargin"><?php echo TEXT_SPECIALS_PRICE_TIP; ?></div>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('specials_add', BUTTON_SAVE, array('class' => 'btn btn-success save-form', 'data-form-action' => 'specials_save', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'delete') { ?>

	<?php echo $cartet->html->form('specials_form', 'ajax.php', 'ajax_action=specials_delete', 'post', array('id' => 'specials_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="specials_id" value="<?php echo $special['specials_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_DELETE_SPECIALS.' - '.$special['products_name']; ?>
			</h4>
		</div>
		<div class="modal-body">
			<?php echo TEXT_INFO_DELETE_INTRO; ?>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('special_delete', BUTTON_DELETE, array('class' => 'btn btn btn-danger save-form', 'data-form-action' => 'specials_delete', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } ?>