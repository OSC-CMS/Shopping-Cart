<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiAdmin extends CartET
{
	private static $_setting = array();

	public function getAllSetting()
	{
		if (!empty(self::$_setting))
		{
			return self::$_setting;
		}
		else
		{
			$getSetting = os_db_query("SELECT * FROM ".DB_PREFIX."admin_setting");
			if (os_db_num_rows($getSetting) > 0)
			{
				while($s = os_db_fetch_array($getSetting))
				{
					self::$_setting[$s['group']][$s['name']] = array('setting' => $s['value'], 'type' => $s['setting_type']);
				}
			}
		}

		return self::$_setting;
	}

	public function getSettingGroup($group = 'index2')
	{
		$aSetting = $this->getAllSetting();
		return ($aSetting[$group]) ? $aSetting[$group] : false;
	}

	public function saveIndex2($params)
	{
		if (empty($params)) return false;

		foreach($params AS $name => $value)
		{
			os_db_perform(DB_PREFIX."admin_setting", array('value' => os_db_input($value)), 'update', "name = '".os_db_input($name)."'");
		}

		return $params;
	}
}