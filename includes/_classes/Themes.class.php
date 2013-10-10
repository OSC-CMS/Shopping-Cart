<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiThemes extends CartET
{
	/**
	 * Возвращает содержимое файла шаблона
	 */
	public function getTemplateFileContent($params)
	{
		if (empty($params)) return false;

		$file = dir_path('themes').$params['theme'].'/'.$params['file'];

		if (is_readable($file))
		{
			$content = file_get_contents($file);
			$content = htmlspecialchars($content);
			return $content;
		}
		else
			return false;
	}

	/**
	 * Сохранние файла шаблона
	 */
	public function saveTemplateFile($params)
	{
		if (empty($params)) return false;

		$file = dir_path('themes').$params['theme'].$params['file'];
		$content = $params['content'];

		if (is_file($file) && is_writeable($file))
		{
			$content = str_replace('\"', '"', $content);
			$content = str_replace("\'", "'", $content);
			file_put_contents($file, $content);
			$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');
		}
		else
			$data = array('msg' => 'Файл не существует, либо закрыт для записи!', 'type' => 'error');

		return $data;
	}
}
?>