<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

   defined('_VALID_OS') or die('Прямой доступ  не допускается.');
   
  $delete_sql = os_db_query("SELECT products_attributes_id FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '" . $_POST['current_product_id'] . "'");
  while($delete_res = os_db_fetch_array($delete_sql)) 
  {
      $delete_download_sql = os_db_query("SELECT products_attributes_filename FROM ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." WHERE products_attributes_id = '" . $delete_res['prducts_attributes_id'] . "'");
      $delete_download_file = os_db_fetch_array($delete_download_sql);
      os_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." WHERE products_attributes_id = '" . $delete_res['products_attributes_id'] . "'");
  }
  os_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '" . $_POST['current_product_id'] . "'" );

  for ($i = 0; $i < sizeof($_POST['optionValues']); $i++) {
    $query = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." where products_options_values_id = '" . $_POST['optionValues'][$i] . "'";
    $result = os_db_query($query);
    $matches = os_db_num_rows($result);
    while ($line = os_db_fetch_array($result)) {
      $optionsID = $line['products_options_id'];
    }

    $cv_id = $_POST['optionValues'][$i];
    $value_price =  $_POST[$cv_id . '_price'];

    if (PRICE_IS_BRUTTO=='true'){

    $value_price= ($value_price/((os_get_tax_rate(os_get_tax_class_id($_POST['current_product_id'])))+100)*100);
    }
          $value_price=os_round($value_price,PRICE_PRECISION);


    $value_prefix = $_POST[$cv_id . '_prefix'];
    $value_sortorder = $_POST[$cv_id . '_sortorder'];
    $value_weight_prefix = $_POST[$cv_id . '_weight_prefix'];
    $value_model =  $_POST[$cv_id . '_model'];
    $value_stock =  $_POST[$cv_id . '_stock'];
    $value_weight =  $_POST[$cv_id . '_weight'];


      os_db_query("INSERT INTO ".TABLE_PRODUCTS_ATTRIBUTES." (products_id, options_id, options_values_id, options_values_price, price_prefix ,attributes_model, attributes_stock, options_values_weight, weight_prefix,sortorder) VALUES ('" . $_POST['current_product_id'] . "', '" . $optionsID . "', '" . $_POST['optionValues'][$i] . "', '" . $value_price . "', '" . $value_prefix . "', '" . $value_model . "', '" . $value_stock . "', '" . $value_weight . "', '" . $value_weight_prefix . "','".$value_sortorder."')") or die(mysql_error());

    $products_attributes_id = os_db_insert_id();

        if ($_POST[$cv_id . '_download_file'] != '') {
        $value_download_file = $_POST[$cv_id . '_download_file'];
        $value_download_expire = $_POST[$cv_id . '_download_expire'];
        $value_download_count = $_POST[$cv_id . '_download_count'];

        os_db_query("INSERT INTO ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." (products_attributes_id, products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount) VALUES ('" . $products_attributes_id . "', '" . $value_download_file . "', '" . $value_download_expire . "', '" . $value_download_count . "')") or die(mysql_error());
    }
  }

?>