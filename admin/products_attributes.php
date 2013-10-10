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

$order = '';
if (isset($_GET['sort']))
{
	//id
	if ($_GET['sort-type'] == 'id' && strtolower($_GET['sort']) == 'desc')
	{
		$order = ' ORDER BY pov.products_options_values_id DESC';
	}
	elseif ($_GET['sort-type'] == 'id' && strtolower($_GET['sort']) == 'asc')
	{
		$order = ' ORDER BY pov.products_options_values_id ASC';
	}    


	//value
	if ($_GET['sort-type'] == 'value' && strtolower($_GET['sort']) == 'desc')
	{
		$order = ' ORDER BY pov.products_options_values_name DESC';
	}
	elseif ($_GET['sort-type'] == 'value' && strtolower($_GET['sort']) == 'asc')
	{
		$order = ' ORDER BY pov.products_options_values_name ASC';
	}
	//$order
}

$languages = os_get_languages();
$max_byte_size = MAX_BYTE_SIZE;
$max_thumb_width = MAX_THUMB_WIDTH;
$max_thumb_height = MAX_THUMB_HEIGHT;
$max_admin_width = MAX_ADMIN_WIDTH;
$max_admin_height = MAX_ADMIN_HEIGHT;

if($_GET['status'] == '0') $messageStack->add(TEXT_ATTRIBUTE_FILE_1);
if($_GET['status'] == '1') $messageStack->add(TEXT_ATTRIBUTE_FILE_2);
if($_GET['status'] == '2') $messageStack->add(TEXT_ATTRIBUTE_FILE_3);
if($_GET['status'] == '3') $messageStack->add(TEXT_ATTRIBUTE_FILE_4);
if($_GET['status'] == '4') $messageStack->add(TEXT_ATTRIBUTE_FILE_5);
if($_GET['status'] == '5') $messageStack->add(TEXT_ATTRIBUTE_FILE_6);
if($_GET['status'] == '6') $messageStack->add(TEXT_ATTRIBUTE_FILE_7);
if($_GET['status'] == '7') $messageStack->add(TEXT_ATTRIBUTE_FILE_8);

if($_GET['status'] == 'image_processing')
{
	$files_to_rebuild = os_db_query('SELECT products_options_values_image FROM '.TABLE_PRODUCTS_OPTIONS_VALUES.' WHERE products_options_values_image != ""');
	while($file_to_rebuild = os_db_fetch_array($files_to_rebuild))
	{
		$filename = $file_to_rebuild['products_options_values_image'];
		$filetyp = explode('.',$filename);
		$filetyp = ($filetyp[((count($filetyp))-1)]);
		if (!os_attribute_image_processing($filename,$filetyp,_IMG.'attribute_images/',$max_thumb_width,$max_thumb_height,$max_admin_width,$max_admin_height))
			$messageStack->add('failed while image_processing filename: '.$filename);
	}
}

if ($_GET['action'])
{
	$page_info = 'option_page='.$_GET['option_page'].'&value_page='.$_GET['value_page'].'&attribute_page='.$_GET['attribute_page'];
	switch($_GET['action'])
	{
		case 'add_product_option_values':
			for ($i = 0, $n = sizeof($languages); $i < $n; $i ++)
			{
				$value_name = $_POST['value_name'];
				$value_description = $_POST['value_description'];
				$value_text = $_POST['value_text'];
				$value_link = $_POST['value_link'];

				$status = os_upload_attribute_image($_FILES['value_image'],$languages[$i]['id'],$max_byte_size,_IMG.'attribute_images/',$max_thumb_width,$max_thumb_height,$max_admin_width,$max_admin_height);

				if($status[0] == 'success')
				{
					os_db_query("insert into ".TABLE_PRODUCTS_OPTIONS_VALUES." (products_options_values_id, language_id, products_options_values_name, products_options_values_description, products_options_values_text, products_options_values_image, products_options_values_link) values ('".$_POST['value_id']."', '".$languages[$i]['id']."', '".$value_name[$languages[$i]['id']]."', '".$value_description[$languages[$i]['id']]."', '".$value_text[$languages[$i]['id']]."', '".$status[1]."', '".$value_link[$languages[$i]['id']]."')");
				}
			}

			if($status[0] == 'success')
			{
				os_db_query("insert into ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." (products_options_id, products_options_values_id) values ('".$_POST['option_id']."', '".$_POST['value_id']."')");
			}

			os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info.'&status='.$status[1]));
		break;

		case 'update_value' :
			$value_name = $_POST['value_name'];
			$value_description = $_POST['value_description'];
			for ($i = 0, $n = sizeof($languages); $i < $n; $i ++)
			{
				$value_text = $_POST['value_text'];
				$value_link = $_POST['value_link'];
				$new_image = $_POST['orig_image_'.$languages[$i]['code']];
				$status = array('success','');

				if ((isset($_POST['delete_flag'])) and (in_array($languages[$i]['code'],$_POST['delete_flag'])))
				{
					unlink(_IMG.'attribute_images/original/'.$new_image);
					unlink(_IMG.'attribute_images/thumbs/'.$new_image);
					unlink(_IMG.'attribute_images/mini/'.$new_image);
					$new_image = '';
				}

				if ((isset($_POST['edit_flag'])) and (in_array($languages[$i]['code'],$_POST['edit_flag'])))
				{
					$status = os_upload_attribute_image($_FILES['value_image'],$languages[$i]['id'],$max_byte_size,_IMG.'attribute_images/',$max_thumb_width,$max_thumb_height,$max_admin_width,$max_admin_height);
					if($status[0] == 'success')
					{
						unlink(_IMG.'attribute_images/original/'.$new_image);
						unlink(_IMG.'attribute_images/thumbs/'.$new_image);
						unlink(_IMG.'attribute_images/mini/'.$new_image);
						$new_image = $status[1];
					}
				}
				os_db_query("update ".TABLE_PRODUCTS_OPTIONS_VALUES." set products_options_values_name = '".$value_name[$languages[$i]['id']]."', products_options_values_description = '".$value_description[$languages[$i]['id']]."', products_options_values_text = '".$value_text[$languages[$i]['id']]."', products_options_values_image = '".$new_image."', products_options_values_link = '".$value_link[$languages[$i]['id']]."' where products_options_values_id = '".$_POST['value_id']."' and language_id = '".$languages[$i]['id']."'");
			}
			os_db_query("update ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." set products_options_id = '".$_POST['option_id']."' where products_options_values_id = '".$_POST['value_id']."'");

			os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info.'&status='.$status[1]));
		break;

		case 'delete_value':
			$filenames_to_delete = os_db_query("SELECT products_options_values_image from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".$_GET['value_id']."'");
			while($filename_to_delete = os_db_fetch_array($filenames_to_delete))
			{
				if($filename_to_delete['products_options_values_image'] != '')
				{
					unlink(_IMG.'attribute_images/original/'.$filename_to_delete['products_options_values_image']);
					unlink(_IMG.'attribute_images/thumbs/'.$filename_to_delete['products_options_values_image']);
					unlink(_IMG.'attribute_images/mini/'.$filename_to_delete['products_options_values_image']);
				}
			}

			os_db_query("delete from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".$_GET['value_id']."'");
			os_db_query("delete from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".$_GET['value_id']."'");
			os_db_query("delete from ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." where products_options_values_id = '".$_GET['value_id']."'");

			os_redirect(os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
		break;
	}
}

add_action('head_admin', 'head_atributes');

function head_atributes ()
{
	echo '<script type="text/javascript"><!--
	function go_option()
	{
		if (document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value != "none")
		{
			location = "'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page='.($_GET['option_page'] ? $_GET['option_page'] : 1)).'&option_order_by="+document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value;
		}
	}
	//--></script>';
}

$breadcrumb->add(HEADING_TITLE_OPT.' - '.HEADING_TITLE_VAL);

$main->head();
$main->top_menu();

if ($_GET['action'] == 'delete_option_value')
{
	$values = os_db_query("select products_options_values_id, products_options_values_name from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".$_GET['value_id']."' and language_id = '".$_SESSION['languages_id']."'");
	$values_values = os_db_fetch_array($values);
?>
<div class="row-fluid">
	<div class="well well-box well-nice">
		<div class="navbar">
			<div class="navbar-inner">
			    <h4 class="title"><?php echo $values_values['products_options_values_name']; ?></h4>
			</div>
		</div>
		<div class="well-box-content well-small-font">
			<table class="table table-striped">
			<?php
			$products = os_db_query("select p.products_id, pd.products_name, po.products_options_name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_ATTRIBUTES." pa, ".TABLE_PRODUCTS_OPTIONS." po, ".TABLE_PRODUCTS_DESCRIPTION." pd where pd.products_id = p.products_id and pd.language_id = '".$_SESSION['languages_id']."' and po.language_id = '".$_SESSION['languages_id']."' and pa.products_id = p.products_id and pa.options_values_id='".$_GET['value_id']."' and po.products_options_id = pa.options_id order by pd.products_name");
			if (os_db_num_rows($products))
			{
				?>
				<tr>
					<td align="center"><?php echo TABLE_HEADING_ID; ?></td>
					<td><?php echo TABLE_HEADING_PRODUCT; ?></td>
					<td><?php echo TABLE_HEADING_OPT_NAME; ?></td>
				</tr>
				<?php

				while ($products_values = os_db_fetch_array($products))
				{
					?>
					<tr>
						<td align="center"><?php echo $products_values['products_id']; ?></td>
						<td><?php echo $products_values['products_name']; ?></td>
						<td><?php echo $products_values['products_options_name']; ?></td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td colspan="3"><span class="label label-important"><?php echo TEXT_WARNING_OF_DELETE; ?></span></td>
				</tr>
				<tr>
					<td><?php echo $cartet->html->link(BUTTON_CANCEL, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&value_page='.$_GET['value_page'].'&attribute_page='.$attribute_page, 'NONSSL'), array('class' => 'btn')); ?></td>
				</tr>
				<?php
			}
			else
			{
				?>
				<tr>
					<td colspan="3"><span class="label label-success"><?php echo TEXT_OK_TO_DELETE; ?></span></td>
				</tr>
				<tr>
					<td colspan="3">
						<?php echo $cartet->html->link(BUTTON_DELETE, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_value&value_id='.$_GET['value_id'], 'NONSSL'), array('class' => 'btn btn-danger')); ?>
						<?php echo $cartet->html->link(BUTTON_CANCEL, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&option_page='.$option_page.'&value_page='.$_GET['value_page'].'&attribute_page='.$attribute_page, 'NONSSL'), array('class' => 'btn btn-link')); ?>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
		</div>
	</div>
</div>
<?php } ?>

	<div class="second-page-nav">
		<div class="row-fluid">
			<div class="span6">
				<form name="search" action="<?php echo FILENAME_PRODUCTS_ATTRIBUTES; ?>" method="get">
					<fieldset>
						<input type="text" name="search_optionsname" placeholder="<?php echo TEXT_SEARCH; ?>" value="<?php echo $_GET['search_optionsname'];?>" />
					</fieldset>
				</form>
			</div>
			<div class="span6">
				<div class="pull-right">
					<a href="#add_product_options" role="button" class="btn  btn-info btn-mini" data-toggle="modal">Добавить значение</a>
					<?php echo $cartet->html->link(BUTTON_IMAGE_PROCESSING, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'status=image_processing', 'NONSSL'), array('class' => 'btn btn-danger btn-mini')); ?>
				</div>
			</div>
		</div>
	</div>

	<?php
	$per_page = MAX_DISPLAY_ADMIN_PAGE;
	if (isset ($_GET['search_optionsname']))
	{
		$values = "select distinct 
		pov.products_options_values_id, 
		pov.products_options_values_name, 
		pov.products_options_values_description, 
		pov.products_options_values_text,
		pov.products_options_values_image,
		pov.products_options_values_link,
		pov2po.products_options_id 
		from ".TABLE_PRODUCTS_OPTIONS." po,
		".TABLE_PRODUCTS_OPTIONS_VALUES." pov 
		left join ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." pov2po 
		on pov.products_options_values_id = pov2po.products_options_values_id 
		where pov.language_id = '".(int)$_SESSION['languages_id']."' 
		and pov2po.products_options_id = po.products_options_id
		and (po.products_options_name LIKE '%".$_GET['search_optionsname']."%' or pov.products_options_values_name LIKE '%".$_GET['search_optionsname']."%')
		order by pov.products_options_values_id";
	}
	else
	{
		// opt.products_options_name
		$values = "select 
		pov.products_options_values_id, 
		pov.products_options_values_name, 
		pov.products_options_values_description, 
		pov.products_options_values_text,
		pov.products_options_values_image,
		pov.products_options_values_link,
		pov2po.products_options_id
		from ".TABLE_PRODUCTS_OPTIONS_VALUES." pov 
		left join ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." pov2po 
		on pov.products_options_values_id = pov2po.products_options_values_id 
		where pov.language_id = '".(int)$_SESSION['languages_id']."'".$order;
	}

	if (!$_GET['value_page'])
	{
		$_GET['value_page'] = 1;
	}

	$prev_value_page = $_GET['value_page'] - 1;
	$next_value_page = $_GET['value_page'] + 1;

	$value_query = os_db_query($values);

	$value_page_start = ($per_page * $_GET['value_page']) - $per_page;
	$num_rows = os_db_num_rows($value_query);

	if ($num_rows <= $per_page)
	{
		$num_pages = 1;
	}
	elseif (($num_rows % $per_page) == 0)
	{
		$num_pages = ($num_rows / $per_page);
	}
	else
	{
		$num_pages = ($num_rows / $per_page) + 1;
	}

	$num_pages = (int)$num_pages;

	$values = $values." LIMIT $value_page_start, $per_page";
	?>
	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th>
					<?php echo TABLE_HEADING_ID; ?> 
					<?php echo '<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'sort=asc&sort-type=id">&uarr;</a>'); ?>
					<?php echo '<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'sort=desc&sort-type=id">&darr;</a>'); ?>
				</th>
				<th><span class="line"></span><?php echo TABLE_HEADING_OPT_NAME; ?></th>
				<th>
					<span class="line"></span><?php echo TABLE_HEADING_OPT_VALUE; ?> 
					<?php echo '<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'sort=asc&sort-type=value">&uarr;</a>'); ?>
					<?php echo '<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'sort=desc&sort-type=value">&darr;</a>'); ?>
				</th>
				<th align="center"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
	<?php
	$next_id = 1;
	$values = os_db_query($values);
	while ($values_values = os_db_fetch_array($values))
	{
		$options_name = os_options_name($values_values['products_options_id']);
		$values_name = $values_values['products_options_values_name'];
		?>
		<tr>
			<?php
			if (($_GET['action'] == 'update_option_value') && ($_GET['value_id'] == $values_values['products_options_values_id']))
			{
				echo os_draw_form('values', FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_value&value_page='.$_GET['value_page'], 'post', 'enctype="multipart/form-data"');
				$inputs = '';
			?>
			<td align="center" colspan="4">
				<table width="100%" cellpadding="1" cellspacing="0" border="0">
					<tr>
						<td width="100"><?php echo $values_values['products_options_values_id']; ?><input type="hidden" name="value_id" value="<?php echo $values_values['products_options_values_id']; ?>"></td>
						<td width="150"><?php echo TABLE_HEADING_OPT_NAME; ?></td>
						<td width="1">
							<select name="option_id"> 
								<?php
								$options = os_db_query("select products_options_id, products_options_name from ".TABLE_PRODUCTS_OPTIONS." where language_id = '".$_SESSION['languages_id']."' order by products_options_name");
								while ($options_values = os_db_fetch_array($options))
								{
									echo "\n".'<option name="'.$options_values['products_options_name'].'" value="'.$options_values['products_options_id'].'"';
									if ($values_values['products_options_id'] == $options_values['products_options_id'])
									{
										echo ' selected';
									}
									echo '>'.$options_values['products_options_name'].'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<?php
					$inputs = '';
					$inputs_desc = '';
					$inputs_text = '';
					$inputs_image = '';
					$inputs_image_edit = '';
					$inputs_image_delete = '';
					$inputs_link = '';
					sort($languages);
					for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
					{
						$value_name = os_db_query("select products_options_values_name, products_options_values_description, products_options_values_text, products_options_values_image, products_options_values_link from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".$values_values['products_options_values_id']."' and language_id = '".$languages[$i]['id']."'");
						$value_name = os_db_fetch_array($value_name);
						$flag = $languages[$i]['name'];
						$inputs .= $languages[$i]['name'].':&nbsp;<input type="text" name="value_name['.$languages[$i]['id'].']" size="15" value="'.$value_name['products_options_values_name'].'">&nbsp;<input type="hidden" name="orig_image_'.$languages[$i]['code'].'" value="'.$value_name['products_options_values_image'].'"></input><br />';
						$inputs_text .= $languages[$i]['name'].':&nbsp;<input type="text" name="value_text['.$languages[$i]['id'].']" size="15" value="'.$value_name['products_options_values_text'].'">&nbsp;<br />';

						$inputs_desc = $flag.':&nbsp;<textarea name="value_description['.$languages[$i]['id'].']" cols="50" rows="4">'.$value_name['products_options_values_description'].'</textarea>&nbsp;<br />';

						if($value_name['products_options_values_image'] != '')
						{
							$inputs_image .= $languages[$i]['name'].':&nbsp;<img src="'.(($request_type == 'SSL') ? _HTTPS_IMG : _HTTP_IMG).'attribute_images/mini/'.$value_name['products_options_values_image'].'">&nbsp;<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id='.$values_values['products_options_values_id'].'&value_page='.$_GET['value_page'].'&image=edit', 'NONSSL').'">'.os_image(http_path('icons_admin').'icon_edit.gif', IMAGE_EDIT).'</a>&nbsp;<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id='.$values_values['products_options_values_id'].'&value_page='.$_GET['value_page'].'&image=delete', 'NONSSL').'">'.os_image(http_path('icons_admin').'delete.gif', IMAGE_DELETE).'</a>';
							$inputs_image_delete .= $languages[$i]['name'].':&nbsp;<img src="'.http_path('images').'attribute_images/mini/'.$value_name['products_options_values_image'].'"></img>&nbsp;'.os_draw_checkbox_field('delete_flag[]',$languages[$i]['code']).'&nbsp;'.DELETE_TEXT;
						}
						else
						{
							$inputs_image .= $languages[$i]['name'].':&nbsp;<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id='.$values_values['products_options_values_id'].'&value_page='.$_GET['value_page'].'&image=edit', 'NONSSL').'">'.os_image(http_path('icons_admin').'icon_edit.gif', IMAGE_EDIT).'</a>&nbsp;<a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id='.$values_values['products_options_values_id'].'&value_page='.$_GET['value_page'].'&image=delete', 'NONSSL').'">'.os_image(http_path('icons_admin').'delete.gif', IMAGE_DELETE).'</a>';
							$inputs_image_delete .= $languages[$i]['name'].':&nbsp;'.os_draw_checkbox_field('delete_flag[]',$languages[$i]['code']).'&nbsp;'.DELETE_TEXT;
						}

						$inputs_image_edit .= $languages[$i]['code'].':&nbsp;<input type="file" name="value_image['.$languages[$i]['id'].']" size="15" value="'.$value_name['products_options_values_image'].'">&nbsp;'.os_draw_checkbox_field('edit_flag[]',$languages[$i]['code']).'&nbsp;'.EDIT_TEXT.'&nbsp;<br />';
						$inputs_link .= $languages[$i]['name'].':&nbsp;http://<input type="text" name="value_link['.$languages[$i]['id'].']" size="15" value="'.$value_name['products_options_values_link'].'">&nbsp;<br />';
						?>
						<tr>
							<td width="100"></td>
							<td width="150"><b><?php echo TABLE_HEADING_OPT_VALUE; ?></b></td>
							<td align="left"><?php echo $inputs; ?></td>
						</tr>
						<tr>
							<td width="100"></td>
							<td width="150"><b><?php echo TABLE_HEADING_OPT_TEXT; ?></b></td>
							<td  align="left"><?php echo $inputs_text; ?></td>
						</tr>
						<tr>
							<td width="100"></td>
							<td width="150"><b><?php echo TABLE_HEADING_OPT_DESC; ?></b></td>
							<td  align="left"><?php echo $inputs_desc; ?></td>
						</tr>
						<?php if(($_GET['image'] == 'nothing') || (!isset($_GET['image']))) { ?>
						<tr>
							<td width="100"></td>
							<td width="150"><b><?php echo TABLE_HEADING_OPT_IMAGE; ?></b></td>
							<td align="left"><?php echo $inputs_image; ?></td>
						</tr>
						<?php } elseif($_GET['image'] == 'edit') { ?>      
						<tr>
							<td width="100"></td>
							<td width="150"><b><?php echo TABLE_HEADING_OPT_IMAGE; ?></b></td>
							<td align="left"><?php echo $inputs_image_edit; ?></td>
						</tr>
						<?php } elseif($_GET['image'] == 'delete') { ?>
						<tr>
							<td width="100"></td>
							<td width="150"><b><?php echo TABLE_HEADING_OPT_IMAGE; ?></b></td>
							<td align="left"><?php echo $inputs_image_delete; ?></td>
						</tr>
						<?php } ?> 
						<tr>
							<td width="100"></td>
							<td width="150"><b><?php echo TABLE_HEADING_OPT_LINK; ?></b></td>
							<td align="left"><?php echo $inputs_link; ?></td>
						</tr>
						<?php
					} ?>
					<tr>
						<td align="center" colspan="3">
							<?php echo os_button(BUTTON_UPDATE); ?>
							<?php echo os_button_link(BUTTON_CANCEL, os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'value_page='.$_GET['value_page'], 'NONSSL')); ?>
						</td>
					</tr>
				</table>
			</td>
		<?php
		echo '</form>';
		}
		else
		{
			?>
			<td align="center"><?php echo $values_values["products_options_values_id"]; ?></td>
			<td align="center"><?php echo $options_name; ?></td>
			<td><?php echo $values_name; ?></td>
			<td width="100">
				<div class="btn-group pull-right">
					<?php echo $cartet->html->link(
						'<i class="icon-edit" title="'.BUTTON_EDIT.'"></i>',
						os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id='.$values_values['products_options_values_id'].'&value_page='.$_GET['value_page'], 'NONSSL'),
						array('class' => 'btn btn-mini')
					); ?>
					<?php echo $cartet->html->link(
						'<i class="icon-trash" title="'.BUTTON_DELETE.'"></i>',
						os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_option_value&value_id='.$values_values['products_options_values_id'], 'NONSSL'),
						array('class' => 'btn btn-mini')
					); ?>
				</div>
			</td>
			<?php
		}
		$max_values_id_query = os_db_query("select max(products_options_values_id) + 1 as next_id from ".TABLE_PRODUCTS_OPTIONS_VALUES);
		$max_values_id_values = os_db_fetch_array($max_values_id_query);
		$next_id = $max_values_id_values['next_id'];
		}
		?>
		</tr>
	</table>

	<div class="action-table">

		<div class="pull-right">
			<div class="pagination pagination-mini pagination-right">
				<ul>
					<?php
					if ($prev_value_page)
					{
						echo '<li><a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by='.$option_order_by.'&value_page='.$prev_value_page.'&search_optionsname='.$_GET['search_optionsname']).'">&laquo;</a></li>';
					}

					for ($i = 1; $i <= $num_pages; $i++)
					{
						if ($i != $_GET['value_page'])
						{
							echo '<li><a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by='.$option_order_by.'&value_page='.$i.'&search_optionsname='.$_GET['search_optionsname']).'">'.$i.'</a></li>';
						}
						else
						{
							echo '<li class="active"><span>'.$i.'</span></li>';
						}
					}

					if ($_GET['value_page'] != $num_pages)
					{
						echo '<li><a href="'.os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by='.$option_order_by.'&value_page='.$next_value_page.'&search_optionsname='.$_GET['search_optionsname']).'">&raquo;</a></li>';
					}
					?>
				</ul>
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<div id="add_product_options" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<form name="values" class="form-horizontal" action="<?php echo os_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=add_product_option_values&value_page='.$_GET['value_page'], 'NONSSL'); ?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="value_id" value="<?php echo $next_id; ?>" />
			<div class="modal-body">
				<p>
					<div class="control-group">
						<label class="control-label" for=""><?php echo TABLE_HEADING_OPT_NAME; ?></label>
						<div class="controls">
							<select name="option_id">
							<?php
								$options = os_db_query("select products_options_id, products_options_name from ".TABLE_PRODUCTS_OPTIONS." where language_id = '".$_SESSION['languages_id']."' order by products_options_name");
								while ($options_values = os_db_fetch_array($options))
								{
									echo '<option name="'.$options_values['products_options_name'].'" value="'.$options_values['products_options_id'].'">'.$options_values['products_options_name'].'</option>';
								}
							?>
							</select>
						</div>
					</div>
					<?php
					$inputs = '';
					sort($languages);
					for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
					{
						$flag = $languages[$i]['name'];
						$inputs = $flag.': <input type="text" name="value_name['.$languages[$i]['id'].']" />';
						$inputs_desc = $flag.': <textarea name="value_description['.$languages[$i]['id'].']"></textarea>';

						$inputs_text = $flag.': <input type="text" name="value_text['.$languages[$i]['id'].']" />';
						$inputs_image = $flag.': <input type="file" name="value_image['.$languages[$i]['id'].']" />';
						$inputs_link = $flag.': http://<input type="text" name="value_link['.$languages[$i]['id'].']" />';
						?>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TABLE_HEADING_OPT_VALUE; ?></label>
							<div class="controls">
								<?php echo $inputs; ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TABLE_HEADING_OPT_TEXT; ?></label>
							<div class="controls">
								<?php echo $inputs_text; ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TABLE_HEADING_OPT_DESC; ?></label>
							<div class="controls">
								<?php echo $inputs_desc; ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TABLE_HEADING_OPT_IMAGE; ?></label>
							<div class="controls">
								<?php echo $inputs_image; ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for=""><?php echo TABLE_HEADING_OPT_LINK; ?></label>
							<div class="controls">
								<?php echo $inputs_link; ?>
							</div>
						</div>
						<?php
					}
					?>
				</p>
			</div>
			<div class="modal-footer">
				<?php echo $cartet->html->input_submit('options_values', BUTTON_INSERT, array('class' => 'btn btn-success')); ?>
			</div>
		</form>
	</div>

<?php $main->bottom(); ?>