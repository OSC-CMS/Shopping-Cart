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

require(_CLASS.'price.php');
$osPrice = new osPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);

$breadcrumb->add(HEADING_TITLE, FILENAME_SPECIALS);

$main->head();
$main->top_menu();
?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span8">
			<div class="btn-group">
				<a class="btn btn-mini <?php echo (!isset($_GET['act'])) ? 'btn-info' : ''; ?>" href="<?php echo os_href_link(FILENAME_SPECIALS); ?>"><?php echo TABLE_HEADING_PRODUCTS; ?></a>
				<a class="btn btn-mini <?php echo (isset($_GET['act']) && $_GET['act'] == 'categories') ? 'btn-info' : ''; ?>" href="<?php echo os_href_link(FILENAME_SPECIALS, 'act=categories'); ?>"><?php echo TABLE_HEADING_CATEGORIES; ?></a>
			</div>
		</div>
		<div class="span4">
			<div class="btn-group pull-right">
				<?php if (isset($_GET['act']) && $_GET['act'] == 'categories') { ?>
				<?php echo $cartet->html->link(
					BUTTON_NEW_CATEGORIES,
					os_href_link(FILENAME_SPECIALS, 'action=new'),
					array(
						'class' => 'ajax-load-page btn btn-mini btn-info',
						'data-load-page' => 'specials_category&action=new',
						'data-toggle' => 'modal',
						)
				); ?>					
				<? } else { ?>
				<?php echo $cartet->html->link(
					BUTTON_NEW_PRODUCTS,
					os_href_link(FILENAME_SPECIALS, 'action=new'),
					array(
						'class' => 'ajax-load-page btn btn-mini btn-info',
						'data-load-page' => 'specials&action=new',
						'data-toggle' => 'modal',
						)
				); ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php if (isset($_GET['act']) && $_GET['act'] == 'categories') { ?>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_CATEGORIES; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_SPECIAL_PRODUCT; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_CATEGORY_DISCOUNT; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_DATE_ADDED; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_EXPIRES_DATE; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
				<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
	<?php
	$specials_query_raw = "select A.special_id, A.categ_id, A.discount, A.discount_type, A.status, B.categories_name, A.special_date_added, A.special_last_modified, A.expire_date,
	A.date_status_change from ". TABLE_SPECIAL_CATEGORY. " A, ".TABLE_CATEGORIES_DESCRIPTION." B where A.categ_id = B.categories_id and 
	B.language_id = '".(int)$_SESSION['languages_id']."' order by B.categories_name";
	$specials_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $specials_query_raw, $specials_query_numrows);
	$specials_query = os_db_query($specials_query_raw);

	while ($specials = os_db_fetch_array($specials_query))
	{
		$query = "select count(*) as cnt from ".TABLE_SPECIAL_PRODUCT." where special_id = ".$specials['special_id'];
		$prod_count = os_db_query($query);
		$prod_count = os_db_fetch_array($prod_count);
		$special_product = $prod_count['cnt'];
		$query = "select count(A.products_id) as cnt from ". TABLE_PRODUCTS_TO_CATEGORIES. " A, ". TABLE_SPECIAL_CATEGORY. " B where A.categories_id = B.categ_id and B.special_id = ". $specials['special_id'];
		$prod_count = os_db_query($query);
		$prod_count = os_db_fetch_array($prod_count);
		$total_product = $prod_count['cnt'];
		?>
		<tr>
			<td><?php echo $specials['categories_name']; ?></td>
			<td class="tcenter"><?php echo $special_product." / ".$total_product; ?></td>
			<td class="tcenter"><?php echo $specials['discount_type'] == "f" ? "$" : "%"; ?> <?php echo sprintf("%0.2f", $specials['discount']); ?></td>
			<td class="tcenter">
				<?php echo $specials['special_date_added']; ?>
				<?php if ($specials['special_last_modified']) { ?>
				<i class="icon-edit tt" title="<?php echo TEXT_INFO_LAST_MODIFIED; ?>: <?php echo $specials['special_last_modified']; ?>"></i>
				<?php } ?>
			</td>
			<td class="tcenter">
				<?php echo $specials['expire_date']; ?>
				<?php if ($specials['date_status_change']) { ?>
				<i class="icon-edit tt" title="<?php echo TEXT_INFO_LAST_MODIFIED; ?>: <?php echo $specials['date_status_change']; ?>"></i>
				<?php } ?>
			</td>
			<td class="tcenter">
			<?php
				echo '<a '.(($specials['status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$specials['special_id'].'_0_status" data-column="status" data-action="specials_statusCategories" data-id="'.$specials['special_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
				echo '<a '.(($specials['status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$specials['special_id'].'_1_status" data-column="status" data-action="specials_statusCategories" data-id="'.$specials['special_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
			?>
			</td>
			<td width="100">
				<div class="btn-group pull-right">
					<?php echo $cartet->html->link(
						'<i class="icon-edit"></i>',
						os_href_link(FILENAME_SPECIALS, 'cID='.$specials['special_id'].'&action=edit'),
						array(
							'class' => 'ajax-load-page btn btn-mini',
							'data-load-page' => 'specials_category&c_id='.$specials['special_id'].'&action=edit',
							'data-toggle' => 'modal',
							'title' => BUTTON_EDIT,
						)
					); ?>
					<?php echo $cartet->html->link(
						'<i class="icon-trash"></i>',
						os_href_link(FILENAME_SPECIALS, 'cID='.$specials['special_id'].'&action=delete'),
						array(
							'class' => 'ajax-load-page btn btn-mini',
							'data-load-page' => 'specials_category&c_id='.$specials['special_id'].'&action=delete',
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
	<div class="action-table">

		<div class="pull-right">
			<div class="pagination pagination-mini pagination-right">
				<?php echo $specials_split->display_count($specials_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?>
				<?php echo $specials_split->display_links($specials_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>

<?php } else { ?>

	<table class="table table-condensed table-big-list">
		<thead>
			<tr>
				<th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_NEW_PRICE; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_ORIGINAL_PRICE; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_PERCENTAGE; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_DATE_ADDED; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TEXT_INFO_EXPIRES_DATE; ?></th>
				<th class="tcenter"><span class="line"></span><?php echo TABLE_HEADING_STATUS; ?></th>
				<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
			</tr>
		</thead>
	<?php
	$specials_query_raw = "select p.products_id, pd.products_name,p.products_tax_class_id, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from ".TABLE_PRODUCTS." p, ".TABLE_SPECIALS." s, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = pd.products_id and pd.language_id = '".$_SESSION['languages_id']."' and p.products_id = s.products_id order by pd.products_name";
	$specials_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $specials_query_raw, $specials_query_numrows);
	$specials_query = os_db_query($specials_query_raw);
	while ($specials = os_db_fetch_array($specials_query))
	{
		$price = $specials['products_price'];
		$new_price = $specials['specials_new_products_price'];
		// налог
		if (PRICE_IS_BRUTTO =='true')
		{
			//$price_netto = os_round($price,PRICE_PRECISION);
			//$new_price_netto = os_round($new_price,PRICE_PRECISION);
			$price = ($price*(os_get_tax_rate($specials['products_tax_class_id'])+100)/100);
			$new_price = ($new_price*(os_get_tax_rate($specials['products_tax_class_id'])+100)/100);
		}
		$specials['products_price'] = os_round($price,PRICE_PRECISION);
		$specials['specials_new_products_price'] = os_round($new_price,PRICE_PRECISION);

		?>
		<tr>
			<td><?php echo $specials['products_name']; ?></td>
			<td class="tcenter"><?php echo $osPrice->Format($specials['specials_new_products_price'], true); ?></td>
			<td class="tcenter"><?php echo $osPrice->Format($specials['products_price'], true); ?></td>
			<td class="tcenter"><?php echo number_format(100 - (($specials['specials_new_products_price'] / $specials['products_price']) * 100)); ?>%</td>
			<td class="tcenter">
				<?php echo $specials['specials_date_added']; ?>
				<?php if ($specials['specials_last_modified']) { ?>
				<i class="icon-edit tt" title="<?php echo TEXT_INFO_LAST_MODIFIED; ?>: <?php echo $specials['specials_last_modified']; ?>"></i>
				<?php } ?>
			</td>
			<td class="tcenter">
				<?php echo $specials['expires_date']; ?>
				<?php if ($specials['date_status_change']) { ?>
				<i class="icon-edit tt" title="<?php echo TEXT_INFO_LAST_MODIFIED; ?>: <?php echo $specials['date_status_change']; ?>"></i>
				<?php } ?>
			</td>
			<td class="tcenter">
			<?php
				echo '<a '.(($specials['status'] == 1) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$specials['specials_id'].'_0_status" data-column="status" data-action="specials_status" data-id="'.$specials['specials_id'].'" data-status="0" data-show-status="1" title="'.IMAGE_ICON_STATUS_RED_LIGHT.'"><i class="icon-ok"></i></a>';
				echo '<a '.(($specials['status'] == 0) ? '' : 'style="display:none;"').' href="javascript:;" class="ajax-change-status status_'.$specials['specials_id'].'_1_status" data-column="status" data-action="specials_status" data-id="'.$specials['specials_id'].'" data-status="1" data-show-status="0" title="'.IMAGE_ICON_STATUS_GREEN_LIGHT.'"><i class="icon-remove"></i></a>';
			?>
			</td>
			<td width="100">
				<div class="btn-group pull-right">
					<?php echo $cartet->html->link(
						'<i class="icon-edit"></i>',
						os_href_link(FILENAME_SPECIALS, 'sID='.$specials['specials_id'].'&action=edit'),
						array(
							'class' => 'ajax-load-page btn btn-mini',
							'data-load-page' => 'specials&s_id='.$specials['specials_id'].'&action=edit',
							'data-toggle' => 'modal',
							'title' => BUTTON_EDIT,
						)
					); ?>
					<?php echo $cartet->html->link(
						'<i class="icon-trash"></i>',
						os_href_link(FILENAME_SPECIALS, 'sID='.$specials['specials_id'].'&action=delete'),
						array(
							'class' => 'ajax-load-page btn btn-mini',
							'data-load-page' => 'specials&s_id='.$specials['specials_id'].'&action=delete',
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

	<div class="action-table">

		<div class="pull-right">
			<div class="pagination pagination-mini pagination-right">
				<?php echo $specials_split->display_count($specials_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?>
				<?php echo $specials_split->display_links($specials_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>

<?php } ?>

<?php $main->bottom(); ?>