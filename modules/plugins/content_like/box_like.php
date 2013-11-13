<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

global $osTemplate;
$tpl = new osTemplate;
$tpl->template_dir = plugdir();

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

$tpl->assign('likeArray', $likeArray);

$tpl->caching = 0;
$tpl->cache_lifetime = 0;
$box_value = $tpl->fetch('themes/default/box_like.html');
$osTemplate->assign('box_like', $box_value);
?>