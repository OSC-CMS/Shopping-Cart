<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

?>
    <td valign="top" class="main"><?php echo ENTRY_MEMO; ?></td>
    <td class="main"><?php
  $memo_query = os_db_query("SELECT
                                  *
                              FROM
                                  " . TABLE_CUSTOMERS_MEMO . "
                              WHERE
                                  customers_id = '" . $_GET['cID'] . "'
                              ORDER BY
                                  memo_date DESC");
  while ($memo_values = os_db_fetch_array($memo_query)) {
    $poster_query = os_db_query("SELECT customers_firstname, customers_lastname FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $memo_values['poster_id'] . "'");
    $poster_values = os_db_fetch_array($poster_query);
?><table width="100%">
      <tr>
        <td class="main"><b><?php echo TEXT_DATE; ?></b>: <i><?php echo $memo_values['memo_date']; ?></i> <b><?php echo TEXT_TITLE; ?></b>: <?php echo $memo_values['memo_title']; ?><b>  <?php echo TEXT_POSTER; ?></b>: <?php echo $poster_values['customers_lastname']; ?> <?php echo $poster_values['customers_firstname']; ?></td>
      </tr>
      <tr>
        <td width="142" class="main" style="border: 1px solid; border-color: #cccccc;"><?php echo $memo_values['memo_text']; ?></td>
      </tr>
      <tr>
        <td><a href="<?php echo os_href_link(FILENAME_CUSTOMERS, 'cID=' . $_GET['cID'] . '&action=edit&special=remove_memo&mID=' . $memo_values['memo_id']); ?>" onClick="return confirm('<?php echo DELETE_ENTRY; ?>')"><span class="button"><button type="submit" value="<?php echo BUTTON_DELETE; ?>"><?php echo BUTTON_DELETE; ?></button></span></a></td>
      </tr>
    </table>
<?php
  }
?>
    <table width="100%">
      <tr>
        <td class="main" style="border-top: 1px solid; border-color: #cccccc;"><b><?php echo TEXT_TITLE ?></b>: <?php echo os_draw_input_field('memo_title'); ?><br><?php echo os_draw_textarea_field('memo_text', 'soft', '80', '5'); ?><br><span class="button"><button type="submit" value="<?php echo BUTTON_INSERT; ?>"><?php echo BUTTON_INSERT; ?></button></span></td>
      </tr>
    </table></td>