<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.1
#####################################
*/

require_once (_LIB.'smarty/smarty.class.php');

class osTemplate extends Smarty {
   function osTemplate()
   {
        $this->Smarty();
        $this->template_dir = _THEMES;
        $this->compile_dir = _CACHE;
        $this->config_dir   = _LANG;
        $this->cache_dir    = _CACHE;
        $this->plugins_dir = array(_LIB.'smarty/plugins',);
        $this->assign('app_name', 'osTemplate');
   }
}

?>