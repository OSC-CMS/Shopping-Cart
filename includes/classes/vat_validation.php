<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class vat_validation
{
	var $vat_info;
	var $vat_mod;

	function vat_validation($vat_id = '', $customers_id = '', $customers_status = '', $country_id = '', $guest = false) 
	{
		$this->vat_info = array ();
		$this->live_check = ACCOUNT_COMPANY_VAT_LIVE_CHECK;
		if (os_not_null($vat_id)) {
			$this->getInfo($vat_id, $customers_id, $customers_status, $country_id, $guest);
		} 
		else 
		{

			if ($guest) 
			{
				$this->vat_info = array ('status' => DEFAULT_CUSTOMERS_STATUS_ID_GUEST);
			} 
			else 
			{
				$this->vat_info = array ('status' => DEFAULT_CUSTOMERS_STATUS_ID);
			}

		}
	}

	function getInfo($vat_id = '', $customers_id = '', $customers_status = '', $country_id = '', $guest = false) 
	{
		if (!$guest) 
		{
			if ($vat_id) 
			{
				$validate_vatid = $this->validate_vatid($vat_id, $country_id);

				switch ($validate_vatid) 
				{

					case '0' :
						if (ACCOUNT_VAT_BLOCK_ERROR == 'true') 
						{
							$error = true;
						}
						$status = DEFAULT_CUSTOMERS_STATUS_ID;
						$vat_id_status = '0';
						break;

					case '1' :
						if ($country_id == STORE_COUNTRY) 
						{
							if (ACCOUNT_COMPANY_VAT_GROUP == 'true') 
							{
								$status = DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL;
							} 
							else 
							{
								$status = DEFAULT_CUSTOMERS_STATUS_ID;
							}
						} 
						else 
						{
							if (ACCOUNT_COMPANY_VAT_GROUP == 'true') 
							{
								$status = DEFAULT_CUSTOMERS_VAT_STATUS_ID;
							} 
							else 
							{
								$status = DEFAULT_CUSTOMERS_STATUS_ID;
							}
						}
						$error = false;
						$vat_id_status = '1';
						break;

					case '8' :
						if (ACCOUNT_VAT_BLOCK_ERROR == 'true') 
						{
							$error = true;
						}
						$status = DEFAULT_CUSTOMERS_STATUS_ID;
						$vat_id_status = '8';
						break;

					case '9' :
						if (ACCOUNT_VAT_BLOCK_ERROR == 'true') 
						{
							$error = true;
						}
						$status = DEFAULT_CUSTOMERS_STATUS_ID;
						$vat_id_status = '9';
						break;

					default :
						$status = DEFAULT_CUSTOMERS_STATUS_ID;

				}

			} 
			else 
			{
				if ($customers_status) 
				{
					$status = $customers_status;
				} 
				else 
				{
					$status = DEFAULT_CUSTOMERS_STATUS_ID;
				}
				$vat_id_status = '';
				$error = false;
			}

		} 
		else 
		{
			if ($vat_id)
			{
				$validate_vatid = $this->validate_vatid($vat_id, $country_id);

				switch ($validate_vatid) 
				{
					case '0' :
						if (ACCOUNT_VAT_BLOCK_ERROR == 'true') 
						{
							$error = true;
						}
						$status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
						$vat_id_status = '0';
						break;

					case '1' :
						if ($country_id == STORE_COUNTRY) 
						{
							if (ACCOUNT_COMPANY_VAT_GROUP == 'true') 
							{
								$status = DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL;
							} 
							else
							{
								$status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
							}
						} 
						else 
						{
							if (ACCOUNT_COMPANY_VAT_GROUP == 'true') 
							{
								$status = DEFAULT_CUSTOMERS_VAT_STATUS_ID;
							} 
							else
							{
								$status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
							}
						}
						$error = false;
						$vat_id_status = '1';
						break;

					case '8' :
						if (ACCOUNT_VAT_BLOCK_ERROR == 'true') 
						{
							$error = true;
						}
						$status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
						$vat_id_status = '8';

						break;

					case '9' :
						if (ACCOUNT_VAT_BLOCK_ERROR == 'true') 
						{
							$error = true;
						}
						$status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
						$vat_id_status = '9';
						break;

					default :
						$status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;

				}

			}
			else 
			{
				if ($customers_status)
				{
					$status = $customers_status;
				} 
				else 
				{
					$status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
				}
				$vat_id_status = '';
				$error = false;
			}
		}

		if ($customers_id)
		{
			$customers_status_query = os_db_query("SELECT customers_status FROM ".TABLE_CUSTOMERS." WHERE customers_id = '".$customers_id."'");
			$customers_status_value = os_db_fetch_array($customers_status_query);

			if ($customers_status_value['customers_status'] != 0) 
			{
				$status = $status;
			} 
			else 
			{
				$status = $customers_status_value['customers_status'];
			}
		}

		$this->vat_info = array ('status' => $status, 'vat_id_status' => $vat_id_status, 'error' => $error, 'validate' => $validate_vatid);

	}

	function validate_vatid($vat_id, $country_id) 
	{
		$remove = array (' ', '-', '/', '\\', '.', ':', ',');
		$results = array (0 => '0', 1 => '1', 8 => '8', 9 => '9'); //$results = array(0 => 'false', 1 => 'true', 8 => 'unknown country', 9 => 'unknown algorithm');
		$vat_id = trim(chop($vat_id));

		// sonderzeichen entfernen
		for ($i = 0; $i < count($remove); $i ++) 
		{
			$vat_id = str_replace($remove[$i], '', $vat_id);
		} // end for($i = 0; $i < count($remove)); $i++)

		// land bestimmen
				// RWS starts
		// Get country ISO code after $country_id
		$country_array = array ();
		$country_array = os_get_countriesList($country_id, true);
		$vat_id_country = strtolower($country_array['countries_iso_code_2']);
		// Check if $vat_id contains country code already. If not, add it to $vat_id
		$country = strtolower(substr($vat_id, 0, 2));
		if ($vat_id_country != $country)
		{
			$country = $vat_id_country;
			$vat_id = $country.$vat_id;
		}

		// RWS ends

		// je nach land anders behandeln
		switch ($country) {
			case 'ad' : // andorra
				return $results[9];

			case 'be' : // belgien
				return $results[$this->checkVatID_be($vat_id)];

			case 'bg' : // bulgarien
				return $results[9];

			case 'dk' : // daenemark
				return $results[$this->checkVatID_dk($vat_id)];

			case 'de' : // deutschland
				return $results[$this->checkVatID_de($vat_id)];

			case 'ee' : // estland
				return $results[$this->checkVatID_ee($vat_id)];

			case 'fi' : // finnland
				return $results[$this->checkVatID_fi($vat_id)];

			case 'fr' : // frankreich
				return $results[$this->checkVatID_fr($vat_id)];

			case 'gi' : // gibraltar
				return $results[9];

			case 'el' : // griechenland
				return $results[$this->checkVatID_el($vat_id)];

			case 'gb' : // grossbrittanien
			case 'uk' : // grossbrittanien
				return $results[$this->checkVatID_gb($vat_id)];

			case 'ie' : // irland
				return $results[$this->checkVatID_ie($vat_id)];

			case 'is' : // island
				return $results[9];

			case 'it' : // italien
				return $results[$this->checkVatID_it($vat_id)];

			case 'lv' : // lettland
				return $results[$this->checkVatID_lv($vat_id)];

			case 'lt' : // litauen
				return $results[$this->checkVatID_lt($vat_id)];

			case 'lu' : // luxemburg
				return $results[$this->checkVatID_lu($vat_id)];

			case 'mt' : // malta
				return $results[$this->checkVatID_mt($vat_id)];

			case 'nl' : // niederlande
				return $results[$this->checkVatID_nl($vat_id)];

			case 'no' : // norwegen
				return $results[9];

			case 'at' : // oesterreich
				return $results[$this->checkVatID_at($vat_id)];

			case 'pl' : // polen
				return $results[$this->checkVatID_pl($vat_id)];

			case 'pt' : // portugal
				return $results[$this->checkVatID_pt($vat_id)];

			case 'ro' : // rumaenien
				return $results[9];

			case 'se' : // schweden
				return $results[$this->checkVatID_se($vat_id)];

			case 'ch' : // schweiz
				return $results[9];

			case 'sk' : // slowakai
				return $results[$this->checkVatID_sk($vat_id)];

			case 'si' : // slowenien
			case 'sl' : // welches ist richtig?
				return $results[$this->checkVatID_si($vat_id)];

			case 'es' : // spanien
				return $results[$this->checkVatID_es($vat_id)];

			case 'cz' : // tschechien
				return $results[$this->checkVatID_cz($vat_id)];

			case 'hu' : // ungarn
				return $results[$this->checkVatID_hu($vat_id)];

			case 'cy' : // zypern
				return $results[9];

			case 'r0' : // canadian LUHN-10 code checking
			case 'r1' :
			case 'r2' :
			case 'r3' :
			case 'r4' :
			case 'r5' :
			case 'r6' :
			case 'r7' :
			case 'r8' :
			case 'r9' :
				return $results[$this->checkVatID_c($vat_id)];

			default :
				return $results[8];
		}
	}

	/********************************************************************
	* landesabhaengige Hilfsfunktionen zur Berechnung                   *
	********************************************************************/

	// Canada
	function checkVatID_c($vat_id) 
	{
		if (strlen($vat_id) != 10)
			return 0;

		// LUHN-10 code http://www.ee.unb.ca/tervo/ee4253/luhn.html

		$id = substr($vat_id, 1);
		$checksum = 0;
		for ($i = 9; $i > 0; $i --)
		{
			$digit = $vat_id {$i};
			if ($i % 2 == 1)
				$digit *= 2;
			if ($digit >= 10) 
			{
				$checksum += $digit -10 + 1;
			} 
			else 
			{
				$checksum += $digit;
			}
		}
		if ($this->modulo($checksum, 10) == 0)
			return 1;

		return 0;
	} // Canada

	// belgien
	function checkVatID_be($vat_id)
	{
		if (strlen($vat_id) != 11)
			return 0;

		$checkvals = (int) substr($vat_id, 2, -2);
		$checksum = (int) substr($vat_id, -2);

		if (97 - $this->modulo($checkvals, 97) != $checksum)
			return 0;

		return 1;
	} // end belgien

	// daenemark
	function checkVatID_dk($vat_id) 
	{
		if (strlen($vat_id) != 10)
			return 0;

		$weights = array (2, 7, 6, 5, 4, 3, 2, 1);
		$checksum = 0;

		for ($i = 0; $i < 8; $i ++)
			$checksum += (int) $vat_id[$i +2] * $weights[$i];
		if ($this->modulo($checksum, 11) > 0)
			return 0;

		return 1;
	} // end daenemark

	// deutschland
	function checkVatID_de($vat_id) 
	{
		if (strlen($vat_id) != 11)
			return 0;

		$prod = 10;
		$checkval = 0;
		$checksum = (int) substr($vat_id, -1);

		for ($i = 2; $i < 10; $i ++) 
		{
			$checkval = $this->modulo((int) $vat_id[$i] + $prod, 10);
			if ($checkval == 0)
				$checkval = 10;
			$prod = $this->modulo($checkval * 2, 11);
		} // end for($i = 2; $i < 10; $i++)
		$prod = $prod == 1 ? 11 : $prod;
		if (11 - $prod != $checksum)
			return 0;

		return 1;
	} // end deutschland

	// estland
	function checkVatID_ee($vat_id)
	{

		if (strlen($vat_id) != 11)
			return 0;
		if (!is_numeric(substr($vat_id, 2)))
			return 0;

		if ($this->live_check = true) 
		{

			return $this->live($vat_id);

		} 
		else 
		{
			return 9; // es gibt keinen algorithmus
		}
	} // end estland

	// finnland
	function checkVatID_fi($vat_id)
	{
		if (strlen($vat_id) != 10)
			return 0;

		$weights = array (7, 9, 10, 5, 8, 4, 2);
		$checkval = 0;
		$checksum = (int) substr($vat_id, -1);

		for ($i = 0; $i < 8; $i ++)
			$checkval += (int) $vat_id[$i +2] * $weights[$i];

		if (11 - $this->modulo($checkval, 11) != $checksum)
			return 0;

		return 1;
	} // end finnland

	// frankreich
	function checkVatID_fr($vat_id)
	{
		if (strlen($vat_id) != 13)
			return 0;
		if (!is_numeric(substr($vat_id), 4))
			return 0;

		if ($this->live_check = true) 
		{
			return $this->live($vat_id);

		} 
		else 
		{
			return 9; // es gibt keinen algorithmus
		}

	} // end frankreich

	// griechenland
	function checkVatID_el($vat_id) 
	{
		if (strlen($vat_id) != 11)
			return 0;

		$checksum = substr($vat_id, -1);
		$checkval = 0;

		for ($i = 1; $i <= 8; $i ++)
			$checkval += (int) $vat_id[10 - $i] * pow(2, $i);
		$checkval = $this->modulo($checkval, 11) > 9 ? 0 : $this->modulo($checkval, 11);
		if ($checkval != $checksum)
			return 0;

		return 1;
	} // end griechenland

	// grossbrittanien
	function checkVatID_gb($vat_id) 
	{
		if (strlen($vat_id) != 11 && strlen($vat_id) != 14)
			return 0;
		if (!is_numeric(substr($vat_id, 2)))
			return 0;

		if ($this->live_check = true) 
		{
			return $this->live($vat_id);
		} 
		else 
		{
			return 9; // es gibt keinen algorithmus
		}

	} // end grossbrittanien

	/********************************************
	* irland                                    *
	********************************************/
	// irland switch
	function checkVatID_ie($vat_id) 
	{
		if (strlen($vat_id) != 10)
			return 0;
		if (!checkVatID_ie_new($vat_id) && !checkVatID_ie_old($vat_id))
			return 0;

		return 1;
	} // end irland switch

	// irland alte methode
	function checkVatID_ie_old($vat_id) 
	{
		// in neue form umwandeln
		$transform = array (substr($vat_id, 0, 2), '0', substr($vat_id, 4, 5), $vat_id[2], $vat_id[9]);
		$vat_id = join('', $transform);

		// nach neuer form pruefen
		return checkVatID_ie_new($vat_id);
	} // end irland alte methode

	// irland neue methode
	function checkVatID_ie_new($vat_id) 
	{
		$checksum = strtoupper(substr($vat_id, -1));
		$checkval = 0;
		$checkchar = 'A';
		for ($i = 2; $i <= 8; $i ++)
			$checkval += (int) $vat_id[10 - $i] * $i;
		$checkval = $this->modulo($checkval, 23);
		if ($checkval == 0) 
		{
			$checkchar = 'W';
		} 
		else 
		{
			for ($i = $checkval -1; $i > 0; $i --)
				$checkchar ++;
		}
		if ($checkchar != $checksum)
			return false;

		return true;
	} // end irland neue methode
	/* end irland
	********************************************/

	// italien
	function checkVatID_it($vat_id) 
	{
		if (strlen($vat_id) != 13)
			return 0;

		if ($this->live_check = true) 
		{
			return $this->live($vat_id);
		} 
		else 
		{
			return 9; // es gibt keinen algorithmus
		}
	} // end italien

	// lettland
	function checkVatID_lv($vat_id) 
	{
		if (strlen($vat_id) != 13)
			return 0;
		if (!is_numeric(substr($vat_id, 2)))
			return 0;

		if ($this->live_check = true) 
		{
			return $this->live($vat_id);

		} 
		else 
		{
			return 9; // es gibt keinen algorithmus
		}
	} // end lettland

	// litauen
	function checkVatID_lt($vat_id) 
	{
		if ((strlen($vat_id) != 13) || (strlen($vat_id) != 11))
			return 0;
		if (!is_numeric(substr($vat_id, 2)))
			return 0;

		if ($this->live_check = true) 
		{
			return $this->live($vat_id);
		} 
		else 
		{
			return 9; // es gibt keinen algorithmus
		}
	} // end litauen

	// luxemburg
	function checkVatID_lu($vat_id)
	{
		if (strlen($vat_id) != 10)
			return 0;

		$checksum = (int) substr($vat_id, -2);
		$checkval = (int) substr($vat_id, 2, 6);
		if ($this->modulo($checkval, 89) != $checksum)
			return 0;

		return 1;
	} // luxemburg

	// malta
	function checkVatID_mt($vat_id)
	{

		if (strlen($vat_id) != 10)
			return 0;
		if (!is_numeric(substr($vat_id, 2)))
			return 0;

		if ($this->live_check = true) 
		{
			return $this->live($vat_id);
		} 
		else 
		{
			return 9; // es gibt keinen algorithmus
		}
	} // end malta

	// niederlande
	function checkVatID_nl($vat_id) 
	{
		if (strlen($vat_id) != 14)
			return 0;
		if (strtoupper($vat_id[11]) != 'B')
			return 0;
		if ((int) $vat_id[12] == 0 || (int) $vat_id[13] == 0)
			return 0;

		$checksum = (int) $vat_id[10];
		$checkval = 0;

		for ($i = 2; $i <= 9; $i ++)
			$checkval += (int) $vat_id[11 - $i] * $i;
		$checkval = $this->modulo($checkval, 11) > 9 ? 0 : $this->modulo($checkval, 11);

		if ($checkval != $checksum)
			return 0;

		return 1;
	} // end niederlande

	// oesterreich
	function checkVatID_at($vat_id) 
	{
		if (strlen($vat_id) != 11)
			return 0;
		if (strtoupper($vat_id[2]) != 'U')
			return 0;

		$checksum = (int) $vat_id[10];
		$checkval = 0;

		for ($i = 3; $i < 10; $i ++)
			$checkval += $this->cross_summa((int) $vat_id[$i] * ($this->is_even($i) ? 2 : 1));
		$checkval = substr((string) (96 - $checkval), -1);

		if ($checksum != $checkval)
			return 0;

		return 1;
	} // end oesterreich

	// polen
	function checkVatID_pl($vat_id) 
	{
		if (strlen($vat_id) != 12)
			return 0;

		$weights = array (6, 5, 7, 2, 3, 4, 5, 6, 7);
		$checksum = (int) $vat_id[11];
		$checkval = 0;
		for ($i = 0; $i < count($weights); $i ++)
			$checkval += (int) $vat_id[$i +2] * $weights[$i];
		$checkval = $this->modulo($checkval, 11);

		if ($checkval != $checksum)
			return 0;

		return 1;
	} // end polen

	// portugal
	function checkVatID_pt($vat_id) 
	{
		if (strlen($vat_id) != 11)
			return 0;

		$checksum = (int) $vat_id[10];
		$checkval = 0;

		for ($i = 2; $i < 10; $i ++) 
		{
			$checkval += (int) $vat_id[11 - $i] * $i;
		}
		$checkval = (11 - $this->modulo($checkval, 11)) > 9 ? 0 : (11 - $this->modulo($checkval, 11));
		if ($checksum != $checkval)
			return 0;

		return 1;
	} // end portugal

	// schweden
	function checkVatID_se($vat_id) {
		if (strlen($vat_id) != 14)
			return 0;
		if ((int) substr($vat_id, -2) < 1 || (int) substr($vat_id, -2) > 94)
			return 0;
		$checksum = (int) $vat_id[11];
		$checkval = 0;

		for ($i = 0; $i < 10; $i ++)
			$checkval += $this->cross_summa((int) $vat_id[10 - $i] * ($this->is_even($i) ? 2 : 1));
		if ($checksum != ($this->modulo($checkval, 10) == 0 ? 0 : 10 - $this->modulo($checkval, 10)))
			return 0;

		$checkval = 0;
		for ($i = 0; $i < 13; $i ++)
			$checkval += (int) $vat_id[13 - $i] * ($this->is_even($i) ? 2 : 1);
		if ($this->modulo($checkval, 10) > 0)
			return 0;

		return 1;
	} // end schweden

	// slowakische republik
	function checkVatID_sk($vat_id) {
		if (strlen($vat_id) != 12)
			return 0;
		if (!is_numeric(substr($vat_id, 2)))
			return 0;

		if ($this->live_check = true) {

			return $this->live($vat_id);

		} else {
			return 9; // es gibt keinen algorithmus
		}

	} // end slowakische republik

	// slowenien
	function checkVatID_si($vat_id) {
		if (strlen($vat_id) != 10)
			return 0;
		if ((int) $vat_id[2] == 0)
			return 0;

		$checksum = (int) $vat_id[9];
		$checkval = 0;

		for ($i = 2; $i <= 8; $i ++)
			$checkval += (int) $vat_id[10 - $i] * $i;
		$checkval = $this->modulo($checkval, 11) == 10 ? 0 : 11 - $this->modulo($checkval, 11);
		if ($checksum != $checkval)
			return 0;

		return 1;
	} // end slowenien

	// spanien
	function checkVatID_es($vat_id) {
		// Trim country info
		$vat_id = substr($vat_id, 2);

		// Is it a naturalized foreigner?
		if (strtoupper($vat_id[0]) == 'X')
			$vat_id = substr($vat_id, 1); // Truncated $vat_id is validated as a regular one

		// Length check 
		if (strlen($vat_id) > 9) // $vat_id at this point should be 9 chars at most



			return 0;

		// Is it a company?
		if (!is_numeric($vat_id[0])) {
			$allowed = array ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
			$checkval = false;

			for ($i = 0; $i < count($allowed); $i ++) {
				if (strtoupper($vat_id[0]) == $allowed[$i])
					$checkval = true;
			} // end for($i=0; $i<count($allowed); $i++)
			if (!$checkval)
				return 9; // Few more letters are allowed, but not likely to happen

			$vat_len1 = strlen($vat_id) - 1;

			$checksum = (int) $vat_id[$vat_len1];
			$checkval = 0;

			for ($i = 1; $i < $vat_len1; $i ++)
				$checkval += $this->cross_summa((int) $vat_id[$i] * ($this->is_even($i) ? 1 : 2));

			if ($checksum != 10 - $this->modulo($checkval, 10))
				return 0;

			return 1;
		} // end Is it a company?

		// Is it an Individual? (or naturalized foreigner)
		if (!is_numeric($vat_id[strlen($vat_id) - 1])) {
			$allowed1 = "TRWAGMYFPDXBNJZSQVHLCKE";

			$vat_len1 = strlen($vat_id) - 1;

			$checksum = strtoupper($vat_id[$vat_len1]);
			$checkval = $this->modulo((int) substr($vat_id, 0, $vat_len1), 23);

			if ($checksum != $allowed1[$checkval])
				return 0;

			$this->vat_mod = array ('status' => $allowed1[$checkval]);

			return 1;
		} // end Is it an Individual?

		return 0; // No match found
	} // end spanien

	// tschechien
	function checkVatID_cz($vat_id) {

		if ((strlen($vat_id) != 10) || (strlen($vat_id) != 11) || (strlen($vat_id) != 12))
			return 0;
		if (!is_numeric(substr($vat_id, 2)))
			return 0;

		if ($this->live_check = true) {

			return $this->live($vat_id);

		} else {
			return 9; // es gibt keinen algorithmus
		}
	} // end tschechien

	// ungarn
	function checkVatID_hu($vat_id) {

		if (strlen($vat_id) != 10)
			return 0;
		if (!is_numeric(substr($vat_id, 2)))
			return 0;

		if ($this->live_check = true) {

			return $this->live($vat_id);

		} else {
			return 9; // es gibt keinen algorithmus
		}
	} // end ungarn

	// zypern
	function checkVatID_cy($vat_id) {

		if (strlen($vat_id) != 11)
			return 0;

		if ($this->live_check = true) {

			return $this->live($vat_id);

		} else {
			return 9; // es gibt keinen algorithmus
		}
	} // end zypern

	/*******************************************************************/

	/********************************************************************
	* mathematische Hilfsfunktionen                                     *
	********************************************************************/
	// modulo berechnet den rest einer division von $val durch $param
	function modulo($val, $param) {
		return $val - (floor($val / $param) * $param);
	} // end function modulo($val, $param)

	// stellt fest, ob eine zahl gerade ist
	function is_even($val) {
		return ($val / 2 == floor($val / 2)) ? true : false;
	} // end function is_even($val)

	// errechnet die quersumme von $val
	function cross_summa($val) {
		$val = (string) $val;
		$sum = 0;
		for ($i = 0; $i < strlen($val); $i ++)
			$sum += (int) $val[$i];
		return $sum;
	} // end function cross_summa((string) $val)
	/*******************************************************************/

	/********************************************************************
	* Live Check                                     *
	********************************************************************/
	// Live Check ьberprьft die USTid beim Bundesamt fьr Finanzen
	function live($abfrage_nummer) {

		$eigene_nummer = STORE_OWNER_VAT_ID;

		/* Hier wird der String fьr den POST per URL aufgebaut */
		$ustid_post = "eigene_id=".$eigene_nummer."&abfrage_id=".$abfrage_nummer."";

		/* Zur Verbindung mit dem Server wird CURL verwendet */
		/* mit curl_init wird zunдchst die URL festgelegt */

		$ch = curl_init("http://wddx.bff-online.de//ustid.php?".$ustid_post."");

		/* Hier werden noch einige Parameter fьr CURL gesetzt */
		curl_setopt($ch, CURLOPT_HEADER, 0); /* Header nicht in die Ausgabe */
		curl_setopt($ch, CURLOPT_NOBODY, 0); /* Ausgabe nicht in die HTML-Seite */
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); /* Umleitung der Ausgabe in eine Variable ermцglichen */

		/* Aufruf von CURL und Ausgabe mit WDDX deserialisieren */

		$des_out = wddx_deserialize(curl_exec($ch));
		curl_close($ch);

		/* Die deserialisierte Ausgabe in ein Array schreiben */

		while (list ($key, $val) = each($des_out)) {
			$ergebnis[$key] = $val;
		}

		if ($ergebnis[fehler_code] == '200') {
			return 1;
		}
		elseif ($ergebnis[fehler_code] == '201') {
			return 0;
		}
		elseif ($ergebnis[fehler_code] == '202') {
			return 0;
		}
		elseif ($ergebnis[fehler_code] == '203') {
			return 0;
		}
		elseif ($ergebnis[fehler_code] == '204') {
			return 0;
		}
		elseif ($ergebnis[fehler_code] == '205') {
			return 9;
		}
		elseif ($ergebnis[fehler_code] == '206') {
			return 9;
		}
		elseif ($ergebnis[fehler_code] == '207') {
			return 9;
		}
		elseif ($ergebnis[fehler_code] == '208') {
			return 9;
		}
		elseif ($ergebnis[fehler_code] == '209') {
			return 0;
		}
		elseif ($ergebnis[fehler_code] == '210') {
			return 0;
		}
		elseif ($ergebnis[fehler_code] == '666') {
			return 9;
		}
		elseif ($ergebnis[fehler_code] == '777') {
			return 9;
		}
		elseif ($ergebnis[fehler_code] == '888') {
			return 9;
		}
		elseif ($ergebnis[fehler_code] == '999') {
			return 9;
		} else {
			return 9;
		}

	} // end function Live
	/*******************************************************************/
}
?>