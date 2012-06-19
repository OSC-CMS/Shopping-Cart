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

$gID = 26;

  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'save':
           ///set_configuration_cache();
          $configuration_query = os_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . (int)$gID . "' order by sort_order");

          while ($configuration = os_db_fetch_array($configuration_query))
              os_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$_POST[$configuration['configuration_key']]."' where configuration_key='".$configuration['configuration_key']."'");
               
			  /// set_configuration_cache(); 
               os_redirect(FILENAME_ARTICLES_CONFIG);
        break;

    }
  }

?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main">
         <?php os_header('page_white_edit.png',HEADING_TITLE); ?> 
		 
<?php echo os_draw_form('configuration', FILENAME_ARTICLES_CONFIG, 'gID=' . (int)$gID . '&action=save'); ?>
            <table width="100%"  border="0" cellspacing="0" cellpadding="4">
<?php
  $configuration_query = os_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . (int)$gID . "' order by sort_order");

  while ($configuration = os_db_fetch_array($configuration_query)) {
    if ($gID == 6) {
      switch ($configuration['configuration_key']) {
        case 'MODULE_PAYMENT_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $payment_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $payment_installed[$i]);
            }
          }
          break;

        case 'MODULE_SHIPPING_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $shipping_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/' . $shipping_installed[$i]);			
            }
          }
          break;

        case 'MODULE_ORDER_TOTAL_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $ot_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $ot_installed[$i]);			
            }
          }
          break;
      }
    }
    if (os_not_null($configuration['use_function'])) {
      $use_function = $configuration['use_function'];
      if (ereg('->', $use_function)) {
        $class_method = explode('->', $use_function);
        if (!is_object(${$class_method[0]})) {
          include(get_path('class_admin') . $class_method[0] . '.php');
          ${$class_method[0]} = new $class_method[0]();
        }
        $cfgValue = os_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
      } else {
        $cfgValue = os_call_function($use_function, $configuration['configuration_value']);
      }
    } else {
      $cfgValue = $configuration['configuration_value'];
    }

    if (((!$_GET['cID']) || (@$_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $cfg_extra_query = os_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
      $cfg_extra = os_db_fetch_array($cfg_extra_query);

      $cInfo_array = os_array_merge($configuration, $cfg_extra);
      $cInfo = new objectInfo($cInfo_array);
    }
    if ($configuration['set_function']) {
        eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars($configuration['configuration_value']) . '");');
      } else {
        $value_field = os_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'],'size=15');
      }

   if (strstr($value_field,'configuration_value')) $value_field=str_replace('configuration_value',$configuration['configuration_key'],$value_field);

  
   echo '<tr style="padding-top:2cm; background-color:';
   $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff'; 
  
   echo '">
      <td style="padding-bottom: 10px;padding-top: 10px;" valign="middle" width="200" align="center">'.$value_field.'</td>
    <td style="padding-bottom: 10px;padding-top: 10px;" valign="top" align="left"><b>'.constant(strtoupper($configuration['configuration_key'].'_TITLE')).'</b><br>'.constant(strtoupper( $configuration['configuration_key'].'_DESC')).'</td>

  </tr>
  ';

  }
?>
            </table><br>
			<div align="right"><a class="button" onclick="document.configuration.submit()" href="#"><span><?php echo BUTTON_SAVE;  ?></span></a></div>
</form>

            </td>

          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>