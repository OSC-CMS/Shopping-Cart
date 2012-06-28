<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

require_once (_LIB.'smarty/Smarty.class.php');

class osTemplate extends Smarty
{
	function __construct()
	{
		parent::__construct();
		$this->setTemplateDir(_THEMES);
		$this->setCompileDir(_CACHE);
		$this->setConfigDir(_LANG);
		$this->setCacheDir(_CACHE);
		$this->compile_check   =  TEMPLATE_COMPILE_CHECK;
		$this->setPluginsDir(_LIB.'smarty/plugins');

		$this->assign('index', http_path('catalog'));
		$this->assign('tpl_dir', _THEMES.CURRENT_TEMPLATE);

		if (is_file(_THEMES_C.'lang/'.$_SESSION['language_code'].'.conf'))
		{
			$this->config_load(_THEMES_C.'lang/'.$_SESSION['language_code'].'.conf');
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
		$p->set_dir();

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