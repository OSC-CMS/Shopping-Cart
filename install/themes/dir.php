<h1><?php echo t('step_3'); ?></h1>
<p><?php echo t('dir_1'); ?></p>
<table class="table">
<?php
$errors = array();
foreach ($checkWritables AS $name => $path)
{
	if ((is_writable($path)))
	{
		$status = text_status(t('yes'), 1);
	}
	else
	{
		$status = text_status(t('no'), 0);
		$errors[$name] = 1;
	}
	?>
	<tr>
		<td><?php echo $name; ?></td>
		<td class="right"><?php echo $status; ?></td>
	</tr>
	<?php
}
?>
</table>

<?php if(!$errors){ ?>
    <div class="buttons">
        <input class="button button-rounded button-flat-action" type="button" name="next" id="btn-next" value="<?php echo t('next'); ?>" onclick="nextStep()" />
    </div>
<?php } else { ?>
    <p><?php echo t('step_error'); ?></p>
	<div class="buttons">
		<input class="button button-rounded button-flat-primary" type="button" name="update" id="btn-update" value="<?php echo t('update'); ?>" onclick="updateStep()" />
	</div>
<?php } ?>