<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

  if (isset($_SESSION['customer_id'])) {
    $customers_status_query_1 = os_db_query("SELECT customers_status, account_type, customers_default_address_id FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $_SESSION['customer_id'] . "'");
    $customers_status_value_1 = os_db_fetch_array($customers_status_query_1);

    // check if zone id is unset bug #0000169
    if (!isset ($_SESSION['customer_country_id']))
    {
      $zone_query = os_db_query("SELECT  entry_country_id FROM ".TABLE_ADDRESS_BOOK." WHERE customers_id='".(int) $_SESSION['customer_id']."' and address_book_id='".$customers_status_value_1['customers_default_address_id']."'");
      $zone = os_db_fetch_array($zone_query);
      $_SESSION['customer_country_id'] = $zone['entry_country_id'];
    }

    $_SESSION['account_type'] = $customers_status_value_1['account_type'];

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
    $_SESSION['account_type'] = '0';
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