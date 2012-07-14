<?php
/*
	Plugin Name: Настройки шаблона
	Plugin URI: http://osc-cms.com/extend/themes
	Version: 1.0
	Description: Плагин управления некоторыми функциями шаблона. Необходим для корректной работы шаблона Default!
	Author: OSC-CMS
	Author URI: http://osc-cms.com
*/

// Стилизация "хлебных крошек"
add_filter('trail_string', 'trail_string_func');

function trail_string_func($array)
{
	$srt = str_replace('</a></li>', '</a><span class="divider">/</span></li>', $array);
	return $srt;
}
?>