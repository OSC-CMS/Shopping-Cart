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

  switch ($_GET['action']) 
  {
    case 'setflag':
      os_set_featured_status($_GET['id'], $_GET['flag']);
      os_redirect(os_href_link(FILENAME_FEATURED, '', 'NONSSL'));
      break;
    case 'insert':

      $expires_date = '';
      if ($_POST['expires-dd'] && $_POST['expires-mm'] && $_POST['expires']) {
        $expires_date = $_POST['expires'];
        $expires_date .= (strlen($_POST['expires-mm']) == 1) ? '0' . $_POST['expires-mm'] : $_POST['expires-mm'];
        $expires_date .= (strlen($_POST['expires-dd']) == 1) ? '0' . $_POST['expires-dd'] : $_POST['expires-dd'];
      }

      os_db_query("insert into " . TABLE_FEATURED . " (products_id, featured_quantity, featured_date_added, expires_date, status) values ('" . $_POST['products_id'] . "', '" . $_POST['featured_quantity'] . "', now(), '" . $expires_date . "', '1')");
      os_redirect(os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page']));
      break;

    case 'update':
      $expires_date = '';
      if ($_POST['expires-dd'] && $_POST['expires-mm'] && $_POST['expires']) {
        $expires_date = $_POST['expires'];
        $expires_date .= (strlen($_POST['expires-mm']) == 1) ? '0' . $_POST['expires-mm'] : $_POST['expires-mm'];
        $expires_date .= (strlen($_POST['expires-dd']) == 1) ? '0' . $_POST['expires-dd'] : $_POST['expires-dd'];
      }

      os_db_query("update " . TABLE_FEATURED . " set featured_quantity = '" . $_POST['featured_quantity'] . "', featured_last_modified = now(), expires_date = '" . $expires_date . "' where featured_id = '" . $_POST['featured_id'] . "'");
      os_redirect(os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page'] . '&fID=' . $featured_id));
      break;

    case 'deleteconfirm':
      $featured_id = os_db_prepare_input($_GET['fID']);

      os_db_query("delete from " . TABLE_FEATURED . " where featured_id = '" . os_db_input($featured_id) . "'");

      os_redirect(os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page']));
      break;
  }
  
  add_action('head_admin', 'head_featured');
  
  function head_featured ()
  {
       $head = '';
       if ( ($_GET['action'] == 'new') || ($_GET['action'] == 'edit') ) 
       {
           echo '<link href="includes/javascript/date-picker/css/datepicker.css" rel="stylesheet" type="text/css" />'."\n";
           echo '<script type="text/javascript" src="includes/javascript/date-picker/js/datepicker.js"></script>'."\n";
	   }
	   
	   return true;
  }
  
    $main->head();
?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( ($_GET['action'] == 'new') || ($_GET['action'] == 'edit') ) {
    $form_action = 'insert';
    if ( ($_GET['action'] == 'edit') && ($_GET['fID']) ) {
          $form_action = 'update';

      $product_query = os_db_query("select p.products_tax_class_id,
                                            p.products_id,
                                            pd.products_name,
                                            p.products_price,
                                            f.featured_quantity,
                                            f.expires_date from
                                            " . TABLE_PRODUCTS . " p,
                                            " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                                            " . TABLE_FEATURED . "
                                            f where p.products_id = pd.products_id
                                            and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                            and p.products_id = f.products_id
                                            and f.featured_id = '" . (int)$_GET['fID'] . "'");
      $product = os_db_fetch_array($product_query);

      $fInfo = new objectInfo($product);
    } else {
      $fInfo = new objectInfo(array());
      $featured_array = array();
      $featured_query = os_db_query("select
                                      p.products_id from
                                      " . TABLE_PRODUCTS . " p,
                                      " . TABLE_FEATURED . " f
                                      where f.products_id = p.products_id");

      while ($featured = os_db_fetch_array($featured_query)) {
        $featured_array[] = $featured['products_id'];
      }
    }
?>
      <tr><form name="new_featured" <?php echo 'action="' . os_href_link(FILENAME_FEATURED, os_get_all_get_params(array('action', 'info', 'fID')) . 'action=' . $form_action, 'NONSSL') . '"'; ?> method="post"><?php if ($form_action == 'update') echo os_draw_hidden_field('featured_id', $_GET['fID']); ?>
        <td><br /><table border="0" cellspacing="0" cellpadding="2">

                <td class="main"><?php echo TEXT_FEATURED_PRODUCT; echo ($fInfo->products_name) ? "" :  ''; ?>&nbsp;</td>
           <?php
                echo '<input type="hidden" name="products_up_id" value="' . $fInfo->products_id . '">';
           ?>
          <td class="main"><?php echo ($fInfo->products_name) ? $fInfo->products_name : os_draw_products_pull_down('products_id', 'style="font-size:10px"', $featured_array); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_FEATURED_QUANTITY; ?>&nbsp;</td>
            <td class="main"><?php echo os_draw_input_field('featured_quantity', $fInfo->featured_quantity);?> </td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_FEATURED_EXPIRES_DATE; ?>&nbsp;</td>
            <td class="main"><?php echo os_draw_input_field('expires-dd', substr($fInfo->expires_date, 8, 2), "size=\"2\" maxlength=\"2\" id=\"expires-dd\""); ?> / <?php echo os_draw_input_field('expires-mm', substr($fInfo->expires_date, 5, 2), "size=\"2\" maxlength=\"2\" id=\"expires-mm\""); ?> / <?php echo os_draw_input_field('expires', substr($fInfo->expires_date, 0, 4), "size=\"4\" maxlength=\"4\" id=\"expires\" class=\"format-d-m-y split-date\""); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" align="right" valign="top"><br /><?php echo (($form_action == 'insert') ? '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span>' : '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span>'). '&nbsp;&nbsp;&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page'] . '&fID=' . $_GET['fID']) . '"><span>' . BUTTON_CANCEL . '</span></a>'; ?></td>
          </tr>
        </table></td>
      </form></tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $featured_query_raw = "select p.products_id, pd.products_name,p.products_tax_class_id, p.products_price, f.featured_id, f.featured_date_added, f.featured_last_modified, f.expires_date, f.date_status_change, f.status from " . TABLE_PRODUCTS . " p, " . TABLE_FEATURED . " f, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_id = f.products_id order by pd.products_name";
    $featured_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $featured_query_raw, $featured_query_numrows);
    $featured_query = os_db_query($featured_query_raw);
    while ($featured = os_db_fetch_array($featured_query)) {

      if ( ((!$_GET['fID']) || ($_GET['fID'] == $featured['featured_id'])) && (!$fInfo) ) {
        $products_query = os_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $featured['products_id'] . "'");
        $products = os_db_fetch_array($products_query);
        $fInfo_array = os_array_merge($featured, $products);
        $fInfo = new objectInfo($fInfo_array);
      }

      $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff'; 	  
      if ( (is_object($fInfo)) && ($featured['featured_id'] == $fInfo->featured_id) ) {
        echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page'] . '&fID=' . $fInfo->featured_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'"  onclick="document.location.href=\'' . os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page'] . '&fID=' . $featured['featured_id']) . '\'">' . "\n";
      }
?>
                <td  class="dataTableContent"><?php echo $featured['products_name']; ?></td>
                <td  class="dataTableContent" align="center">
<?php
      if ($featured['status'] == '1') {
        echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_FEATURED, 'action=setflag&flag=0&id=' . $featured['featured_id'], 'NONSSL') . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . os_href_link(FILENAME_FEATURED, 'action=setflag&flag=1&id=' . $featured['featured_id'], 'NONSSL') . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($fInfo)) && ($featured['featured_id'] == $fInfo->featured_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page'] . '&fID=' . $featured['featured_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
      </tr>
<?php
    }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellpadding="0"cellspacing="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $featured_split->display_count($featured_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FEATURED); ?></td>
                    <td class="smallText" align="right"><?php echo $featured_split->display_links($featured_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_NEW_PRODUCTS . '</span></a>';?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FEATURED . '</b>');

      $contents = array('form' => os_draw_form('featured', FILENAME_FEATURED, 'page=' . $_GET['page'] . '&fID=' . $fInfo->featured_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $fInfo->products_name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page'] . '&fID=' . $fInfo->featured_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    default:
      if (is_object($fInfo)) {
        $heading[] = array('text' => '<b>' . $fInfo->products_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page'] . '&fID=' . $fInfo->featured_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a><br /><a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_FEATURED, 'page=' . $_GET['page'] . '&fID=' . $fInfo->featured_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $contents[] = array('text' => '<br /><b>' . TEXT_INFO_DATE_ADDED . '</b> ' . os_date_short($fInfo->featured_date_added));
        $contents[] = array('text' => '<b>' . TEXT_INFO_LAST_MODIFIED . '</b> ' . os_date_short($fInfo->featured_last_modified));
        $contents[] = array('align' => 'center', 'text' => '<br />' . os_product_thumb_image($fInfo->products_image, $fInfo->products_name, PRODUCT_IMAGE_THUMBNAIL_WIDTH, PRODUCT_IMAGE_THUMBNAIL_HEIGHT));

        $contents[] = array('text' => '<br /><b>' . TEXT_INFO_EXPIRES_DATE . '</b> <b>' . os_date_short($fInfo->expires_date) . '</b>');
        $contents[] = array('text' => '<b>' . TEXT_INFO_STATUS_CHANGE . '</b> ' . os_date_short($fInfo->date_status_change));
      }
      break;
  }
  if ( (os_not_null($heading)) && (os_not_null($contents)) ) {
    echo '            <td class="right_box" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
}
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>