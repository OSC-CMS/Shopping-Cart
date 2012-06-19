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

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

function os_get_tax_rate_export($class_id, $country_id = -1, $zone_id = -1) {
   global $currency;

    if ( ($country_id == -1) && ($zone_id == -1) ) {

        $country_id = STORE_COUNTRY;
        $zone_id = STORE_ZONE;

    }

    $tax_query = os_db_query("select sum(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . $country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . $zone_id . "') and tr.tax_class_id = '" . $class_id . "' group by tr.tax_priority");
    if (os_db_num_rows($tax_query)) {
      $tax_multiplier = 1.0;
      while ($tax = os_db_fetch_array($tax_query)) {
        $tax_multiplier *= 1.0 + ($tax['tax_rate'] / 100);
      }
      return ($tax_multiplier - 1.0) * 100;
    } else {
      return 0;
    }
  }

function os_get_products_price_export($products_id,$price_special,$quantity,$group_id=0,$add_tax=1,$currency,$calculate_currencies=true)
    {


        $product_price_query = os_db_query("SELECT   products_price,
                                            products_discount_allowed,
                                            products_tax_class_id
                                            FROM ". TABLE_PRODUCTS ."
                                            WHERE
                                            products_id = '".$products_id."'");
        $product_price = os_db_fetch_array($product_price_query);
        $price_data=array();
        $price_data=array(
                    'PRODUCTS_PRICE'=>$product_price['products_price'],
                    'PRODUCTS_DISCOUNT_ALLOWED'=>$product_price['products_discount_allowed'],
                    'PRODUCT_TAX_CLASS_ID'=>$product_price['products_tax_class_id']
                    );
        $products_tax=os_get_tax_rate_export($price_data['PRODUCT_TAX_CLASS_ID']);

        if ($add_tax =='0') {
            $products_tax='';
        }
        if ($special_price=os_get_products_special_price($products_id)) {
            $special_price= (os_add_tax($special_price,$products_tax));
             $price_data['PRODUCTS_PRICE']= (os_add_tax($price_data['PRODUCTS_PRICE'],$products_tax));

            $price_string=os_format_special_price_export($special_price,$price_data['PRODUCTS_PRICE'],$price_special,$calculate_currencies=true,$quantity,$products_tax,$add_tax,$currency);
        }
        else {

            $group_price_query=os_db_query("SELECT personal_offer
                                             FROM ".TABLE_PERSONAL_OFFERS.$group_id."
                                             WHERE products_id='".$products_id."'");
            $group_price_data=os_db_fetch_array($group_price_query);
            if     ($group_price_data['personal_offer']!='' and $group_price_data['personal_offer']!='0.0000') {
                 $price_string=$group_price_data['personal_offer'];



                     $qty=os_get_qty($products_id);
                     if (!os_get_qty($products_id)) $qty=$quantity;



                     $graduated_price_query=os_db_query("SELECT max(quantity)
                                                          FROM ".TABLE_PERSONAL_OFFERS.$group_id."
                                                          WHERE products_id='".$products_id."'
                                                          AND quantity<='".$qty."'");
                     $graduated_price_data=os_db_fetch_array($graduated_price_query);
                     $graduated_price_query=os_db_query("SELECT personal_offer
                                                          FROM ".TABLE_PERSONAL_OFFERS.$group_id."
                                                          WHERE products_id='".$products_id."'
                                                            AND quantity='".$graduated_price_data['max(quantity)']."'");
                     $graduated_price_data=os_db_fetch_array($graduated_price_query);
                     $price_string=$graduated_price_data['personal_offer'];
                 $price_string= (os_add_tax($price_string,$products_tax));

            }
            else {
               $price_string= (os_add_tax($price_data['PRODUCTS_PRICE'],$products_tax));


            }

            $price_string=$price_string*$quantity;

 
          $currencies_query = os_db_query("SELECT *
          FROM ". TABLE_CURRENCIES ." WHERE
          code = '".$currency."'");
          $currencies_value=os_db_fetch_array($currencies_query);
          $currencies_data=array();
          $currencies_data=array(
                           'SYMBOL_LEFT'=>$currencies_value['symbol_left'] ,
                           'SYMBOL_RIGHT'=>$currencies_value['symbol_right'] ,
                           'DECIMAL_PLACES'=>$currencies_value['decimal_places'] ,
                           'VALUE'=> $currencies_value['value']);

          if ($calculate_currencies=='true') {
             $price_string=$price_string * $currencies_data['VALUE'];
          }
          $price_string=os_precision($price_string,$currencies_data['DECIMAL_PLACES']);

          if ($price_special=='1') {
              $currencies_query = os_db_query("SELECT *
                                            FROM ". TABLE_CURRENCIES ." WHERE
                                            code = '".$currency ."'");

              $currencies_value=os_db_fetch_array($currencies_query);
              $price_string=number_format($price_string,$currencies_data['DECIMAL_PLACES'], $currencies_value['decimal_point'], $currencies_value['thousands_point']);

          if ($show_currencies == 1) {
          $price_string = $currencies_data['SYMBOL_LEFT']. ' '.$price_string.' '.$currencies_data['SYMBOL_RIGHT'];
            }
}

        }

    return $price_string;

}



function os_format_special_price_export ($special_price,$price,$price_special,$calculate_currencies,$quantity,$products_tax,$add_tax,$currency)
    {
    global $currency; 
    $currencies_query = os_db_query("SELECT symbol_left,
                                            symbol_right,
                                            decimal_places,
                                            decimal_point,
                                                  thousands_point,
                                            value
                                            FROM ". TABLE_CURRENCIES ." WHERE
                                            code = '".$currency ."'");
    $currencies_value=os_db_fetch_array($currencies_query);
    $currencies_data=array();
    $currencies_data=array(
                            'SYMBOL_LEFT'=>$currencies_value['symbol_left'] ,
                            'SYMBOL_RIGHT'=>$currencies_value['symbol_right'] ,
                            'DECIMAL_PLACES'=>$currencies_value['decimal_places'],
                            'DEC_POINT'=>$currencies_value['decimal_point'],
                            'THD_POINT'=>$currencies_value['thousands_point'],
                            'VALUE'=> $currencies_value['value'])                            ;
    if ($add_tax =='0') {
        $products_tax='';
    }

    $price=$price*$quantity;
    $special_price=$special_price*$quantity;

    if ($calculate_currencies=='true') {
    $special_price=$special_price * $currencies_data['VALUE'];
    $price=$price * $currencies_data['VALUE'];

    }

    $special_price=os_precision($special_price,$currencies_data['DECIMAL_PLACES'] );
    $price=os_precision($price,$currencies_data['DECIMAL_PLACES'] );

    return $special_price;
    }

?>