<?php
$_config = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))))).'/includes/top.php';
include($_config);

// орнева€ директори€ сайта
define('DIR_ROOT',		_CATALOG);
//ƒиректори€ с изображени€ми (относительно корневой)
define('DIR_IMAGES',	'images');
//ƒиректори€ с файлами (относительно корневой)
define('DIR_FILES',		'images');
//¬ысота и ширина картинки до которой будет сжато исходное изображение и создана ссылка на полную версию
define('WIDTH_TO_LINK', 500);
define('HEIGHT_TO_LINK', 500);
//јтрибуты которые будут присвоены ссылке (дл€ скриптов типа lightbox)
define('CLASS_LINK', 'lightview');
define('REL_LINK', 'lightbox');

date_default_timezone_set('Europe/Moscow');
?>
