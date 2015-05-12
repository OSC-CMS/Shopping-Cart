<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require('includes/top.php');

$breadcrumb->add(HEADING_TITLE, 'cron.php');

$getTasks = $cartet->cron->getTasks();

if (isset($_POST['action']))
{
	$result = $cartet->cron->saveTask($_POST);

	$messageStack->add_session(TEXT_CRON_SAVE_SUCCESS, 'success');

	if ($result)
		os_redirect(os_href_link('cron.php'));
}

if (isset($_GET['action']) && $_GET['action'] == 'delete')
{
	$result = $cartet->cron->deleteTask($_GET['task_id']);

	$messageStack->add_session(TEXT_CRON_DELETE_SUCCESS, 'success');

	if ($result)
		os_redirect(os_href_link('cron.php'));
}

$main->head();
$main->top_menu();
?>

<div class="btn-group">
	<a class="btn <?php echo (!isset($_GET['action'])) ? 'active' : '' ?>" href="<?php echo os_href_link('cron.php'); ?>"><?php echo TEXT_CRON_ALL; ?></a>
	<a class="btn <?php echo (isset($_GET['action']) && $_GET['action'] == 'add') ? 'active' : '' ?>" href="<?php echo os_href_link('cron.php', 'action=add'); ?>"><?php echo TEXT_CRON_ADD; ?></a>
</div>

<hr>

<?php if (!isset($_GET['action'])) { ?>

	<?php if (is_array($getTasks)) { ?>
		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th>#</th>
					<th><span class="line"></span><?php echo TEXT_CRON_NAME; ?></th>
					<th><span class="line"></span><?php echo TEXT_CRON_CLASS; ?></th>
					<th><span class="line"></span><?php echo TEXT_CRON_FUNCTION; ?></th>
					<th><span class="line"></span><?php echo TEXT_CRON_TIME; ?></th>
					<th><span class="line"></span><?php echo TEXT_CRON_LAST_RUN; ?></th>
					<th><span class="line"></span><?php echo TEXT_CRON_STATUS; ?></th>
					<th class="tright"><span class="line"></span><?php echo TEXT_CRON_ACTION; ?></th>
				</tr>
			</thead>
			<?php foreach($getTasks AS $t) { ?>
			<tr>
				<td><?php echo $t['id']; ?></td>
				<td><?php echo $t['title']; ?></td>
				<td><?php echo $t['class']; ?></td>
				<td><?php echo $t['function']; ?></td>
				<td><?php echo $t['period']; ?></td>
				<td><?php echo $t['date_last_run']; ?></td>
				<td><?php echo $t['status']; ?></td>
				<td class="tright">
					<div class="btn-group">
						<a class="btn btn-mini" href="<?php echo os_href_link('cron.php', 'action=run&task_id='.$t['id']); ?>"><i class="icon-play-sign"></i></a>
						<a class="btn btn-mini" href="<?php echo os_href_link('cron.php', 'action=edit&task_id='.$t['id']); ?>"><i class="icon-edit"></i></a>
						<a class="btn btn-mini" href="<?php echo os_href_link('cron.php', 'action=delete&task_id='.$t['id']); ?>"><i class="icon-trash"></i></a>
					</div>
				</td>
			</tr>
			<?php } ?>
		</table>
	<?php } else { echo TEXT_CRON_EMPTY; } ?>

<?php } elseif (isset($_GET['action']) && ($_GET['action'] == 'edit' OR $_GET['action'] == 'add')) { ?>

	<?php
	if ($_GET['action'] == 'edit')
		$aTask = $getTasks[$_GET['task_id']];
	else
		$aTask = array();
	?>

	<div class="form-horizontal pt10">
		<form action="" method="post">
			<input type="hidden" name="action" value="<?php echo $_GET['action'];?>">
			<?php if (isset($_GET['task_id'])) { ?>
			<input type="hidden" name="task_id" value="<?php echo $_GET['task_id'];?>">
			<?php } ?>
			<div class="control-group">
				<label class="control-label" for="title"><?php echo TEXT_CRON_NAME; ?></label>
				<div class="controls">
					<input type="text" name="task[title]" value="<?php echo $aTask['title']; ?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="class"><?php echo TEXT_CRON_CLASS; ?></label>
				<div class="controls">
					<input type="text" name="task[class]" value="<?php echo $aTask['class']; ?>">
					<span class="help-block"><?php echo TEXT_CRON_CLASS_DESC; ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="function"><?php echo TEXT_CRON_FUNCTION; ?></label>
				<div class="controls">
					<input type="text" name="task[function]" value="<?php echo $aTask['function']; ?>">
					<span class="help-block"><?php echo TEXT_CRON_FUNCTION_DESC; ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="period"><?php echo TEXT_CRON_TIME; ?></label>
				<div class="controls">
					<input type="text" name="task[period]" value="<?php echo $aTask['period']; ?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="status"><?php echo TEXT_CRON_STATUS; ?></label>
				<div class="controls">
					<select name="task[status]">
						<option value="1" <?php echo ($aTask['status'] == 1) ? 'selected' : ''; ?>><?php echo TEXT_CRON_STATUS_ON; ?></option>
						<option value="0" <?php echo ($aTask['status'] == 0) ? 'selected' : ''; ?>><?php echo TEXT_CRON_STATUS_OFF; ?></option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for=""></label>
				<div class="controls">
					<input class="btn" type="submit" value="<?php echo TEXT_CRON_SAVE; ?>" />
				</div>
			</div>
		</form>
	</div>

<?php } ?>

<?php $main->bottom(); ?>