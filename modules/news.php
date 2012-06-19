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

$module = new osTemplate;
$module->assign('tpl_path', _HTTP_THEMES_C);

$sql = "
    SELECT
        news_id,
        headline,
        content,
        date_added
    FROM " . TABLE_LATEST_NEWS . "
    WHERE
         status = '1'
         and language = '" . (int)$_SESSION['languages_id'] . "'
    ORDER BY date_added DESC
    LIMIT " . MAX_DISPLAY_LATEST_NEWS . "
    ";

$row = 0;
$module_content = array ();

$query = osDBquery($sql);
while ($one = os_db_fetch_array($query,true)) {

		$SEF_parameter = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter = '&headline='.os_cleanName($one['headline']);

    $module_content[]=array(
        'NEWS_HEADING' => $one['headline'],
        'NEWS_CONTENT' => strip_tags($one['content']),
        'NEWS_ID'      => $one['news_id'],
        'NEWS_DATA'    => os_date_short($one['date_added']),
        'NEWS_LINK_MORE'    => os_href_link(FILENAME_NEWS, 'news_id='.$one['news_id'] . $SEF_parameter, 'NONSSL'),
        );

}
if (sizeof($module_content) > 0) {
    $module->assign('NEWS_LINK', os_href_link(FILENAME_NEWS));
    $module->assign('language', $_SESSION['language']);
    $module->assign('module_content',$module_content);

	 if (!CacheCheck()) {
		$module->caching = 0;
      $module= $module->fetch(CURRENT_TEMPLATE.'/module/latest_news_default.html');
	} else {
        $module->caching = 1;
        $module->cache_lifetime=CACHE_LIFETIME;
        $module->cache_modified_check=CACHE_CHECK;
        $module = $module->fetch(CURRENT_TEMPLATE.'/module/latest_news_default.html',$cache_id);
	}
	$default->assign('MODULE_latest_news', $module);
}
?>