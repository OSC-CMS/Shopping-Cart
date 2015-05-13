<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require ('includes/top.php');

$main->head();
$main->top_menu();

$system_error = 1;
$file_warning = '';
$folder_warning = '';
$installed_payment = '';
$installed_shipping = '';
include(get_path('modules_admin') . FILENAME_SECURITY_CHECK);

$getSettingGroup = $cartet->admin->getSettingGroup('index2');
?>

<!-- <div class="page-header-big">
	<div class="page-header-big-inner">
		<h1><?php echo HEADING_TITLE; ?></h1>
		<p></p>
	</div>
</div> -->

<div class="page-content">
	<?php if ($system_error == 1) { ?>
		<h5><?php echo MENU_SYSTEM_ERRORS; ?></h5>
		<?php include(get_path('page_admin').'index/warning.php'); ?>
	<?php } ?>

	<div class="row-fluid">
		<div class="span6">
			<div class="index2_orders" style="display:<?php echo ($getSettingGroup['orders']['setting'] == 1) ? 'block' : 'none'; ?>;">
				<div class="well well-box well-nice">
					<div class="navbar">
						<div class="navbar-inner">
							<h4 class="title"><?php echo TEXT_SUMMARY_ORDERS; ?></h4>
							<div class="well-right-btn">
								<a class="btn btn-success btn-mini pull-right" href="<?php echo os_href_link(FILENAME_ORDERS, '', 'NONSSL'); ?>"><?php echo TABLE_HEADING_ORDERS; ?></a>
							</div>
						</div>
					</div>
					<div class="well-box-content well-max-height well-small-font">
						<?php include(get_path('page_admin').'index/orders.php'); ?>
					</div>
				</div>
			</div>

			<div class="index2_month_stats" style="display:<?php echo ($getSettingGroup['month_stats']['setting'] == 1) ? 'block' : 'none'; ?>;">
				<div class="well well-box well-nice">
					<div class="navbar">
						<div class="navbar-inner">
							<h4 class="title"><?php echo BOX_HEADING_ORDER_STATISTICS; ?></h4>
						</div>
					</div>
					<div class="well-box-content p10">
						<?php include(get_path('page_admin').'index/flot_statistics.php'); ?>
					</div>
				</div>
			</div>

			<div class="index2_reviews" style="display:<?php echo ($getSettingGroup['reviews']['setting'] == 1) ? 'block' : 'none'; ?>;">
				<div class="well well-box well-nice">
					<div class="navbar">
						<div class="navbar-inner">
							<h4 class="title"><?php echo TEXT_SUMMARY_REVIEWS; ?></h4>
							<div class="well-right-btn">
								<a class="btn btn-success btn-mini pull-right" href="<?php echo os_href_link(FILENAME_REVIEWS, '', 'NONSSL'); ?>"><?php echo TEXT_SUMMARY_REVIEWS_ALL; ?></a>
							</div>
						</div>
					</div>
					<div class="well-box-content well-max-height well-small-font">
						<?php include(get_path('page_admin').'index/reviews.php'); ?>
					</div>
				</div>
			</div>

			<div class="index2_products" style="display:<?php echo ($getSettingGroup['products']['setting'] == 1) ? 'block' : 'none'; ?>;">
				<div class="well well-box well-nice">
					<div class="navbar">
					    <div class="navbar-inner">
					        <h4 class="title"><?php echo TEXT_SUMMARY_PRODUCTS; ?></h4>
					    </div>
					</div>
					<div class="well-box-content well-max-height well-small-font">
						<?php include(get_path('page_admin').'index/products.php'); ?>
					</div>
				</div>
			</div>

		</div>
		<div class="span6">

			<div class="index2_stats" style="display:<?php echo ($getSettingGroup['stats']['setting'] == 1) ? 'block' : 'none'; ?>;">
				<div class="well well-box well-nice">
					<div class="navbar">
					    <div class="navbar-inner">
					        <h4 class="title"><?php echo BOX_HEADING_STATISTICS; ?></h4>
					    </div>
					</div>
					<div class="well-box-content well-max-height well-small-font">
						<?php include(get_path('page_admin').'index/order_status.php'); ?>
					</div>
				</div>
			</div>

			<div class="index2_customers" style="display:<?php echo ($getSettingGroup['customers']['setting'] == 1) ? 'block' : 'none'; ?>;">
				<div class="well well-box well-nice">
					<div class="navbar">
						<div class="navbar-inner">
							<h4 class="title"><?php echo TEXT_SUMMARY_CUSTOMERS; ?></h4>
						</div>
					</div>
					<div class="well-box-content well-max-height well-small-font">
						<?php include(get_path('page_admin').'index/customers.php'); ?>
					</div>
				</div>
			</div>

			<div class="index2_notes" style="display:<?php echo ($getSettingGroup['notes']['setting'] == 1) ? 'block' : 'none'; ?>;">
				<?php include(get_path('page_admin').'index/notes.php'); ?>
			</div>
			<div class="index2_cache" style="display:<?php echo ($getSettingGroup['cache']['setting'] == 1) ? 'block' : 'none'; ?>;">
				<?php include(get_path('page_admin').'index/cache.php'); ?>
			</div>
		</div>
	</div>

</div>

<?php $main->bottom(); ?>