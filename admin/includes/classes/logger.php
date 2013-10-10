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
  
defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

  class logger {
    var $timer_start, $timer_stop, $timer_total;

    function logger() {
      $this->timer_start();
    }

    function timer_start() {
      if (defined("PAGE_PARSE_START_TIME")) {
        $this->timer_start = PAGE_PARSE_START_TIME;
      } else {
        $this->timer_start = microtime();
      }
    }

    function timer_stop($display = 'false') {
      $this->timer_stop = microtime();

      $time_start = explode(' ', $this->timer_start);
      $time_end = explode(' ', $this->timer_stop);

      $this->timer_total = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

      $this->write($_SERVER['REQUEST_URI'], $this->timer_total . 's');

      if ($display == 'true') {
        return $this->timer_display();
      }
    }

    function timer_display() {
      return '<span class="smallText">'.PARSE_TIME.' '. $this->timer_total . 'c</span>';
    }

    function write($message, $type) {
      error_log(strftime(STORE_PARSE_DATE_TIME_FORMAT) . ' [' . $type . '] ' . $message . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }
  }
?>