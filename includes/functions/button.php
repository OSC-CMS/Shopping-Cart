<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

/**

*/
function buttonSubmit($img = '', $href = '', $alt = '', $param = '', $class = '')
{
	// смотрим, есть ли у нас картинка или нет
	$addImg	= (is_file(_THEMES_C.'buttons/'.$_SESSION['language'].'/'.$img)) ? $img : 'noimage.gif';

	// Задаем массив из параметров
	$a = array(
		'img'		=> $addImg,
		'alt'		=> (os_not_null($alt)) ? ' alt="'.os_parse_input_field_data($alt, array('"' => '&quot;')).'"' : '',
		'param'		=> $param,
		'class'		=> $class,
		'href'		=> (os_not_null($href)) ? $href : FILENAME_DEFAULT,
		'name'		=> (os_not_null($alt)) ? $alt : 'OK',
		'filter'	=> '',
	);

	// вешаем массив на фильтр, чтобы можно было изменять при желании параметры
	$a = apply_filter('button_submit', $a);

	// если filter пусто (нет обработки через фильтр)
	if (empty($a['filter']))
	{
		// делаем кнопку, если href пусто, а картинка нет
		if (empty($href) && !empty($img))
		{
			// использовать картинки в кнопках
			if (USE_IMAGE_SUBMIT == 'true')
				$a['filter'] = '<input class="imgsub '.$a['class'].'" type="image" src="'.os_parse_input_field_data('themes/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/'.$a['img'], array('"' => '&quot;')).'" '.$a['alt'].$a['param'].' />';
			// простая кнопка, без картинки
			else
				$a['filter'] = '<input class="imgsub btn '.$a['class'].'" type="submit" value="'.os_parse_input_field_data($a['name'], array('"' => '&quot;')).'" '.$a['param'].' />';
		}
		// если href не пусто, то делаем ссылку
		else
		{
			// если img не пусто и выставлено использование картинок, то делаем картинку в ссылке
			if (!empty($img) && USE_IMAGE_SUBMIT == 'true')
				$a['filter'] = '<a class="imglink '.$a['class'].'" href="'.$a['href'].'" '.$a['param'].'>'.os_image_button($a['img'], $a['name']).'</a>';
			// в противном случае выводим текст
			else
				$a['filter'] = '<a class="btn '.$a['class'].'" href="'.$a['href'].'" '.$a['param'].'>'.$a['name'].'</a>';
		}
	}

	// возвращаем результат filter
	return $a['filter'];
}

    function button_continue($_href = '')
    {
        if (empty($_href)) $_href = os_href_link(FILENAME_DEFAULT);

		$_array = array(
			'img' => 'button_continue.gif',
			'href' => $_href,
			'alt' => TEXT_BUTTON_CONTINUE,
			'code' => ''
		);

		$_array = apply_filter('button_continue', $_array);	

		if (empty($_array['code']))
		{
			$_array['code'] = buttonSubmit($_array['img'], $_array['href'], $_array['alt']);
		}

        return $_array['code'];
    }

    function button_continue_submit()
    {
        $_array = array(
			'img' => 'button_continue.gif',
			'href' => '',
			'alt' => TEXT_BUTTON_CONTINUE,
			'code' => ''
		);

        $_array = apply_filter('button_continue', $_array);	

        if (empty($_array['code']))
        {
			$_array['code'] = buttonSubmit($_array['img'], null, $_array['alt']);
        }

        return $_array['code'];	   
    }

?>