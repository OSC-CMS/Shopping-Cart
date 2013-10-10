<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function lang( $code )
{
   global $lang;

}

function __( $value )
{
   global $lang;
   if (isset($lang[$value])) return $lang[$value];
}

?>