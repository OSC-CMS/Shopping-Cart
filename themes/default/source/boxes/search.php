<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

$box = new osTemplate;
$box_content = '';

$box->assign('FORM_ACTION', os_draw_form('quick_find', os_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get').os_hide_session_id());
$box->assign('INPUT_SEARCH', os_draw_input_field('keywords', '', 'class="box-input-search search-query" id="quick_find_keyword" placeholder="'.BOX_HEADING_SEARCH.'"'));
$box->assign('BUTTON_SUBMIT', buttonSubmit('button_quick_find.gif', null, IMAGE_BUTTON_SEARCH));
$box->assign('FORM_END', '</form>');
$box->assign('LINK_ADVANCED', os_href_link(FILENAME_ADVANCED_SEARCH));
$box->assign('BOX_CONTENT', $box_content);

$box->assign('language', $_SESSION['language']);
// set cache ID
 if (!CacheCheck()) {
	$box->caching = 0;
	$box_search = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_search.html');
} else {
	$box->caching = 1;
	$box->cache_lifetime = CACHE_LIFETIME;
	$box->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'];
	$box_search = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_search.html', $cache_id);
}

$osTemplate->assign('box_SEARCH', $box_search);
?>