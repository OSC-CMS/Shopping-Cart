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

  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'update':
        $reviews_id = os_db_prepare_input($_GET['rID']);
        $reviews_rating = os_db_prepare_input($_POST['reviews_rating']);
        $last_modified = os_db_prepare_input($_POST['last_modified']);
        $reviews_text = os_db_prepare_input($_POST['reviews_text']);

        os_db_query("update " . TABLE_REVIEWS . " set reviews_rating = '" . os_db_input($reviews_rating) . "', last_modified = now() where reviews_id = '" . os_db_input($reviews_id) . "'");
        os_db_query("update " . TABLE_REVIEWS_DESCRIPTION . " set reviews_text = '" . os_db_input($reviews_text) . "' where reviews_id = '" . os_db_input($reviews_id) . "'");

        os_redirect(os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews_id));
        break;

      case 'deleteconfirm':
        $reviews_id = os_db_prepare_input($_GET['rID']);

        os_db_query("delete from " . TABLE_REVIEWS . " where reviews_id = '" . os_db_input($reviews_id) . "'");
        os_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . os_db_input($reviews_id) . "'");

        os_redirect(os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page']));
        break;

      case 'update_staus':
		$reviews_id = os_db_prepare_input($_GET['rID']);
        $status = (int)$_GET['flag'];

os_db_query("update ".TABLE_REVIEWS." set status = '".$status."' where reviews_id = '" . os_db_input($reviews_id) . "'");

        os_redirect(os_href_link(FILENAME_REVIEWS, 'page='.$_GET['page'].'&rID='.$reviews_id));
        break;
    }
  }
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ($_GET['action'] == 'edit') {
    $rID = os_db_prepare_input($_GET['rID']);

    $reviews_query = os_db_query("select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . os_db_input($rID) . "' and r.reviews_id = rd.reviews_id");
    $reviews = os_db_fetch_array($reviews_query);
    $products_query = os_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
    $products = os_db_fetch_array($products_query);

    $products_name_query = os_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
    $products_name = os_db_fetch_array($products_name_query);

    $rInfo_array = os_array_merge($reviews, $products, $products_name);
    $rInfo = new objectInfo($rInfo_array);
?>
      <tr><?php echo os_draw_form('review', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=preview'); ?>
        <td>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top"><b><?php echo ENTRY_PRODUCT; ?></b> <?php echo $rInfo->products_name; ?><br /><b><?php echo ENTRY_FROM; ?></b> <?php echo $rInfo->customers_name; ?><br /><br /><b><?php echo ENTRY_DATE; ?></b> <?php echo os_date_short($rInfo->date_added); ?></td>
           </tr>
        </table></td>
      </tr>
      <tr>
        <td><table witdh="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top"><b><?php echo ENTRY_REVIEW; ?></b><br /><br /><?php echo os_draw_textarea_field('reviews_text', 'soft', '60', '15', $rInfo->reviews_text); ?></td>
          </tr>
          <tr>
            <td class="smallText" align="right"><?php echo ENTRY_REVIEW_TEXT; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo ENTRY_RATING; ?></b>&nbsp;<?php echo TEXT_BAD; ?>&nbsp;<?php for ($i=1; $i<=5; $i++) echo os_draw_radio_field('reviews_rating', $i, '', $rInfo->reviews_rating) . '&nbsp;'; echo TEXT_GOOD; ?></td>
      </tr>
      <tr>
        <td align="right" class="main"><?php echo os_draw_hidden_field('reviews_id', $rInfo->reviews_id) . os_draw_hidden_field('products_id', $rInfo->products_id) . os_draw_hidden_field('customers_name', $rInfo->customers_name) . os_draw_hidden_field('products_name', $rInfo->products_name) . os_draw_hidden_field('products_image', $rInfo->products_image) . os_draw_hidden_field('date_added', $rInfo->date_added) . '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_PREVIEW . '"/>' . BUTTON_PREVIEW . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']) . '"><span>' . BUTTON_CANCEL . '</span></a>'; ?></td>
      </form></tr>
<?php
  } elseif ($_GET['action'] == 'preview') {
    if ($_POST) {
      $rInfo = new objectInfo($_POST);
    } else {
      $reviews_query = os_db_query("select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . $_GET['rID'] . "' and r.reviews_id = rd.reviews_id");
      $reviews = os_db_fetch_array($reviews_query);
      $products_query = os_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
      $products = os_db_fetch_array($products_query);

      $products_name_query = os_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $products_name = os_db_fetch_array($products_name_query);

      $rInfo_array = os_array_merge($reviews, $products, $products_name);
      $rInfo = new objectInfo($rInfo_array);
    }
?>
      <tr><?php echo os_draw_form('update', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=update', 'post', 'enctype="multipart/form-data"'); ?>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top"><b><?php echo ENTRY_PRODUCT; ?></b> <?php echo $rInfo->products_name; ?><br /><b><?php echo ENTRY_FROM; ?></b> <?php echo $rInfo->customers_name; ?><br /><br /><b><?php echo ENTRY_DATE; ?></b> <?php echo os_date_short($rInfo->date_added); ?></td>
             </tr>
        </table>
      </tr>
      <tr>
        <td><table witdh="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top" class="main"><b><?php echo ENTRY_REVIEW; ?></b><br /><br /><?php echo nl2br(os_db_output(os_break_string($rInfo->reviews_text, 15))); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo ENTRY_RATING; ?></b>&nbsp;<?php echo os_image(http_path('icons_admin').'stars_' . $rInfo->reviews_rating . '.gif', sprintf(TEXT_OF_5_STARS, $rInfo->reviews_rating)); ?>&nbsp;<small>[<?php echo sprintf(TEXT_OF_5_STARS, $rInfo->reviews_rating); ?>]</small></td>
      </tr>
<?php
    if ($_POST) {
      reset($_POST);
      while(list($key, $value) = each($_POST)) echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars(stripslashes($value)) . '">';
?>
      <tr>
        <td align="right" class="smallText"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=edit') . '"><span>' . BUTTON_BACK . '</span></a> <span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id) . '"><span>' . BUTTON_CANCEL . '</span></a>'; ?></td>
      </form></tr>
<?php
    } else {
      if ($_GET['origin']) {
        $back_url = $_GET['origin'];
        $back_url_params = '';
      } else {
        $back_url = FILENAME_REVIEWS;
        $back_url_params = 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id;
      }
?>
      <tr>
        <td align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link($back_url, $back_url_params, 'NONSSL') . '"><span>' . BUTTON_BACK . '</span></a>'; ?></td>
      </tr>
<?php
    }
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_RATING; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $reviews_query_raw = "select reviews_id, products_id, date_added, last_modified, reviews_rating, status from " . TABLE_REVIEWS . " order by date_added DESC";
    $reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $reviews_query_raw, $reviews_query_numrows);
    $reviews_query = os_db_query($reviews_query_raw);
    while ($reviews = os_db_fetch_array($reviews_query)) {
      if ( ((!$_GET['rID']) || ($_GET['rID'] == $reviews['reviews_id'])) && (!$rInfo) ) {
        $reviews_text_query = os_db_query("select r.reviews_read, r.customers_name, length(rd.reviews_text) as reviews_text_size from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . $reviews['reviews_id'] . "' and r.reviews_id = rd.reviews_id");
        $reviews_text = os_db_fetch_array($reviews_text_query);

        $products_image_query = os_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
        $products_image = os_db_fetch_array($products_image_query);

        $products_name_query = os_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
        $products_name = os_db_fetch_array($products_name_query);

        $reviews_average_query = os_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . $reviews['products_id'] . "'");
        $reviews_average = os_db_fetch_array($reviews_average_query);

        $review_info = os_array_merge($reviews_text, $reviews_average, $products_name);
        $rInfo_array = os_array_merge($reviews, $review_info, $products_image);
        $rInfo = new objectInfo($rInfo_array);
      }
  $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff'; 
      if ( (is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) {
        echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=preview') . '\'">' . "\n";
      } else {
        echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id']) . '\'">' . "\n";
      }

?>
                <td class="dataTableContent"><?php echo '<a href="' . os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id'] . '&action=preview') . '">' . os_image(http_path('icons_admin') . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . os_get_products_name($reviews['products_id']); ?></td>
                <td class="dataTableContent" align="center"><?php echo os_image(http_path('icons_admin') . 'stars_' . $reviews['reviews_rating'] . '.gif'); ?></td>
                <td class="dataTableContent" align="right"><?php echo os_date_short($reviews['date_added']); ?></td>
                <td class="dataTableContent" align="center">
<?php
	 if ($reviews['status'] == 1) {
		 echo os_image(http_path('icons_admin').'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10).'&nbsp;&nbsp;<a href="'.os_href_link(FILENAME_REVIEWS, 'page='.$_GET['page'].'&rID='.$reviews['reviews_id'].'&action=update_staus&flag=0').'">'.os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
	 } else {
		 echo '<a href="'.os_href_link(FILENAME_REVIEWS, 'page='.$_GET['page'].'&rID='.$reviews['reviews_id'].'&action=update_staus&flag=1').'">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10).'</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
	 }
?>
				</td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="'.os_href_link(FILENAME_REVIEWS, 'page='.$_GET['page'].'&rID=' . $reviews['reviews_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $reviews_split->display_count($reviews_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                    <td class="smallText" align="right"><?php echo $reviews_split->display_links($reviews_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();
    switch ($_GET['action']) {
      case 'delete':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_REVIEW . '</b>');

        $contents = array('form' => os_draw_form('reviews', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=deleteconfirm'));
        $contents[] = array('text' => TEXT_INFO_DELETE_REVIEW_INTRO);
        $contents[] = array('text' => '<br /><b>' . $rInfo->products_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
        break;

      default:
      if (is_object($rInfo)) {
        $heading[] = array('text' => '<b>' . $rInfo->products_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . os_date_short($rInfo->date_added));
        if (os_not_null($rInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . os_date_short($rInfo->last_modified));
        $contents[] = array('text' => '<br />' . os_product_thumb_image($rInfo->products_image, $rInfo->products_name));
        $contents[] = array('text' => '<br />' . TEXT_INFO_REVIEW_AUTHOR . ' ' . $rInfo->customers_name);
        $contents[] = array('text' => TEXT_INFO_REVIEW_RATING . ' ' . os_image(http_path('icons_admin').'stars_' . $rInfo->reviews_rating . '.gif'));
        $contents[] = array('text' => TEXT_INFO_REVIEW_READ . ' ' . $rInfo->reviews_read);
        $contents[] = array('text' => '<br />' . TEXT_INFO_REVIEW_SIZE . ' ' . $rInfo->reviews_text_size . ' bytes');
        $contents[] = array('text' => '<br />' . TEXT_INFO_PRODUCTS_AVERAGE_RATING . ' ' . number_format($rInfo->average_rating, 2) . '%');
      }
        break;
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
<?php
  }
?>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>