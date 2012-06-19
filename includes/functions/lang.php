<?php

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