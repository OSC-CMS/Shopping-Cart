<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

	define("AJAX_QUICKSEARCH_RESULT", 'text'); // dropdown or text
	define("AJAX_QUICKSEARCH_DROPDOWN_SIZE", 5);
	define("AJAX_QUICKSEARCH_LIMIT", 15);

	$q = addslashes(preg_replace("%[^0-9a-zA-Zа-яА-Я\s]%iu", "", $_REQUEST['keywords']) );

	$out = "";
	if(isset($q) && os_not_null($q)) {

		$searchwords = explode(" ",$q);
		$nosearchwords = sizeof($searchwords);
		foreach($searchwords as $key => $value) {
			if ($value == '')
				unset($searchwords[$key]);
		}
		$searchwords = array_values($searchwords);
		$nosearchwords = sizeof($searchwords);
		foreach($searchwords as $key => $value) {
			$booltje = '+' . $searchwords[$key] . '*';
			$searchwords[$key] = $booltje;
		}
		$q = implode(" ",$searchwords);

		$products_query = os_db_query("select pd.products_id, pd.products_name, pd.products_keywords, p.products_model
							from " . TABLE_PRODUCTS_DESCRIPTION . " pd
							inner join " . TABLE_PRODUCTS . " p
							on (p.products_id = pd.products_id)
							where (match (pd.products_name) against ('" . $q . "' in boolean mode)
							or match (p.products_model) against ('" . $q . "' in boolean mode) or match (pd.products_keywords) against ('" . $q . "' in boolean mode)" .
							($_REQUEST['search_in_description'] == '1' ? "or match (pd.products_description) against ('" . $q . "' in boolean mode)" : "") . ")
							and p.products_status = '1'
							and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
							order by pd.products_name asc
							limit " . AJAX_QUICKSEARCH_LIMIT);

		if(os_db_num_rows($products_query)) {
			$dropdown = array();
			$out .= '<ul class="ajaxQuickFind">';
			$out .= '<li><h3>'.sprintf(TEXT_AJAX_QUICKSEARCH_TOP, AJAX_QUICKSEARCH_LIMIT).'</h3></li>';
			while($products = os_db_fetch_array($products_query)) {
				$out .= '<li><a href="' . os_href_link(FILENAME_PRODUCT_INFO, os_product_link($products['products_id'], $products['products_name']), 'NONSSL', false) . '">' . $products['products_name'] . '</a></li>' . "\n";
				$dropdown[] = array('id' => $products['products_id'],
														'text' => $products['products_name']);
			}
			$out .= '</ul>' . "\n";
			if(AJAX_QUICKSEARCH_RESULT == 'dropdown') {
				$out .= os_draw_pull_down_menu('AJAX_QUICKSEARCH_pid', $dropdown, '', 'onChange="this.form.submit();" size="' . AJAX_QUICKSEARCH_DROPDOWN_SIZE . '" class="ajaxQuickFind"') . os_hide_session_id();
			}
		}
	}
	$_RESULT['ajaxQuickFind'] = $out;
?>