<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

  $report = isset($_GET['report']) ? $_GET['report'] : null;
  $report_type = isset($_GET['report_type']) ? $_GET['report_type'] : null;
  require(dirname(dirname(dirname(dirname(__FILE__)))).'/admin/includes/top.php');

				switch ($report_type) {
					case 'orders' :

  require(_LANG_ADMIN.$_SESSION['language'].FILENAME_STATS_SALES_REPORT2);

  require(dir_path('class') . 'currencies.php');
  $currencies = new currencies();
  $sales_report_default_view = 2;
  $sales_report_view = $sales_report_default_view;
  if ( ($_GET['report']) && (os_not_null($_GET['report'])) ) {
    $sales_report_view = $_GET['report'];
  }
  if ($sales_report_view > 5) {
    $sales_report_view = $sales_report_default_view;
  }

  if ($sales_report_view == 2) {
    $report = 2;
  }

    $report_desc = REPORT_TYPE_MONTHLY;

  require(dir_path('class') . 'sales_report2.php');
  $report = new sales_report(4, $startDate, $endDate, $sales_report_filter);

include_once(dir_path('class') . 'ofc-library/open-flash-chart.php');

$data_count = array();
$data_sum = array();
for ($i = 0; $i < $report->size; $i++) { 

$data_count[] = $report->info[$i]['count'];
$data_sum[] = number_format($report->info[$i]['sum'],0,'','');
									
}

$data_date = array();
for ($i = 0; $i < $report->size; $i++) { 

$data_date[] = $report->info[$i]['text'];
									
}

$g = new graph();
$g->bg_colour = '0xFFFFFF';
$g->x_grid_colour = '0xd8d8d8';
$g->y_grid_colour = '0xd8d8d8';

$g->title( HEADING_TITLE . ': ' . $report_desc, '{font-size: 18px;}' );

$g->set_data( $data_sum );
$g->bar( 60, '#ff9900', TEXT_TOTAL_SUMM, 12 );

$g->set_data( $data_count );
$g->line_hollow( 3, 4, '#0077cc', TEXT_NUMBER_OF_ORDERS, 12 );

$g->attach_to_y_right_axis(2);

$g->set_x_labels( $data_date );
$g->set_x_label_style( 10, '0x000000', 0, 2 );
$g->set_y_max( (max($data_sum) / 10) + max($data_sum) );
$g->set_y_right_max( (max($data_count) / 10) + max($data_count) );
$g->y_label_steps( 4 );
echo $g->render();

						break;
					default :

  require(_LANG_ADMIN.$_SESSION['language'].'/'.FILENAME_STATS_SALES_REPORT2);
  require(_CLASS_ADMIN . 'currencies.php');
  $currencies = new currencies();

  $sales_report_default_view = 2;
  $sales_report_view = $sales_report_default_view;
  if ( ($_GET['report']) && (os_not_null($_GET['report'])) ) {
    $sales_report_view = $_GET['report'];
  }
  if ($sales_report_view > 5) {
    $sales_report_view = $sales_report_default_view;
  }

  if ($sales_report_view == 2) {
    $report = 2;
  }

  if ($report == 1) {
    $report_desc = REPORT_TYPE_HOURLY;
  } else if ($report == 2) {
    $report_desc = REPORT_TYPE_DAILY;
  } else if ($report == 3) {
    $report_desc = REPORT_TYPE_WEEKLY;
  } else if ($report == 4) {
    $report_desc = REPORT_TYPE_MONTHLY;
  } else if ($report == 5) {
    $report_desc = REPORT_TYPE_YEARLY;
  }

  $startDate = "";
  if ( ($_GET['startDate']) && (os_not_null($_GET['startDate'])) ) {
    $startDate = $_GET['startDate'];
  }
  $endDate = "";
  if ( ($_GET['endDate']) && (os_not_null($_GET['endDate'])) ) {
    $endDate = $_GET['endDate'];
  }

  if (($_GET['filter']) && (os_not_null($_GET['filter']))) {
    $sales_report_filter = $_GET['filter'];
    $sales_report_filter_link = "&filter=$sales_report_filter";
  }

  require(_CLASS_ADMIN . 'sales_report2.php');
  $report = new sales_report($sales_report_view, $startDate, $endDate, $sales_report_filter);

  if (strlen($sales_report_filter) == 0) {
    $sales_report_filter = $report->filter;
    $sales_report_filter_link = "";
  }

$data_count = array();
$data_avg = array();
$data_sum = array();
for ($i = 0; $i < $report->size; $i++) { 

$data_count[] = $report->info[$i]['count'];
$data_sum[] = number_format($report->info[$i]['sum'],0,'','');
									
}

$data_date = array();
for ($i = 0; $i < $report->size; $i++) { 

$data_date[] = $report->info[$i]['text'];
									
}

include_once(_LIB.'flash_chart/flash_chart.php');
$g = new graph();
$g->bg_colour = '0xFFFFFF';
$g->x_grid_colour = '0xd8d8d8';
$g->y_grid_colour = '0xd8d8d8';

$g->title( HEADING_TITLE . ': ' . $report_desc, '{font-size: 18px;}' );

$g->set_data( $data_count );
$g->line_hollow( 3, 4, '0x0077cc', TEXT_NUMBER_OF_ORDERS, 12 );

$g->set_data( $data_sum );
$g->line_dot( 3, 4, '0xff9900', TEXT_TOTAL_SUMM, 12 );

$g->attach_to_y_right_axis(2);
$g->set_x_labels( $data_date );
$g->set_x_label_style( 10, '0x000000', 0, 2 );
$g->set_y_max( (max($data_count) / 10) + max($data_count) );
$g->set_y_right_max( (max($data_sum) / 10) + max($data_sum) );
$g->y_label_steps( 4 );

echo $g->render();

						break;


}

?>