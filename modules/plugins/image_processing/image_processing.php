<?php
/*
	Plugin Name: Пакетная обработка изображений
	Plugin URI: http://osc-cms.com/
	Description: Нажмите выполнить для начала пакетной обработки изображений, этот процесс может длиться некоторое время, ничего не трогайте и не прерывайте!
	Version: 1.0.0
	Author: CartET
	Author URI: http://osc-cms.com
	Plugin Group: Products
*/

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

define('MODULE_IMAGE_PROCESS_TEXT_DESCRIPTION', 'Пакетная обработка изображений');
define('MODULE_IMAGE_PROCESS_TEXT_TITLE', 'Пакетная обработка изображений');
define('IMAGE_EXPORT_TYPE','Пакетная обработка');

add_action('process', 'image_processing');
add_action('admin_menu', 'image_processing_menu');

function image_processing_menu()
{
   add_plug_menu(MODULE_IMAGE_PROCESS_TEXT_TITLE, 'plugins.php?module=image_processing&action=process');
}

    function image_processing() 
	{
        include (dir_path_admin('class').FILENAME_IMAGEMANIPULATOR);  
        @os_set_time_limit(0);
        $files=array();

			require_once(dir_path('func_admin') . 'trumbnails_add_funcs.php');
			
			$files = os_get_files_in_dir( dir_path('images_original') );
			
			for ($i=0;$n=sizeof($files),$i<$n;$i++) 
			{

				$products_image_name = $files[$i]['text'];
				if ($files[$i]['text'] != 'Thumbs.db' &&  $files[$i]['text'] != 'Index.html' &&  $files[$i]['text'] != '.svn') 
				{
					require(dir_path_admin('includes') . 'product_thumbnail_images.php');
					require(dir_path_admin('includes') . 'product_info_images.php');
					require(dir_path_admin('includes') . 'product_popup_images.php');
				}
			}
			
			global $messageStack;
			
		   $messageStack->add_session('ok', 'success');

	}


?>