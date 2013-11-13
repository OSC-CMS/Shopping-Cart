<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

global $main;

$id = (int)$_POST['id'];
$content = os_db_prepare_input($_POST['content']);
$action = os_db_prepare_input($_POST['action']);
$block = $_POST['block'];

$getQuery = os_db_query("
	SELECT 
		* 
	FROM 
		".TABLE_LIKE." LEFT JOIN ".TABLE_LIKE_USERS." ON (l_id = u_like_id) 
	WHERE 
		l_content = '".$content."' AND l_content_id = '".$id."'
");
$qUsers = array();
while($query = os_db_fetch_array($getQuery))
{
	$qUsers[] = $query['u_user_id'];
	$qLikeId = $query['l_id'];
	$qCount = $query['l_count'];
}

$data = array();
$blockResult = '';

if (isset($_SESSION['customer_id']))
{
	if (isset($action) && $action == 'addLike')
	{
		//if ($query['u_user_id'] == $_SESSION['customer_id'])
		if (in_array($_SESSION['customer_id'], $qUsers))
		{
			$lContent = '<span id="likeCount_'.$content.'_'.$id.'"><a class="i-like-it-del" href="javascript://" onclick="setLike('.$id.', \''.$content.'\', \'delLike\')">'.$query['l_count'].'</a></span>';
			$data = getLikeMsg($lContent, 'Действие не выполнимо!', 'jm_message_error');
		}
		else
		{
			if (os_db_num_rows($getQuery) > 0)
			{
				$count = $qCount+1;
				os_db_query("UPDATE ".TABLE_LIKE." SET l_count = ".$count." WHERE l_content = '".$content."' AND l_content_id = ".$id);
				$likeId = $qLikeId;
			}
			else
			{
				$likeNew = array(
					'l_count'		=> 1,
					'l_content'		=> $content,
					'l_content_id'	=> $id
				);
				os_db_perform(TABLE_LIKE, $likeNew);
				$likeId = os_db_insert_id();

				$count = 1;
			}

			$likeUser = array(
				'u_like_id'	=> $likeId,
				'u_user_id'	=> $_SESSION['customer_id'],
				'u_like_date'	=> 'now()'
			);
			os_db_perform(TABLE_LIKE_USERS, $likeUser);

			if (isset($block) && $block == 1)
			{
				include(dirname(__FILE__).'/box_like.php');
				$blockResult = $box_value;
			}

			$lContent = '<span id="likeCount_'.$content.'_'.$id.'"><a class="i-like-it-del" href="javascript://" onclick="setLike('.$id.', \''.$content.'\', \'delLike\')">'.$count.'</a></span>';
			$data = array
			(
				'count'		=> $lContent,
				'msg'		=> 'Лайк добавлен!',
				'msg_type'	=> 'jm_message_success',
				'block'		=> $blockResult,
			);
		}
	}

	if (isset($action) && $action == 'delLike')
	{
		//if ($query['u_user_id'] == $_SESSION['customer_id'])
		if (in_array($_SESSION['customer_id'], $qUsers))
		{
			$count = $qCount-1;
			if ($count != 0)
				os_db_query("UPDATE ".TABLE_LIKE." SET l_count = ".$count." WHERE l_content = '".$content."' AND l_content_id = '".$id."'");
			else
				os_db_query("DELETE FROM ".TABLE_LIKE." WHERE l_id = ".$qLikeId."");

			os_db_query("DELETE FROM ".TABLE_LIKE_USERS." WHERE u_like_id = ".$qLikeId." AND u_user_id = '".(int)$_SESSION['customer_id']."'");

			if (isset($block) && $block == 1)
			{
				include(dirname(__FILE__).'/box_like.php');
				$blockResult = $box_value;
			}

			$lContent = '<span id="likeCount_'.$content.'_'.$id.'"><a class="i-like-it-add" href="javascript://" onclick="setLike('.$id.', \''.$content.'\', \'addLike\')">'.$count.'</a></span>';
			$data = array
			(
				'count'		=> $lContent,
				'msg'		=> 'Лайк удален!',
				'msg_type'	=> 'jm_message_error',
				'block'		=> $blockResult,
			);
		}
		else
		{
			$lContent = '<span id="likeCount_'.$content.'_'.$id.'"><a class="i-like-it-add" href="javascript://" onclick="setLike('.$id.', \''.$content.'\', \'addLike\')">'.$query['l_count'].'</a></span>';
			$data = getLikeMsg($lContent, 'Действие не выполнимо!', 'jm_message_error');
		}
	}
}
else
{
	$lContent = '<span class="iLikeThis"><span class="iLike-guest">'.$qCount.'</span></span>';
	$data = getLikeMsg($lContent, 'Необходимо войти как покупатель!', 'jm_message_error');
}

echo json_encode($data);
die();
?>