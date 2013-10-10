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
  
class tableBlock {
    var $table_border = '0';
    var $table_width = '100%';
    var $table_cellspacing = '0';
    var $table_cellpadding = '2';
    var $table_parameters = '';
    var $table_row_parameters = '';
    var $table_data_parameters = '';
	var $table_class = "contentTable";
	var $table_id = "contentTable";

    function tableBlock($contents) {
      $tableBox_string = '';

      $form_set = false;
      if (isset($contents['form'])) {
        $tableBox_string .= $contents['form'] . "\n";
        $form_set = true;
        os_array_shift($contents);
      }

	   if (!empty($this->table_id)) $id = 'id="'. $this->table_id.'"';else $id='';
	   
      $tableBox_string .= '<table class="' . $this->table_class . '" '.$id.' border="' . $this->table_border . '" width="' . $this->table_width . '" cellspacing="' . $this->table_cellspacing . '" cellpadding="' . $this->table_cellpadding . '"';
      if ($this->table_parameters != '') $tableBox_string .= ' ' . $this->table_parameters;
      $tableBox_string .= '>' . "\n";

      for ($i = 0, $n = sizeof($contents); $i < $n; $i++) {
        $tableBox_string .= '  <tr';
        if ($this->table_row_parameters != '') $tableBox_string .= ' ' . $this->table_row_parameters;
        if (isset($contents[$i]['params'])) $tableBox_string .= ' ' . $contents[$i]['params'];
        $tableBox_string .= '>' . "\n";
        if (!isset($contents[$i][0])) $contents[$i][0] = '';
        if (is_array($contents[$i][0])) {
          for ($x = 0, $y = sizeof($contents[$i]); $x < $y; $x++) {
            if ($contents[$i][$x]['text']) {
              $tableBox_string .= '    <td ';
              if ($contents[$i][$x]['align'] != '') $tableBox_string .= ' align="' . $contents[$i][$x]['align'] . '"';
              if ($contents[$i][$x]['params']) {
                $tableBox_string .= ' ' . $contents[$i][$x]['params'];
              } elseif ($this->table_data_parameters != '') {
                $tableBox_string .= ' ' . $this->table_data_parameters;
              }
              $tableBox_string .= '>';
              if ($contents[$i][$x]['form']) $tableBox_string .= $contents[$i][$x]['form'];
              $tableBox_string .= $contents[$i][$x]['text'];
              if ($contents[$i][$x]['form']) $tableBox_string .= '</form>';
              $tableBox_string .= '</td>' . "\n";
            }
          }
        } else {
          $tableBox_string .= '    <td ';
          if (!isset($contents[$i]['align'])) $contents[$i]['align'] = '';
          if ($contents[$i]['align'] != '') $tableBox_string .= ' align="' . $contents[$i]['align'] . '"';
          if (isset($contents[$i]['params'])) {
            $tableBox_string .= ' ' . $contents[$i]['params'];
          } elseif ($this->table_data_parameters != '') {
            $tableBox_string .= ' ' . $this->table_data_parameters;
          }
          $tableBox_string .= '>' . $contents[$i]['text'] . '</td>' . "\n";
        }

        $tableBox_string .= '  </tr>' . "\n";
      }

      $tableBox_string .= '</table>' . "\n";

      if ($form_set) $tableBox_string .= '</form>' . "\n";

      return $tableBox_string;
    }
  }
?>