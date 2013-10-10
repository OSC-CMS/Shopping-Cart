<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiTree extends CartET
{
	var $data;

	function addItem($id, $parent, $li_attr, $label)
	{
		$this->data[$parent][] = array(
			'id' => $id,
			'li_attr' => $li_attr,
			'label' => $label
		);
	}

	function get($arr = array())
	{
		if (is_array($this->data[$arr['parent']]))
		{
			$html = '<ul '.$arr['ul'].'>';
			foreach ($this->data[$arr['parent']] as $row)
			{
				$child = $this->get(array('parent' => $row['id']));
				$html .= '<li'. $row['li_attr'] . '>';
				$html .= $row['label'];
				if ($child)
				{
					$html .= $child;
				}
				$html .= '</li>';
			}
			$html .= '</ul>';
			return $html;
		}
		else
			return false;
	}

	function clear()
	{
		$this->data = array();
	}
}
?>