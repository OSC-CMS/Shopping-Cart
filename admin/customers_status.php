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

  switch (@$_GET['action']) {
    case 'insert':
    case 'save':
      $customers_status_id = os_db_prepare_input($_GET['cID']);

      $languages = os_get_languages();
      for ($i=0; $i<sizeof($languages); $i++) {
        $customers_status_name_array = $_POST['customers_status_name'];
        $customers_status_public = $_POST['customers_status_public'];
        $customers_status_show_price = $_POST['customers_status_show_price'];
        $customers_status_show_price_tax = $_POST['customers_status_show_price_tax'];
        $customers_status_min_order = $_POST['customers_status_min_order'];
        $customers_status_max_order = $_POST['customers_status_max_order'];
        $customers_status_discount = $_POST['customers_status_discount'];
        $customers_status_ot_discount_flag = $_POST['customers_status_ot_discount_flag'];
        $customers_status_ot_discount = $_POST['customers_status_ot_discount'];
        $customers_status_graduated_prices = $_POST['customers_status_graduated_prices'];
        $customers_status_discount_attributes = $_POST['customers_status_discount_attributes'];
        $customers_status_add_tax_ot = $_POST['customers_status_add_tax_ot'];
        $customers_status_payment_unallowed = $_POST['customers_status_payment_unallowed'];
        $customers_status_shipping_unallowed = $_POST['customers_status_shipping_unallowed'];
        $customers_fsk18 = $_POST['customers_fsk18'];
        $customers_fsk18_display = $_POST['customers_fsk18_display'];
        $customers_status_write_reviews = $_POST['customers_status_write_reviews'];
        $customers_status_read_reviews = $_POST['customers_status_read_reviews'];
        $customers_status_accumulated_limit = $_POST['customers_status_accumulated_limit'];
        $customers_base_status = $_POST['customers_base_status'];        

        $language_id = $languages[$i]['id'];

        $sql_data_array = array(
          'customers_status_name' => os_db_prepare_input($customers_status_name_array[$language_id]),
          'customers_status_public' => os_db_prepare_input($customers_status_public),
          'customers_status_show_price' => os_db_prepare_input($customers_status_show_price),
          'customers_status_show_price_tax' => os_db_prepare_input($customers_status_show_price_tax),
          'customers_status_min_order' => os_db_prepare_input($customers_status_min_order),
          'customers_status_max_order' => os_db_prepare_input($customers_status_max_order),
          'customers_status_discount' => os_db_prepare_input($customers_status_discount),
          'customers_status_ot_discount_flag' => os_db_prepare_input($customers_status_ot_discount_flag),
          'customers_status_ot_discount' => os_db_prepare_input($customers_status_ot_discount),
          'customers_status_graduated_prices' => os_db_prepare_input($customers_status_graduated_prices),
          'customers_status_add_tax_ot' => os_db_prepare_input($customers_status_add_tax_ot),
          'customers_status_payment_unallowed' => os_db_prepare_input($customers_status_payment_unallowed),
          'customers_status_shipping_unallowed' => os_db_prepare_input($customers_status_shipping_unallowed),
          'customers_fsk18' => os_db_prepare_input($customers_fsk18),
          'customers_fsk18_display' => os_db_prepare_input($customers_fsk18_display),
          'customers_status_write_reviews' => os_db_prepare_input($customers_status_write_reviews),
          'customers_status_read_reviews' => os_db_prepare_input($customers_status_read_reviews),
          'customers_status_accumulated_limit' => os_db_prepare_input($customers_status_accumulated_limit),
          'customers_status_discount_attributes' => os_db_prepare_input($customers_status_discount_attributes)
        );
        
        if ($_GET['action'] == 'insert') {
          if (!os_not_null($customers_status_id)) {
            $next_id_query = os_db_query("select max(customers_status_id) as customers_status_id from " . TABLE_CUSTOMERS_STATUS . "");
            $next_id = os_db_fetch_array($next_id_query);
            $customers_status_id = $next_id['customers_status_id'] + 1;
            os_db_query("create table IF NOT EXISTS ".TABLE_PERSONAL_OFFERS.$customers_status_id . " (price_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, products_id int NOT NULL, quantity int, personal_offer decimal(15,4)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");
		   os_db_query("ALTER TABLE  `".DB_PREFIX."products` ADD  `group_permission_" . $customers_status_id . "` TINYINT( 1 ) NOT NULL");
		   os_db_query("ALTER TABLE  `".DB_PREFIX."categories` ADD  `group_permission_" . $customers_status_id . "` TINYINT( 1 ) NOT NULL");

        $products_query = os_db_query("select price_id, products_id, quantity, personal_offer from ".TABLE_PERSONAL_OFFERS.$customers_base_status ."");
        while($products = os_db_fetch_array($products_query)){  
        $product_data_array = array(
          'price_id' => os_db_prepare_input($products['price_id']),
          'products_id' => os_db_prepare_input($products['products_id']),
          'quantity' => os_db_prepare_input($products['quantity']),
          'personal_offer' => os_db_prepare_input($products['personal_offer'])
         );          
         os_db_perform(TABLE_PERSONAL_OFFERS.$customers_status_id, $product_data_array);
         } 

          }

          $insert_sql_data = array('customers_status_id' => os_db_prepare_input($customers_status_id), 'language_id' => os_db_prepare_input($language_id));
          $sql_data_array = os_array_merge($sql_data_array, $insert_sql_data);
          os_db_perform(TABLE_CUSTOMERS_STATUS, $sql_data_array);
 
        } elseif ($_GET['action'] == 'save') {
          os_db_perform(TABLE_CUSTOMERS_STATUS, $sql_data_array, 'update', "customers_status_id = '" . os_db_input($customers_status_id) . "' and language_id = '" . $language_id . "'");
        }
      }
  
      if ($customers_status_image = &os_try_upload('customers_status_image', _ICONS)) {
        os_db_query("update " . TABLE_CUSTOMERS_STATUS . " set customers_status_image = '" . $customers_status_image->filename . "' where customers_status_id = '" . os_db_input($customers_status_id) . "'");
      }

      if ($_POST['default'] == 'on') {
        os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . os_db_input($customers_status_id) . "' where configuration_key = 'DEFAULT_CUSTOMERS_STATUS_ID'");
       // set_configuration_cache(); 
	  }

        os_db_query("delete from " . TABLE_CUSTOMERS_STATUS_ORDERS_STATUS . " where customers_status_id = " .  os_db_input($customers_status_id));
        $orders_status_query = os_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where language_id = " . $_SESSION['languages_id'] . " order by orders_status_id");
        while ($orders_status = os_db_fetch_array($orders_status_query)) {
           if ($_POST['orders_status_' . $orders_status['orders_status_id']]) {
              os_db_query("insert into " . TABLE_CUSTOMERS_STATUS_ORDERS_STATUS . " values (" .  os_db_input($customers_status_id) . ", " . $orders_status['orders_status_id'] . ")");
           }
        }

      os_redirect(os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $customers_status_id));
      break;

    case 'deleteconfirm':
      $cID = os_db_prepare_input($_GET['cID']);

      $customers_status_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_CUSTOMERS_STATUS_ID'");
      $customers_status = os_db_fetch_array($customers_status_query);
      if ($customers_status['configuration_value'] == $cID) {
        os_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_CUSTOMERS_STATUS_ID'");
      }

      os_db_query("delete from " . TABLE_CUSTOMERS_STATUS . " where customers_status_id = '" . os_db_input($cID) . "'");

      os_db_query("delete from " . TABLE_CUSTOMERS_STATUS_ORDERS_STATUS . " where customers_status_id = " .  os_db_input($cID));

      os_db_query("drop table IF EXISTS ".TABLE_PERSONAL_OFFERS.os_db_input($cID) . "");
      os_db_query("ALTER TABLE `".DB_PREFIX."products` DROP `group_permission_" . os_db_input($cID) . "`");
      os_db_query("ALTER TABLE `".DB_PREFIX."categories` DROP `group_permission_" . os_db_input($cID) . "`");
      os_redirect(os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page']));
      break;

    case 'delete':
      $cID = os_db_prepare_input($_GET['cID']);

      $status_query = os_db_query("select count(*) as count from " . TABLE_CUSTOMERS . " where customers_status = '" . os_db_input($cID) . "'");
      $status = os_db_fetch_array($status_query);

      $remove_status = true;
      if (($cID == DEFAULT_CUSTOMERS_STATUS_ID) || ($cID == DEFAULT_CUSTOMERS_STATUS_ID_GUEST) || ($cID == DEFAULT_CUSTOMERS_STATUS_ID_NEWSLETTER)) {
        $remove_status = false;
        $messageStack->add(ERROR_REMOVE_DEFAULT_CUSTOMERS_STATUS, 'error');
      } elseif ($status['count'] > 0) {
        $remove_status = false;
        $messageStack->add(ERROR_STATUS_USED_IN_CUSTOMERS, 'error');
      } else {
        $history_query = os_db_query("select count(*) as count from " . TABLE_CUSTOMERS_STATUS_HISTORY . " where '" . os_db_input($cID) . "' in (new_value, old_value)");
        $history = os_db_fetch_array($history_query);
        if ($history['count'] > 0) {
          os_db_query("DELETE FROM " . TABLE_CUSTOMERS_STATUS_HISTORY . "
                        where '" . os_db_input($cID) . "' in (new_value, old_value)");
          $remove_status = true;
        }
      }
      break;
  }
?>

<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php $main->heading('user_green.png',HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="left" width=""><?php echo TABLE_HEADING_ICON; ?></td>
                <td class="dataTableHeadingContent" align="left" width=""><?php echo TABLE_HEADING_USERS; ?></td>
                <td class="dataTableHeadingContent" align="left" width=""><?php echo TABLE_HEADING_CUSTOMERS_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="center" width=""><?php echo TABLE_HEADING_TAX_PRICE; ?></td>
                <td class="dataTableHeadingContent" align="center" colspan="2"><?php echo TABLE_HEADING_DISCOUNT; ?></td>
                <td class="dataTableHeadingContent" width=""><?php echo TABLE_HEADING_CUSTOMERS_GRADUATED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $customers_status_ot_discount_flag_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_graduated_prices_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_public_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_show_price_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_show_price_tax_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_discount_attributes_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_add_tax_ot_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_fsk18_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_fsk18_display_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_write_reviews_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_read_reviews_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));

  $customers_status_query_raw = "select * from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "' order by customers_status_id";

  $customers_status_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ADMIN_PAGE, $customers_status_query_raw, $customers_status_query_numrows);
  $customers_status_query = os_db_query($customers_status_query_raw);
  
 $color = '';
 
  while ($customers_status = os_db_fetch_array($customers_status_query)) 
  {
    if (((!isset($_GET['cID'])) || ($_GET['cID'] == $customers_status['customers_status_id'])) && (!isset($cInfo)) && (substr(isset($_GET['action'])?$_GET['action']:'', 0, 3) != 'new')) {
      $cInfo = new objectInfo($customers_status);
    }

	$color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
    if (isset($cInfo) && (is_object($cInfo)) && ($customers_status['customers_status_id'] == $cInfo->customers_status_id) ) 
	{
      echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '<tr style="background-color:'.$color.'" class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $customers_status['customers_status_id']) . '\'">' . "\n";
    }

    echo '<td class="dataTableContent"  align="center">';
     if ($customers_status['customers_status_image'] != '') {
       echo os_image(http_path('icons_admin') . $customers_status['customers_status_image'] , IMAGE_ICON_INFO);
     }
     echo '</td>';

     echo '<td class="dataTableContent" align="left">';
     echo os_get_status_users($customers_status['customers_status_id']);
     echo '</td>';

    if ($customers_status['customers_status_id'] == DEFAULT_CUSTOMERS_STATUS_ID ) {
      echo '<td class="dataTableContent" align="left"><b>' . $customers_status['customers_status_name'];
      echo ' (' . TEXT_DEFAULT . ')';
    } else {
      echo '<td class="dataTableContent" align="left">' . $customers_status['customers_status_name'];
    }
    if ($customers_status['customers_status_public'] == '1') {
      echo TEXT_PUBLIC;
    }
    echo '</b></td>';

    if ($customers_status['customers_status_show_price'] == '1') {
      echo '<td nowrap class="dataTableContent" align="center">' . YES . ' / ';
      if ($customers_status['customers_status_show_price_tax'] == '1') {
        echo TAX_YES;
      } else {
        echo TAX_NO;
      }
    } else {
      echo '<td class="dataTableContent" align="left"> ';
    }
    echo '</td>';

    echo '<td nowrap class="dataTableContent" align="center">' . $customers_status['customers_status_discount'] . ' %</td>';
      
    echo '<td nowrap class="dataTableContent" align="center">';
    if ($customers_status['customers_status_ot_discount_flag'] == 0){
      echo '<font color="ff0000">'.$customers_status['customers_status_ot_discount'].' %</font>';
    } else {
      echo $customers_status['customers_status_ot_discount'].' %';
    }
    echo ' </td>';
  
    echo '<td class="dataTableContent" align="center">';
    if ($customers_status['customers_status_graduated_prices'] == 0) {
      echo NO;
    } else {
      echo YES;
    }
    echo '</td>';
    echo '<!--<td nowrap class="dataTableContent" align="center">' . $customers_status['customers_status_payment_unallowed'] . '&nbsp;</td>';
    echo '<td nowrap class="dataTableContent" align="center">' . $customers_status['customers_status_shipping_unallowed'] . '&nbsp;</td>-->';
    echo "\n";
?>
                <td class="dataTableContent" align="right">
				<?php if (isset($cInfo) && (is_object($cInfo)) && ($customers_status['customers_status_id'] == $cInfo->customers_status_id) )
				
{ 	
		echo os_image(get_path('icons_admin', 'http') . 'icon_arrow_right.gif', ''); 
} 
else 
{ echo '<a href="' . os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $customers_status['customers_status_id']) . '">' . os_image(get_path('icons_admin', 'http') . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="10"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr >
                    <td class="smallText" valign="top"><?php echo $customers_status_split->display_count($customers_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS_STATUS); ?></td>
                    <td class="smallText" align="right"><?php echo $customers_status_split->display_links($customers_status_query_numrows, MAX_DISPLAY_ADMIN_PAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (substr(isset($_GET['action'])?$_GET['action']:'', 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&action=new') . '"><span>' . BUTTON_INSERT . '</span></a>'; ?></td>
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
  switch (@$_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CUSTOMERS_STATUS . '</b>');
      $contents = array('form' => os_draw_form('status', FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $customers_status_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0; $i<sizeof($languages); $i++) 
	  {
	    if ($languages[$i]['status']==1)
		{
        $customers_status_inputs_string .= '<br />' . os_image( http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif' , $languages[$i]['name']) . '&nbsp;' . os_draw_input_field('customers_status_name[' . $languages[$i]['id'] . ']');
		}
      }
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_NAME . $customers_status_inputs_string);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_IMAGE . '<br />' . os_draw_file_field('customers_status_image'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_PUBLIC_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_PUBLIC . ' ' . os_draw_pull_down_menu('customers_status_public', $customers_status_public_array, $cInfo->customers_status_public ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_MIN_ORDER_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_MIN_ORDER . ' ' . os_draw_input_field('customers_status_min_order', $cInfo->customers_status_min_order ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_MAX_ORDER_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_MAX_ORDER . ' ' . os_draw_input_field('customers_status_max_order', $cInfo->customers_status_max_order ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_INTRO     . '<br />' . ENTRY_CUSTOMERS_STATUS_SHOW_PRICE . ' ' . os_draw_pull_down_menu('customers_status_show_price', $customers_status_show_price_array, $cInfo->customers_status_show_price ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_TAX_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_SHOW_PRICE_TAX . ' ' . os_draw_pull_down_menu('customers_status_show_price_tax', $customers_status_show_price_tax_array, $cInfo->customers_status_show_price_tax ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_ADD_TAX_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_ADD_TAX . ' ' . os_draw_pull_down_menu('customers_status_add_tax_ot', $customers_status_add_tax_ot_array, $cInfo->customers_status_add_tax_ot));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO . '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE . '<br />' . os_draw_input_field('customers_status_discount', $cInfo->customers_status_discount));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO     . '<br />' . ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES . ' ' . os_draw_pull_down_menu('customers_status_discount_attributes', $customers_status_discount_attributes_array, $cInfo->customers_status_discount_attributes ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO . '<br /> ' . ENTRY_OT_XMEMBER . ' ' . os_draw_pull_down_menu('customers_status_ot_discount_flag', $customers_status_ot_discount_flag_array, $cInfo->customers_status_ot_discount_flag ). '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE . '<br />' . os_draw_input_field('customers_status_ot_discount', $cInfo->customers_status_ot_discount));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO . '<br />' . ENTRY_GRADUATED_PRICES . ' ' . os_draw_pull_down_menu('customers_status_graduated_prices', $customers_status_graduated_prices_array, $cInfo->customers_status_graduated_prices ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES . ' ' . os_draw_pull_down_menu('customers_status_discount_attributes', $customers_status_discount_attributes_array, $cInfo->customers_status_discount_attributes ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED . ' ' . os_draw_input_field('customers_status_payment_unallowed', $cInfo->customers_status_payment_unallowed ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED . ' ' . os_draw_input_field('customers_status_shipping_unallowed', $cInfo->customers_status_shipping_unallowed ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_FSK18_INTRO . '<br />' . ENTRY_CUSTOMERS_FSK18 . ' ' . os_draw_pull_down_menu('customers_fsk18', $customers_fsk18_array, $cInfo->customers_fsk18));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_FSK18_DISPLAY_INTRO . '<br />' . ENTRY_CUSTOMERS_FSK18_DISPLAY . ' ' . os_draw_pull_down_menu('customers_fsk18_display', $customers_fsk18_display_array, $cInfo->customers_fsk18_display));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_WRITE_REVIEWS_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_WRITE_REVIEWS . ' ' . os_draw_pull_down_menu('customers_status_write_reviews', $customers_status_write_reviews_array, $cInfo->customers_status_write_reviews));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_READ_REVIEWS_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_READ_REVIEWS_DISPLAY . ' ' . os_draw_pull_down_menu('customers_status_read_reviews', $customers_status_read_reviews_array, $cInfo->customers_status_read_reviews));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_DISPLAY . ' ' . os_draw_input_field('customers_status_accumulated_limit', $cInfo->customers_status_accumulated_limit));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_ORDERS_STATUS_INTRO . '<br />' . TEXT_INFO_CUSTOMERS_STATUS_ORDERS_STATUS_DISPLAY);

  $orders_status_query = os_db_query("select * from " . TABLE_ORDERS_STATUS . " where language_id = " . $_SESSION['languages_id'] . " order by orders_status_id");
  while ($orders_status = os_db_fetch_array($orders_status_query)) {

      $contents[] = array('text' => '<input type="checkbox" name="orders_status_' . $orders_status['orders_status_id'] . '" value="1">' . $orders_status['orders_status_name'] . '<br />');

}
   
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_BASE . '<br />' . ENTRY_CUSTOMERS_STATUS_BASE . '<br />' . os_draw_pull_down_menu('customers_base_status', os_get_customers_statuses()));
      $contents[] = array('text' => '<br />' . os_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" class="button" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>'.BUTTON_INSERT.'</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page']) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CUSTOMERS_STATUS . '</b>');
      $contents = array('form' => os_draw_form('status', FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id  .'&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $customers_status_inputs_string = '';
      $languages = os_get_languages();
      for ($i=0; $i<sizeof($languages); $i++)
	  {
	  if ($languages[$i]['status']==1)
	  {
        $customers_status_inputs_string .= '<br />' . os_image( http_path('icons_admin').'lang/'.$languages[$i]['directory'].'.gif', $languages[$i]['name']) . '&nbsp;' . os_draw_input_field('customers_status_name[' . $languages[$i]['id'] . ']', os_get_customers_status_name($cInfo->customers_status_id, $languages[$i]['id']));
      }
	  }
	  
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_NAME . $customers_status_inputs_string);
      $contents[] = array('text' => '<br />' . os_image(http_path('icons_admin') . $cInfo->customers_status_image, $cInfo->customers_status_name) . '<br />'.'<br /><b>' . $cInfo->customers_status_image . '</b>');
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_IMAGE . '<br />' . os_draw_file_field('customers_status_image', $cInfo->customers_status_image));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_PUBLIC_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_PUBLIC . ' ' . os_draw_pull_down_menu('customers_status_public', $customers_status_public_array, $cInfo->customers_status_public ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_MIN_ORDER_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_MIN_ORDER . ' ' . os_draw_input_field('customers_status_min_order', $cInfo->customers_status_min_order ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_MAX_ORDER_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_MAX_ORDER . ' ' . os_draw_input_field('customers_status_max_order', $cInfo->customers_status_max_order )); 
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_INTRO     . '<br />' . ENTRY_CUSTOMERS_STATUS_SHOW_PRICE . ' ' . os_draw_pull_down_menu('customers_status_show_price', $customers_status_show_price_array, $cInfo->customers_status_show_price ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_TAX_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_SHOW_PRICE_TAX . ' ' . os_draw_pull_down_menu('customers_status_show_price_tax', $customers_status_show_price_tax_array, $cInfo->customers_status_show_price_tax ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_ADD_TAX_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_ADD_TAX . ' ' . os_draw_pull_down_menu('customers_status_add_tax_ot', $customers_status_add_tax_ot_array, $cInfo->customers_status_add_tax_ot));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO . '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE . ' ' . os_draw_input_field('customers_status_discount', $cInfo->customers_status_discount));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES . ' ' . os_draw_pull_down_menu('customers_status_discount_attributes', $customers_status_discount_attributes_array, $cInfo->customers_status_discount_attributes ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO . '<br /> ' . ENTRY_OT_XMEMBER . ' ' . os_draw_pull_down_menu('customers_status_ot_discount_flag', $customers_status_ot_discount_flag_array, $cInfo->customers_status_ot_discount_flag). '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE . ' ' . os_draw_input_field('customers_status_ot_discount', $cInfo->customers_status_ot_discount));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO . '<br />' . ENTRY_GRADUATED_PRICES . ' ' . os_draw_pull_down_menu('customers_status_graduated_prices', $customers_status_graduated_prices_array, $cInfo->customers_status_graduated_prices));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED . ' ' . os_draw_input_field('customers_status_payment_unallowed', $cInfo->customers_status_payment_unallowed ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED . ' ' . os_draw_input_field('customers_status_shipping_unallowed', $cInfo->customers_status_shipping_unallowed ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_FSK18_INTRO . '<br />' . ENTRY_CUSTOMERS_FSK18 . ' ' . os_draw_pull_down_menu('customers_fsk18', $customers_fsk18_array, $cInfo->customers_fsk18 ));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_FSK18_DISPLAY_INTRO . '<br />' . ENTRY_CUSTOMERS_FSK18_DISPLAY . ' ' . os_draw_pull_down_menu('customers_fsk18_display', $customers_fsk18_display_array, $cInfo->customers_fsk18_display));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_WRITE_REVIEWS_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_WRITE_REVIEWS . ' ' . os_draw_pull_down_menu('customers_status_write_reviews', $customers_status_write_reviews_array, $cInfo->customers_status_write_reviews));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_READ_REVIEWS_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_READ_REVIEWS . ' ' . os_draw_pull_down_menu('customers_status_read_reviews', $customers_status_read_reviews_array, $cInfo->customers_status_read_reviews));
      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_DISPLAY . ' ' . os_draw_input_field('customers_status_accumulated_limit', $cInfo->customers_status_accumulated_limit));

      $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_ORDERS_STATUS_INTRO . '<br />' . TEXT_INFO_CUSTOMERS_STATUS_ORDERS_STATUS_DISPLAY);

  $orders_status_query = os_db_query("select * from " . TABLE_ORDERS_STATUS . " where language_id = " . $_SESSION['languages_id'] . " order by orders_status_id");
  while ($orders_status = os_db_fetch_array($orders_status_query)) {
    $check_status_query = os_db_query("select orders_status_id from " . TABLE_CUSTOMERS_STATUS_ORDERS_STATUS . " where customers_status_id = " . $cInfo->customers_status_id . " and orders_status_id = " . $orders_status['orders_status_id']);
    if (os_db_num_rows($check_status_query)) {
      $selected = 'checked';
    } else {
      $selected = '';
    }

      $contents[] = array('text' => '<input type="checkbox" name="orders_status_' . $orders_status['orders_status_id'] . '" value="1" ' . $selected . '>' . $orders_status['orders_status_name'] . '<br />');

}

      if (DEFAULT_CUSTOMERS_STATUS_ID != $cInfo->customers_status_id) $contents[] = array('text' => '<br />' . os_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPDATE . '">' . BUTTON_UPDATE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CUSTOMERS_STATUS . '</b>');

      $contents = array('form' => os_draw_form('status', FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $cInfo->customers_status_name . '</b>');

      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br /><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_DELETE . '">' . BUTTON_DELETE . '</button></span> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id) . '"><span>' . BUTTON_CANCEL . '</span></a>');
      break;

    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->customers_status_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id . '&action=edit') . '"><span>' . BUTTON_EDIT . '</span></a> <a class="button" onClick="this.blur();" href="' . os_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id . '&action=delete') . '"><span>' . BUTTON_DELETE . '</span></a>');
        $customers_status_inputs_string = '';
        $languages = os_get_languages();
        for ($i=0; $i<sizeof($languages); $i++) 
		{
		  if ($languages[$i]['status']==1)
		  {
          $customers_status_inputs_string .= '<br />' . os_image( http_path('icons_admin').'lang/'. $languages[$i]['directory'] . '.gif', $languages[$i]['name']) . '&nbsp;' . os_get_customers_status_name($cInfo->customers_status_id, $languages[$i]['id']);
		  }
        }
        $contents[] = array('text' => $customers_status_inputs_string);
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO . '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE . ' ' . $cInfo->customers_status_discount . '%');
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO . '<br />' . ENTRY_OT_XMEMBER . ' ' . $customers_status_ot_discount_flag_array[$cInfo->customers_status_ot_discount_flag]['text'] . ' (' . $cInfo->customers_status_ot_discount_flag . ')' . ' - ' . $cInfo->customers_status_ot_discount . '%');
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO . '<br />' . ENTRY_GRADUATED_PRICES . ' ' . $customers_status_graduated_prices_array[$cInfo->customers_status_graduated_prices]['text'] . ' (' . $cInfo->customers_status_graduated_prices . ')' );
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES . ' ' . $customers_status_discount_attributes_array[$cInfo->customers_status_discount_attributes]['text'] . ' (' . $cInfo->customers_status_discount_attributes . ')' );
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED . ':<b> ' . $cInfo->customers_status_payment_unallowed.'</b>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED . ':<b> ' . $cInfo->customers_status_shipping_unallowed.'</b>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_ACCUMULATED_LIMIT_DISPLAY . ':<b> ' . $cInfo->customers_status_accumulated_limit.'</b>');
      }
      break;
  }

  if ( (os_not_null($heading)) && (os_not_null($contents)) ) {
    echo '<td class="right_box" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '</td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>