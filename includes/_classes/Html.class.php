<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class apiHtml extends CartET
{
	/**
	 *
	 *
	 * @param string $name
	 * @param string $action
	 * @param string $parameters
	 * @param string $method
	 * @param array $params
	 * @return string
	 */
	function form($name, $action, $parameters = '', $method = 'post', $params = '')
	{
		$form = '<form name="'.$name.'" action="';
		if ($parameters)
			$form .= os_href_link($action, $parameters);
		else
			$form .= os_href_link($action);

		$form .= '" method="'.$method.'"';

		$param = '';
		if (is_array($params) && !empty($params))
		{
			foreach ($params as $param_name => $param_value)
			{
				$param .= ' '.$param_name.'="'.$param_value.'"';
			}
		}

		$form .= $param.'>';

		return $form;
	}

	/**
	 *
	 *
	 * @param string $name название поля
	 * @param string $value значение поля
	 * @param array $params параметры
	 * @param $required обязательное
	 * @param $reinsert сохранять исходное значение
	 * @param string $placeholder текст по умолчанию
	 * @return string
	 */
	function textarea($name, $value = '', $params = '', $required = false, $reinsert = true, $placeholder = '')
	{
		$param = '';
		if (is_array($params) && !empty($params))
		{
			foreach ($params as $param_name => $param_value)
			{
				$param .= ' '.$param_name.'="'.$param_value.'"';
			}
		}

		$ph = ($placeholder) ? 'placeholder="'.$placeholder.'"' : '';

		$field = '<textarea name="'.$name.'" '.$param.'>';

		if (isset($GLOBALS[$name]) && ($GLOBALS[$name]) && ($reinsert))
			$field .= $GLOBALS[$name];
		elseif ($value != '')
			$field .= $value;

			$field .= $ph;

		$field .= '</textarea>';

		if ($required) $field .= TEXT_FIELD_REQUIRED;

		return $field;
	}

	/**
	 *
	 *
	 * @param string $name название поля
	 * @param string $value значение поля
	 * @param array $params параметры
	 * @param $required обязательное
	 * @param $reinsert сохранять исходное значение
	 * @param string $placeholder текст по умолчанию
	 * @return string
	 */
	function input_text($name, $value = '', $params = '', $required = false, $reinsert = true, $placeholder = '')
	{
		if (isset($GLOBALS[$name]) && ($GLOBALS[$name]) && ($reinsert))
			$value = htmlspecialchars(trim($GLOBALS[$name]));
		elseif ($value != '')
			$value = htmlspecialchars(trim($value));

		$param = '';
		if (is_array($params) && !empty($params))
		{
			foreach ($params as $param_name => $param_value)
			{
				$param .= ' '.$param_name.'="'.$param_value.'"';
			}
		}

		$ph = ($placeholder) ? 'placeholder="'.$placeholder.'"' : '';

		$iRequired = ($required) ? 'required' : '';
		$field = '<input type="text" name="'.$name.'" value="'.$value.'" '.$iRequired.' '.$param.' '.$ph.' />';

		if ($required) $field .= TEXT_FIELD_REQUIRED;

		return $field;
	}

	/**
	 *
	 *
	 * @param string $name название поля
	 * @param string $value значение поля
	 * @param array $params параметры
	 * @param $required обязательное
	 * @param $reinsert сохранять исходное значение
	 * @param string $placeholder текст по умолчанию
	 * @return string
	 */
	function input($name, $value = '', $params = '', $required = false, $reinsert = true, $placeholder = '')
	{
		if (isset($GLOBALS[$name]) && ($GLOBALS[$name]) && ($reinsert))
			$value = htmlspecialchars(trim($GLOBALS[$name]));
		elseif ($value != '')
			$value = htmlspecialchars(trim($value));

		$param = '';
		if (is_array($params) && !empty($params))
		{
			foreach ($params as $param_name => $param_value)
			{
				$param .= ' '.$param_name.'="'.$param_value.'"';
			}
		}

		$ph = ($placeholder) ? 'placeholder="'.$placeholder.'"' : '';

		$iRequired = ($required) ? 'required' : '';
		$field = '<input name="'.$name.'" value="'.$value.'" '.$iRequired.' '.$param.' '.$ph.' />';

		if ($required) $field .= TEXT_FIELD_REQUIRED;

		return $field;
	}

	/**
	 *
	 *
	 * @param string $name название поля
	 * @param string $value значение поля
	 * @param array $params параметры
	 * @return string
	 */
	function input_submit($name, $value = '', $params = '')
	{
		$param = '';
		if (is_array($params) && !empty($params))
		{
			foreach ($params as $param_name => $param_value)
			{
				$param .= ' '.$param_name.'="'.$param_value.'"';
			}
		}

		$field = '<input type="submit" name="'.$name.'" value="'.$value.'" '.$param.' />';

		return $field;
	}

	/**
	 *
	 *
	 * @return string
	 */
	public function select($name, $values, $default = '', $params = '', $required = false) {

		$param = '';
		if (is_array($params) && !empty($params))
		{
			foreach ($params as $param_name => $param_value)
			{
				$param .= ' '.$param_name.'="'.$param_value.'"';
			}
		}

		$field = '<select '.$param.' name="'.$name.'" >';

		if (empty($default) && isset($GLOBALS[$name]))
			$default = $GLOBALS[$name];

		for ($i=0, $n=sizeof($values); $i<$n; $i++) 
		{
			$field .= '<option value="'.$values[$i]['id'].'"';

			if ($default == $values[$i]['id']) 
			{
				$field .= ' selected="selected"';
			}

			if ($values[$i]['subcats'] == 1) 
			{
				$field .= ' disabled="disabled"';
			}

			$field .= '>'.$values[$i]['text'].'</option>'."\n";
		}
		$field .= '</select>'."\n\n";

		if ($required == true) $field .= TEXT_FIELD_REQUIRED;

		return $field;
	}

	/**
	 *
	 *
	 * @param string $name название ссылки
	 * @param string $href ссылка
	 * @param array $params параметры
	 * @return string
	 */
	public function link($name, $href = 'javascript:void(null)', $params = '')
	{
		$param = '';
		if (is_array($params) && !empty($params))
		{
			foreach ($params as $param_name => $param_value)
			{
				$param .= ' '.$param_name.'="'.$param_value.'"';
			}
		}

		$link = '<a href="'.$href.'" '.$param.' >'.$name.'</a>';
    	return $link;
	}
}
?>