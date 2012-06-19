<?php

 
$post_data = $HTTP_RAW_POST_DATA;

$header[] = "Content-type: application/json";
$header[] = "Content-length: ".strlen($post_data);

$ch = curl_init( 'http://speller.yandex.net/services/tinyspell '); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

if ( strlen($post_data)>0 ){
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
}

$response = curl_exec($ch);     

if (curl_errno($ch)) {
    print curl_error($ch);
} else {
    curl_close($ch);
    print $response;
}


?>