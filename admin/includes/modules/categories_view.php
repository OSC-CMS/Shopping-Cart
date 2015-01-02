<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' ); 

if (@$_GET['sorting'])
{
	switch ($_GET['sorting'])
	{
		case 'sort':
			$catsort = 'c.sort_order ASC';
			$prodsort = 'p.products_sort ASC';
		break;
		case 'sort-desc':
			$catsort = 'c.sort_order DESC';
			$prodsort = 'p.products_sort DESC';
		break;
		case 'name':
			$catsort = 'cd.categories_name ASC';
			$prodsort = 'pd.products_name ASC';
		break;
		case 'name-desc':
			$catsort = 'cd.categories_name DESC';
			$prodsort = 'pd.products_name DESC';
		break;
		case 'status':
			$catsort = 'c.categories_status ASC';
			$prodsort = 'p.products_status ASC';
		break;
		case 'status-desc':
			$catsort = 'c.categories_status DESC';
			$prodsort = 'p.products_status DESC';
		break;
		case 'price':
			$catsort = 'c.sort_order ASC';
			$prodsort = 'p.products_price ASC';
		break;
		case 'price-desc':
			$catsort = 'c.sort_order ASC';
			$prodsort = 'p.products_price DESC';
		break;
		case 'stock':
			$catsort = 'c.sort_order ASC';
			$prodsort = 'p.products_quantity ASC';
		break;
		case 'stock-desc':
			$catsort = 'c.sort_order ASC';
			$prodsort = 'p.products_quantity DESC';
		break;
		case 'stocksort':
			$catsort = 'c.sort_order ASC';
			$prodsort = 'p.stock ASC';
		break;
		case 'stocksort-desc':
			$catsort = 'c.sort_order ASC';
			$prodsort = 'p.stock DESC';
		break;
		case 'discount':
			$catsort = 'c.sort_order ASC';
			$prodsort = 'p.products_discount_allowed ASC';
		break;
		case 'discount-desc':
			$catsort = 'c.sort_order ASC';
			$prodsort = 'p.products_discount_allowed DESC';
		break;
		default:
			$catsort = 'cd.categories_name ASC';
			$prodsort = 'pd.products_name ASC';
		break;
	}
}
else
{
	$catsort = 'c.sort_order, cd.categories_name ASC';
	$prodsort = 'p.products_sort, pd.products_name ASC';
}

$main->head();
$main->top_menu();
?>

<div class="second-page-nav">

	<div class="row-fluid">
		<div class="span6">
			<?php echo os_draw_form('search', FILENAME_CATEGORIES, '', 'get'); ?>
				<fieldset>
					<?php echo os_draw_input_field('search', '', 'placeholder="'.HEADING_TITLE_SEARCH.'…"'); ?>
				</fieldset>
			</form>
		</div>
		<div class="span6">
			<div class="pull-right">
				<?php echo os_draw_form('goto', FILENAME_CATEGORIES, '', 'get'); ?>
					<fieldset>
						<?php echo os_draw_pull_down_menu('cPath', os_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"'); ?>
					</fieldset>
				</form>
			</div>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span8">
			<?php
			if (os_not_null(@$_POST['multi_categories']) || os_not_null(@$_POST['multi_products']))
				$action = "action=multi_action_confirm&".os_get_all_get_params(array('cPath', 'action')).'cPath='.$cPath;
			else
				$action = "action=multi_action&".os_get_all_get_params(array('cPath', 'action')).'cPath='.$cPath;

			echo os_draw_form('multi_action_form', FILENAME_CATEGORIES, $action, 'post', 'id="table-big-list-form"');//, 'onsubmit="javascript:return CheckMultiForm()"'

			echo '<input type="hidden" id="cPath" name="cPath" value="'.$cPath.'">';
			?>
			<div class="btn-group">
				<input class="btn btn-mini btn-success ajax-save-form" data-form-action="products_saveList" type="submit" name="multi_save" value="<?php echo SAVE_ALL; ?>">
				<input class="btn btn-mini btn-disabled ajax-load-page" data-load-page="products&action=multi_move&cPath=<?php echo $cPath; ?>" data-toggle="modal" type="submit" name="multi_move" value="<?php echo BUTTON_MOVE; ?>">
				<input class="btn btn-mini btn-disabled ajax-load-page" data-load-page="products&action=multi_copy&cPath=<?php echo $cPath; ?>" data-toggle="modal" type="submit" name="multi_copy" value="<?php echo BUTTON_COPY; ?>">
				<input class="btn btn-mini btn-disabled" type="submit" name="multi_status_on" value="<?php echo BUTTON_STATUS_ON; ?>">
				<input class="btn btn-mini btn-disabled" type="submit" name="multi_status_off" value="<?php echo BUTTON_STATUS_OFF; ?>">
				<input class="btn btn-mini btn-danger btn-disabled ajax-load-page" data-load-page="products&action=multi_delete&cPath=<?php echo $cPath; ?>" data-toggle="modal" type="submit" name="multi_delete" value="<?php echo BUTTON_DELETE; ?>">
			</div>
		</div>
		<div class="span4">
			<div class="pull-right">
				<?php if (@!$_GET['search']) { ?>
				<div class="btn-group">
					<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')).'cPath='.$cPath.'&action=new_category'); ?>"><?php echo BUTTON_NEW_CATEGORIES; ?></a>
					<a class="btn btn-info btn-mini" href="<?php echo os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')).'cPath='.$cPath.'&action=new_product'); ?>"><?php echo BUTTON_NEW_PRODUCTS; ?></a>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<table class="table table-condensed table-big-list">
<?php
$categories_count = 0;
$rows = 0;
if (@$_GET['search'])
	$categories_query = os_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.yml_enable, c.categories_status, c.menu from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and cd.language_id = '".(int)$_SESSION['languages_id']."' and cd.categories_name like '%".$_GET['search']."%' order by ".$catsort);
else
	$categories_query = os_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.yml_enable, c.categories_status, c.menu from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.parent_id = '".$current_category_id."' and c.categories_id = cd.categories_id and cd.language_id = '".(int)$_SESSION['languages_id']."' order by ".$catsort);

$aCategories = array();
if (os_db_num_rows($categories_query) > 0)
{
	while ($c = os_db_fetch_array($categories_query))
	{
		$aCategories[] = $c;
	}
}

if (@$_GET['search'])
	$products_query = os_db_query("SELECT p.products_tax_class_id, p.products_id, pd.products_name, p.products_sort, p.products_quantity, p.products_to_xml, p.products_image, p.products_price, p.products_discount_allowed, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_startpage, p.products_startpage_sort, p2c.categories_id FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c WHERE p.products_id = pd.products_id AND pd.language_id = '".$_SESSION['languages_id']."' AND p.products_id = p2c.products_id AND (pd.products_name like '%".$_GET['search']."%' OR p.products_model = '".$_GET['search']."') ORDER BY ".$prodsort);
else
	$products_query = os_db_query("SELECT p.products_tax_class_id, p.products_sort, p.products_id, pd.products_name, p.products_quantity, p.products_to_xml, p.products_image, p.products_price, p.products_discount_allowed, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_startpage, p.products_startpage_sort, p2c.categories_id FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c WHERE p.products_id = pd.products_id AND pd.language_id = '".(int)$_SESSION['languages_id']."' AND p.products_id = p2c.products_id AND p2c.categories_id = '".$current_category_id."' ORDER BY ".$prodsort);

$numr = os_db_num_rows($products_query);
$products_count = 0;

if (!isset($_GET['page'])){$page=0;} else { $page = $_GET['page']; };

$max_count = MAX_DISPLAY_ADMIN_PAGE;

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
$page = ($page-1)*MAX_DISPLAY_ADMIN_PAGE;

$products_shippingtime = $cartet->product->getShippingStatus();

if (@$_GET['search'])
	$products_query = os_db_query("SELECT p.products_tax_class_id,p.products_id,pd.products_name,p.products_sort,p.stock, p.products_quantity,p.products_to_xml,p.products_image,p.products_price,p.products_discount_allowed,p.products_date_added,p.products_last_modified,p.products_date_available,p.products_status,p.products_startpage,p.products_startpage_sort,p.products_shippingtime,p.products_model,p2c.categories_id FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c WHERE p.products_id = pd.products_id AND pd.language_id = '".$_SESSION['languages_id']."' AND p.products_id = p2c.products_id AND (pd.products_name like '%".$_GET['search']."%' OR p.products_model = '".$_GET['search']."') ORDER BY ".$prodsort." limit ".$page.",".$max_count);
else
	$products_query = os_db_query("SELECT p.products_tax_class_id,p.products_sort, p.products_id, p.stock, pd.products_name, p.products_quantity, p.products_to_xml,p.products_image, p.products_price, p.products_discount_allowed, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status,p.products_startpage,p.products_startpage_sort,p.products_shippingtime,p.products_model, p2c.categories_id FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c WHERE p.products_id = pd.products_id AND pd.language_id = '".(int)$_SESSION['languages_id']."' AND p.products_id = p2c.products_id AND p2c.categories_id = '".$current_category_id."' ORDER BY ".$prodsort." limit ".$page.",".$max_count);

$aProducts = array();
if (os_db_num_rows($products_query) > 0)
{
	while ($_p = os_db_fetch_array($products_query))
	{
		$aProducts[] = $_p;
	}
}

?>
	<thead>
		<tr>
			<th width="5%" class="tcenter"><?php echo TABLE_HEADING_EDIT; ?><input type="checkbox" class="selectAllCheckbox" onClick="javascript:SwitchCheck();"></th>
			<th width="25%"><span class="line"></span><?php echo TABLE_HEADING_CATEGORIES_PRODUCTS.os_sorting(FILENAME_CATEGORIES,'name'); ?></th>
			<th width="5%"><span class="line"></span><?php echo TABLE_HEADING_STATUS.os_sorting(FILENAME_CATEGORIES,'status'); ?></th>
			<th width="5%"><span class="line"></span><?php echo TABLE_HEADING_STARTPAGE.os_sorting(FILENAME_CATEGORIES,'startpage'); ?></th>
			<th width="5%"><span class="line"></span><?php echo TABLE_HEADING_STOCKS.os_sorting(FILENAME_CATEGORIES,'stocksort');?></th>
			<?php if (STOCK_CHECK == 'true') { ?><th width="5%"><span class="line"></span><?php echo TABLE_HEADING_STOCK.os_sorting(FILENAME_CATEGORIES,'stock'); ?></th><?php } ?>
			<th width="5%"><span class="line"></span><?php echo TABLE_HEADING_MENU; ?></th>
			<th width="5%"><span class="line"></span><?php echo TABLE_HEADING_XML.os_sorting(FILENAME_CATEGORIES,'yandex'); ?></th>
			<th width="10%"><span class="line"></span><?php echo TABLE_HEADING_PRICE.os_sorting(FILENAME_CATEGORIES,'price'); ?></th>
			<th width="5%"><span class="line"></span><?php echo TABLE_HEADING_SORT.os_sorting(FILENAME_CATEGORIES,'sort'); ?></th>
			<th width="10%"><span class="line"></span><?php echo TABLE_HEADING_SHIPPING_TIME; ?></th>
			<th width="10%"><span class="line"></span><?php echo TABLE_HEADING_MODEL; ?></th>
			<?php
			$array = array();
			$array['categories'] = $aCategories;
			$array['products'] = $aProducts;
			$array = apply_filter('table_products', $array);

			if (isset($array['values']) && is_array($array['values']) )
			{
				foreach ($array['values'] as $num => $value)
				{
					echo '<th><span class="line"></span>'.$value['name'].'</th>';
				}
			}
			?>
			<th width="5%" class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
	<tbody>

<?php

if (is_array($aCategories))
{
	foreach ($aCategories AS $categories)
	{
		$categories_count++;
		$rows++;

		if (@$_GET['search'])
			$cPath = @$categories['parent_id'];
		?>
		<tr class="categories_tr">
			<td class="tcenter"><input type="checkbox" name="multi_categories[]" value="<?php echo $categories['categories_id']; ?>" /></td>
			<td><input class="width90" type="text" name="categories[<?php echo $categories['categories_id']; ?>][categories_name]" value="<?php echo $categories['categories_name']; ?>" /></td>
			<td class="tcenter">
				<?php
					echo '<a '.(($categories['categories_status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$categories['categories_id'].'_0_categories_status" data-column="categories_status" data-action="products_changeCategoryStatus" data-id="'.$categories['categories_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($categories['categories_status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$categories['categories_id'].'_1_categories_status" data-column="categories_status" data-action="products_changeCategoryStatus" data-id="'.$categories['categories_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
			<td class="tcenter">--</td>
			<td class="tcenter">--</td>
			<?php if (STOCK_CHECK == 'true') { ?><td class="tcenter">--</td><?php } ?>
			<td class="tcenter">
				<?php
					echo '<a '.(($categories['menu'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$categories['categories_id'].'_0_menu" data-column="menu" data-action="products_changeCategoryStatus" data-id="'.$categories['categories_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($categories['menu'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$categories['categories_id'].'_1_menu" data-column="menu" data-action="products_changeCategoryStatus" data-id="'.$categories['categories_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
			<td class="tcenter">
				<?php
					echo '<a '.(($categories['yml_enable'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$categories['categories_id'].'_0_yml_enable" data-column="yml_enable" data-action="products_setCategoriesYmlStatus" data-id="'.$categories['categories_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($categories['yml_enable'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$categories['categories_id'].'_1_yml_enable" data-column="yml_enable" data-action="products_setCategoriesYmlStatus" data-id="'.$categories['categories_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
			</td>
			<td class="tcenter">--</td>
			<td class="tcenter"><input class="width40px tcenter" type="text" name="categories[<?php echo $categories['categories_id']; ?>][sort_order]" value="<?php echo $categories['sort_order']; ?>" /></td>
			<td class="tcenter">--</td>
			<td class="tcenter">--</td>
			<?php if (isset($array['values']) && is_array($array['values']))
			{
				foreach ($array['values'] as $num => $value)
				{
					echo $value['content_categories'][$categories['categories_id']];
				}
			} ?>
			<td><div class="btn-group pull-right">
			<?php
			echo '<a class="btn btn-mini" href="'.os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')).os_get_path($categories['categories_id'])) .'"><i class="icon-folder-open"></i></a>';
			echo '<a class="btn btn-mini" href="'.os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')).'cPath='.$cPath.'&cID='.$categories['categories_id']."&action=edit_category").'" title="'.BUTTON_EDIT.'"><i class="icon-edit"></i></a> ';
			?>
		</div></td>
		</tr>
		<?php
	}
}

if (is_array($aProducts))
{
	foreach ($aProducts AS $products)
	{
		$products_count++;
		$rows++;

		if (@$_GET['search'])
			$cPath=$products['categories_id'];
		?>
		<tr class="products_tr">
			<td class="tcenter"><input type="checkbox" name="multi_products[]" value="<?php echo @$products['products_id']; ?>" /></td>
			<td><input class="width90" type="text" name="products[<?php echo $products['products_id']; ?>][products_name]" value="<?php echo html($products['products_name']); ?>" /></td>
			<td class="tcenter">
				<?php
					echo '<a '.(($products['products_status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$products['products_id'].'_0_products_status" data-column="products_status" data-action="products_changeProductStatus" data-id="'.$products['products_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($products['products_status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$products['products_id'].'_1_products_status" data-column="products_status" data-action="products_changeProductStatus" data-id="'.$products['products_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
			<td class="tcenter">
				<?php
					echo '<a '.(($products['products_startpage'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$products['products_id'].'_0_products_startpage" data-column="products_startpage" data-action="products_changeProductStatus" data-id="'.$products['products_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($products['products_startpage'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$products['products_id'].'_1_products_startpage" data-column="products_startpage" data-action="products_changeProductStatus" data-id="'.$products['products_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
			<td class="tcenter">
				<?php
					echo '<a '.(($products['stock'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$products['products_id'].'_0_stock" data-column="stock" data-action="products_changeProductStatus" data-id="'.$products['products_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($products['stock'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$products['products_id'].'_1_stock" data-column="stock" data-action="products_changeProductStatus" data-id="'.$products['products_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
			<?php if (STOCK_CHECK == 'true') {
				$bgStock = ($products['products_quantity'] <= 0) ? 'bg-stock-warn' : '';
			?>
			<td class="tcenter"><input class="width40px tcenter <?php echo $bgStock; ?>" type="text" name="products[<?php echo $products['products_id']; ?>][products_quantity]" value="<?php echo $products['products_quantity']; ?>" /></td>
			<?php } ?>
			<td class="tcenter">--</td>
			<td class="tcenter">
				<?php
					echo '<a '.(($products['products_to_xml'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$products['products_id'].'_0_products_to_xml" data-column="products_to_xml" data-action="products_changeProductStatus" data-id="'.$products['products_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
					echo '<a '.(($products['products_to_xml'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$products['products_id'].'_1_products_to_xml" data-column="products_to_xml" data-action="products_changeProductStatus" data-id="'.$products['products_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
				?>
			</td>
			<td class="tcenter">
				<?php
				if (PRICE_IS_BRUTTO == 'true')
					$products_price = os_round($products['products_price'] * ((100 + os_get_tax_rate($products['products_tax_class_id'])) / 100), PRICE_PRECISION);
				else
					$products_price = $products['products_price'];
				?>
				<input class="width100px" type="text" name="products[<?php echo $products['products_id']; ?>][products_price]" value="<?php echo $products_price; ?>" />
			</td>
			<td class="tcenter">
			<?php 
			if ($current_category_id == 0)
				echo '<input class="width40px tcenter" type="text" name="products['.$products['products_id'].'][products_startpage_sort]" value="'.$products['products_startpage_sort'].'" />';
			else
				echo '<input class="width40px tcenter" type="text" name="products['.$products['products_id'].'][products_sort]" value="'.$products['products_sort'].'" />';
			?>
			</td>
			<td class="tcenter">
				<select class="width100px" name="products[<?php echo $products['products_id']; ?>][products_shippingtime]">
					<?php
						if (is_array($products_shippingtime))
						{
							foreach($products_shippingtime AS $id => $text)
							{
								$selected = ($products['products_shippingtime'] == $id) ? 'selected' : '';
								echo '<option value="'.$id.'" '.$selected.'>'.$text.'</option>';
							}
						}
					?>
				</select>
			</td>
			<td class="tcenter"><input class="width100px" type="text" name="products[<?php echo $products['products_id']; ?>][products_model]" value="<?php echo html($products['products_model']); ?>" /></td>
			<?php if (isset($array['values']) && is_array($array['values']))
			{
				foreach ($array['values'] as $num => $value)
				{
					echo $value['content_products'][$products['products_id']];
				}
			} ?>
			<td>
				<div class="btn-group pull-right">
					<a class="btn btn-mini" href="<?php echo os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')).'cPath='.$cPath.'&pID='.$products['products_id']); ?>&action=new_product" title="<?php echo BUTTON_EDIT; ?>"><i class="icon-edit"></i></a>
					<a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a class="" href="<?php echo os_href_link(FILENAME_NEW_ATTRIBUTES.'?action=edit&current_product_id='.$products['products_id'].'&cpath='.$cPath); ?>" title="<?php echo BUTTON_EDIT_ATTRIBUTES; ?>"><i class="icon-tasks"></i> <?php echo BUTTON_EDIT_ATTRIBUTES; ?></a></li>
						<li><a class="" href="<?php echo os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('cPath', 'action', 'pID', 'cID')).'action=edit_crossselling&current_product_id='.$products['products_id'].'&cpath='.$cPath); ?>" title="<?php echo BUTTON_EDIT_CROSS_SELLING; ?>"><i class="icon-th-large"></i> <?php echo BUTTON_EDIT_CROSS_SELLING; ?></a></li>
						<li class="divider"></li>
						<li><a class="ajax-action" data-reload-page="1" data-action="products_duplicateProduct_get&product_id=<?php echo $products['products_id']; ?>&categories_id=<?php echo $cPath; ?>" href="javascript:;"  title="<?php echo BUTTON_COPY; ?>"><i class="icon-copy"></i> <?php echo BUTTON_COPY; ?></a></li>
					</ul>
				</div>
			</td>
		</tr>
	<?php } ?>
<?php } ?>
	<tbody>
</table>

<div class="table-message-info"><?php echo TEXT_CATEGORIES.' '.$categories_count.' '.TEXT_PRODUCTS.' '.$products_count; ?></div>

<div class="action-table">
	<div class="btn-group pull-left">
		<input class="btn btn-mini btn-success ajax-save-form" data-form-action="products_saveList" type="submit" value="<?php echo SAVE_ALL; ?>">
		<input class="btn btn-mini btn-disabled ajax-load-page" data-load-page="products&action=multi_move&cPath=<?php echo $cPath; ?>" data-toggle="modal" type="submit" name="multi_move" value="<?php echo BUTTON_MOVE; ?>">
		<input class="btn btn-mini btn-disabled ajax-load-page" data-load-page="products&action=multi_copy&cPath=<?php echo $cPath; ?>" data-toggle="modal" type="submit" name="multi_copy" value="<?php echo BUTTON_COPY; ?>">
		<input class="btn btn-mini btn-disabled" type="submit" name="multi_status_on" value="<?php echo BUTTON_STATUS_ON; ?>">
		<input class="btn btn-mini btn-disabled" type="submit" name="multi_status_off" value="<?php echo BUTTON_STATUS_OFF; ?>">
		<input class="btn btn-mini btn-danger btn-disabled ajax-load-page" data-load-page="products&action=multi_delete&cPath=<?php echo $cPath; ?>" data-toggle="modal" type="submit" name="multi_delete" value="<?php echo BUTTON_DELETE; ?>">
		<a class="btn btn-mini selectAllCheckboxProducts" data-toggle="button" href="javascript:SwitchProducts()"><?php echo BUTTON_SWITCH_PRODUCTS; ?></a>
		<a class="btn btn-mini selectAllCheckboxCategories" data-toggle="button" href="javascript:SwitchCategories()"><?php echo BUTTON_SWITCH_CATEGORIES; ?></a>
	</div>

	<div class="pull-right">
		<div class="pagination pagination-mini pagination-right">
			<ul>
			<?php
			if ($numr > $max_count)
			{
				$_param = array(
					'file_name' => FILENAME_CATEGORIES,
					'page_name' => 'page',
					'param' => array('cPath' => $cPath)
				);

				if (isset($_GET['search'])) $_param['param']['search'] = $_GET['search'];

				echo osc_pages_menu($numr, $max_count, $_GET['page'], $_param);
			}
			?>
			</ul>
		</div>
	</div>
	<div class="clear"></div>
</div>

</form>