<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

$module = new osTemplate;
$module_content = array ();
$filename = '';
$check_query = osDBquery("SELECT DISTINCT
				products_id
				FROM ".TABLE_PRODUCTS_CONTENT."
				WHERE languages_id='".(int) $_SESSION['languages_id']."'");


$check_data = array ();
$i = '0';
while ($content_data = os_db_fetch_array($check_query,true)) {
	$check_data[$i] = $content_data['products_id'];
	$i ++;
}
if (os_in_array($product->data['products_id'], $check_data)) {
	// get content data

	if (GROUP_CHECK == 'true')
		$group_check = "group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%' AND";

	$content_query = osDBquery("SELECT
					content_id,
					content_name,
					content_link,
					content_file,
					content_read,
					file_comment
					FROM ".TABLE_PRODUCTS_CONTENT."
					WHERE
					products_id='".$product->data['products_id']."' AND
	                ".$group_check."
					languages_id='".(int) $_SESSION['languages_id']."'");

	while ($content_data = os_db_fetch_array($content_query,true)) {
		$filename = '';
		if ($content_data['content_link'] != '') {

			$icon = os_image(DIR_WS_CATALOG.'media/icons/icon_link.gif');
		} else {
			$icon = os_image(DIR_WS_CATALOG.'media/icons/icon_'.str_replace('.', '', strstr($content_data['content_file'], '.')).'.gif');
		}

		if ($content_data['content_link'] != '')
			$filename = '<a href="'.$content_data['content_link'].'" target="new">';
		$filename .= $content_data['content_name'];
		if ($content_data['content_link'] != '')
			$filename .= '</a>';
		$button = '';
		if ($content_data['content_link'] == '') {
			if (preg_match('/.html/i', $content_data['content_file']) or preg_match('/.htm/i', $content_data['content_file']) or preg_match('/.txt/i', $content_data['content_file']) or preg_match('/.bmp/i', $content_data['content_file']) or preg_match('/.jpg/i', $content_data['content_file']) or preg_match('/.gif/i', $content_data['content_file']) or preg_match('/.png/i', $content_data['content_file']) or preg_match('/.tif/i', $content_data['content_file'])) {

			//кнопка
	$_array = array('img' => 'button_view.gif', 
	                                'href' => os_href_link(FILENAME_MEDIA_CONTENT, 'coID='.$content_data['content_id']), 
									'alt' => TEXT_VIEW,
                  /* код готовой кнопки, по умолчанию пусто */									
									'code' => ''
	);
	
	$_array = apply_filter('button_view', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a style="cursor:pointer" onclick="javascript:window.open(\''.$_array['href'].'\', \'popup\', \'toolbar=0, width=640, height=600\')">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}

				$button = $_array['code'];

		//кнопка		-----
			} 
			else 
			{
			
			
						//кнопка
	$_array = array('img' => 'button_download.gif', 
	                                'href' => os_href_link('media/products/'.$content_data['content_file']), 
									'alt' => TEXT_DOWNLOAD,
                  /* код готовой кнопки, по умолчанию пусто */									
									'code' => ''
	);
	
	$_array = apply_filter('button_download', $_array);
	
	if (empty($_array['code']))
	{
	   $_array['code'] = '<a href="'.$_array['href'].'">'.os_image_button($_array['img'], $_array['alt']).'</a>';
	}

$button = $_array['code'];

			}
		}
		$module_content[] = array ('ICON' => $icon, 'FILENAME' => $filename, 'DESCRIPTION' => $content_data['file_comment'], 'FILESIZE' => os_filesize($content_data['content_file']), 'BUTTON' => $button, 'HITS' => $content_data['content_read']);
	}

	$module->assign('language', $_SESSION['language']);
	$module->assign('module_content', $module_content);
	// set cache ID

		$module->caching = 0;
		$module = $module->fetch(CURRENT_TEMPLATE.'/module/products_media.html');

	$info->assign('MODULE_products_media', $module);
}

?>