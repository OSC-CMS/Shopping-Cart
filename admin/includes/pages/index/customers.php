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

?><table border="0" width="100%">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
				  <tr> 
				    <td colspan="3" class="pageHeading" width="100%">
				     	<?php os_header('portfolio_package.gif',TABLE_HEADING_NEWS); ?> 
				    </td>
				  </tr>
              <tr class="dataTableHeadingRow">
                <td width="35%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_LASTNAME; ?></td>
                <td width="35%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_FIRSTNAME; ?></td>
                <td width="30%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_DATE; ?></td>
              </tr>

<?php
	$customers_query_raw = "select
	                                c.customers_id,
	                                c.customers_lastname,
	                                c.customers_firstname,
	                                c.customers_date_added
	                                from
	                                ".TABLE_CUSTOMERS." c order by c.customers_date_added desc limit 20";

	$customers_query = os_db_query($customers_query_raw);
	while ($customers = os_db_fetch_array($customers_query)) {


?>
              <tr>
                <td class="dataTableContent"><a href="<?php echo os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID')).'cID='.$customers['customers_id'].'&action=edit'); ?>"><?php echo $customers['customers_lastname']; ?></a></td>
                <td class="dataTableContent"><a href="<?php echo os_href_link(FILENAME_CUSTOMERS, os_get_all_get_params(array ('cID')).'cID='.$customers['customers_id'].'&action=edit'); ?>"><?php echo $customers['customers_firstname']; ?></a></td>
                <td class="dataTableContent"><?php echo $customers['customers_date_added']; ?></td>
              </tr>
<?php

	}
?>

                </table></td>
              </tr></table>