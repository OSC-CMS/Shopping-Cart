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

  $xx_mins_ago = (time() - 900);

  require('includes/top.php');
  require(_CLASS.'price.php');
  
  os_db_query("delete from " . TABLE_WHOS_ONLINE . " where time_last_click < '" . $xx_mins_ago . "'");
  
  $main->head();  
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php os_header('portfolio_package.gif',HEADING_TITLE); 
	
	if (SET_WHOS_ONLINE == "true") {
	?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ONLINE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_CUSTOMER_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FULL_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_IP_ADDRESS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ENTRY_TIME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LAST_CLICK; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LAST_PAGE_URL; ?>&nbsp;</td>
              </tr>
<?php
  $whos_online_query = os_db_query("select customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, session_id from " . TABLE_WHOS_ONLINE ." order by time_last_click desc");
  while ($whos_online = os_db_fetch_array($whos_online_query)) {
    $time_online = (time() - $whos_online['time_entry']);
    if ( ((!$_GET['info']) || (@$_GET['info'] == $whos_online['session_id'])) && (!$info) ) {
      $info = $whos_online['session_id'];
    }
    if ($whos_online['session_id'] == $info) {
      echo '              <tr class="dataTableRowSelected">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . os_href_link(FILENAME_WHOS_ONLINE, os_get_all_get_params(array('info', 'action')) . 'info=' . $whos_online['session_id'], 'NONSSL') . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo gmdate('H:i:s', $time_online); ?></td>
                <td class="dataTableContent" align="center"><?php echo $whos_online['customer_id']; ?></td>
                <td class="dataTableContent"><?php echo $whos_online['full_name']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $whos_online['ip_address']; ?></td>
                <td class="dataTableContent"><?php echo date('H:i:s', $whos_online['time_entry']); ?></td>
                <td class="dataTableContent" align="center"><?php echo date('H:i:s', $whos_online['time_last_click']); ?></td>
                <td class="dataTableContent"><?php if (preg_match('/^(.*)' . os_session_name() . '=[a-f,0-9]+[&]*(.*)/i', $whos_online['last_page_url'], $array)) { echo Htmlspecialchars($array[1] . $array[2]); } else { echo Htmlspecialchars($whos_online['last_page_url']); } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td class="smallText" colspan="7"><?php echo sprintf(TEXT_NUMBER_OF_CUSTOMERS, os_db_num_rows($whos_online_query)); ?></td>
              </tr>
            </table></td>
<?php

  $heading = array();
  $contents = array();
  if (@$info) 
  {
    $heading[] = array('text' => '<b>' . TABLE_HEADING_SHOPPING_CART . '</b>');

	//фильтруем $info
    $info  = mysql_real_escape_string($info);
	   
	if (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $info)) 
    {
        die('OSC-CMS error: invalide session key.');
    }
	   
    if (STORE_SESSIONS == 'mysql') 
	{
      $session_data = os_db_query("select value from " . TABLE_SESSIONS . " WHERE sesskey = '" . $info . "'");
      $session_data = os_db_fetch_array($session_data);
      $session_data = trim($session_data['value']);
    } 
	else 
	{  
      if ( (file_exists(os_session_save_path() . '/sess_' . $info)) && (filesize(os_session_save_path() . '/sess_' . $info) > 0) ) {
        $session_data = file(os_session_save_path() . '/sess_' . $info);
        $session_data = trim(implode('', $session_data));
      }
    }

      $user_session = unserialize_session_data($session_data);
		
      if ($user_session) {
        $products = os_get_products($user_session);
        for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
          $contents[] = array('text' => $products[$i]['quantity'] . ' x ' . $products[$i]['name']);
        }

        if (sizeof($products) > 0) {
          $contents[] = array('text' => os_draw_separator('pixel_black.gif', '100%', '1'));
          $contents[] = array('align' => 'right', 'text'  => TEXT_SHOPPING_CART_SUBTOTAL . ' ' . $user_session['cart']->total . ' ' . $user_session['currency']);
        } else {
          $contents[] = array('text' => '&nbsp;');
        }
      }
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
    </table>
	
	<?php
	}
	else
	{
	    echo ("Модуль: ".HEADING_TITLE." - отключен;<br>");
	    echo ("<a href='configuration.php?gID=17'>Настройка -> Разное -> Быстрое оформление</a>");
	}
	?>
	</td>
  </tr>
</table>
<?php $main->bottom(); ?>