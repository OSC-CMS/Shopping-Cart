<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

function os_get_extra_fields($customer_id,$languages_id)
{
          $extra_fields_query = os_db_query("select ce.fields_id, ce.fields_input_type, ce.fields_input_value, ce.fields_required_status, cei.fields_name, ce.fields_status, ce.fields_input_type from " . TABLE_EXTRA_FIELDS . " ce, " . TABLE_EXTRA_FIELDS_INFO . " cei where ce.fields_status=1 and cei.fields_id=ce.fields_id and cei.languages_id =" . $languages_id);
          $extra_fields_string ='';
          $extra_fields_string_name ='';
          $extra_fields_string_value .='';
          if(os_db_num_rows($extra_fields_query)>0){
             while($extra_fields = os_db_fetch_array($extra_fields_query)){
                  $value='';
                  if(isset($customer_id)){
                          $value_query = os_db_query("select value from " . TABLE_CUSTOMERS_TO_EXTRA_FIELDS . " where customers_id=" . $customer_id . " and fields_id=" . $extra_fields['fields_id']);
                          $value_info = os_db_fetch_array($value_query);
                          $value_list = explode("\n", $value_info['value']);
                          for($i = 0, $n = sizeof($value_list); $i < $n; $i++)
                          {
                            $value_list[$i] = trim($value_list[$i]);
													}
													$value = $value_list[0];
                  }
                  $extra_fields_string_name = $extra_fields['fields_name'];


									$select_values_list = explode("\n", $extra_fields['fields_input_value']);
									$select_values = array();
									foreach($select_values_list as $item)
									{
									  $item = trim($item);
                    $select_values[] = array('id' => $item, 'text' => $item);
									}

									switch($extra_fields['fields_input_type'])
									{
									  case  0: $extra_fields_string_value = os_draw_input_field('fields_' . $extra_fields['fields_id'],$value). (($extra_fields['fields_required_status']==1) ? '&nbsp;<span class="Requirement">*</span>': ''); break;
									  case  1: $extra_fields_string_value = os_draw_textarea_field('fields_' . $extra_fields['fields_id'], 'soft', 50, 6,$value,'style="width:400px;"'). (($extra_fields['fields_required_status']==1) ? '&nbsp;<span class="Requirement">*</span>': ''); break;
									  case  2:
									      $extra_fields_string_value = '';
									  	foreach($select_values_list as $item)
											{
                          $item = trim($item);
                      		$extra_fields_string_value .= os_draw_selection_field('fields_' . $extra_fields['fields_id'], 'radio', $item, (($value == $item)?(true):(false))).$item. (($extra_fields['fields_required_status']==1) ? '&nbsp;<span class="Requirement">*</span>': '').'<br />';
                      		$extra_fields['fields_required_status']  = 0;
											}
									    break;
									  case  3:
									      $extra_fields_string_value = '';
											$cnt = 1;
									  	foreach($select_values_list as $item)
											{
											    $item = trim($item);
                      		$extra_fields_string_value .= os_draw_selection_field('fields_' . $extra_fields['fields_id'] . '_' . ($cnt++), 'checkbox', $item, ((@in_array($item, $value_list))?(true):(false))).$item. (($extra_fields['fields_required_status']==1) ? '&nbsp;<span class="Requirement">*</span>': '').'<br />';
                      		$extra_fields['fields_required_status']  = 0;
											}
											$extra_fields_string_value .= os_draw_hidden_field('fields_' . $extra_fields['fields_id'] . '_total' , $cnt);
									    break;
									  case  4: $extra_fields_string_value = os_draw_pull_down_menu('fields_' . $extra_fields['fields_id'], $select_values, $value).(($extra_fields['fields_required_status']==1) ? '&nbsp;<span class="Requirement">*</span>': ''); break;
									  default: $extra_fields_string_value = os_draw_input_field('fields_' . $extra_fields['fields_id'],$value). (($extra_fields['fields_required_status']==1) ? '&nbsp;<span class="Requirement">*</span>': ''); break;
									}

$extra_fields_string[] = array('NAME' => $extra_fields_string_name,
                                                   'VALUE' => $extra_fields_string_value);

             }
          }
          return $extra_fields_string;
}
?>