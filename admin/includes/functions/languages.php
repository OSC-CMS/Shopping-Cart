<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.1
#####################################
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

function os_get_languages_directory($code) 
{
    $language_query = os_db_query("select languages_id, directory from " . TABLE_LANGUAGES . " where code = '" . $code . "'");
    if (os_db_num_rows($language_query)) 
	{
       $lang = os_db_fetch_array($language_query);
       $_SESSION['languages_id'] = $lang['languages_id'];
       return $lang['directory'];
    } 
	else 
	{
       return false;
    }
}
?>