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

  require(_CLASS_ADMIN . 'currencies.php');
  $currencies = new currencies();

  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
      case 'save':
	  
        $currency_id = os_db_prepare_input($_GET['cID']);
        $title = os_db_prepare_input($_POST['title']);
        $code = os_db_prepare_input($_POST['code']);
        $symbol_left = os_db_prepare_input($_POST['symbol_left']);
        $symbol_right = os_db_prepare_input($_POST['symbol_right']);
        $decimal_point = os_db_prepare_input($_POST['decimal_point']);
        $thousands_point = os_db_prepare_input($_POST['thousands_point']);
        $decimal_places = os_db_prepare_input($_POST['decimal_places']);
        $value = os_db_prepare_input($_POST['value']);

		//$value = number_format($value, $decimal_places);
		
        $sql_data_array = array('title' => $title,
                                'code' => $code,
                                'symbol_left' => $symbol_left,
                                'symbol_right' => $symbol_right,
                                'decimal_point' => $decimal_point,
                                'thousands_point' => $thousands_point,
                                'decimal_places' => empty($decimal_places) ? 0: $decimal_places,
                                'value' => $value);
								
								
        if ($_GET['action'] == 'insert') {
          os_db_perform(TABLE_CURRENCIES, $sql_data_array);
          $currency_id = os_db_insert_id();
        } elseif ($_GET['action'] == 'save') {
          os_db_perform(TABLE_CURRENCIES, $sql_data_array, 'update', "currencies_id = '" . os_db_input($currency_id) . "'");
        }

        if ($_POST['default'] == 'on') {
          os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . os_db_input($code) . "' where configuration_key = 'DEFAULT_CURRENCY'");
          ///set_configuration_cache(); 
		}
		set_default_cache();
        os_redirect(os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $currency_id));
		
        break;

      case 'deleteconfirm':
        $currencies_id = os_db_prepare_input($_GET['cID']);

        $currency_query = os_db_query("select currencies_id from " . TABLE_CURRENCIES . " where code = '" . DEFAULT_CURRENCY . "'");
        $currency = os_db_fetch_array($currency_query);
        if ($currency['currencies_id'] == $currencies_id) {
          os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_CURRENCY'");
          ///set_configuration_cache(); 
		}

        os_db_query("delete from " . TABLE_CURRENCIES . " where currencies_id = '" . os_db_input($currencies_id) . "'");

		set_default_cache();
        os_redirect(os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page']));
        break;

      case 'update':
        $server_used = CURRENCY_SERVER_PRIMARY;
        $currency_query = os_db_query("select currencies_id, code, title from " . TABLE_CURRENCIES);
        while ($currency = os_db_fetch_array($currency_query)) {
          $quote_function = 'quote_' . CURRENCY_SERVER_PRIMARY . '_currency';
          $rate = $quote_function($currency['code']);
          if ( (!$rate) && (CURRENCY_SERVER_BACKUP != '') ) {
            $quote_function = 'quote_' . CURRENCY_SERVER_BACKUP . '_currency';
            $rate = $quote_function($currency['code']);
            $server_used = CURRENCY_SERVER_BACKUP;
          }
          if ($rate) {
            os_db_query("update " . TABLE_CURRENCIES . " set value = '" . $rate . "', last_updated = now() where currencies_id = '" . $currency['currencies_id'] . "'");
            $messageStack->add_session(sprintf(TEXT_INFO_CURRENCY_UPDATED, $currency['title'], $currency['code'], $server_used), 'success');
          } else {
            $messageStack->add_session(sprintf(ERROR_CURRENCY_INVALID, $currency['title'], $currency['code'], $server_used), 'error');
          }
        }
		set_default_cache();
        os_redirect(os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $_GET['cID']));
        break;

      case 'delete':
        $currencies_id = os_db_prepare_input($_GET['cID']);

        $currency_query = os_db_query("select code from " . TABLE_CURRENCIES . " where currencies_id = '" . os_db_input($currencies_id) . "'");
        $currency = os_db_fetch_array($currency_query);

        $remove_currency = true;
        if ($currency['code'] == DEFAULT_CURRENCY) {
          $remove_currency = false;
          $messageStack->add(ERROR_REMOVE_DEFAULT_CURRENCY, 'error');
        }
		set_default_cache();
        break;
    }
  }
?>

<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('coins.png',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow" align="center">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENCY_NAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENCY_CODES; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENCY_VALUE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $currency_query_raw = "select currencies_id, title, code, symbol_left, symbol_right, decimal_point, thousands_point, decimal_places, last_updated, value from " . TABLE_CURRENCIES . " order by title";
  $currency_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $currency_query_raw, $currency_query_numrows);
  $currency_query = os_db_query($currency_query_raw);

  while ($currency = os_db_fetch_array($currency_query)) 
  {
    if (((!$_GET['cID']) || (@$_GET['cID'] == $currency['currencies_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) 
	{
      $cInfo = new objectInfo($currency);
	  //$cInfo->value = number_format($cInfo->value, $cInfo->decimal_places);
    }
   
    $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff'; 
    if ( (is_object($cInfo)) && ($currency['currencies_id'] == $cInfo->currencies_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr onmouseover="this.style.background=\'#e9fff1\';this.style.cursor=\'hand\';" onmouseout="this.style.background=\''.$color.'\';" style="background-color:'.$color.'" onclick="document.location.href=\'' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $currency['currencies_id']) . '\'">' . "\n";
    }

    if (DEFAULT_CURRENCY == $currency['code']) {
      echo '                <td class="dataTableContent"><b>' . $currency['title'] . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {
      echo '                <td class="dataTableContent">' . $currency['title'] . '</td>' . "\n";
    }
	
	$_price = @number_format($currency['value'], $currency['decimal_places']);
	//$cInfo->value = number_format($cInfo->value, $cInfo->value);
?>
                <td class="dataTableContent"><?php echo $currency['code']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $_price; ?></td>
                <td class="dataTableContent" align="center"><?php if ( (is_object($cInfo)) && ($currency['currencies_id'] == $cInfo->currencies_id) ) { echo os_image(http_path('icons_admin') . 'icon_arrow_right.gif'); } else { echo '<a href="' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $currency['currencies_id']) . '">' . os_image(http_path('icons_admin') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $currency_split->display_count($currency_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CURRENCIES); ?></td>
                    <td class="smallText" align="right"><?php echo $currency_split->display_links($currency_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td><?php if (CURRENCY_SERVER_PRIMARY) { echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=update') . '"><span>' . BUTTON_UPDATE . '</span></a>'; } ?></td>
                    <td align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=new') . '"><span>' . BUTTON_NEW_CURRENCY . '</span></a>'; ?></td>
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
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CURRENCY . '</b>');

      $contents = array('form' => os_draw_form('currencies', FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_TITLE . '<br />' . os_draw_input_field('title'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_CODE . '<br />' . os_draw_input_field('code'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . '<br />' . os_draw_input_field('symbol_left'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '<br />' . os_draw_input_field('symbol_right'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_DECIMAL_POINT . '<br />' . os_draw_input_field('decimal_point'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_THOUSANDS_POINT . '<br />' . os_draw_input_field('thousands_point'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_DECIMAL_PLACES . '<br />' . os_draw_input_field('decimal_places'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_VALUE . '<br />' . os_draw_input_field('value'));
      $contents[] = array('text' => '<br />' . os_draw_checkbox_field('default') . ' ' . TEXT_INFO_SET_AS_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>' . BUTTON_INSERT . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $_GET['cID']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CURRENCY . '</b>');

      $contents = array('form' => os_draw_form('currencies', FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_TITLE . '<br />' . os_draw_input_field('title', $cInfo->title));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_CODE . '<br />' . os_draw_input_field('code', $cInfo->code));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . '<br />' . os_draw_input_field('symbol_left', $cInfo->symbol_left));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '<br />' . os_draw_input_field('symbol_right', $cInfo->symbol_right));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_DECIMAL_POINT . '<br />' . os_draw_input_field('decimal_point', $cInfo->decimal_point));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_THOUSANDS_POINT . '<br />' . os_draw_input_field('thousands_point', $cInfo->thousands_point));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_DECIMAL_PLACES . '<br />' . os_draw_input_field('decimal_places', $cInfo->decimal_places));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_VALUE . '<br />' . os_draw_input_field('value', $cInfo->value));
      if (DEFAULT_CURRENCY != $cInfo->code) $contents[] = array('text' => '<br />' . os_draw_checkbox_field('default') . ' ' . TEXT_INFO_SET_AS_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>' . BUTTON_UPDATE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CURRENCY . '</b>');

      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $cInfo->title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br />' . (($remove_currency) ? '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=deleteconfirm') . '"><span>' . BUTTON_DELETE . '</span></a>' : '') . ' <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->title . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $contents[] = array('text' => '<br /><b>' . TEXT_INFO_CURRENCY_TITLE . '</b> ' . $cInfo->title);
        $contents[] = array('text' => '<b>'.TEXT_INFO_CURRENCY_CODE  . '</b> ' . $cInfo->code);
        $contents[] = array('text' => '<br /><b>'. TEXT_INFO_CURRENCY_SYMBOL_LEFT. '</b> ' .$cInfo->symbol_left);
        $contents[] = array('text' => '<b>'.TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '</b> ' . $cInfo->symbol_right);
        $contents[] = array('text' => '<br /><b>' . TEXT_INFO_CURRENCY_DECIMAL_POINT . '</b> ' . $cInfo->decimal_point);
        $contents[] = array('text' => '<b>'.TEXT_INFO_CURRENCY_THOUSANDS_POINT . '</b> ' . $cInfo->thousands_point);
        $contents[] = array('text' => '<b>'.TEXT_INFO_CURRENCY_DECIMAL_PLACES . '</b> ' . $cInfo->decimal_places);
        $contents[] = array('text' => '<br /><b>' . TEXT_INFO_CURRENCY_LAST_UPDATED . '</b> ' . os_date_short($cInfo->last_updated));
        $contents[] = array('text' => '<b>'.TEXT_INFO_CURRENCY_VALUE . '</b> ' . $cInfo->value);
        $contents[] = array('text' => '<br /><b>'. TEXT_INFO_CURRENCY_EXAMPLE . '</b><br />' . $currencies->format('30', false, DEFAULT_CURRENCY) . ' = ' . $currencies->format('30', true, $cInfo->code));
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