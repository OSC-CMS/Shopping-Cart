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

  require_once('includes/top.php');
  
  if ( isset($_GET['cache']) && $_GET['cache']=='set_all_cache_block' )
  {
     set_all_cache();
	 $dir = _CACHE;

          if ($d = opendir($dir)) {

          $i=0;
          while (false !== ($file = readdir($d))) 
		  {
          if ($file != "." && $file != ".." && $file !=".htaccess" && $file !=".svn" && $file !="system") 
		  {
                 os_delete_file($dir . $file);
          }
          }

           closedir($d);
		   
         }
		 
		 $dir = _CACHE;
   
     os_spaceUsed($dir);
     echo os_format_filesize($total);
	 die();
  }
  
  if (isset($_GET['cache']))
  {
   switch ($_GET['cache'])
   {
      case 'set_all_cache':
	     set_all_cache();
		

	      $dir = _CACHE;

          if ($d = opendir($dir)) {

          $i=0;
          while (false !== ($file = readdir($d))) 
		  {
          if ($file != "." && $file != ".." && $file !=".htaccess" && $file !=".svn" && $file !="system") 
		  {
                 os_delete_file($dir . $file);
          }
          }

           closedir($d);
		   
         }
		 
		  $messageStack->add(CACHE_CLEAN_OK, 'ok');
	  break;
   }
}

  if (!is_dir(_CACHE)) $messageStack->add(ERROR_CACHE_DIRECTORY_DOES_NOT_EXIST, 'error');
  if (!is_writeable(_CACHE)) $messageStack->add(ERROR_CACHE_DIRECTORY_NOT_WRITEABLE, 'error');

  $dir = _CACHE;
   
   os_spaceUsed($dir);

   $main->head();
?>

<?php $main->top_menu();?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    <?php os_header('cache.png',BOX_CONFIGURATION." / ".HEADING_TITLE); ?> 
	<?php $main->fly_menu (os_href_link(FILENAME_CONFIGURATION.'?gID=11', '', 'NONSSL'), TEXT_SETTING, '');?>
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td>

<?php
echo USED_SPACE.' '.os_format_filesize($total);

 /*if ($fp = opendir(_CACHE.'system/')) 
			   {
		            while ($file = readdir($fp)) 
					{
			            if (is_file(_CACHE.'system/'.$file) && $file != '.htaccess' && $file != 'index.php') 
						{
						    $color = $color == '#f0f1ff' ? '#f9f9ff':'#f0f1ff';
							$size = filesize(_CACHE.'system/'.$file);
  
                            if ($size == 1) $size = 0;
							$size = os_format_filesize($size);
				            // $size = filesize($dir.$file);
							echo '<tr bgcolor="'.$color.'">';
				            echo '<td width="33%">'.$file.'</td>';
				            echo '<td width="33%" align="center">'.$size.'</td>';
							$file = str_replace('.php', '', $file);
				            echo '<td width="33%" align="center"><a href="'.FILENAME_CACHE.'?cache='.$file.'">'.CACHE_UP.'</a></td>';
							echo '</tr>';
			            } 
		            } 
		            closedir($fp);
	           }*/
			   
			   os_spaceUsed(_CACHE);
			   
			 
?>
<br /><br />
			<span style="left:50px;"><a class="button" href="<?php echo FILENAME_CACHE; ?>?cache=set_all_cache"><span><?php echo CACHE_CLEAN_ALL; ?></span></a></span>
				

            </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php $main->bottom(); ?>