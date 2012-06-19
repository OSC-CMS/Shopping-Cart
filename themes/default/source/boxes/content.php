<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  Ver. 1.0.0
#####################################
*/

$box = new osTemplate;

$content_string = '';

$box->assign('language', $_SESSION['language']);

if (!CacheCheck()) {
	$cache=false;
	$box->caching = 0;
} else {
	$cache=true;
	$box->caching = 1;
	$box->cache_lifetime = CACHE_LIFETIME;
	$box->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'];
}

if (!$box->is_cached(CURRENT_TEMPLATE.'/boxes/box_content.html', @$cache_id) || !$cache) {

	$box->assign('tpl_path', _HTTP_THEMES_C);

	if (GROUP_CHECK == 'true') {
		$group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
	}

	$content_query = "SELECT
	 					content_id,
	 					categories_id,
	 					parent_id,
	 					content_title,
	 					content_url,
	 					content_group
	 					FROM ".TABLE_CONTENT_MANAGER."
	 					WHERE languages_id='".(int) $_SESSION['languages_id']."'
	 					and file_flag=1 ".$group_check." and content_status=1 order by sort_order";

	$content_query = osDBquery($content_query);

	while ($content_data = os_db_fetch_array($content_query, true)) {
		$SEF_parameter = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter = '&content='.os_cleanName($content_data['content_title']);

if ($content_data['content_url'] != '') {
	$link = '<li><a class="content" href="'.$content_data['content_url'].'" target="_blank">';
} else {
	if (strstr($PHP_SELF, FILENAME_CONTENT) && isset($_GET['coID']) && $_GET['coID'] == $content_data['content_id'])  {
		$link = '<li class="current"><a href="'.os_href_link(FILENAME_CONTENT, 'coID='.$content_data['content_group'].$SEF_parameter).'">';
	} else {
		$link = '<li><a href="'.os_href_link(FILENAME_CONTENT, 'coID='.$content_data['content_group'].$SEF_parameter).'">';
	}
}

		$content_string .= $link.$content_data['content_title'].'</a></li>' . "\n";
	}

	if ($content_string != '')
		$box->assign('BOX_CONTENT', $content_string);

}

if (!$cache) {
	$box_content = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_content.html');
} else {
	$box_content = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_content.html', $cache_id);
}

$osTemplate->assign('box_CONTENT', $box_content);
?>