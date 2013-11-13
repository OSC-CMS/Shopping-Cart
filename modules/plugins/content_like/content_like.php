<?php
/*
	Plugin Name: Закладки
	Plugin URI: http://osc-cms.com/extend/plugins
	Version: 1.3
	Description: Плагин дает возможность покупателям сохранять товары, новости, статьи и отзывы в закладки
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Content
*/

global $p;
if($p->info['content_like']['status'] == 1)
{
	define("TABLE_LIKE", DB_PREFIX."like");
	define("TABLE_LIKE_USERS", DB_PREFIX."like_users");
	define('CONTENT_LIKE_DIR', dirname(__FILE__).'/');

	include (dirname(__FILE__).'/func_like.php');
}

add_action('head', 'head_like');
add_filter('build_products', 'products_like');
add_filter('build_news', 'news_like');
add_action('products_info',	'products_info_like');
add_action('box', 'box_like');
add_action('page', 'page_like');
add_action('page', 'likes');
add_filter('title', 'like_title');
add_filter('profile_add_tabs', 'likes_profile_tabs');

/*
	HEAD
*/
function head_like()
{
	_e('<link href="'.plugurl().'css/content_like.css" rel="stylesheet" type="text/css" />');
	_e('<script type="text/javascript">
function setLike(idLike, contentLike, actionLike)
{
	$.ajax({
		url: "'._HTTP.'index.php?page=page_like",
		type: "post",
		cache: false,
		data: {id: idLike, content: contentLike, action: actionLike, block: 1},
		dataType: "json",
		beforeSend: function()
		{
			$("#like_box").css("opacity","0.5"); // Добавляем прозрачности блоку
			$("#likeId_"+contentLike+"_"+idLike).addClass("hidden");
		},
		success: function(data)
		{
			//$.jmessage(data.msg, 6000, data.msg_type);
			$("#likeCount_"+contentLike+"_"+idLike).html(data.count);
			$("#like_box").html(data.block); // Обновляем блок
			$("#like_box").css("opacity", "1"); // Убираем прозрачность
		}
	});
}
</script>');
}

/*
	PRODUCTS
*/
function products_like($value)
{
	$products_id = @$value['PRODUCTS_ID'];
	$content_type = 'products';

	if (empty($products_id)) return $value;

	$getLike = getLikeById($products_id, $content_type);
	$uId = array();
	while($like = os_db_fetch_array($getLike))
	{
		$uId[] = $like['u_user_id'];
		$count = $like['l_count'];
	}
	$count = (!empty($count)) ? $count : 0;

	if (isset($_SESSION['customer_id']))
	{
		if (in_array($_SESSION['customer_id'], $uId))
			$value['like'] = '<span class="iLikeThis"><span id="likeCount_'.$content_type.'_'.$products_id.'"><a title="Добавить в любимые | Убрать из любимых" class="i-like-it-del" href="javascript://" onclick="setLike('.$products_id.', \''.$content_type.'\', \'delLike\')">'.$count.'</a></span></span>';
		else
			$value['like'] = '<span class="iLikeThis"><span id="likeCount_'.$content_type.'_'.$products_id.'"><a title="Добавить в любимые | Убрать из любимых" class="i-like-it-add" href="javascript://" onclick="setLike('.$products_id.', \''.$content_type.'\', \'addLike\')">'.$count.'</a></span></span>';
	}
	else
	{
		$value['like'] = '<span title="Любимый товар" class="iLikeThis"><span class="iLike-guest">'.$count.'</span></span>';
	}
	return $value;
}

/*
	NEWS
*/
function news_like($value)
{
	$news_id = @$value['news_id'];
	$content_type = 'news';

	if (empty($news_id)) return $value;

	$getLike = getLikeById($news_id, $content_type);
	$uId = array();
	while($like = os_db_fetch_array($getLike))
	{
		$uId[] = $like['u_user_id'];
		$count = $like['l_count'];
	}
	$count = (!empty($count)) ? $count : 0;

	if (isset($_SESSION['customer_id']))
	{
		if (in_array($_SESSION['customer_id'], $uId))
			$value['like'] = '<span class="iLikeThis"><span id="likeCount_'.$content_type.'_'.$news_id.'"><a title="Добавить в любимые | Убрать из любимых" class="i-like-it-del" href="javascript://" onclick="setLike('.$news_id.', \''.$content_type.'\', \'delLike\')">'.$count.'</a></span></span>';
		else
			$value['like'] = '<span class="iLikeThis"><span id="likeCount_'.$content_type.'_'.$news_id.'"><a title="Добавить в любимые | Убрать из любимых" class="i-like-it-add" href="javascript://" onclick="setLike('.$news_id.', \''.$content_type.'\', \'addLike\')">'.$count.'</a></span></span>';
	}
	else
	{
		$value['like'] = '<span title="Любимая новость" class="iLikeThis"><span class="iLike-guest">'.$count.'</span></span>';
	}
	return $value;
}

/*
	PRODUCTS INFO
*/
function products_info_like()
{
	global $product;

	$products_id = $product->data['products_id'];
	$content_type = 'products';

	if (empty($products_id)) return $value;

	$getLike = getLikeById($products_id, $content_type);
	$uId = array();
	while($like = os_db_fetch_array($getLike))
	{
		$uId[] = $like['u_user_id'];
		$count = $like['l_count'];
	}
	$count = (!empty($count)) ? $count : 0;

	if (isset($_SESSION['customer_id']))
	{
		if (in_array($_SESSION['customer_id'], $uId))
			$result = '<span class="iLikeThis"><span id="likeCount_'.$content_type.'_'.$products_id.'"><a title="Добавить в любимые | Убрать из любимых" class="i-like-it-del" href="javascript://" onclick="setLike('.$products_id.', \''.$content_type.'\', \'delLike\')">'.$count.'</a></span></span>';
		else
			$result = '<span class="iLikeThis"><span id="likeCount_'.$content_type.'_'.$products_id.'"><a title="Добавить в любимые | Убрать из любимых" class="i-like-it-add" href="javascript://" onclick="setLike('.$products_id.', \''.$content_type.'\', \'addLike\')">'.$count.'</a></span></span>';
	}
	else
	{
		$result = '<span title="Любимый товар" class="iLikeThis"><span class="iLike-guest">'.$count.'</span></span>';
	}

	return array('name' => 'like', 'value' => $result);
}


/*
	BOX
*/
function box_like()
{
	include 'box_like.php';
}


/*
	PROFILE
*/
function likes_profile_tabs($value)
{
	global $osTemplate, $product;
	$tpl = new osTemplate;
	$tpl->template_dir = plugdir();

	$cId = $value['param']['customers_id'];

	$tpl->assign('likeProducts', getLikeProducts($cId, 5));
	$tpl->assign('likeNews', getLikeNews($cId, 5));

	$tpl->caching = 0;
	$tpl->cache_lifetime = 0;

	$box_value = $tpl->fetch('themes/default/profile_like.html');

    $value['values'][] =  array
    (
        'tab_name' => 'Закладки',
        'tab_content' => $box_value,
    );

    return $value;
}

/*
	PAGE
*/
function page_like()
{
	include(dirname(__FILE__).'/page_like.php');
}

/*
	PAGE
*/
function likes()
{
	include(dirname(__FILE__).'/likes.php');
}

/*
	INSTALL
*/
function content_like_install()
{
	os_db_query('DROP TABLE IF EXISTS '.DB_PREFIX.'like;');
	os_db_query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."like` (
		`l_id` int(11) NOT NULL AUTO_INCREMENT,
		`l_count` int(11) DEFAULT NULL,
		`l_content` varchar(255) NOT NULL,
		`l_content_id` int(11) NOT NULL,
	PRIMARY KEY (`l_id`,`l_content_id`)
	)ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;");

	os_db_query('DROP TABLE IF EXISTS '.DB_PREFIX.'like_users;');
	os_db_query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."like_users` (
		`u_id` int(11) NOT NULL AUTO_INCREMENT,
		`u_like_id` int(11) DEFAULT NULL,
		`u_user_id` int(11) DEFAULT NULL,
		`u_like_date` DATETIME NOT NULL,
	PRIMARY KEY (`u_id`,`u_like_id`)
	)ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;");
}

/*
	UNINSTALL
*/
function content_like_remove()
{
	os_db_query('DROP TABLE IF EXISTS '.DB_PREFIX.'like;');
	os_db_query('DROP TABLE IF EXISTS '.DB_PREFIX.'like_users;');
}
?>