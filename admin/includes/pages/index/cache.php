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

defined('_VALID_OS') or die('Прямой доступ  не допускается.');
?>
<table border="0" width="100%">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
				  <tr> 
				    <td colspan="3" class="pageHeading" width="100%">
	<?php $main->heading('campaigns.png', TABLE_HEADING_CACHE); ?> 			
				    </td>
				  </tr>

              <tr>
                <td class="dataTableContentRss" valign="top">
<script type="text/javascript">
$(document).ready(function(){

//global vars
	var cache_size = $("#cache_size");

	//functions
	function CleanCache(){
		//send the post to shoutbox.php
		$.ajax({
			type: "POST", url: "cache.php?cache=set_all_cache_block", data: "",
			complete: function(data)
			{
				cache_size.html(data.responseText);
			}
		});
	}
	
	$('#clean_cache').click(function() 
	{
       CleanCache();
    });
});
</script>
<div id="cache">
<?php
   os_spaceUsed(_CACHE);
?>
			 <div id="total_cache"> <b><?php echo TABLE_CACHE_SIZE;?></b> <span id="cache_size"><?php echo os_format_filesize($total); ?></span></div>
			 <br /><a class="button" id="clean_cache" href="#"><span><?php echo TABLE_CACHE_CLEAN; ?></span></a>
</div>
			 
     
                </td>
              </tr>

                </table></td>
              </tr></table>