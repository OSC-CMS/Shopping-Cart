<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$box = new osTemplate;

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

$module_content = array();
$query = osDBquery($sql);
while ($one = os_db_fetch_array($query,true)) {

		$SEF_parameter = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter = '&headline='.os_cleanName($one['headline']);

    $module_content[]=array(
        'NEWS_HEADING' => $one['headline'],
        'NEWS_CONTENT' => $one['content'],
        'NEWS_ID'      => $one['news_id'],
        'NEWS_DATA'    => os_date_short($one['date_added']),
        'NEWS_LINK_MORE'    => os_href_link(FILENAME_NEWS, 'news_id='.$one['news_id'] . $SEF_parameter, 'NONSSL'),
        );
}

if (sizeof($module_content) > 0) {
    $box->assign('NEWS_LINK', os_href_link(FILENAME_NEWS));
    $box->assign('language', $_SESSION['language']);
    $box->assign('module_content',$module_content);
    // set cache ID
    if (USE_CACHE=='false') {
        $box->caching = 0;
        $module= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_latest_news.html');
    } else {
        $box->caching = 1;
        $box->cache_lifetime=CACHE_LIFETIME;
        $box->cache_modified_check=CACHE_CHECK;
        $module = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_latest_news.html',$cache_id);
    }
    $osTemplate->assign('box_LATESTNEWS',$module);
}
?>