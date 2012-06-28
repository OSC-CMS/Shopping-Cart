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
$breadcrumb->add(NAVBAR_TITLE_PAYMENT, os_href_link(FILENAME_AFFILIATE_PAYMENT, '', 'SSL'));

if (!isset($_GET['page'])) $_GET['page'] = 1;

$affiliate_payment_raw = "select p.* , s.affiliate_payment_status_name
           from " . TABLE_AFFILIATE_PAYMENT . " p, " . TABLE_AFFILIATE_PAYMENT_STATUS . " s 
           where p.affiliate_payment_status = s.affiliate_payment_status_id 
           and s.affiliate_language_id = '" . $_SESSION['languages_id'] . "'
           and p.affiliate_id =  '" . $_SESSION['affiliate_id'] . "'
           order by p.affiliate_payment_id DESC";

$affiliate_payment_split = new splitPageResults($affiliate_payment_raw, $_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);

require(dir_path('includes') . 'header.php');

$osTemplate->assign('affiliate_payment_split_number', $affiliate_payment_split->number_of_rows);

$affiliate_payment_table = '';

if ($affiliate_payment_split->number_of_rows > 0) {
	$affiliate_payment_values = os_db_query($affiliate_payment_split->sql_query);
    $number_of_payment = 0;
    while ($affiliate_payment = os_db_fetch_array($affiliate_payment_values)) {
    	$number_of_payment++;
    	
        if (($number_of_payment / 2) == floor($number_of_payment / 2)) {
        	$affiliate_payment_table .= '<tr class="productListing-even">';
        }
		else {
			$affiliate_payment_table .= '<tr class="productListing-odd">';
		}
		
		$affiliate_payment_table .= '<td class="smallText" align="right">' . $affiliate_payment['affiliate_payment_id'] . '</td>';
		$affiliate_payment_table .= '<td class="smallText" align="center">' . os_date_short($affiliate_payment['affiliate_payment_date']) . '</td>';
		$affiliate_payment_table .= '<td class="smallText" align="right">' . $osPrice->Format($affiliate_payment['affiliate_payment_total'], true) . '</td>';
		$affiliate_payment_table .= '<td class="smallText" align="right">' . $affiliate_payment['affiliate_payment_status_name'] . '</td>';
	}
	$osTemplate->assign('affiliate_payment_table', $affiliate_payment_table);
}

if ($affiliate_payment_split->number_of_rows > 0) {
	$osTemplate->assign('affiliate_payment_split_count', $affiliate_payment_split->display_count(TEXT_DISPLAY_NUMBER_OF_PAYMENTS));
	$osTemplate->assign('affiliate_payment_split_link', $affiliate_payment_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array('page', 'info', 'x', 'y'))));
}

$affiliate_payment_values = os_db_query("select sum(affiliate_payment_total) as total from " . TABLE_AFFILIATE_PAYMENT . " where affiliate_id = '" . $_SESSION['affiliate_id'] . "'");
$affiliate_payment = os_db_fetch_array($affiliate_payment_values);

$osTemplate->assign('affiliate_payment_total', $osPrice->Format($affiliate_payment['total'], true));
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_payment.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');

?>
