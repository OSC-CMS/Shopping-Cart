<h1><?php echo t('step_5'); ?></h1>

<p><?php echo t('db_1'); ?></p>

<form id="step-form">
	<fieldset>

		<div class="field">
			<label><?php echo t('db_2'); ?></label>
			<input type="text" class="input input-icon icon-db-server" name="db[host]" value="localhost" />
		</div>

		<div class="field">
			<label><?php echo t('db_3'); ?></label>
			<input type="text" class="input input-icon icon-user" name="db[user]" value="" />
		</div>

		<div class="field">
			<label><?php echo t('db_4'); ?></label>
			<input type="password" class="input input-icon icon-password" name="db[pass]" value="" />
		</div>

		<div class="field">
			<label><?php echo t('db_5'); ?></label>
			<input type="text" class="input input-icon icon-db" name="db[base]" value="" />
		</div>

		<div class="field">
			<label><?php echo t('db_6'); ?></label>
			<input type="text" class="input input-icon icon-db-prefix" name="db[prefix]" value="cet_" />
		</div>
	</fieldset>

	<fieldset>
		<div class="field">
			<label><input type="radio" name="db[sessions]" value="files" checked="checked"><?php echo t('db_7'); ?></label>
			<label><input type="radio" name="db[sessions]" value="mysql"><?php echo t('db_8'); ?></label>
		</div>
	</fieldset>

	<fieldset>
		<div class="field">
			<label><input type="checkbox" value="1" name="demo"> <?php echo t('db_9'); ?></label>
		</div>
	</fieldset>
</form>

<div class="buttons">
	<input class="button button-rounded button-flat-action" type="button" name="next" id="btn-next" value="<?php echo t('next'); ?>" onclick="submitStep()" />
</div>