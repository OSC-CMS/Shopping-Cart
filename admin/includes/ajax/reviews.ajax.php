<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

include 'lang/'.$_SESSION['language_admin'].'/reviews.php';

$review = $cartet->reviews->getById($_GET['r_id']);

if ($_GET['action'] == 'edit') { ?>
	<?php echo $cartet->html->form('reviews_form', 'ajax.php', 'ajax_action=reviews_save', 'post', array('id' => 'reviews_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="reviews_id" value="<?php echo $review['reviews_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo HEADING_TITLE_EDIT; ?>
			</h4>
		</div>
		<div class="modal-body">

			<div class="control-group">
				<label class="control-label" for=""><?php echo ENTRY_REVIEW; ?> (<?php echo ENTRY_REVIEW_TEXT; ?>)</label>
				<div class="controls">
					<?php echo $cartet->html->textarea('reviews_text', $review['reviews_text'], array('class' => 'input-block-level', 'rows' => '4'), true); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for=""><?php echo ENTRY_REVIEW_ADMIN; ?> (<?php echo ENTRY_REVIEW_TEXT; ?>)</label>
				<div class="controls">
					<?php echo $cartet->html->textarea('reviews_text_admin', $review['reviews_text_admin'], array('class' => 'input-block-level', 'rows' => '3'), true); ?>
				</div>
			</div>

			<p>
				<?php echo ENTRY_PRODUCT; ?>: <a target="_blank" href="<?php echo FILENAME_CATEGORIES; ?>?pID=<?php echo $review['products_id']; ?>&action=new_product"><?php echo $review['products_name']; ?></a>
				<input type="hidden" name="products_id" value="<?php echo $review['products_id']; ?>" />
			</p>

			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_INFO_REVIEW_AUTHOR; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('customers_name', $review['customers_name'], array('class' => 'input-block-level')); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for=""><?php echo TABLE_HEADING_STATUS; ?></label>
						<div class="controls">
							<select name="status" class="span12">
								<option value="1" <?php echo ($review['status'] == 1) ? 'selected' : ''; ?>><?php echo ON; ?></option>
								<option value="0" <?php echo ($review['status'] == 0) ? 'selected' : ''; ?>><?php echo OFF; ?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="control-group pull-left w48">
						<label class="control-label" for=""><?php echo ENTRY_RATING; ?></label>
						<div class="controls">
							<select name="reviews_rating" class="span12">
							<?php
							for ($i = 1; $i <= 5; $i++)
							{
								$bad = ($i == 1) ? ' '.TEXT_BAD : '';
								$good = ($i == 5) ? ' '.TEXT_GOOD : '';
								$selected = ($i == $review['reviews_rating']) ? ' selected ' : '';
								echo '<option value="'.$i.'" '.$selected.'>'.$i.$bad.$good.'</option>';
							}
							?>
							</select>
						</div>
					</div>
					<div class="control-group pull-right w48">
						<label class="control-label" for=""><?php echo TEXT_INFO_REVIEW_READ; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('reviews_read', $review['reviews_read'], array('class' => 'span12')); ?>
						</div>
					</div>
					<div class="clear"></div>

					<div class="control-group pull-left w48">
						<label class="control-label" for=""><?php echo TEXT_INFO_DATE_ADDED; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('date_added', $review['date_added'], array('class' => 'span12')); ?>
						</div>
					</div>
					<div class="control-group pull-right w48">
						<label class="control-label" for=""><?php echo TEXT_INFO_LAST_MODIFIED; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('last_modified', $review['last_modified'], array('class' => 'span12', 'disabled' => 'disabled')); ?>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>

		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('review_edit', BUTTON_SAVE, array('class' => 'btn btn-success save-form', 'data-form-action' => 'reviews_save', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'delete') { ?>

	<?php echo $cartet->html->form('reviews_form', 'ajax.php', 'ajax_action=reviews_delete', 'post', array('id' => 'reviews_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="reviews_id" value="<?php echo $review['reviews_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_DELETE_REVIEW.' - '.$review['products_name']; ?>
			</h4>
		</div>
		<div class="modal-body">
			<?php echo TEXT_INFO_DELETE_REVIEW_INTRO; ?>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('reviews_delete', BUTTON_DELETE, array('class' => 'btn btn btn-danger save-form', 'data-form-action' => 'reviews_delete', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } ?>