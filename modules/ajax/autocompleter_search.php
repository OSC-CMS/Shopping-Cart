<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

$limit = 30;

$keyword = strval(preg_replace('/[^\p{L}\p{Nd}\d\s_\-\.\%\s]/ui', '', $_REQUEST['query']));

$searchQuery = os_db_query("
	SELECT 
		distinct p.products_id, pd.products_name, p.products_image 
	FROM 
		".TABLE_PRODUCTS." p 
			LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd on pd.products_id = p.products_id 
	WHERE 
		pd.products_name LIKE '%".mysql_real_escape_string($keyword)."%' AND 
		p.products_status = '1' AND 
		p.products_search = '0' AND 
		pd.language_id = '".(int)$_SESSION['languages_id']."' 
	ORDER BY 
		pd.products_name DESC limit ".$limit."
");

while ($p = os_db_fetch_array($searchQuery, true))
{
	if(!empty($p['products_image']))
	{
		$p['products_image'] = 'images/product_images/thumbnail_images/'.$p['products_image'];
		$products_name[] = $p['products_name'];
	}
	else
		$products_name[] = $p['products_name'];

	$products_data[] = $p;
}

$res->query = $keyword;
$res->suggestions = $products_name;
$res->data = $products_data;

header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");
print json_encode($res);
?>