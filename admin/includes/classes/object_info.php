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

  class objectInfo {

    function objectInfo($object_array) 
	{
	  
      reset($object_array);
      while (list($key, $value) = each($object_array)) {
        $this->$key = os_db_prepare_input($value);
      }
    }
  }
?>