<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
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