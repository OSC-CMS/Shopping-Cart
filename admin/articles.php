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

set_articles_url_cache();
set_topics_url_cache();
set_default_cache();

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (isset($action) && $action == 'update_topic' OR isset($action) && $action == 'insert_topic')
{
	$cartet->articles->saveCategory($_POST);
	os_redirect(os_href_link(FILENAME_ARTICLES, 'tPath='.$_GET['tPath']));
}
elseif (isset($action) && $action == 'update_article' OR isset($action) && $action == 'insert_article')
{
	$cartet->articles->saveArticle($_POST);
	os_redirect(os_href_link(FILENAME_ARTICLES, 'tPath='.$_GET['tPath']));
}

$breadcrumb->add(HEADING_TITLE, FILENAME_ARTICLES);

if (isset($_GET['action']) && ($_GET['action'] == 'new_topic' OR $_GET['action'] == 'edit_topic'))
{
	$breadcrumb->add(($_GET['action'] == 'new_topic_ACD') ? TEXT_INFO_HEADING_NEW_TOPIC : TEXT_INFO_HEADING_EDIT_TOPIC, FILENAME_ARTICLES);
}
elseif (isset($_GET['action']) && $_GET['action'] == 'new_article')
{
	$breadcrumb->add(sprintf(TEXT_NEW_ARTICLE, os_output_generated_topic_path($current_topic_id)), FILENAME_ARTICLES);
}

$main->head();
$main->top_menu();
?>

<?php
if (isset($action) && ($action == 'new_topic' OR $action == 'edit_topic'))
{
	$form_action = ($_GET['tID']) ? 'update_topic' : 'insert_topic';

	if ($form_action == 'update_topic')
	{
		$topics_query = os_db_query("select * from ".TABLE_TOPICS." t, ".TABLE_TOPICS_DESCRIPTION." td where t.topics_id = '".(int)$_GET['tID']."' and t.topics_id = td.topics_id and td.language_id = '".(int)$_SESSION['languages_id']."' order by t.sort_order, td.topics_name");
		$topic = os_db_fetch_array($topics_query);

		$tInfo = new objectInfo($topic);
	}
	else
		$tInfo = array();

	$languages = os_get_languages();

	echo os_draw_form($form_action, FILENAME_ARTICLES, 'tPath='.$tPath.'&tID='.$_GET['tID'].'&action='.$form_action, 'post', 'enctype="multipart/form-data"'); ?>

	<?php echo os_draw_hidden_field('parent_id', (!empty($tInfo->parent_id)) ? $tInfo->parent_id : $_GET['tPath']); ?>
	<?php if ($form_action == 'update_topic') { echo os_draw_hidden_field('topics_id', $tInfo->topics_id); } ?>

	<div class="p10">

		<ul class="nav nav-tabs default-tabs">
			<?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
			<li <?php echo ($i == 0) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $languages[$i]['id']; ?>"><?php echo $languages[$i]['name']; ?></a></li>
			<?php } ?>
			<li><a href="#tab_info"><?php echo TEXT_ARTICLE_OTHER; ?></a></li>
		</ul>

		<div class="tab-content">
			<?php
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
			{
				if ($languages[$i]['status'] == 1)
				{ ?>
					<div class="tab-pane <?php echo ($i == 0) ? 'active' : ''; ?>" id="tab_lang_<?php echo $languages[$i]['id']; ?>">
						<div class="pt10">
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_EDIT_TOPICS_NAME; ?></label>
								<div class="controls">
									<?php
									echo $cartet->html->input_text(
										'topics_name['.$languages[$i]['id'].']',
										(($topics_name[$languages[$i]['id']]) ? stripslashes($topics_name[$languages[$i]['id']]) : os_get_topic_name($tInfo->topics_id, $languages[$i]['id'])),
										array('id' => 'topics_name', 'class' => 'span12')
									);
									?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_EDIT_TOPICS_HEADING_TITLE; ?></label>
								<div class="controls">
									<?php
									echo $cartet->html->input_text(
										'topics_heading_title['.$languages[$i]['id'].']',
										(($topics_name[$languages[$i]['id']]) ? stripslashes($topics_name[$languages[$i]['id']]) : os_get_topic_heading_title($tInfo->topics_id, $languages[$i]['id'])),
										array('id' => 'topics_heading_title', 'class' => 'span12')
									);
									?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_EDIT_TOPICS_DESCRIPTION; ?></label>
								<div class="controls">
									<?php
									echo $cartet->html->textarea(
										'topics_description['.$languages[$i]['id'].']',
										(($categories_description[$languages[$i]['id']]) ? stripslashes($topics_description[$languages[$i]['id']]) : os_get_topic_description($tInfo->topics_id, $languages[$i]['id'])),
										array('id' => 'topics_description['.$languages[$i]['id'].']', 'class' => 'span12 textarea_big')
									);
									?>
								</div>
							</div>
						</div>
					</div>
				<?php
				}
			}
			?>

			<div class="tab-pane" id="tab_info">
				<div class="pt10">
					<div class="control-group">
						<label class="control-label" for="topics_page_url"><?php echo TEXT_TOPIC_PAGE_URL; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('topics_page_url', $tInfo->topics_page_url, array('id' => 'topics_page_url', 'class' => 'span12')); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="sort_order"><?php echo TEXT_EDIT_SORT_ORDER; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('sort_order', $tInfo->sort_order, array('id' => 'sort_order', 'class' => 'span3')); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="date_added"><?php echo TEXT_DATE_ADDED; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('topics_date_added', (($tInfo->date_added) ? $tInfo->date_added : ''), array(
								'id' => 'date_added',
								'class' => 'formDatetime span3',
								'data-date-format' => 'yyyy-mm-dd hh:ii:ss',
								'data-date-today-btn' => 'true',
								'data-date-autoclose' => 'true'
								))
							; ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span2">
						
							<?php if ($tInfo->topics_image) { ?>
							<div class="tcenter"><img class="img-polaroid" src="<?php echo DIR_WS_CATALOG.'images/articles/'.$tInfo->topics_image; ?>"></div>
							<br />
							<br />
							<label class="checkbox"><?php $tInfo->topics_image; echo os_draw_selection_field('del_cat_pic', 'checkbox', 'yes').TEXT_DELETE; ?></label>
							<?php } ?>
						</div>
						<div class="span10">
							<h4><?php echo TEXT_SORT_IMAGE; ?></h4>
							<?php echo os_draw_file_field('topics_image').os_draw_hidden_field('topics_current_image', $tInfo->topics_image); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.'&tID='.$_GET['tID']); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</div>
</form>

<?php
}
elseif ($action == 'new_article')
{
	$form_action = (isset($_GET['aID'])) ? 'update_article' : 'insert_article';

	if ($form_action == 'update_article')
	{
		$article_query = os_db_query("SELECT * FROM ".TABLE_ARTICLES." a, ".TABLE_ARTICLES_DESCRIPTION." ad WHERE a.articles_id = '".(int)$_GET['aID']."' AND a.articles_id = ad.articles_id AND ad.language_id = '".(int)$_SESSION['languages_id']."'");
		$aInfo = os_db_fetch_array($article_query);
	}
	else
		$aInfo = array();

	$languages = os_get_languages();
?>

<?php echo os_draw_form($form_action, FILENAME_ARTICLES, 'tPath='.$tPath.(isset($_GET['aID']) ? '&aID='.$_GET['aID'] : '').'&action='.$form_action, 'post', 'enctype="multipart/form-data"'); ?>

	<?php echo os_draw_hidden_field('current_topic_id', $current_topic_id); ?>
	<?php echo (isset($_GET['aID']) ? os_draw_hidden_field('articles_id', $_GET['aID']) : ''); ?>

	<div class="p10">

		<ul class="nav nav-tabs default-tabs">
			<?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
			<li <?php echo ($i == 0) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $languages[$i]['id']; ?>"><?php echo $languages[$i]['name']; ?></a></li>
			<?php } ?>
			<li><a href="#tab_info"><?php echo TEXT_ARTICLE_OTHER; ?></a></li>
		</ul>

		<div class="tab-content">
			<?php
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
			{
				if ($languages[$i]['status'] == 1)
				{
					if (SEO_URL_ARTICLES_GENERATOR == 'true' && empty($aInfo['articles_page_url']))
						$acricleParams = array('id' => 'articles_name', 'class' => 'span12', 'onKeyPress' => 'onchange_articles_url()', 'onChange' => 'onchange_articles_url()');
					else
						$acricleParams = array('id' => 'articles_name', 'class' => 'span12');
					?>
					<div class="tab-pane <?php echo ($i == 0) ? 'active' : ''; ?>" id="tab_lang_<?php echo $languages[$i]['id']; ?>">
						<div class="pt10">
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_ARTICLES_NAME; ?></label>
								<div class="controls">
									<?php
									echo $cartet->html->input_text(
										'articles_name['.$languages[$i]['id'].']',
										(($articles_name[$languages[$i]['id']]) ? stripslashes($articles_name[$languages[$i]['id']]) : os_get_articles_name($aInfo['articles_id'], $languages[$i]['id'])),
										$acricleParams
									);
									?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_ARTICLES_DESCRIPTION_SHORT; ?></label>
								<div class="controls">
									<?php
									echo $cartet->html->textarea(
										'articles_description_short['.$languages[$i]['id'].']',
										(($articles_description_short[$languages[$i]['id']]) ? stripslashes($articles_description_short[$languages[$i]['id']]) : os_get_articles_description_short($aInfo['articles_id'], $languages[$i]['id'])),
										array('id' => 'articles_description_short['.$languages[$i]['id'].']', 'class' => 'span12 textarea_small')
									);
									?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_ARTICLES_DESCRIPTION; ?></label>
								<div class="controls">
									<?php
									echo $cartet->html->textarea(
										'articles_description['.$languages[$i]['id'].']',
										(($articles_description[$languages[$i]['id']]) ? stripslashes($articles_description[$languages[$i]['id']]) : os_get_articles_description($aInfo['articles_id'], $languages[$i]['id'])),
										array('id' => 'articles_description['.$languages[$i]['id'].']', 'class' => 'span12 textarea_big')
									);
									?>
								</div>
							</div>
							<hr>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_ARTICLES_HEAD_TITLE_TAG; ?></label>
								<div class="controls">
									<?php
									echo $cartet->html->input_text(
										'articles_head_title_tag['.$languages[$i]['id'].']',
										(isset($articles_head_title_tag[$languages[$i]['id']]) ? $articles_head_title_tag[$languages[$i]['id']] : os_get_articles_head_title_tag($aInfo['articles_id'], $languages[$i]['id'])),
										array('id' => 'articles_head_title_tag', 'class' => 'span12')
									);
									?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_ARTICLES_HEAD_DESC_TAG; ?></label>
								<div class="controls">
									<?php
									echo $cartet->html->textarea(
										'articles_head_desc_tag['.$languages[$i]['id'].']',
										(isset($articles_head_desc_tag[$languages[$i]['id']]) ? $articles_head_desc_tag[$languages[$i]['id']] : os_get_articles_head_desc_tag($aInfo['articles_id'], $languages[$i]['id'])),
										array('id' => 'articles_head_desc_tag['.$languages[$i]['id'].']', 'class' => 'span12')
									);
									?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_ARTICLES_HEAD_KEYWORDS_TAG; ?></label>
								<div class="controls">
									<?php
									echo $cartet->html->input_text(
										'articles_head_keywords_tag['.$languages[$i]['id'].']',
										(isset($articles_head_keywords_tag[$languages[$i]['id']]) ? $articles_head_keywords_tag[$languages[$i]['id']] : os_get_articles_head_keywords_tag($aInfo['articles_id'], $languages[$i]['id'])),
										array('id' => 'articles_head_keywords_tag', 'class' => 'span12')
									);
									?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for=""><?php echo TEXT_ARTICLES_URL; ?></label>
								<div class="controls">
									<?php
									echo $cartet->html->input_text(
										'articles_url['.$languages[$i]['id'].']',
										(isset($articles_url[$languages[$i]['id']]) ? $articles_url[$languages[$i]['id']] : os_get_articles_url($aInfo['articles_id'], $languages[$i]['id'])),
										array('id' => 'articles_url', 'class' => 'span12')
									);
									?>
									<span class="help-block"><?php echo TEXT_ARTICLES_URL_WITHOUT_HTTP; ?></span>
								</div>
							</div>
						</div>
					</div>
				<?php
				}
			}
			?>

			<div class="tab-pane" id="tab_info">
				<div class="pt10">
					<div class="control-group">
						<label class="control-label" for="articles_page_url"><?php echo TEXT_ARTICLE_PAGE_URL; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('articles_page_url', $aInfo['articles_page_url'], array('id' => 'articles_page_url', 'class' => 'span12')); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for=""><?php echo TEXT_ARTICLES_STATUS; ?></label>
						<div class="controls">
							<select name="articles_status" class="span3">
								<option value="1" <?php echo ($aInfo['articles_status'] == 1 OR !isset($aInfo['articles_status'])) ? 'selected' : ''; ?>><?php echo ON; ?></option>
								<option value="0" <?php echo (isset($aInfo['articles_status']) && $aInfo['articles_status'] == 0) ? 'selected' : ''; ?>><?php echo OFF; ?></option>
							</select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="sort_order"><?php echo TEXT_ARTICLE_SORT_ORDER; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('sort_order', $aInfo['sort_order'], array('id' => 'sort_order', 'class' => 'span3')); ?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="articles_date_added"><?php echo TEXT_DATE_ADDED; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('articles_date_added', (($aInfo['articles_date_added']) ? $aInfo['articles_date_added'] : ''), array(
								'id' => 'articles_date_added',
								'class' => 'formDatetime span3',
								'data-date-format' => 'yyyy-mm-dd hh:ii:ss',
								'data-date-today-btn' => 'true',
								'data-date-autoclose' => 'true'
								))
							; ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="articles_date_available"><?php echo TEXT_ARTICLES_DATE_AVAILABLE; ?></label>
						<div class="controls">
							<?php echo $cartet->html->input_text('articles_date_available', (($aInfo['articles_date_available']) ? $aInfo['articles_date_available'] : ''), array(
								'id' => 'articles_date_available',
								'class' => 'formDatetime span3',
								'data-date-format' => 'yyyy-mm-dd hh:ii:ss',
								'data-date-today-btn' => 'true',
								'data-date-autoclose' => 'true'
								))
							; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.(isset($_GET['aID']) ? '&aID='.$_GET['aID'] : '')); ?>"><?php echo BUTTON_CANCEL; ?></a>
		</div>
	</div>
</form>

<?php
}
else
{
?>

	<div class="second-page-nav">

		<div class="row-fluid">
			<div class="span6">
				<?php echo os_draw_form('search', FILENAME_ARTICLES, '', 'get'); ?>
					<fieldset>
						<?php echo os_draw_input_field('search', '', 'placeholder="'.HEADING_TITLE_SEARCH.'…"'); ?>
					</fieldset>
				</form>
			</div>
			<div class="span6">
				<div class="pull-right">
					<?php echo os_draw_form('goto', FILENAME_ARTICLES, '', 'get'); ?>
						<fieldset>
							<?php echo os_draw_pull_down_menu('tPath', os_get_topic_tree(), $current_topic_id, 'onChange="this.form.submit();"'); ?>
						</fieldset>
					</form>
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span8"></div>
			<div class="span4">
				<div class="pull-right">
					<div class="btn-group">
						<?php
							echo '<a class="btn btn-info btn-mini" href="'.os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.'&action=new_topic').'">'.BUTTON_NEW_TOPIC.'</a>';
							echo '<a class="btn btn-info btn-mini" href="'.os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.'&action=new_article').'">'.BUTTON_NEW_ARTICLE.'</a>';
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_TOPICS_ARTICLES; ?></th>
				<th><span class="line"></span><?php echo TEXT_DATE_ADDED; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
		<?php
		if (isset($_GET['search']))
		{
			$search = os_db_prepare_input($_GET['search']);
			$topics_query = os_db_query("select * from ".TABLE_TOPICS." t, ".TABLE_TOPICS_DESCRIPTION." td where t.topics_id = td.topics_id and td.language_id = '".(int)$_SESSION['languages_id']."' and td.topics_name like '%".os_db_input($search)."%' order by t.sort_order, td.topics_name");
		}
		else
		{
			$topics_query = os_db_query("select * from ".TABLE_TOPICS." t, ".TABLE_TOPICS_DESCRIPTION." td where t.parent_id = '".(int)$current_topic_id."' and t.topics_id = td.topics_id and td.language_id = '".(int)$_SESSION['languages_id']."' order by t.sort_order, td.topics_name");
		}
		while ($topics = os_db_fetch_array($topics_query))
		{
			// Get parent_id for subtopics if search
			if (isset($_GET['search'])) $tPath = $topics['parent_id'];
			?>
			<tr>
				<td><a href="<?php echo os_href_link(FILENAME_ARTICLES, 'tPath='.$topics['topics_id']); ?>"><i class="icon-folder-close"></i> <?php echo $topics['topics_name']; ?></a></td>
				<td class="tcenter">
					<?php echo $topics['date_added']; ?>
					<?php if ($topics['last_modified']) { ?>
					<i class="icon-edit tt" title="<?php echo TEXT_LAST_MODIFIED; ?>: <?php echo $topics['last_modified']; ?>"></i>
					<?php } ?>
				</td>
				<td class="tcenter"></td>
				<td width="100">
					<div class="btn-group pull-right">
						<?php echo $cartet->html->link(
							'<i class="icon-edit"></i>',
							os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.'&tID='.$topics['topics_id'].'&action=edit_topic'),
							array(
								'class' => 'btn btn-mini',
								'title' => BUTTON_EDIT,
							)
						); ?>
						<?php echo $cartet->html->link(
							'<i class="icon-chevron-right"></i>',
							os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.'&tID='.$topics['topics_id'].'&action=topic_move'),
							array(
								'class' => 'ajax-load-page btn btn-mini',
								'data-load-page' => 'articles&t_id='.$topics['topics_id'].'&action=topic_move',
								'data-toggle' => 'modal',
								'title' => BUTTON_MOVE,
							)
						); ?>
						<?php echo $cartet->html->link(
							'<i class="icon-trash"></i>',
							os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.'&tID='.$topics['topics_id'].'&action=delete_topic'),
							array(
								'class' => 'ajax-load-page btn btn-mini',
								'data-load-page' => 'articles&t_id='.$topics['topics_id'].'&action=category_delete',
								'data-toggle' => 'modal',
								'title' => BUTTON_DELETE,
							)
						); ?>
					</div>
				</td>
			</tr>
			<?php
		}

		if (isset($_GET['search']))
		{
			$articles_query = os_db_query("select * from ".TABLE_ARTICLES." a, ".TABLE_ARTICLES_DESCRIPTION." ad, ".TABLE_ARTICLES_TO_TOPICS." a2t where a.articles_id = ad.articles_id and ad.language_id = '".(int)$_SESSION['languages_id']."' and a.articles_id = a2t.articles_id and ad.articles_name like '%".os_db_input($search)."%' order by ad.articles_name");
		}
		else
		{
			$articles_query = os_db_query("select * from ".TABLE_ARTICLES." a, ".TABLE_ARTICLES_DESCRIPTION." ad, ".TABLE_ARTICLES_TO_TOPICS." a2t where a.articles_id = ad.articles_id and ad.language_id = '".(int)$_SESSION['languages_id']."' and a.articles_id = a2t.articles_id and a2t.topics_id = '".(int)$current_topic_id."' order by ad.articles_name");
		}

		while ($articles = os_db_fetch_array($articles_query))
		{
			// Get topics_id for article if search
			if (isset($_GET['search'])) $tPath = $articles['topics_id'];
			?>
			<tr>
				<td><?php echo $articles['articles_name']; ?></td>
				<td class="tcenter">
					<?php echo $articles['articles_date_added']; ?>
					<?php if ($articles['articles_last_modified']) { ?>
					<i class="icon-edit tt" title="<?php echo TEXT_LAST_MODIFIED; ?>: <?php echo $articles['articles_last_modified']; ?>"></i>
					<?php } ?>
				</td>
				<td class="tcenter">
					<?php
						echo '<a '.(($articles['articles_status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$articles['articles_id'].'_0_articles_status" data-column="articles_status" data-action="articles_status" data-id="'.$articles['articles_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
						echo '<a '.(($articles['articles_status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$articles['articles_id'].'_1_articles_status" data-column="articles_status" data-action="articles_status" data-id="'.$articles['articles_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
					?>
				</td>
				<td width="100">
					<div class="btn-group pull-right">
						<?php echo $cartet->html->link(
							'<i class="icon-edit"></i>',
							os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.'&aID='.$articles['articles_id'].'&action=new_article'),
							array(
								'class' => 'btn btn-mini',
								'title' => BUTTON_EDIT,
							)
						); ?>
						<?php echo $cartet->html->link(
							'<i class="icon-share"></i>',
							os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.'&aID='.$articles['articles_id'].'&action=article_copy'),
							array(
								'class' => 'ajax-load-page btn btn-mini',
								'data-load-page' => 'articles&t_id='.$tPath.'&a_id='.$articles['articles_id'].'&action=article_copy',
								'data-toggle' => 'modal',
								'title' => BUTTON_COPY_TO,
							)
						); ?>
						<?php echo $cartet->html->link(
							'<i class="icon-chevron-right"></i>',
							os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.'&aID='.$articles['articles_id'].'&action=article_move'),
							array(
								'class' => 'ajax-load-page btn btn-mini',
								'data-load-page' => 'articles&t_id='.$tPath.'&a_id='.$articles['articles_id'].'&action=article_move',
								'data-toggle' => 'modal',
								'title' => BUTTON_MOVE,
							)
						); ?>
						<?php echo $cartet->html->link(
							'<i class="icon-trash"></i>',
							os_href_link(FILENAME_ARTICLES, 'tPath='.$tPath.'&aID='.$articles['articles_id'].'&action=delete'),
							array(
								'class' => 'ajax-load-page btn btn-mini',
								'data-load-page' => 'articles&a_id='.$articles['articles_id'].'&action=delete',
								'data-toggle' => 'modal',
								'title' => BUTTON_DELETE,
							)
						); ?>
					</div>
				</td>
			</tr>
			<?php
		}
		?>
	</table>

<?php } ?>

<?php $main->bottom(); ?>