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
  
  add_action('head_admin', 'head_tabs');
  
  require(get_path('class_admin') . 'currencies.php');
  $currencies = new currencies();
  $sales_report_default_view = 2;
  $sales_report_view = $sales_report_default_view;
  if ( ($_GET['report']) && (os_not_null($_GET['report'])) ) {
    $sales_report_view = $_GET['report'];
  }
  if ($sales_report_view > 5) {
    $sales_report_view = $sales_report_default_view;
  }
	else {
    $report = $sales_report_view;
  }

  switch( $report ) {
  	case 1:
    	$summary1 = AVERAGE_HOURLY_TOTAL;
    	$summary2 = TODAY_TO_DATE;
    	$report_desc = REPORT_TYPE_HOURLY;
    	break;
    	
  	case 2:
    	$summary1 = AVERAGE_DAILY_TOTAL;
    	$summary2 = WEEK_TO_DATE;
    	$report_desc = REPORT_TYPE_DAILY;
    	break;
    	
  	case 3:
     $summary1 = AVERAGE_WEEKLY_TOTAL;
     $summary2 = MONTH_TO_DATE;
     $report_desc = REPORT_TYPE_WEEKLY;
     break;
     
 	 case 4:
  	$summary1 = AVERAGE_MONTHLY_TOTAL;
    $summary2 = YEAR_TO_DATE;
    $report_desc = REPORT_TYPE_MONTHLY;
    break;
    
  	case 5:
    	$summary1 = AVERAGE_YEARLY_TOTAL;
    	$summary2 = YEARLY_TOTAL;
    	$report_desc = REPORT_TYPE_YEARLY;
    	break;
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

  require(get_path('class_admin') . 'sales_report2.php');
  $report = new sales_report($sales_report_view, $startDate, $endDate, $sales_report_filter);

  if (strlen($sales_report_filter) == 0) {
    $sales_report_filter = $report->filter;
    $sales_report_filter_link = "";
  }

?>
<?php $main->head();?>
<?php $main->top_menu();?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php echo HEADING_TITLE; ?> 

			<table border="0" width="100%" cellspacing="0" cellpadding="2">
			    <tr>
					<td colspan=2>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
<td align="right">
<?php
  echo '<a href="' . os_href_link(FILENAME_STATS_SALES_REPORT2, 'report=1' . $sales_report_filter_link, 'NONSSL') . '">' . REPORT_TYPE_HOURLY .'</a> | ';
  echo '<a href="' . os_href_link(FILENAME_STATS_SALES_REPORT2, 'report=2' . $sales_report_filter_link, 'NONSSL') . '">' . REPORT_TYPE_DAILY .'</a> | ';
  echo '<a href="' . os_href_link(FILENAME_STATS_SALES_REPORT2, 'report=3' . $sales_report_filter_link, 'NONSSL') . '">' . REPORT_TYPE_WEEKLY . '</a> | ';
  echo '<a href="' . os_href_link(FILENAME_STATS_SALES_REPORT2, 'report=4' . $sales_report_filter_link, 'NONSSL') . '">' . REPORT_TYPE_MONTHLY . '</a> | ';
  echo '<a href="' . os_href_link(FILENAME_STATS_SALES_REPORT2, 'report=5' . $sales_report_filter_link, 'NONSSL') . '">' . REPORT_TYPE_YEARLY . '</a>';
?>
</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			
			<div id="tabs">
			<ul>
				<li><a href="#tab_chart_1"><?php echo TAB_CHART; ?></a></li>
				<li><a href="#tab_table"><?php echo TAB_TABLE; ?></a></li>
				<li><a href="#tab_status"><?php echo TAB_STATUS; ?></a></li>
			</ul>
			<div id="tab_chart_1">
			<table border="0" width="95%" height="100%" cellspacing="0" cellpadding="0">
			    <tr>
					<td valign="top" width="100%" align="center">
					
<?php
include(get_path('lib').'flash_chart/flash_chart_object.php');
open_flash_chart_object( '100%', 250, os_href_link('chart_data.php', os_get_all_get_params(), 'NONSSL'), false );
?>
					</td>
				</tr>
<?php
  if (strlen($report->previous . " " . $report->next) > 1) {
?>
										<tr>
											<td width=100% colspan=5>
												<table width=100%>
													<tr>
														<td align=left>
<?php
    if (strlen($report->previous) > 0) {
      echo '<a href="' . os_href_link(FILENAME_STATS_SALES_REPORT2, $report->previous, 'NONSSL') . '">&lt;&lt;&nbsp;' . TEXT_PREVIOUS . '</a>';
    }
?>
														</td>
										                <td align=right>
<?php
    if (strlen($report->next) > 0) {
      echo '<a href="' . os_href_link(FILENAME_STATS_SALES_REPORT2, $report->next, 'NONSSL') . '">' . TEXT_NEXT . '&nbsp;&gt;&gt;</a>';
      echo "";
    }
?>
														</td>
													</tr>
												</table>
											</td>
										</tr>
<?php
  }
?>				
          </table></div>
			


        <div id="tab_table">
        <h3><?php echo TAB_TABLE; ?></h3>				
				
									<table border="0" width="100%" cellspacing="2" cellpadding="2">
										<tr class="dataTableHeadingRow">
											<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DATE; ?></td>
											<td class="dataTableHeadingContent" align=center><?php echo TABLE_HEADING_STAT_ORDERS; ?></td>
											<td class="dataTableHeadingContent" align=right><?php echo TABLE_HEADING_CONV_PER_ORDER; ?></td>
											<td class="dataTableHeadingContent" align=right><?php echo TABLE_HEADING_CONVERSION; ?></td>
											<td class="dataTableHeadingContent" align=right><?php echo TABLE_HEADING_VARIANCE; ?></td>
										</tr>
<?php

  $last_value = 0;
  $sum = 0;
  for ($i = 0; $i < $report->size; $i++) {
    if ($last_value != 0) {
      $percent = 100 * $report->info[$i]['sum'] / $last_value - 100;
    } else {
      $percent = "0";
    }
    $sum += $report->info[$i]['sum'];
    $avg += $report->info[$i]['avg'];
    $last_value = $report->info[$i]['sum'];
?>
										<tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='dataTableRow'">
							                <td class="dataTableContent">
<?php
    if (strlen($report->info[$i]['link']) > 0 ) {
      echo '<a href="' . os_href_link(FILENAME_STATS_SALES_REPORT2, $report->info[$i]['link'], 'NONSSL') . '">';
    }
    echo $report->info[$i]['text'] . $date_text[$i];
    if (strlen($report->info[$i]['link']) > 0 ) {
      echo '</a>';
    }
?></td>
											<td class="dataTableContent" align=center><?php echo $report->info[$i]['count']?></td>
											<td class="dataTableContent"align=right><?php echo $currencies->format($report->info[$i]['avg'])?></td>
											<td class="dataTableContent" align=right><?php echo $currencies->format($report->info[$i]['sum'])?></td>
											<td class="dataTableContent" align=right>
<?php
    if ($percent == 0){
      echo "---";
    } else {
      echo number_format($percent,0) . "%";
    }
?>
</td>
										</tr>
<?php
 }
?>

<?php
  if (strlen($report->previous . " " . $report->next) > 1) {
?>
										<tr>
											<td width=100% colspan=5>
												<table width=100%>
													<tr>
														<td align=left>
<?php
    if (strlen($report->previous) > 0) {
      echo '<a href="' . os_href_link(FILENAME_STATS_SALES_REPORT2, $report->previous, 'NONSSL') . '">&lt;&lt;&nbsp;' . TEXT_PREVIOUS . '</a>';
    }
?>
														</td>
										                <td align=right>
<?php
    if (strlen($report->next) > 0) {
      echo '<a href="' . os_href_link(FILENAME_STATS_SALES_REPORT2, $report->next, 'NONSSL') . '">' . TEXT_NEXT . '&nbsp;&gt;&gt;</a>';
      echo "";
    }
?>
														</td>
													</tr>
												</table>
											</td>
										</tr>
<?php
  }
?>

                  </table>
                 
                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php if ($order_cnt != 0){
?>
                    <tr class="dataTableRow">
                      <td class="dataTableContent" width=100% align=right><?php echo '<b>'. AVERAGE_ORDER . ' </b>' ?></td>
                      <td class="dataTableContent" align=right><?php echo $currencies->format($sum / $order_cnt) ?></td>
                    </tr>
<?php } 
  if ($report->size != 0) {
?>
                    <tr class="dataTableRow">
                      <td class="dataTableContent" width=100% align=right><?php echo '<b>'. $summary1 . ' </b>' ?></td>
                      <td class="dataTableContent" align=right><?php echo $currencies->format($sum / $report->size) ?></td>
                    </tr>
<?php } ?>
                    <tr class="dataTableRow">
                      <td class="dataTableContent" width=100% align=right><?php echo '<b>'. $summary2 . ' </b>' ?></td>
                      <td class="dataTableContent" align=right><?php echo $currencies->format($sum) ?></td>
                    </tr>
                  </table>
             
                </div>                  

        <div id="tab_status">
        <h3><?php echo TAB_STATUS; ?></h3>				

                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr class="dataTableRow">
                      <td class="dataTableContent" align="left" width="100%"><?php echo FILTER_STATUS  ?></td>
                      <td class="dataTableContent" align="center"><?php echo FILTER_VALUE ?></td>
                    </tr>
<?php
  if (($sales_report_filter) == 0) {
    for ($i = 0; $i < $report->status_available_size; $i++) {
      $sales_report_filter .= "0";
    }
  }

  for ($i = 0; $i < $report->status_available_size; $i++) {
?>
                    <tr>
                      <td class="dataTableContent" align="left"><?php echo $report->status_available[$i]['value'] ?></a></td>
<?php
    if (substr($sales_report_filter,$i,1) ==  "0") {
      $tmp = substr($sales_report_filter, 0, $i) . "1" . substr($sales_report_filter, $i+1, $report->status_available_size - ($i + 1));

      $tmp = os_href_link(FILENAME_STATS_SALES_REPORT2, $report->filter_link . "&filter=". $tmp, 'NONSSL');
?>
      <td class="dataTableContent"  align="center"><?php echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) ?>&nbsp;<a href="<?php echo $tmp; ?>"><?php echo os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) ?></a></td>
<?php
    } else {
      $tmp = substr($sales_report_filter, 0, $i) . "0" . substr($sales_report_filter, $i+1);
      $tmp = os_href_link(FILENAME_STATS_SALES_REPORT2, $report->filter_link . "&filter=". $tmp, 'NONSSL');
?>
      <td class="dataTableContent" width="100%" align="center"><a href="<?php echo $tmp; ?>"><?php echo os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) ?></a>&nbsp;<?php echo os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) ?></td>
<?php
    }
?>
                    </tr>
<?php
  }
?>
                  </table>
               </div>
</div>


    </td>
  </tr>
</table>
<?php $main->bottom(); ?>