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

  os_db_query("update " . TABLE_PRODUCTS . " set products_date_available = '' where to_days(now()) > to_days(products_date_available)");
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE_EXPECTED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $products_query_raw = "select pd.products_id, pd.products_name, p.products_date_available from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p where p.products_id = pd.products_id and p.products_date_available != '' and pd.language_id = '" . $_SESSION['languages_id'] . "' order by p.products_date_available DESC";
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $products_query_raw, $products_query_numrows);
  $products_query = os_db_query($products_query_raw);
  while ($products = os_db_fetch_array($products_query)) 
  {
    if (((!$_GET['pID']) || ($_GET['pID'] == $products['products_id'])) && (!$pInfo) ) {
      $pInfo = new objectInfo($products);
    }
   
     $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if ( (is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_CATEGORIES, 'pID=' . $products['products_id'] . '&action=new_product') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'"  onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_PRODUCTS_EXPECTED, 'page=' . $_GET['page'] . '&pID=' . $products['products_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $products['products_name']; ?></td>
                <td class="dataTableContent" align="center"><?php echo os_date_short($products['products_date_available']); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="' . os_href_link(FILENAME_PRODUCTS_EXPECTED, 'page=' . $_GET['page'] . '&pID=' . $products['products_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED); ?></td>
                    <td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  if (is_object($pInfo)) {
    $heading[] = array('text' => '<b>' . $pInfo->products_name . '</b>');

    $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CATEGORIES, 'pID=' . $pInfo->products_id . '&action=new_product') . '"><span>' . BUTTON_EDIT . '</span></a>');
    $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_EXPECTED . ' ' . os_date_short($pInfo->products_date_available));
  }

  if ( (os_not_null($heading)) && (os_not_null($contents)) ) {
    echo '            <td class="right_box" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>