<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

/*
	Получение имени типа контента
*/
function getLikeName($value)
{
	include(CONTENT_LIKE_DIR.$_SESSION['language'].'.php');
	return $lang[$value];
}

/*
	Массив сообщений
*/
function getLikeMsg($count, $msg, $msg_type)
{
	return array
	(
		'count'		=> $count,
		'msg'		=> $msg,
		'msg_type'	=> $msg_type,
	);
}

/*
	Удалить лайк по id
*/
function deleteLike($like_id, $cid)
{
	$getQuery = os_db_query(" SELECT l_count, u_user_id FROM ".TABLE_LIKE." JOIN ".TABLE_LIKE_USERS." ON (l_id = u_like_id) WHERE l_id = '".(int)$like_id."'");
	$query = os_db_fetch_array($getQuery);

	if ($query['u_user_id'] == $cid)
	{
		if ($query['l_count'] != '0')
			os_db_query("UPDATE ".TABLE_LIKE." SET l_count = l_count-1 WHERE l_id = ".(int)$like_id."");
		else
			os_db_query("DELETE FROM ".TABLE_LIKE." WHERE l_id = ".(int)$like_id."");

		os_db_query("DELETE FROM ".TABLE_LIKE_USERS." WHERE u_like_id = ".(int)$like_id." AND u_user_id = '".(int)$cid."'");
		return true;
	}
}

/*
	Получить лайки по типу
*/
function getLikeByType($type, $cid, $limit = '')
{
	// товары
	if (isset($type) && $type == 'products')
	{
		$getLike = os_db_query("
			SELECT 
				* 
			FROM 
				".TABLE_LIKE." 
					JOIN ".TABLE_LIKE_USERS." ON (l_id = u_like_id) 
					JOIN ".TABLE_PRODUCTS." p ON (l_content_id = p.products_id) 
					JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd ON (p.products_id = pd.products_id AND l_content_id = pd.products_id) 
			WHERE 
				u_user_id = '".(int)$cid."' AND 
				l_id = u_like_id AND 
				l_content = '".os_db_prepare_input($type)."' AND 
				p.products_status = '1' AND 
				pd.language_id = '".(int)$_SESSION['languages_id']."' 
			".(($limit) ? "LIMIT ".$limit : '')."
		");
	}
	elseif (isset($type) && $type == 'news')
	{
//osDBquery("SELECT * FROM ".TABLE_LATEST_NEWS." WHERE status = '".(int)$status."' AND language = '".(int)$lang."' AND news_id = ".(int)$news_id." LIMIT 1");
		$getLike = os_db_query("
			SELECT 
				* 
			FROM 
				".TABLE_LIKE." 
					JOIN ".TABLE_LIKE_USERS." ON (l_id = u_like_id) 
					JOIN ".TABLE_LATEST_NEWS." n ON (l_content_id = n.news_id) 
			WHERE 
				u_user_id = '".(int)$cid."' AND 
				l_id = u_like_id AND 
				l_content = '".os_db_prepare_input($type)."' AND 
				n.status = '1' AND 
				n.language = '".(int)$_SESSION['languages_id']."' 
			".(($limit) ? "LIMIT ".$limit : '')."
		");
	}
	return $getLike;
}

/*
	Возвращает товары
*/
function getLikeProducts($customer_id, $limit = '')
{
	global $product;

	$getLikeByType = getLikeByType('products', $customer_id, $limit);
	$likeProducts = array();
	$aLike = array();
	if (os_db_num_rows($getLikeByType) > 0)
	{
		while ($like = os_db_fetch_array($getLikeByType)) 
		{
			$likeProducts[$like['products_id']] = $product->buildDataArray($like);
			$aLike[$like['products_id']] = $like;
		}

		if (!empty($likeProducts))
		{
			foreach ($likeProducts AS $pid => $values)
			{
				$values['l_count'] = $aLike[$pid]['l_count'];
				$values['l_content'] = $aLike[$pid]['l_content'];
				$values['l_content_id'] = $aLike[$pid]['l_content_id'];
				$values['u_id'] = $aLike[$pid]['u_id'];
				$values['u_like_id'] = $aLike[$pid]['u_like_id'];
				$values['u_user_id'] = $aLike[$pid]['u_user_id'];
				$values['u_like_date'] = $aLike[$pid]['u_like_date'];
				$likeProducts[$pid] = $values;
			}
		}
	}

	return $likeProducts;
}

/*
	Возвращает новости
*/
function getLikeNews($customer_id, $limit = '')
{
	global $cartet;

	$getLikeByType = getLikeByType('news', $customer_id, $limit);
	$likeNews = array();
	$aLike = array();
	if (os_db_num_rows($getLikeByType) > 0)
	{
		while ($like = os_db_fetch_array($getLikeByType)) 
		{
			$likeNews[$like['news_id']] = $cartet->news->getData($like);//$product->buildDataArray($like);
			$aLike[$like['news_id']] = $like;
		}

		if (!empty($likeNews))
		{
			foreach ($likeNews AS $nid => $values)
			{
				$values['l_count'] = $aLike[$nid]['l_count'];
				$values['l_content'] = $aLike[$nid]['l_content'];
				$values['l_content_id'] = $aLike[$nid]['l_content_id'];
				$values['u_id'] = $aLike[$nid]['u_id'];
				$values['u_like_id'] = $aLike[$nid]['u_like_id'];
				$values['u_user_id'] = $aLike[$nid]['u_user_id'];
				$values['u_like_date'] = $aLike[$nid]['u_like_date'];
				$likeNews[$nid] = $values;
			}
		}
	}

	return $likeNews;
}

/*
	Получить лайки по пользователю
*/
function getLikeByUserId($cid)
{
	$getLike = os_db_query("
		SELECT 
			COUNT(l_content) cnt, l_id, l_count, l_content, l_content_id, u_id, u_like_id, u_user_id 
		FROM 
			".TABLE_LIKE." 
				LEFT JOIN ".TABLE_LIKE_USERS." ON (l_id = u_like_id) 
		WHERE 
			u_user_id = '".(int)$cid."' AND l_id = u_like_id 
		GROUP BY 
			l_content
	");
	return $getLike;
}

/*
	Получить лайки по id b типу
*/
function getLikeById($pid, $type)
{
	$getLike = os_db_query("
		SELECT 
			* 
		FROM 
			".TABLE_LIKE_USERS." 
				LEFT JOIN ".TABLE_LIKE." ON (l_id = u_like_id) 
		WHERE 
			l_content = '".os_db_prepare_input($type)."' AND l_content_id = '".(int)$pid."' 
	");
	return $getLike;
}












?>