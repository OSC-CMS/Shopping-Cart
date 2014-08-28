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