<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

class navigationHistory {
    var $path, $snapshot;

    function navigationHistory() {
      $this->reset();
    }

    function reset() {
      $this->path = array();
      $this->snapshot = array();
    }

    function add_current_page() {
      global $PHP_SELF, $cPath;

      $set = 'true';
      for ($i=0, $n=sizeof($this->path); $i<$n; $i++) {
        if ( ($this->path[$i]['page'] == basename($PHP_SELF)) ) {
          if (isset($cPath)) {
            if (!isset($this->path[$i]['get']['cPath'])) {
              continue;
            } else {
              if ($this->path[$i]['get']['cPath'] == $cPath) {
                array_splice($this->path, ($i+1));
                $set = 'false';
                break;
              } else {
                $old_cPath = explode('_', $this->path[$i]['get']['cPath']);
                $new_cPath = explode('_', $cPath);

                for ($j=0, $n2=sizeof($old_cPath); $j<$n2; $j++) {
                  if ($old_cPath[$j] != $new_cPath[$j]) {
                    array_splice($this->path, ($i));
                    $set = 'true';
                    break 2;
                  }
                }
              }
            }
          } else {
            array_splice($this->path, ($i));
            $set = 'true';
            break;
          }
        }
      }

      if ($set == 'true') {
        $this->path[] = array('page' => basename($PHP_SELF),
                              'mode' => (($_SERVER['HTTPS'] == 'on') ? 'SSL' : 'NONSSL'),
                              'get' => $_GET,
                              'post' => $_POST);
      }
    }

    function remove_current_page() {
      global $PHP_SELF;

      $last_entry_position = sizeof($this->path) - 1;
      if ($this->path[$last_entry_position]['page'] == basename($PHP_SELF)) {
        unset($this->path[$last_entry_position]);
      }
    }

    function set_snapshot($page = '') {
      global $PHP_SELF;

      if (is_array($page)) {
        $this->snapshot = array('page' => $page['page'],
                                'mode' => $page['mode'],
                                'get' => $page['get'],
                                'post' => $page['post']);
      } else {
        $this->snapshot = array('page' => basename($PHP_SELF),
                                'mode' => (($_SERVER['HTTPS'] == 'on') ? 'SSL' : 'NONSSL'),
                                'get' => $_GET,
                                'post' => $_POST);
      }
    }

    function clear_snapshot() {
      $this->snapshot = array();
    }

    function set_path_as_snapshot($history = 0) {
      $pos = (sizeof($this->path)-1-$history);
      $this->snapshot = array('page' => $this->path[$pos]['page'],
                              'mode' => $this->path[$pos]['mode'],
                              'get' => $this->path[$pos]['get'],
                              'post' => $this->path[$pos]['post']);
    }

    function debug() {
      for ($i=0, $n=sizeof($this->path); $i<$n; $i++) {
        echo $this->path[$i]['page'] . '?';
        while (list($key, $value) = each($this->path[$i]['get'])) {
          echo $key . '=' . $value . '&';
        }
        if (sizeof($this->path[$i]['post']) > 0) {
          echo '<br />';
          while (list($key, $value) = each($this->path[$i]['post'])) {
            echo '&nbsp;&nbsp;<b>' . $key . '=' . $value . '</b><br />';
          }
        }
        echo '<br />';
      }

      if (sizeof($this->snapshot) > 0) {
        echo '<br /><br />';

        echo $this->snapshot['mode'] . ' ' . $this->snapshot['page'] . '?' . os_array_to_string($this->snapshot['get'], array(os_session_name())) . '<br />';
      }
    }

    function unserialize($broken) {
      for(reset($broken);$kv=each($broken);) {
        $key=$kv['key'];
        if (gettype($this->$key)!="user function")
        $this->$key=$kv['value'];
      }
    }
  }
?>