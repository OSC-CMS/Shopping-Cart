<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/
/*
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(2003/06/02); www.oscommerce.com 
  (c) 2003	 nextcommerce (2003/08/18); www.nextcommerce.org
  (c) 2004	 xt:Commerce (2003/08/18); xt-commerce.com
  (c) 2008	 VamShop (2008/01/01); vamshop.com
*/
  
defined( '_VALID_OS' ) or die( 'CartET error' );

  class upload {
    var $file, $filename, $destination, $permissions, $extensions, $tmp_filename;

    function upload($file = '', $destination = '', $permissions = '777', $extensions = array('gif', 'jpg', 'jpeg', 'png', 'swf', 'sql')) {

      $this->set_file($file);
      $this->set_destination($destination);
      $this->set_permissions($permissions);
      $this->set_extensions($extensions);

      if (os_not_null($this->file) && os_not_null($this->destination)) {
        if ( ($this->parse() == true) && ($this->save() == true) ) {
          return true;
        } else {
          return false;
        }
      }
    }

    function parse() {
      global $messageStack;
      if (isset($_FILES[$this->file])) {
        $file = array('name' => @$_FILES[$this->file]['name'],
                      'type' => @$_FILES[$this->file]['type'],
                      'size' => @$_FILES[$this->file]['size'],
                      'tmp_name' => @$_FILES[$this->file]['tmp_name']);
      } elseif (isset($_FILES[$this->file])) {

        $file = array('name' => @$_FILES[$this->file]['name'],
                      'type' => @$_FILES[$this->file]['type'],
                      'size' => @$_FILES[$this->file]['size'],
                      'tmp_name' => @$_FILES[$this->file]['tmp_name']);
      } else {
        $file = array('name' => @$GLOBALS[$this->file . '_name'],
                      'type' => @$GLOBALS[$this->file . '_type'],
                      'size' => @$GLOBALS[$this->file . '_size'],
                      'tmp_name' => @$GLOBALS[$this->file]);
      }

      if ( os_not_null($file['tmp_name']) && ($file['tmp_name'] != 'none') && is_uploaded_file($file['tmp_name']) ) {
        if (sizeof($this->extensions) > 0) {
          if (!in_array(strtolower(substr($file['name'], strrpos($file['name'], '.')+1)), $this->extensions)) {
            $messageStack->add_session(ERROR_FILETYPE_NOT_ALLOWED, 'error');

            return false;
          }
        }

        $this->set_file($file);
        $this->set_filename($file['name']);
        $this->set_tmp_filename($file['tmp_name']);

        return $this->check_destination();
      } else {

        if ($file['tmp_name']=='none') $messageStack->add_session(WARNING_NO_FILE_UPLOADED, 'warning');

        return false;
      }
    }

    function save() {
      global $messageStack;

      if (substr($this->destination, -1) != '/') $this->destination .= '/';

      if (!function_exists('imagecreatefromgif')) {

        if ($this->destination==dir_path('images_original')) {
            if (strstr(PRODUCT_IMAGE_THUMBNAIL_MERGE,'.gif') ||
                strstr(PRODUCT_IMAGE_INFO_MERGE,'.gif') ||
                strstr(PRODUCT_IMAGE_POPUP_MERGE,'.gif')) {

                $messageStack->add_session(ERROR_GIF_MERGE, 'error');
                return false;

            }
            if (strstr($this->filename,'.gif')) {
             $messageStack->add_session(ERROR_GIF_UPLOAD, 'error');
             return false;
            }

        }

      }

      if ( @move_uploaded_file($this->file['tmp_name'], $this->destination . $this->filename)) 
	  {
        @chmod($this->destination . $this->filename, $this->permissions);

        $messageStack->add_session(SUCCESS_FILE_SAVED_SUCCESSFULLY, 'success');

        return true;
      }
	  else 
	  {
        $messageStack->add_session(ERROR_FILE_NOT_SAVED, 'error');

        return false;
      }
    }

    function set_file($file) {
      $this->file = $file;
    }

    function set_destination($destination) {
      $this->destination = $destination;
    }

    function set_permissions($permissions) {
      $this->permissions = octdec($permissions);
    }

    function set_filename($filename) {
      $this->filename = $filename;
    }

    function set_tmp_filename($filename) {
      $this->tmp_filename = $filename;
    }

    function set_extensions($extensions) {
      if (os_not_null($extensions)) {
        if (is_array($extensions)) {
          $this->extensions = $extensions;
        } else {
          $this->extensions = array($extensions);
        }
      } else {
        $this->extensions = array();
      }
    }

    function check_destination() {
      global $messageStack;

      if (!is_writeable($this->destination)) {
        if (is_dir($this->destination)) {
          $messageStack->add_session(sprintf(ERROR_DESTINATION_NOT_WRITEABLE, $this->destination), 'error');
        } else {
          $messageStack->add_session(sprintf(ERROR_DESTINATION_DOES_NOT_EXIST, $this->destination), 'error');
        }

        return false;
      } else {
        return true;
      }
    }

  }
?>