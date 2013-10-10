<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function step($is_submit=false){

    $host = $_SESSION['install']['hosts']['root'];

    unset($_SESSION['install']);

    $result = array('html' => display('finish', array('host' => $host)));

    return $result;

}
