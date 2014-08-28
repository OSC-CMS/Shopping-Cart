<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

require('includes/top.php');
//$osTemplate = new osTemplate;



if (!isset($_SESSION['affiliate_id'])) {
    os_redirect(os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
}

$breadcrumb->add(NAVBAR_TITLE, os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_CLICKS, os_href_link(FILENAME_AFFILIATE_CLICKS, '', 'SSL'));

if (!isset($_GET['page'])) $_GET['page'] = 1;

$affiliate_clickthroughs_raw = "select a.*, pd.products_name from " . TABLE_AFFILIATE_CLICKTHROUGHS . " a
                                    left join " . TABLE_PRODUCTS . " p on (p.products_id = a.affiliate_products_id)
                                    left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on (pd.products_id = p.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "')
                                    where a.affiliate_id = '" . $_SESSION['affiliate_id'] . "'  ORDER BY a.affiliate_clientdate desc";
$affiliate_clickthroughs_split = new splitPageResults($affiliate_clickthroughs_raw, $_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);

require(dir_path('includes') . 'header.php');

$osTemplate->assign('affiliate_clickthroughs_split_number', $affiliate_clickthroughs_split->number_of_rows);

$affiliate_clickthrough_table = '';

if ($affiliate_clickthroughs_split->number_of_rows > 0) {
    $affiliate_clickthroughs_values = os_db_query($affiliate_clickthroughs_split->sql_query);
    $number_of_clickthroughs = '0';
    while ($affiliate_clickthroughs = os_db_fetch_array($affiliate_clickthroughs_values)) {
        $number_of_clickthroughs++;
        
        if (($number_of_clickthroughs / 2) == floor($number_of_clickthroughs / 2)) {
            $affiliate_clickthrough_table .= '<tr class="productListing-even">';
        }
        else {
            $affiliate_clickthrough_table .= '<tr class="productListing-odd">';
        }
        $affiliate_clickthrough_table .= '<td class="smallText">' . os_date_short($affiliate_clickthroughs['affiliate_clientdate']) . '</td>';
        if ($affiliate_clickthroughs['affiliate_products_id'] > 0) {
            $link_to = '<a href="' . os_href_link (FILENAME_PRODUCT_INFO, 'products_id=' . $affiliate_clickthroughs['affiliate_products_id']) . '" target="_blank">' . $affiliate_clickthroughs['products_name'] . '</a>';
        }
        else {
            $link_to = "Startpage";
        }
        $affiliate_clickthrough_table .= '<td class="smallText">' . $link_to . '</td>';
        $affiliate_clickthrough_table .= '<td class="smallText">' . $affiliate_clickthroughs['affiliate_clientreferer'] . '</td></tr>';
    }
    $osTemplate->assign('affiliate_clickthrough_table', $affiliate_clickthrough_table);
}

if ($affiliate_clickthroughs_split->number_of_rows > 0) {
    $osTemplate->assign('affiliate_clickthroughs_split_count', $affiliate_clickthroughs_split->display_count(TEXT_DISPLAY_NUMBER_OF_CLICKS));
    $osTemplate->assign('affiliate_clickthroughs_split_links', $affiliate_clickthroughs_split->display_links(MAX_DISPLAY_PAGE_LINKS, os_get_all_get_params(array('page', 'info', 'x', 'y'))));
}
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_clicks.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->loadFilter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');?>
