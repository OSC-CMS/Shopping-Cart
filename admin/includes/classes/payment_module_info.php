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

  defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' ); 
  class paymentModuleInfo {
    var $payment_code, $keys;
    function paymentModuleInfo($pmInfo_array) {
      $this->payment_code = $pmInfo_array['payment_code'];

      for ($i = 0, $n = sizeof($pmInfo_array) - 1; $i < $n; $i++) {
        $key_value_query = os_db_query("select configuration_title, configuration_value, configuration_description from " . TABLE_CONFIGURATION . " where configuration_key = '" . $pmInfo_array[$i] . "'");
        $key_value = os_db_fetch_array($key_value_query);

        $this->keys[$pmInfo_array[$i]]['title'] = $key_value['configuration_title'];
        $this->keys[$pmInfo_array[$i]]['value'] = $key_value['configuration_value'];
        $this->keys[$pmInfo_array[$i]]['description'] = $key_value['configuration_description'];
      }
    }
  }
?>