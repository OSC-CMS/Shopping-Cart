<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

if (isset($_GET['action']) && $_GET['action'] == 'delete_status')
	include 'lang/'.$_SESSION['language_admin'].'/customers_status.php';
else
	include 'lang/'.$_SESSION['language_admin'].'/customers.php';

if ($_GET['action'] == 'editstatus' && !empty($_GET['c_id'])) { ?>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4>
			<?php echo TEXT_INFO_HEADING_STATUS_CUSTOMER; ?>
		</h4>
	</div>
	<div class="modal-body modal-body-mh500">
		<?php
		if ($_GET['c_id'] != 1)
		{
			$customers_statuses_array = os_get_customers_statuses();

			$customers_history_query = os_db_query("select new_value, old_value, date_added, customer_notified from ".TABLE_CUSTOMERS_STATUS_HISTORY." where customers_id = '".(int)$_GET['c_id']."' order by customers_status_history_id desc");

			//echo '<p>'.os_draw_pull_down_menu('status', $customers_statuses_array, $_GET['c_status']).'</p>'; 
			echo '
				<table class="table table-condensed table-big-list">
					<thead><tr>
						<th>'.TABLE_HEADING_NEW_VALUE.'</th>
						<th><span class="line"></span>'.TABLE_HEADING_DATE_ADDED.'</th>
					</tr></thead>';

			if (os_db_num_rows($customers_history_query))
			{
				while ($customers_history = os_db_fetch_array($customers_history_query))
				{
					echo '<tr>';
						echo '<td>'.$customers_statuses_array[$customers_history['new_value']]['text'].'</td>';
						echo '<td>'.$customers_history['date_added'].'</td>';
					echo '</tr>';
				}
			}
			else
				echo '<tr><td colspan="2">'.TEXT_NO_CUSTOMER_HISTORY.'</td></tr>';

			echo '</table>';
		}
		?>
	</div>
	<div class="modal-footer">
		<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
	</div>

<?php } elseif ($_GET['action'] == 'delete') { ?>

	<?php echo $cartet->html->form('customers_form', 'ajax.php', 'ajax_action=customers_delete', 'post', array('id' => 'customers_form', 'class' => 'form-inline')); ?>

		<?php
		$customers_query = os_db_query("select customers_firstname, customers_lastname from ".TABLE_CUSTOMERS." WHERE customers_id = '".(int)$_GET['c_id']."'");
		$customers = os_db_fetch_array($customers_query);

		$reviews_query = os_db_query("select count(reviews_id) number_of_reviews from ".TABLE_REVIEWS." where customers_id = '".(int)$_GET['c_id']."'");
		$reviews = os_db_fetch_array($reviews_query);
		?>

		<input type="hidden" name="id" value="<?php echo $_GET['c_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_DELETE_CUSTOMER.' - '.$customers['customers_firstname'].' '.$customers['customers_lastname']; ?>
			</h4>
		</div>
		<div class="modal-body">
			<p><?php echo TEXT_DELETE_INTRO; ?></p>

			<hr>
			<p>
				<label class="checkbox">
					<?php echo os_draw_checkbox_field('reviews'); ?> <?php echo sprintf(TEXT_DELETE_REVIEWS, $reviews['number_of_reviews']); ?>
				</label>
			</p>

		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('customer_delete', BUTTON_DELETE, array('class' => 'btn btn btn-danger save-form', 'data-form-action' => 'customers_delete', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'delete_status') { ?>

	<?php echo $cartet->html->form('customers_form', 'ajax.php', 'ajax_action=customers_deleteStatus', 'post', array('id' => 'customers_form', 'class' => 'form-inline')); ?>

		<?php
		$cID = os_db_prepare_input($_GET['s_id']);

		$status_query = os_db_query("select count(*) as count from ".TABLE_CUSTOMERS." where customers_status = '".os_db_input($cID)."'");
		$status = os_db_fetch_array($status_query);

		$error = '';
		if (($cID == DEFAULT_CUSTOMERS_STATUS_ID) || ($cID == DEFAULT_CUSTOMERS_STATUS_ID_GUEST) || ($cID == DEFAULT_CUSTOMERS_STATUS_ID_NEWSLETTER))
		{
			$error = ERROR_REMOVE_DEFAULT_CUSTOMER_STATUS;
		}
		elseif ($status['count'] > 0)
		{
			$error = ERROR_STATUS_USED_IN_CUSTOMERS;
		}
		?>

		<input type="hidden" name="id" value="<?php echo $cID; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo TEXT_INFO_HEADING_DELETE_CUSTOMERS_STATUS; ?>
			</h4>
		</div>
		<div class="modal-body">
			<p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>

			<?php if (!empty($error)) { ?>
			<hr>
			<div class="alert alert-error"><?php echo $error; ?></div>
			<?php } ?>

		</div>
		<div class="modal-footer">
			<?php if (empty($error)) { ?>
			<?php echo $cartet->html->input_submit('customer_delete', BUTTON_DELETE, array('class' => 'btn btn btn-danger save-form', 'data-form-action' => 'customers_deleteStatus', 'data-reload-page' => 1)); ?>
			<?php } ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } elseif ($_GET['action'] == 'add_memo') { ?>

	<?php echo $cartet->html->form('customers_form', 'ajax.php', 'ajax_action=customers_addMemo', 'post', array('id' => 'customers_form', 'class' => 'form-inline')); ?>

		<input type="hidden" name="customers_id" value="<?php echo $_GET['c_id']; ?>" />

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4>
				<?php echo ENTRY_MEMO; ?>
			</h4>
		</div>
		<div class="modal-body">
			<div class="row-fluid">
			<div class="control-group">
				<label class="control-label" for="memo_title"><?php echo TEXT_TITLE ?></label>
				<div class="controls">
					<input class="span12" type="text" id="memo_title" name="memo_title" value="" required>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="memo_text"><?php echo TEXT_TEXT; ?></label>
				<div class="controls">
					<textarea class="span12" id="memo_text" name="memo_text" required></textarea>
				</div>
			</div>
			</div>
		</div>
		<div class="modal-footer">
			<?php echo $cartet->html->input_submit('customer_delete', BUTTON_INSERT, array('class' => 'btn btn btn-success save-form', 'data-form-action' => 'customers_addMemo', 'data-reload-page' => 1)); ?>
			<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
		</div>
	</form>

<?php } ?>