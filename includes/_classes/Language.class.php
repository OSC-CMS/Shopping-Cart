<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiLanguage extends CartET
{
	private $aLangs = array();

	public function get()
	{
		if (!empty($this->aLangs))
		{
			return $this->aLangs;
		}
		else
		{
			$languagesQuery = os_db_query("SELECT * FROM ".TABLE_LANGUAGES." ORDER BY sort_order");
			$aLang = array();
			while ($lang = os_db_fetch_array($languagesQuery))
			{
				$aLang[$lang['languages_id']] = $lang;
			}

			$this->aLangs = $aLang;

			return $aLang;
		}
	}
}
?>