<h1><?php echo t('step_4'); ?></h1>

<h2><?php echo t('php_1'); ?></h2>
<table class="table">
    <tr>
        <td><?php echo t('php_2'); ?></td>
        <td class="right">
            <?php echo text_status($info['php']['version'], $info['php']['valid']); ?>
        </td>
    </tr>
</table>

<h2><?php echo t('php_3'); ?></h2>
<p><?php echo t('php_4'); ?></p>

<table class="table">
    <?php foreach($info['ext'] as $name=>$valid) { ?>
    <tr>
        <td><?php echo $name; ?></td>
        <td class="right">
            <?php if ($valid) { ?>
                <?php echo text_status(t('installed'), $valid); ?>
            <?php } else { ?>
                <?php echo text_status(t('not_installed'), $valid); ?>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table>

<h2><?php echo t('php_5'); ?></h2>
<p><?php echo t('php_6'); ?></p>

<table class="table">
    <?php foreach($info['ext_extra'] as $name=>$valid) { ?>
    <tr>
        <td><?php echo $name; ?></td>
        <td class="right">
            <?php if ($valid) { ?>
	            <?php echo text_status(t('installed'), 1); ?>
            <?php } else { ?>
	            <?php echo text_status(t('not_installed'), 0); ?>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table>

<?php if($info['valid']){ ?>
    <div class="buttons">
        <input class="button button-rounded button-flat-action" type="button" name="next" id="btn-next" value="<?php echo t('next'); ?>" onclick="nextStep()" />
    </div>
<?php } else { ?>
    <p><?php echo t('step_error'); ?></p>
	<div class="buttons">
		<input class="button button-rounded button-flat-primary" type="button" name="update" id="btn-update" value="<?php echo t('update'); ?>" onclick="updateStep()" />
	</div>
<?php } ?>