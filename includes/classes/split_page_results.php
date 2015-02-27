<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

class splitPageResults
{
	var $sql_query, $number_of_rows, $current_page_number, $number_of_pages, $number_of_rows_per_page;

	function splitPageResults($query, $page, $max_rows, $count_key = '*') 
	{
		$this->sql_query = $query;

		if (empty($page) || (is_numeric($page) == false))
			$page = 1;

		$this->current_page_number = $page;

		$this->number_of_rows_per_page = $max_rows;

		$pos_to = strlen($this->sql_query);
		$pos_from = strpos($this->sql_query, ' FROM', 0);

		$pos_group_by = strpos($this->sql_query, ' GROUP BY', $pos_from);

		if (($pos_group_by < $pos_to) && ($pos_group_by != false))
			$pos_to = $pos_group_by;

		$pos_having = strpos($this->sql_query, ' HAVING', $pos_from);

		if (($pos_having < $pos_to) && ($pos_having != false))
			$pos_to = $pos_having;

		$pos_order_by = strpos($this->sql_query, ' ORDER BY', $pos_from);
		if (($pos_order_by < $pos_to) && ($pos_order_by != false)) $pos_to = $pos_order_by;

		if (strpos($this->sql_query, 'DISTINCT') || strpos($this->sql_query, 'GROUP BY'))
			$count_string = 'DISTINCT '.os_db_input($count_key);
		else
			$count_string = os_db_input($count_key);

		$count_query = osDBquery($query);
		$count = os_db_num_rows($count_query,true);

		$this->number_of_rows = $count;
		$this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);

		if ($this->current_page_number > $this->number_of_pages)
		{
			$this->current_page_number = $this->number_of_pages;
		}

		$offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

		$this->sql_query .= " LIMIT ".max($offset, 0).", ".$this->number_of_rows_per_page;
	}

	function display_links($max_page_links, $parameters = '', $page = 'page')
	{
		global $PHP_SELF, $request_type;
		$tpl = new osTemplate;

		if (os_not_null($parameters) && (substr($parameters, -1) != '&'))
			$parameters .= '&';

		$prev = array();
		if ($this->current_page_number > 1)
		{
			$_prev = (($this->current_page_number - 1) != 1) ? $page. '='.($this->current_page_number - 1): '';
			$prev = array(
				'title' => PREVNEXT_BUTTON_PREV,
				'link' => os_href_link(basename($PHP_SELF), $parameters .$_prev, $request_type),
			);
		}

		$cur_window_num = intval($this->current_page_number / $max_page_links);

		if ($this->current_page_number % $max_page_links)
			$cur_window_num++;

		$max_window_num = intval($this->number_of_pages / $max_page_links);

		if ($this->number_of_pages % $max_page_links)
			$max_window_num++;

		$prev_cur = array();
		if ($cur_window_num > 1)
		{
			$prev_cur = array(
				'title' => '...',
				'link' => os_href_link(basename($PHP_SELF), $parameters .$page. '='.(($cur_window_num - 1) * $max_page_links), $request_type),
			);
		}

		$pagination = array();
		for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++)
		{
			$_jump_to_page = ($jump_to_page != 1) ? $page.'='.$jump_to_page : '';
			$link = ($jump_to_page == $this->current_page_number) ? '' : os_href_link(basename($PHP_SELF), $parameters .$_jump_to_page, $request_type);
			$pagination[] = array(
				'title' => $jump_to_page,
				'link' => $link,
			);
		}

		$next_cur = array();
		if ($cur_window_num < $max_window_num)
		{
			$next_cur = array(
				'title' => '...',
				'link' => os_href_link(basename($PHP_SELF), $parameters .$page. '='.(($cur_window_num) * $max_page_links + 1), $request_type).'" class="pageResults" title=" '.sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links),
			);
		}

		$next = array();
		if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1))
		{
			$next = array(
				'title' => PREVNEXT_BUTTON_NEXT,
				'link' => os_href_link(basename($PHP_SELF), $parameters .$page. '='.($this->current_page_number + 1), $request_type),
			);
		}

		// Передаем в файл шаблона pagination.html
		$tpl->assign('prev_cur', $prev_cur);
		$tpl->assign('prev', $prev);
		$tpl->assign('next_cur', $next_cur);
		$tpl->assign('next', $next);
		$tpl->assign('pagination', $pagination);

		$tpl->assign('pages', $this->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS));

		// Файл шаблона не кэшируем
		$tpl->caching = 0;
		$getTemplate = $tpl->fetch(CURRENT_TEMPLATE.'/module/pagination.html');

		return $getTemplate;
	}

	function display_count($text_output)
	{
		$to_num = ($this->number_of_rows_per_page * $this->current_page_number);

		if ($to_num > $this->number_of_rows)
			$to_num = $this->number_of_rows;

		$from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

		if ($to_num == 0)
			$from_num = 0;
		else
			$from_num++;

		return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
	}
}