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

include ('includes/top.php');

$breadcrumb->add(NAVBAR_TITLE_RSS2_INFO);

require (dir_path('includes').'header.php');

$osTemplate->assign('RSS2_INFO', TEXT_RSS2_INFO);

$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->caching = 0;
$main_content = $osTemplate->fetch(CURRENT_TEMPLATE.'/module/rss2_info.html');
$osTemplate->assign('main_content', $main_content);
 $osTemplate->load_filter('output', 'trimhitespace');
$template = (file_exists(_THEMES_C.FILENAME_RSS2_INFO.'.html') ? CURRENT_TEMPLATE.'/'.FILENAME_RSS2_INFO.'.html' : CURRENT_TEMPLATE.'/index.html');
$osTemplate->display($template);

include ('includes/bottom.php');
?>