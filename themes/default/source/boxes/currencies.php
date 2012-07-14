<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

if (isset($osPrice) && is_object($osPrice)) 
{
	$currencies_string = '';
	$count_cur='';
	reset($osPrice->currencies);
	$curArray = array();
	while (list($key, $value) = each($osPrice->currencies))
	{
		$count_cur++;
		$curArray[] = array(
			'key' => $key,
			'link' => os_href_link(basename($PHP_SELF), 'currency='.$key.'&'.os_get_all_get_params(array('language', 'currency')), $request_type),
			'title' => $value['title'],
		);
	}

	$hidden_get_variables = '';
	reset($_GET);
	while (list($key, $value) = each($_GET)) 
	{
		if (($key != 'currency') && ($key != os_session_name()) && ($key != 'x') && ($key != 'y'))
		{
			$hidden_get_variables .= os_draw_hidden_field($key, $value);
		}
	}
}

// dont show box if there's only 1 currency
if ($count_cur > 1 )
{
	$box->assign('curArray', $curArray);
	$box->assign('current', $_SESSION['currency']);
	$box->assign('hidden_get_variables', $hidden_get_variables);
	$box->assign('language', $_SESSION['language']);
	// set cache ID
	if (!CacheCheck())
	{
		$box->caching = 0;
		$box_currencies= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_currencies.html');
	}
	else
	{
		$box->caching = 1;	
		$box->cache_lifetime=CACHE_LIFETIME;
		$box->cache_modified_check=CACHE_CHECK;
		$cache_id = $_SESSION['language'].$_SESSION['currency'];
		$box_currencies= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_currencies.html',$cache_id);
	}

	$osTemplate->assign('box_CURRENCIES',$box_currencies);
}
?>