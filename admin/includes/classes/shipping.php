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

  class shipping {
    var $modules;
  
    function shipping() {

      if (defined('MODULE_SHIPPING_INSTALLED') && os_not_null(MODULE_SHIPPING_INSTALLED)) {
        $allmods = explode(';', MODULE_SHIPPING_INSTALLED);
        
        $this->modules = array();
        
        for ($i = 0, $n = sizeof($allmods); $i < $n; $i++) {
          $file = $allmods[$i];
          $class = substr($file, 0, strrpos($file, '.'));
          $this->modules[$i] = array();
          $this->modules[$i]['class'] = $class;
          $this->modules[$i]['file'] = $file;
        }
      }
    }
    
    function get_modules(){
      return $this->modules;
    }
    
    function shipping_select($parameters, $selected = '') {
      echo $selected;
      $select_string = '<select ' . $parameters . '>';
      for ($i = 0, $n = sizeof($this->modules); $i < $n; $i++) {
        $select_string .= '<option value="' . $this->modules[$i]['class'] . '"';
        if ($selected == $this->modules[$i]['class']) $select_string .= ' SELECTED';
        $select_string .= '>' . $this->modules[$i]['class'] . '</option>';
      }
      $select_string .= '</select>';
      return $select_string;
    }
  }
?>
