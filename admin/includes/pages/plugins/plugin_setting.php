<?php

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );
?>
	<table border="0" width="100%" cellspacing="0" cellpadding="2" valign="top">
      <tr>
        <td  valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0"  valign="top">
          <tr>
            <td valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
			    <td class="dataTableHeadingContent" width="20px">Имя</td>
				<td class="dataTableHeadingContent" width="300px">URL</td>
				<td class="dataTableHeadingContent" width="300px">description</td>
				<td class="dataTableHeadingContent">target</td>
				<td class="dataTableHeadingContent" width="50px">link_status</td>
				<td class="dataTableHeadingContent" width="50px">link_sort_order</td>
                <td class="dataTableHeadingContent" align="right" width="50"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>		  
<?php
    
	  if (!empty($_link_query))
	  {
		  foreach ($_link_query as $_link_id => $_link_value)
		  {    	
		      $color = $color == '#f9f9ff' ? '#f0f1ff':'#f9f9ff';
              
			  echo '<tr onmouseover="this.style.background=\'#e9fff1\';" onmouseout="this.style.background=\''.$color.'\';"  style="background-color:'.$color;
		      echo ';" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'">' . "\n"; 

              echo '<td  align="center" valign="center">'.$_link_value['link_name'].'</td>';
              echo '<td  align="center" valign="center">'.$_link_value['link_url'].'</td>';
              echo '<td  align="center" valign="center">'.$_link_value['link_description'].'</td>';
              echo '<td  align="center" valign="center">'.$_link_value['link_target'].'</td>';
              echo '<td  align="center" valign="center">'.$_link_value['link_status'].'</td>';
              echo '<td  align="center" valign="center">'.$_link_value['link_sort_order'].'</td>';
              echo '<td width="80px" class="dataTableContent" align="right" valign="center">&nbsp;';
			  if ($_GET['module'] == $plugin_name)
		      {
			        echo '<img src="'._HTTP.'admin/themes/'.ADMIN_TEMPLATE.'/images/icon_arrow_right.gif" border="0" title="info">';	
		      }
	          else
              {		
		            echo '<a href="' . os_href_link(FILENAME_PLUGINS, 'module=' . $plugin_name) . '">' .'<img src="'._HTTP.'admin/themes/'.ADMIN_TEMPLATE.'/images/icon_info.gif" border="0" title="info">' . '</a>';	
		      }
		      echo '</td>';
              echo '</tr>';
			
		 }
      }

?>


<?php
//блоки из текущего шаблона
echo '</table><br />';

?>
	  

            </table>
			
			
		
			</td>
    <td class="right_box" valign="top">
<table class="contentTable" border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr class="infoBoxHeading">
    <td  class="infoBoxHeading"><b>Нет ссылок</b></td>
  </tr>
</table>
<table class="contentTable" border="0" width="100%" cellspacing="0" cellpadding="2"> 
<tr class="infoBoxContent">
<td>

<table>  
  <tr><td>Добавьте faq</td></tr>
  <tr><td><form action="<?php echo FILENAME_PLUGINS; ?>?page_admin=links_page_admin&action=link_save" method="post"><b>URL</b></td></tr>
  <tr><td><input name="admin_page[link_id]" type="hidden" value="1"></td></tr>  
    <tr><td><input name="admin_page[link_url]" value="http://"></td></tr>  
  
  <tr><td><form action=""><b>Имя</b></td></tr>
  <tr><td><input name="admin_page[link_name]" value=""></td></tr>  
  
  <tr><td><form action=""><b>link_target</b></td></tr>
  <tr><td><input name="admin_page[link_target]" value=""></td></tr>
   
  <tr><td><form action=""><b>link_description</b></td></tr>
  <tr><td><input name="admin_page[link_description]" value=""></td></tr>  
  
  <tr><td><form action=""><b>link_status</b></td></tr>
  <tr><td><input name="admin_page[link_status]" value=""></td></tr>
  
  <tr><td><form action=""><b>link_sort_order</b></td></tr>
  <tr><td><input name="admin_page[link_sort_order]" value=""></td></tr>
  
  <tr>
    <td>	
	
	<?php
	     _e('<tr><td align="center"><span class="button"><button type="submit" value="'.PLUGINS_ADD.'">'.PLUGINS_ADD.'</span></td></tr>');
	?>
	
	<td></tr>
	<tr><td></form></td></tr>
	
</table></td></tr>	
	</table>
	</td>
  </tr>
</table>
            </td>
          </tr>
        </table></td>
      </tr>
    </table>