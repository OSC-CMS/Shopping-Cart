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

$breadcrumb->add(HEADING_TITLE);

$main->head();
$main->top_menu();
?>

<div class="second-page-nav">
	<div class="row-fluid">
		<div class="span8"></div>
		<div class="span4">
			<div class="pull-right">
				<?php echo $cartet->html->link(
					BUTTON_INSERT,
					'',
					array(
						'class' => 'ajax-load-page btn btn-mini btn-info',
						'data-load-page' => 'manufacturers&action=new',
						'data-toggle' => 'modal',
						)
				); ?>
			</div>
		</div>
	</div>
</div>

<table class="table table-condensed table-big-list">
	<thead>
		<tr>
			<th colspan="2"><?php echo TABLE_HEADING_MANUFACTURERS; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TEXT_PRODUCTS; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TEXT_DATE_ADDED; ?></th>
			<th class="tcenter"><span class="line"></span><?php echo TEXT_LAST_MODIFIED; ?></th>
			<th class="tright"><span class="line"></span><?php echo TABLE_HEADING_ACTION; ?></th>
		</tr>
	</thead>
	<?php
	$manufacturers_query_raw = "
		SELECT 
			m.*, 
			(SELECT count(p.products_id) FROM ".TABLE_PRODUCTS." p WHERE manufacturers_name = m.manufacturers_name) as products_count 
		FROM 
			".TABLE_MANUFACTURERS." m 
		ORDER BY 
			m.manufacturers_id DESC
		";

	//$manufacturers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $manufacturers_query_raw, $manufacturers_query_numrows);
	$manufacturers_query = os_db_query($manufacturers_query_raw);
	while ($manufacturers = os_db_fetch_array($manufacturers_query))
	{
		//$manufacturer_products_query = os_db_query("SELECT count(*) as products_count from ".TABLE_PRODUCTS." where manufacturers_id = '".$manufacturers['manufacturers_id']."'");
		//$manufacturer_products = os_db_fetch_array($manufacturer_products_query);

		//echo os_info_image($manufacturers['manufacturers_image'], $manufacturers['manufacturers_name']);
		if (!empty($manufacturers['manufacturers_image']))
			$img = ' <i class="icon-camera"></i>';
		else
			$img = '';
	?>
	<tr>
		<td class="tcenter" width="20"><?php echo $img; ?></td>
		<td><?php echo $manufacturers['manufacturers_name']; ?></td>
		<td class="tcenter"><?php echo $manufacturers['products_count']; ?></td>
		<td class="tcenter"><?php echo os_date_short($manufacturers['date_added']); ?></td>
		<td class="tcenter"><?php echo os_date_short($manufacturers['last_modified']); ?></td>
		<td width="100">
			<div class="btn-group pull-right">
				<?php echo $cartet->html->link(
					'<i class="icon-edit"></i>',
					os_href_link(FILENAME_MANUFACTURERS, 'page='.$_GET['page'].'&mID='.$manufacturers['manufacturers_id'].'&action=edit'),
					array(
						'class' => 'ajax-load-page btn btn-mini',
						'data-load-page' => 'manufacturers&m_id='.$manufacturers['manufacturers_id'].'&action=edit',
						'data-toggle' => 'modal',
						'title' => BUTTON_EDIT,
					)
				); ?>
				<?php echo $cartet->html->link(
					'<i class="icon-trash"></i>',
					os_href_link(FILENAME_MANUFACTURERS, 'page='.$_GET['page'].'&mID='.$manufacturers['manufacturers_id'].'&action=delete'),
					array(
						'class' => 'ajax-load-page btn btn-mini',
						'data-load-page' => 'manufacturers&m_id='.$manufacturers['manufacturers_id'].'&action=delete',
						'data-toggle' => 'modal',
						'title' => BUTTON_DELETE,
					)
				); ?>
			</div>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="6">
			<?php //echo $manufacturers_split->display_count($manufacturers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS); ?>

			<?php //echo $manufacturers_split->display_links($manufacturers_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
		</td>
	</tr>
</table>

<?php $main->bottom(); ?>
