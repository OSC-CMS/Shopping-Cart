<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

if (!isset($lng) && @!is_object($lng)) 
{
	include(_CLASS . 'language.php');
	$lng = new language;
}

$languages_string = '';
$count_lng = '';
reset($lng->catalog_languages);

while (list($key, $value) = each($lng->catalog_languages)) 
{
	$count_lng++;
	if ($value['status'] == 1) //Показывать только активные языки
	{
		$active = ($_SESSION['language'] == $value['code']) ? 'class="active"' : '';
		$languages_string .= '<li '.$active.'><a href="'.os_href_link(basename($PHP_SELF), 'language='.$key.'&'.os_get_all_get_params(array('language', 'currency')), $request_type).'">'.$value['name'].'</a></li>'; 
	}
}

// dont show box if there's only 1 language
if ($count_lng > 1)
{
	$box = new osTemplate;
	$box_content='';
	$box->assign('BOX_CONTENT', $languages_string);
	$box->assign('language', $_SESSION['language']);
	// set cache ID
	$box->caching = 0;
	$box_languages= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_languages.html');
	$osTemplate->assign('box_LANGUAGES',$box_languages);
}
?>