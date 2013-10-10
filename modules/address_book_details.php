<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

  $module=new osTemplate;

  if (!isset($process)) $process = false;


  if (ACCOUNT_GENDER == 'true') {
    $male = ($entry['entry_gender'] == 'm') ? true : false;
    $female = ($entry['entry_gender'] == 'f') ? true : false;

  $module->assign('gender','1');
  $module->assign('INPUT_MALE',os_draw_radio_field(array('name'=>'gender','suffix'=>MALE.'&nbsp;'), 'm',$male, 'id="gender" checked="checked"'));
  $module->assign('INPUT_FEMALE',os_draw_radio_field(array('name'=>'gender','suffix'=>FEMALE.'&nbsp;','text'=>(os_not_null(ENTRY_GENDER_TEXT) ? '<span class="Requirement">&nbsp;' . ENTRY_GENDER_TEXT . '</span>': '')), 'f',$female, 'id="gender"'));
  $module->assign('ENTRY_GENDER_ERROR', ENTRY_GENDER_ERROR);


  }

  $module->assign('INPUT_FIRSTNAME',os_draw_input_fieldNote(array('name'=>'firstname','text'=>'&nbsp;' . (os_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="Requirement">' . ENTRY_FIRST_NAME_TEXT . '</span>': '')),$entry['entry_firstname'], 'id="firstname"'));
  $module->assign('ENTRY_FIRST_NAME_ERROR', ENTRY_FIRST_NAME_ERROR);
if (ACCOUNT_SECOND_NAME == 'true') {
	$module->assign('secondname', '1');
$module->assign('INPUT_SECONDNAME', os_draw_input_fieldNote(array ('name' => 'secondname', 'text' => '&nbsp;'. (os_not_null(ENTRY_SECOND_NAME_TEXT) ? '<span class="Requirement">'.ENTRY_SECOND_NAME_TEXT.'</span>' : '')),$entry['entry_secondname'], 'id="secondname"'));
}
if (ACCOUNT_LAST_NAME == 'true')
{
	$module->assign('lastname', '1');
  $module->assign('INPUT_LASTNAME',os_draw_input_fieldNote(array('name'=>'lastname','text'=>'&nbsp;' . (os_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="Requirement">' . ENTRY_LAST_NAME_TEXT . '</span>': '')),$entry['entry_lastname'], 'id="lastname"'));
  $module->assign('ENTRY_LAST_NAME_ERROR', ENTRY_LAST_NAME_ERROR);
}

  if (ACCOUNT_COMPANY == 'true') {
  $module->assign('company','1');
  $module->assign('INPUT_COMPANY',os_draw_input_fieldNote(array('name'=>'company','text'=>'&nbsp;' . (os_not_null(ENTRY_COMPANY_TEXT) ? '<span class="Requirement">' . ENTRY_COMPANY_TEXT . '</span>': '')), $entry['entry_company']));


  }


  if (ACCOUNT_STREET_ADDRESS == 'true') {
  $module->assign('street_address','1');
  $module->assign('INPUT_STREET',os_draw_input_fieldNote(array('name'=>'street_address','text'=>'&nbsp;' . (os_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="Requirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>': '')), $entry['entry_street_address'], 'id="address"'));
  $module->assign('ENTRY_STREET_ADDRESS_ERROR', ENTRY_STREET_ADDRESS_ERROR);
  }

  if (ACCOUNT_SUBURB == 'true') {
  $module->assign('suburb','1');
  $module->assign('INPUT_SUBURB',os_draw_input_fieldNote(array('name'=>'suburb','text'=>'&nbsp;' . (os_not_null(ENTRY_SUBURB_TEXT) ? '<span class="Requirement">' . ENTRY_SUBURB_TEXT . '</span>': '')), $entry['entry_suburb']));

  }

  if (ACCOUNT_POSTCODE == 'true') {
  $module->assign('postcode','1');
  $module->assign('INPUT_CODE',os_draw_input_fieldNote(array('name'=>'postcode','text'=>'&nbsp;' . (os_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="Requirement">' . ENTRY_POST_CODE_TEXT . '</span>': '')), $entry['entry_postcode'], 'id="postcode"'));
  $module->assign('ENTRY_POST_CODE_ERROR', ENTRY_POST_CODE_ERROR);
  }

  if (ACCOUNT_CITY == 'true') {
  $module->assign('city','1');
  $module->assign('INPUT_CITY',os_draw_input_fieldNote(array('name'=>'city','text'=>'&nbsp;' . (os_not_null(ENTRY_CITY_TEXT) ? '<span class="Requirement">' . ENTRY_CITY_TEXT . '</span>': '')), $entry['entry_city'], 'id="city"'));
  $module->assign('ENTRY_CITY_ERROR', ENTRY_CITY_ERROR);
  }
  
if (ACCOUNT_STATE == 'true' && ACCOUNT_COUNTRY == 'true') {
	$module->assign('state', '1');

    if ($process != true) {

//	    $country = (isset($_POST['country']) ? os_db_prepare_input($_POST['country']) : STORE_COUNTRY);
	    $zone_id = 0;
		 $check_query = os_db_query("select count(*) as total from ".TABLE_ZONES." where zone_country_id = '".(int)$entry['entry_country_id']."'");
		 $check = os_db_fetch_array($check_query);
		 $entry_state_has_zones = ($check['total'] > 0);
		 if ($entry_state_has_zones == true) {
			$zones_array = array ();
			$zones_query = os_db_query("select zone_name from ".TABLE_ZONES." where zone_country_id = '".(int)$entry['entry_country_id']."' order by zone_name");
			while ($zones_values = os_db_fetch_array($zones_query)) {
				$zones_array[] = array ('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
			}
			
			$zone = os_db_query("select distinct zone_id, zone_name from ".TABLE_ZONES." where zone_country_id = '".(int)$entry['entry_country_id']."' and zone_code = '".os_db_input($state)."'");

	      if (os_db_num_rows($zone) > 0) {
	        $zone_id = $zone['zone_id'];
	        $zone_name = $zone['zone_name'];

	      } else {

		   $zone = os_db_query("select distinct zone_id, zone_name from ".TABLE_ZONES." where zone_country_id = '".(int)$entry['entry_country_id']."' and zone_code = '".os_db_input($state)."'");

	      if (os_db_num_rows($zone) > 0) {
	          $zone_id = $zone['zone_id'];
	          $zone_name = $zone['zone_name'];
	        }
	      }
		}
	}

      if ($entry_state_has_zones == true) {
        $state_input = os_draw_pull_down_menuNote(array ('name' => 'state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="Requirement">'.ENTRY_STATE_TEXT.'</span>' : '')), $zones_array, os_get_zone_name($entry['entry_country_id'], $entry['entry_zone_id'], $entry['entry_state']), ' id="state"');

      } else {
		 $state_input = os_draw_input_fieldNote(array ('name' => 'state', 'text' => '&nbsp;'. (os_not_null(ENTRY_STATE_TEXT) ? '<span class="Requirement">'.ENTRY_STATE_TEXT.'</span>' : '')), os_get_zone_name($entry['entry_country_id'], $entry['entry_zone_id'], $entry['entry_state']), ' id="state"');

      }
		
	$module->assign('INPUT_STATE', $state_input);
   $module->assign('ENTRY_STATE_ERROR_SELECT', ENTRY_STATE_ERROR_SELECT);
} else {
	$module->assign('state', '0');
}

  if ($_POST['country']){
  $selected = $_POST['country'];
  }else{
  $selected = $entry['entry_country_id'];
  }

if (ACCOUNT_COUNTRY == 'true') {

  $module->assign('country','1');
  
  
  if ($process == true) $entry['entry_country_id'] = (int)$_POST['country'];

   //$module->assign('SELECT_COUNTRY', os_get_country_list('country', $entry['entry_country_id'], 'id="country", onChange="document.getElementById(\'stateXML\').innerHTML = \'' . ENTRY_STATEXML_LOADING . '\';loadXMLDoc(\'loadStateXML\',{country_id: this.value});"') . (os_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="Requirement">' . ENTRY_COUNTRY_TEXT . '</span>': ''));
   $module->assign('SELECT_COUNTRY', os_get_country_list(array ('name' => 'country', 'text' => '&nbsp;'. (os_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="Requirement">'.ENTRY_COUNTRY_TEXT.'</span>' : '')), $selected, 'id="country"'));

     //buttons	
	$_array = array('img' => 'button_update.gif', 
	                                'href' => '', 
									'alt' => IMAGE_BUTTON_UPDATE, 'code' => '');
									
	$_array = apply_filter('button_update', $_array);	
	
	//TODO: проверить что за кнопка
	if (empty($_array['code']))
	{
	   $_array['code'] = buttonSubmit($_array['img'], null, $_array['alt'], 'name=loadStateXML');
	}
	
   $module->assign('SELECT_COUNTRY_NOSCRIPT', '<noscript><br />' . $_array['code'] . '<br />' . ENTRY_STATE_RELOAD . '</noscript>');

   $module->assign('ENTRY_COUNTRY_ERROR', ENTRY_COUNTRY_ERROR);

} else {
	$osTemplate->assign('country', '0');
}

  if ((isset($_GET['edit']) && ($_SESSION['customer_default_address_id'] != $_GET['edit'])) || (isset($_GET['edit']) == false) ) {
  $module->assign('new','1');
  $module->assign('CHECKBOX_PRIMARY',os_draw_checkbox_field('primary', 'on', false, 'id="primary"'));

  }

  $module->assign('language', $_SESSION['language']);
  $module->caching = 0;
  $main_content=$module->fetch(CURRENT_TEMPLATE . '/module/address_book_details.html');
  $osTemplate->assign('MODULE_address_book_details',$main_content);
?>