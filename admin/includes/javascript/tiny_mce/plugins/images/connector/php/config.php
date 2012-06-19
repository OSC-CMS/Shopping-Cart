<?php
$_config = dirname(dirname(dirname(dirname(dirname((dirname(dirname(dirname(__FILE__))))))))).'/includes/top.php';
include($_config);

//Корневая директория сайта
define('DIR_ROOT',		_CATALOG);
//Директория с изображениями (относительно корневой)
define('DIR_IMAGES',	'images');
//Директория с файлами (относительно корневой)
define('DIR_FILES',		'images');


//Высота и ширина картинки до которой будет сжато исходное изображение и создана ссылка на полную версию
define('WIDTH_TO_LINK', 500);
define('HEIGHT_TO_LINK', 500);

//Атрибуты которые будут присвоены ссылке (для скриптов типа lightbox)
define('CLASS_LINK', 'lightview');
define('REL_LINK', 'lightbox');

date_default_timezone_set('Asia/Yekaterinburg');

?>
