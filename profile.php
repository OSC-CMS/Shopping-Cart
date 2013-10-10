<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
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
		p.customers_id, p.customers_signature, p.show_gender, p.show_firstname, p.show_secondname, p.show_lastname, p.show_dob, p.show_email, p.show_telephone, p.show_fax, p.customers_wishlist, p.customers_avatar, p.customers_photo, c.customers_id, c.customers_gender, c.customers_firstname, c.customers_secondname, c.customers_lastname, c.customers_dob, c.customers_email_address, c.customers_telephone, c.customers_fax, c.customers_date_added, c.customers_last_modified, c.login_time, c.customers_username, customers_status_name
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

		$titleName = (!empty($profile['customers_username'])) ? $profile['customers_username'] : $profile['customers_firstname'];

		$avatar = (!empty($profile['customers_avatar'])) ? $profile['customers_avatar'] : 'noavatar.gif';
		$profile['customers_avatar'] = '<img src="'.http_path('images').'avatars/'.$avatar.'" alt="'.$titleName.'" title="'.$titleName.'" />';

		$customers_dob = explode(' ', $profile['customers_last_modified']);
		$profile['customers_last_modified'] = $customers_dob[0];

		// Отзывы покупателя
		$reviews_query_raw = osDBquery("
			SELECT 
				r.reviews_id, rd.reviews_text, r.reviews_rating, r.date_added, r.status, p.products_id, pd.products_name, p.products_image, r.customers_id, r.customers_name 
			FROM 
				".TABLE_REVIEWS." r, ".TABLE_REVIEWS_DESCRIPTION." rd, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
			WHERE 
				p.products_status = '1' AND 
				p.products_id = r.products_id AND 
				r.reviews_id = rd.reviews_id AND 
				r.status = '1' AND 
				r.customers_id = '".$id."' AND
				p.products_id = pd.products_id AND 
				pd.language_id = '".(int) $_SESSION['languages_id']."' AND 
				rd.languages_id = '".(int) $_SESSION['languages_id']."' 
			ORDER BY 
				r.reviews_id DESC
		");

		if (os_db_num_rows($reviews_query_raw, true))
		{
			$cReviews = array();
			while ($reviews = os_db_fetch_array($reviews_query_raw))
			{
				$products_image = dir_path('images_thumbnail').$reviews['products_image'];

				if (!is_file($products_image))
					$products_image = http_path('images_thumbnail').'../noimage.gif';
				else
					$products_image = http_path('images_thumbnail').$reviews['products_image'];

				$cReviews[] = array
				(
					'PRODUCTS_IMAGE' => $products_image,
					'PRODUCTS_LINK' => os_href_link(FILENAME_PRODUCT_INFO, os_product_link($reviews['products_id'], $reviews['products_name'])),
					'PRODUCTS_NAME' => $reviews['products_name'],
					'DATE' => $reviews['date_added'],
					'TEXT' => nl2br(htmlspecialchars($reviews['reviews_text'])),
					'RATING' => os_image('themes/'.CURRENT_TEMPLATE.'/img/stars_'.$reviews['reviews_rating'].'.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating']))
				);
			}

			$osTemplate->assign('cReviews', $cReviews);
		}

		// Хлебные крошки
		$breadcrumb->add('Профиль '.$titleName.'', customerProfileLink($profile['customers_username'], $id));
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
	$count = 0;
	foreach ($aProfileTabs['values'] as $num => $value)
	{
		$count++;
		$tProfileTabs[] = array(
			'tab_name' => $value['tab_name'],
			'tab_content' => $value['tab_content'],
			'count' => $count,
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