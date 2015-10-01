<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

require('includes/top.php');

// utf8cp1251 and cp1251toutf8 functions
function Utf8ToWin($fcontents)
{
	if (function_exists('iconv'))
	{
		return iconv('UTF-8', 'CP1251', $fcontents); 
	}
	else
	{
		$out = $c1 = '';
		$byte2 = false;
		for ($c = 0;$c < strlen($fcontents);$c++)
		{
			$i = ord($fcontents[$c]);
			if ($i <= 127)
			{
				$out .= $fcontents[$c];
			}
			if ($byte2)
			{
				$new_c2 = ($c1 & 3) * 64 + ($i & 63);
				$new_c1 = ($c1 >> 2) & 5;
				$new_i = $new_c1 * 256 + $new_c2;
				if ($new_i == 1025)
				{
					$out_i = 168;
				}
				else
				{
					if ($new_i == 1105)
						$out_i = 184;
					else
						$out_i = $new_i - 848;
				}
				$out .= chr($out_i);
				$byte2 = false;
			}
			if (($i >> 5) == 6)
			{
				$c1 = $i;
				$byte2 = true;
			}
		}
		return $out;
	}
}

function CP1251toUTF8($str)
{
	if (function_exists('iconv'))
	{
		return iconv('CP1251', 'UTF-8', $str); 
	}
	else
	{
		static $table = array(
			"\xA8" => "\xD0\x81", 
			"\xB8" => "\xD1\x91", 
			// украинские символы
			"\xA1" => "\xD0\x8E", 
			"\xA2" => "\xD1\x9E", 
			"\xAA" => "\xD0\x84", 
			"\xAF" => "\xD0\x87", 
			"\xB2" => "\xD0\x86", 
			"\xB3" => "\xD1\x96", 
			"\xBA" => "\xD1\x94", 
			"\xBF" => "\xD1\x97", 
			// чувашские символы
			"\x8C" => "\xD3\x90", 
			"\x8D" => "\xD3\x96", 
			"\x8E" => "\xD2\xAA", 
			"\x8F" => "\xD3\xB2", 
			"\x9C" => "\xD3\x91", 
			"\x9D" => "\xD3\x97", 
			"\x9E" => "\xD2\xAB", 
			"\x9F" => "\xD3\xB3", 
		);
		return preg_replace('#[\x80-\xFF]#se', ' "$0" >= "\xF0" ? "\xD1".chr(ord("$0")-0x70) : ("$0" >= "\xC0" ? "\xD0".chr(ord("$0")-0x30) : (isset($table["$0"]) ? $table["$0"] : ""))', $str);
	}
}

function prepare_image($image)
{
	$products_image_name = os_db_prepare_input($image);

	if (!is_file(dir_path('images_original').$products_image_name))
		return false;

	require_once(get_path('class_admin').FILENAME_IMAGEMANIPULATOR);
	require(get_path('includes_admin').'product_thumbnail_images.php');
	require(get_path('includes_admin').'product_info_images.php');
	require(get_path('includes_admin').'product_popup_images.php');
	return $products_image_name;
}

// Current EP Version
$curver = ' 2.75';

require('epconfigure.php');

// fix by jb 20040815 set the strings to http post/request, since they don't seem to work on the new server, with register globals=off...
$dltype = @ $_REQUEST['dltype'];
$download = @ $_REQUEST['download'];

global $_FILES;

foreach( $_FILES as $varname => $fileinfo )
{
	$GLOBALS[$varname] = $fileinfo["tmp_name"];
	$GLOBALS[$varname.'_name'] = $fileinfo["name"];
}
// end fix jb

//*******************************
//*******************************
// S T A R T
// INITIALIZATION
//*******************************
//*******************************

// VJ product attributes begin
global $attribute_options_array;
$attribute_options_array = array();

if ($products_with_attributes == true)
{
	if (is_array($attribute_options_select) && (count($attribute_options_select) > 0))
	{
		foreach ($attribute_options_select as $value)
		{
			$attribute_options_values = os_db_query("select distinct products_options_id from ".TABLE_PRODUCTS_OPTIONS." where products_options_name = '".$value."'");
			if ($attribute_options = os_db_fetch_array($attribute_options_values))
			{
				$attribute_options_array[] = array('products_options_id' => $attribute_options['products_options_id']);
			}
		}
	}
	else
	{
		$attribute_options_values = os_db_query("select distinct products_options_id from ".TABLE_PRODUCTS_OPTIONS." order by products_options_id");
		while ($attribute_options = os_db_fetch_array($attribute_options_values))
		{
			$attribute_options_array[] = array('products_options_id' => $attribute_options['products_options_id']);
		}
	}
}
// VJ product attributes end

global $filelayout, $filelayout_count, $filelayout_sql, $langcode, $fileheaders;

//product range

// these are the fields that will be defaulted to the current values in the database
// if they are not found in the incoming file
global $default_these;
$default_these = array(
	'v_products_image',
	'v_categories_id',
	'v_products_price',
	'v_price_currency_code',
	'v_products_quantity',
	'v_products_weight',
	'v_date_avail',
	'v_instock',
	'v_tax_class_title',
	'v_manufacturers_name',
	'v_manufacturers_id'
);

//elari check default language_id from configuration table DEFAULT_LANGUAGE
$epdlanguage_query = os_db_query("select languages_id, name, code from ".TABLE_LANGUAGES." where code = '".DEFAULT_LANGUAGE."'");
if (os_db_num_rows($epdlanguage_query))
{
	$epdlanguage = os_db_fetch_array($epdlanguage_query);
	$epdlanguage_id   = $epdlanguage['languages_id'];
	$epdlanguage_name = $epdlanguage['name'];
	$epdlanguage_code = $epdlanguage['code'];
}
else
	echo 'Strange but there is no default language to work... That may not happen, just in case... ';

$langcode = ep_get_languages();

if ( $dltype != '' )
{
	// if dltype is set, then create the filelayout.  Otherwise it gets read from the
	// uploaded file
	ep_create_filelayout($dltype); // get the right filelayout for this download
}

//*******************************
//*******************************
// E N D
// INITIALIZATION
//*******************************
//*******************************


if (@$_POST['download'] == 'stream' or @$_POST['download'] == 'tempfile')
{
	//*******************************
	//*******************************
	// DOWNLOAD FILE
	//*******************************
	//*******************************
	$filestring = ""; // this holds the csv file we want to download

	$result = os_db_query($filelayout_sql);
	$row =  os_db_fetch_array($result);

	// Here we need to allow for the mapping of internal field names to external field names
	// default to all headers named like the internal ones
	// the field mapping array only needs to cover those fields that need to have their name changed
	if ( count($fileheaders) != 0 )
		$filelayout_header = $fileheaders; // if they gave us fileheaders for the dl, then use them
	else
		$filelayout_header = $filelayout; // if no mapping was spec'd use the internal field names for header names

	//We prepare the table heading with layout values
	foreach( $filelayout_header as $key => $value )
	{
		$filestring .= $key.$separator;
	}

	// now lop off the trailing tab
	$filestring = substr($filestring, 0, strlen($filestring)-1);

	// set the type
	if ($dltype == 'froogle')
		$endofrow = "\n";
	else
		$endofrow = $separator.'EOREOR'."\n"; // default to normal end of row

	$filestring .= $endofrow;

	$num_of_langs = count($langcode);

	while ($row)
	{
		// if the filelayout says we need a products_name, get it
		// build the long full froogle image path
		@$row['v_products_fullpath_image'] = $froogle_image_path.$row['v_products_image'];
		// Other froogle defaults go here for now
		@$row['v_froogle_instock']     = 'Y';
		@$row['v_froogle_shipping']    = '';
		@$row['v_froogle_upc']       = '';
		@$row['v_froogle_color']     = '';
		@$row['v_froogle_size']      = '';
		@$row['v_froogle_quantitylevel']   = '';
		@$row['v_froogle_manufacturer_id'] = '';
		@$row['v_froogle_exp_date']    = '';
		@$row['v_froogle_product_type']    = 'OTHER';
		@$row['v_froogle_delete']    = '';
		@$row['v_froogle_currency']    = 'USD';
		@$row['v_froogle_offer_id']    = $row['v_products_model'];
		@$row['v_froogle_product_id']    = $row['v_products_model'];

		// names and descriptions require that we loop thru all languages that are turned on in the store
		foreach ($langcode as $key => $lang)
		{
			$lid = $lang['id'];
			$lcd = $lang['code'];

			// for each language, get the description and set the vals
			$sql2 = "SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id = ".$row['v_products_id']." AND language_id = '".$lid."'";
			$result2 = os_db_query($sql2);
			$row2 =  os_db_fetch_array($result2);

			//added cpath
			// for the categories, we need to keep looping until we find the root category
			// start with v_categories_id
			// Get the category description
			// set the appropriate variable name
			// if parent_id is not null, then follow it up.
			// we'll populate an aray first, then decide where it goes in the
			$thecategory_id1 = @$row['v_categories_id'];
			$fullcategory1 = ''; // this will have the entire category stack for froogle
			for($categorylevel=1; $categorylevel<$max_categories+1; $categorylevel++)
			{
				if ($thecategory_id1)
				{
					// now get the parent ID if there was one
					$result23 = os_db_query("SELECT parent_id FROM ".TABLE_CATEGORIES." WHERE categories_id = ".$thecategory_id1."");
					$row23 =  os_db_fetch_array($result23);
					$theparent_id1 = $row23['parent_id'];
				}
				$cPath = @$theparent_id1. '_' .@$row['v_categories_id'];
			}

			// I'm only doing this for the first language, since right now froogle is US only.. Fix later!
			// adding url for froogle, but it should be available no matter what
			if ($froogle_SEF_urls)
			{
				// if only one language
				if ($num_of_langs == 1)
					$row['v_froogle_products_url_'.$lid] = $froogle_product_info_path.'?cPath=' . $cPath.'&products_id='.$row['v_products_id'];
				else
					$row['v_froogle_products_url_'.$lid] = $froogle_product_info_path.'?cPath=' . $cPath.'&products_id='.$row['v_products_id'].'&language='.$epdlanguage_code;
			}
			else
			{
				if ($num_of_langs == 1)
					$row['v_froogle_products_url_'.$lid] = $froogle_product_info_path.'?cPath=' . $cPath.'&products_id='.$row['v_products_id'];
				else
					$row['v_froogle_products_url_'.$lid] = $froogle_product_info_path.'?cPath=' . $cPath.'&products_id='.$row['v_products_id'].'&language='.$epdlanguage_code;
			}

			$row['v_products_name_'.$lid] = $row2['products_name'];
			$row['v_products_description_'.$lid] = $row2['products_description'];
			$row['v_products_short_description_'.$lid] = $row2['products_short_description'];
			$row['v_products_keywords_'.$lid] = $row2['products_keywords'];
			$row['v_products_url_'.$lid] = $row2['products_url'];

			// froogle advanced format needs the quotes around the name and desc
			$row['v_froogle_products_name_'.$lid] = '"'.strip_tags(str_replace('"','""',$row2['products_name'])).'"';
			$row['v_froogle_products_description_'.$lid] = '"'.strip_tags(str_replace('"','""',$row2['products_description'])).'"';

			// support for Linda's Header Controller 2.0 here
			if(isset($filelayout['v_products_meta_title_'.$lid]))
			{
				$row['v_products_meta_title_'.$lid]   = $row2['products_meta_title'];
				$row['v_products_meta_description_'.$lid]   = $row2['products_meta_description'];
				$row['v_products_meta_keywords_'.$lid]  = $row2['products_meta_keywords'];
			}
			// end support for Header Controller 2.0
		}

		// for the categories, we need to keep looping until we find the root category

		// start with v_categories_id
		// Get the category description
		// set the appropriate variable name
		// if parent_id is not null, then follow it up.
		// we'll populate an aray first, then decide where it goes in the
		$thecategory_id = @$row['v_categories_id'];
		$fullcategory = ''; // this will have the entire category stack for froogle
		for($categorylevel=1; $categorylevel<$max_categories+1; $categorylevel++)
		{
			if ($thecategory_id)
			{
				$result2 = os_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id = ".$thecategory_id." AND language_id = ".$epdlanguage_id."");
				$row2 =  os_db_fetch_array($result2);
				// only set it if we found something
				$temprow['v_categories_name_'.$categorylevel] = $row2['categories_name'];
				// now get the parent ID if there was one
				$result3 = os_db_query("SELECT parent_id FROM ".TABLE_CATEGORIES." WHERE categories_id = ".$thecategory_id."");
				$row3 =  os_db_fetch_array($result3);
				$theparent_id = $row3['parent_id'];

				if ($theparent_id != '')
					$thecategory_id = $theparent_id; // there was a parent ID, lets set thecategoryid to get the next level
				else
					$thecategory_id = false; // we have found the top level category for this item,
		
				//$fullcategory .= " > ".$row2['categories_name'];
				$fullcategory = $row2['categories_name']." > ".$fullcategory;
			}
			else
				$temprow['v_categories_name_'.$categorylevel] = '';
		}

		// now trim off the last ">" from the category stack
		$row['v_category_fullpath'] = substr($fullcategory,0,strlen($fullcategory)-3);

		// temprow has the old style low to high level categories.
		$newlevel = 1;
		// let's turn them into high to low level categories
		for( $categorylevel=6; $categorylevel>0; $categorylevel--)
		{
			if ($temprow['v_categories_name_'.$categorylevel] != '')
			{
				$row['v_categories_name_'.$newlevel++] = $temprow['v_categories_name_'.$categorylevel];
			}
		}
		// if the filelayout says we need a manufacturers name, get it
		if (isset($filelayout['v_manufacturers_name']))
		{
			if ($row['v_manufacturers_id'] != '')
			{
				$result2 = os_db_query("SELECT manufacturers_name FROM ".TABLE_MANUFACTURERS." WHERE manufacturers_id = ".$row['v_manufacturers_id']."");
				$row2 =  os_db_fetch_array($result2);
				$row['v_manufacturers_name'] = $row2['manufacturers_name'];
			}
		}
		// If you have other modules that need to be available, put them here

		// VJ product attribs begin
		if (isset($filelayout['v_attribute_options_id_1']))
		{
			$languages = os_get_languages();

			$attribute_options_count = 1;
			foreach ($attribute_options_array as $attribute_options)
			{
				$row['v_attribute_options_id_'.$attribute_options_count]  = $attribute_options['products_options_id'];

				for ($i=0, $n=sizeof($languages); $i<$n; $i++)
				{
					$lid = $languages[$i]['id'];

					$attribute_options_languages_values = os_db_query("select products_options_name from ".TABLE_PRODUCTS_OPTIONS." where products_options_id = '".(int)$attribute_options['products_options_id']."' and language_id = '".(int)$lid."'");
					$attribute_options_languages = os_db_fetch_array($attribute_options_languages_values);
					$row['v_attribute_options_name_'.$attribute_options_count.'_'.$lid] = $attribute_options_languages['products_options_name'];
				}

				$attribute_values_values = os_db_query("select products_options_values_id from ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." where products_options_id = '".(int)$attribute_options['products_options_id']."' order by products_options_values_id");

				$attribute_values_count = 1;
				while ($attribute_values = os_db_fetch_array($attribute_values_values))
				{
					$row['v_attribute_values_id_'.$attribute_options_count.'_'.$attribute_values_count] = $attribute_values['products_options_values_id'];

					$attribute_values_price_values = os_db_query("select options_values_price, price_prefix from ".TABLE_PRODUCTS_ATTRIBUTES." where products_id = '".(int)$row['v_products_id']."' and options_id = '".(int)$attribute_options['products_options_id']."' and options_values_id = '".(int)$attribute_values['products_options_values_id']."'");
					$attribute_values_price = os_db_fetch_array($attribute_values_price_values);
					$row['v_attribute_values_price_'.$attribute_options_count.'_'.$attribute_values_count]  = $attribute_values_price['price_prefix'].$attribute_values_price['options_values_price'];

					for ($i=0, $n=sizeof($languages); $i<$n; $i++)
					{
						$lid = $languages[$i]['id'];
						$attribute_values_languages_values = os_db_query("select products_options_values_name from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".(int)$attribute_values['products_options_values_id']."' and language_id = '".(int)$lid."'");
						$attribute_values_languages = os_db_fetch_array($attribute_values_languages_values);
						$row['v_attribute_values_name_'.$attribute_options_count.'_'.$attribute_values_count.'_'.$lid] = $attribute_values_languages['products_options_values_name'];
					}

					$attribute_values_count++;
				}

				$attribute_options_count++;
			}
		}
		// VJ product attribs end

		// this is for the separate price per customer module
		if (isset($filelayout['v_customer_price_1']))
		{
			$result2 = os_db_query("SELECT customers_group_price, customers_group_id FROM ".TABLE_PRODUCTS_GROUPS." WHERE products_id = ".$row['v_products_id']." ORDER BY customers_group_id");
			$ll = 1;
			$row2 =  os_db_fetch_array($result2);
			while( $row2 )
			{
				$row['v_customer_group_id_'.$ll]  = $row2['customers_group_id'];
				$row['v_customer_price_'.$ll]   = $row2['customers_group_price'];
				$row2 = os_db_fetch_array($result2);
				$ll++;
			}
		}

		if ($dltype == 'froogle')
		{
			// For froogle, we check the specials prices for any applicable specials, and use that price
			// by grabbing the specials id descending, we always get the most recently added special price
			// I'm checking status because I think you can turn off specials
			$result2 = os_db_query("SELECT specials_new_products_price FROM ".TABLE_SPECIALS." WHERE products_id = ".$row['v_products_id']." and status = 1 and expires_date < CURRENT_TIMESTAMP ORDER BY specials_id DESC");
			$ll = 1;
			$row2 = os_db_fetch_array($result2);
			if($row2)
			{
				// reset the products price to our special price if there is one for this product
				$row['v_products_price']  = $row2['specials_new_products_price'];
			}
		}

		//elari -
		//We check the value of tax class and title instead of the id
		//Then we add the tax to price if $price_with_tax is set to 1
		$row_tax_multiplier = os_get_tax_class_rate(@$row['v_tax_class_id']);
		$row['v_tax_class_title'] = os_get_tax_class_title(@$row['v_tax_class_id']);
		$row['v_products_price'] = @$row['v_products_price'] +
		(@$price_with_tax * round(@$row['v_products_price'] * @$row_tax_multiplier / 100,2));

		// Now set the status to a word the user specd in the config vars
		if (@$row['v_status'] == '1')
			@$row['v_status'] = $active;
		else
			@$row['v_status'] = $inactive;

		// BOF mo_image
		for ($i = 0; $i < MO_PICS; $i ++)
		{
			$row['v_mo_image_'.($i+1)] = "";
		}

		if ($mo_image = os_get_products_mo_images($row['v_products_id']))
		{
			// echo '<pre>';var_dump($mo_image);echo '</pre>';
			for($i=0, $n=sizeof($mo_image); $i<$n; $i++)
			{
				$row['v_mo_image_'.$mo_image[$i]["image_nr"]] = $mo_image[$i]["image_name"];
			}
		}
		// EOF mo_image

		// remove any bad things in the texts that could confuse EasyPopulate
		$therow = '';
		foreach($filelayout as $key => $value)
		{
			//echo "The field was $key<br>";
			$thetext = @$row[$key];

			// kill the carriage returns and tabs in the descriptions, they're killing me!
			$thetext = str_replace("\r",' ',$thetext);
			$thetext = str_replace("\n",' ',$thetext);
			$thetext = str_replace("\t",' ',$thetext);
			// and put the text into the output separated by tabs
			$therow .= $thetext.$separator;
		}

		// lop off the trailing tab, then append the end of row indicator
		$therow = substr($therow,0,strlen($therow)-1).$endofrow;

		$filestring .= $therow;
		// grab the next row from the db
		$row =  os_db_fetch_array($result);
	}

	#$EXPORT_TIME = time();
	$EXPORT_TIME = strftime('%Y%b%d-%H%I');
	if ($dltype == 'froogle')
		$EXPORT_TIME = "FroogleEP";
	else
		$EXPORT_TIME = "EPA";
	//end froogle

	if ($_POST['export_charset'] == 'cp1251')
	{
		$filestring = Utf8ToWin($filestring);
	}

	// now either stream it to them or put it in the temp directory
	if ($_POST['download'] == 'stream')
	{
		//*******************************
		// STREAM FILE
		//*******************************
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: attachment; filename=$EXPORT_TIME.txt");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $filestring;
		die();
	}
	else
	{
		//*******************************
		// PUT FILE IN TEMP DIR
		//*******************************
		$tmpfname = DIR_FS_DOCUMENT_ROOT.$tempdir."$EXPORT_TIME.txt";
		//unlink($tmpfname);
		$fp = fopen( $tmpfname, "w+");
		fwrite($fp, $filestring);
		fclose($fp);
		$messageStack->add_session(EASY_FILE_LOCATE.$tempdir. $EXPORT_TIME.".txt" , 'success');
		os_redirect(os_href_link(FILENAME_EASY_POPULATE));
		exit;
	}
} // *** END *** download section

$breadcrumb->add(HEADING_TITLE, FILENAME_EASY_POPULATE);

$main->head();
$main->top_menu();
?>

<?php
if (@$_POST['localfile'] or (is_uploaded_file(@$_FILES['usrfl']['tmp_name']) && @$_GET['split'] == 0))
{
	//*******************************
	//*******************************
	// UPLOAD AND INSERT FILE
	//*******************************
	//*******************************
	//  check files name for EPA
	//    if ( (strstr($_POST['localfile'], 'EPA')) or ( (strstr($_FILES['usrfl']['name'], 'EPA')) && $_GET['split']==0) )  {
	//     }else{
	//          echo EASY_ERROR_6. '<a href="'.os_href_link(FILENAME_EASYPOPULATE).'">'.EASY_ERROR_6a.'</a><br>';
	//   die();
	//      }

	if ($csv_upload = &os_try_upload('usrfl', DIR_FS_DOCUMENT_ROOT.$tempdir))
	{
		// move the file to where we can work with it

		echo '<p class=smallText>';
		echo EASY_UPLOAD_FILE.'<br>';
		echo EASY_UPLOAD_TEMP.$_FILES['usrfl']['tmp_name'].'<br>';
		echo EASY_UPLOAD_USER_FILE.$_FILES['usrfl']['name'].'<br>';
		echo EASY_SIZE.$_FILES['usrfl']['size'].'<br></p>';

		// get the entire file into an array
		$readed = file(DIR_FS_DOCUMENT_ROOT.$tempdir.$_FILES['usrfl']['name']);
	}

	if (@$_POST['localfile'])
	{
		//$attribute_options_values = os_db_query("select distinct products_options_id from ".TABLE_PRODUCTS_OPTIONS." order by products_options_id");
		$attribute_options_count = 1;
		//while ($attribute_options = os_db_fetch_array($attribute_options_values)){
		echo "<p class=smallText>";
		echo  EASY_FILENAME.$_POST['localfile'].'<br></p>';
		// get the entire file into an array
		$readed = file(DIR_FS_DOCUMENT_ROOT.$tempdir.$_POST['localfile']);
	}

	// now we string the entire thing together in case there were carriage returns in the data
	$newreaded = "";

	if ($_POST['import_charset'] == 'cp1251')
	{
		foreach ($readed as $read)
		{
			$newreaded .= CP1251toUTF8($read);
		}
	}
	else
	{
		foreach ($readed as $read)
		{
			$newreaded .= $read;
		}
	}

	// now newreaded has the entire file together without the carriage returns.
	// if for some reason excel put qoutes around our EOREOR, remove them then split into rows
	$newreaded = str_replace('"EOREOR"', 'EOREOR', $newreaded);
	$readed = explode($separator.'EOREOR',$newreaded);

	// Now we'll populate the filelayout based on the header row.
	$theheaders_array = explode( $separator, $readed[0] ); // explode the first row, it will be our filelayout
	//$pr_array = explode( $separator, $readed); // explode the first row, it will be our filelayout

	for ($i=1; $i<=count($readed); $i++)
	{
		@$pr_array[$i-1] = explode(@$separator, @$readed[$i]);
	}

	// Если привязка по model
	if (trim($theheaders_array[0]) == 'v_products_model' && trim($theheaders_array[1]) == 'v_products_price' && trim($theheaders_array[2]) == 'v_products_quantity')
		$os_walk = true;
	else
		$os_walk = false;

	$lll = 0;
	$filelayout = array();
	foreach($theheaders_array as $header)
	{
		$cleanheader = str_replace( '"', '', $header);
		// echo "Fileheader was $header<br><br><br>";
		$filelayout[ $cleanheader ] = $lll++; //
	}
	unset($readed[0]); //  we don't want to process the headers with the data

	// now we've got the array broken into parts by the expicit end-of-row marker.

	//array_walk($readed, 'walk');
	if ($os_walk != true)
	{
		foreach ($readed as $readed_record) 
		{
			walk($readed_record);
		}
	}
	else
		os_walk($pr_array);
}

function os_walk($pr_array)
{
	global $separator;

	foreach ($pr_array as $v_name)
	{
		$v_products_model = trim($v_name[0]);
		$v_products_price = trim($v_name[1]);
		$v_products_quantity = trim($v_name[2]);

		if (!empty($v_products_model))
		{
			$v_products_model = trim($v_products_model);
			//$sql = os_db_query('SELECT count(*) FROM '.DB_PREFIX."products WHERE products_model='".$v_products_model."'");

			//if (os_db_num_rows($sql) == 1);
			//{
			$sql = @os_db_query('update '.DB_PREFIX."products set products_quantity = '$v_products_quantity', products_price = '$v_products_price', products_model = '$v_products_model' where products_model = '$v_products_model'");
			// }
			echo " | $v_products_model | $v_products_price | $v_products_quantity | <font color=green> Товар обновлён</font><br>";
		}
	}

	//os_db_query('update '.DB_PREFIX.'products');
	//$v_products_model	=	$items[$filelayout['v_products_model']];
	//print($v_products_model).'<br>';
}

if (is_uploaded_file(@$_FILES['usrfl']['tmp_name']) && @$_GET['split'] == 1)
{
	//*******************************
	//*******************************
	// UPLOAD AND SPLIT FILE
	//*******************************
	//*******************************
	//  check files name for EPA

	//	if (strstr($_FILES['usrfl']['name'], 'EPA')){
	//	}else{
	//		echo EASY_ERROR_6.'<span class=smallText><a href="'.os_href_link(FILENAME_EASYPOPULATE).'">'.EASY_ERROR_6a.'</a></span><br>';
	//	 die();
	//	}

	// move the file to where we can work with it
	$csv_upload = &os_try_upload('usrfl', DIR_FS_DOCUMENT_ROOT.$tempdir);

	$infp = fopen(DIR_FS_DOCUMENT_ROOT.$tempdir.$_FILES['usrfl']['name'], "r");

	//toprow has the field headers
	$toprow = fgets($infp,32768);

	$filecount = 1;

	//	echo '<span class=smallText>'.EASY_LABEL_FILE_COUNT_1A.$filecount.'</span>';
	$tmpfname = DIR_FS_DOCUMENT_ROOT.$tempdir."EPA_Split".$filecount.".txt";
	$fp = fopen( $tmpfname, "w+");
	fwrite($fp, $toprow);

	$linecount = 0;
	$line = fgets($infp,32768);

	while ($line)
	{
		// walking the entire file one row at a time
		// but a line is not necessarily a complete row, we need to split on rows that have "EOREOR" at the end
		$line = str_replace('"EOREOR"', 'EOREOR', $line);
		fwrite($fp, $line);
		if (strpos($line, 'EOREOR'))
		{
			// we found the end of a line of data, store it
			$linecount++; // increment our line counter
			if ($linecount >= $maxrecs)
			{
				echo '<span class=smallText>'.EASY_LABEL_FILE_COUNT_1A.$filecount.EASY_LABEL_FILE_COUNT_2.EASY_LABEL_LINE_COUNT_1.$linecount.EASY_LABEL_LINE_COUNT_2.'<br></span>';
				$linecount = 0; // reset our line counter
				// close the existing file and open another;
				fclose($fp);
				// increment filecount
				$filecount++;
				$tmpfname = DIR_FS_DOCUMENT_ROOT.$tempdir."EPA_Split".$filecount.".txt";
				//Open next file name
				$fp = fopen( $tmpfname, "w+");
				fwrite($fp, $toprow);
			}
		}
		$line = fgets($infp,32768);
	}
	echo '<span class=smallText>'.EASY_LABEL_FILE_COUNT_1A.$filecount.EASY_LABEL_FILE_COUNT_2.EASY_LABEL_LINE_COUNT_1.$linecount.EASY_LABEL_LINE_COUNT_2.'<br><br></span>';
	fclose($fp);
	fclose($infp);

	echo '<span class=smallText>'.EASY_SPLIT_DOWN.'</span>';
}

?>


<hr>


<div class="row-fluid">
	<div class="span6">
		<h4><?php echo EASY_LABEL_IMPORT;?></h4>

		<form enctype="multipart/form-data" action="easypopulate.php?split=0" method="post">
			<div class="control-group">
				<label class="control-label" for=""><?PHP ECHO EASY_UPLOAD_EP_FILE;?></label>
				<div class="controls">
					<input name="usrfl" type="file" size="50">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for=""><?php echo EASY_LABEL_IMPORT_CHARSET;?></label>
				<div class="controls">
					<select name="import_charset">
						<option selected value ="cp1251" size="5">cp1251</option>
						<option value="utf8" size="5">utf8</option>
					</select>
				</div>
			</div>
			<button class="btn" type="submit" name="buttoninsert" value="<?PHP ECHO EASY_INSERT;?>"><?PHP ECHO EASY_INSERT;?></button>
		</form>

		<hr>

		<form enctype="multipart/form-data" action="easypopulate.php?split=1" method="post">
			<input TYPE="hidden" name="MAX_FILE_SIZE" value="1000000000">
			<div class="control-group">
				<label class="control-label" for=""><?php echo EASY_SPLIT_EP_FILE;?></label>
				<div class="controls">
					<input name="usrfl" type="file" size="50">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for=""><?php echo EASY_LABEL_IMPORT_CHARSET;?></label>
				<div class="controls">
					<select name="import_charset">
						<option selected value ="cp1251" size="5">cp1251</option>
						<option value="utf8" size="5">utf8</option>
					</select>
				</div>
			</div>
			<button class="btn" type="submit" name="buttonsplit" value="<?PHP ECHO EASY_SPLIT;?>"><?PHP ECHO EASY_SPLIT;?></button>
		</form>

		<hr>

		<form name="localfile_insert" enctype="multipart/form-data" action="easypopulate.php" method="post">
			<div class="control-group">
				<label class="control-label" for=""><?php echo sprintf(TEXT_IMPORT_TEMP, $tempdir); ?></label>
				<div class="controls">
					<?php
					$dir = dir(DIR_FS_CATALOG.$tempdir);
					$contents = array(array('id' => '', 'text' => TEXT_SELECT_ONE));
					while ($file = $dir->read())
					{
						if (($file != '.') && ($file != 'CVS') && ($file != '..'))
						{
							//$file_size = filesize(DIR_FS_CATALOG.$tempdir.$file);
							$contents[] = array('id' => $file, 'text' => $file);
						}
					}
					echo os_draw_pull_down_menu('localfile', @$contents, @$_POST['localfile']);
					?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for=""><?php echo EASY_LABEL_IMPORT_CHARSET;?></label>
				<div class="controls">
					<select name="import_charset">
						<option selected value ="cp1251" size="5">cp1251</option>
						<option value="utf8" size="5">utf8</option>
					</select>
				</div>
			</div>
			<button class="btn" type="submit" name="buttoninsert" value="<?php echo TEXT_INSERT_INTO_DB ; ?>"><?php echo TEXT_INSERT_INTO_DB ; ?></button>
		</form>
	</div>

	<div class="span6">
		<h4><?php echo EASY_LABEL_CREATE;?></h4>

		<form enctype="multipart/form-data" action="easypopulate.php?action=export" method="post">
			<div class="control-group">
				<label class="control-label" for=""><?php echo EASY_LABEL_EXPORT_CHARSET;?></label>
				<div class="controls">
					<select name="export_charset">
						<option selected value ="cp1251" size="5">cp1251</option>
						<option value="utf8" size="5">utf8</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for=""><?php echo EASY_LABEL_CREATE_SELECT;?></label>
				<div class="controls">
					<select name="download">
						<option selected value ="stream" size="10"><?php echo EASY_LABEL_DOWNLOAD.'<b> ';?>
						<option value="tempfile" size="10"><?php echo EASY_LABEL_CREATE_SAVE;?>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for=""><?php echo EASY_LABEL_SELECT_DOWN;?></label>
				<div class="controls">
					<?php $_dltype_array = array(); ?>
					<select name="dltype">
						<option selected value="full" size="10"><?php echo EASY_LABEL_COMPLETE //full;?>
						<option value="category" size="10"><?php echo EASY_LABEL_EP_MC //model category;?>
						<option value="froogle" size="10"><?php echo EASY_LABEL_EP_FROGGLE //froggle;?>
						<option value="attrib" size="10"><?php echo EASY_LABEL_EP_ATTRIB //attibutes;?>
						<option value="extra_field" size="10"><?php echo EASY_LABEL_EXTRA_FIELDS //product extra fields;?>
						<option value="params" size="10">Параметры</option>
						<option value="label_ep_1" size="10"><?php echo EASY_LABEL_EP_1 //ID товара, цена, количество.;?>
						<option value="label_ep_2" size="10"><?php echo EASY_LABEL_EP_2 //model, цена, количество.;?>
					</select>
				</div>
			</div>

			<h5><?php echo EASY_LIMIT ;?></h5>

			<div class="control-group">
				<label class="control-label" for=""><?php echo EASY_LABEL_LIMIT_CAT;?></label>
				<div class="controls">
					<?php
					$categories_query = os_db_query("select c.categories_id, cd.categories_name, c.parent_id, c.sort_order from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.parent_id = '0' and c.categories_id = cd.categories_id and cd.language_id = '".@$languages_id."' order by c.sort_order, cd.categories_name");
					$category = os_db_fetch_array($categories_query);
					echo os_draw_pull_down_menu('limit_cat', os_get_category_tree(), 0);
					?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for=""><?php echo EASY_LABEL_LIMIT_MAN;?></label>
				<div class="controls">
					<?php
					$manufacturers_array = array();
					$manufacturers_array[] = array('id' => '0', 'text' => PULL_DOWN_MANUFACTURES);

					$manufacturers_query = os_db_query("select manufacturers_id, manufacturers_name from ".TABLE_MANUFACTURERS." order by manufacturers_name");
					while ($manufacturers = os_db_fetch_array($manufacturers_query))
					{
						$manufacturers_array[] = array(
							'id' => $manufacturers['manufacturers_id'],
							'text' => $manufacturers['manufacturers_name']
						);
					}
					echo os_draw_pull_down_menu('limit_man', @$manufacturers_array, @$_POST['limit_man']);
					?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for=""><?php echo EASY_LABEL_PRODUCT_RANGE;?></label>
				<div class="controls">
					<?php echo EASY_LABEL_PRODUCT_BEGIN;?><INPUT TYPE="text" name="rangebegin" size="12">
					<?php echo EASY_LABEL_PRODUCT_END;?><INPUT TYPE="text" name="rangeend" size="12">
				</div>
			</div>

			<?php // below are the queries to do the counts
			//$totalrows = os_db_query("SELECT COUNT(*) FROM ".TABLE_PRODUCTS."");
			$first_query = os_db_query("SELECT products_id FROM ".TABLE_PRODUCTS."  ORDER BY products_id ASC LIMIT 1");
			while ($firstid = os_db_fetch_array($first_query))
			{
				$firstid1 =  $firstid['products_id'];
			}

			$first_query2 = os_db_query("SELECT products_id FROM ".TABLE_PRODUCTS."  ORDER BY products_id DESC LIMIT 1");
			while ($firstid2 = os_db_fetch_array($first_query2))
			{
				$firstid2a =  $firstid2['products_id'];
			}

			$first_query3 = os_db_query("SELECT products_id FROM ".TABLE_PRODUCTS." ");
			while ($firstid3 = os_db_fetch_array($first_query3))
			{
				@$total3 = @$total3 + 1;
			}
			?>
			<div class="alert alert-info">
				<?php echo EASY_LABEL_PRODUCT_AVAIL.EASY_LABEL_PRODUCT_FROM.$firstid1.EASY_LABEL_PRODUCT_TO.$firstid2a;?><br>
				<?php echo EASY_LABEL_PRODUCT_RECORDS.$total3; ?>
			</div>

			<button class="btn" type="submit" name="buttoninsert" value="<?php echo EASY_LABEL_PRODUCT_START;?>"><?php echo EASY_LABEL_PRODUCT_START;?></button>
		</form>
	</div>
</div>












<?php
function os_get_category_treea($parent_id , $spacing = '', $exclude = '', $category_id_array = '', $include_itself = true)
{
	global $languages_id;

	if (!is_array($category_id_array))
		$category_tree_array = array();

	if ((sizeof($category_id_array) < 1) && ($exclude != '0'))
		$category_id_array[] = array('id' => '0', 'text' => TEXT_TOP);

	if ($include_itself)
	{
		$category_query = os_db_query("select cd.categories_name from ".TABLE_CATEGORIES_DESCRIPTION." cd where cd.language_id = '".(int)$languages_id."' and cd.categories_id = '".(int)$parent_id."'");
		$category = os_db_fetch_array($category_query);
		$category_tree_arraya[] = array('id' => $parent_id, 'text' => $category['categories_name']);
	}

	$categories_query = os_db_query("select c.categories_id, cd.categories_name, c.parent_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and cd.language_id = '".(int)$languages_id."' and c.parent_id = '".(int)$parent_id."' order by c.sort_order, cd.categories_name");
	while ($categories = os_db_fetch_array($categories_query))
	{
		if ($exclude != $categories['categories_id']) $category_id_array[] = array('id' => $categories['categories_id']);
		$category_tree_array = os_get_category_treea($categories['categories_id'],  $exclude, $category_id_array);
	}

	return $category_id_array;
}

function ep_get_languages()
{
	$languages_query = os_db_query("select languages_id, code from ".TABLE_LANGUAGES." order by sort_order");
	// start array at one, the rest of the code expects it that way
	$ll =1;
	while ($ep_languages = os_db_fetch_array($languages_query))
	{
		//will be used to return language_id en language code to report in product_name_code instead of product_name_id
		$ep_languages_array[$ll++] = array(
			'id' => $ep_languages['languages_id'],
			'code' => $ep_languages['code']
		);
	}
	return $ep_languages_array;
}

function os_get_tax_class_rate($tax_class_id)
{
	$tax_multiplier = 0;
	$tax_query = os_db_query("select SUM(tax_rate) as tax_rate from ".TABLE_TAX_RATES." WHERE  tax_class_id = '".$tax_class_id."' GROUP BY tax_priority");
	if (os_db_num_rows($tax_query))
	{
		while ($tax = os_db_fetch_array($tax_query))
		{
			$tax_multiplier += $tax['tax_rate'];
		}
	}
	return $tax_multiplier;
}

function os_get_tax_title_class_id($tax_class_title)
{
	$classes_query = os_db_query("select tax_class_id from ".TABLE_TAX_CLASS." WHERE tax_class_title = '".$tax_class_title."'" );
	$tax_class_array = os_db_fetch_array($classes_query);
	$tax_class_id = $tax_class_array['tax_class_id'];
	return $tax_class_id;
}

function print_el($item2)
{
	echo " | ".substr(strip_tags($item2), 0, 10);
}

function print_el1($item2)
{
	echo sprintf("| %'.4s ", substr(strip_tags($item2), 0, 80));
}

function ep_create_filelayout($dltype)
{
	global $filelayout, $filelayout_count, $filelayout_sql, $langcode, $fileheaders, $max_categories, $rangebegin, $rangeend, $catsort, $catfilter, $BEGIN1, $BEEND1, $categories_range;
	// depending on the type of the download the user wanted, create a file layout for it.
	$fieldmap = array(); // default to no mapping to change internal field names to external.

	//category range to download
	switch($dltype)
	{
		// start EP for product extra field ============================= DEVSOFTVN - 10/20/2005 	
		case 'extra_field':
			$iii = 0;
			// uncomment the customer_price and customer_group to support multi-price per product contrib
			// Mofificata Davide Duca
			$filelayout = array(
				'v_products_id' => $iii++,
				'v_products_extra_fields_name' => $iii++, 
				'v_products_extra_fields_id' => $iii++,
				'v_products_extra_fields_value' => $iii++,
			);

			$filelayout_sql = "SELECT p.products_id as v_products_id, subc.products_extra_fields_id as v_products_extra_fields_id, subc.products_extra_fields_value as v_products_extra_fields_value, ptoc.products_extra_fields_name as v_products_extra_fields_name FROM ".TABLE_PRODUCTS." as p, ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." as subc, ".TABLE_PRODUCTS_EXTRA_FIELDS." as ptoc WHERE p.products_id = subc.products_id AND ptoc.products_extra_fields_id = subc.products_extra_fields_id";	
			// Fine modifica
		break;

		//импорт параметров
		case 'params':
			$iii = 0;
			// uncomment the customer_price and customer_group to support multi-price per product contrib
			// Mofificata Davide Duca
			$filelayout = array(
				'v_products_id'		=> $iii++,
				'v_products_extra_fields_name'		=> $iii++, 
				'v_products_extra_fields_id'		=> $iii++,
				'v_products_extra_fields_value'		=> $iii++,
			);

			$filelayout_sql = "SELECT p.products_id as v_products_id, subc.products_extra_fields_id as v_products_extra_fields_id, subc.products_extra_fields_value as v_products_extra_fields_value, ptoc.products_extra_fields_name as v_products_extra_fields_name FROM ".TABLE_PRODUCTS." as p, ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." as subc, ".TABLE_PRODUCTS_EXTRA_FIELDS." as ptoc WHERE p.products_id = subc.products_id AND ptoc.products_extra_fields_id = subc.products_extra_fields_id";	
			// Fine modifica
		break;

		// end of EP for extra field code ======= DEVSOFTVN================       
		case 'full':
		case 'category':
		case 'froogle':
			if ($_POST['limit_cat'] == '0')
			{}
			else
			{
				// for one level down
				$categories_range .= 'ptoc.categories_id =  \''.$_POST['limit_cat']. '\' and ';

				// for two levels down
				//  $catfeild=os_get_category_treea($_POST['limit_cat']);
				//          $categories_range .= "( ";
				//for ($i=0, $n=sizeof($catfeild); $i<$n; $i++) {
				//  $categories_range .= 'ptoc.categories_id = '."'" .os_output_string($catfeild[$i]['id']."' ");
				//if ($i<$n){
				// $categories_range .= ' or ';
				//         }
				//
				//    }
				//     $categories_range=substr_replace($categories_range, ')and ', -4);
			}
		break;

		case 'attrib':
			if ($_POST['limit_cat'] == '0')
			{}
			else
			{
				$catfeild=os_get_category_treea($_POST['limit_cat']);
				$categories_range .= "( ";
				for ($i=0, $n=sizeof($catfeild); $i<$n; $i++) {
				$categories_range .= 'ptoc.categories_id = '."'" .os_output_string($catfeild[$i]['id']."' ");
				if ($i<$n){
				$categories_range .= ' or ';
				}

				}
				$categories_range=substr_replace($categories_range, ')and ', -4);
			}

		case 'priceqty':
			if ($_POST['limit_cat'] == '0')
			{}
			else
			{
				$catfeild=os_get_category_treea($_POST['limit_cat']);
				$categories_range .= "( ";
				for ($i=0, $n=sizeof($catfeild); $i<$n; $i++)
				{
					$categories_range .= 'ptoc.categories_id = '."'" .os_output_string($catfeild[$i]['id']."' ");
					if ($i<$n)
					{
						$categories_range .= ' or ';
					}
				}
				$categories_range=substr_replace($categories_range, ')and ', -4);
			}
		break;
	}
	//manufactur range to download

	switch($dltype)
	{
		case 'full':
		case 'category':
		case 'froogle':
			if ($_POST['limit_man'] == '0')
			{}
			else
			{
				$limitman_sql = 'p.manufacturers_id = \''.$_POST['limit_man']. '\' and ';
			}
		case 'attrib':
		case 'priceqty':
		break;
	}

	//product range to download
	switch($dltype)
	{
		case 'full':
		case 'category':
		case 'froogle':
			if ($rangebegin != '')
			{
				$BEGIN1= 'p.products_id >= \''.$rangebegin.'\' and';
			}

			if ($rangeend != '')
			{
				$BEEND1= 'p.products_id <= \''.$rangeend.'\' and';
			}
		break;

		case 'attrib':
		case 'priceqty':
			if ($rangebegin != '')
			{
				$BEGIN1= 'p.products_id >= \''.$rangebegin.'\' and';
			}

			if ($rangeend != '')
			{
				$BEEND1= 'p.products_id <= \''.$rangeend.'\' ';
			}
		break;
	}

	//sort order by category,product, number, manufacture
	switch($dltype)
	{
		case 'full':
		case 'category':
		case 'froogle':
		case 'priceqty':
			if (($dltype== 'priceqty') || ($dltype== 'attrib'))
			{}
			else
			{
				$WHERE = 'WHERE';
			}

			if ($catsort == 'none')
			{
				$catfil= '';
			}

			if (($catsort == 'category') && ($dltype!= 'attrib'))
			{
				$catfil= 'ORDER BY subc.categories_id';
			}

			if($catsort == 'product')
			{
				$catfil= 'ORDER BY p.products_id';
			}

			if ($catsort == 'manufacture')
			{
				$catfil= 'ORDER BY p.manufacturers_id';
			}
		case 'priceqty':
		case 'attrib':
		break;
	}

	switch( $dltype )
	{
		case 'full':
			// The file layout is dynamically made depending on the number of languages
			$iii = 0;
			$filelayout = array(
				'v_products_id'   => $iii++,
				'v_products_model'    => $iii++,
				'v_products_page_url'    => $iii++,
				'v_products_image'    => $iii++
			);

			foreach ($langcode as $key => $lang)
			{
				$l_id = $lang['id'];
				// uncomment the head_title, head_desc, and head_keywords to use
				// Linda's Header Tag Controller 2.0
				//echo $langcode['id'].$langcode['code'];
				$filelayout  = array_merge($filelayout , array(
					'v_products_name_'.$l_id    => $iii++,
					//'v_products_description_'.$l_id => $iii++,
					'v_products_description_'.$l_id => (str_replace('"', '\"', $iii++)),
					'v_products_short_description_'.$l_id => $iii++,
					'v_products_keywords_'.$l_id  => $iii++,
					'v_products_url_'.$l_id => $iii++,
					'v_products_meta_title_'.$l_id  => $iii++,
					'v_products_meta_description_'.$l_id  => $iii++,
					'v_products_meta_keywords_'.$l_id => $iii++,
				));
			}

			// uncomment the customer_price and customer_group to support multi-price per product contrib

			// VJ product attribs begin
			$header_array = array(
				'v_products_price'    => $iii++,
				'v_price_currency_code'    => $iii++,
				'v_products_weight'   => $iii++,
				'v_date_avail'      => $iii++,
				'v_date_added'      => $iii++,
				'v_products_quantity'   => $iii++,
				//      'v_products_quantity_order_min'   => $iii++,
				//      'v_products_quantity_order_units'   => $iii++,
				'v_products_sort'   => $iii++,
			);

			$languages = os_get_languages();

			global $attribute_options_array;

			$header_array['v_manufacturers_name'] = $iii++;

			$filelayout = array_merge($filelayout, $header_array);
			// VJ product attribs end

			// build the categories name section of the array based on the number of categores the user wants to have
			for($i=1;$i<$max_categories+1;$i++)
			{
				$filelayout = array_merge($filelayout, array('v_categories_name_'.$i => $iii++));
			}

			$filelayout = array_merge($filelayout, array(
				'v_tax_class_title'   => $iii++,
				'v_status'      => $iii++,
				'v_action'      => $iii++,
			));

			$filelayout_sql = "
			SELECT
				p.products_id as v_products_id,
				p.products_model as v_products_model,
				p.products_image as v_products_image,
				p.products_page_url as v_products_page_url,
				p.products_price as v_products_price,
				p.price_currency_code as v_price_currency_code,
				p.products_weight as v_products_weight,
				p.products_date_available as v_date_avail,
				p.products_date_added as v_date_added,
				p.products_tax_class_id as v_tax_class_id,
				p.products_quantity as v_products_quantity,
				p.products_sort as v_products_sort,
				p.manufacturers_id as v_manufacturers_id,
				subc.categories_id as v_categories_id,
				p.products_status as v_status
			FROM
				".TABLE_PRODUCTS." as p,
				".TABLE_CATEGORIES." as subc,
				".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
			WHERE
				".@$categories_range."
				".@$limitman_sql."
				p.products_id = ptoc.products_id AND
				".@$BEGIN1."
				".@$BEEND1."
				ptoc.categories_id = subc.categories_id
				".@$catfil."
			";

			// BOF mo_image
			//		echo '<pre>';var_dump($fileheaders);echo '</pre>';
			for ($i = 0; $i < MO_PICS; $i ++)
			{
				$filelayout['v_mo_image_'.($i+1)] = $iii++;
			}
			// EOF mo_image
		break;

		case 'priceqty':
			$iii = 0;
			// uncomment the customer_price and customer_group to support multi-price per product contrib
			$filelayout = array(
				'v_products_id'   => $iii++,  //added
				'v_products_model'    => $iii++,
				'v_products_image'    => $iii++,
				'v_products_name_1'    => $iii++,
				'v_products_page_url'    => $iii++,
				'v_products_price'    => $iii++,
				'v_price_currency_code'    => $iii++,
				'v_products_quantity'   => $iii++,
				//      'v_products_quantity_order_min'   => $iii++,
				//      'v_products_quantity_order_units'   => $iii++,
				'v_products_sort'   => $iii++,
				#'v_customer_price_1'   => $iii++,
				#'v_customer_group_id_1'    => $iii++,
				#'v_customer_price_2'   => $iii++,
				#'v_customer_group_id_2'    => $iii++,
				#'v_customer_price_3'   => $iii++,
				#'v_customer_group_id_3'    => $iii++,
				#'v_customer_price_4'   => $iii++,
				#'v_customer_group_id_4'    => $iii++,
			);

			$filelayout_sql = "
			SELECT
				p.products_id as v_products_id,
				p.products_model as v_products_model,
				p.products_image as v_products_image,
				pd.products_name as v_products_name,
				p.products_page_url as v_products_page_url,
				p.products_price as v_products_price,
				p.price_currency_code as v_price_currency_code,
				p.products_quantity as v_products_quantity,
				p.products_sort as v_products_sort
			FROM
				".TABLE_PRODUCTS." as p left join ".TABLE_PRODUCTS_DESCRIPTION." as pd on pd.products_id = p.products_id 
				".$WHERE."
				".$categories_range."
				".$limitman_sql."
				".$BEGIN1."
				".$BEEND1."
				".$catfil."
			";
		break;

		case 'category':
			// The file layout is dynamically made depending on the number of languages
			$iii = 0;
			$filelayout = array(
				'v_products_id'   => $iii++,
				'v_products_model'    => $iii++,
				'v_products_page_url'    => $iii++,
			);

			// build the categories name section of the array based on the number of categores the user wants to have
			for($i=1;$i<$max_categories+1;$i++)
			{
				$filelayout = array_merge($filelayout, array('v_categories_name_'.$i => $iii++));
			}
			$filelayout = array_merge($filelayout, array('v_action' => $iii++));

			$filelayout_sql = "
			SELECT
				p.products_id as v_products_id,
				p.products_model as v_products_model,
				p.products_image as v_products_image,
				p.products_page_url as v_products_page_url,
				subc.categories_id as v_categories_id
			FROM
				".TABLE_PRODUCTS." as p,
				".TABLE_CATEGORIES." as subc,
				".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
			WHERE
				".$categories_range."
				".@$limitman_sql."
				p.products_id = ptoc.products_id AND
				".@$BEGIN1."
				".@$BEEND1."
				ptoc.categories_id = subc.categories_id
				".@$catfil."
			";
		break;

		case 'label_ep_1':
			// The file layout is dynamically made depending on the number of languages
			$iii = 0;
			$filelayout = array(
				'v_products_id'   => $iii++,
				'v_products_price'    => $iii++,
				'v_price_currency_code'    => $iii++,
				'v_products_quantity'    => $iii++,
			);

			$filelayout_sql = "
			SELECT 
				p.products_id as v_products_id,
				p.products_price as v_products_price,
				p.price_currency_code as v_price_currency_code,
				p.products_quantity as v_products_quantity
			FROM
				".TABLE_PRODUCTS." as p,
				".TABLE_CATEGORIES." as subc,
				".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
			WHERE
				".$categories_range."
				".@$limitman_sql."
				p.products_id = ptoc.products_id AND
				".@$BEGIN1."
				".@$BEEND1."
				ptoc.categories_id = subc.categories_id
				".@$catfil."
			";
		break;

		case 'label_ep_2':
			// The file layout is dynamically made depending on the number of languages
			$iii = 0;
			$filelayout = array(
				'v_products_model'   => $iii++,
				'v_products_price'    => $iii++,
				'v_price_currency_code'    => $iii++,
				'v_products_quantity'    => $iii++,
			);

			$filelayout_sql = "
			SELECT 
				p.products_id as v_products_id,
				p.products_model as v_products_model, 
				p.products_price as v_products_price,
				p.price_currency_code as v_price_currency_code,
				p.products_quantity as v_products_quantity
			FROM
				".TABLE_PRODUCTS." as p,
				".TABLE_CATEGORIES." as subc,
				".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
			WHERE
				".$categories_range."
				".@$limitman_sql."
				p.products_id = ptoc.products_id AND
				".@$BEGIN1."
				".@$BEEND1."
				ptoc.categories_id = subc.categories_id
				".@$catfil."
			";
		break;

		case 'froogle':
			// this is going to be a little interesting because we need
			// a way to map from internal names to external names
			//
			// Before it didn't matter, but with froogle needing particular headers,
			// The file layout is dynamically made depending on the number of languages
			$iii = 0;
			$filelayout = array(
				'v_froogle_products_url_1' => $iii++,
			);
			//
			// here we need to get the default language and put
			$l_id = 1; // dummy it in for now.
			$filelayout  = array_merge($filelayout , array(
				'v_froogle_products_name_'.$l_id => $iii++,
				'v_froogle_products_description_'.$l_id => $iii++,
			));

			$filelayout  = array_merge($filelayout , array(
				'v_products_price'    => $iii++,
				'v_price_currency_code'    => $iii++,
				'v_products_fullpath_image' => $iii++,
				'v_category_fullpath'   => $iii++,
				'v_froogle_offer_id'    => $iii++,
				'v_froogle_instock'   => $iii++,
				'v_froogle_ shipping'   => $iii++,
				'v_manufacturers_name'    => $iii++,
				'v_froogle_ upc'    => $iii++,
				'v_froogle_color'   => $iii++,
				'v_froogle_size'    => $iii++,
				'v_froogle_quantitylevel' => $iii++,
				'v_froogle_product_id'    => $iii++,
				'v_froogle_manufacturer_id' => $iii++,
				'v_froogle_exp_date'    => $iii++,
				'v_froogle_product_type'  => $iii++,
				'v_froogle_delete'    => $iii++,
				'v_froogle_currency'    => $iii++,
			));
			$iii=0;
			$fileheaders = array(
				'product_url'   => $iii++,
				'name'      => $iii++,
				'description'   => $iii++,
				'price'     => $iii++,
				'price_currency'     => $iii++,
				'image_url'   => $iii++,
				'category'    => $iii++,
				'offer_id'    => $iii++,
				'instock'   => $iii++,
				'shipping'    => $iii++,
				'brand'     => $iii++,
				'upc'     => $iii++,
				'color'     => $iii++,
				'size'      => $iii++,
				'quantity'    => $iii++,
				//      'quantity_order_min'    => $iii++,
				//      'quantity_order_units'    => $iii++,
				'sort_order'    => $iii++,
				'product_id'    => $iii++,
				'manufacturer_id' => $iii++,
				'exp_date'    => $iii++,
				'product_type'    => $iii++,
				'delete'    => $iii++,
				'currency'    => $iii++,
			);

			$filelayout_sql = "
			SELECT
				p.products_id as v_products_id,
				p.products_model as v_products_model,
				p.products_image as v_products_image,
				p.products_page_url as v_products_page_url,
				p.products_price as v_products_price,
				p.price_currency_code as v_price_currency_code,
				p.products_weight as v_products_weight,
				p.products_date_added as v_date_avail,
				p.products_tax_class_id as v_tax_class_id,
				p.products_quantity as v_products_quantity,
				p.products_sort as v_products_sort,
				p.manufacturers_id as v_manufacturers_id,
				subc.categories_id as v_categories_id
			FROM
				".TABLE_PRODUCTS." as p,
				".TABLE_CATEGORIES." as subc,
				".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
			WHERE
				".@$categories_range."
				".@$limitman_sql."
				p.products_id = ptoc.products_id AND
				".@$BEGIN1."
				".@$BEEND1."
				ptoc.categories_id = subc.categories_id AND
				p.products_quantity > 0
				".@$catfil."
			";
		break;

		// VJ product attributes begin
		case 'attrib':
			$iii = 0;
			$filelayout = array(
				'v_products_id'   => $iii++,
				'v_products_model'  => $iii++,
				'v_products_page_url'  => $iii++,
			);

			$header_array = array();

			$languages = os_get_languages();

			global $attribute_options_array;

			$attribute_options_count = 1;
			foreach ($attribute_options_array as $attribute_options_values)
			{
				$key1 = 'v_attribute_options_id_'.$attribute_options_count;
				$header_array[$key1] = $iii++;

				for ($i=0, $n=sizeof($languages); $i<$n; $i++)
				{
					$l_id = $languages[$i]['id'];

					$key2 = 'v_attribute_options_name_'.$attribute_options_count.'_'.$l_id;
					$header_array[$key2] = $iii++;
				}

				$attribute_values_query = "select products_options_values_id  from ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." where products_options_id = '".(int)$attribute_options_values['products_options_id']."' order by products_options_values_id";

				$attribute_values_values = os_db_query($attribute_values_query);

				$attribute_values_count = 1;
				while ($attribute_values = os_db_fetch_array($attribute_values_values))
				{
					$key3 = 'v_attribute_values_id_'.$attribute_options_count.'_'.$attribute_values_count;
					$header_array[$key3] = $iii++;

					$key4 = 'v_attribute_values_price_'.$attribute_options_count.'_'.$attribute_values_count;
					$header_array[$key4] = $iii++;

					for ($i=0, $n=sizeof($languages); $i<$n; $i++)
					{
						$l_id = $languages[$i]['id'];

						$key5 = 'v_attribute_values_name_'.$attribute_options_count.'_'.$attribute_values_count.'_'.$l_id;
						$header_array[$key5] = $iii++;
					}

					$attribute_values_count++;
				}

				$attribute_options_count++;
			}

			$filelayout = array_merge($filelayout, $header_array);

			$filelayout_sql = "
			SELECT
				p.products_id as v_products_id,
				p.products_page_url as v_products_page_url,
				p.products_model as v_products_model
			FROM
				".TABLE_PRODUCTS." as p
				".@$WHERE."
				".@$categories_range."
				".@$limitman_sql."
				".@$BEGIN1."
				".@$BEEND1."
				".@$catfil."
			";

		break;
		// VJ product attributes end
	}

	$filelayout_count = count($filelayout);
}


function walk( $item1 )
{
	global $filelayout, $filelayout_count, $modelsize;
	global $active, $inactive, $langcode, $default_these, $deleteit, $zero_qty_inactive;
	global $epdlanguage_id, $price_with_tax, $replace_quotes, $v_products_id1;
	global $default_images, $default_image_manufacturer, $default_image_product, $default_image_category;
	global $separator, $max_categories ;

	// first we clean up the row of data


	// формируем сразу массив производителей
	$getManufacturersQuery = os_db_query("SELECT manufacturers_id, manufacturers_name FROM ".TABLE_MANUFACTURERS."");
	$getManufacturers = array();
	if (os_db_num_rows($getManufacturersQuery) > 0)
	{
		while($getManu = os_db_fetch_array($getManufacturersQuery))
		{
			$getManufacturers[$getManu['manufacturers_id']] = $getManu['manufacturers_name'];
		}
	}

	// chop blanks from each end
	$item1 = ltrim(rtrim($item1));

	// blow it into an array, splitting on the tabs
	$items = explode($separator, $item1);

	// make sure all non-set things are set to '';
	// and strip the quotes from the start and end of the stings.
	// escape any special chars for the database.

	foreach($filelayout as $key => $value)
	{
		$i = $filelayout[$key];
		if (isset($items[$i]) == false)
		{
			$items[$i]='';
		}
		else
		{
			// Check to see if either of the magic_quotes are turned on or off;
			// And apply filtering accordingly.
			if (function_exists('ini_get'))
			{
				//echo "Getting ready to check magic quotes<br>";
				if (ini_get('magic_quotes_runtime') == 1)
				{
					// The magic_quotes_runtime are on, so lets account for them
					// check if the last character is a quote;
					// if it is, chop off the quotes.
					if (substr($items[$i],-1) == '"')
					{
						$items[$i] = substr($items[$i],2,strlen($items[$i])-4);
					}
					// now any remaining doubled double quotes should be converted to one doublequote
					$items[$i] = str_replace('\"\"',"\"",$items[$i]);
					//if ($replace_quotes){
					//  $items[$i] = str_replace('\"',"\"",$items[$i]);
					//  $items[$i] = str_replace("\'","\"",$items[$i]);
					//}
				}
				else
				{ // no magic_quotes are on
					// check if the last character is a quote;
					// if it is, chop off the 1st and last character of the string.
					if (substr($items[$i],-1) == '"')
					{
						$items[$i] = substr($items[$i],1,strlen($items[$i])-2);
					}
					// now any remaining doubled double quotes should be converted to one doublequote
					$items[$i] = str_replace('""',"\"",$items[$i]);
					if ($replace_quotes)
					{
						$items[$i] = str_replace('"',"\"",$items[$i]);
						$items[$i] = str_replace("'","\'",$items[$i]);
					}
				}
			}
		}
	}

	if ((!isset($items[$filelayout['v_products_page_url']]) or empty($items[$filelayout['v_products_page_url']])) && SEO_URL_PRODUCT_GENERATOR_IMPORT=='true')
	{
		$items[$filelayout['v_products_page_url']] = os_cleanName(strtolower($items[$filelayout['v_products_name_1']])).'.html';
	}

	// EP for product extra fields Contrib by minhmaster DEVSOFTVN ==========
	/* if ((!isset($row['v_products_url']) or empty($row['v_products_url'])) && SEO_URL_PRODUCT_GENERATOR_IMPORT=='true')
	{
	// echo $row['v_products_name'];
	print_r($row);
	$row['v_products_url'] = os_cleanName($row['v_products_name']).'.html';
	//echo $row['v_products_url'];
	}
	*/

	$v_products_extra_fields_id = @$items[@$filelayout['v_products_extra_fields_id']];
	$v_products_id = $items[$filelayout['v_products_id']];
	$v_products_extra_fields_value = @$items[@$filelayout['v_products_extra_fields_value']];

	if (isset($v_products_extra_fields_id) )
	{					
		$sql_exist = "SELECT products_extra_fields_value FROM ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS. " WHERE (products_id ='".$v_products_id. "') AND (products_extra_fields_id ='".$v_products_extra_fields_id ."')";
		if (os_db_num_rows(os_db_query($sql_exist)) <= 0)
		{
			$sql_extra_field = "INSERT INTO ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS."(products_id,products_extra_fields_id,products_extra_fields_value) VALUES ('".$v_products_id."','".$v_products_extra_fields_id."','".$v_products_extra_fields_value."')";
			$str_err_report= " $v_products_extra_fields_id | $v_products_id  | $v_products_extra_fields_value | <b><font color=blue>".EASY_LABEL_NEW_PRODUCT."</font></b><br>";					
		}
		else
		{
			$sql_extra_field = "UPDATE ".TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS." SET products_extra_fields_value='".$v_products_extra_fields_value."' WHERE (products_id ='".$v_products_id. "') AND (products_extra_fields_id ='".$v_products_extra_fields_id ."')";
			$str_err_report = " $v_products_extra_fields_id | $v_products_id  | $v_products_extra_fields_value | <b><font color=blue>".EASY_LABEL_UPDATED."</font></b><br>";					
		}

		$result = os_db_query($sql_extra_field);
		//echo $sql_extra_field;
		echo $str_err_report;
	}
	else
	{
		//============ EP for product extra fields Contrib by minhmt DEVSOFTVN off============
		// now do a query to get the record's current contents
		$sql = "
		SELECT
			p.products_id as v_products_id,
			p.products_model as v_products_model,
			p.products_image as v_products_image,
			p.products_page_url as v_products_page_url,
			p.products_price as v_products_price,
			p.price_currency_code as v_price_currency_code,
			p.products_weight as v_products_weight,
			p.products_date_added as v_date_avail,
			p.products_tax_class_id as v_tax_class_id,
			p.products_quantity as v_products_quantity,
			p.products_sort as v_products_sort,
			p.manufacturers_id as v_manufacturers_id,
			subc.categories_id as v_categories_id
		FROM
			".TABLE_PRODUCTS." as p,
			".TABLE_CATEGORIES." as subc,
			".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
		WHERE
			p.products_id = ptoc.products_id AND
			p.products_model = '".$items[$filelayout['v_products_model']]."' AND
			ptoc.categories_id = subc.categories_id
		";

		$result = os_db_query($sql);
		$row =  os_db_fetch_array($result);

		while ($row)
		{
			// OK, since we got a row, the item already exists.
			// Let's get all the data we need and fill in all the fields that need to be defaulted to the current values
			// for each language, get the description and set the vals
			foreach ($langcode as $key => $lang)
			{
				//echo "Inside defaulting loop";
				//echo "key is $key<br>";
				//echo "langid is ".$lang['id']."<br>";
				$result2 = os_db_query("SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id = ".$row['v_products_id']." AND language_id = '".$lang['id']."'");
				$row2 =  os_db_fetch_array($result2);
				// Need to report from ......_name_1 not ..._name_0
				$row['v_products_name_'.$lang['id']] = $row2['products_name']; 
				$row['v_products_description_'.$lang['id']] = $row2['products_description'];
				$row['v_products_short_description_'.$lang['id']] = $row2['products_short_description'];
				$row['v_products_keywords_'.$lang['id']] = $row2['products_keywords'];

				// support for Linda's Header Controller 2.0 here
				if(isset($filelayout['v_products_meta_title_'.$lang['id'] ]))
				{
					$row['v_products_meta_title_'.$lang['id']] = $row2['products_meta_title'];
					$row['v_products_meta_description_'.$lang['id']] = $row2['products_meta_description'];
					$row['v_products_meta_keywords_'.$lang['id']] = $row2['products_meta_keywords'];
				}
				// end support for Header Controller 2.0
			}

			// start with v_categories_id
			// Get the category description
			// set the appropriate variable name
			// if parent_id is not null, then follow it up.
			$thecategory_id = $row['v_categories_id'];

			for ($categorylevel = 1; $categorylevel < $max_categories + 1; $categorylevel++)
			{
				if ($thecategory_id)
				{
					$category_query = os_db_query("SELECT cd.categories_name, c.parent_id FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd WHERE c.categories_id = '".(int)$thecategory_id."' AND cd.categories_id = '".(int)$thecategory_id."' AND cd.language_id = '".(int)$epdlanguage_id."'");
					$row2 =  os_db_fetch_array($category_query);
					$temprow['v_categories_name_'.$categorylevel] = $row2['categories_name'];

					//$result2 = os_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id = ".$thecategory_id." AND language_id = ".$epdlanguage_id."");
					//$row2 =  os_db_fetch_array($result2);
					// only set it if we found something
					//$temprow['v_categories_name_'.$categorylevel] = $row2['categories_name'];
					// now get the parent ID if there was one

					//$result3 = os_db_query("SELECT parent_id FROM ".TABLE_CATEGORIES." WHERE categories_id = ".$thecategory_id."");
					//$row3 = os_db_fetch_array($result3);
					$theparent_id = $row2['parent_id'];

					if ($theparent_id != '')
						$thecategory_id = $theparent_id; // there was a parent ID, lets set thecategoryid to get the next level
					else
						$thecategory_id = false; // we have found the top level category for this item,
				}
				else
					$temprow['v_categories_name_'.$categorylevel] = '';
			}

			// temprow has the old style low to high level categories.
			$newlevel = 1;
			// let's turn them into high to low level categories
			for( $categorylevel=$max_categories+1; $categorylevel>0; $categorylevel--)
			{
				if (@$temprow['v_categories_name_'.$categorylevel] != '')
				{
					@$row['v_categories_name_'.@$newlevel++] = @$temprow['v_categories_name_'.$categorylevel];
				}
			}

			if ($row['v_manufacturers_id'] != '')
			{
				//$result2 = os_db_query("SELECT manufacturers_name FROM ".TABLE_MANUFACTURERS." WHERE manufacturers_id = ".$row['v_manufacturers_id']."");
				//$row2 = os_db_fetch_array($result2);
				//$row['v_manufacturers_name'] = $row2['manufacturers_name'];
				$row['v_manufacturers_name'] = $getManufacturers[$row['v_manufacturers_id']]['manufacturers_name'];
			}

			//elari -
			//We check the value of tax class and title instead of the id
			//Then we add the tax to price if $price_with_tax is set to true
			$row_tax_multiplier = os_get_tax_class_rate($row['v_tax_class_id']);
			$row['v_tax_class_title'] = os_get_tax_class_title($row['v_tax_class_id']);
			if ($price_with_tax)
			{
				$row['v_products_price'] = $row['v_products_price'] + round($row['v_products_price']* $row_tax_multiplier / 100,2);
			}

			// now create the internal variables that will be used
			// the $$thisvar is on purpose: it creates a variable named what ever was in $thisvar and sets the value
			foreach ($default_these as $thisvar)
			{
				@$$thisvar =@ $row[$thisvar];
			}

			$row =  os_db_fetch_array($result);
		}

		// this is an important loop.  What it does is go thru all the fields in the incoming file and set the internal vars.
		// Internal vars not set here are either set in the loop above for existing records, or not set at all (null values)
		// the array values are handled separatly, although they will set variables in this loop, we won't use them.
		foreach( $filelayout as $key => $value )
		{
			$$key = $items[$value];
		}

		// so how to handle these?  we shouldn't built the array unless it's been giving to us.
		// The assumption is that if you give us names and descriptions, then you give us name
		// and description for all applicable languages
		foreach ($langcode as $lang)
		{
			//echo "Langid is ".$lang['id']."<br>";
			$l_id = $lang['id'];
			if (isset($filelayout['v_products_name_'.$l_id ]))
			{
				//we set dynamically the language values
				$v_products_name[$l_id]   = $items[$filelayout['v_products_name_'.$l_id]];
				$v_products_description[$l_id]  = $items[$filelayout['v_products_description_'.$l_id ]];
				$v_products_short_description[$l_id]  = $items[$filelayout['v_products_short_description_'.$l_id ]];
				$v_products_keywords[$l_id]   = $items[$filelayout['v_products_keywords_'.$l_id ]];
				$v_products_url[$l_id]    = $items[$filelayout['v_products_url_'.$l_id ]];
				// support for Linda's Header Controller 2.0 here
				if (isset($filelayout['v_products_meta_title_'.$l_id]))
				{
					$v_products_meta_title[$l_id]   = $items[$filelayout['v_products_meta_title_'.$l_id]];
					$v_products_meta_description[$l_id]   = $items[$filelayout['v_products_meta_description_'.$l_id]];
					$v_products_meta_keywords[$l_id]  = $items[$filelayout['v_products_meta_keywords_'.$l_id]];
				}
				// end support for Header Controller 2.0
			}
		}

		//elari... we get the tax_clas_id from the tax_title
		//on screen will still be displayed the tax_class_title instead of the id....
		if ( isset( $v_tax_class_title) )
		{
			$v_tax_class_id = os_get_tax_title_class_id($v_tax_class_title);
		}
		//we check the tax rate of this tax_class_id
		$row_tax_multiplier = os_get_tax_class_rate($v_tax_class_id);

		//And we recalculate price without the included tax...
		//Since it seems display is made before, the displayed price will still include tax
		//This is same problem for the tax_clas_id that display tax_class_title
		if ($price_with_tax)
		{
			$v_products_price = round( $v_products_price / (1 + ($row_tax_multiplier * $price_with_tax/100)), 2);
		}

		// if they give us one category, they give us all 6 categories
		unset ($v_categories_name); // default to not set.
		if (isset($filelayout['v_categories_name_1']))
		{
			$newlevel = 1;
			for( $categorylevel=6; $categorylevel>0; $categorylevel--)
			{
				if ( $items[$filelayout['v_categories_name_'.$categorylevel]] != '')
				{
					$v_categories_name[$newlevel++] = $items[$filelayout['v_categories_name_'.$categorylevel]];
				}
			}
			while( $newlevel < $max_categories+1)
			{
				$v_categories_name[$newlevel++] = ''; // default the remaining items to nothing
			}
		}

		if (ltrim(rtrim($v_products_quantity)) == '')
		{
			$v_products_quantity = 1;
		}

		if ($v_date_avail == '')
		{
			$v_date_avail = "CURRENT_TIMESTAMP";
		}
		else
		{
			// we put the quotes around it here because we can't put them into the query, because sometimes
			//   we will use the "current_timestamp", which can't have quotes around it.
			$v_date_avail = '"'.$v_date_avail.'"';
		}

		if ($v_date_added == '')
		{
			$v_date_added = "CURRENT_TIMESTAMP";
		}
		else
		{
			// we put the quotes around it here because we can't put them into the query, because sometimes
			//   we will use the "current_timestamp", which can't have quotes around it.
			$v_date_added = '"'.$v_date_added.'"';
		}

		// default the stock if they spec'd it or if it's blank
		$v_db_status = '1'; // default to active
		if ($v_status == $inactive)
		{
			// they told us to deactivate this item
			$v_db_status = '0';
		}

		if ($zero_qty_inactive && $v_products_quantity == 0)
		{
			// if they said that zero qty products should be deactivated, let's deactivate if the qty is zero
			$v_db_status = '0';
		}

		if (@$v_manufacturer_id=='')
		{
			@$v_manufacturer_id="NULL";
		}

		if (trim($v_products_image)=='')
		{
			$v_products_image = $default_image_product;
		}
		else
		{
			if (USE_EP_IMAGE_MANIPULATOR == 'true')
				prepare_image($v_products_image);
			else
				$v_products_image;
		}

		if (strlen($v_products_model) > $modelsize)
		{
			echo "<font color='red'>".strlen($v_products_model).$v_products_model.EASY_ERROR_2.$modelsize.'</font>';
			die();
		}

		// OK, we need to convert the manufacturer's name into id's for the database
		if ( isset($v_manufacturers_name) && $v_manufacturers_name != '' )
		{
			$result = os_db_query("SELECT man.manufacturers_id FROM ".TABLE_MANUFACTURERS." as man WHERE man.manufacturers_name = '".$v_manufacturers_name."'");
			$row =  os_db_fetch_array($result);
			if ( $row != '' )
			{
				foreach( $row as $item )
				{
					$v_manufacturer_id = $item;
				}
			}
			else
			{
				// to add, we need to put stuff in categories and categories_description
				$result = os_db_query("SELECT MAX( manufacturers_id) max FROM ".TABLE_MANUFACTURERS."");
				$row = os_db_fetch_array($result);
				$max_mfg_id = $row['max']+1;
				// default the id if there are no manufacturers yet
				if (!is_numeric($max_mfg_id))
				{
					$max_mfg_id=1;
				}

				$result = os_db_query("INSERT INTO ".TABLE_MANUFACTURERS."(manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified) VALUES ($max_mfg_id, '$v_manufacturers_name', '$default_image_manufacturer', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
				$v_manufacturer_id = $max_mfg_id;
			}
		}

		// if the categories names are set then try to update them
		if (isset($v_categories_name_1))
		{
			// start from the highest possible category and work our way down from the parent
			$v_categories_id = 0;
			$theparent_id = 0;
			for ( $categorylevel=$max_categories+1; $categorylevel>0; $categorylevel-- )
			{
				@$thiscategoryname = @$v_categories_name[$categorylevel];
				if ($thiscategoryname != '')
				{
					// we found a category name in this field

					// now the subcategory
					$result = os_db_query("SELECT cat.categories_id FROM ".TABLE_CATEGORIES." as cat, ".TABLE_CATEGORIES_DESCRIPTION." as des WHERE cat.categories_id = des.categories_id AND des.language_id = $epdlanguage_id AND cat.parent_id = ".$theparent_id." AND des.categories_name = '".$thiscategoryname."'");
					$row =  os_db_fetch_array($result);
					if ( $row != '' )
					{
						foreach( $row as $item )
						{
							$thiscategoryid = $item;
						}
					}
					else
					{
						// to add, we need to put stuff in categories and categories_description
						$result = os_db_query("SELECT MAX( categories_id) max FROM ".TABLE_CATEGORIES."");
						$row =  os_db_fetch_array($result);
						//var_dump($sql);
						echo '<br />';
						$max_category_id = $row['max']+1;
						if (!is_numeric($max_category_id))
						{
							$max_category_id=1;
						}
						$result = os_db_query("INSERT INTO ".TABLE_CATEGORIES."(categories_id, categories_image, group_permission_0, group_permission_1, group_permission_2, group_permission_3, parent_id, sort_order, date_added, last_modified) VALUES ($max_category_id, '$default_image_category', '1', '1', '1', '1', $theparent_id, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
						$result = os_db_query("INSERT INTO ".TABLE_CATEGORIES_DESCRIPTION."(categories_id, language_id, categories_name) VALUES ($max_category_id, '$epdlanguage_id', '$thiscategoryname')");
						$thiscategoryid = $max_category_id;
					}
					// the current catid is the next level's parent
					$theparent_id = $thiscategoryid;
					$v_categories_id = $thiscategoryid; // keep setting this, we need the lowest level category ID later
				}
			}
		}

		//if delete
		if ($v_action == 'delete')
		{
			// they want to delete this product.
			echo EASY_LABEL_DELETE_STATUS_1.$v_products_name[$l_id] .EASY_LABEL_DELETE_STATUS_2.'<br>';
			// Get the ID
			$delete_id = $v_products_id;
			// get category ID
			// kill in the products_to_categories
			os_db_query("delete from ".TABLE_PRODUCTS." where products_id ='". $delete_id."'");
			os_db_query("delete from ".TABLE_PRODUCTS_DESCRIPTION." where products_id ='". $delete_id."'");
			os_db_query("delete from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id ='". $delete_id."' and categories_id = '".$v_categories_id."' ");

			$prod_attrib_query = os_db_query("select products_attributes_id from ".TABLE_PRODUCTS_ATTRIBUTES." where products_id ='". $delete_id."'");
			while ($prod_attrib1 = os_db_fetch_array($prod_attrib_query))
			{
				os_db_query("delete from ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." where products_attributes_id  ='". $prod_attrib1[products_attributes_id] ."'");
			}

			os_db_query("delete from ".TABLE_PRODUCTS_ATTRIBUTES." where products_id ='". $delete_id."'");

			// Kill in the products table
			return; // we're done deleteing!
		}
		else if ($v_products_id != "")
		{
			//   products_id exists!
			array_walk($items, 'print_el');

			///////////////////////////////////////////// print_r($items);
			// First we check to see if this is a product in the current db.

			$result = os_db_query("SELECT products_id FROM ".TABLE_PRODUCTS." WHERE (products_id = '".$v_products_id."')");

			if (os_db_num_rows($result) == 0)
			{
				// insert into products
				$result = os_db_query("SHOW TABLE STATUS LIKE '".TABLE_PRODUCTS."'");
				$row =  os_db_fetch_array($result);
				$max_product_id = $row['Auto_increment'];

				//check for insert new product
				if ($v_products_id == '0')
					$v_products_id=$max_product_id;
				else
					$v_products_id=$v_products_id;

				// checks for numeric product_id
				if (!is_numeric($max_product_id))
				{
					$max_product_id=1;
					$v_products_id = $max_product_id;
				}

				//  $v_products_id1 = $max_product_id;
				echo EASY_LABEL_NEW_PRODUCT;

				$query = "INSERT INTO ".TABLE_PRODUCTS." (
				products_id,
				products_image,
				products_model,
				group_permission_0,
				group_permission_1,
				group_permission_2,
				group_permission_3,
				products_page_url,
				products_price,
				price_currency_code,
				products_status,
				products_tax_class_id,
				products_weight,
				products_quantity,
				products_sort,
				manufacturers_id)
				VALUES (
				'$v_products_id',
				'$v_products_image',
				'$v_products_model',
				'1',
				'1',
				'1',
				'1',
				'$v_products_page_url',
				'$v_products_price',
				'$v_price_currency_code',
				'$v_db_status',
				'$v_tax_class_id',
				'$v_products_weight',
				'$v_products_quantity',
				'$v_products_sort',
				'$v_manufacturer_id')
				";
				$result = os_db_query($query);
			}
			else
			{
				// existing product, get the id from the query
				// and update the product data
				$row =  os_db_fetch_array($result);
				$v_products_id = $row['products_id'];
				echo EASY_LABEL_UPDATED;
				$row =  os_db_fetch_array($result);
				$query = 'UPDATE '.TABLE_PRODUCTS.'
				SET
				products_page_url="'.$v_products_page_url.'" ,
				products_price="'.$v_products_price.'" ,
				price_currency_code="'.$v_price_currency_code.'" ,
				products_page_url="'.$v_products_page_url.'" ,
				products_model="'.$v_products_model.'" ,
				group_permission_0="1" ,
				group_permission_1="1" ,
				group_permission_2="1" ,
				group_permission_3="1" ,
				products_image="'.$v_products_image;

				// uncomment these lines if you are running the image mods
				$query .= '", products_weight="'.$v_products_weight .
				'", products_tax_class_id="'.$v_tax_class_id .
				'", products_date_available= '.$v_date_avail .
				', products_date_added= '.$v_date_added .
				', products_last_modified=CURRENT_TIMESTAMP
				, products_quantity="'.$v_products_quantity. '",
				products_sort="'.$v_products_sort. '",
				manufacturers_id='.$v_manufacturer_id .
				' , products_status='.$v_db_status.'
				WHERE
				(products_id = "'. $v_products_id.'")';

				$result = os_db_query($query);
			}

			// the following is common in both the updating an existing product and creating a new product
			if (isset($v_products_name))
			{
				foreach( $v_products_name as $key => $name)
				{
					if ($name!='' or $v_products_description[$key] !='')
					{
						$sql = "SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE
						products_id = $v_products_id AND
						language_id = ".$key;
						$result = os_db_query($sql);
						if (os_db_num_rows($result) == 0)
						{
							// nope, this is a new product description
							$result = os_db_query($sql);
							$sql =
							"INSERT INTO ".TABLE_PRODUCTS_DESCRIPTION."
							(products_id,
							language_id,
							products_name,
							products_description,
							products_short_description,
							products_keywords,
							products_url)
							VALUES (
							'".$v_products_id."',
							".$key.",
							'".$name."',
							'". $v_products_description[$key]."',
							'". $v_products_short_description[$key]."',
							'". $v_products_keywords[$key]."',
							'". $v_products_url[$key]."'
							)";

							// support for Linda's Header Controller 2.0
							if (isset($v_products_meta_title))
							{
								// override the sql if we're using Linda's contrib
								$sql =
								"INSERT INTO ".TABLE_PRODUCTS_DESCRIPTION."
								(products_id,
								language_id,
								products_name,
								products_description,
								products_short_description,
								products_keywords,
								products_url,
								products_meta_title,
								products_meta_description,
								products_meta_keywords)
								VALUES (
								'".$v_products_id."',
								".$key.",
								'".$name."',
								'". $v_products_description[$key]."',
								'". $v_products_short_description[$key]."',
								'". $v_products_keywords[$key]."',
								'". $v_products_url[$key]."',
								'". $v_products_meta_title[$key]."',
								'". $v_products_meta_description[$key]."',
								'". $v_products_meta_keywords[$key]."')";
							}
							// end support for Linda's Header Controller 2.0
							$result = os_db_query($sql);
						}
						else
						{
							// already in the description, let's just update it
							$sql =
							"UPDATE ".TABLE_PRODUCTS_DESCRIPTION." SET
							products_name='$name',
							products_description='".$v_products_description[$key]."',
							products_short_description='".$v_products_short_description[$key]."',
							products_keywords='".$v_products_keywords[$key]."',
							products_url='".$v_products_url[$key]."'
							WHERE
							products_id = '$v_products_id' AND
							language_id = '$key'";

							// support for Lindas Header Controller 2.0
							if (isset($v_products_meta_title))
							{
								// override the sql if we're using Linda's contrib
								$sql =
								"UPDATE ".TABLE_PRODUCTS_DESCRIPTION." SET
								products_name = '$name',
								products_description = '".$v_products_description[$key]."',
								products_short_description = '".$v_products_short_description[$key]."',
								products_keywords = '".$v_products_keywords[$key]."',
								products_url = '".$v_products_url[$key] ."',
								products_meta_title = '".$v_products_meta_title[$key] ."',
								products_meta_description = '".$v_products_meta_description[$key] ."',
								products_meta_keywords = '".$v_products_meta_keywords[$key] ."'
								WHERE
								products_id = '$v_products_id' AND
								language_id = '$key'";
							}
							// end support for Linda's Header Controller 2.0
							$result = os_db_query($sql);
						}
					}
				}
			}

			if (isset($v_categories_id))
			{
				if ($v_products_id == "0")
					$v_products_id=$max_product_id;
				else
					$v_products_id=$v_products_id;

				//find out if this product is listed in the category given
				$result_incategory = os_db_query("SELECT
				".TABLE_PRODUCTS_TO_CATEGORIES.".products_id,
				".TABLE_PRODUCTS_TO_CATEGORIES.".categories_id
				FROM
				".TABLE_PRODUCTS_TO_CATEGORIES."
				WHERE
				".TABLE_PRODUCTS_TO_CATEGORIES.".products_id='".$v_products_id."' AND
				".TABLE_PRODUCTS_TO_CATEGORIES.".categories_id='".$v_categories_id."'");

				if (os_db_num_rows($result_incategory) == 0)
				{
					// nope, this is a new category for this product
					$res1 = os_db_query('INSERT INTO '.TABLE_PRODUCTS_TO_CATEGORIES.' (products_id, categories_id) VALUES ("'.$v_products_id.'", "'.$v_categories_id.'")');
				}
			}

			// for the separate prices per customer module
			$ll=1;

			if (isset($v_customer_price_1))
			{
				if (($v_customer_group_id_1 == '') AND ($v_customer_price_1 != ''))
				{
					echo EASY_ERROR_4;
					die();
				}

				// they spec'd some prices, so clear all existing entries
				os_db_query('DELETE FROM '.TABLE_PRODUCTS_GROUPS.' WHERE products_id = '.$v_products_id);

				// and insert the new record
				if ($v_customer_price_1 != '')
				{
					os_db_query('INSERT INTO '.TABLE_PRODUCTS_GROUPS.' VALUES ('.$v_customer_group_id_1.', '.$v_customer_price_1.', '.$v_products_id.', '.$v_products_price.')');
				}

				if ($v_customer_price_2 != '')
				{
					os_db_query('INSERT INTO '.TABLE_PRODUCTS_GROUPS.' VALUES ('.$v_customer_group_id_2.', '.$v_customer_price_2.', '.$v_products_id.', '.$v_products_price.')');
				}

				if ($v_customer_price_3 != '')
				{
					os_db_query('INSERT INTO '.TABLE_PRODUCTS_GROUPS.' VALUES ('.$v_customer_group_id_3.', '.$v_customer_price_3.', '.$v_products_id.', '.$v_products_price.')');
				}

				if ($v_customer_price_4 != '')
				{
					os_db_query('INSERT INTO '.TABLE_PRODUCTS_GROUPS.' VALUES ('.$v_customer_group_id_4.', '.$v_customer_price_4.', '.$v_products_id.', '.$v_products_price.')');
				}
			}

			// VJ product attribs begin insert
			if (isset($v_attribute_options_id_1))
			{
				$attribute_rows = 1; // master row count

				$languages = os_get_languages();

				// product options count
				$attribute_options_count = 1;
				$v_attribute_options_id_var = 'v_attribute_options_id_'.$attribute_options_count;

				while (isset($$v_attribute_options_id_var) && !empty($$v_attribute_options_id_var))
				{
					// remove product attribute options linked to this product before proceeding further
					// this is useful for removing attributes linked to a product
					$attributes_clean_query = "delete from ".TABLE_PRODUCTS_ATTRIBUTES." where products_id = '".(int)$v_products_id."' and options_id = '".(int)$$v_attribute_options_id_var."'";

					os_db_query($attributes_clean_query);

					$attribute_options_query = "select products_options_name from ".TABLE_PRODUCTS_OPTIONS." where products_options_id = '".(int)$$v_attribute_options_id_var."'";

					$attribute_options_values = os_db_query($attribute_options_query);

					// option table update begin
					if ($attribute_rows == 1)
					{
						// insert into options table if no option exists
						if (os_db_num_rows($attribute_options_values) <= 0)
						{
							for ($i=0, $n=sizeof($languages); $i<$n; $i++)
							{
								$lid = $languages[$i]['id'];

								$v_attribute_options_name_var = 'v_attribute_options_name_'.$attribute_options_count.'_'.$lid;

								if (isset($$v_attribute_options_name_var))
								{
									$attribute_options_insert = os_db_query("insert into ".TABLE_PRODUCTS_OPTIONS." (products_options_id, language_id, products_options_name) values ('".(int)$$v_attribute_options_id_var."', '".(int)$lid."', '".$$v_attribute_options_name_var."')");
								}
							}
						}
						else
						{
							// update options table, if options already exists
							for ($i=0, $n=sizeof($languages); $i<$n; $i++)
							{
								$lid = $languages[$i]['id'];

								$v_attribute_options_name_var = 'v_attribute_options_name_'.$attribute_options_count.'_'.$lid;

								if (isset($$v_attribute_options_name_var))
								{
									$attribute_options_update_lang_values = os_db_query("select products_options_name from ".TABLE_PRODUCTS_OPTIONS." where products_options_id = '".(int)$$v_attribute_options_id_var."' and language_id ='".(int)$lid."'");

									// if option name doesn't exist for particular language, insert value
									if (os_db_num_rows($attribute_options_update_lang_values) <= 0)
										os_db_query("insert into ".TABLE_PRODUCTS_OPTIONS." (products_options_id, language_id, products_options_name) values ('".(int)$$v_attribute_options_id_var."', '".(int)$lid."', '".$$v_attribute_options_name_var."')");
									else
										os_db_query("update ".TABLE_PRODUCTS_OPTIONS." set products_options_name = '".$$v_attribute_options_name_var."' where products_options_id ='".(int)$$v_attribute_options_id_var."' and language_id = '".(int)$lid."'");
								}
							}
						}
					}
					// option table update end

					// product option values count
					$attribute_values_count = 1;
					$v_attribute_values_id_var = 'v_attribute_values_id_'.$attribute_options_count.'_'.$attribute_values_count;

					while (isset($$v_attribute_values_id_var) && !empty($$v_attribute_values_id_var))
					{
						$attribute_values_values = os_db_query("select products_options_values_name from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".(int)$$v_attribute_values_id_var."'");

						// options_values table update begin
						if ($attribute_rows == 1)
						{
							// insert into options_values table if no option exists
							if (os_db_num_rows($attribute_values_values) <= 0)
							{
								for ($i=0, $n=sizeof($languages); $i<$n; $i++)
								{
									$lid = $languages[$i]['id'];

									$v_attribute_values_name_var = 'v_attribute_values_name_'.$attribute_options_count.'_'.$attribute_values_count.'_'.$lid;

									if (isset($$v_attribute_values_name_var))
									{
										os_db_query("insert into ".TABLE_PRODUCTS_OPTIONS_VALUES." (products_options_values_id, language_id, products_options_values_name) values ('".(int)$$v_attribute_values_id_var."', '".(int)$lid."', '".$$v_attribute_values_name_var."')");
									}
								}

								// insert values to pov2po table
								os_db_query("insert into ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." (products_options_id, products_options_values_id) values ('".(int)$$v_attribute_options_id_var."', '".(int)$$v_attribute_values_id_var."')");
							}
							else
							{
								// update options table, if options already exists
								for ($i=0, $n=sizeof($languages); $i<$n; $i++)
								{
									$lid = $languages[$i]['id'];

									$v_attribute_values_name_var = 'v_attribute_values_name_'.$attribute_options_count.'_'.$attribute_values_count.'_'.$lid;

									if (isset($$v_attribute_values_name_var))
									{
										$attribute_values_update_lang_values = os_db_query("select products_options_values_name from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".(int)$$v_attribute_values_id_var."' and language_id ='".(int)$lid."'");

										// if options_values name doesn't exist for particular language, insert value
										if (os_db_num_rows($attribute_values_update_lang_values) <= 0)
											os_db_query("insert into ".TABLE_PRODUCTS_OPTIONS_VALUES." (products_options_values_id, language_id, products_options_values_name) values ('".(int)$$v_attribute_values_id_var."', '".(int)$lid."', '".$$v_attribute_values_name_var."')");
										else
											os_db_query("update ".TABLE_PRODUCTS_OPTIONS_VALUES." set products_options_values_name = '".$$v_attribute_values_name_var."' where products_options_values_id ='".(int)$$v_attribute_values_id_var."' and language_id = '".(int)$lid."'");
									}
								}
							}
						}
						// options_values table update end

						// options_values price update begin
						$v_attribute_values_price_var = 'v_attribute_values_price_'.$attribute_options_count.'_'.$attribute_values_count;

						if (isset($$v_attribute_values_price_var) && ($$v_attribute_values_price_var != ''))
						{
							$attribute_prices_values = os_db_query("select options_values_price, price_prefix from ".TABLE_PRODUCTS_ATTRIBUTES." where products_id = '".(int)$v_products_id."' and options_id ='".(int)$$v_attribute_options_id_var."' and options_values_id = '".(int)$$v_attribute_values_id_var."'");

							$attribute_values_price_prefix = ($$v_attribute_values_price_var < 0) ? '-' : '+';

							// options_values_prices table update begin
							// insert into options_values_prices table if no price exists
							if (os_db_num_rows($attribute_prices_values) <= 0)
								os_db_query("insert into ".TABLE_PRODUCTS_ATTRIBUTES." (products_id, options_id, options_values_id, options_values_price, price_prefix) values ('".(int)$v_products_id."', '".(int)$$v_attribute_options_id_var."', '".(int)$$v_attribute_values_id_var."', '".$$v_attribute_values_price_var."', '".$attribute_values_price_prefix."')");
							else
								$attribute_prices_update = os_db_query("update ".TABLE_PRODUCTS_ATTRIBUTES." set options_values_price = '".$$v_attribute_values_price_var."', price_prefix = '".$attribute_values_price_prefix."' where products_id = '".(int)$v_products_id."' and options_id = '".(int)$$v_attribute_options_id_var."' and options_values_id ='".(int)$$v_attribute_values_id_var."'");
						}
						// options_values price update end

						$attribute_values_count++;
						$v_attribute_values_id_var = 'v_attribute_values_id_'.$attribute_options_count.'_'.$attribute_values_count;
					}

					$attribute_options_count++;
					$v_attribute_options_id_var = 'v_attribute_options_id_'.$attribute_options_count;
				}

				$attribute_rows++;
			}

			// VJ product attribs end
			// BOF mo_image
			for ($i = 0; $i < MO_PICS; $i++)
			{
				if(isset($filelayout['v_mo_image_'.($i+1)]))
				{
					// echo '<pre>';var_dump($items[$filelayout['v_mo_image_'.($i+1)]]);echo '</pre>';
					if ($items[$filelayout['v_mo_image_'.($i+1)]] != "")
					{
						$items[$filelayout['v_mo_image_'.($i+1)]];
						if (USE_EP_IMAGE_MANIPULATOR == 'true')
							prepare_image($items[$filelayout['v_mo_image_'.($i+1)]]);
						else
							$items[$filelayout['v_mo_image_'.($i+1)]];
					}
					$check_query = os_db_query("select image_id, image_name from ".TABLE_PRODUCTS_IMAGES." where products_id='".(int)$v_products_id."' and image_nr='".($i+1)."'");
					if (os_db_num_rows($check_query) <= 0)
					{
						if($items[$filelayout['v_mo_image_'.($i+1)]] != "")
						{
							os_db_query("insert into ".TABLE_PRODUCTS_IMAGES." (products_id, image_nr, image_name) values ('".(int)$v_products_id."', '".($i+1)."', '".$items[$filelayout['v_mo_image_'.($i+1)]]."')");
						}
					}
					else
					{
						$check = os_db_fetch_array($check_query);
						if($items[$filelayout['v_mo_image_'.($i+1)]] == "")
							os_db_query("delete from ".TABLE_PRODUCTS_IMAGES." where image_id='".$check['image_id']."'");
						elseif ($items[$filelayout['v_mo_image_'.($i+1)]] != $check['image_name'])
							os_db_query("update ".TABLE_PRODUCTS_IMAGES." set image_name='".$items[$filelayout['v_mo_image_'.($i+1)]]."' where image_id='".$check['image_id']."'");
					}
				}
			}
			// EOF mo_image
		} else
		{
			if ($row['v_products_id'] = '')
			{
				echo EASY_ERROR_3 ;
			}
		}
		// end of row insertion code
	}
} // end of EP for extra filed 	

$main->bottom();
?>