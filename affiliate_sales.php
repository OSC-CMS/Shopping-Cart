<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

require('includes/top.php');
//$osTemplate = new osTemplate;



if (!isset($_SESSION['affiliate_id'])) {
    os_redirect(os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
}

$breadcrumb->add(NAVBAR_TITLE, os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_SALES, os_href_link(FILENAME_AFFILIATE_SALES, '', 'SSL'));

if (!isset($_GET['page'])) $_GET['page'] = 1;

if (os_not_null($_GET['a_period'])) {
    $period_split = preg_split('/-/', os_db_prepare_input( $_GET['a_period'] ) );
    $period_clause = " AND year(a.affiliate_date) = " . $period_split[0] . " and month(a.affiliate_date) = " . $period_split[1];
}
if (os_not_null($_GET['a_status'])) {
    $a_status = os_db_prepare_input( $_GET['a_status'] );
    $status_clause = " AND o.orders_status = '" . $a_status . "'";
}
if ( is_numeric( $_GET['a_level'] )  ) {
      $a_level = os_db_prepare_input( $_GET['a_level'] );
      $level_clause = " AND a.affiliate_level = '" . $a_level . "'";
}
$affiliate_sales_raw = "select a.affiliate_payment, a.affiliate_date, a.affiliate_value, a.affiliate_percent,
    a.affiliate_payment, a.affiliate_level AS level,
    o.orders_status as orders_status_id, os.orders_status_name as orders_status, 
    MONTH(aa.affiliate_date_account_created) as start_month, YEAR(aa.affiliate_date_account_created) as start_year
    from " . TABLE_AFFILIATE . " aa
    left join " . TABLE_AFFILIATE_SALES . " a on (aa.affiliate_id = a.affiliate_id )
    left join " . TABLE_ORDERS . " o on (a.affiliate_orders_id = o.orders_id) 
    left join " . TABLE_ORDERS_STATUS . " os on (o.orders_status = os.orders_status_id and language_id = '" . $_SESSION['languages_id'] . "')
    where a.affiliate_id = '" . $_SESSION['affiliate_id'] . "' " .
    $period_clause . $status_clause . $level_clause . " 
    group by aa.affiliate_date_account_created, o.orders_status, os.orders_status_name, 
        a.affiliate_payment, a.affiliate_date, a.affiliate_value, a.affiliate_percent, 
        o.orders_status, os.orders_status_name
    order by affiliate_date DESC";

$count_key = 'aa.affiliate_date_account_created, o.orders_status, os.orders_status_name, a.affiliate_payment, a.affiliate_date, a.affiliate_value, a.affiliate_percent, o.orders_status, os.orders_status_name';
        
$affiliate_sales_split = new splitPageResults($affiliate_sales_raw, $_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $count_key);
if ($affiliate_sales_split->number_of_rows > 0) {
    $affiliate_sales_values = os_db_query($affiliate_sales_split->sql_query);
    $affiliate_sales = os_db_fetch_array($affiliate_sales_values);
}
else {
    $affiliate_sales_values = os_db_query( "select MONTH(affiliate_date_account_created) as start_month,
                                      YEAR(affiliate_date_account_created) as start_year
                                      FROM " . TABLE_AFFILIATE . " WHERE affiliate_id = '" . $_SESSION['affiliate_id'] . "'" );
    $affiliate_sales = os_db_fetch_array( $affiliate_sales_values );
}

$osTemplate->assign('period_selector', affiliate_period('a_period', $affiliate_sales['start_year'], $affiliate_sales['start_month'], true, os_db_prepare_input($_GET['a_period'] ), 'onchange="this.form.submit();"' ));
$osTemplate->assign('status_selector', affiliate_get_status_list('a_status', os_db_prepare_input($_GET['a_status']), 'onchange="this.form.submit();"' ));
$osTemplate->assign('level_selector', affiliate_get_level_list('a_level', os_db_prepare_input($_GET['a_level']), 'onchange="this.form.submit();"'));

require(dir_path('includes') . 'header.php');

$osTemplate->assign('affiliate_sales_split_numbers', $affiliate_sales_split->number_of_rows);
$osTemplate->assign('FORM_ACTION', os_draw_form('params', os_href_link(FILENAME_AFFILIATE_SALES ), 'get'));

$affiliate_sales_table = '';

if ($affiliate_sales_split->number_of_rows > 0) {
    $number_of_sales = 0;
    $sum_of_earnings = 0;

    do {
    	$number_of_sales++;
    	if ($affiliate_sales['orders_status_id'] >= AFFILIATE_PAYMENT_ORDER_MIN_STATUS) $sum_of_earnings += $affiliate_sales['affiliate_payment'];
    	if (($number_of_sales / 2) == floor($number_of_sales / 2)) {
    		$affiliate_sales_table .= '<tr class="productListing-even">';
    	}
		else {
			$affiliate_sales_table .= '<tr class="productListing-odd">';
		}
		$affiliate_sales_table .= '<td class="smallText" align="center">' . os_date_short($affiliate_sales['affiliate_date']) . '</td>';
		$affiliate_sales_table .= '<td class="smallText" align="right">' . $osPrice->Format($affiliate_sales['affiliate_value'], true) . '</td>';
		$affiliate_sales_table .= '<td class="smallText" align="right">' . $affiliate_sales['affiliate_percent'] . " %" . '</td>';
		$affiliate_sales_table .= '<td class="smallText" align="right">' . (($affiliate_sales['level'] > 0) ? $affiliate_sales['level'] : TEXT_AFFILIATE_PERSONAL_LEVEL_SHORT) . '</td>';
		$affiliate_sales_table .= '<td class="smallText" align="right">' . $osPrice->Format($affiliate_sales['affiliate_payment'], true) . '</td>';
		$affiliate_sales_table .= '<td class="smallText" align="right">' . (($affiliate_sales['orders_status'] != '')?$affiliate_sales['orders_status']:TEXT_DELETED_ORDER_BY_ADMIN) . '</td>';
		$affiliate_sales_table .= '</tr>';
	} while ( $affiliate_sales = os_db_fetch_array($affiliate_sales_values) );
	$osTemplate->assign('affiliate_sales_table', $affiliate_sales_table);
}

if ($affiliate_sales_split->number_of_rows > 0) {
	$osTemplate->assign('affiliate_sales_count', $affiliate_sales_split->display_count(TEXT_DISPLAY_NUMBER_OF_SALES));
	$osTemplate->assign('affiliate_sales_links', $affiliate_sales_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array('page', 'info', 'x', 'y'))));
}

$osTemplate->assign('affiliate_sales_total', $osPrice->Format($sum_of_earnings,true));
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_sales.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');

?>
