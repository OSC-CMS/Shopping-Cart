<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

$box = new osTemplate;
$box_content = '';
$id = '';
$box->assign('tpl_path', _HTTP_THEMES_C);

global $MaxLevel, $HideEmpty, $ShowAktSub;

$MaxLevel = 10;
$HideEmpty = false;
$ShowAktSub = true;

function os_show_category($cid, $level, $foo, $cpath)
{
	global $old_level, $categories_string, $MaxLevel, $HideEmpty, $ShowAktSub;

	$Empty = true;
	if (SHOW_COUNTS == 'true')
	{
		$pInCat = os_count_products_in_category($cid);
	}

	if ($pInCat > 0)
		$Empty = false;

	$Show = false;
	if ($HideEmpty)
	{
		if (!$Empty)
			$Show = true;
	}
	else
		$Show = true;

	$ShowSub = false;
	if ($MaxLevel)
	{
		if ($level < $MaxLevel)
			$ShowSub = true;
	}
	else
		$ShowSub = true;

	if($Show)
	{
		if ($cid != 0)
		{
			$category_path = explode('_',$GLOBALS['cPath']);
			$in_path = in_array($cid, $category_path);
			$this_category = array_pop($category_path);

			for ($a = 0; $a < $level; $a++);

			$ProductsCount = false;

			if (SHOW_COUNTS == 'true')
			{
				$ProductsCount = ' <em>(' . $pInCat . ')</em>';
			}

			$Aktiv = false;
			if ($this_category == $cid) 
				$Aktiv = ' Current active '; 
			elseif ($in_path) 
				$Aktiv = ' CurrentParent'; 
			if ($in_path) 
				$Aktiv2 = ' CurrentParentLink'; 

			$SubMenue = false;

			if (os_has_category_subcategories($cid))
			{
				$SubMenue = " SubMenue";
				$SubMenueSpan = "<span></span>";
			}

			$MainStyle = 'CatLevel'.$level;

			$Tabulator = str_repeat("\t",$level-1);

			if($old_level)
			{
				if ($old_level < $level)
				{
					$Pre = "\n<ul>";
					$Pre = str_replace("\n","\n".$Tabulator, $Pre)."\n";
				}
				else
				{
					$Pre = "</li>\n";
					if ($old_level > $level)
					{
						for ($counter = 0; $counter < $old_level - $level; $counter++)
						{
							$Pre .= str_repeat("\t", $old_level - $counter -1)."</ul>\n".str_repeat("\t", $old_level - $counter- 2)."</li>\n";
						}
					}
				}
			}

			$categories_string .=	@$Pre.$Tabulator.
			'<li class="'.$MainStyle.$SubMenue.$Aktiv.'">'.
			'<a class="'.$Aktiv2.'" href="' . os_href_link(FILENAME_DEFAULT, os_category_link($cid, $foo[$cid]['name']) ) . '">'.
			$foo[$cid]['name'].$ProductsCount.$SubMenueSpan.
			'</a>';
		}

		$old_level = $level;

		foreach ($foo as $key => $value)
		{
			if ($foo[$key]['parent'] == $cid)
			{
				if (@$ShowAktSub && @$Aktiv)
					$ShowSub = true;

				if ($ShowSub)
					os_show_category($key, $level+1, $foo, ($level != 0 ? $cpath . $cid . '_' : ''));
			}
		}
	}
}

$categories_string = '';

if (GROUP_CHECK == 'true')
$group_check = "AND c.group_permission_".$_SESSION['customers_status']['customers_status_id']." = 1 "; 
else
$group_check = '';

$categories_query = osDBquery("
	SELECT 
		c.categories_id, cd.categories_name, c.parent_id 
	FROM 
		".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION . " cd 
	WHERE 
		c.categories_status = '1' ".$group_check." AND c.categories_id = cd.categories_id AND cd.language_id='".(int)$_SESSION['languages_id']."' 
	ORDER BY 
		sort_order, cd.categories_name
");

if (os_db_num_rows($categories_query, true))
{
	while ($categories = os_db_fetch_array($categories_query,true))  
	{
		$foo[$categories['categories_id']] = array
		(
			'name' => $categories['categories_name'],
			'parent' => $categories['parent_id']
		);
	}

	os_show_category(0, 0, $foo, '');

	$CatNaviStart = "\n<ul class=\"nav nav-tabs nav-stacked\">\n";

	for ($counter = 1; $counter < $old_level+1; $counter++)
	{
		@$CatNaviEnd .= "</li>\n".str_repeat("\t", $old_level - $counter)."</ul>\n";
		if ($old_level - $counter > 0)
			$CatNaviEnd .= str_repeat("\t", ($old_level - $counter)-1);
	}
}

$box->assign('BOX_CONTENT', $CatNaviStart.$categories_string.$CatNaviEnd);
$box->assign('language', $_SESSION['language']);

if (USE_CACHE=='false')
{
$box->caching = 0;
$box_categories= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_categories.html');
}
else
{
$box->caching = 1;
$box->cache_lifetime=CACHE_LIFETIME;
$box->cache_modified_check=CACHE_CHECK;
$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].$current_category_id;
$box_categories= $box->fetch(CURRENT_TEMPLATE.'/boxes/box_categories.html',$cache_id);
}

$osTemplate->assign('box_CATEGORIES',$box_categories);
?>