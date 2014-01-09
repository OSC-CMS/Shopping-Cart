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

	public function checkUpdate($cached = false)
	{
		$current_version = '1.1.0';

		$aUpdateInfo = $this->getData($current_version, $cached);

		if (!$aUpdateInfo)
		{
			return false;
		}

		$aUpdateInfo = json_decode($aUpdateInfo, true);

		$new_version = $aUpdateInfo['version'];

		if (version_compare($new_version, $current_version, '<='))
		{
			@unlink($this->cache_file);
			return false;
		}

		return $aUpdateInfo;
	}

	public function getData($current_version, $cached)
	{
		if (file_exists($this->cache_file))
			return file_get_contents($this->cache_file);
		else if ($cached)
			return false;

		$url = $this->api_url.'?update='.$current_version;

		$data = get_contents_from_url($url);

		if ($data === false)
		{
			return false;
		}

		file_put_contents($this->cache_file, $data);

		return $data;
	}
}

function get_contents_from_url($url)
{
	$data = @file_get_contents($url);

	if ($data === false)
	{
		if (function_exists('curl_init'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$data = curl_exec($curl);
			curl_close($curl);
		}
	}

	return $data;
}