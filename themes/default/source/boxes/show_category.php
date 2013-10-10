<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

global $MaxLevel, $HideEmpty, $ShowAktSub;

	$MaxLevel = 1;
	$HideEmpty = false;
	$ShowAktSub = true;

function os_show_category($cid, $level, $foo, $cpath) 
{

	global $old_level, $categories_string; //, $HTTP_GET_VARS; // Brauchen wir nicht
	global $MaxLevel, $HideEmpty, $ShowAktSub;

	// 1) Ьberprьfen, ob Kategorie Produkte enthдlt
	$Empty = true;
	$pInCat = os_count_products_in_category($cid);
	if ($pInCat > 0)
		$Empty = false;
	
	// 2) Ьberprьfen, ob Kategorie gezeigt werden soll
	$Show = false;
	if ($HideEmpty) {
		if (!$Empty)
			$Show = true;
	} else {
		$Show = true;
	}

	// 3) Ьberprьfen, ob Unterkategorien gezeigt werden sollen
	$ShowSub = false;
	if ($MaxLevel) {
		if ($level < $MaxLevel)
			$ShowSub = true;
	} else {
		$ShowSub = true;
	}
				
	if($Show) { // Wenn Kategorie gezeigt werden soll ....
	
		if ($cid != 0) {
			
			// 24.06.2007 BugFix
			// Auf "product_info"-Seiten wurde Kategorie nicht erkannt 
			// $category_path = explode('_',$HTTP_GET_VARS['cPath']);
			$category_path = explode('_',$GLOBALS['cPath']);
			$in_path = in_array($cid, $category_path);
			$this_category = array_pop($category_path);
		
			for ($a = 0; $a < $level; $a++)                           ;
			
			// Produktzдhlung
			$ProductsCount = false;
			// Lange gerдtselt, aber das ist tatsдchlich 
			// ein String und kein Boolean.                                                                                
			if (SHOW_COUNTS == 'true') 
				$ProductsCount = ' <em>(' . $pInCat . ')</em>';	
                                                  
			// Aktiv - Nicht Aktiv
			$Aktiv = false;
			if ($this_category == $cid) 
				// Wenn Kategorie aktiv ist
				$Aktiv = ' Current'; 
			elseif ($in_path) 
				// Wenn Oberkategorie aktiv ist
				$Aktiv = ' CurrentParent'; 
	
			// Hat ein SubMenue - hat kein SubMenue
			// CSS-Klasse festlegen
			$SubMenue = false;
			
			if (os_has_category_subcategories($cid)) $SubMenue = " SubMenue";
	
			// Listenpunkt
			// CSS-Klasse festlegen
			$MainStyle = 'CatLevel'.$level;
			
			// Quelltext einrьcken
			$Tabulator = str_repeat("\t",$level-1);
	
			// Navigations-Liste ist jetzt hierarchisch!
			if($old_level) { 
				if ($old_level < $level) {
					$Pre = "\n<ul>";
					$Pre = str_replace("\n","\n".$Tabulator, $Pre)."\n";
				} else {
					$Pre = "</li>\n";
					if ($old_level > $level) {
						// Listenpunkte schlieЯen
						// Quelltext einrьcken
						for ($counter = 0; $counter < $old_level - $level; $counter++) {
							$Pre .= str_repeat("\t", $old_level - $counter -1)."</ul>\n".str_repeat("\t", $old_level - $counter- 2)."</li>\n";
						}
					}
				} 
			}
				
			// Listenpunkte zusammensetzen
			if (!isset($Pre)) $Pre ='';
			$categories_string .=	$Pre.$Tabulator.
									'<li class="'.$MainStyle.$SubMenue.$Aktiv.'">'.
									// Bugfix, 12. Juli 2007
									//'<a href="' . os_href_link(FILENAME_DEFAULT, 'cPath=' . $cpath . $cid) . '">'.
									'<a href="' . os_href_link(FILENAME_DEFAULT, os_category_link($cid, $foo[$cid]['name']) ) . '">'.
									$foo[$cid]['name'].$ProductsCount.
									'</a>';
		}
		
		// fьr den nдchsten Durchgang ...
		$old_level = $level;
	
		// Unterkategorien durchsteppen
		foreach ($foo as $key => $value) {
	
			if ($foo[$key]['parent'] == $cid) 
			{
				// Sollen Unterkategorien gezeigt werden?
				if (isset($ShowAktSub) && isset($Aktiv)) $ShowSub = true;
				
				if ($ShowSub) 
					os_show_category($key, $level+1, $foo, ($level != 0 ? $cpath . $cid . '_' : ''));
			} 
		}
	} // Ende if($Show)
} 		
?>