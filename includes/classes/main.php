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

class main
{
	function main()
	{
		$this->SHIPPING = array();

		global $default_cache;

		if (isset($default_cache['shipping_status']))
		{
			if (isset($default_cache['shipping_status'][(int)$_SESSION['languages_id']]) && !empty($default_cache['shipping_status'][(int)$_SESSION['languages_id']]))
			{
				$shipping_data = $default_cache['shipping_status'][(int)$_SESSION['languages_id']];

				foreach ($shipping_data as $shipping_status_id => $_val)
				{
					$this->SHIPPING[$shipping_status_id]=array
					(
						'name'=>$_val['shipping_status_name'],
						'image'=>$_val['shipping_status_image']
					);
				}
			}
		}
		else
		{
			$status_query = osDBquery("SELECT shipping_status_name, shipping_status_image,shipping_status_id FROM ".TABLE_SHIPPING_STATUS." where language_id = '".(int)$_SESSION['languages_id']."'");

			while ($status_data=os_db_fetch_array($status_query,true)) 
			{
				$this->SHIPPING[$status_data['shipping_status_id']]=array
				(
					'name'=>$status_data['shipping_status_name'],
					'image'=>$status_data['shipping_status_image']
				);
			}
		}
	}

	function getShippingStatusName($id)
	{
		if (SHOW_SHIPPING == 'true')
		{
			return $this->SHIPPING[$id]['name'];
		}

		return;
	}

	function getShippingStatusImage($id)
	{
		if (SHOW_SHIPPING == 'true')
		{
			if ($this->SHIPPING[$id]['image'])
			return 'admin/images/icons/'.$this->SHIPPING[$id]['image'];
		}
		return;
	}

	function getShippingLink()
	{
		if (SHOW_SHIPPING == 'true')
		{
			//return ' '.SHIPPING_EXCL.'<a href="'. os_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS) .'" target="_blank" onclick="window.open(\'' . os_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS) . '\', \'popUp\', \'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=395,height=320\'); return false;">'.SHIPPING_COSTS.'</a>';
			return ' '.SHIPPING_EXCL.'<a href="'.os_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS) .'" rel="modal:open" target="_blank">'.SHIPPING_COSTS.'</a>';
		}
		return;
	}

	function getTaxNotice()
	{
		if ($_SESSION['customers_status']['customers_status_show_price'] == 0)
			return;

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 0)
		{
			return TAX_INFO_INCL_GLOBAL;
		}
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
		{
			return TAX_INFO_ADD_GLOBAL;
		}
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0)
		{
			return TAX_INFO_EXCL_GLOBAL;
		}

		return;
	}

	function getTaxInfo($tax_rate)
	{
		$tax_info = '';
		if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] != 0)
		{
			$tax_info = sprintf(TAX_INFO_INCL, $tax_rate.' %');
		}
		if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
		{
			$tax_info = sprintf(TAX_INFO_ADD, $tax_rate.' %');
		}
		if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0)
		{
			$tax_info = sprintf(TAX_INFO_EXCL, $tax_rate.' %');
		}

		return $tax_info;
	}

	function getShippingNotice()
	{
		if (SHOW_SHIPPING == 'true')
		{
			return ' '.SHIPPING_EXCL.'<a href="'.os_href_link(FILENAME_CONTENT, 'coID='.SHIPPING_INFOS).'">'.SHIPPING_COSTS.'</a>';
		}
		return;
	}

	function getContentLink($coID,$text)
	{
		return '<a href="'. os_href_link(FILENAME_POPUP_CONTENT, 'coID='.$coID) .'" rel="modal:open" target="_blank">'.$text.'</a>';
	}
}
?>
