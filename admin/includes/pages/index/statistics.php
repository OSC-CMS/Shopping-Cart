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

?>

<td class="dataTableContentRss" valign="top" width="50%">

<?php
include(get_path('class_admin') . 'ofc-library/open_flash_chart_object.php');
open_flash_chart_object( '100%', 250, os_href_link('chart_data.php', 'NONSSL'), false );
?>
</td>
<td class="dataTableContentRss" valign="top" width="50%">
<?php
open_flash_chart_object( '100%', 250, os_href_link('chart_data.php', 'report_type=orders', 'NONSSL'), false );
?>
</td>
