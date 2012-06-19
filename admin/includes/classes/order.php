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
/*
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(2003/06/02); www.oscommerce.com 
  (c) 2003	 nextcommerce (2003/08/18); www.nextcommerce.org
  (c) 2004	 xt:Commerce (2003/08/18); xt-commerce.com
  (c) 2008	 VamShop (2008/01/01); vamshop.com
*/
  
defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );
  class order {
    var $info, $totals, $products, $customer, $delivery;

    function order($order_id) {
      $this->info = array();
      $this->totals = array();
      $this->products = array();
      $this->customer = array();
      $this->delivery = array();

      $this->query($order_id);
    }

    function query($order_id) {
      $order_query = os_db_query("select customers_name,
                                   customers_cid,
                                   customers_id,
                                   customers_vat_id,
                                   customers_company,
                                   customers_street_address,
                                   customers_suburb,
                                   customers_city,
                                   customers_postcode,
                                   customers_state,
                                   customers_country,
                                   customers_telephone,
                                   customers_email_address,
                                   customers_address_format_id,
                                   delivery_name,
                                   delivery_company,
                                   delivery_street_address,
                                   delivery_suburb,
                                   delivery_city,
                                   delivery_postcode,
                                   delivery_state,
                                   delivery_country,
                                   delivery_address_format_id,
                                   billing_name,
                                   billing_company,
                                   billing_street_address,
                                   billing_suburb,
                                   billing_city,
                                   billing_postcode,
                                   billing_state,
                                   billing_country,
                                   billing_address_format_id,
                                   payment_method,
                                   payment_class,
				                  shipping_class,
				                  cc_type,
                                   cc_owner,
                                   cc_number,
                                   cc_expires,
                                   cc_cvv,
                                   comments,
                                   currency,
                                   currency_value,
                                   date_purchased,
                                   orders_status,
                                   last_modified,
                                   orig_reference, 
                                   login_reference,
                                   customers_status,
                                   customers_status_name,
                                   customers_status_image,
                                   customers_ip,
                                   language,
                                   customers_status_discount
                                   from " . TABLE_ORDERS . " where
                                   orders_id = '" . os_db_input($order_id) . "'");

      $order = os_db_fetch_array($order_query);

      $totals_query = os_db_query("select title, text from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . os_db_input($order_id) . "' order by sort_order");
      while ($totals = os_db_fetch_array($totals_query)) {
        $this->totals[] = array('title' => $totals['title'],
                                'text' => $totals['text']);
      }

      $this->info = array('currency' => $order['currency'],
                          'currency_value' => $order['currency_value'],
                          'payment_method' => $order['payment_method'],
                          'payment_class' => $order['payment_class'],
                          'shipping_class' => $order['shipping_class'],
                          'status' => $order['customers_status'],
                          'status_name' => $order['customers_status_name'],
                          'status_image' => $order['customers_status_image'],
                          'status_discount' => $order['customers_status_discount'],
                          'cc_type' => $order['cc_type'],
                          'cc_owner' => $order['cc_owner'],
                          'cc_number' => $order['cc_number'],
                          'cc_expires' => $order['cc_expires'],
                          'cc_cvv' => $order['cc_cvv'],
                          'comments' => $order['comments'],
                          'language' => $order['language'],
                          'date_purchased' => $order['date_purchased'],
                          'orders_status' => $order['orders_status'],
                          'last_modified' => $order['last_modified']);

      $this->customer = array('name' => $order['customers_name'],
                              'company' => $order['customers_company'],
                              'csID' => $order['customers_cid'],
                              'vat_id' => $order['customers_vat_id'],                               
                              'shop_id' => $order['shop_id'], 
                              'ID' => $order['customers_id'],
                              'cIP' => $order['customers_ip'],
                              'street_address' => $order['customers_street_address'],
                              'suburb' => $order['customers_suburb'],
                              'city' => $order['customers_city'],
                              'postcode' => $order['customers_postcode'],
                              'state' => $order['customers_state'],
                              'country' => $order['customers_country'],
                              'format_id' => $order['customers_address_format_id'],
                              'telephone' => $order['customers_telephone'],
                              'email_address' => $order['customers_email_address'],
                              'orig_reference' => $order['orig_reference'],
                              'login_reference' => $order['login_reference']);

      $this->delivery = array('name' => $order['delivery_name'],
                              'company' => $order['delivery_company'],
                              'street_address' => $order['delivery_street_address'],
                              'suburb' => $order['delivery_suburb'],
                              'city' => $order['delivery_city'],
                              'postcode' => $order['delivery_postcode'],
                              'state' => $order['delivery_state'],
                              'country' => $order['delivery_country'],
                              'format_id' => $order['delivery_address_format_id']);

      $this->billing = array('name' => $order['billing_name'],
                             'company' => $order['billing_company'],
                             'street_address' => $order['billing_street_address'],
                             'suburb' => $order['billing_suburb'],
                             'city' => $order['billing_city'],
                             'postcode' => $order['billing_postcode'],
                             'state' => $order['billing_state'],
                             'country' => $order['billing_country'],
                             'format_id' => $order['billing_address_format_id']);

      $index = 0;
      $orders_products_query = os_db_query("select
                                                 orders_products_id,products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price,allow_tax, products_discount_made
                                             from
                                                 " . TABLE_ORDERS_PRODUCTS . "
                                             where
                                                 orders_id ='" . os_db_input($order_id) . "'");

      while ($orders_products = os_db_fetch_array($orders_products_query)) {
        $this->products[$index] = array('qty' => $orders_products['products_quantity'],
                                        'name' => $orders_products['products_name'],
                                        'id' => $orders_products['products_id'],
                                        'opid' => $orders_products['orders_products_id'],                                        
                                        'model' => $orders_products['products_model'],
                                        'tax' => $orders_products['products_tax'],
                                        'price' => $orders_products['products_price'],
                                        'discount' => $orders_products['products_discount_made'],
                                        'final_price' => $orders_products['final_price'],
					'allow_tax' => $orders_products['allow_tax']);

        $subindex = 0;
        $attributes_query = os_db_query("select products_options, products_options_values, options_values_price, price_prefix from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . os_db_input($order_id) . "' and orders_products_id = '" . $orders_products['orders_products_id'] . "'");
        if (os_db_num_rows($attributes_query)) {
          while ($attributes = os_db_fetch_array($attributes_query)) {
            $this->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options'],
                                                                     'value' => $attributes['products_options_values'],
                                                                     'prefix' => $attributes['price_prefix'],
                                                                     'price' => $attributes['options_values_price']);

            $subindex++;
          }
        }
        $index++;
      }
    }
  }
?>