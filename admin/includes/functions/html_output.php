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

  function os_href_link($page = '', $parameters = '', $connection = 'NONSSL') 
  {
    if ($page == '') {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine the page link!<br><br>Function used:<br><br>os_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }
    if ($connection == 'NONSSL') {
      $link = HTTP_SERVER . DIR_WS_ADMIN;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL == 'true') {
        $link = HTTPS_SERVER . DIR_WS_ADMIN;
      } else {
        $link = HTTP_SERVER . DIR_WS_ADMIN;
      }
    } else {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>os_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }
    if ($parameters == '') {
      $link = $link . $page . '?' . @SID;
    } else {
      $link = $link . $page . '?' . $parameters . '&' . @SID;
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

    return $link;
  }

  function os_catalog_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
    if ($connection == 'NONSSL') {
      $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL_CATALOG == 'true') {
        $link = HTTPS_CATALOG_SERVER . DIR_WS_CATALOG;
      } else {
        $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
      }
    } else {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>os_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }
    if ($parameters == '') {
      $link .= $page;
    } else {
      $link .= $page . '?' . $parameters;
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

    return $link;
  }


  function os_image($src, $alt = '', $width = '', $height = '', $params = '') {
    $image = '<img src="' . $src . '" border="0" alt="' . $alt . '"';
    if ($alt) {
      $image .= ' title=" ' . $alt . ' "';
    }
    if ($width) {
      $image .= ' width="' . $width . '"';
    }
    if ($height) {
      $image .= ' height="' . $height . '"';
    }
    if ($params) {
      $image .= ' ' . $params;
    }
    $image .= '>';

    return $image;
  }


  function os_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') 
  {
    return os_image(http_path('images_admin') . $image, '', $width, $height);
  }

  function os_js_zone_list($country, $form, $field) {
    $countries_query = os_db_query("select distinct zone_country_id from " . TABLE_ZONES . " order by zone_country_id");
    $num_country = 1;
    $output_string = '';
    while ($countries = os_db_fetch_array($countries_query)) {
      if ($num_country == 1) {
        $output_string .= '  if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      } else {
        $output_string .= '  } else if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      }

      $states_query = os_db_query("select zone_name, zone_id from " . TABLE_ZONES . " where zone_country_id = '" . $countries['zone_country_id'] . "' order by zone_name");

      $num_state = 1;
      while ($states = os_db_fetch_array($states_query)) {
        if ($num_state == '1') $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . PLEASE_SELECT . '", "");' . "\n";
        $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $states['zone_name'] . '", "' . $states['zone_id'] . '");' . "\n";
        $num_state++;
      }
      $num_country++;
    }
    $output_string .= '  } else {' . "\n" .
                      '    ' . $form . '.' . $field . '.options[0] = new Option("' . TYPE_BELOW . '", "");' . "\n" .
                      '  }' . "\n";

    return $output_string;
  }


  function os_draw_form($name, $action, $parameters = '', $method = 'post', $params = '') {
    $form = '<form name="' . $name . '" action="';
    if ($parameters) {
      $form .= os_href_link($action, $parameters);
    } else {
      $form .= os_href_link($action);
    }
    $form .= '" method="' . $method . '"';
    if ($params) {
      $form .= ' ' . $params;
    }
    $form .= '>';

    return $form;
  }


  function os_draw_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) 
  {
    $field = '<input type="' . $type . '" name="' . $name . '"';
    if (isset($GLOBALS[$name]) && ($GLOBALS[$name]) && ($reinsert_value) ) 
	{
      $field .= ' value="' . htmlspecialchars(trim($GLOBALS[$name])) . '"';
    } 
	elseif ($value != '') 
	{
      $field .= ' value="' . htmlspecialchars(trim($value)) . '"';
    }
	
    if ($parameters != '') {
      $field .= ' ' . $parameters;
    }
    $field .= '>';

    if ($required) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

  function os_draw_small_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . $type . '" size="3" name="' . $name . '"';
    if ( ($GLOBALS[$name]) && ($reinsert_value) ) {
      $field .= ' value="' . htmlspecialchars(trim($GLOBALS[$name])) . '"';
    } elseif ($value != '') {
      $field .= ' value="' . htmlspecialchars(trim($value)) . '"';
    }
    if ($parameters != '') {
      $field .= ' ' . $parameters;
    }
    $field .= '>';

    if ($required) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }


  function os_draw_password_field($name, $value = '', $required = false) {
    $field = os_draw_input_field($name, $value, 'maxlength="40"', $required, 'password', false);

    return $field;
  }


  function os_draw_file_field($name, $required = false) {
    $field = os_draw_input_field($name, '', '', $required, 'file');

    return $field;
  }


  function os_draw_selection_field($name, $type, $value = '', $checked = false, $compare = '') 
  {
  
    $selection = '<input type="' . $type . '" name="' . $name . '"';
    if ($value != '') {
      $selection .= ' value="' . $value . '"';
    }

    if ( (@$checked == 'true') || (@$GLOBALS[$name] == 'on') || (@$value && (@$GLOBALS[$name] == @$value)) || (@$value && (@$value == @$compare)) ) 
	{
      $selection .= ' CHECKED';
    }

    $selection .= '>';

    return $selection;
  }


  function os_draw_checkbox_field($name, $value = '', $checked = false, $compare = '') 
  {
    return os_draw_selection_field($name, 'checkbox', $value, $checked, $compare);
  }

  function os_draw_radio_field($name, $value = '', $checked = false, $compare = '') 
  {
    return os_draw_selection_field($name, 'radio', $value, $checked, $compare);
  }

  function os_draw_textarea_field($name, $wrap, $width, $height, $text = '', $params = '', $reinsert_value = true) 
  {
    $field = '<textarea class="round" id="'.$name.'" name="' . $name . '" wrap="' . $wrap . '" cols="' . $width . '" rows="' . $height . '"';
    if ($params) $field .= ' ' . $params;
    $field .= '>';
	
    if (isset($GLOBALS[$name]) && ($GLOBALS[$name]) && ($reinsert_value) ) 
	{
      $field .= $GLOBALS[$name];
    } 
	elseif ($text != '') 
	{
      $field .= $text;
    }
	
    $field .= '</textarea>';

    return $field;
  }


  function os_draw_hidden_field($name, $value = '') {
    $field = '<input type="hidden" name="' . $name . '" value="';
    if ($value != '') {
      $field .= trim($value);
    } else {
      $field .= trim(@$GLOBALS[$name]);
    }
    $field .= '">';

    return $field;
  }


  function os_draw_pull_down_menu($name, $values, $default = '', $params = '', $required = false) {
    $field = '<select class="round" name="' . $name . '"';
    if ($params) $field .= ' ' . $params;
    $field .= '>';
 	if(is_array($values))
   	{
         foreach ($values as $key=>$val) {
             $field .= '<option value="' .$val['id'] . '"';
	
             if ( ((strlen($val['id']) > 0) && isset($GLOBALS[$name]) && ($GLOBALS[$name] == $val['id'])) || ($default == $val['id']) ) 
			 {
                 $field .= ' SELECTED';    
             }    
			 
             $field .= '>' . $val['text'] . '</option>';
         }
   	} 
    $field .= '</select>';

    if ($required) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

  function os_sorting($page,$sort) {

      switch ($page) {
          case FILENAME_CUSTOMERS:

          $nav='&nbsp;<a href="'.os_href_link(FILENAME_CUSTOMERS,'sorting='.$sort.'&'.os_get_all_get_params(array('action','sorting'))).'">&uarr;</a>';
          $nav.='<a href="'.os_href_link(FILENAME_CUSTOMERS,'sorting='.$sort.'-desc&'.os_get_all_get_params(array('action','sorting'))).'">&darr;</a>&nbsp;';

          break;
          
          case FILENAME_CATEGORIES:

          $nav='&nbsp;<a href="'.os_href_link(FILENAME_CATEGORIES,'sorting='.$sort.'&'.os_get_all_get_params(array('action','sorting'))).'">&uarr;</a>';
          $nav.='<a href="'.os_href_link(FILENAME_CATEGORIES,'sorting='.$sort.'-desc&'.os_get_all_get_params(array('action','sorting'))).'">&darr;</a>&nbsp;';

          break;          

      }

      return $nav;

  }
?>