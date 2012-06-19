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

class shipping {

   var $modules;
   
   function shipping($module = '') 
   {
      global $PHP_SELF, $order;

      if (defined('MODULE_SHIPPING_INSTALLED') && os_not_null(MODULE_SHIPPING_INSTALLED)) 
	  {
         $this->modules = explode(';', MODULE_SHIPPING_INSTALLED);
         $include_modules = array();
        
        if ( (os_not_null($module)) && (in_array(substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $this->modules)) ) 
		
		{
          $include_modules[] = array('class' => substr($module['id'], 0, strpos($module['id'], '_')), 'file' => substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)));
       
	   } 
		else {
          reset($this->modules);
          while (list(, $value) = each($this->modules)) {
             //print();
			$class =  substr($value, 0, strrpos($value, '.'));
			
            $include_modules[] = array('class' => $class, 'file' => $value);
          }
        }
        $unallowed_modules = explode(',',$_SESSION['customers_status']['customers_status_shipping_unallowed'].','.$order->customer['shipping_unallowed']);
        for ($i = 0, $n = sizeof($include_modules); $i < $n; $i++) {
		  
          if (os_in_array(str_replace('.php', '', $include_modules[$i]['file']), $unallowed_modules) != 'false') {
            if (constant(MODULE_SHIPPING_ . strtoupper(str_replace('.php', '', $include_modules[$i]['file'])) . _ALLOWED) != '') {
              $unallowed_zones = explode(',', constant(MODULE_SHIPPING_ . strtoupper(str_replace('.php', '', $include_modules[$i]['file'])) . _ALLOWED));
            } else {
              $unallowed_zones = array();
            }
            if (in_array($_SESSION['delivery_zone'], $unallowed_zones) == true || count($unallowed_zones) == 0) 
			{
                //$class =  substr($include_modules[$i]['file'], 0, strrpos($include_modules[$i]['file'], '.'));
			  include (DIR_FS_DOCUMENT_ROOT.'/modules/shipping/'.substr($include_modules[$i]['file'], 0, strrpos($include_modules[$i]['file'], '.')).'/'.$_SESSION['language'].'.php');
			  include (DIR_FS_DOCUMENT_ROOT.'/modules/shipping/'.substr($include_modules[$i]['file'], 0, strrpos($include_modules[$i]['file'], '.')).'/'.$include_modules[$i]['file']);
              $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
            }
          }
        }
      }
    }

    function quote($method = '', $module = '') 
	{
      global $total_weight, $shipping_weight, $shipping_quoted, $shipping_num_boxes;

      $quotes_array = array();

      if (is_array($this->modules)) {
        $shipping_quoted = '';
        $shipping_num_boxes = 1;
        $shipping_weight = $total_weight;

        if (SHIPPING_BOX_WEIGHT >= $shipping_weight*SHIPPING_BOX_PADDING/100) {
          $shipping_weight = $shipping_weight+SHIPPING_BOX_WEIGHT;
        } else {
          $shipping_weight = $shipping_weight + ($shipping_weight*SHIPPING_BOX_PADDING/100);
        }

        if ($shipping_weight > SHIPPING_MAX_WEIGHT) 
		{ 
		  if (SHIPPING_MAX_WEIGHT != 0)
		  {
             $shipping_num_boxes = ceil($shipping_weight/SHIPPING_MAX_WEIGHT);
             $shipping_weight = $shipping_weight/$shipping_num_boxes;
		  }
		  else
		  {
		      $shipping_num_boxes = 0;
			  $shipping_weight = 0;
		  }
        }

        $include_quotes = array();

        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if (os_not_null($module)) {
            if ( ($module == $class) && ($GLOBALS[$class]->enabled) ) {
              $include_quotes[] = $class;
            }
          } elseif ($GLOBALS[$class]->enabled) {
            $include_quotes[] = $class;
          }
        }

        $size = sizeof($include_quotes);
        for ($i=0; $i<$size; $i++) {
          $quotes = $GLOBALS[$include_quotes[$i]]->quote($method);
          if (is_array($quotes)) $quotes_array[] = $quotes;
        }
      }

      return $quotes_array;
    }

    function cheapest() {

      if (is_array($this->modules)) {
        $rates = array();

        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $quotes = $GLOBALS[$class]->quotes;
            $size = sizeof($quotes['methods']);
            for ($i=0; $i<$size; $i++) {
            if(array_key_exists("cost",$quotes['methods'][$i])) {
                $rates[] = array('id' => $quotes['id'] . '_' . $quotes['methods'][$i]['id'],
                                 'title' => $quotes['module'] . ' (' . $quotes['methods'][$i]['title'] . ')',
                                 'cost' => $quotes['methods'][$i]['cost']);

              }
            }
          }
        }

        $cheapest = false;
        $size = sizeof($rates);
        for ($i=0; $i<$size; $i++) {
          if (is_array($cheapest)) {
            if ($rates[$i]['cost'] < $cheapest['cost']) {
              $cheapest = $rates[$i];
            }
          } else {
            $cheapest = $rates[$i];
          }
        }
        return $cheapest;

      }

    }
  }
?>