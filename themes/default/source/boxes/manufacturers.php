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

$box->assign('language', $_SESSION['language']);

if (!CacheCheck()) {
	$cache = false;
	$box->caching = 0;
} else {
	$cache = true;
	$box->caching = 1;
	$box->cache_lifetime = CACHE_LIFETIME;
	$box->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].(int) $_GET['manufacturers_id'];
}

if (!$box->isCached(CURRENT_TEMPLATE.'/boxes/box_manufacturers.html', @$cache_id) || !$cache) 
{
	$box->assign('tpl_path', _HTTP_THEMES_C);

	$manufacturers_query = "select distinct m.manufacturers_id, m.manufacturers_name from ".TABLE_MANUFACTURERS." as m, ".TABLE_PRODUCTS." as p where m.manufacturers_id=p.manufacturers_id order by m.manufacturers_name";

	$manufacturers_query = osDBquery($manufacturers_query);
	if (os_db_num_rows($manufacturers_query, true) <= MAX_DISPLAY_MANUFACTURERS_IN_A_LIST) {
		// Display a list
		$manufacturers_list = '';
		while ($manufacturers = os_db_fetch_array($manufacturers_query, true)) {
			$manufacturers_name = ((utf8_strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? utf8_substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN).'..' : $manufacturers['manufacturers_name']);
			if (isset ($_GET['manufacturers_id']) && ($_GET['manufacturers_id'] == $manufacturers['manufacturers_id']))
				$manufacturers_name = '<b>'.$manufacturers_name.'</b>';
			$manufacturers_list .= '<a href="'.os_href_link(FILENAME_DEFAULT, 'manufacturers_id='.$manufacturers['manufacturers_id']).'">'.$manufacturers_name.'</a><br />';
		}
		
		$box_content = $manufacturers_list;
		
	} 
	else 
	{
		// Display a drop-down
		$manufacturers_array = array ();
		if (MAX_MANUFACTURERS_LIST < 2) {
			$manufacturers_array[] = array ('id' => '', 'text' => PULL_DOWN_DEFAULT);
		}

		while ($manufacturers = os_db_fetch_array($manufacturers_query, true)) {
			$manufacturers_name = ((utf8_strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? utf8_substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN).'..' : $manufacturers['manufacturers_name']);
			$manufacturers_array[] = array ('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers_name);
		}

		$box_content = os_draw_form('manufacturers', os_href_link(FILENAME_DEFAULT, '', 'NONSSL', false), 'get').os_draw_pull_down_menu('manufacturers_id', $manufacturers_array, isset($_GET['manufacturers_id'])?$_GET['manufacturers_id']:'', 'onchange="this.form.submit();" size="'.MAX_MANUFACTURERS_LIST.'" style="width: 100%"').os_hide_session_id().'</form>';

	}

	if ($box_content != '')
		$box->assign('BOX_CONTENT', $box_content);

}
// set cache ID
if (!$cache) {
	$box_manufacturers = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_manufacturers.html');
} else {
	$box_manufacturers = $box->fetch(CURRENT_TEMPLATE.'/boxes/box_manufacturers.html', $cache_id);
}

$osTemplate->assign('box_MANUFACTURERS', $box_manufacturers);
?>