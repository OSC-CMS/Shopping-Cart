<h1><?php echo t('step_1'); ?></h1>

<div class="warning"><?php echo t('start_1'); ?></div>

<p><?php echo t('start_2'); ?></p>

<p><?php echo t('start_3'); ?></p>

<p><?php echo t('start_4'); ?></p>

<h3><?php echo t('start_5'); ?></h3>
<form id="step-form">
	<fieldset>
		<label><input type="radio" name="type" value="1" checked /><?php echo t('start_6'); ?></label><br />
		<label><input type="radio" name="type" value="2" /><?php echo t('start_7'); ?></label>
	</fieldset>
</form>

<div class="buttons">
    <input class="button button-rounded button-flat-action" type="button" name="next" id="btn-next" value="<?php echo t('next'); ?>" onclick="submitStep()" />
</div>