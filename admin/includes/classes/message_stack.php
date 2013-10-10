<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/
/*
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(2003/06/02); www.oscommerce.com 
  (c) 2003	 nextcommerce (2003/08/18); www.nextcommerce.org
  (c) 2004	 xt:Commerce (2003/08/18); xt-commerce.com
  (c) 2008	 VamShop (2008/01/01); vamshop.com
*/
  
defined( '_VALID_OS' ) or die( '������ ������  �� �����������.' );

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
        $this->errors[] = array('params' => 'error', 'text' =>  $message);
      } elseif ($type == 'warning') {
        $this->errors[] = array('params' => 'warning', 'text' => $message);
      } elseif ($type == 'success') {
        $this->errors[] = array('params' => 'ok', 'text' => $message);
      } else {
        $this->errors[] = array('params' => 'info', 'text' => $message);
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

	    $return = '<div id="notifier-box">';
	  foreach($this->errors AS $error)
	  {
		  $return .= '
		<div class="message-box '.$error['params'].'" style="">
			<a class="message-close" href="#"></a>
			<div class="message-body"><span>'.$error['text'].'</span></div>
		</div>
	';
	  }
	    $return .= '</div>';

      return $return;
    }
  }
?>