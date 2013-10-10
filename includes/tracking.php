<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$ref_url = parse_url(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'');
if ($_SESSION['tracked'] != true) { 
$_SESSION['tracking']['http_referer']= $ref_url;
	$_SESSION['tracked'] = true; 
}

 if (!isset($_SESSION['tracking']['ip'])) 
    $_SESSION['tracking']['ip'] = $_SERVER['REMOTE_ADDR'];

if (!isset ($_SESSION['tracking']['refID'])) {	
	if (isset($_GET['refID'])) {
		      $campaign_check_query_raw = "SELECT *
			                            FROM ".TABLE_CAMPAIGNS." 
			                            WHERE campaigns_refID = '".os_db_input($_GET['refID'])."'";
			$campaign_check_query = os_db_query($campaign_check_query_raw);
		if (os_db_num_rows($campaign_check_query) > 0) {			
			$_SESSION['tracking']['refID'] = os_db_input($_GET['refID']);		
			$insert_sql = array('user_ip'=>$_SESSION['tracking']['ip'],'campaign'=>os_db_input($_GET['refID']),'time'=>'now()');
			
			os_db_perform(TABLE_CAMPAIGNS_IP,$insert_sql);	
			} 	
	}
}
if (!isset ($_SESSION['tracking']['date']))
	$_SESSION['tracking']['date'] = (date("Y-m-d H:i:s"));
if (!isset ($_SESSION['tracking']['browser']))
	$_SESSION['tracking']['browser'] = $_SERVER["HTTP_USER_AGENT"];



$i = count($_SESSION['tracking']['pageview_history']);
if ($i > 6) {
	array_shift($_SESSION['tracking']['pageview_history']);
	$_SESSION['tracking']['pageview_history'][6] = $ref_url;
} else {
	$_SESSION['tracking']['pageview_history'][$i] = $ref_url;
}


if (isset($_SESSION['tracking']['pageview_history'][$i]) && $_SESSION['tracking']['pageview_history'][$i] == $_SESSION['tracking']['http_referer'])
	array_shift($_SESSION['tracking']['pageview_history']);

?>