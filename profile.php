<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

include ('includes/top.php');

if (isset($_GET['id']) && is_numeric($_GET['id']))
{
	// Получаем ID покупателя
	$id = (int)$_GET['id'];

	// Запрос на выбору данных о покупателе
	$profileQuery = os_db_query("
	SELECT 
		p.customers_id, p.customers_signature, p.show_gender, p.show_firstname, p.show_secondname, p.show_lastname, p.show_dob, p.show_email, p.show_telephone, p.show_fax, p.customers_wishlist, c.customers_id, c.customers_gender, c.customers_firstname, c.customers_secondname, c.customers_lastname, c.customers_dob, c.customers_email_address, c.customers_telephone, c.customers_fax, c.customers_date_added, c.customers_last_modified, c.login_time, c.customers_username, customers_status_name
	FROM 
		".DB_PREFIX."customers_profile p, ".TABLE_CUSTOMERS." c 
			LEFT JOIN ".TABLE_CUSTOMERS_STATUS." ON (c.customers_status = customers_status_id AND language_id = ".(int)$_SESSION['languages_id'].")
	WHERE 
		p.customers_id = '".$id."' AND p.customers_id = c.customers_id
	");

	// Если запись в БД есть, то продолжаем
	if (os_db_num_rows($profileQuery) > 0)
	{
		$profile = os_db_fetch_array($profileQuery);

		$customers_dob = explode(' ', $profile['customers_dob']);
		$profile['customers_dob'] = $customers_dob[0];

		$customers_dob = explode(' ', $profile['customers_date_added']);
		$profile['customers_date_added'] = $customers_dob[0];

		$customers_dob = explode(' ', $profile['customers_last_modified']);
		$profile['customers_last_modified'] = $customers_dob[0];

		$breadcrumbName = (!empty($profile['customers_username'])) ? $profile['customers_username'] : $profile['customers_firstname'];

		$breadcrumb->add('Профиль '.$breadcrumbName.'', os_href_link('profile.php?id='.$id.'', '', 'SSL'));
		$id_error = false;// ID есть
	}
	else
		$id_error = true;// Такого ID нет

	$id_empty = false;// ID указан
}
else
	$id_empty = true;// ID не указан


require (dir_path('includes').'header.php');

// Отдаем все в шаблон
$osTemplate->assign('profile', $profile);
$osTemplate->assign('id_error', $id_error);
$osTemplate->assign('id_empty', $id_empty);

// TODO: переделать на currentUser
if ($_SESSION['customer_id'] == $id)
	$osTemplate->assign('account_edit', true);

// Фильтр таб-меню
$aProfileTabs = array();

if ($profile)
	$aProfileTabs['param'] = $profile;
else
	$aProfileTabs['param'] = '';

$aProfileTabs = apply_filter('profile_add_tabs', $aProfileTabs);

if (isset($aProfileTabs['values']) && is_array($aProfileTabs['values']) )
{
	$tProfileTabs = array();
	foreach ($aProfileTabs['values'] as $num => $value)
	{
		$tProfileTabs[] = array(
			'tab_name' => $value['tab_name'],
			'tab_content' => $value['tab_content'],
			'is_array' => (is_array($value['tab_content'])) ? true : false,
		);
	}
}

$osTemplate->assign('tProfileTabs', $tProfileTabs);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/profile.html');

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->assign('main_content', $main_content);
$osTemplate->caching = 0;
$osTemplate->loadFilter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.'profile.php.html') ? CURRENT_TEMPLATE.'/profile.php.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);

include ('includes/bottom.php');
?>