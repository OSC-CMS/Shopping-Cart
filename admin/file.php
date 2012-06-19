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
 
  $ext = array('zip', 'rar', 'gz', 'bz2', 'csv', 'tar', 'sql');
  
  if (isset($_GET['path']) && !empty($_GET['path']))
  {
     $_path = os_check_path($_GET['path']);
	 
	 if (is_file(DIR_FS_ADMIN.$_path))
	 {		 
	     $_name = explode('/', $_path);
		 
		 if (count($_name)>0) $_name = $_name[count($_name)-1]; $_name = $_path;
		 
		 if (empty($_name)) die('no file!');
		 if (filesize(DIR_FS_ADMIN.$_path)==0) die('no file!');
		 
		 $_expansion = explode('.', $_path);
		 $_expansion = $_expansion[count($_expansion)-1];
		 
		 if (!in_array($_expansion, $ext)) die('Forbidden file format!');
		 
		 header("Expires: Mon, 30 Nov 2009 00:00:00 GMT");
         header("Last-Modified: ".gmdate("D,d M Y H:i:s")." GMT");
         header("Cache-Control: no-cache, must-revalidate");
         header("Pragma: no-cache");
         header("Content-Type: Application/octet-stream");
         header("Content-Length: ".filesize(DIR_FS_ADMIN.$_path));
         header("Content-disposition: attachment; filename=\"".$_name."\"");
		 
	     $_content = file_get_contents(DIR_FS_ADMIN.$_path);
		 
		 echo $_content;
	 }
	 else die('no file!');
	 
  }
  else die('no file!');
  
  
?>