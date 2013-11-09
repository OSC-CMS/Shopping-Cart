<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class freeamount {
    var $code, $title, $description, $icon, $enabled;


    function freeamount() {
      $this->code = 'freeamount';
      $this->title = MODULE_SHIPPING_FREEAMOUNT_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_FREEAMOUNT_TEXT_DESCRIPTION;
      $this->icon ='';   
      $this->sort_order = MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER;
      $this->enabled = ((MODULE_SHIPPING_FREEAMOUNT_STATUS == 'True') ? true : false);
    }

    function quote($method = '') {
    	global $osPrice;
	
		$getCartInfo = $_SESSION['cart']->getCartInfo();

	  if (( $osPrice->RemoveCurr($getCartInfo['show_total']) < MODULE_SHIPPING_FREEAMOUNT_AMOUNT ) && MODULE_SHIPPING_FREEAMOUNT_DISPLAY == 'False')
	  return;

      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_FREEAMOUNT_TEXT_TITLE);

      if ( $osPrice->RemoveCurr($getCartInfo['show_total']) < MODULE_SHIPPING_FREEAMOUNT_AMOUNT )
        $this->quotes['error'] = sprintf(MODULE_SHIPPING_FREEAMOUNT_TEXT_WAY,$osPrice->Format(MODULE_SHIPPING_FREEAMOUNT_AMOUNT,true,0,true));
      else
 	$this->quotes['methods'] = array(array('id'    => $this->code,
                                               'title' => sprintf(MODULE_SHIPPING_FREEAMOUNT_TEXT_WAY,$osPrice->Format(MODULE_SHIPPING_FREEAMOUNT_AMOUNT,true,0,true)),
                                               'cost'  => 0));

      if (os_not_null($this->icon)) $this->quotes['icon'] = os_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check() {
      $check = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_FREEAMOUNT_STATUS'");
      $check = os_db_num_rows($check);

      return $check;
    }

    function install() {
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_STATUS', 'True', '6', '7', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_ALLOWED', '', '6', '0', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_DISPLAY', 'True', '6', '7', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_AMOUNT', '50.00', '6', '8', now())");
      os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER', '0', '6', '4', now())");
    }

    function remove() {
      os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_SHIPPING_FREEAMOUNT_STATUS','MODULE_SHIPPING_FREEAMOUNT_ALLOWED', 'MODULE_SHIPPING_FREEAMOUNT_DISPLAY', 'MODULE_SHIPPING_FREEAMOUNT_AMOUNT','MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER');
    }
  }
?>
