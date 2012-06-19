<?php
function smarty_modifier_os_truncate($string, $length = 80, $etc = '...',
                                  $break_words = false, $middle = false)
{

$string = strip_tags($string); 

    if ($length == 0)
        return '';

    if (strlen($string) > $length) {
        $length -= min($length, utf8_strlen($etc));
        if (!$break_words && !$middle) {
            $string = preg_replace('/\s+?(\S+)?$/', '', utf8_substr($string, 0, $length+1));
        }
        if(!$middle) {
            return utf8_substr($string, 0, $length) . $etc;
        } else {
            return utf8_substr($string, 0, $length/2) . $etc . utf8_substr($string, -$length/2);
        }
    } else {
        return $string;
    }
}


?>