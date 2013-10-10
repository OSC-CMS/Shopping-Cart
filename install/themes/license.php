<h1><?php echo t('step_2'); ?></h1>

<p><?php echo t('license_1'); ?></p>

<textarea class="license-text" id="gpl-en" readonly><?php echo $license_text; ?></textarea>

<form id="step-form">
    <p>
        <label>
            <input type="checkbox" value="1" name="agree">
	        <?php echo t('license_2'); ?>
        </label>
    </p>
</form>

<div class="buttons">
    <input class="button button-rounded button-flat-action" type="button" name="next" id="btn-next" value="<?php echo t('next'); ?>" onclick="submitStep()" />
</div>