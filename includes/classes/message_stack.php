<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

class messageStack {

    function messageStack() {
      $this->messages = array();

      if (isset($_SESSION['messageToStack'])) {
        $messageToStack = $_SESSION['messageToStack'];
        for ($i=0, $n=sizeof($messageToStack); $i<$n; $i++) {
          $this->add($messageToStack[$i]['class'], $messageToStack[$i]['text'], $messageToStack[$i]['type']);
        }
        unset($_SESSION['messageToStack']);
      }
    }

    function add($class, $message, $type = 'error') {
      $this->messages[] = array('class' => $class, 'type' => $type, 'text' => $message);
    }
    
    function add_session($class, $message, $type = 'error') {

      if (!isset($_SESSION['messageToStack'])) {
        $_SESSION['messageToStack'] = array();
      }

      $_SESSION['messageToStack'][] = array('class' => $class, 'text' => $message, 'type' => $type);
    }
    
    function reset() {
      $this->messages = array();
    }

    function output($class) {
      for ($i=0, $n=sizeof($this->messages); $i<$n; $i++) {
        if ($this->messages[$i]['class'] == $class) {

          $messages .= $this->messages[$i]['text'];
        }
      }

      return $messages;
    }

    function size($class) {
      $count = 0;

      for ($i=0, $n=sizeof($this->messages); $i<$n; $i++) {
        if ($this->messages[$i]['class'] == $class) {
          $count++;
        }
      }

      return $count;
    }
  }
?>