<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

global $osTemplate, $breadcrumb, $product;

function like_title($value)
{
	$value = 'Закладки - '.$value;
	return $value;
}

$p_url_like = ($_GET['like']) ? '&like='.$_GET['like'] : '';
$p_url = os_href_link('index.php?page=likes'.$p_url_like);

if (isset($_GET['del_like']) && !empty($_GET['del_like']))
{
	deleteLike($_GET['del_like'], $_SESSION['customer_id']);
	os_redirect($p_url);
}

require(dir_path('includes') . 'header.php');

$breadcrumb->add('Закладки', $p_url);

// Заголовок
$title = getLikeName($_GET['like']);

if (isset($_GET['like']) && !empty($_GET['like']))
{
	$breadcrumb->add($title, $p_url);
}

$osTemplate->assign('navtrail', $breadcrumb->trail(' &raquo; '));

// Если товары
if (isset($_GET['like']) && $_GET['like'] == 'products')
{
	$osTemplate->assign('likeProducts', getLikeProducts($_SESSION['customer_id']));
}

// Если новости
if (isset($_GET['like']) && $_GET['like'] == 'news')
{
	$osTemplate->assign('likeNews', getLikeNews($_SESSION['customer_id']));
}

$getLike = getLikeByUserId($_SESSION['customer_id']);

$likeArray = array ();
if (os_db_num_rows($getLike) > 0)
{
	while ($like = os_db_fetch_array($getLike)) 
	{
		$like['content'] = getLikeName($like['l_content']);
		$likeArray[] = $like;
	}
}

$osTemplate->assign('likeArray', $likeArray);

$osTemplate->assign('title', (($title) ? $title : ''));
$osTemplate->assign('url', $p_url);

// отправляем язык в шаблон
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(dirname(__FILE__).'/themes/default/page_like.html');
$osTemplate->assign('main_content', $main_content);
$osTemplate->loadFilter('output', 'trimhitespace');
$osTemplate->display(CURRENT_TEMPLATE.'/index.html');
require(dir_path('includes') . 'bottom.php');
?>