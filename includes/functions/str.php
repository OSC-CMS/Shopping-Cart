<?php

function os_substr($str, $start, $len = '', $encoding="UTF-8")
{
        $limit = strlen($str);
        for ($s = 0; $start > 0;--$start) {
            if ($s >= $limit)
                break;
            if ($str[$s] <= "\x7F")
                ++$s;
            else {
                ++$s; 
                while ($str[$s] >= "\x80" && $str[$s] <= "\xBF")
                    ++$s;
            }
        }
        if ($len == '')
            return substr($str, $s);
        else
            for ($e = $s; $len > 0; --$len) {
                if ($e >= $limit)
                    break;
                if ($str[$e] <= "\x7F")
                    ++$e;
                else {
                    ++$e;
                    while ($str[$e] >= "\x80" && $str[$e] <= "\xBF" && $e < $limit)
                        ++$e;
                       }
            }
        return substr($str, $s, $e - $s);
}
 
?>