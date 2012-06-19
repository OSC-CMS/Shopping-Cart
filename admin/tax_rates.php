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
      case 'insert':
        $tax_zone_id = os_db_prepare_input($_POST['tax_zone_id']);
        $tax_class_id = os_db_prepare_input($_POST['tax_class_id']);
        $tax_rate = os_db_prepare_input($_POST['tax_rate']);
        $tax_description = os_db_prepare_input($_POST['tax_description']);
        $tax_priority = os_db_prepare_input($_POST['tax_priority']);
        $date_added = os_db_prepare_input($_POST['date_added']);

        os_db_query("insert into " . TABLE_TAX_RATES . " (tax_zone_id, tax_class_id, tax_rate, tax_description, tax_priority, date_added) values ('" . os_db_input($tax_zone_id) . "', '" . os_db_input($tax_class_id) . "', '" . os_db_input($tax_rate) . "', '" . os_db_input($tax_description) . "', '" . os_db_input($tax_priority) . "', now())");
        os_redirect(os_href_link(FILENAME_TAX_RATES));
        break;

      case 'save':
        $tax_rates_id = os_db_prepare_input($_GET['tID']);
        $tax_zone_id = os_db_prepare_input($_POST['tax_zone_id']);
        $tax_class_id = os_db_prepare_input($_POST['tax_class_id']);
        $tax_rate = os_db_prepare_input($_POST['tax_rate']);
        $tax_description = os_db_prepare_input($_POST['tax_description']);
        $tax_priority = os_db_prepare_input($_POST['tax_priority']);
        $last_modified = os_db_prepare_input($_POST['last_modified']);

        os_db_query("update " . TABLE_TAX_RATES . " set tax_rates_id = '" . os_db_input($tax_rates_id) . "', tax_zone_id = '" . os_db_input($tax_zone_id) . "', tax_class_id = '" . os_db_input($tax_class_id) . "', tax_rate = '" . os_db_input($tax_rate) . "', tax_description = '" . os_db_input($tax_description) . "', tax_priority = '" . os_db_input($tax_priority) . "', last_modified = now() where tax_rates_id = '" . os_db_input($tax_rates_id) . "'");
        os_redirect(os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $tax_rates_id));
        break;

      case 'deleteconfirm':
        $tax_rates_id = os_db_prepare_input($_GET['tID']);

        os_db_query("delete from " . TABLE_TAX_RATES . " where tax_rates_id = '" . os_db_input($tax_rates_id) . "'");
        os_redirect(os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page']));
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
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_RATE_PRIORITY; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_CLASS_TITLE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ZONE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_RATE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $rates_query_raw = "select r.tax_rates_id, z.geo_zone_id, z.geo_zone_name, tc.tax_class_title, tc.tax_class_id, r.tax_priority, r.tax_rate, r.tax_description, r.date_added, r.last_modified from " . TABLE_TAX_CLASS . " tc, " . TABLE_TAX_RATES . " r left join " . TABLE_GEO_ZONES . " z on r.tax_zone_id = z.geo_zone_id where r.tax_class_id = tc.tax_class_id";
  $rates_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $rates_query_raw, $rates_query_numrows);
  $rates_query = os_db_query($rates_query_raw);
  
  while ($rates = os_db_fetch_array($rates_query)) 
  {
    if (((!$_GET['tID']) || (@$_GET['tID'] == $rates['tax_rates_id'])) && (!$trInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $trInfo = new objectInfo($rates);
    }
	
	$color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
	
    if ( (is_object($trInfo)) && ($rates['tax_rates_id'] == $trInfo->tax_rates_id) ) 
	{
         echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit') . '\'">' . "\n";
    } 
	else 
	{
         echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $rates['tax_priority']; ?></td>
                <td class="dataTableContent"><?php echo $rates['tax_class_title']; ?></td>
                <td class="dataTableContent"><?php echo $rates['geo_zone_name']; ?></td>
                <td class="dataTableContent"><?php echo os_display_tax_value($rates['tax_rate']); ?>%</td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($trInfo)) && ($rates['tax_rates_id'] == $trInfo->tax_rates_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $rates_split->display_count($rates_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_RATES); ?></td>
                    <td class="smallText" align="right"><?php echo $rates_split->display_links($rates_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="5" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_NEW_TAX_RATE . '</span></a>'; ?></td>
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
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_TAX_RATE . '</b>');

      $contents = array('form' => os_draw_form('rates', FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_TITLE . '<br />' . os_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_NAME . '<br />' . os_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE . '<br />' . os_draw_input_field('tax_rate'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_RATE_DESCRIPTION . '<br />' . os_draw_input_field('tax_description'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE_PRIORITY . '<br />' . os_draw_input_field('tax_priority'));
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_TAX_RATE . '</b>');

      $contents = array('form' => os_draw_form('rates', FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_TITLE . '<br />' . os_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"', $trInfo->tax_class_id));
      $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_NAME . '<br />' . os_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"', $trInfo->geo_zone_id));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE . '<br />' . os_draw_input_field('tax_rate', $trInfo->tax_rate));
      $contents[] = array('text' => '<br />' . TEXT_INFO_RATE_DESCRIPTION . '<br />' . os_draw_input_field('tax_description', $trInfo->tax_description));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE_PRIORITY . '<br />' . os_draw_input_field('tax_priority', $trInfo->tax_priority));
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_TAX_RATE . '</b>');

      $contents = array('form' => os_draw_form('rates', FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $trInfo->tax_class_title . ' ' . number_format($trInfo->tax_rate, TAX_DECIMAL_PLACES) . '%</b>');
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    default:
      if (is_object($trInfo)) {
        $heading[] = array('text' => '<b>' . $trInfo->tax_class_title . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . os_date_short($trInfo->date_added));
        $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . os_date_short($trInfo->last_modified));
        $contents[] = array('text' => '<br />' . TEXT_INFO_RATE_DESCRIPTION . '<br />' . $trInfo->tax_description);
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
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>