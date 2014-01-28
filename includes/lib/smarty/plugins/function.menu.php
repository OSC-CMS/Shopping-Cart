<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function smarty_function_menu($group)
{
	global $cartet;

	$menu = $cartet->menu->getByGroupId(array('group_id' => $group['id'], 'status' => true));
	if ($menu)
	{
		foreach ($menu as $row)
		{
			$item = '<a href="'.$row['menu_url'].'">'.$row['lang_title'].'</a>';

			$cartet->tree->addItem(
				$row['menu_id'],
				$row['menu_parent_id'],
				' class="menu_'.$group['id'].' item_'.$row['menu_id'].'"',
				$item
			);
		}

		$class = ($group['class']) ? 'class="'.$group['class'].'"' : '';

		$result = $cartet->tree->get(array(
			'parent' => 0,
			'ul' => $class
		));
		$cartet->tree->clear();
		return $result;
	}
}

?>