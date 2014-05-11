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
if (isset($_GET['c_id']) && !empty($_GET['c_id']))
{
	$special = $cartet->specials->getCategoryById($_GET['c_id']);

	$per =  $special['discount_type'] == "p" ? "%" : "";
}
else
{
	$special = array();
	$allCategories = $cartet->products->getCategories(array(array('id' => '', 'text' => CATEGORIES_LIST)));
}

if ($_GET['action'] == 'edit' OR $_GET['action'] == 'new')
{
?>
	<?php echo $cartet->html->form('specials_form', 'ajax.php', 'ajax_action=specials_saveCategory', 'post', array('id' => 'specials_form', 'class' => 'form-inline')); ?>

		<?php if (isset($_GET['c_id']) && !empty($_GET['c_id'])) { ?>
		<input type="hidden" name="special_id" value="<?php echo $special['special_id']; ?>" />
		<input type="hidden" name="categ_id" value="<?php echo $special['categ_id']; ?>" />
		<?php } ?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php
				if (isset($_GET['c_id']) && !empty($_GET['c_id']))
					echo SPECIALS_EDIT_CATEGORY.': '.$special['categories_name'];
				else
					echo SPECIALS_ADD_CATEGORY;
				?>
			</h4>
		</div>
		<div class="modal-body">

			<?php if (!isset($_GET['c_id']) && empty($_GET['c_id'])) { ?>
			<div class="control-group">
				<label class="control-label" for=""><?php echo TABLE_HEADING_CATEGORIES; ?></label>
				<div class="controls">
					<?php echo $cartet->html->select('categ_id', $allCategories); ?>
				</div>
			</div>
			<?php } ?>

			<div class="row-fluid">
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('expire_date', $special['expire_date'], array(
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
							<?php echo os_draw_input_field('specials_price', (isset($special['discount']) ? ($special['discount'] . $per) : '')); ?>
						</div>
					</div>
				</div>
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for=""><?php echo TABLE_HEADING_STATUS; ?></label>
						<div class="controls">
							<select name="status">
								<option value="1" <?php echo ($special['status'] == 1 OR !isset($special['status'])) ? 'selected' : ''; ?>><?php echo ON; ?></option>
								<option value="0" <?php echo (isset($special['status']) && $special['status'] == 0) ? 'selected' : ''; ?>><?php echo OFF; ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<hr>

			<label class="checkbox"><?php echo os_draw_checkbox_field('override', 'y'); ?> <?php echo TEXT_SPECIALS_OVERRIDE; ?></label>

			<hr>

			<div class="alert alert-info nomargin"><?php echo TEXT_SPECIALS_PRICE_TIP; ?></div>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('special_add', BUTTON_SAVE, array('class' => 'btn btn-success save-form', 'data-form-action' => 'specials_saveCategory', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'delete') { ?>

	<?php echo $cartet->html->form('specials_form', 'ajax.php', 'ajax_action=specials_deleteCategory', 'post', array('id' => 'specials_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="special_id" value="<?php echo $special['special_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_DELETE_SPECIALS.' - '.$special['categories_name']; ?>
			</h4>
		</div>
		<div class="modal-body">
			<?php echo TEXT_INFO_DELETE_INTRO_CAT; ?>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('special_delete', BUTTON_DELETE, array('class' => 'btn btn btn-danger save-form', 'data-form-action' => 'specials_deleteCategory', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } ?>