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
  require_once(_FUNC_ADMIN.'wysiwyg_tiny.php');
  set_faq_url_cache();
	
  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'setflag': 
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if ($_GET['faq_id']) {
            os_db_query("update " . TABLE_FAQ . " set status = '" . $_GET['flag'] . "' where faq_id = '" . $_GET['faq_id'] . "'");
          }
        }

        break;

      case 'delete_faq_confirm': 
        if ($_POST['faq_id']) {
          $faq_id = os_db_prepare_input($_POST['faq_id']);
          os_db_query("delete from " . TABLE_FAQ . " where faq_id = '" . os_db_input($faq_id) . "'");
        }
        break;

      case 'insert_faq':
        if ($_POST['question']) {
          $sql_data_array = array('question'   => os_db_prepare_input($_POST['question']),
                                  'faq_page_url'    => os_db_prepare_input($_POST['faq_page_url']),
                                  'answer'    => os_db_prepare_input($_POST['answer']),
                                  'date_added' => 'now()', 
                                  'language'   => os_db_prepare_input($_POST['item_language']),
                                  'status'     => '1' );
          os_db_perform(TABLE_FAQ, $sql_data_array);
          $faq_id = os_db_insert_id(); 
        }
        break;

      case 'update_faq': 
        if($_GET['faq_id']) {
          $sql_data_array = array('question' => os_db_prepare_input($_POST['question']),
                                  'faq_page_url'    => os_db_prepare_input($_POST['faq_page_url']),
                                  'answer'  => os_db_prepare_input($_POST['answer']),
                                  'date_added'  => os_db_prepare_input($_POST['date_added']),
                                  'language'   => os_db_prepare_input($_POST['item_language']),
                                  );
          os_db_perform(TABLE_FAQ, $sql_data_array, 'update', "faq_id = '" . os_db_prepare_input($_GET['faq_id']) . "'");
        }
        break;
    }
  }
  
  
  add_action('head_admin', 'head_faq');
  
  function head_faq ()
  {
      $query=os_db_query("SELECT code FROM ". TABLE_LANGUAGES ." WHERE languages_id='".$_SESSION['languages_id']."'");
      $data=os_db_fetch_array($query);
      if ($_GET['action']=='new_faq') echo os_wysiwyg_tiny('faq',$data['code']);
  
  }
?>


<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('portfolio_package.gif',HEADING_TITLE); ?> 
<?php 
$manual_link = 'add-faq';
if ($_GET['action'] == 'new_faq' and isset($_GET['faq_id'])) {
$manual_link = 'edit-faq';
}  
if ($_GET['action'] == 'delete_faq') {
$manual_link = 'delete-faq';
}  
?>
  
  </td>
  </tr>
  <tr>
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ($_GET['action'] == 'new_faq') { //insert or edit a faq
    if ( isset($_GET['faq_id']) ) { //editing exsiting faq
      $faq_query = os_db_query("select faq_id, question, language, faq_page_url, date_added, answer from " . TABLE_FAQ . " where faq_id = '" . $_GET['faq_id'] . "'");
      $faq = os_db_fetch_array($faq_query);
    } else { //adding new faq
      $faq = array();
    }
?>
      <tr><?php echo os_draw_form('new_faq', FILENAME_FAQ, isset($_GET['faq_id']) ? 'faq_id=' . $_GET['faq_id'] . '&action=update_faq' : 'action=insert_faq', 'post', 'enctype="multipart/form-data"'); ?>
        <td><table border="0" cellspacing="0" cellpadding="2" width="100%">
          <tr>
            <td class="main"><?php echo TEXT_FAQ_QUESTION; ?>:</td>
            <td class="main"><?php echo '&nbsp;' . os_draw_input_field('question', $faq['question'], 'size="60"', true); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_FAQ_ANSWER; ?>:</td>
<td class="main"><?php echo '&nbsp;' . os_draw_textarea_field('answer', '', '100%', '25', stripslashes($faq['answer'])); ?><br /><a href="javascript:toggleHTMLEditor('answer');" class="code"><?php echo TEXT_EDIT_E;?></a></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_FAQ_PAGE_URL; ?>:</td>
            <td class="main"><?php echo '&nbsp;' . os_draw_input_field('faq_page_url', $faq['faq_page_url'], 'size="60"', true); ?></td>
          </tr>

<?php
if ( isset($_GET['faq_id']) ) {
?>
          <tr>
            <td class="main"><?php echo TEXT_FAQ_DATE; ?>:</td>
            <td class="main"><?php echo '&nbsp;' .  os_draw_input_field('date_added', $faq['date_added'], '', true); ?></td>
          </tr>


<?php
}
?>

          <tr>
            <td class="main"><?php echo TEXT_FAQ_LANGUAGE; ?>:</td>
            <td class="main">
<?php

  $languages = os_get_languages();
  $languages_array = array();

  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                        
  if ($languages[$i]['id']==$faq['language']) {
         $languages_selected=$languages[$i]['id'];
         $languages_id=$languages[$i]['id'];
        }               
    $languages_array[] = array('id' => $languages[$i]['id'],
               'text' => $languages[$i]['name']);

  } // for
  
echo os_draw_pull_down_menu('item_language',$languages_array,$languages_selected); ?>

</td>
          </tr>


        </table></td>
      </tr>
      <tr>
        <td><?php echo os_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main" align="right">
          <?php
            isset($_GET['faq_id']) ? $cancel_button = '&nbsp;&nbsp;<a class="button" href="' . os_href_link(FILENAME_FAQ, 'faq_id=' . $_GET['faq_id']) . '"><span>' . BUTTON_CANCEL . '</span></a>' : $cancel_button = '';
            echo '<span class="button"><button type="submit" value="' . BUTTON_INSERT .'">' . BUTTON_INSERT .'</button></span>' . $cancel_button;
          ?>
        </td>
      </form></tr>
<?php

  } else {
?>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FAQ_QUESTION; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_FAQ_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_FAQ_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $rows = 0;

    $faq_count = 0;
    $faq_query_raw = 'select faq_id, question, faq_page_url, answer, status from ' . TABLE_FAQ . ' order by date_added desc';
	$faq_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $faq_query_raw, $faq_query_numrows);
    $faq_query = os_db_query($faq_query_raw);
    
    while ($faq = os_db_fetch_array($faq_query)) {
      $faq_count++;
      $rows++;
      
      if ( ((!$_GET['faq_id']) || (@$_GET['faq_id'] == $faq['faq_id'])) && (!$selected_item) && (substr($_GET['action'], 0, 4) != 'new_') ) {
        $selected_item = $faq;
      }
	  $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
      if ( (is_array($selected_item)) && ($faq['faq_id'] == $selected_item['faq_id']) ) {
        echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_FAQ, 'faq_id=' . $faq['faq_id']) . '\'">' . "\n";
      } else {
        echo '<tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_FAQ, 'faq_id=' . $faq['faq_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '&nbsp;' . $faq['question']; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($faq['status'] == '1') {
        echo os_image(http_path('icons_admin')  . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . os_href_link(FILENAME_FAQ, 'action=setflag&flag=0&faq_id=' . $faq['faq_id']) . '">' . os_image(http_path('icons_admin') . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . os_href_link(FILENAME_FAQ, 'action=setflag&flag=1&faq_id=' . $faq['faq_id']) . '">' . os_image(http_path('icons_admin') . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . os_image(http_path('icons_admin') . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td class="dataTableContent" align="right"><?php if ($faq['faq_id'] == $_GET['faq_id']) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . os_href_link(FILENAME_FAQ, 'faq_id=' . $faq['faq_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }

?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo '<br>' . TEXT_FAQ_ITEMS . '&nbsp;' . $faq_count; ?></td>
                    <td align="right" class="smallText"><?php echo '&nbsp;<a class="button" href="' . os_href_link(FILENAME_FAQ, 'action=new_faq') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?>&nbsp;</td>
                  </tr>																																		  
                </table></td>
              </tr>

            </table></td>
<?php
    $heading = array();
    $contents = array();
    switch ($_GET['action']) {
      case 'delete_faq': //generate box for confirming a faqdeletion
        $heading[] = array('text'   => '<b>' . TEXT_INFO_HEADING_DELETE_ITEM . '</b>');
        
        $contents = array('form'    => os_draw_form('faq', FILENAME_FAQ, 'action=delete_faq_confirm') . os_draw_hidden_field('faq_id', $_GET['faq_id']));
        $contents[] = array('text'  => TEXT_DELETE_ITEM_INTRO);
        $contents[] = array('text'  => '<br><b>' . $selected_item['question'] . '</b>');
        
        $contents[] = array('align' => 'center',
                            'text'  => '<br><span class="button"><button type="submit" value="' . BUTTON_DELETE .'">' . BUTTON_DELETE .'</button></span><a class="button" href="' . os_href_link(FILENAME_FAQ, 'faq_id=' . $selected_item['faq_id']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
        break;

      default:
        if ($rows > 0) {
          if (is_array($selected_item)) { //an item is selected, so make the side box
            $heading[] = array('text' => '<b>' . $selected_item['question'] . '</b>');

            $contents[] = array('align' => 'center', 
                                'text' => '<a class="button" href="' . os_href_link(FILENAME_FAQ, 'faq_id=' . $selected_item['faq_id'] . '&action=new_faq') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" href="' . os_href_link(FILENAME_FAQ, 'faq_id=' . $selected_item['faq_id'] . '&action=delete_faq') . '"><span>' . BUTTON_DELETE . '</span></a>');

            $contents[] = array('text' => '<br>' . $selected_item['answer']);
          }
        } else {
          $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');

          $contents[] = array('text' => sprintf(TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS, $parent_categories_name));
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