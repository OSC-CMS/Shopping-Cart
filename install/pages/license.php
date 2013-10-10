<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function step($is_submit){

    if ($is_submit){
        return check_agree();
    }

    $license_text = file_get_contents(PATH.'languages'.DS.LANG.DS."license.txt");

    $result = array('html' => display('license', array('license_text' => $license_text,)));

    return $result;

}

function check_agree(){

    $error = !isset($_POST['agree']);

    return array(
        'error' => $error,
        'message' => t('license_3')
    );

}
