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

if (!isset($_SESSION['affiliate_id'])) {
    os_redirect(os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
}

$breadcrumb->add(NAVBAR_TITLE, os_href_link(FILENAME_AFFILIATE, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_SUMMARY, os_href_link(FILENAME_AFFILIATE_SUMMARY));
  
$affiliate_raw = "select sum(affiliate_banners_shown) as banner_count, "
                   . "count(affiliate_clickthrough_id) as clickthrough_count, "
                   . "MONTH(affiliate_date_account_created) as start_month, "
                   . "YEAR(affiliate_date_account_created) as start_year, "
                   . "a.affiliate_commission_percent, a.affiliate_firstname, a.affiliate_id, affiliate_lastname "
                   . "from " . TABLE_AFFILIATE . " AS a "
                   . "LEFT JOIN " . TABLE_AFFILIATE_CLICKTHROUGHS . " AS ac ON ( a.affiliate_id = ac.affiliate_id )"
                   . "LEFT JOIN " . TABLE_AFFILIATE_BANNERS_HISTORY . " AS ab ON ( a.affiliate_id = ab.affiliate_banners_affiliate_id )"
                   . " where a.affiliate_id  = '" . $_SESSION['affiliate_id'] . "'"
                   . " GROUP BY a.affiliate_date_account_created, a.affiliate_commission_percent, a.affiliate_firstname, affiliate_lastname ";
$affiliate_query = os_db_query( $affiliate_raw );
$affiliate = os_db_fetch_array($affiliate_query);
$osTemplate->assign('affiliate', $affiliate);

$affiliate_impressions = $affiliate['banner_count'];
if ($affiliate_impressions == 0) $affiliate_impressions="n/a";
$osTemplate->assign('affiliate_impressions', $affiliate_impressions);

$osTemplate->assign('period_selector', affiliate_period( 'a_period', $affiliate['start_year'], $affiliate['start_month'], true, os_db_prepare_input( $_GET['a_period'] ), 'onchange="this.form.submit();"' ));

$affiliate_percent = 0;
$affiliate_percent = $affiliate['affiliate_commission_percent'];
if ($affiliate_percent < AFFILIATE_PERCENT) $affiliate_percent = AFFILIATE_PERCENT;
$osTemplate->assign('affiliate_percent', os_round($affiliate_percent, 2));

$affiliate_percent_tier = preg_split("/;/", AFFILIATE_TIER_PERCENTAGE, AFFILIATE_TIER_LEVELS );

if ( (empty($_GET['a_period'])) or ( $_GET['a_period'] == "all" ) ) {
    $affiliate_sales = affiliate_level_statistics_query( $_SESSION['affiliate_id'] );
}
else {
    $affiliate_sales = affiliate_level_statistics_query( $_SESSION['affiliate_id'], os_db_prepare_input( $_GET['a_period'] ) );
}

$osTemplate->assign('affiliate_transactions', os_not_null($affiliate_sales['count']) ? $affiliate_sales['count'] : 0);

if ($affiliate_clickthroughs > 0) {
	$affiliate_conversions = os_round(($affiliate_transactions / $affiliate_clickthroughs) * 100, 2) . "%";
}
else {
    $affiliate_conversions = "n/a";
}
$osTemplate->assign('affiliate_conversions', $affiliate_conversions);

$osTemplate->assign('affiliate_amount', $osPrice->Format($affiliate_sales['total'], true));

if ($affiliate_transactions > 0) {
	$affiliate_average = os_round($affiliate_amount / $affiliate_transactions, 2);
	$affiliate_average = $osPrice->Format($affiliate_average, true);
}
else {
	$affiliate_average = "n/a";
}
$osTemplate->assign('affiliate_average', $affiliate_average);

$osTemplate->assign('affiliate_commission', $osPrice->Format($affiliate_sales['payment'], true));;

require(dir_path('includes') . 'header.php');

$osTemplate->assign('FORM_ACTION', os_draw_form('period', os_href_link(FILENAME_AFFILIATE_SUMMARY ), 'get'));

$osTemplate->assign('LINK_IMPRESSION', '<a href="javascript:popupAffWindow(\'' . os_href_link(FILENAME_AFFILIATE_HELP_1) . '\')">');
$osTemplate->assign('LINK_VISIT', '<a href="javascript:popupAffWindow(\'' . os_href_link(FILENAME_AFFILIATE_HELP_2) . '\')">');
$osTemplate->assign('LINK_TRANSACTIONS', '<a href="javascript:popupAffWindow(\'' . os_href_link(FILENAME_AFFILIATE_HELP_3) . '\')">');
$osTemplate->assign('LINK_CONVERSION', '<a href="javascript:popupAffWindow(\'' . os_href_link(FILENAME_AFFILIATE_HELP_4) . '\')">');
$osTemplate->assign('LINK_AMOUNT', '<a href="javascript:popupAffWindow(\'' . os_href_link(FILENAME_AFFILIATE_HELP_5) . '\')">');
$osTemplate->assign('LINK_AVERAGE', '<a href="javascript:popupAffWindow(\'' . os_href_link(FILENAME_AFFILIATE_HELP_6) . '\')">');
$osTemplate->assign('LINK_COMISSION_RATE', '<a href="javascript:popupAffWindow(\'' . os_href_link(FILENAME_AFFILIATE_HELP_7) . '\')">');
$osTemplate->assign('LINK_COMISSION', '<a href="javascript:popupAffWindow(\'' . os_href_link(FILENAME_AFFILIATE_HELP_8) . '\')">');

if ( AFFILATE_USE_TIER == 'true' ) {
	$osTemplate->assign('AFFILIATE_USE_TIER', 'true');
	
    for ($tier_number = 0; $tier_number <= AFFILIATE_TIER_LEVELS; $tier_number++ ) {
    	if (is_null($affiliate_percent_tier[$tier_number - 1])) {
    		$affiliate_percent_tier[$tier_number - 1] = $affiliate_percent;
    	}
    	$affiliate_percent_tier_table .= '<tr>';
    	$affiliate_percent_tier_table .= '<td width="15%" class="boxtext"><a href=' . os_href_link(FILENAME_AFFILIATE_SALES, 'a_level=' . $tier_number . '&a_period=' . $a_period, 'SSL') . '>' . TEXT_COMMISSION_LEVEL_TIER . $tier_number . '</a></td>';
    	$affiliate_percent_tier_table .= '<td width="15%" align="right" class="boxtext"><a href=' . os_href_link(FILENAME_AFFILIATE_SALES, 'a_level=' . $tier_number . '&a_period=' . $a_period, 'SSL') . '>' . TEXT_COMMISSION_RATE_TIER . '</a></td>';
    	$affiliate_percent_tier_table .= '<td width="5%" class="boxtext">' . os_round($affiliate_percent_tier[$tier_number - 1], 2). '%' . '</td>';
    	$affiliate_percent_tier_table .= '<td width="15%" align="right" class="boxtext"><a href=' . os_href_link(FILENAME_AFFILIATE_SALES, 'a_level=' . $tier_number . '&a_period=' . $a_period, 'SSL') . '>' . TEXT_COMMISSION_TIER_COUNT . '</a></td>';
    	$affiliate_percent_tier_table .= '<td width="5%" class="boxtext">' . ($affiliate_sales[$tier_number]['count'] > 0 ? $affiliate_sales[$tier_number]['count'] : '0') . '</td>';
    	$affiliate_percent_tier_table .= '<td width="15%" align="right" class="boxtext"><a href=' . os_href_link(FILENAME_AFFILIATE_SALES, 'a_level=' . $tier_number . '&a_period=' . $a_period, 'SSL') . '>' . TEXT_COMMISSION_TIER_TOTAL . '</a></td>';
    	$affiliate_percent_tier_table .= '<td width="5%" class="boxtext">' . $osPrice->Format($affiliate_sales[$tier_number]['total'], true) . '</td>';
    	$affiliate_percent_tier_table .= '<td width="20%" align="right" class="boxtext"><a href=' . os_href_link(FILENAME_AFFILIATE_SALES, 'a_level=' . $tier_number . '&a_period=' . $a_period, 'SSL') . '>' . TEXT_COMMISSION_TIER . '</a></td>';
    	$affiliate_percent_tier_table .= '<td width="5%" class="boxtext">' . $osPrice->Format($affiliate_sales[$tier_number]['payment'],true) . '</td>';
    	$affiliate_percent_tier_table .= '</tr>';
	}
	$osTemplate->assign('affiliate_percent_tier_table', $affiliate_percent_tier_table);
}

    $_array = array('img' => 'button_affiliate_banners.gif', 'href' => os_href_link(FILENAME_AFFILIATE_BANNERS), 'alt' => IMAGE_BANNERS, 'code' => '');
	
	   $_array = apply_filter('button_affiliate_banners', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  '<a href="' . $_array['href'] . '">' . os_image_button($_array['img'], $_array['alt']) . '</a>';
	   }
	   
$osTemplate->assign('LINK_BANNER', $_array['code']);


    $_array = array('img' => 'button_affiliate_clickthroughs.gif', 'href' => os_href_link(FILENAME_AFFILIATE_CLICKS, '', 'SSL'), 'alt' => IMAGE_CLICKTHROUGHS, 'code' => '');
	
	   $_array = apply_filter('button_affiliate_clickthroughs', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  '<a href="' . $_array['href'] . '">' . os_image_button($_array['img'], $_array['alt']) . '</a>';
	   }
	   

      $osTemplate->assign('LINK_CLICKS', $_array['code']);

    $_array = array('img' => 'button_affiliate_sales.gif', 'href' => os_href_link(FILENAME_AFFILIATE_SALES, 'a_period=' . $a_period, 'SSL'), 'alt' => IMAGE_SALES, 'code' => '');
	
	   $_array = apply_filter('button_affiliate_sales', $_array);	
	
	   if (empty($_array['code']))
 	   {
	       $_array['code'] =  '<a href="' . $_array['href'] . '">' . os_image_button($_array['img'], $_array['alt']) . '</a>';
	   }
	   

$osTemplate->assign('LINK_SALES',  $_array['code']);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content=$osTemplate->fetch(CURRENT_TEMPLATE . '/module/affiliate_summary.html');
$osTemplate->assign('main_content',$main_content);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
 $osTemplate->load_filter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE . '/index.html');?>
