<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include 'lang/'.$_SESSION['language_admin'].'/manufacturers.php';
$languages = os_get_languages();

require_once(CLS_NEW.'manufacturers.class.php');
$manufacturers = new manufacturers();

if ($_GET['action'] == 'edit' OR $_GET['action'] == 'new')
{
	// Если редактируем производителя
	if (isset($_GET['m_id']) && !empty($_GET['m_id']))
	{
		$manufacturer = $manufacturers->getById($_GET['m_id']);

		$aManufacturersInfo = $manufacturers->getInfoById($manufacturer['manufacturers_id']);
	}
	?>

	<?php echo $cartet->html->form('manufacturers_form', 'ajax.php', 'ajax_action=manufacturers_save', 'post', array('enctype' => 'multipart/form-data', 'id' => 'manufacturers_form', 'class' => 'form-inline')); ?>

		<?php if (isset($_GET['m_id']) && !empty($_GET['m_id'])) { ?>
		<input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer['manufacturers_id']; ?>" />
		<?php } ?>

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php
				if (isset($_GET['m_id']) && !empty($_GET['m_id']))
					echo TEXT_HEADING_EDIT_MANUFACTURER;
				else
					echo TEXT_HEADING_NEW_MANUFACTURER;
				?>
			</h4>
		</div>
		<div class="modal-body">

			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_MANUFACTURERS_NAME; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('manufacturers_name', $manufacturer['manufacturers_name'], array('class' => 'input-block-level'), true); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_MANUFACTURERS_SEO_URL; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('manufacturers_page_url', $manufacturer['manufacturers_page_url'], array('class' => 'input-block-level')); ?>
						</div>
					</div>
					<hr>
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_MANUFACTURERS_IMAGE; ?></label>
						<div class="controls">
							<?php echo os_draw_file_field('manufacturers_image'); ?>
							<div class="modal-image-box">
								<?php echo os_info_image('manufacturers/'.$manufacturer['manufacturers_image'], ''); ?>
							</div>
							<?php if (!empty($manufacturer['manufacturers_image'])) { ?>
							<p>
								<label class="checkbox">
									<input type="hidden" name="manufacturers_image_current" value="<?php echo $manufacturer['manufacturers_image']; ?>" />
									<?php echo os_draw_checkbox_field('delete_image'); ?>
									<?php echo TEXT_DELETE_IMAGE; ?>
								</label>
							</p>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<ul class="nav nav-tabs default-tabs">
					<?php for ($i=0, $n=sizeof($languages); $i<$n; $i++) { if ($languages[$i]['status'] == 1) { $current = ($i == 0) ? ' class="active"' : ''; ?>
						<li<?php echo $current; ?>><a href="#manu_lang_<?php echo $languages[$i]['id']; ?>" data-toggle="tab"><?php echo $languages[$i]['name']; ?></a></li>
					<?php }} ?>
					</ul>

					<div class="tab-content">
						<?php for ($i=0, $n=sizeof($languages); $i<$n; $i++) { if ($languages[$i]['status'] == 1) { $current = ($i == 0) ? 'active' : ''; ?>
						<div class="tab-pane <?php echo $current; ?>" id="manu_lang_<?php echo $languages[$i]['id']; ?>">
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_MANUFACTURERS_DESCRIPTION; ?></label>
								<div class="controls">
									<?php echo $cartet->html->textarea('manufacturers_description['.$languages[$i]['id'].']', $aManufacturersInfo[$languages[$i]['id']]['manufacturers_description'], array('class' => 'input-block-level')); ?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_MANUFACTURERS_URL; ?></label>
								<div class="controls">
									<?php echo $cartet->html->input_text('manufacturers_url['.$languages[$i]['id'].']', $aManufacturersInfo[$languages[$i]['id']]['manufacturers_url'], array('class' => 'input-block-level')); ?>
								</div>
							</div>
							<hr>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_MANUFACTURERS_META_TITLE; ?></label>
								<div class="controls">
									<?php echo $cartet->html->input_text('manufacturers_meta_title['.$languages[$i]['id'].']', $aManufacturersInfo[$languages[$i]['id']]['manufacturers_meta_title'], array('class' => 'input-block-level'));?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_MANUFACTURERS_META_KEYWORDS; ?></label>
								<div class="controls">
									<?php echo $cartet->html->input_text('manufacturers_meta_keywords['.$languages[$i]['id'].']', $aManufacturersInfo[$languages[$i]['id']]['manufacturers_meta_keywords'], array('class' => 'input-block-level')); ?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_MANUFACTURERS_META_DESCRIPTION; ?></label>
								<div class="controls">
									<?php echo $cartet->html->input_text('manufacturers_meta_description['.$languages[$i]['id'].']', $aManufacturersInfo[$languages[$i]['id']]['manufacturers_meta_description'], array('class' => 'input-block-level')); ?>
								</div>
							</div>
						</div>
						<?php }} ?>
					</div>
				</div>
			</div>

		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('manufactur_add', BUTTON_SAVE, array('class' => 'btn btn-success save-form', 'data-form-action' => 'manufacturers_save', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'delete') {
	$productsCount = $manufacturers->getProductsCount($manufacturer['manufacturers_id']);
?>

	<?php echo $cartet->html->form('manufacturers_form', 'ajax.php', 'ajax_action=manufacturers_delete', 'post', array('enctype' => 'multipart/form-data', 'id' => 'manufacturers_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer['manufacturers_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_HEADING_DELETE_MANUFACTURER.' - '.$manufacturer['manufacturers_name']; ?>
			</h4>
		</div>
		<div class="modal-body">
			<p>
				<label class="checkbox">
					<?php echo os_draw_checkbox_field('delete_image', '', true); ?>
					<?php echo TEXT_DELETE_IMAGE; ?>
				</label>
			</p>

			<?php if ($productsCount > 0) { ?>
			<hr>
			<p>
				<div class="alert alert-error"><?php echo sprintf(TEXT_DELETE_WARNING_PRODUCTS, $productsCount); ?></div>
				<label class="checkbox">
					<?php echo os_draw_checkbox_field('delete_products'); ?>
					<?php echo TEXT_DELETE_PRODUCTS; ?>
				</label>
			</p>
			<?php } ?>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('manufactur_delete', BUTTON_DELETE, array('class' => 'btn btn btn-danger save-form', 'data-form-action' => 'manufacturers_delete', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } ?>