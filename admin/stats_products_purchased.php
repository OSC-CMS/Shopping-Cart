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

  require('includes/top.php');
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('stats.png',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NUMBER; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PURCHASED; ?>&nbsp;</td>
              </tr>
<?php
  if ($_GET['page'] > 1) $rows = $_GET['page'] * '20' - '20';
  $products_query_raw = "select p.products_id, p.products_ordered, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_ordered > 0 group by pd.products_id order by p.products_ordered DESC, pd.products_name";
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $products_query_raw, $products_query_numrows);

  $products_query = os_db_query($products_query_raw);
  while ($products = os_db_fetch_array($products_query)) {
    $rows++;

    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
	$color = ($color == '#f9f9ff') ? '#f0f1ff':'#f9f9ff';
?>
              <tr style="background-color: <?php echo $color; ?>" class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='dataTableRow'" onclick="document.location.href='<?php echo os_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $products['products_id'] . '&origin=' . FILENAME_STATS_PRODUCTS_PURCHASED . '?page=' . $_GET['page'], 'NONSSL'); ?>'">
                <td class="dataTableContent"><?php echo $rows; ?>.</td>
                <td class="dataTableContent"><?php echo $products['products_name'] . '</a>'; ?></td>
                <td class="dataTableContent" align="center"><?php echo $products['products_ordered']; ?>&nbsp;</td>
              </tr>
<?php
  }
?>
            </table></td>
          </tr>
          <tr>
            <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                <td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>