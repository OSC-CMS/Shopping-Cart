<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiSms extends CartET
{
	private static $aSetting = array();

	private static $aSms = array();

	/**
	 * СМС настройки
	 */
	public function setting()
	{
		if (!empty(self::$aSetting))
		{
			return self::$aSetting;
		}

		$getSettingQuery = os_db_query("SELECT * FROM ".DB_PREFIX."sms_setting WHERE sms_id = 1");
		$setting = os_db_fetch_array($getSettingQuery);

    	self::$aSetting = $setting;
    
    	return $setting;
	}

	/**
	 * СМС по умолчанию
	 */
	public function getDefaultSms()
	{
		if (!empty(self::$aSms))
		{
			return self::$aSms;
		}

		$setting = $this->setting();

		if (!$setting['sms_default_id']) return false;

		$getSmsQuery = os_db_query("SELECT * FROM ".DB_PREFIX."sms WHERE id = '".(int)$setting['sms_default_id']."'");
		$getSms = os_db_fetch_array($getSmsQuery);

		$url = $getSms['url'];
		$password = ($getSms['password_md5'] == 1) ? md5(urlencode($getSms['password'])) : urlencode($getSms['password']);

		$url = str_replace('{login}', urlencode($getSms['login']), $url);
		$url = str_replace('{password}', $password, $url);
		$url = str_replace('{api_id}', urlencode($getSms['api_id']), $url);
		$url = str_replace('{api_key}', urlencode($getSms['api_key']), $url);
		$url = str_replace('{title}', urlencode($getSms['title']), $url);
		$url = str_replace('{status}', urlencode($getSms['status']), $url);

		$getSms['url'] = $url;

		self::$aSms = $getSms;
	
		return $getSms;
	}

	/**
	 * Отправка СМС сообщения
	 */
	public function send($text, $phone = '')
	{
		if (!$text) return false;

		$getDefaultSms = $this->getDefaultSms();

		$phone = ($phone) ? $phone : $getDefaultSms['phone'];

		$url = $getDefaultSms['url'];
		$url = str_replace('{phone}', urlencode($phone), $url);
		$url = str_replace('{text}', urlencode($text), $url);
		$url = "http://".$url;

		if (strstr($url, 'http://sms.ru/')) { $url = $url.'&partner_id=30401'; }

		$result = file_get_contents($url);

		return $result;
	}

	/**
	 * функция передачи сообщения wwwsms.ru
	 * $this->send_wwwsms("api.wwwsms.ru", 80, $this->login, $this->password, $this->phone, $this->msg, $this->status)
	 */
	public function send_wwwsms($host, $port, $login, $password, $phone, $text, $sender = false, $wapurl = false)
	{
		$fp = fsockopen($host, $port, $errno, $errstr);
		if (!$fp) {
			return "errno: $errno \nerrstr: $errstr\n";
		}
		fwrite($fp, "GET /send/" .
			"?phone=" . rawurlencode($phone) .
			"&text=" . rawurlencode($text) .
			($sender ? "&sender=" . rawurlencode($sender) : "") .
			($wapurl ? "&wapurl=" . rawurlencode($wapurl) : "") .
			"  HTTP/1.0\n");
		fwrite($fp, "Host: " . $host . "\r\n");
		if ($login != "") {
			fwrite($fp, "Authorization: Basic " . 
				base64_encode($login. ":" . $password) . "\n");
		}
		fwrite($fp, "\n");
		$response = "";
		while(!feof($fp)) {
			$response .= fread($fp, 1);
		}
		fclose($fp);
		list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
		return $responseBody;
	}

	/**
	 * Сохранение настроек
	 */
	public function saveSetting($params)
	{
		if (empty($params)) return false;

		$aSms = array();
		foreach($params['sms'] AS $key => $val)
		{
			$aSms[$key] = os_db_prepare_input($val);
		}

		os_db_perform(DB_PREFIX."sms_setting", $aSms, 'update', 'sms_id = 1');

		$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Сохранение СМС сервиса
	 */
	public function save($params)
	{
		if (empty($params)) return false;

		$aSms = array();
		foreach($params['sms'] AS $key => $val)
		{
			$aSms[$key] = os_db_prepare_input($val);
		}

		if ($params['action'] == 'add')
			os_db_perform(DB_PREFIX."sms", $aSms);
		else
			os_db_perform(DB_PREFIX."sms", $aSms, 'update', "id = '".(int)$params['id']."'");

		$data = array('msg' => 'Успешно сохранено!', 'type' => 'ok');

		return $data;
	}

	/**
	 * Удаление СМС сервиса
	 */
	public function delete($params)
	{
		if (!isset($params)) return false;
		$id = (is_array($params)) ? $params['id'] : $params;
		
		os_db_query("DELETE FROM ".DB_PREFIX."sms WHERE id = '".(int)$id."'");

		$data = array('msg' => 'Успешно удалено!', 'type' => 'ok');

		return $data;
	}
}


?>