<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/
?>

<?php echo $cartet->html->form('notes_form', 'ajax.php', 'ajax_action=notes_save', 'post', array('id' => 'notes_form', 'class' => 'form-inline')); ?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4>
		Добавить заметку
	</h4>
</div>
<div class="modal-body">

	<div class="control-group">
		<div class="controls">
			<textarea class="input-block-level" id="note" name="note" placeholder="Введите текст заметки" rows="3"></textarea>
			<span class="help-block">HTML не поддерживается.</span>
		</div>
	</div>

</div>
<div class="modal-footer">
	<?php echo $cartet->html->input_submit('notes_add', 'Добавить заметку', array('class' => 'btn btn btn-success save-form', 'data-form-action' => 'notes_save', 'data-reload-page' => 1)); ?>
	<?php echo $cartet->html->input_submit('button_cancel', BUTTON_CANCEL, array('class' => 'btn', 'data-dismiss' => 'modal')); ?>
</div>
</form>