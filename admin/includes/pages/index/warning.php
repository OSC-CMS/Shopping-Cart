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

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');
?>
<table border="0" width="99%">
         <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
				  <tr> 
				    <td colspan="3" class="pageHeading" width="100%">
				   <?php $main->heading('bug.png', MENU_SYSTEM_ERRORS); ?> 					
				    </td>
				  </tr>

              <tr>
                <td class="dataTableContentRss" valign="top">
<?php

	if ($file_warning != '') {
		echo TEXT_FILE_WARNING;
		echo '<font color="red">'.$file_warning.'</font><br>';
	}

	if ($folder_warning != '') {
		echo TEXT_FOLDER_WARNING;
		echo '<b>'.$folder_warning.'</b>';
	}
	
	if ( ($installed_payment == '') or ($installed_shipping == '')){
	       echo MENU_PRED.'<br>';
		
    	   if ($installed_payment == '') {
	        	echo '<a href="modules.php?set=payment" target="_blank" style="padding-left:10px;">'.TEXT_PAYMENT_ERROR.'</a><br>';
	       }

	       if ($installed_shipping == '') {
	            echo '<a href="modules.php?set=shipping" target="_blank"  style="padding-left:10px;">'.TEXT_SHIPPING_ERROR.'</a><br>';
           }	

     }
	 
	if (is_dir(_CATALOG.'install'))
    {
       echo "<br />".TEXT_INSTALL_ERROR;
    }


?>


				  
              <br><br>  
                </td>
              </tr>

                </table></td>
              </tr>
</table>