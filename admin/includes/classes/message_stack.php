<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/
/*
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(2003/06/02); www.oscommerce.com 
  (c) 2003	 nextcommerce (2003/08/18); www.nextcommerce.org
  (c) 2004	 xt:Commerce (2003/08/18); xt-commerce.com
  (c) 2008	 VamShop (2008/01/01); vamshop.com
*/
  
defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

  class messageStack extends tableBlock {
    var $size = 0;

    function messageStack() {

      $this->errors = array();

      if (isset($_SESSION['messageToStack'])) {
        for ($i = 0, $n = sizeof($_SESSION['messageToStack']); $i < $n; $i++) {
          $this->add($_SESSION['messageToStack'][$i]['text'], $_SESSION['messageToStack'][$i]['type']);
        }
        unset($_SESSION['messageToStack']);
      }
    }

    function add($message, $type = 'error') {
      if ($type == 'error') {
        $this->errors[] = array('params' => 'class="messageStackError" id="messageStackError"', 'text' => os_image(get_path('icons_admin', 'http') . 'error.gif', ICON_ERROR) . '&nbsp;' . $message);
      } elseif ($type == 'warning') {
        $this->errors[] = array('params' => 'class="messageStackWarning" id="messageStackWarning"', 'text' => os_image(get_path('icons_admin', 'http') . 'warning.gif', ICON_WARNING) . '&nbsp;' . $message);
      } elseif ($type == 'success') {
        $this->errors[] = array('params' => 'class="messageStackSuccess" id="messageStackSuccess"', 'text' => os_image(get_path('icons_admin', 'http') . 'success.png', ICON_SUCCESS) . '&nbsp;' . $message);
      } else {
        $this->errors[] = array('params' => 'class="messageStackError" id="messageStackError"', 'text' => $message);
      }

      $this->size++;
    }
	
    function add_session($message, $type = 'error') {
      if (!isset($_SESSION['messageToStack'])) {
        $_SESSION['messageToStack'] = array();
      }

      $_SESSION['messageToStack'][] = array('text' => $message, 'type' => $type);
    }

    function reset() {
      $this->errors = array();
      $this->size = 0;
    }

    function output() {
      $this->table_data_parameters = 'class="messageBox"';
      $this->table_class = 'contentTable1';
      $this->table_id = 'messageStackWarning';
	 
      return $this->tableBlock($this->errors);
    }
  }
?>