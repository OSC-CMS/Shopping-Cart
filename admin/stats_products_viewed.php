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
  if (isset($_GET['action']) && $_GET['action']=='default')
  {
     os_db_query('UPDATE '.DB_PREFIX.'products_description SET products_viewed = \'0\'');
  }
  
  
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php $main->heading('stats.png', HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NUMBER; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_VIEWED; ?>&nbsp;</td>
              </tr>
<?php
  if ($_GET['page'] > 1) $rows = $_GET['page'] * '20' - '20';
  $products_query_raw = "select p.products_id, pd.products_name, pd.products_viewed, l.name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_LANGUAGES . " l where p.products_id = pd.products_id and l.languages_id = pd.language_id order by pd.products_viewed DESC";
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $products_query_raw, $products_query_numrows);
  $products_query = os_db_query($products_query_raw);
  while ($products = os_db_fetch_array($products_query)) {
    $rows++;

    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
	
	 $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';

		      echo '<tr class="dataTableRow" onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'"  onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'">';

		   
?>
                <td class="dataTableContent"><?php echo $rows; ?>.</td>
                <td class="dataTableContent"><?php echo  $products['products_name'] . ' (' . $products['name'] . ')'; ?></td>
                <td class="dataTableContent" align="center"><?php echo $products['products_viewed']; ?>&nbsp;</td>
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
                <td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
              </tr>
            </table></td>
          </tr>
        </table>
		
		<a class="button" href="?action=default"><span><?php echo SET_DEFAULT_VIEWED; ?></span></a>
		</td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>