<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

function affiliate_delete ($affiliate_id) {
  $affiliate_query = os_db_query("SELECT affiliate_rgt, affiliate_lft, affiliate_root FROM " . TABLE_AFFILIATE . " WHERE affiliate_id = '" . $affiliate_id . "' ");
  if ($affiliate = os_db_fetch_array($affiliate_query)) {
    if ($affiliate['affiliate_root'] == $affiliate_id) {
      // a root entry is deleted -> his childs get root
      $affiliate_child_query = os_db_query("
                    SELECT aa1.affiliate_id, aa1.affiliate_lft, aa1.affiliate_rgt,  COUNT(*) AS level
	                  FROM ".DB_PREFIX."affiliate_affiliate AS aa1, ".DB_PREFIX."affiliate_affiliate AS aa2
	                  WHERE aa1.affiliate_root = " . $affiliate['affiliate_root'] . " 
	                        AND aa2.affiliate_root = aa1.affiliate_root 
	                        AND aa1.affiliate_lft BETWEEN aa2.affiliate_lft AND aa2.affiliate_rgt
	                        AND aa1.affiliate_rgt BETWEEN aa2.affiliate_lft AND aa2.affiliate_rgt
	                  GROUP BY aa1.affiliate_id, aa1.affiliate_lft, aa1.affiliate_rgt
                    HAVING level = 2
	                  ORDER BY aa1.affiliate_id
                   ");
      @mysql_query("LOCK TABLES " . TABLE_AFFILIATE . " WRITE");
      while ($affiliate_child = os_db_fetch_array($affiliate_child_query)) {
        os_db_query ("UPDATE  " . TABLE_AFFILIATE . " SET affiliate_root = " . $affiliate_child['affiliate_id'] . " WHERE affiliate_root =  " . $affiliate['affiliate_root'] . "  AND affiliate_lft >= " . $affiliate_child['affiliate_lft']  . " AND affiliate_rgt <= " . $affiliate_child['affiliate_rgt']  . " ");
        $substract =  $affiliate_child['affiliate_lft'] -1;
        os_db_query ("UPDATE  " . TABLE_AFFILIATE . " SET affiliate_lft = affiliate_lft - " . $substract . " WHERE  affiliate_root = " . $affiliate_child['affiliate_id']);
        os_db_query ("UPDATE  " . TABLE_AFFILIATE . " SET affiliate_rgt = affiliate_rgt - " . $substract . " WHERE  affiliate_root = " . $affiliate_child['affiliate_id']) ;
      }
      os_db_query("DELETE FROM " . TABLE_AFFILIATE . "  WHERE affiliate_id = " . $affiliate_id);
      @mysql_query("UNLOCK TABLES");
    } else {
      @mysql_query("LOCK TABLES " . TABLE_AFFILIATE . " WRITE");
      os_db_query("DELETE FROM " . TABLE_AFFILIATE . "  WHERE affiliate_id = " . $affiliate_id . " AND affiliate_root = " . $affiliate['affiliate_root'] . " ");
    
      os_db_query("UPDATE " . TABLE_AFFILIATE . "
                  SET affiliate_lft = affiliate_lft -1, affiliate_rgt=affiliate_rgt-1
	                WHERE affiliate_lft BETWEEN " . $affiliate['affiliate_lft'] . " and " . $affiliate['affiliate_rgt'] . "
	                AND affiliate_root =  " . $affiliate['affiliate_root'] . " 
                ");
      os_db_query("UPDATE " . TABLE_AFFILIATE . "
	                SET affiliate_lft = affiliate_lft-2
	                WHERE affiliate_lft > " . $affiliate['affiliate_rgt'] . "
	                AND affiliate_root =  " . $affiliate['affiliate_root'] . " 
                 ");
      os_db_query("UPDATE " . TABLE_AFFILIATE . "
                	SET affiliate_rgt = affiliate_rgt-2
                  WHERE affiliate_rgt > " . $affiliate['affiliate_rgt'] . "
                  AND affiliate_root =  " . $affiliate['affiliate_root'] . " 
                 ");
      @mysql_query("UNLOCK TABLES");
    }
  }
}


  function os_get_affiliate_tax_rate($class_id, $country_id, $zone_id) {

    $tax_query = os_db_query("select SUM(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za ON tr.tax_zone_id = za.geo_zone_id left join " . TABLE_GEO_ZONES . " tz ON tz.geo_zone_id = tr.tax_zone_id WHERE (za.zone_country_id IS NULL OR za.zone_country_id = '0' OR za.zone_country_id = '" . $country_id . "') AND (za.zone_id IS NULL OR za.zone_id = '0' OR za.zone_id = '" . $zone_id . "') AND tr.tax_class_id = '" . $class_id . "' GROUP BY tr.tax_priority");
    if (os_db_num_rows($tax_query)) {
      $tax_multiplier = 0;
      while ($tax = os_db_fetch_array($tax_query)) {
        $tax_multiplier += $tax['tax_rate'];
      }
      return $tax_multiplier;
    } else {
      return 0;
    }
  }
?>
