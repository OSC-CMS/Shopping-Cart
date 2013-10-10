<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

  require('includes/top.php');
  require(get_path('class_admin') . 'import.php');

  switch ($_GET['action']) {

      case 'upload':
        $upload_file=os_db_prepare_input($_POST['file_upload']);
        if ($upload_file = &os_try_upload('file_upload',_IMPORT)) {
            $$upload_file_name=$upload_file->filename;
        }
      break;

      case 'import':
           $handler = new osImport($_POST['select_file']);
           $mapping=$handler->map_file($handler->generate_map());
           $import=$handler->import($mapping);
      break;

      case 'export':
            $handler = new osExport('export.csv');
            $import=$handler->exportProdFile();
      break;

      case 'save':

          $configuration_query = os_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '20' order by sort_order");

          while ($configuration = os_db_fetch_array($configuration_query))
              os_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$_POST[$configuration['configuration_key']]."' where configuration_key='".$configuration['configuration_key']."'");

			 /// set_configuration_cache(); 
               os_redirect(FILENAME_CSV_BACKEND);
        break;
  }


?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
     <a style="right:20px;position:absolute;" class="button" onclick="document.configuration.submit()" href="<?php echo FILENAME_EASY_POPULATE; ?>"><span><?php echo BOX_EASY_POPULATE;  ?></span></a>
    <?php echo HEADING_TITLE; ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main">
        <table class="infoBoxHeading" width="100%">
            <tr>
                <td width="150" align="center">
                <a href="#" onClick="toggleBox('config');"><?php echo CSV_SETUP; ?></a>
                </td>
                <td width="1">|
                </td>
                <td>
                </td>
            </tr>
        </table>
<div id="config" class="longDescription">
<?php echo os_draw_form('configuration', FILENAME_CSV_BACKEND, 'gID=20&action=save'); ?>
            <table width="100%"  border="0" cellspacing="0" cellpadding="4">
<?php
  $configuration_query = os_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '20' order by sort_order");

  while ($configuration = os_db_fetch_array($configuration_query)) {
    if ($_GET['gID'] == 6) {
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
        $value_field = os_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'],'size=40');
      }
   if (strstr($value_field,'configuration_value')) $value_field=str_replace('configuration_value',$configuration['configuration_key'],$value_field);

   echo '
  <tr>
    <td width="300" valign="top" class="dataTableContent"><b>'.constant(strtoupper($configuration['configuration_key'].'_TITLE')).'</b></td>
    <td valign="top" class="dataTableContent">
    <table width="100%"  border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td style="background-color:#FCF2CF ; border: 1px solid; border-color: #CCCCCC;" class="dataTableContent">'.$value_field.'</td>
      </tr>
    </table>
    <br />'.constant(strtoupper( $configuration['configuration_key'].'_DESC')).'</td>
  </tr>
  ';

  }
?>
            </table>
<?php echo '<span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>' . BUTTON_SAVE . '</button></span>'; ?></form>
</div>
<?php

  if ($import)
  {
     if ($import[0])
     {
      echo '<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="messageStackSuccess"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                    ';

                   if (isset($import[0]['prod_new'])) echo 'new products:'.$import[0]['prod_new'].'<br />';
                   if (isset($import[0]['cat_new'])) echo 'new categories:'.$import[0]['cat_new'].'<br />';
                   if (isset($import[0]['prod_upd'])) echo 'updated products:'.$import[0]['prod_upd'].'<br />';
                   if (isset($import[0]['cat_upd'])) echo 'updated categories:'.$import[0]['cat_upd'].'<br />';
                   if (isset($import[0]['cat_touched'])) echo 'touched categories:'.$import[0]['cat_touched'].'<br />';
                   if (isset($import[0]['prod_exp'])) echo 'products exported:'.$import[0]['prod_exp'].'<br />';
                   if (isset($import[2])) echo $import[2];

      echo '</font></td>
                </tr>
                </table>';
     }

     if (isset($import[1]) && $import[1][0]!='')
     {
      echo '<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="messageStackError"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                    ';

                   for ($i=0;$i<count($import[1]);$i++)
                   {
                    echo $import[1][$i].'<br />';
                   }


      echo '</font></td>
                </tr>
                </table>';
     }

  }

?>
<table width="100%"  border="0" cellspacing="5" cellpadding="0">
  <tr>
    <td class="pageHeading"><?php echo IMPORT; ?></td>
  </tr>
  <tr>
    <td class="dataTableHeadingContent"><?php echo TEXT_IMPORT; ?>
      <table width="100%"  border="0" cellspacing="2" cellpadding="0">
        <tr>
          <td width="7%"></td>
          <td width="93%" class="infoBoxHeading"><?php echo UPLOAD; ?></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
<?php
echo os_draw_form('upload',FILENAME_CSV_BACKEND,'action=upload','POST','enctype="multipart/form-data"');
echo os_draw_file_field('file_upload');
echo '<br/><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_UPLOAD . '"/>' . BUTTON_UPLOAD . '</button></span>';
?>
</form>
          </td>
        </tr>
        <tr>
          <td></td>
          <td class="infoBoxHeading"><?php echo SELECT; ?></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
          <?php
          $files=array();
          echo os_draw_form('import',FILENAME_CSV_BACKEND,'action=import','POST','enctype="multipart/form-data"');
             if ($dir= opendir(_IMPORT)){
             while  (($file = readdir($dir)) !==false) {
                if (is_file(_IMPORT.$file) && ($file !=".htaccess") && ($file !=".svn") && ($file !="index.html") && ($file !="index.htm"))
                {
                    $size=filesize(_IMPORT.$file);
                    $files[]=array(
                        'id' => $file,
                        'text' => $file.' | '.os_format_filesize($size));
                }
             }
             closedir($dir);
            }
          echo os_draw_pull_down_menu('select_file',$files,'');
          echo '<br/><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_IMPORT . '"/>' . BUTTON_IMPORT . '</button></span>';

          ?></form>
</td>
        </tr>
      </table>      <p>&nbsp; </p></td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="5" cellpadding="0">
  <tr>
    <td class="pageHeading"><?php echo EXPORT; ?></td>
  </tr>
  <tr>
    <td class="infoBoxHeading"><?php echo TEXT_EXPORT; ?>
      <table width="100%"  border="0" cellspacing="2" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
          <td>
<?php
echo os_draw_form('export',FILENAME_CSV_BACKEND,'action=export','POST','enctype="multipart/form-data"');
$content=array();
$content[]=array('id'=>'products','text'=>TEXT_PRODUCTS);
echo os_draw_pull_down_menu('select_content',$content,'products');
echo '<br/><span class="button"><button type="submit" onClick="this.blur();" value="' . BUTTON_EXPORT . '"/>' . BUTTON_EXPORT . '</button></span>';
?>
</form>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>

</td>
        </tr>
      </table>      <p>&nbsp; </p></td>
  </tr>
</table>

</td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>