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

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

 $main->heading('plugin.gif', HEADING_TITLE_THEMES); 
?> 
		<table border="0" width="100%" cellspacing="0" cellpadding="0"  valign="top">
          <tr>
            <td valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
			    <td class="dataTableHeadingContent" width="20px">&nbsp;</td>
			    <td class="dataTableHeadingContent" width="20px">&nbsp;</td>
				<td class="dataTableHeadingContent" width="300px"><?php echo TABLE_HEADING_PLUGINS; ?></td>
				<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FILENAME; ?></td>
				<td class="dataTableHeadingContent" width="50px"><?php echo TABLE_HEADING_VERSION; ?></td>
				<td class="dataTableHeadingContent" width="50px"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right" width="50"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>	
<?			  
    $plugin_activ = ''; 

    for ($i = 0, $n = sizeof($plugins_file); $i < $n; $i++) 
    {
        $plugin_name = $plugins_file[$i][0];
		$_plug->icons($plugin_name); //получение иконки плагина

		require_once(_CATALOG.'themes/'.CURRENT_TEMPLATE.'/plugins/'.$plugins_file[$i][1]);
		
		$plugin_data = get_plugin_data(_CATALOG.'themes/'.CURRENT_TEMPLATE.'/plugins/'.$plugins_file[$i][1]);
      
	    $_plug->info[$plugin_name]['title'] = trim($plugin_data['Name'][1]);
	    $_plug->info[$plugin_name]['desc'] = trim($plugin_data['Description'][1]);
	    $_plug->info[$plugin_name]['version'] = trim($plugin_data['Version'][1]);
	    $_plug->info[$plugin_name]['author'] = trim($plugin_data['Author'][1]);
	    $_plug->info[$plugin_name]['author_uri'] = trim($plugin_data['AuthorURI'][1]);
		
		$color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
		
		if ((empty($_GET['module']) && $i==0) | ($_GET['module'] == $plugins_file[$i][0]))
		{
		      echo '<tr class="dataTableRowSelected">' . "\n";
              if (empty($_GET['module']))
              {
			      $_GET['module'] = $plugins_file[$i][0]; 
			  }			  
		}
		else
		{ 
		   //$plugin_data['Description'][1]
              echo '<tr onmouseover="this.style.background=\'#e9fff1\';" onmouseout="this.style.background=\''.$color.'\';"  style="background-color:'.$color;
		      echo ';" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'">' . "\n";
	    }  
?>
<td align="center" valign="center">&nbsp;</td>
<td <?php echo 'onclick="document.location.href=\'' . os_href_link(FILENAME_PLUGINS, 'module='.$plugin_name).'\'"'; ?> align="center" valign="center"><?php  echo '<img width="16px" height="16px" src="'. $_plug->info[$plugin_name]['icons'].'" border="0" />';?></td>
<td <?php echo 'onclick="document.location.href=\'' . os_href_link(FILENAME_PLUGINS, 'module='.$plugin_name).'\'"'; ?> class="dataTableContent"><?php  echo (!empty($_plug->info[$plugin_name]['title'])) ? ($_plug->info[$plugin_name]['title']) : (ucfirst($plugins_file[$i][0])) ;?></td>
<td <?php echo 'onclick="document.location.href=\'' . os_href_link(FILENAME_PLUGINS, 'module='.$plugin_name).'\'"'; ?> class="dataTableContent"><?php echo $_plug->desc($plugin_name); ?></td>
<td <?php echo 'onclick="document.location.href=\'' . os_href_link(FILENAME_PLUGINS, 'module='.$plugin_name).'\'"'; ?> class="dataTableContent" align="center"><?php echo $_plug->info[$plugin_name]['version']; ?></td>
<td class="dataTableContent" align="center"><?php $_plug->status($plugin_name, $plugins);?></td>
<td width="80px" class="dataTableContent" align="right" valign="center"><?php $_plug->action($plugin_name); ?></td> 
</tr>
<?php
 }
 
 echo '</table><br /><div style="width:100%;text-align:right;font-size:12px;"><i>'.'/themes/'.CURRENT_TEMPLATE.'/plugins/'.'</i>&nbsp;&nbsp;</div>';

?>

</td></tr></table>		
			