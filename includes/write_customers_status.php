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

  if (isset($_SESSION['customer_id'])) {
    $customers_status_query_1 = os_db_query("SELECT customers_status FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $_SESSION['customer_id'] . "'");
    $customers_status_value_1 = os_db_fetch_array($customers_status_query_1);

	 //cache get_customers_status
	$customers_status_value = get_customers_status($customers_status_value_1['customers_status']);
	
    $_SESSION['customers_status'] = array();
    $_SESSION['customers_status']= array(
      'customers_status_id' => $customers_status_value_1['customers_status'],
      'customers_status_name' => $customers_status_value['customers_status_name'],
      'customers_status_image' => $customers_status_value['customers_status_image'],
      'customers_status_public' => $customers_status_value['customers_status_public'],
      'customers_status_min_order' => $customers_status_value['customers_status_min_order'],
      'customers_status_max_order' => $customers_status_value['customers_status_max_order'],
      'customers_status_discount' => $customers_status_value['customers_status_discount'],
      'customers_status_ot_discount_flag' => $customers_status_value['customers_status_ot_discount_flag'],
      'customers_status_ot_discount' => $customers_status_value['customers_status_ot_discount'],
      'customers_status_graduated_prices' => $customers_status_value['customers_status_graduated_prices'],
      'customers_status_show_price' => $customers_status_value['customers_status_show_price'],
      'customers_status_show_price_tax' => $customers_status_value['customers_status_show_price_tax'],
      'customers_status_add_tax_ot' => $customers_status_value['customers_status_add_tax_ot'],
      'customers_status_payment_unallowed' => $customers_status_value['customers_status_payment_unallowed'],
      'customers_status_shipping_unallowed' => $customers_status_value['customers_status_shipping_unallowed'],
      'customers_status_discount_attributes' => $customers_status_value['customers_status_discount_attributes'],
      'customers_fsk18' => $customers_status_value['customers_fsk18'],
      'customers_fsk18_display' => $customers_status_value['customers_fsk18_display'],
      'customers_status_write_reviews' => $customers_status_value['customers_status_write_reviews'],
      'customers_status_read_reviews' => $customers_status_value['customers_status_read_reviews']
    );
  } else {
    //$customers_status_query = os_db_query("SELECT * FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . DEFAULT_CUSTOMERS_STATUS_ID_GUEST . "' AND language_id = '" . $_SESSION['languages_id'] . "'");
    //$customers_status_value = os_db_fetch_array($customers_status_query);

	$customers_status_value = get_customers_status (DEFAULT_CUSTOMERS_STATUS_ID_GUEST);

    $_SESSION['customers_status'] = array();
    $_SESSION['customers_status']= array(
      'customers_status_id' => DEFAULT_CUSTOMERS_STATUS_ID_GUEST,
      'customers_status_name' => $customers_status_value['customers_status_name'],
      'customers_status_image' => $customers_status_value['customers_status_image'],
      'customers_status_discount' => $customers_status_value['customers_status_discount'],
      'customers_status_public' => $customers_status_value['customers_status_public'],
      'customers_status_min_order' => $customers_status_value['customers_status_min_order'],
      'customers_status_max_order' => $customers_status_value['customers_status_max_order'],
      'customers_status_ot_discount_flag' => $customers_status_value['customers_status_ot_discount_flag'],
      'customers_status_ot_discount' => $customers_status_value['customers_status_ot_discount'],
      'customers_status_graduated_prices' => $customers_status_value['customers_status_graduated_prices'],
      'customers_status_show_price' => $customers_status_value['customers_status_show_price'],
      'customers_status_show_price_tax' => $customers_status_value['customers_status_show_price_tax'],
      'customers_status_add_tax_ot' => $customers_status_value['customers_status_add_tax_ot'],
      'customers_status_payment_unallowed' => $customers_status_value['customers_status_payment_unallowed'],
      'customers_status_shipping_unallowed' => $customers_status_value['customers_status_shipping_unallowed'],
      'customers_status_discount_attributes' => $customers_status_value['customers_status_discount_attributes'],
      'customers_fsk18' => $customers_status_value['customers_fsk18'],
      'customers_fsk18_display' => $customers_status_value['customers_fsk18_display'],
      'customers_status_write_reviews' => $customers_status_value['customers_status_write_reviews'],
      'customers_status_read_reviews' => $customers_status_value['customers_status_read_reviews']
    );
  }

?>