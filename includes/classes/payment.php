<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class payment 
{
    var $modules, $selected_module;

    function payment($module = '') 
	{
      global $PHP_SELF,$order;

      if (defined('MODULE_PAYMENT_INSTALLED') && os_not_null(MODULE_PAYMENT_INSTALLED)) {

        require_once(get_path('func') . 'ship2pay.php');
        $this->modules = ship2pay();
			
        $include_modules = array();

        if ( (os_not_null($module)) && (in_array($module . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $this->modules)) ) {
          $this->selected_module = $module;

          $include_modules[] = array('class' => $module, 'file' => $module . '.php');
        } else {
          reset($this->modules);
          while (list(, $value) = each($this->modules)) {
            $class = substr($value, 0, strrpos($value, '.'));
            $include_modules[] = array('class' => $class, 'file' => $value);
          }
        }
	$unallowed_modules = explode(',', $_SESSION['customers_status']['customers_status_payment_unallowed'].','.$order->customer['payment_unallowed']);
    if ($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight')) {
     $unallowed_modules = array_merge($unallowed_modules,explode(',',DOWNLOAD_UNALLOWED_PAYMENT));
    }

        for ($i = 0, $n = sizeof($include_modules); $i < $n; $i++) {
          if (!in_array($include_modules[$i]['class'], $unallowed_modules)) {

            if (@constant(MODULE_PAYMENT_ . strtoupper(str_replace('.php', '', $include_modules[$i]['file'])) . _ALLOWED) != '') {
              $unallowed_zones = explode(',', constant(MODULE_PAYMENT_ . strtoupper(str_replace('.php', '', $include_modules[$i]['file'])) . _ALLOWED));
            } else {
              $unallowed_zones = array();
            }
            if (in_array($_SESSION['delivery_zone'], $unallowed_zones) == true || count($unallowed_zones) == 0) {
              if ($include_modules[$i]['file']!='' && $include_modules[$i]['file']!='no_payment') 
			  {

			     $_payment_lang = DIR_FS_DOCUMENT_ROOT.'/modules/payment/' . substr($include_modules[$i]['file'], 0, strrpos($include_modules[$i]['file'], '.')) . '/';
				 
                 if (is_file($_payment_lang.$_SESSION['language'].'.php'))
				 {
			          include ($_payment_lang. '/'.$_SESSION['language'].'.php');
				 }
				 elseif (is_file($_payment_lang. '/ru.php'))
				 {
				      include ($_payment_lang. '/ru.php');
				 }
				 
				 if (is_file(DIR_FS_DOCUMENT_ROOT.'/modules/payment/' . substr($include_modules[$i]['file'], 0, strrpos($include_modules[$i]['file'], '.')) . '/'.$include_modules[$i]['file']))
				 {
			          include (DIR_FS_DOCUMENT_ROOT.'/modules/payment/' . substr($include_modules[$i]['file'], 0, strrpos($include_modules[$i]['file'], '.')) . '/'.$include_modules[$i]['file']);
			     }
			   
              }
              $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
            }
          }
        }
 
        if ( (os_count_payment_modules() == 1) && (!is_object($_SESSION['payment'])) ) {
          $_SESSION['payment'] = $include_modules[0]['class'];
        }

        if ( (os_not_null($module)) && (in_array($module, $this->modules)) && (isset($GLOBALS[$module]->form_action_url)) ) {
          $this->form_action_url = $GLOBALS[$module]->form_action_url;
        }
      }
    }
 
    function update_status() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module])) {
          if (function_exists('method_exists')) {
            if (method_exists($GLOBALS[$this->selected_module], 'update_status')) {
              $GLOBALS[$this->selected_module]->update_status();
            }
          } else {
            //@ call_user_method('update_status', $GLOBALS[$this->selected_module]);
            @ call_user_func('update_status', $GLOBALS[$this->selected_module]);
          }
        }
      }
    }

    function javascript_validation() {
      $js = '';
      if (is_array($this->modules)) {
        $js = '<script type="text/javascript"><!-- ' . "\n" .
              'function check_form() {' . "\n" .
              '  var error = 0;' . "\n" .
              '  var error_message = unescape("' . os_js_lang(JS_ERROR) . '");' . "\n" .
              '  var payment_value = null;' . "\n" .
              '  if (document.getElementById("checkout_payment").payment.length) {' . "\n" .
              '    for (var i=0; i<document.getElementById("checkout_payment").payment.length; i++) {' . "\n" .
              '      if (document.getElementById("checkout_payment").payment[i].checked) {' . "\n" .
              '        payment_value = document.getElementById("checkout_payment").payment[i].value;' . "\n" .
              '      }' . "\n" .
              '    }' . "\n" .
              '  } else if (document.getElementById("checkout_payment").payment.checked) {' . "\n" .
              '    payment_value = document.getElementById("checkout_payment").payment.value;' . "\n" .
              '  } else if (document.getElementById("checkout_payment").payment.value) {' . "\n" .
              '    payment_value = document.getElementById("checkout_payment").payment.value;' . "\n" .
              '  }' . "\n\n";

        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $js .= $GLOBALS[$class]->javascript_validation();
          }
        }
        if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
        $js .= "\n" . '  if (!document.getElementById("checkout_payment").conditions.checked) {' . "\n" .
               '    error_message = error_message + unescape("' . os_js_lang(ERROR_CONDITIONS_NOT_ACCEPTED) . '");' . "\n" .
               '    error = 1;' . "\n" .
               '  }' . "\n\n";
        }
        $js .= "\n" . '  if (payment_value == null) {' . "\n" .
               '    error_message = error_message + unescape("' . os_js_lang(JS_ERROR_NO_PAYMENT_MODULE_SELECTED) . '");' . "\n" .
               '    error = 1;' . "\n" .
               '  }' . "\n\n" .
               '  if (error == 1 && submitter != 1) {' . "\n" . 
               '    alert(error_message);' . "\n" .
               '    return false;' . "\n" .
               '  } else {' . "\n" .
               '    return true;' . "\n" .
               '  }' . "\n" .
               '}' . "\n" .
               '//--></script>' . "\n";
      }

      return $js;
    }

    function selection() {
      $selection_array = array();

      if (is_array($this->modules)) {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $selection = $GLOBALS[$class]->selection();
            if (is_array($selection)) $selection_array[] = $selection;
          }
        }
      }

      return $selection_array;
    }

    function check_credit_covers() {
       global $credit_covers;

    return $credit_covers;
            }

    function pre_confirmation_check() {
    global $credit_covers, $payment_modules;
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {

                  if ($credit_covers) {
                    $GLOBALS[$this->selected_module]->enabled = false;
                    $GLOBALS[$this->selected_module] = NULL;
                    $payment_modules = '';
                  } else {
                    $GLOBALS[$this->selected_module]->pre_confirmation_check();
                  }
            }
      }
    }

    function confirmation() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->confirmation();
        }
      }
    }

    function process_button() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->process_button();
        }
      }
    }

    function before_process() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->before_process();
        }
      }
    }
    
    function payment_action() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->payment_action();
        }
      }
    }

    function after_process() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->after_process();
        }
      }
    }

    function get_error() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->get_error();
        }
      }
    }
  }
?>