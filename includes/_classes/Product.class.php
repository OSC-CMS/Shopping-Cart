<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiProduct extends CartET
{
	public function getManufacturer($id)
	{
		if (empty($id)) return false;

		$manufacturers_query = osDBquery("
		SELECT 
			* 
		FROM 
			".TABLE_MANUFACTURERS." m 
				left join ".TABLE_MANUFACTURERS_INFO." mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '".(int)$_SESSION['languages_id']."') 
		WHERE 
			m.manufacturers_id = '".(int)$id."'
		");
   
		if (os_db_num_rows($manufacturers_query) > 0)
		{
			$result = os_db_fetch_array($manufacturers_query);

			if (os_not_null($result['manufacturers_image']))
				$image = http_path('images').'manufacturers/'.$result['manufacturers_image'];
			else
				$image = '';

			$result['manufacturers_image'] = $image;

			if ($result['manufacturers_url'] != '')
				$url = '<a target="_blank" href="'.os_href_link(FILENAME_REDIRECT, 'action=manufacturer&'.os_manufacturer_link($result['manufacturers_id'],$result['manufacturers_name'])).'">'.sprintf(BOX_MANUFACTURER_INFO_HOMEPAGE, $result['manufacturers_name']).'</a>';
			else
				$url = '';

			$result['manufacturers_url'] = $url;

			$this->manufacturerData = $result;

			return $result;
		}
	}
}