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
   include(_LANG_ADMIN . $_SESSION['language_admin'] . '/customers.php');

if ($_GET['action']) {
switch ($_GET['action']) {

        case 'save':

        $memo_title = os_db_prepare_input($_POST['memo_title']);
        $memo_text = os_db_prepare_input($_POST['memo_text']);

        if ($memo_text != '' && $memo_title != '' ) {
          $sql_data_array = array(
            'customers_id' => $_POST['ID'],
            'memo_date' => date("Y-m-d"),
            'memo_title' =>$memo_title,
            'memo_text' => nl2br($memo_text),
            'poster_id' => $_SESSION['customer_id']);

          os_db_perform(TABLE_CUSTOMERS_MEMO, $sql_data_array);
          }
        break;

        case 'remove':
        os_db_query("DELETE FROM ".TABLE_CUSTOMERS_MEMO." where memo_id='".$_GET['mID']."'");
        break;

}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<?php $main->favicon();?>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo $page_title; ?></title>
<?php 
   $main->style("style"); 
   $main->style("menu");
?>
</head>
<body>
<div class="pageHeading"><?php echo TITLE_MEMO; ?></div></p>
    <table width="100%">
      <tr>
      <form name="customers_memo" method="POST" action="popup_memo.php?action=save&ID=<?php echo (int)$_GET['ID'];?>">
        <td class="main" style="border-top: 1px solid; border-color: #cccccc;"><b><?php echo TEXT_TITLE ?></b>: <?php echo os_draw_input_field('memo_title').os_draw_hidden_field('ID',(int)$_GET['ID']); ?><br /><?php echo os_draw_textarea_field('memo_text', 'soft', '60', '5'); ?><br /><?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span>'; ?></td>
      </tr>
    </table></form>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">

  <tr>
    <td>



    <td class="main"><?php
  $memo_query = os_db_query("SELECT
                                  *
                              FROM
                                  " . TABLE_CUSTOMERS_MEMO . "
                              WHERE
                                  customers_id = '" . (int)$_GET['ID'] . "'
                              ORDER BY
                                  memo_id DESC");
  while ($memo_values = os_db_fetch_array($memo_query)) {
    $poster_query = os_db_query("SELECT customers_firstname, customers_lastname FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $memo_values['poster_id'] . "'");
    $poster_values = os_db_fetch_array($poster_query);
?><table width="100%">
      <tr>
        <td class="main"><hr noshade><b><?php echo TEXT_DATE; ?></b>: <i><?php echo $memo_values['memo_date']; ?><br /></i> <b><?php echo TEXT_TITLE; ?></b>: <?php echo $memo_values['memo_title']; ?><br /><b>  <?php echo TEXT_POSTER; ?></b>: <?php echo $poster_values['customers_lastname']; ?> <?php echo $poster_values['customers_firstname']; ?></td>
      </tr>
      <tr>
        <td width="142" class="main" style="border: 1px solid; border-color: #cccccc;"><?php echo $memo_values['memo_text']; ?></td>
      </tr>
      <tr>
        <td><a class="button" onClick="this.blur();" href="<?php echo os_href_link('popup_memo.php', 'ID=' . $_GET['ID'] . '&action=remove&mID=' . $memo_values['memo_id']); ?>" onClick="return confirm('<?php echo DELETE_ENTRY; ?>')"><span><?php echo BUTTON_DELETE; ?></span></a></td>
      </tr>
    </table>
<?php
  }
?>
  </td>
    </td>
  </tr>
</table>

</body>
</html>