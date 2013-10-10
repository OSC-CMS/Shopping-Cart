<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class csPagination extends db
{
	private $page;
	private $totalPages;
	private $maxPages;

	public function __construct()
	{
		$this->maxPages = 10;
	}

	public function setMaxPages($maxPages)
	{
		$this->maxPages = $maxPages;
	}

	public function pagerQuery($sql, $rowsPerPage)
	{
		$page = isset($_GET['p']) ? $_GET['p'] : 1;

		$result = $this->query($sql);

		$totalRows = $this->num_rows($result);
		$this->totalPages = intval($totalRows/$rowsPerPage) + ($totalRows%$rowsPerPage==0 ? 0 : 1);

		if ($this->totalPages < 1)
		{
			$this->totalPages = 1;
		}

		$this->page = intval($page);
		if ($this->page < 1)
		{
			$this->page = 1;
		}

		if ($this->page > $this->totalPages)
		{
			$this->page = $this->totalPages;
		}

		$this->page -= 1;
		if ($this->page < 0)
		{
			$this->page = 0;
		}

		$result = $this->query($sql." LIMIT ".$this->page*$rowsPerPage.", ".$rowsPerPage);
		$this->page += 1;

		return $result;
	}

	public function createPaging($link)
	{
		$start = ((($this->page%$this->maxPages == 0) ? ($this->page/$this->maxPages) : intval($this->page/$this->maxPages)+1)-1)*$this->maxPages+1;
		$end = ((($start+$this->maxPages-1)<=$this->totalPages) ? ($start+$this->maxPages-1) : $this->totalPages);

		if ($this->page > 1)
		{
			$paging .= '<a class="btn" href="'.$link.'" title="Первая">Первая</a>';
		}

		if ($start > $this->maxPages)
		{
			$paging .= '<a class="btn" href="'.$link.'&p='.($start-1).'" title="Назад '.$this->maxPages.'">Назад '.$this->maxPages.'</a>';
		}

		for ($i = $start; $i <= $end; $i++)
		{
			if($this->page == $i)
				$paging .= '<a class="btn active" href="'.$link.'" title="Страница '.$i.'">'.$i.'</a>';
			else
				if ($i != 1)
					$paging .= '<a class="btn" href="'.$link.'&p='.$i.'" title="Страница '.$i.'">'.$i.'</a>';
				else
					$paging .= '<a class="btn" href="'.$link.'" title="Страница '.$i.'">'.$i.'</a>';
		}

		if ($end < $this->totalPages)
		{
			$paging .= '<a class="btn" href="'.$link.'&p='.($end+1).'" title="Еще '.$this->maxPages.'">Еще '.$this->maxPages.'</a>';
		}

		if($this->page < $this->totalPages)
		{
			$paging .= '<a class="btn" href="'.$link.'&p='.$this->totalPages.'" title="Последняя">Последняя</a>';
		}

		return $paging;
	}
}
?>