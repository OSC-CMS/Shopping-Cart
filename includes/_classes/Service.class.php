<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiService extends CartET
{
	private $api_url = 'http://api.cartet.org/';

	private $cache_file = 'update.dat';

	public function __construct()
	{
		$this->cache_file = DIR.DIR_FS_CACHE.'/system/'.$this->cache_file;
	}

	/**
	 * Возвращает версию CartET
	 */
	public function getVersion()
	{
		if (is_file(dir_path('catalog').'VERSION'))
			$_version = @file_get_contents(dir_path('catalog').'VERSION');
		else
			$_version = ' --- ';

		return $_version;
	}

	/**
	 * Возвращает список плагинов
	 */
	protected function getPlugins()
	{
		global $p;
		$aPlugins = $p->plug_array();

		$_newPluginsList = array();
		foreach($aPlugins AS $_g)
		{
			foreach($_g AS $_p)
			{
				$_newPluginsList[] = implode(':', array($_p['name'], $_p['version']));
			}
		}

		return implode(';', $_newPluginsList);
	}

	/**
	 * Возвращает список шаблонов
	 */
	protected function getThemes()
	{
		$templates_array = array();
		if ($dir = opendir(DIR_FS_CATALOG.'themes/'))
		{
			while (($templates = readdir($dir)) !== false)
			{
				if (is_dir(DIR_FS_CATALOG.'themes/'."//".$templates)  & ($templates != ".") && ($templates != "..") && ($templates != ".svn") )
				{
					$templates_array[] = $templates;
				}
			}
			closedir($dir);
			sort($templates_array);
		}

		return implode(';', $templates_array);
	}

	/**
	 * Проверяет обновления
	 */
	public function checkUpdate($cached = false)
	{
		$result = array(
			'action' => 'update',
			'host' => $_SERVER['HTTP_HOST'],
			'version' => '1.1.0',
			'plugins' => $this->getPlugins(),
			'themes' => $this->getThemes(),
		);

		$aUpdateInfo = $this->getData($result, $cached);

		if (!$aUpdateInfo)
		{
			return false;
		}

		return json_decode($aUpdateInfo, true);
	}

	/**
	 * Проверяет версию ядра
	 */
	public function checkCore($version)
	{
		if (version_compare($version, $this->getVersion(), '<='))
		{
			@unlink($this->cache_file);
			return false;
		}

		return true;
	}

	/**
	 * Запрос обновлений
	 */
	public function getData($params, $cached)
	{
		// Если уже есть файл кэша, то возвращаем данные из него
		if (file_exists($this->cache_file))
			return file_get_contents($this->cache_file);
		else if ($cached)
			return false;

		$data = $this->getUrl($this->api_url, $params);

		if ($data === false)
		{
			return false;
		}

		// Если что-то вернулось, то пишем в файл кэша
		file_put_contents($this->cache_file, $data);

		return $data;
	}

	/**
	 * Обработка запроса обновлений
	 */
	protected function getUrl($url, $params)
	{
		if (function_exists('curl_init'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

			$result = curl_exec($curl);
			if (curl_errno($curl))
			{
				curl_close($curl);
				return false;
			}
			curl_close($curl);
			return $result;
		}
		else
			return false;
	}
}