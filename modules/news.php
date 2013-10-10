<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$module = new osTemplate;

$query = $cartet->news->getAll(1, $_SESSION['languages_id'], true, MAX_DISPLAY_LATEST_NEWS);

$aNews = array();
if (os_db_num_rows($query) > 0)
{
	while ($row = os_db_fetch_array($query))
	{
		$aNews[] = $cartet->news->getData($row);
	}
	$module->assign('EMPTY', false);
}
else
	$module->assign('EMPTY', TEXT_NO_NEWS);

$module->assign('NEWS_LINK', os_href_link(FILENAME_NEWS));
$module->assign('language', $_SESSION['language']);
$module->assign('aNews', $aNews);

if (!CacheCheck())
{
	$module->caching = 0;
	$module= $module->fetch(CURRENT_TEMPLATE.'/module/latest_news_default.html');
}
else
{
	$module->caching = 1;
	$module->cache_lifetime=CACHE_LIFETIME;
	$module->cache_modified_check=CACHE_CHECK;
	$module = $module->fetch(CURRENT_TEMPLATE.'/module/latest_news_default.html',$cache_id);
}
$default->assign('MODULE_latest_news', $module);

?>