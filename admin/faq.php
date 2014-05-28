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

$breadcrumb->add(HEADING_TITLE, FILENAME_FAQ);

if ($_GET['action'] == 'edit' OR $_GET['action'] == 'new')
{
	if ($_GET['action'] == 'edit')
	{
		$faq_query = os_db_query("SELECT * FROM ".TABLE_FAQ." WHERE faq_id = '".$_GET['id']."'");
		$faq = os_db_fetch_array($faq_query);

		$breadcrumb->add($faq['question'], os_href_link(FILENAME_FAQ, 'action=edit&id='.$_GET['id']));
	}
	else
	{
		$breadcrumb->add(BUTTON_INSERT, os_href_link(FILENAME_FAQ, 'action=new'));
		$latest_news = array();
	}
}

$main->head();
$main->top_menu();
?>

<?php if ($_GET['action'] == 'new' OR $_GET['action'] == 'edit') { ?>

	<form id="faq" name="faq" action="<?php echo os_href_link(FILENAME_FAQ); ?>" method="post" enctype="multipart/form-data">

		<?php if (isset($_GET['id']) && !empty($_GET['id'])) { ?>
			<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
		<?php } ?>
		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

		<div class="control-group">
			<label class="control-label" for="question"><?php echo TEXT_FAQ_QUESTION; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="question" name="question" required value="<?php echo $faq['question']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="faq_page_url"><?php echo TEXT_FAQ_PAGE_URL; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="faq_page_url" name="faq_page_url" value="<?php echo $faq['faq_page_url']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="answer"><?php echo TEXT_FAQ_ANSWER; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<textarea class="input-block-level textarea_big" id="answer" name="answer" required><?php echo stripslashes(@$faq['answer']); ?></textarea>
			</div>
		</div>
		<?php if (isset($_GET['id'])) { ?>
			<div class="control-group">
				<label class="control-label" for="date_added"><?php echo TEXT_FAQ_DATE; ?> <span class="input-required">*</span></label>
				<div class="controls">
					<input class="input-block-level formDatetime" type="text" required data-date-autoclose="true" data-date-format="yyyy-mm-dd hh:ii:ss" id="date_added" name="date_added" required value="<?php echo $faq['date_added']; ?>">
				</div>
			</div>
		<?php } ?>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label class="control-label" for="item_language"><?php echo TEXT_FAQ_LANGUAGE; ?></label>
					<div class="controls">
						<select class="input-block-level" name="item_language" id="item_language">
							<?php
							$languages = $cartet->language->get();
							foreach($languages AS $lang)
							{
								$selected = ($faq['language'] == $lang['languages_id']) ? 'selected' : '';
								echo '<option value="'.$lang['languages_id'].'" '.$selected.'>'.$lang['name'].'</option>';
							}
							?>
						</select>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label class="control-label" for="status"><?php echo TABLE_HEADING_FAQ_STATUS; ?></label>
					<div class="controls">
						<select class="input-block-level" name="status" id="status">
							<option value="1" <?php echo ($faq['status'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
							<option value="0" <?php echo (isset($faq['status']) && $faq['status'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="faq_save" data-reload-page="1" type="submit" value="<?php echo BUTTON_INSERT; ?>">
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_FAQ); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</form>

<?php } else { ?>

	<div class="second-page-nav">
		<div class="row-fluid">
			<div class="span6">

			</div>
			<div class="span6">
				<div class="pull-right">
					<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_FAQ, 'action=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_FAQ_QUESTION; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_FAQ_STATUS; ?></th>
				<th><span class="line"></span><?php echo TEXT_FAQ_DATE; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_FAQ_ACTION; ?></th>
			</tr>
		</thead>
		<?php
		$faq_query = os_db_query('select * from '.TABLE_FAQ.' order by date_added desc');
		while ($faq = os_db_fetch_array($faq_query))
		{
		?>
		<tr>
			<td><?php echo '&nbsp;'.$faq['question']; ?></td>
			<td class="tcenter">
			<?php
			echo '<a '.(($faq['status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$faq['faq_id'].'_0_status" data-column="status" data-action="faq_status" data-id="'.$faq['faq_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
			echo '<a '.(($faq['status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$faq['faq_id'].'_1_status" data-column="status" data-action="faq_status" data-id="'.$faq['faq_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
			?>
			<td><?php echo '&nbsp;'.$faq['date_added']; ?></td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_FAQ, 'action=edit&id='.$faq['faq_id']); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
					<a class="btn btn-mini" href="#" data-action="faq_delete" data-remove-parent="tr" data-id="<?php echo $faq['faq_id']; ?>" data-confirm="<?php echo TEXT_DELETE_ITEM_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
		<?php } ?>
	</table>

<?php } ?>

<?php $main->bottom(); ?>