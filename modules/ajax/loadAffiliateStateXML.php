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

defined('_VALID_OS') or die('Direct Access to this location is not allowed.');

$country = $_REQUEST['country_id'];

	if ( isset($_REQUEST['country_id']) && os_not_null($_REQUEST['country_id']) ) {
		$zones_array = array();
		$zones_query = os_db_query("select zone_name from ".TABLE_ZONES." where zone_country_id = '".(int) $country."' order by zone_name");

		if(os_db_num_rows($zones_query) > 0) {
			if(os_db_num_rows($zones_query) > 1) {
			while ($zones_values = os_db_fetch_array($zones_query)) {
				$zones_array[] = array ('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
				}
				$_RESULT = array("stateXML" => os_draw_pull_down_menuNote(array ('name' => 'a_state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="Requirement">'.ENTRY_STATE_TEXT.'</span>' : '')), $zones_array, $zone_name, 'id="state"'));
			} else {
				$_RESULT = array("stateXML" => os_draw_input_fieldNote(array ('name' => 'a_state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="Requirement">'.ENTRY_STATE_TEXT.'</span>' : '')), $zones_values['zone_name'], 'id="state"'));
			}
		} else {
			$_RESULT = array("stateXML" => os_draw_input_fieldNote(array ('name' => 'a_state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="Requirement">'.ENTRY_STATE_TEXT.'</span>' : '')), '', 'id="state"'));
		}
	} else {
		$_RESULT = array("stateXML" => os_draw_input_fieldNote(array ('name' => 'a_state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="Requirement">'.ENTRY_STATE_TEXT.'</span>' : '')), '', 'id="state"'));
	}
?>