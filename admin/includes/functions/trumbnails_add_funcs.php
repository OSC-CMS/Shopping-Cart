<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

function os_mkdir_recursive($basedir, $subdir) 
{
		global $messageStack;
		if(!is_dir($basedir . $subdir)) {
			$mkdir_array = explode('/', $subdir);
			$mkdir = $basedir;
			for($i=0, $n=sizeof($mkdir_array); $i<$n; $i++) {
				$mkdir .= $mkdir_array[$i].'/';
				if(!is_dir($mkdir)) {
					if(!mkdir($mkdir)) {
						$messageStack->add(ERROR_IMAGE_DIRECTORY_CREATE . $mkdir, 'error');
						return false;
					} else {
						$messageStack->add(TEXT_IMAGE_DIRECTORY_CREATE . $mkdir, 'success');
					}
				}
			}
		}
}

  function os_get_image_size($src, $width, $height) {
      if ( (CONFIG_CALCULATE_IMAGE_SIZE == 'true')  ) {
         if ($image_size = @getimagesize($src)) {
             if (os_not_null($width) && os_not_null($height)) {
            $ratio = $width / $height;
            $src_ratio = $image_size[0] / $image_size[1];
              if ($ratio < $src_ratio) {
                $height = $width / $src_ratio;
             }
             else {
                $width = $height * $src_ratio;
             }
            }  elseif (!os_not_null($width) && os_not_null($height)) {
               $ratio = $height / $image_size[1];
               $width = $image_size[0] * $ratio;
            } elseif (os_not_null($width) && !os_not_null($height)) {
               $ratio = $width / $image_size[0];
               $height = $image_size[1] * $ratio;
            } elseif (!os_not_null($width) && !os_not_null($height) or $width > $image_size[0] or $height > $image_size[1]) {
               $width = $image_size[0];
               $height = $image_size[1];
            }
         }
      }
      return(array((int)$width, (int)$height));
   }

	function os_get_files_in_dir($startdir, $ext=array('.jpg', '.jpeg', '.png', '.gif', '.JPG', '.bmp'), $dir_only=false, $subdir = '') {
//		echo 'Directory: ' . $startdir . '  Subirectory: ' . $subdir . '<br />';
		if(!is_array($ext)) $ext=array();
		$dirname = $startdir . $subdir;
		if ($dir= opendir($dirname)){
			while ($file = readdir($dir)) {
				if(substr($file, 0, 1) != '.') {
					if (is_file($dirname.$file) && !$dir_only) {
						if (in_array(substr($file, strrpos($file, '.')), $ext)) {
//							echo '&nbsp;&nbsp;File: ' . $subdir.$file . '<br />';
							$files[]=array('id' => $subdir.$file,
														 'text' => $subdir.$file);
						}
					} elseif (is_dir($dirname.$file)) {
						if($dir_only) {
							$files[]=array('id' => $subdir.$file.'/',
														 'text' => $subdir.$file.'/');
						}
						$files = os_array_merge($files, os_get_files_in_dir($startdir, $ext, $dir_only, $subdir.$file.'/'));
					}
				}
			}
			closedir($dir);
		}
		return(@$files);
	}
?>