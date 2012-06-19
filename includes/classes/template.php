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

require_once (_LIB.'smarty/smarty.class.php');

class osTemplate extends Smarty 
{
   function osTemplate()
   {
        $this->Smarty();
        $this->template_dir = _THEMES;
        $this->compile_dir = _CACHE;
        $this->config_dir   = _LANG;
		$this->compile_check   =  TEMPLATE_COMPILE_CHECK;
        $this->cache_dir    = _CACHE;
        $this->plugins_dir = array(_LIB.'smarty/plugins',);

		$this->assign('index', http_path('catalog'));

		if ( is_file( _THEMES_C.'lang/'.$_SESSION['language_code'].'.conf') )
		{
		   $this->config_load(_THEMES_C.'lang/'.$_SESSION['language_code'].'.conf');
		}

		global $p;
		$name = $p->name;
		$group = $p->group;
				
		$array = array('app_name' => 'osTemplate');
		
		$array = apply_filter('tpl_vars', $array);
		
		$p->name = $name;
		$p->group = $group;
		$p->set_dir();
		
		if ( count($array) > 0 )
		{
		   foreach ($array as $name => $value)
		   {
		       $this->assign($name, $value);
		   }
		}
		
   }
}
?>