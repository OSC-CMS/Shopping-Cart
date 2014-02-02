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
   
		if (os_db_num_rows($manufacturers_query, true) > 0)
		{
			$result = os_db_fetch_array($manufacturers_query, true);

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

	public function getProductExtraFields($product_id)
	{
		if (empty($product_id)) return false;

		$extra_fields_query = osDBquery("
		SELECT
			pef.products_extra_fields_name as name, pef.products_extra_fields_group, ptf.products_extra_fields_value as value
		FROM
			".TABLE_PRODUCTS_EXTRA_FIELDS." pef
				LEFT JOIN ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." ptf ON ptf.products_extra_fields_id = pef.products_extra_fields_id
		WHERE
			ptf.products_id = ".(int)$product_id." AND
			ptf.products_extra_fields_value <> '' AND
			pef.products_extra_fields_status = 1 AND
			(pef.languages_id = '0' or pef.languages_id = '".(int)$_SESSION['languages_id']."')
		ORDER BY
			products_extra_fields_order
		");

		$efResult = array();
		if (os_db_num_rows($extra_fields_query, true) > 0)
		{
			while ($extra_fields = os_db_fetch_array($extra_fields_query, true))
			{
				$extra_fields_data[$extra_fields['products_extra_fields_group']][] = array(
					'NAME' => $extra_fields['name'],
					'VALUE' => $extra_fields['value']
				);
			}

			$groupsDescQuery = osDBquery("
			SELECT
				*, d.extra_fields_groups_name as group_name
			FROM
				".DB_PREFIX."products_extra_fields_groups g
					LEFT JOIN ".DB_PREFIX."products_extra_fields_groups_desc d ON (g.extra_fields_groups_id = d.extra_fields_groups_id AND d.extra_fields_groups_languages_id = '".(int)$_SESSION['languages_id']."')
			WHERE
				g.extra_fields_groups_status = 1
			ORDER BY
				g.extra_fields_groups_order ASC
			");

			if (os_db_num_rows($groupsDescQuery, true) > 0)
			{
				while ($groups = os_db_fetch_array($groupsDescQuery, true))
				{
					$groupDescEdit[$groups['extra_fields_groups_id']] = $groups;
				}
			}

			foreach($groupDescEdit AS $gId => $gValue)
			{
				foreach ($extra_fields_data as $fGId => $fValue)
				{
					if ($gId == $fGId)
					{
						$efResult[$gId] = $gValue;
						$efResult[$gId]['values'] = $fValue;
					}
				}
			}
		}

		return $efResult;
	}

}