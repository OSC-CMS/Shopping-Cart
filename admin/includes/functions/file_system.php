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

class FileFilter_I {


     function accept($pathname){
         return true;
     }
     

     function FileFilter_I(){
     }

}

class FileFilter extends FileFilter_I{

    var $included_extensions;
    var $excluded_extensions;
    var $excluded_files;


    function FileFilter($included_extensions = NULL, 
                        $excluded_extensions = NULL,
                        $excluded_files = NULL){
        if (!is_array($included_extensions)){
            $included_extensions = NULL;
        }
        if (!is_array($excluded_extensions)){
            $excluded_extensions = NULL;
        }
        if (!is_array($excluded_files)){
            $excluded_files = NULL;
        }
        $this->included_extensions = $included_extensions;
        $this->excluded_extensions = $excluded_extensions;
        $this->excluded_files = $excluded_files;
       
    }


    function accept ($pathName){
        $fileName = basename($pathName);
        return $this->isExtensionSupported($fileName) && !$this->fileInExcludedFileList ($fileName);
    }

    function isExtensionSupported ($fileName){
        $file_extension = substr($fileName, strrpos($fileName, '.')); 
        $included = true;
        if ($this->included_extensions != NULL){
            $included = in_array($file_extension, $this->included_extensions);
        }

        $excluded = false;
        if ($this->excluded_extensions != NULL){
            $excluded = in_array($file_extension, $this->excluded_extensions);
        }
        
        return $included && !$excluded;
    }


    function fileInExcludedFileList ($fileName){
        if ($this->excluded_files == NULL){
            return false;
        }
        return in_array($fileName, $this->excluded_files);
    }

}


function os_get_filelist ($startdir, $includedExt = array (), $excludedFilenames = array()){
    return os_get_filelist_func ($startdir, new FileFilter($includedExt, null, $excludedFilenames));
}


function os_get_image_files ($startdir, $includedExt = array ('.jpg','.jpeg','.png','.gif')){
    return os_get_filelist_func ($startdir, new FileFilter($includedExt));
}



function os_get_filelist_func ($startdir,
                           $file_filter = NULL,
                           $dir_only = false, $subdir = '') {

    if ($file_filter == null){
        $file_filter = new FileFilter_I();
    }

    $dirname = $startdir . $subdir;
    if ($dir = opendir($dirname)) {
        while ($file = readdir($dir)) {
            if (substr($file, 0, 1) != '.') {
                if (!$dir_only && is_file($dirname . $file)) {
                    if ($file_filter->accept($file)){
                        $files[] = array (
                            'id' => $subdir . $file,
                            'text' => $subdir . $file
                        );
                    }
                } elseif (is_dir($dirname . $file)) {
                    if ($dir_only) {
                        $files[] = array (
                            'id' => $subdir . $file . '/',
                            'text' => $subdir . $file . '/'
                        );
                    }
                    $files = os_array_merge($files, os_get_filelist_func ($startdir, $file_filter, $dir_only, $subdir . $file . '/'));
                }
            }
        }
        closedir($dir);
    }
    return ($files);
}

?>
