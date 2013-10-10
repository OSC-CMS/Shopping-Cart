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
        faq_id,
        question,
        answer,
        date_added
    FROM " . TABLE_FAQ . "
    WHERE
         status = '1'
         and language = '" . (int)$_SESSION['languages_id'] . "'
    ORDER BY date_added DESC
    LIMIT " . MAX_DISPLAY_FAQ . "
    ";

$module_content = array();
$query = osDBquery($sql);
while ($one = os_db_fetch_array($query,true)) {

		$SEF_parameter = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter = '&question='.os_cleanName($one['question']);

    $module_content[]=array(
        'FAQ_QUESTION' => $one['question'],
        'FAQ_ANSWER' => $one['answer'],
        'FAQ_ID'      => $one['faq_id'],
        'FAQ_DATA'    => os_date_short($one['date_added']),
        'FAQ_LINK_MORE'    => os_href_link(FILENAME_FAQ, 'faq_id='.$one['faq_id'] . $SEF_parameter, 'NONSSL'),
        );
}

if (sizeof($module_content) > 0) {
    $box->assign('FAQ_LINK', os_href_link(FILENAME_FAQ));
    $box->assign('language', $_SESSION['language']);
    $box->assign('module_content',$module_content);
    // set cache ID
    if (USE_CACHE=='false') {
        $box->caching = 0;
        $module= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_faq.html');
    } else {
        $box->caching = 1;
        $box->cache_lifetime=CACHE_LIFETIME;
        $box->cache_modified_check=CACHE_CHECK;
        $module = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_faq.html',$cache_id);
    }
    $osTemplate->assign('box_FAQ',$module);
}
?>