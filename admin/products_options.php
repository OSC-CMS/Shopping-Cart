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

$type_array = array();
$type_array[] = array('id'=>'0','text'=>TEXT_TYPE_SELECT);
$type_array[] = array('id'=>'1','text'=>TEXT_TYPE_DROPDOWN);
$type_array[] = array('id'=>'2','text'=>TEXT_TYPE_TEXT);
$type_array[] = array('id'=>'3','text'=>TEXT_TYPE_TEXTAREA);
$type_array[] = array('id'=>'4','text'=>TEXT_TYPE_RADIO);
$type_array[] = array('id'=>'5','text'=>TEXT_TYPE_CHECKBOX);
$type_array[] = array('id'=>'6','text'=>TEXT_TYPE_READ_ONLY);

if ($_GET['action'])
{
	$page_info = 'option_page='.$_GET['option_page'].'&value_page='.$_GET['value_page'].'&attribute_page='.$_GET['attribute_page'];

	switch($_GET['action'])
	{
		case 'add_product_options':
			for ($i = 0, $n = sizeof($languages); $i < $n; $i ++)
			{
				$option_name = $_POST['option_name'];
				$option_rows = (int)$_POST['option_rows'];
				$option_size = (int)$_POST['option_size'];
				$option_length = (int)$_POST['option_length'];
				$option_type = (int)$_POST['options_type'];      

				os_db_query("insert into ".TABLE_PRODUCTS_OPTIONS." (products_options_id,products_options_name, language_id,products_options_type,products_options_length,products_options_rows,products_options_size) values ('".$_POST['products_options_id']."', '".$option_name[$languages[$i]['id']]."', '".$languages[$i]['id']."','".$option_type."','".$option_length."','".$option_rows."','".$option_size."')");
			}

			os_redirect(os_href_link(FILENAME_PRODUCTS_OPTIONS, $page_info));
		break;

		case 'update_option_name':
			for ($i = 0, $n = sizeof($languages); $i < $n; $i ++)
			{
				$option_name = $_POST['option_name'];
				$id = (int)$_POST['option_id'];
				$option_rows = (int)$_POST['option_rows'];
				$option_size = (int)$_POST['option_size'];
				$option_length = (int)$_POST['option_length'];
				$option_type = (int)$_POST['options_type']; 
				os_db_query("update ".TABLE_PRODUCTS_OPTIONS." set products_options_name = '".$option_name[$languages[$i]['id']]."' where products_options_id = '".$id."' and language_id = '".$languages[$i]['id']."'");
				os_db_query("update ".TABLE_PRODUCTS_OPTIONS." set products_options_type = '".$option_type."' where products_options_id = '".$id."' and language_id = '".$languages[$i]['id']."'");
				os_db_query("update ".TABLE_PRODUCTS_OPTIONS." set products_options_length = '".$option_length."' where products_options_id = '". $id."' and language_id = '".$languages[$i]['id']."'");
				os_db_query("update ".TABLE_PRODUCTS_OPTIONS." set products_options_rows = '".$option_rows."' where products_options_id = '".$id."' and language_id = '".$languages[$i]['id']."'");
				os_db_query("update ".TABLE_PRODUCTS_OPTIONS." set products_options_size = '".$option_size."' where products_options_id = '".$id."' and language_id = '".$languages[$i]['id']."'");
			}

			os_redirect(os_href_link(FILENAME_PRODUCTS_OPTIONS, $page_info));
		break;

		case 'delete_option':
			$del_options = os_db_query("select products_options_values_id from ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." where products_options_id = '".$_GET['option_id']."'");
			while($del_options_values = os_db_fetch_array($del_options))
			{  
				os_db_query("delete from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".$_GET['option_id']."'");
			}
			os_db_query("delete from ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." where products_options_id = '".$_GET['option_id']."'");
			os_db_query("delete from ".TABLE_PRODUCTS_OPTIONS." where products_options_id = '".$_GET['option_id']."'");

			os_redirect(os_href_link(FILENAME_PRODUCTS_OPTIONS, $page_info));
		break;
	}
}

add_action('head_admin', 'head_go_option');

function head_go_option ()
{
	echo '<script type="text/javascript"><!--
	function go_option()
	{
		if (document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value != "none") {
			location = "'.os_href_link(FILENAME_PRODUCTS_OPTIONS, 'option_page=' . ($_GET['option_page'] ? $_GET['option_page'] : 1)).'&option_order_by="+document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value;
		}
	}
	//--></script>';
}

$breadcrumb->add(HEADING_TITLE_OPT.' - '.HEADING_TITLE_VAL);

$main->head();
$main->top_menu();


if ($_GET['action'] == 'delete_product_option')
{
	$options = os_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $_GET['option_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
	$options_values = os_db_fetch_array($options);
?>
<div class="row-fluid">
	<div class="well well-box well-nice">
		<div class="navbar">
			<div class="navbar-inner">
			    <h4 class="title"><?php echo $options_values['products_options_name']; ?></h4>
			</div>
		</div>
		<div class="well-box-content well-small-font">
			<table class="table table-striped">
			<?php
				$products = os_db_query("select p.products_id, pd.products_name, pov.products_options_values_name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_OPTIONS_VALUES." pov, ".TABLE_PRODUCTS_ATTRIBUTES." pa, ".TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pov.language_id = '".(int)$_SESSION['languages_id']."' and pd.language_id = '".(int)$_SESSION['languages_id']."' and pa.products_id = p.products_id and pa.options_id='".(int)$_GET['option_id']."' and pov.products_options_values_id = pa.options_values_id order by pd.products_name");
				if (os_db_num_rows($products))
				{
					?>
					<thead>
						<tr>
							<th align="center"><?php echo TABLE_HEADING_ID; ?></th>
							<th><?php echo TABLE_HEADING_PRODUCT; ?></th>
							<th><?php echo TABLE_HEADING_OPT_VALUE; ?></th>
						</tr>
					</thead>
					<?php
					while ($products_values = os_db_fetch_array($products))
					{
						?>
						<tr>
							<td align="center"><?php echo $products_values['products_id']; ?></td>
							<td><?php echo $products_values['products_name']; ?></td>
							<td><?php echo $products_values['products_options_values_name']; ?></td>
						</tr>
						<?php
					}
					?>
					<tr>
						<td colspan="3"><span class="label label-important"><?php echo TEXT_WARNING_OF_DELETE; ?></span></td>
					</tr>
					<tr>
						<td colspan="3">
							<?php echo $cartet->html->link(BUTTON_CANCEL, os_href_link(FILENAME_PRODUCTS_OPTIONS, '&value_page='.$_GET['value_page'].'&attribute_page='.$attribute_page, 'NONSSL'), array('class' => 'btn')); ?>
						</td>
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
							<?php echo $cartet->html->link(BUTTON_DELETE, os_href_link(FILENAME_PRODUCTS_OPTIONS, 'action=delete_option&option_id='.$_GET['option_id'], 'NONSSL'), array('class' => 'btn btn-danger')); ?>
							<?php echo $cartet->html->link(BUTTON_CANCEL, os_href_link(FILENAME_PRODUCTS_OPTIONS, '&order_by='.$order_by.'&page='.$page, 'NONSSL'), array('class' => 'btn btn-link')); ?>
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
				<form name="search" action="<?php echo FILENAME_PRODUCTS_OPTIONS; ?>" method="get">
					<fieldset>
						<input type="text" name="searchoption" placeholder="<?php echo TEXT_SEARCH; ?>" value="<?php echo $_GET['searchoption']; ?>" />
					</fieldset>
				</form>
			</div>
			<div class="span6">
				<div class="pull-right">
					<form name="option_order_by" action="<?php echo FILENAME_PRODUCTS_OPTIONS; ?>">
						<fieldset>
							<select name="selected" onChange="go_option()">
								<option value="products_options_id"<?php if ($option_order_by == 'products_options_id') { echo ' SELECTED'; } ?>><?php echo TEXT_OPTION_ID; ?></option>
								<option value="products_options_name"<?php if ($option_order_by == 'products_options_name') { echo ' SELECTED'; } ?>><?php echo TEXT_OPTION_NAME; ?></option>
							</select>
						</fieldset>
					</form>
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span8"></div>
			<div class="span4">
				<div class="pull-right">
					<a href="#add_product_options" role="button" class="btn btn-info btn-mini" data-toggle="modal"><?php echo TEXT_OPTION_ADD; ?></a>
				</div>
			</div>
		</div>
	</div>

	<?php
	if ($_GET['option_order_by'] && !empty($_GET['option_order_by']))
		$option_order_by = $_GET['option_order_by'];
	else
		$option_order_by = 'products_options_id';

	//------------------------------ sort

	$option_page = (int)$_GET['option_page'];
	$per_page = 20;

	$order = '';
	if (isset($_GET['sort']))
	{
		//id
		if ($_GET['sort-type'] == 'id' && strtolower($_GET['sort']) == 'desc')
			$order = ' ORDER BY products_options_id DESC';
		elseif ($_GET['sort-type'] == 'id' && strtolower($_GET['sort']) == 'asc')
			$order = ' ORDER BY products_options_id ASC';

		//name
		if ($_GET['sort-type'] == 'name' && strtolower($_GET['sort']) == 'desc')
			$order = ' ORDER BY products_options_name DESC';
		elseif ($_GET['sort-type'] == 'name' && strtolower($_GET['sort']) == 'asc')
			$order = ' ORDER BY products_options_name ASC';
	}

	if (isset($_GET['searchoption']) && !empty($_GET['searchoption']))
		$options = "select * from ".TABLE_PRODUCTS_OPTIONS." where language_id = '".$_SESSION['languages_id']."' and products_options_name LIKE '%".$_GET['searchoption']."%' order by ".$option_order_by."";
	else
		$options = "select * from ".TABLE_PRODUCTS_OPTIONS." where language_id = '".$_SESSION['languages_id']."' ".$order."";

	if (!$option_page)
	{
		$option_page = 1;
	}

	$prev_option_page = $option_page - 1;
	$next_option_page = $option_page + 1;

	$option_query = os_db_query($options);

	$option_page_start = ($per_page * $option_page) - $per_page;
	$num_rows = os_db_num_rows($option_query);

	if ($num_rows <= $per_page)
		$num_pages = 1;
	elseif (($num_rows % $per_page) == 0)
		$num_pages = ($num_rows / $per_page);
	else
		$num_pages = ($num_rows / $per_page) + 1;

	$num_pages = (int) $num_pages;

	$options = $options." LIMIT $option_page_start, $per_page";

	?>
	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th>
					<?php echo TABLE_HEADING_ID; ?> 
					<?php echo '<a href="'.os_href_link(FILENAME_PRODUCTS_OPTIONS, 'sort=asc&sort-type=id">&uarr;</a>'); ?>
					<?php echo '<a href="'.os_href_link(FILENAME_PRODUCTS_OPTIONS, 'sort=desc&sort-type=id">&darr;</a>'); ?>
				</th>
				<th>
					<span class="line"></span><?php echo TABLE_HEADING_OPT_NAME; ?>
					<?php echo '<a href="'.os_href_link(FILENAME_PRODUCTS_OPTIONS, 'sort=asc&sort-type=name">&uarr;</a>'); ?>
					<?php echo '<a href="'.os_href_link(FILENAME_PRODUCTS_OPTIONS, 'sort=desc&sort-type=name">&darr;</a>'); ?>
				</th>
				<th><?php echo TABLE_HEADING_OPT_TYPE; ?></th>
				<th><span class="line"></span><?php echo TEXT_ROWS; ?></th>
				<th><span class="line"></span><?php echo TEXT_SIZE; ?></th>
				<th><span class="line"></span><?php echo TEXT_MAX_LENGTH; ?></th>
				<th width="100"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
	<?php
	$next_id = 1;
	$options = os_db_query($options);
	while ($options_values = os_db_fetch_array($options))
	{
		echo '<tr '.((isset($_GET['option_id']) && $_GET['option_id'] == $options_values['products_options_id']) ? 'class="error"' : '').' >';
			if (($_GET['action'] == 'update_option') && ($_GET['option_id'] == $options_values['products_options_id']))
			{
				echo '<form name="option" action="'.os_href_link(FILENAME_PRODUCTS_OPTIONS, 'action=update_option_name&option_page='.$_GET['option_page'], 'NONSSL').'" method="post">';
				$inputs = '';
				for ($i = 0, $n = sizeof($languages); $i < $n; $i ++)
				{
					$option_name = os_db_query("select * from ".TABLE_PRODUCTS_OPTIONS." where products_options_id = '".$options_values['products_options_id']."' and language_id = '".$languages[$i]['id']."'");
					$option_name = os_db_fetch_array($option_name);
					$type = $option_name['products_options_type'];
					$inputs .= $languages[$i]['name'].': <input type="text" name="option_name['.$languages[$i]['id'].']" size="20" value="'.$option_name['products_options_name'].'"><br />';
				}
				?>
				<td>
					<?php echo $options_values['products_options_id']; ?>
					<input type="hidden" name="option_id" value="<?php echo $options_values['products_options_id']; ?>">
				</td>
				<td><?php echo $inputs; ?></td>
				<td><?php echo os_draw_pull_down_menu('options_type', $type_array, $type_array[$type]['id']); ?></td>
				<td><input type="text" name="option_rows" size="4" value="<?php echo $options_values['products_options_rows'];?>"></td>
				<td><input type="text" name="option_size" size="4" value="<?php echo $options_values['products_options_size']; ?>"></td>
				<td><input type="text" name="option_length" size="4" value="<?php echo $options_values['products_options_length']; ?>"></td>
				<td>
					<div class="btn-group pull-right">
						<?php echo $cartet->html->input_submit(
							'update_options',
							BUTTON_UPDATE,
							array('class' => 'btn btn-mini')
						); ?>
						<?php echo $cartet->html->link(
							'<i class="icon-hand-right" title="'.BUTTON_CANCEL.'"></i>',
							os_href_link(FILENAME_PRODUCTS_OPTIONS, '', 'NONSSL'),
							array('class' => 'btn btn-mini')
						); ?>
					</div>
				</td>
				</form>
				<?php
			}
			else
			{
				?>
				<td><?php echo $options_values["products_options_id"]; ?></td>
				<td><?php echo $options_values["products_options_name"]; ?></td>
				<td><?php echo $type_array[$options_values['products_options_type']]['text']; ?></td>
				<td><?php echo $options_values['products_options_rows']; ?></td>
				<td><?php echo $options_values['products_options_size']; ?></td>
				<td><?php echo $options_values['products_options_length']; ?></td>
				<td>
					<div class="btn-group pull-right">
						<?php echo $cartet->html->link(
							'<i class="icon-edit" title="'.BUTTON_EDIT.'"></i>',
							os_href_link(FILENAME_PRODUCTS_OPTIONS, 'action=update_option&option_id='.$options_values['products_options_id'].'&option_order_by='.$option_order_by.'&option_page='.$option_page, 'NONSSL'),
							array('class' => 'btn btn-mini')
						); ?>
						<?php echo $cartet->html->link(
							'<i class="icon-trash" title="'.BUTTON_DELETE.'"></i>',
							os_href_link(FILENAME_PRODUCTS_OPTIONS, 'action=delete_product_option&option_id='.$options_values['products_options_id'], 'NONSSL'),
							array('class' => 'btn btn-mini')
						); ?>
					</div>
				</td>
				<?php
			}
			?>
		</tr>
		<?php
		$max_options_id_query = os_db_query("select max(products_options_id) + 1 as next_id from ".TABLE_PRODUCTS_OPTIONS);
		$max_options_id_values = os_db_fetch_array($max_options_id_query);
		$next_id = $max_options_id_values['next_id'];
	}
	?>
	</table>
	<div class="action-table">

		<div class="pull-right">
			<div class="pagination pagination-mini pagination-right">
				<ul>
					<?php
					if ($prev_option_page) 
					{
						echo '<li><a href="'.os_href_link(FILENAME_PRODUCTS_OPTIONS, 'option_page='.$prev_option_page.'&searchoption='.$_GET['searchoption']).'">&laquo;</a></li>';
					}

					for ($i = 1; $i <= $num_pages; $i++)
					{
						if ($i != $option_page)
						{
							echo '<li><a href="'.os_href_link(FILENAME_PRODUCTS_OPTIONS, 'option_page='.$i.'&searchoption='.$_GET['searchoption']).'">'.$i.'</a></li>';
						}
						else
						{
							echo '<li class="active"><span>'.$i.'</span></li>';
						}
					}

					// Next
					if ($option_page != $num_pages)
					{
						echo '<li><a href="'.os_href_link(FILENAME_PRODUCTS_OPTIONS, 'option_page='.$next_option_page.'&searchoption='.$_GET['searchoption']).'">&raquo;</a></li>';
					}
					?>
				</ul>
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<div id="add_product_options" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h5 id="myModalLabel"><?php echo TEXT_OPTION_ADD; ?></h5>
		</div>
		<form name="options" class="form-horizontal" action="<?php echo os_href_link(FILENAME_PRODUCTS_OPTIONS, 'action=add_product_options&option_page='.$option_page, 'NONSSL'); ?>" method="post">
			<div class="modal-body">
				<p>
					<input type="hidden" name="products_options_id" value="<?php echo $next_id; ?>">
					<?php
					$inputs = '';
					sort($languages);
					for ($i = 0, $n = sizeof($languages); $i < $n; $i ++)
					{
						$inputs .= '<div class="control-group">
							<label class="control-label" for="option_name_'.$languages[$i]['id'].'">'.$languages[$i]['name'].'</label>
							<div class="controls">
								<input class="round" type="text" name="option_name['.$languages[$i]['id'].']" id="option_name_'.$languages[$i]['id'].'" />
							</div>
						</div>';
					}
					echo $inputs;
					?>

					<div class="control-group">
						<label class="control-label" for="options_type"><?php echo TABLE_HEADING_OPT_TYPE_1; ?></label>
						<div class="controls">
							<?php echo os_draw_pull_down_menu('options_type', $type_array, '', ' onchange="selectShowHide(this.value, \'option-hide\')"'); ?>
						</div>
					</div>
					<div class="option-hide">
						<div class="div-option-hide-2" style="display:none;">
							<hr>
							<div class="alert alert-info"><?php echo TEXT_NOTE; ?></div>
							<div class="control-group">
								<label class="control-label" for="option_rows"><?php echo TEXT_ROWS; ?></label>
								<div class="controls">
									<input type="text" name="option_rows" id="option_rows" size="4" value="1" />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="option_size"><?php echo TEXT_SIZE; ?></label>
								<div class="controls">
									<input class="round" type="text" name="option_size" id="option_size" size="4" value="32" />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="option_length"><?php echo TEXT_MAX_LENGTH; ?></label>
								<div class="controls">
									<input class="round" type="text" name="option_length" id="option_length" size="4" value="64" />
								</div>
							</div>
						</div>
					</div>
				</p>
			</div>
			<div class="modal-footer">
				<?php echo $cartet->html->input_submit('products_options', BUTTON_INSERT, array('class' => 'btn btn-success')); ?>
			</div>
		</form>
	</div>

<?php $main->bottom(); ?>