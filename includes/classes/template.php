<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require_once (_LIB.'smarty/Smarty.class.php');

class osTemplate extends Smarty
{
	function __construct()
	{
		parent::__construct();

		$this->setTemplateDir(_THEMES);
		$this->setCacheDir(_CACHE.'cache/');
		$this->setCompileDir(_CACHE.'compiled/');
		$this->setConfigDir(_LANG);
		$this->compile_check = TEMPLATE_COMPILE_CHECK;
		$this->setPluginsDir(_LIB.'smarty/plugins');

		$this->assign('index', http_path('catalog'));
		$this->assign('tpl_dir', _THEMES.CURRENT_TEMPLATE);
		$this->assign('tpl_path', _HTTP_THEMES_C);

		if (is_file(_THEMES_C.'lang/'.$_SESSION['language_code'].'.conf'))
		{
			$this->configLoad(_THEMES_C.'lang/'.$_SESSION['language_code'].'.conf');
		}

		global $p;
		$name = $p->name;
		$group = $p->group;
		$array = array(
			'app_name' => 'osTemplate'
		);
		$array = apply_filter('tpl_vars', $array);
		$p->name = $name;
		$p->group = $group;

		if (count($array) > 0)
		{
			foreach ($array as $name => $value)
			{
				$this->assign($name, $value);
			}
		}
	}
}
?>