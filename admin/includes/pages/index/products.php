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

defined('_VALID_OS') or die('Прямой доступ  не допускается.');

require_once (_CLASS_ADMIN.'currencies.php');

$currencies = new currencies();

?><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td valign="top">
			<table border="0" width="100%" cellspacing="2" cellpadding="2">
				  <tr> 
				    <td colspan="3" width="100%">

	<?php os_header_url('download.png',TABLE_HEADING_SUMMARY_PRODUCTS, os_href_link(FILENAME_CATEGORIES)); ?> 
				    
				    </td>
				  </tr>
              <tr class="dataTableHeadingRow">
                <td width="35%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_NAME; ?></td>
                <td width="35%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCT_PRICE; ?></td>
                <td width="30%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_DATE; ?></td>
              </tr>

<?php

        $products_query_raw = os_db_query("
        SELECT 
        p.products_tax_class_id,
        p.products_id, 
        pd.products_name, 
        p.products_price, 
        p.products_date_added, 
        p.products_last_modified 
        FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE p.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "' order by p.products_date_added desc limit 20");
    $color = '';
	$url = '';
	while ($products = os_db_fetch_array($products_query_raw)) {

            $price = $products['products_price'];
            $price = os_round($price, PRICE_PRECISION);
			$color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff'; 
			
			$url = os_href_link(FILENAME_CATEGORIES, os_get_all_get_params(array('pID', 'action')) . 'pID=' . $products['products_id'] . '&action=new_product');
			
			echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . $url . '\'">' . "\n";

?>
              
                <td class="dataTableContent"><a href="<?php echo $url; ?>"><?php echo $products['products_name']; ?></a></td>
                <td class="dataTableContent" align="center"><?php echo $currencies->format($price); ?></td>
                <td class="dataTableContent" align="center"><?php echo $products['products_date_added']; ?></td>
              </tr>
<?php

	}
?>

                </table></td>
              </tr></table>