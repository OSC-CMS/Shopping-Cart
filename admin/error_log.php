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
  
  if(isset($_GET['action']) && $_GET['action']=='clean_log')  
  {
      $fp = @fopen(_TMP.'osc_db_error.log', "w");
      @fclose($fp);
	  
      if (filesize(_TMP.'osc_db_error.log')==0)
	  {
          $messageStack->add('Лог-файл успешно очищен', 'success');
	  } 
  }
  
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
    
    <?php $main->heading('calculator_add.png', HEADING_TITLE); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
	
	  <?php 
	  
	    if (is_file(_TMP.'osc_db_error.log'))
		{
		     $fp = @fopen(_TMP.'osc_db_error.log', "rb");
             if ($fp) 
	         {
                   while (!feof($fp)) 
	               {
                       $st .= fread($fp, 4096);
                   }
             }
			 //$st = explode("|", $st);
			 echo '<p><textarea class="round" style="width:100%; height:400px" cols="60" name="text">'.$st.'</textarea></p';
             @fclose($fp);
		}
		else
		{
		   echo NO_ERRORS;
		}
	  ?>
	
	  </tr>
        </table>
		<div style="width:100%;text-align:right;"><a class="button" href="<?php echo os_href_link(FILENAME_ERROR_LOG, 'action=clean_log'); ?>"><span>Очистить</span></a></div>
		</td>
      </tr>
    </table>
	
<?php $main->bottom(); ?>