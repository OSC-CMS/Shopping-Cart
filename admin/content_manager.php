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

$languages = os_get_languages();

$breadcrumb->add(HEADING_TITLE, FILENAME_CONTENT_MANAGER);

if (isset($_GET['act']) && $_GET['act'] == 'products')
{
	$breadcrumb->add(HEADING_PRODUCTS_CONTENT, FILENAME_CONTENT_MANAGER.'?act=products');

	if ($_GET['action'] == 'new_products')
	{
		$breadcrumb->add(TEXT_NEW_FILE_TO_PRODUCT, FILENAME_CONTENT_MANAGER.'?action=new_products');
	}

	if ($_GET['action'] == 'edit_products')
	{
		$content_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_CONTENT." WHERE content_id='".(int)$_GET['coID']."'");
		$content = os_db_fetch_array($content_query);

		$pinfo = $cartet->products->getProduct(array('product_id' => $content['products_id']));

		$breadcrumb->add($content['content_name'].' ('.$pinfo['products_name'].')', FILENAME_CONTENT_MANAGER.'?action=edit_products&coID='.$_GET['coID']);
	}
}

if (isset($_GET['action']) && $_GET['action'] == 'new')
{
	$breadcrumb->add(BUTTON_NEW_CONTENT, FILENAME_CONTENT_MANAGER.'?action=new');
}

if (isset($_GET['action']) && $_GET['action'] == 'edit')
{
	$content_query = os_db_query("SELECT * FROM ".TABLE_CONTENT_MANAGER." WHERE content_id='".(int)$_GET['coID']."'");
	$content = os_db_fetch_array($content_query);

	$breadcrumb->add($content['content_title'], FILENAME_CONTENT_MANAGER.'?action=edit&coID='.$_GET['coID']);
}

$main->head();
$main->top_menu();
?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span6">
			<div class="btn-group">
				<a class="btn btn-mini <?php echo (!isset($_GET['act'])) ? 'btn-info' : ''; ?>" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER); ?>">Страницы</a>
				<a class="btn btn-mini <?php echo (isset($_GET['act']) && $_GET['act'] == 'products') ? 'btn-info' : ''; ?>" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'act=products'); ?>">Файлы</a>
			</div>
		</div>
		<div class="span6">
			<div class="btn-group pull-right">
				<?php if (isset($_GET['act']) && $_GET['act'] == 'products') { ?>
				<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'act=products&action=new_products'); ?>"><?php echo TEXT_NEW_FILE_TO_PRODUCT; ?></a>
				<? } else { ?>
				<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER,'action=new'); ?>"><?php echo BUTTON_NEW_CONTENT; ?></a>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php if (isset($_GET['act']) && $_GET['act'] == 'products') { ?>

	<?php if ($_GET['action'] == 'edit_products' OR $_GET['action'] == 'new_products') { ?>

		<?php
			$products_query = os_db_query("SELECT products_id, products_name FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE language_id='".(int)$_SESSION['languages_id']."'");
			$products_array = array();

			while ($products_data=os_db_fetch_array($products_query))
			{
				$products_array[] = array(
					'id' => $products_data['products_id'],
					'text' => $products_data['products_name']
				);
			}

			$languages_array = array();

			for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
			{
				if ($languages[$i]['status'] == 1)
				{
					if ($languages[$i]['id'] == $content['languages_id'])
					{
						$languages_selected = $languages[$i]['code'];
						$languages_id = $languages[$i]['id'];
					}
					$languages_array[] = array(
						'id' => $languages[$i]['code'],
						'text' => $languages[$i]['name']
					);
				}
			}
			$content_files_query = os_db_query("SELECT DISTINCT content_name, content_file FROM ".TABLE_PRODUCTS_CONTENT." WHERE content_file!=''");
			$content_files = array();

			while ($content_files_data=os_db_fetch_array($content_files_query))
			{
				$content_files[] = array(
					'id' => $content_files_data['content_file'],
					'text' => $content_files_data['content_name']
				);
			}

			$default_array[] = array('id' => 'default','text' => TEXT_SELECT);
			$default_value = 'default';
			$content_files = array_merge($default_array, $content_files);
		?>
		<form id="edit_content" name="edit_content" action="<?php echo os_href_link(FILENAME_CONTENT_MANAGER) ;?>" method="post" enctype="multipart/form-data">

			<?php if (isset($_GET['coID']) && !empty($_GET['coID'])) { ?>
				<input type="hidden" name="coID" value="<?php echo $_GET['coID']; ?>">
			<?php } ?>
			<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

			<div class="control-group">
				<label class="control-label" for="categories_select"><?php echo TEXT_PRODUCT; ?></label>
				<div class="controls">
					<?php if (isset($_GET['coID']) && !empty($_GET['coID'])) { ?>
						<span class="label label-success">
					<?php
					echo $pinfo['products_name'];
					?>
					</span>
					<?php } else { ?>
						<?php $allCategories = $cartet->products->getCategories(array(array('id' => '', 'text' => CATEGORIES_LIST))); ?>
						<?php echo $cartet->html->select('categories_select', $allCategories, '', array('id' => 'categories_select', 'class' => 'ajax-change-select', 'data-ajax-action' => 'load_products', 'data-sub-select' => 'products_id', 'data-sub-select-value' => 'products_id', 'data-sub-select-title' => 'products_name')); ?>
						<?php echo $cartet->html->select('products_id', array(), '', array('id' => 'products_id', 'disabled' => 'disabled')); ?>
					<?php } ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="news_page_url"><?php echo TEXT_LANGUAGE; ?></label>
				<div class="controls">
					<?php echo os_draw_pull_down_menu('language',$languages_array,$languages_selected); ?>
				</div>
			</div>
			<?php
			if (GROUP_CHECK == 'true')
			{
				$aStatus = $cartet->customers->getStatus();
				?>
				<div class="control-group">
					<label class="control-label" for="groups"><?php echo ENTRY_CUSTOMERS_STATUS; ?></label>
					<div class="controls">
						<label class="checkbox"><input type="checkbox" name="groups[]" value="all" <?php echo ($content['group_ids'] == 'all') ? 'checked' : ''; ?>> <?php echo TXT_ALL; ?></label>
						<?php
						foreach($aStatus AS $s)
						{
							$checked = ($content['group_ids'] == $s['value']) ? 'checked' : '';
							?>
							<label class="checkbox"><input type="checkbox" name="groups[]" value="<?php echo $s['value']; ?>" <?php echo $checked; ?>> <?php echo $s['text']; ?></label>
						<?php
						}
						?>
					</div>
				</div>
			<?php } ?>
			<div class="control-group">
				<label class="control-label" for="content_name"><?php echo TEXT_TITLE_FILE; ?></label>
				<div class="controls">
					<input class="input-block-level" type="text" id="content_name" name="content_name" value="<?php echo $content['content_name']; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="content_link"><?php echo TEXT_LINK; ?></label>
				<div class="controls">
					<input class="input-block-level" type="text" id="content_link" name="content_link" value="<?php echo $content['content_link']; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="file_comment"><?php echo TEXT_FILE_DESC; ?></label>
				<div class="controls">
					<textarea class="input-block-level textarea_big" id="file_comment" name="file_comment"><?php echo $content['file_comment']; ?></textarea>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<label class="control-label" for="file_comment"><?php echo TEXT_UPLOAD_FILE; ?> <?php echo TEXT_UPLOAD_FILE_LOCAL; ?></label>
						<div class="controls">
							<?php echo os_draw_file_field('file_upload'); ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="control-group">
						<label class="control-label" for="file_comment"><?php echo TEXT_CHOOSE_FILE; ?></label>
						<div class="controls">
							<?php
							require_once(dir_path('func_admin').'file_system.php');
							$files = os_get_filelist(DIR_FS_CATALOG.'media/products/','', array('index.html'));
							unset ($default_array);
							if ($content['content_file']=='')
							{
								$default_array[] = array('id' => 'default','text' => TEXT_SELECT);
								$default_value = 'default';
							}
							else
							{
								$default_array[] = array('id' => 'default','text' => TEXT_NO_FILE);
								$default_value = $content['content_file'];
							}
							$files = os_array_merge($default_array, $files);

							echo os_draw_pull_down_menu('select_file',$files,$default_value);
							?>
							<br />
							<?php
							if ($content['content_file'] != '')
							{
								echo TEXT_CURRENT_FILE.' <b>'.$content['content_file'].'</b>';
								echo os_draw_hidden_field('file_name', $content['content_file']);
							}
							?>
							<span class="help-block"><?php echo TEXT_CHOOSE_FILE_SERVER_PRODUCTS; ?></span>
						</div>
					</div>
				</div>
			</div>

			<hr>

			<div class="tcenter footer-btn">
				<input class="btn btn-success ajax-save-form" data-form-action="content_saveProduct" data-reload-page="1" type="submit" value="<?php echo BUTTON_SAVE; ?>">
				<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER); ?>"><?php echo BUTTON_BACK; ?></a>
			</div>

		</form>

	<?php } else {?>

		<?php
		$products_id_query = os_db_query("SELECT DISTINCT pc.products_id, pd.products_name FROM ".TABLE_PRODUCTS_CONTENT." pc, ".TABLE_PRODUCTS_DESCRIPTION." pd WHERE pd.products_id=pc.products_id and pd.language_id='".(int)$_SESSION['languages_id']."'");

		$products_ids = array();
		while ($products_id_data = os_db_fetch_array($products_id_query))
		{
			$products_ids[]=array(
				'id' => $products_id_data['products_id'],
				'name' => $products_id_data['products_name']
			);
		}
		?>
		<table class="table table-condensed table-big-list">
			<thead>
				<tr>
					<th><?php echo TABLE_HEADING_PRODUCTS_ID; ?></th>
					<th><span class="line"></span><?php echo TABLE_HEADING_PRODUCTS; ?></th>
				</tr>
			</thead>
		<?php
		for ($i=0, $n = sizeof($products_ids); $i<$n; $i++)
		{
			?>
			<tr>
				<td><?php echo $products_ids[$i]['id']; ?></td>
				<td><a href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER, 'act=products&pID='.$products_ids[$i]['id']);?>"><?php echo $products_ids[$i]['name']; ?></a></td>
			</tr>
			<?php
			if ($_GET['pID'])
			{
				$content_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_CONTENT." WHERE products_id = '".$_GET['pID']."' order by content_name");
				$content_array = '';
				while ($content_data=os_db_fetch_array($content_query))
				{
					$content_array[] = array(
						'id'=> $content_data['content_id'],
						'name'=> $content_data['content_name'],
						'file'=> $content_data['content_file'],
						'link'=> $content_data['content_link'],
						'comment'=> $content_data['file_comment'],
						'languages_id'=> $content_data['languages_id'],
						'read'=> $content_data['content_read']
					);
				}

				if ($_GET['pID'] == $products_ids[$i]['id'])
				{ ?>
					<tr>
					<td colspan="2">
					<table border="0" width="100%" cellspacing="2" cellpadding="2">
						<tr>
							<td><?php echo TABLE_HEADING_PRODUCTS_CONTENT_ID; ?></td>
							<td><?php echo TABLE_HEADING_LANGUAGE; ?></td>
							<td><?php echo TABLE_HEADING_CONTENT_NAME; ?></td>
							<td><?php echo TABLE_HEADING_CONTENT_FILE; ?></td>
							<td><?php echo TABLE_HEADING_CONTENT_FILESIZE; ?></td>
							<td><?php echo TABLE_HEADING_CONTENT_LINK; ?></td>
							<td><?php echo TABLE_HEADING_CONTENT_HITS; ?></td>
							<td><?php echo TABLE_HEADING_CONTENT_ACTION; ?></td>
						</tr>
					<?php
					for ($ii=0,$nn=sizeof($content_array); $ii<$nn; $ii++)
					{
						?>
						<tr>
							<td><?php echo  $content_array[$ii]['id']; ?></td>
							<?php
								//if ($content_array[$ii]['file'] != '')
									//echo os_image(http_path('catalog').'admin/images/icons/icon_'.str_replace('.','',strstr($content_array[$ii]['file'],'.')).'.gif');
								//else
									//echo os_image(http_path('catalog').'admin/images/icons/icon_link.gif');

								for ($xx = 0, $zz = sizeof($languages); $xx<$zz; $xx++)
								{
									if ($languages[$xx]['id'] == $content_array[$ii]['languages_id'])
									{
										$lang_dir = $languages[$xx]['directory'];
										break;
									}
								}
							?>
							<td><?php echo os_image(http_path_admin('icons').'lang/'.$lang_dir.'.gif'); ?></td>
							<td><?php echo $content_array[$ii]['name']; ?></td>
							<td><?php echo $content_array[$ii]['file']; ?></td>
							<td><?php echo os_filesize($content_array[$ii]['file']); ?></td>
							<td><?php
								if ($content_array[$ii]['link']!='')
								{
									echo '<a href="'.$content_array[$ii]['link'].'" target="new">'.$content_array[$ii]['link'].'</a>';
								}
								?></td>
							<td><?php echo $content_array[$ii]['read']; ?></td>
							<td width="100">
								<div class="btn-group pull-right">
									<?php if (preg_match('/.gif/i',$content_array[$ii]['file']) or preg_match('/.jpg/i',$content_array[$ii]['file']) or preg_match('/.png/i',$content_array[$ii]['file'])
										or preg_match('/.html/i',$content_array[$ii]['file']) or preg_match('/.htm/i',$content_array[$ii]['file']) or
										preg_match('/.txti/',$content_array[$ii]['file']) or preg_match('/.bmp/i',$content_array[$ii]['file'])
									) { ?>
										<a class="btn btn-mini" onClick="javascript:window.open('<?php echo os_href_link(FILENAME_CONTENT_PREVIEW,'pID=media&coID='.$content_array[$ii]['id']); ?>', 'popup', 'toolbar=0, width=640, height=600')" title="<?php echo TEXT_PREVIEW; ?>"><i class="icon-eye-open"></i></a>
									<?php } ?>
									<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER, 'act=products&action=edit_products&coID='.$content_array[$ii]['id']); ?>" title="<?php echo TEXT_EDIT; ?>"><i class="icon-edit"></i></a>
									<a class="btn btn-mini" href="#" data-action="content_deleteProduct" data-remove-parent="tr" data-id="<?php echo $content_array[$ii]['id']; ?>" data-confirm="<?php echo CONFIRM_DELETE_PRODUCT; ?>" title="<?php echo TEXT_DELETE; ?>"><i class="icon-trash"></i></a>
								</div>
							</td>
						</tr>
					<?php
					} // for content_array
					echo '</table>';
				}
			} // for
		}
		?>
		</table>

		<hr>

		<div class="alert alert-info"><?php echo TEXT_CONTENT_DESCRIPTION; ?></div>

		<div class="alert alert-info">
			<?php
			$total = os_spaceUsed(DIR_FS_CATALOG.'media/products/');
			echo USED_SPACE.os_format_filesize($total);
			?>
		</div>

	<?php } ?>

<?php } else { ?>

	<?php if ($_GET['action'] == 'new' OR $_GET['action'] == 'edit') { ?>

		<?php
		if ($_GET['action'] == 'new')
		{
			$content = array();
		}
			
		?>
		<form id="edit_content" name="edit_content" action="<?php echo os_href_link(FILENAME_CONTENT_MANAGER) ;?>" method="post" enctype="multipart/form-data">

		<?php if (isset($_GET['coID']) && !empty($_GET['coID'])) { ?>
			<input type="hidden" name="coID" value="<?php echo $_GET['coID']; ?>">
		<?php } ?>
		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">

		<?php if ($content['content_delete'] != 0 or $_GET['action'] == 'new') { ?>
			<div class="control-group">
				<label class="control-label" for="content_group"><?php echo TEXT_GROUP; ?> <span class="input-required">*</span></label>
				<div class="controls">
					<input class="input-block-level" type="text" id="content_group" name="content_group" required value="<?php echo $content['content_group']; ?>">
					<span class="help-block"><?php echo TEXT_GROUP_DESC; ?></span>
				</div>
			</div>
		<?php } else { echo os_draw_hidden_field('content_group', $content['content_group']); ?>
			<div class="control-group">
				<label class="control-label" for="content_group"><?php echo TEXT_GROUP; ?></label>
				<div class="controls">
					<?php echo $content['content_group']; ?>
				</div>
			</div>
		<?php } ?>
		<div class="control-group">
			<label class="control-label" for="content_title"><?php echo TEXT_TITLE; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="content_title" name="content_title" value="<?php echo $content['content_title']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="content_page_url"><?php echo TEXT_PAGE_URL; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="content_page_url" name="content_page_url" value="<?php echo $content['content_page_url']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="content_heading"><?php echo TEXT_HEADING; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="content_heading" name="content_heading" value="<?php echo $content['content_heading']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="content_meta_title"><?php echo TEXT_META_TITLE; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="content_meta_title" name="content_meta_title" value="<?php echo $content['content_meta_title']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="content_meta_description"><?php echo TEXT_META_DESCRIPTION; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="content_meta_description" name="content_meta_description" value="<?php echo $content['content_meta_description']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="content_meta_keywords"><?php echo TEXT_META_KEYWORDS; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="content_meta_keywords" name="content_meta_keywords" value="<?php echo $content['content_meta_keywords']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="languages_id"><?php echo TEXT_LANGUAGE; ?></label>
			<div class="controls">
				<select class="input-block-level" name="languages_id" id="languages_id">
					<?php
					$languages = $cartet->language->get();
					foreach($languages AS $lang)
					{
						$selected = ($content['languages_id'] == $lang['languages_id']) ? 'selected' : '';
						echo '<option value="'.$lang['languages_id'].'" '.$selected.'>'.$lang['name'].'</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="file_flag"><?php echo TEXT_FILE_FLAG; ?></label>
			<div class="controls">
				<select class="input-block-level" name="file_flag" id="file_flag">
					<?php
					foreach($cartet->content->getFlags() AS $flag)
					{
						$selected = ($flag['value'] == $content['file_flag']) ? 'selected' : '';
						echo '<option value="'.$flag['value'].'" '.$selected.'>'.$flag['text'].'</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="parent_id"><?php echo TEXT_PARENT; ?></label>
			<div class="controls">
				<?php
				$categories_query = os_db_query("SELECT content_id, content_title FROM ".TABLE_CONTENT_MANAGER." WHERE languages_id = '".$content['languages_id']."' AND content_id != '".(int)$_GET['coID']."'");
				while ($categories_data = os_db_fetch_array($categories_query))
				{
					$categories_array[] = array(
						'value' => $categories_data['content_id'],
						'text' => $categories_data['content_title']
					);
				}
				?>
				<select class="input-block-level" name="parent_id" id="parent_id">
					<?php
					foreach($categories_array AS $category)
					{
						$selected = ($category['value'] == $content['parent_id']) ? 'selected' : '';
						echo '<option value="'.$category['value'].'" '.$selected.'>'.$category['text'].'</option>';
					}
					?>
				</select>
				<span class="help-block"><label class="checkbox"><input type="checkbox" name="parent_check" value="yes"> <?php echo TEXT_PARENT_DESCRIPTION; ?></label></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="sort_order"><?php echo TEXT_SORT_ORDER; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="sort_order" name="sort_order" value="<?php echo $content['sort_order']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="status"><?php echo TEXT_STATUS; ?></label>
			<div class="controls">
				<?php $checkedStatus = ($content['content_status'] == 1) ? 'checked' : ''; ?>
				<label class="checkbox"><input type="checkbox" id="status" name="status" value="yes" <?php echo $checkedStatus; ?>> <?php echo TEXT_STATUS_DESCRIPTION; ?></label>
			</div>
		</div>
		<?php
		if (GROUP_CHECK == 'true')
		{
			$aStatus = $cartet->customers->getStatus();
			?>
			<div class="control-group">
				<label class="control-label" for="groups"><?php echo ENTRY_CUSTOMERS_STATUS; ?></label>
				<div class="controls">
					<label class="checkbox"><input type="checkbox" name="groups[]" value="all" <?php echo ($content['group_ids'] == 'all') ? 'checked' : ''; ?>> <?php echo TXT_ALL; ?></label>
					<?php
					foreach($aStatus AS $s)
					{
						$checked = ($content['group_ids'] == $s['value']) ? 'checked' : '';
						?>
						<label class="checkbox"><input type="checkbox" name="groups[]" value="<?php echo $s['value']; ?>" <?php echo $checked; ?>> <?php echo $s['text']; ?></label>
					<?php
					}
					?>
				</div>
			</div>
		<?php } ?>
		<div class="control-group">
			<label class="control-label" for="file_upload"><?php echo TEXT_UPLOAD_FILE; ?> <?php echo TEXT_UPLOAD_FILE_LOCAL; ?></label>
			<div class="controls">
				<input type="file" id="file_upload" name="file_upload">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="select_file"><?php echo TEXT_CHOOSE_FILE; ?></label>
			<div class="controls">
				<?php
				require_once(dir_path('func_admin').'file_system.php');
				$files = os_get_filelist(DIR_FS_CATALOG.'media/content/', '', array('index.html'));
				if ($content['content_file']=='')
				{
					$default_array[] = array('id' => 'default','text' => TEXT_SELECT);
					$default_value = 'default';
					$files = (count($files) == 0) ? $default_array : os_array_merge($default_array, $files);
				}
				else
				{
					$default_array[] = array('id' => 'default','text' => TEXT_NO_FILE);
					$default_value = $content['content_file'];
					$files = (count($files) == 0) ? $default_array : array_merge($default_array,$files);
				}
				?>
				<select class="input-block-level" id="select_file" name="select_file">
					<?php
					foreach ($files as $f)
					{
						$selectedFile = ($default_value == $f['id']) ? 'selected' : '';
						echo '<option value="'.$f['id'].'" '.$selectedFile.'>'.$f['text'].'</option>';
					}
					?>
				</select>
				<span class="help-block"><?php echo TEXT_CHOOSE_FILE_SERVER; ?></span>
				<span class="help-block"><?php echo TEXT_FILE_DESCRIPTION; ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="content_text"><?php echo TEXT_CONTENT; ?></label>
			<div class="controls">
				<textarea class="input-block-level textarea_big" id="content_text" name="content_text"><?php echo $content['content_text']; ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="content_url"><?php echo TEXT_URL; ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="content_url" name="content_url" value="<?php echo $content['content_url']; ?>">
			</div>
		</div>

		<hr>

		<div class="tcenter footer-btn">
			<input class="btn btn-success ajax-save-form" data-form-action="content_save" data-reload-page="1" type="submit" value="<?php echo BUTTON_SAVE; ?>">
			<a class="btn btn-link" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER); ?>"><?php echo BUTTON_BACK; ?></a>
		</div>

		</form>

	<?php } else { ?>

		<?php $languages = $cartet->language->get(); ?>

		<?php
		$contentQuery = os_db_query("SELECT * FROM ".TABLE_CONTENT_MANAGER." c LEFT JOIN ".TABLE_CM_FILE_FLAGS." f ON (c.file_flag = f.file_flag) ORDER BY c.sort_order");
		$aContent = array();
		while ($c = os_db_fetch_array($contentQuery))
		{
			$aContent[$c['languages_id']][] = $c;
		}
		?>

		<ul class="nav nav-tabs default-tabs">
			<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
				<li <?php echo ($i == 1) ? 'class="active"' : ''; ?>><a href="#tab_lang_<?php echo $lang['languages_id']; ?>"><?php echo $lang['name']; ?></a></li>
			<?php } ?>
		</ul>
		<div class="tab-content">
			<?php $i = 0; foreach ($languages as $lang) { $i++; ?>
				<div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_lang_<?php echo $lang['languages_id']; ?>">
					<table class="table table-condensed table-big-list">
						<thead>
						<tr>
							<th><?php echo TABLE_HEADING_CONTENT_ID; ?></th>
							<th><span class="line"></span><?php echo TABLE_HEADING_CONTENT_TITLE; ?></th>
							<th><span class="line"></span><?php echo TABLE_HEADING_CONTENT_GROUP; ?></th>
							<th><span class="line"></span><?php echo TABLE_HEADING_CONTENT_SORT; ?></th>
							<th><span class="line"></span><?php echo TABLE_HEADING_CONTENT_FILE; ?></th>
							<th><span class="line"></span><?php echo TABLE_HEADING_CONTENT_STATUS; ?></th>
							<th><span class="line"></span><?php echo TABLE_HEADING_CONTENT_BOX; ?></th>
							<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_CONTENT_ACTION; ?></th>
						</tr>
						</thead>
						<?php
						if (is_array($aContent[$lang['languages_id']]))
						{
							foreach($aContent[$lang['languages_id']] AS $c)
							{
								?>
								<tr>
									<td><?php echo $c['content_id']; ?></td>
									<td><?php echo $c['content_title']; ?>
										<?php
										if ($c['content_delete'] == '0'){
											echo ' <span class="input-required">*</span>';
										} ?>
									</td>
									<td><?php echo $c['content_group']; ?></td>
									<td><?php echo $c['sort_order']; ?>&nbsp;</td>
									<td>
										<a href="#" class="ae_select" data-type="select" data-value="<?php echo $c['content_file']; ?>" data-pk="<?php echo $c['content_id']; ?>" data-url="content_changeFile_get" data-action="content_getFiles" data-original-title="<?php echo TABLE_HEADING_CONTENT_FILE; ?>"><?php echo $c['content_file']; ?></a>
									</td>
									<td>
										<?php
										echo '<a '.(($c['content_status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$c['content_id'].'_0_content_status" data-column="content_status" data-action="content_status" data-id="'.$c['content_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
										echo '<a '.(($c['content_status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$c['content_id'].'_1_content_status" data-column="content_status" data-action="content_status" data-id="'.$c['content_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
										?>
									</td>
									<td><a href="#" class="ae_select" data-type="select" data-value="<?php echo $c['file_flag']; ?>" data-pk="<?php echo $c['content_id']; ?>" data-url="content_changeFlag_get" data-action="content_getFlags" data-original-title="<?php echo TABLE_HEADING_CONTENT_BOX; ?>"><?php echo $c['file_flag_name']; ?></a></td>
									<td width="100">
										<div class="btn-group pull-right">
											<a class="btn btn-mini" href="#" onClick="javascript:window.open('<?php echo os_href_link(FILENAME_CONTENT_PREVIEW,'coID='.$c['content_id']); ?>', 'popup', 'toolbar=0, width=640, height=600')" title="<?php echo TEXT_PREVIEW; ?>"><i class="icon-eye-open"></i></a>
											<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_CONTENT_MANAGER, 'action=edit&coID='.$c['content_id']); ?>" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
											<?php if ($c['content_delete']=='1') { ?>
												<a class="btn btn-mini" href="#" data-action="content_delete" data-remove-parent="tr" data-id="<?php echo $c['content_id']; ?>" data-confirm="<?php echo CONFIRM_DELETE; ?>" title="<?php echo BUTTON_DELETE; ?>"><i class="icon-trash"></i></a>
											<?php } ?>
										</div>
									</td>
								</tr>
							<?php } ?>
						<?php } ?>
					</table>
				</div>
			<?php } ?>
		</div>

		<hr>

		<div class="alert alert-info"><?php echo CONTENT_NOTE; ?></div>

	<?php } ?>
<?php } ?>

<?php $main->bottom(); ?>