<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class breadcrumb 
{
    var $_trail;

    function breadcrumb() 
    {
        $this->reset();
    }

    function reset() 
    {
        $this->_trail = array();
    }

    function add($title, $link = '') 
    {
        $this->_trail[] = array('title' => $title, 'link' => $link);
    }

    function trail($separator = '') 
    {
        $array = array();
        $array['trail'] = $this->_trail;
        $array['separator'] = $separator;

        $separator = $array['separator'];
        $this->_trail = $array['trail'];

        $trail_string = '';

        for ($i=0, $n=sizeof($this->_trail); $i<$n; $i++) {
            if (isset($this->_trail[$i]['link']) && os_not_null($this->_trail[$i]['link'])) {
                $trail_string .= '<a href="'.$this->_trail[$i]['link'].'">'.$this->_trail[$i]['title'].'</a>';
            } else {
                $trail_string .= $this->_trail[$i]['title'];
            }

            if (($i+1) < $n) $trail_string .= $separator;
        }

        return $trail_string;
    }
}
?>