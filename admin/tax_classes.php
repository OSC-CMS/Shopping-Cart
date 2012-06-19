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
        $tax_class_title = os_db_prepare_input($_POST['tax_class_title']);
        $tax_class_description = os_db_prepare_input($_POST['tax_class_description']);
        $date_added = os_db_prepare_input($_POST['date_added']);

        os_db_query("insert into " . TABLE_TAX_CLASS . " (tax_class_title, tax_class_description, date_added) values ('" . os_db_input($tax_class_title) . "', '" . os_db_input($tax_class_description) . "', now())");
        os_redirect(os_href_link(FILENAME_TAX_CLASSES));
        break;

      case 'save':
        $tax_class_id = os_db_prepare_input($_GET['tID']);
        $tax_class_title = os_db_prepare_input($_POST['tax_class_title']);
        $tax_class_description = os_db_prepare_input($_POST['tax_class_description']);
        $last_modified = os_db_prepare_input($_POST['last_modified']);

        os_db_query("update " . TABLE_TAX_CLASS . " set tax_class_id = '" . os_db_input($tax_class_id) . "', tax_class_title = '" . os_db_input($tax_class_title) . "', tax_class_description = '" . os_db_input($tax_class_description) . "', last_modified = now() where tax_class_id = '" . os_db_input($tax_class_id) . "'");
        os_redirect(os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tax_class_id));
        break;

      case 'deleteconfirm':
        $tax_class_id = os_db_prepare_input($_GET['tID']);

        os_db_query("delete from " . TABLE_TAX_CLASS . " where tax_class_id = '" . os_db_input($tax_class_id) . "'");
        os_redirect(os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page']));
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
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_CLASSES; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $classes_query_raw = "select tax_class_id, tax_class_title, tax_class_description, last_modified, date_added from " . TABLE_TAX_CLASS . " order by tax_class_title";
  $classes_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $classes_query_raw, $classes_query_numrows);
  $classes_query = os_db_query($classes_query_raw);
  while ($classes = os_db_fetch_array($classes_query)) {
    if (((!$_GET['tID']) || (@$_GET['tID'] == $classes['tax_class_id'])) && (!$tcInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $tcInfo = new objectInfo($classes);
    }
	$color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if ( (is_object($tcInfo)) && ($classes['tax_class_id'] == $tcInfo->tax_class_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo'              <tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $classes['tax_class_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $classes['tax_class_title']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($tcInfo)) && ($classes['tax_class_id'] == $tcInfo->tax_class_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $classes['tax_class_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $classes_split->display_count($classes_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES); ?></td>
                    <td class="smallText" align="right"><?php echo $classes_split->display_links($classes_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_NEW_TAX_CLASS . '</span></a>'; ?></td>
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
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_TAX_CLASS . '</b>');

      $contents = array('form' => os_draw_form('classes', FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_TITLE . '<br />' . os_draw_input_field('tax_class_title'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_DESCRIPTION . '<br />' . os_draw_input_field('tax_class_description'));
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_TAX_CLASS . '</b>');

      $contents = array('form' => os_draw_form('classes', FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_TITLE . '<br />' . os_draw_input_field('tax_class_title', $tcInfo->tax_class_title));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_DESCRIPTION . '<br />' . os_draw_input_field('tax_class_description', $tcInfo->tax_class_description));
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_TAX_CLASS . '</b>');

      $contents = array('form' => os_draw_form('classes', FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $tcInfo->tax_class_title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>' . BUTTON_DELETE . '</button></span>&nbsp;<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    default:
      if (is_object($tcInfo)) {
        $heading[] = array('text' => '<b>' . $tcInfo->tax_class_title . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . os_date_short($tcInfo->date_added));
        $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . os_date_short($tcInfo->last_modified));
        $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_DESCRIPTION . '<br />' . $tcInfo->tax_class_description);
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