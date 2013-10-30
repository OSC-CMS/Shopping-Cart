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

if ($_GET['sorting'])
{
	switch ($_GET['sorting'])
	{
		case 'id':
			$prodsort = 'p.products_id ASC';
		break;
		case 'id-desc':
			$prodsort = 'p.products_id DESC';
		break;
		case 'name':
			$prodsort = 'pd.products_name ASC';
		break;
		case 'name-desc':
			$prodsort = 'pd.products_name DESC';
		break;
		case 'price':
			$prodsort = 'p.products_price ASC';
		break;
		case 'price-desc':
			$prodsort = 'p.products_price DESC';
		break;
		case 'stock':
			$prodsort = 'p.products_quantity ASC';
		break;
		case 'stock-desc':
			$prodsort = 'p.products_quantity DESC';
		break;
		default:
			$prodsort = 'pd.products_name ASC';
		break;
	}
}
else
{
	$prodsort = 'p.products_sort, pd.products_name ASC';
}

$manufacturer = '';
if (isset($_GET['manufacturer']) && !empty($_GET['manufacturer']))
{
	$manufacturer = "AND p.manufacturers_id = ".(int)$_GET['manufacturer']."";
}

$i = 0;
$group_query = os_db_query("SELECT customers_status_image, customers_status_id, customers_status_name FROM ".TABLE_CUSTOMERS_STATUS." WHERE language_id = '".$_SESSION['languages_id']."' AND customers_status_id != '0'");
while ($group_values = os_db_fetch_array($group_query))
{
	// load data into array
	$i ++;
	$group_data[$i] = array
	(
		'STATUS_NAME' => $group_values['customers_status_name'],
		'STATUS_IMAGE' => $group_values['customers_status_image'],
		'STATUS_ID' => $group_values['customers_status_id']
	);
}

$breadcrumb->add(HEADING_TITLE, FILENAME_QUICK_UPDATES);


function manufacturers_list()
{
	$manufacturers_query = os_db_query("select m.manufacturers_id, m.manufacturers_name from ".TABLE_MANUFACTURERS." m order by m.manufacturers_name ASC");
	$return_string = '<select name="manufacturer" onChange="this.form.submit();">';
	$return_string .= '<option value="0">'.TEXT_ALL_MANUFACTURERS.'</option>';
	while($manufacturers = os_db_fetch_array($manufacturers_query))
	{
		$return_string .= '<option value="'.$manufacturers['manufacturers_id'].'"';

		if($_GET['manufacturer'] && $manufacturers['manufacturers_id'] == $_GET['manufacturer'])
			$return_string .= ' SELECTED';

		$return_string .= '>'.$manufacturers['manufacturers_name'].'</option>';
	}
	$return_string .= '</select>';
	return $return_string;
}

$row_bypage_array = array(array('id' => '', 'text' => TEXT_MAXI_ROW_BY_PAGE));
for ($i = 50; $i <= 500 ; $i=$i+50)
{
	$row_bypage_array[] = array('id' => $i, 'text' => $i);
}

$row_by_page = $_REQUEST['row_by_page'];
($row_by_page) ? define('MAX_DISPLAY_ROW_BY_PAGE' , $row_by_page ) : $row_by_page = MAX_DISPLAY_ADMIN_PAGE; define('MAX_DISPLAY_ROW_BY_PAGE' , MAX_DISPLAY_ADMIN_PAGE );

// массовое изменение цен
if (isset($_GET['action']) && $_GET['action'] == 'multiup')
{
	$price = $_POST['price'];
	$value = trim($price, '%');
	if (is_numeric($value))
	{
		$isPersent = substr($price, -1) == '%';
		$sql = 'UPDATE '.TABLE_PRODUCTS.' SET products_price = products_price + '.($isPersent ? 'products_price * ('.$value.'/100)' : $value);
		os_db_query($sql);
	}
	os_redirect(FILENAME_QUICK_UPDATES);
}

$main->head();
$main->top_menu();
?>
<div class="second-page-nav">

	<form class="form-inline" method="post" action="quick_updates.php?action=multiup">
		<label class="checkbox"><?php echo TEXT_MARGE_INFO; ?></label>
		<input type="text" class="input-medium" name="price">
		<button type="submit" class="btn">OK</button>
		<label class="checkbox"><?php echo TEXT_SPEC_PRICE_INFO1; ?></label>
	</form>

	<hr>

	<div class="row-fluid">
		<div class="span3">
			<?php echo os_draw_form('search', FILENAME_QUICK_UPDATES, '', 'get'); ?>
				<input type="hidden" name="cPath" value="<?php echo $_GET['cPath']; ?>">
				<input type="hidden" name="row_by_page" value="<?php echo $_GET['row_by_page']; ?>">
				<input type="hidden" name="manufacturer" value="<?php echo $_GET['manufacturer']; ?>">
				<input type="hidden" name="sorting" value="<?php echo $_GET['sorting']; ?>">
				<fieldset>
					<?php echo os_draw_input_field('search', '', 'placeholder="'.HEADING_TITLE_SEARCH.'…"'); ?>
				</fieldset>
			</form>
		</div>
		<div class="span3">
			<?php echo os_draw_form('manufacturers', FILENAME_QUICK_UPDATES, '', 'get'); ?>
				<input type="hidden" name="cPath" value="<?php echo $_GET['cPath']; ?>">
				<input type="hidden" name="row_by_page" value="<?php echo $_GET['row_by_page']; ?>">
				<input type="hidden" name="sorting" value="<?php echo $_GET['sorting']; ?>">
				<fieldset>
					<?php echo manufacturers_list(); ?>
				</fieldset>
			</form>
		</div>
		<div class="span3">
			<?php echo os_draw_form('cPath', FILENAME_QUICK_UPDATES, '', 'get'); ?>
				<input type="hidden" name="manufacturer" value="<?php echo $_GET['manufacturer']; ?>">
				<input type="hidden" name="row_by_page" value="<?php echo $_GET['row_by_page']; ?>">
				<input type="hidden" name="sorting" value="<?php echo $_GET['sorting']; ?>">
				<fieldset>
					<?php echo os_draw_pull_down_menu('cPath', os_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"'); ?>
				</fieldset>
			</form>
		</div>
		<div class="span3">
			<div class="pull-right">
				<?php echo os_draw_form('row_by_page', FILENAME_QUICK_UPDATES, '', 'get'); ?>
					<input type="hidden" name="manufacturer" value="<?php echo $_GET['manufacturer']; ?>">
					<input type="hidden" name="cPath" value="<?php echo $_GET['cPath']; ?>">
					<input type="hidden" name="sorting" value="<?php echo $_GET['sorting']; ?>">
					<fieldset>
					<?php echo  os_draw_pull_down_menu('row_by_page', $row_bypage_array, $row_by_page, 'onChange="this.form.submit();"'); ?>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>

<form id="quick_updates" action="" method="post">

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th><?php echo '#'.os_sorting(FILENAME_QUICK_UPDATES, 'id'); ?></th>
				<th><span class="line"></span><?php echo TABLE_HEADING_PRODUCTS.os_sorting(FILENAME_QUICK_UPDATES, 'name'); ?></th>
				<?php if (STOCK_CHECK == 'true') { ?>
				<th><span class="line"></span><?php echo TABLE_HEADING_QUANTITY.os_sorting(FILENAME_QUICK_UPDATES, 'stock'); ?></th>
				<?php } ?>
				<th><span class="line"></span><?php echo TABLE_HEADING_PRICE.os_sorting(FILENAME_QUICK_UPDATES, 'price'); ?></th>
				<?php
				if (is_array($group_data) && !empty($group_data))
				{
					foreach ($group_data AS $gdId => $gdValue)
					{
						?><th><span class="line"></span><?php echo $gdValue['STATUS_NAME']; ?></th><?php
					}
				}
				?>
			</tr>
		</thead>
		<?php
		if ($_GET['search'])
			$products_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c WHERE p.products_id = pd.products_id AND pd.language_id = '".$_SESSION['languages_id']."' AND p.products_id = p2c.products_id AND (pd.products_name like '%".os_db_prepare_input($_GET['search'])."%' OR p.products_model = '".os_db_prepare_input($_GET['search'])."') ".$manufacturer." ORDER BY ".$prodsort);
		else
			$products_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c WHERE p.products_id = pd.products_id AND pd.language_id = '".(int)$_SESSION['languages_id']."' AND p.products_id = p2c.products_id ".$manufacturer." ORDER BY ".$prodsort);

		$numr = os_db_num_rows($products_query);
		$products_count = 0;

		if (!isset($_GET['page'])){$page=0;} else { $page = $_GET['page']; };

		$max_count = MAX_DISPLAY_ROW_BY_PAGE;

		if ( (isset($product_id)) and ($numr>0) )
		{
			$pnum=1;

			while ($row=os_db_fetch_array($products_query, true))
			{
				if ($row["products_id"]==$product_id)
				{
					$pnum=($pnum/$max_count);
					if (strpos($pnum,".")>0)
					{
						$pnum=substr($pnum,0,strpos($pnum,"."));
					}
					else
					{
						if ($pnum<>0)
						{
							$pnum=$pnum-1;
						}
					}
					$page = $pnum*$max_count;
					echo $page;
					break;
				}
				$pnum++;
			}
		}

		$page = $page == 0 ? 1 : $page;
		$page = ($page-1)*MAX_DISPLAY_ROW_BY_PAGE;

		if ($_GET['search'])
			$products_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c WHERE p.products_id = pd.products_id AND pd.language_id = '".$_SESSION['languages_id']."' AND p.products_id = p2c.products_id AND (pd.products_name like '%".os_db_prepare_input($_GET['search'])."%' OR p.products_model = '".os_db_prepare_input($_GET['search'])."') ".$manufacturer." ORDER BY ".$prodsort." limit ".$page.",".$max_count);
		else
			$products_query = os_db_query("SELECT * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c WHERE p.products_id = pd.products_id AND pd.language_id = '".(int)$_SESSION['languages_id']."' AND p.products_id = p2c.products_id ".$manufacturer." ORDER BY ".$prodsort." limit ".$page.",".$max_count);

		while ($products = os_db_fetch_array($products_query))
		{
			$products_count++;
			$rows++;

			if (@$_GET['search'])
				$cPath=$products['categories_id'];
			?>
			<tr class="products_tr">
				<td><?php echo $products['products_id']; ?></td>
				<td><?php echo $products['products_name']; ?></td>
				<?php if (STOCK_CHECK == 'true') { ?>
				<td><input class="width100px" type="text" name="<?php echo $products['products_id']; ?>[products_quantity]" value="<?php echo $products['products_quantity']; ?>" /></td>
				<?php } ?>
				<td class="tcenter">
					<?php
					if (PRICE_IS_BRUTTO == 'true')
						$products_price = os_round($products['products_price'] * ((100 + os_get_tax_rate($products['products_tax_class_id'])) / 100), PRICE_PRECISION);
					else
						$products_price = $products['products_price'];
					?>
					<input class="width100px" type="text" name="<?php echo $products['products_id']; ?>[products_price]" value="<?php echo $products_price; ?>" />
					
				</td>
				<?php
				if (is_array($group_data) && !empty($group_data))
				{
					$products_price = '';
					foreach ($group_data AS $gdId => $gdValue)
					{
						if ($gdValue['STATUS_NAME'] != '')
						{
							if (PRICE_IS_BRUTTO == 'true')
								$products_price = os_round(get_group_price($gdValue['STATUS_ID'], $products['products_id']) * ((100 + os_get_tax_rate($pInfo->products_tax_class_id)) / 100), PRICE_PRECISION);
							else
								$products_price = os_round(get_group_price($gdValue['STATUS_ID'], $products['products_id']), PRICE_PRECISION);
							?>
							<td class="tcenter">
								<input type="text" name="<?php echo $products['products_id']; ?>[products_price_<?php echo $gdValue['STATUS_ID']; ?>]" value="<?php echo $products_price; ?>" class="width100px">
								</td>
							<?php
						}
					}
				}
				?>
			</tr>
			<?php } ?>
		<tbody>
	</table>


		<div class="pagination pagination-mini pagination-right">
			<ul>
			<?php
			if ($numr > $max_count)
			{
				$_param = array(
					'file_name' => FILENAME_QUICK_UPDATES,
					'page_name' => 'page',
					'param' => array('cPath' => $cPath)
				);

				if (!empty($_GET['search']))
				{
					$_param['param']['search'] = $_GET['search'];
				}

				if (!empty($_GET['manufacturer']))
				{
					$_param['param']['manufacturer'] = $_GET['manufacturer'];
				}

				if (!empty($_GET['row_by_page']))
				{
					$_param['param']['row_by_page'] = $_GET['row_by_page'];
				}

				if (!empty($_GET['sorting']))
				{
					$_param['param']['sorting'] = $_GET['sorting'];
				}
				echo osc_pages_menu($numr, $max_count, $_GET['page'], $_param);
			}
			?>
			</ul>
		</div>

	<hr>

	<div class="tcenter footer-btn">
		<input class="btn btn-success ajax-save-form" data-form-action="products_saveQuickUpdates" type="submit" value="<?php echo BUTTON_SAVE; ?>" />
	</div>
</form>

<?php $main->bottom(); ?>