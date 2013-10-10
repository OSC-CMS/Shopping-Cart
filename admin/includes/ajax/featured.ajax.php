<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include 'lang/'.$_SESSION['language_admin'].'/featured.php';

require_once(CLS_NEW.'featured.class.php');
$featured = new featured();

// Если редактируем скидку
if (isset($_GET['f_id']) && !empty($_GET['f_id']))
{
	$featuredData = $featured->getById($_GET['f_id']);
	//_print_r($featuredData);
}
else
{
	$featuredData = array();
	$allCategories = $cartet->products->getCategories(array(array('id' => '', 'text' => CATEGORIES_LIST)));
}

if ($_GET['action'] == 'edit' OR $_GET['action'] == 'new')
{
?>
	<?php echo $cartet->html->form('featured_form', 'ajax.php', 'ajax_action=featured_save', 'post', array('id' => 'featured_form', 'class' => 'form-inline')); ?>

		<?php if (isset($_GET['f_id']) && !empty($_GET['f_id'])) { ?>
		<input type="hidden" name="products_id" value="<?php echo $featuredData['products_id']; ?>" />
		<input type="hidden" name="featured_id" value="<?php echo $featuredData['featured_id']; ?>" />
		<?php } ?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php
				if (isset($_GET['f_id']) && !empty($_GET['f_id']))
					echo TABLE_HEADING_ACTION_EDIT;
				else
					echo TABLE_HEADING_ACTION_ADD;
				?>
			</h4>
		</div>
		<div class="modal-body">

			<?php if (!isset($_GET['f_id']) && empty($_GET['f_id'])) { ?>
			<div class="control-group">
				<label class="control-label" for=""><?php echo TEXT_FEATURED_PRODUCT; ?></label>
				<div class="controls">
					<?php echo $cartet->html->select('categories_select', $allCategories, '', array('class' => 'change_select', 'data-ajax-action' => 'load_products', 'data-sub-select' => 'products_id', 'data-sub-select-value' => 'products_id', 'data-sub-select-title' => 'products_name')); ?>
					 
					<?php echo $cartet->html->select('products_id', array(), '', array('id' => 'products_id', 'disabled' => 'disabled')); ?>
				</div>
			</div>
			<?php } else { ?>
			<div class="bold"><?php echo $featuredData['products_name']; ?></div>
			<hr>
			<?php } ?>

			<div class="row-fluid">
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_INFO_EXPIRES_DATE; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('expires_date', $featuredData['expires_date'], array(
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
						<label class="control-label" for=""><?php echo TEXT_FEATURED_QUANTITY; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input('featured_quantity', $featuredData['featured_quantity'], array('type' => 'number')); ?>
						</div>
					</div>
				</div>
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for=""><?php echo TABLE_HEADING_STATUS; ?></label>
						<div class="controls">
							<select name="status">
								<option value="1" <?php echo ($featuredData['status'] == 1 OR !isset($featuredData['status'])) ? 'selected' : ''; ?>><?php echo ON; ?></option>
								<option value="0" <?php echo (isset($featuredData['status']) && $featuredData['status'] == 0) ? 'selected' : ''; ?>><?php echo OFF; ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('featured_add', BUTTON_SAVE, array('class' => 'btn btn-success save-form', 'data-form-action' => 'featured_save', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'delete') { ?>

	<?php echo $cartet->html->form('featured_form', 'ajax.php', 'ajax_action=featured_delete', 'post', array('id' => 'featured_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="featured_id" value="<?php echo $featuredData['featured_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_DELETE_FEATURED.' - '.$featuredData['products_name']; ?>
			</h4>
		</div>
		<div class="modal-body">
			<?php echo TEXT_INFO_DELETE_INTRO; ?>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('featured_delete', BUTTON_DELETE, array('class' => 'btn btn btn-danger save-form', 'data-form-action' => 'featured_delete', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } ?>