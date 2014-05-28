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

$breadcrumb->add(HEADING_TITLE, FILENAME_LATEST_NEWS);

if ($_GET['action'] == 'edit' OR $_GET['action'] == 'new')
{
	set_news_url_cache();

	if ($_GET['action'] == 'edit')
	{
		$latest_news_query = os_db_query("select * from ".TABLE_LATEST_NEWS." where news_id = '".$_GET['id']."'");
		$latest_news = os_db_fetch_array($latest_news_query);

		$breadcrumb->add($latest_news['headline'], os_href_link(FILENAME_LATEST_NEWS, 'action=edit&id='.$_GET['id']));
	}
	else
	{
		$breadcrumb->add(BUTTON_INSERT, os_href_link(FILENAME_LATEST_NEWS, 'action=new'));
		$latest_news = array();
	}
}


$main->head();
$main->top_menu();
?>


<?php
if ($_GET['action'] == 'edit' OR $_GET['action'] == 'new')
{
	set_news_url_cache();

	if ($_GET['action'] == 'edit')
	{
		$latest_news_query = os_db_query("select * from ".TABLE_LATEST_NEWS." where news_id = '".$_GET['id']."'");
		$latest_news = os_db_fetch_array($latest_news_query);
	}
	else
		$latest_news = array();

	if (SEO_URL_NEWS_GENERATOR == 'true' && empty($latest_news['news_page_url'])) $seo_input_field = ' onKeyPress="onchange_news_url()"  onChange="onchange_news_url()"'; else $seo_input_field = '';
	?>

	<form id="news" name="news" action="<?php echo os_href_link(FILENAME_LATEST_NEWS); ?>" method="post" enctype="multipart/form-data">

		<?php if (isset($_GET['id']) && !empty($_GET['id'])) { ?>
		<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
		<?php } ?>
		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

		<div class="control-group">
			<label class="control-label" for="headline"><?php echo TEXT_LATEST_NEWS_HEADLINE; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="headline" name="headline" required value="<?php echo $latest_news['headline']; ?>" <?php echo $seo_input_field; ?>>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="news_page_url"><?php echo TEXT_NEWS_PAGE_URL; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="news_page_url" name="news_page_url" value="<?php echo $latest_news['news_page_url']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="content"><?php echo TEXT_LATEST_NEWS_CONTENT; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<textarea class="input-block-level textarea_big" id="content" name="content" required><?php echo stripslashes(@$latest_news['content']); ?></textarea>
			</div>
		</div>
		<?php if (isset($_GET['id'])) { ?>
		<div class="control-group">
			<label class="control-label" for="date_added"><?php echo TEXT_LATEST_NEWS_DATE; ?> <span class="input-required">*</span></label>
			<div class="controls">
				<input class="input-block-level formDatetime" type="text" required data-date-autoclose="true" data-date-format="yyyy-mm-dd hh:ii:ss" id="date_added" name="date_added" required value="<?php echo $latest_news['date_added']; ?>">
			</div>
		</div>
		<?php } ?>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label class="control-label" for="item_language"><?php echo TEXT_LATEST_NEWS_LANGUAGE; ?></label>
					<div class="controls">
						<select class="input-block-level" name="item_language" id="item_language">
							<?php
							$languages = $cartet->language->get();
							foreach($languages AS $lang)
							{
								$selected = ($latest_news['language'] == $lang['languages_id']) ? 'selected' : '';
								echo '<option value="'.$lang['languages_id'].'" '.$selected.'>'.$lang['name'].'</option>';
							}
							?>
						</select>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label class="control-label" for="status"><?php echo TABLE_HEADING_LATEST_NEWS_STATUS; ?></label>
					<div class="controls">
						<select class="input-block-level" name="status" id="status">
							<option value="1" <?php echo ($latest_news['status'] == 1) ? 'selected' : ''; ?>><?php echo YES; ?></option>
							<option value="0" <?php echo (isset($latest_news['status']) && $latest_news['status'] == 0) ? 'selected' : ''; ?>><?php echo NO; ?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label class="control-label" for="news_image">Картинка</label>
					<div class="controls">
						<input type="file" id="news_image" name="news_image">
						<?php if ($latest_news['news_image']) { ?>
							<input type="hidden" name="news_image_current" value="<?php echo $latest_news['news_image']; ?>">
							<br />
							<img src="<?php echo http_path('images').'news/'.$latest_news['news_image']; ?>" alt="">
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label class="control-label" for="images">Выбрать из существующих</label>
					<div class="controls">
						<select class="input-block-level" name="images" id="images">
							<option value=""></option>
							<?php
							$images = os_getFiles(get_path('images').'news/');
							if (is_array($images))
							{
								foreach($images AS $img)
								{
									echo '<option value="'.$img['id'].'">'.$img['text'].'</option>';
								}
							}
							?>
						</select>
					</div>
				</div>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="news_save" data-reload-page="1" type="submit" value="<?php echo BUTTON_INSERT; ?>">
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_LATEST_NEWS); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</form>
<?php } else { ?>
	<div class="second-page-nav">
		<div class="row-fluid">
			<div class="span6">

			</div>
			<div class="span6">
				<div class="pull-right">
					<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_LATEST_NEWS, 'action=new'); ?>"><?php echo BUTTON_INSERT; ?></a>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th colspan="2"><?php echo TABLE_HEADING_LATEST_NEWS_HEADLINE; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_LATEST_NEWS_STATUS; ?></th>
				<th><span class="line"></span><?php echo TEXT_LATEST_NEWS_DATE; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_LATEST_NEWS_ACTION; ?></th>
			</tr>
		</thead>
		<?php
		$latest_news_query = os_db_query('select * from '.TABLE_LATEST_NEWS.' order by date_added ASC');
		while ($latest_news = os_db_fetch_array($latest_news_query))
		{
			if (!empty($latest_news['news_image']))
				$img = ' <i class="icon-camera"></i>';
			else
				$img = '';
		?>
		<tr>
			<td class="tcenter" width="20"><?php echo $img; ?></td>
			<td><?php echo $latest_news['headline']; ?></td>
			<td class="tcenter">
			<?php
				echo '<a '.(($latest_news['status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$latest_news['news_id'].'_0_status" data-column="status" data-action="news_status" data-id="'.$latest_news['news_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
				echo '<a '.(($latest_news['status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$latest_news['news_id'].'_1_status" data-column="status" data-action="news_status" data-id="'.$latest_news['news_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
			?>
			</td>
			<td><?php echo $latest_news['date_added']; ?></td>
			<td width="100">
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_LATEST_NEWS, 'action=edit&id='.$latest_news['news_id']); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
					<a class="btn btn-mini" href="#" data-action="news_delete" data-remove-parent="tr" data-id="<?php echo $latest_news['news_id']; ?>" data-confirm="<?php echo TEXT_DELETE_ITEM_INTRO; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
				</div>
			</td>
		</tr>
		<?php } ?>
	</table>
<?php } ?>

<?php $main->bottom(); ?>