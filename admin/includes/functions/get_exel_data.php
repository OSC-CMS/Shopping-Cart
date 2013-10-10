<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

function get_exel_data($file = "") {
    if (empty($file)) return false;

    $exc = new ExcelFileParser();
    $res = $exc->ParseFromFile( $file );

    switch ($res) {
        case 0: break;
        case 1: return ERROR_OPENFILE;
        case 2: return ERROR_SMALLFILE;
        case 3: return ERROR_HEADERFILE;
        case 4: return ERROR_READFILE;
        case 5: return ERROR_FORMATFILE;
        case 6: return ERROR_BADFILE;
        case 7: return ERROR_BADDATA;
        case 8: return ERROR_VERSIONFILE;

        default:
            return ERROR_UNKNOWN;
    }

    $ws = $exc->worksheet['data'][0];
    $data = array();
    foreach($exc->worksheet['data'] as $page_num => $page) {
          if( $exc->worksheet['unicode'][$page_num] ) {
              $page_name = uc2html($exc->worksheet['name'][$page_num]);
          } else {
              $page_name = $exc->worksheet['name'][$page_num];
        }
        $data[$page_num]["pagename"] = $page_name;
        $data[$page_num]["pagenum"] = $page_num;
        $data[$page_num]["rows"] = sizeof($page['cell']);
        foreach($page['cell'] as $row_num => $row) {
            if (sizeof($row) > $data[$page_num]["cells"]) $data[$page_num]["cells"] = sizeof($row)-1;
            if (sizeof($row) > $max_cells) $max_cells = sizeof($row)-1;
            foreach($row as $cell_num => $cell) {
                $data[$page_num]["data"][$row_num][$cell_num] = get_data($cell, $exc);
            }
        }
        foreach($data[$page_num]["data"] as $row_num => $cells) {
            reset($cells);
            $temp = array();
            for($i = 0; $i <= $max_cells; $i++) {
               if (!isset($cells[$i])) {
                   $temp[$i] = "";
               } else {
                   $temp[$i] = $cells[$i];
               }
               $data[$page_num]["data"][$row_num] = $temp;
            }

            ksort($data[$page_num]["data"][$row_num]);
        }
    }

    $data = convert_charcode($data, "win1251", "utf-8");
    return $data;
}


function uc2html($str) {
    $recode = array(
    0x0402,0x0403,0x201A,0x0453,0x201E,0x2026,0x2020,0x2021,
    0x20AC,0x2030,0x0409,0x2039,0x040A,0x040C,0x040B,0x040F,
    0x0452,0x2018,0x2019,0x201C,0x201D,0x2022,0x2013,0x2014,
    0x0000,0x2122,0x0459,0x203A,0x045A,0x045C,0x045B,0x045F,
    0x00A0,0x040E,0x045E,0x0408,0x00A4,0x0490,0x00A6,0x00A7,
    0x0401,0x00A9,0x0404,0x00AB,0x00AC,0x00AD,0x00AE,0x0407,
    0x00B0,0x00B1,0x0406,0x0456,0x0491,0x00B5,0x00B6,0x00B7,
    0x0451,0x2116,0x0454,0x00BB,0x0458,0x0405,0x0455,0x0457,
    0x0410,0x0411,0x0412,0x0413,0x0414,0x0415,0x0416,0x0417,
    0x0418,0x0419,0x041A,0x041B,0x041C,0x041D,0x041E,0x041F,
    0x0420,0x0421,0x0422,0x0423,0x0424,0x0425,0x0426,0x0427,
    0x0428,0x0429,0x042A,0x042B,0x042C,0x042D,0x042E,0x042F,
    0x0430,0x0431,0x0432,0x0433,0x0434,0x0435,0x0436,0x0437,
    0x0438,0x0439,0x043A,0x043B,0x043C,0x043D,0x043E,0x043F,
    0x0440,0x0441,0x0442,0x0443,0x0444,0x0445,0x0446,0x0447,
    0x0448,0x0449,0x044A,0x044B,0x044C,0x044D,0x044E,0x044F
    );

    $ret = '';
    for( $i=0; $i<strlen($str)/2; $i++ ) {
        $charcode = ord($str[$i*2])+256*ord($str[$i*2+1]);
        //$ret .= '&#'.$charcode;
        if ($charcode < 0x80) {
                $ret .= chr($charcode);
        } else {
                if (in_array($charcode, $recode)) {
                    $ret .= chr(array_search($charcode,$recode)+128);
                }
        }

    }
    return $ret;
}

function get_data($data, $exc) {
       switch ($data['type']) {
        // строка
        case 0:
            $ind = $data['data'];
            if( $exc->sst['unicode'][$ind] ) {
                $s = $exc->sst['data'][$ind];
                $s = uc2html($s);
            } else
                $s = $exc->sst['data'][$ind];
            if( strlen(trim($s))==0 )
                $s = "&nbsp;";
            break;
        //целое число
        case 1:
            $s = (int)($data['data']);
            break;
        //вещественное число
        case 2:
            $s = (float)($data['data']);
            break;
        // дата
        case 3:
            $ret = $exc->getDateArray($data['data']);
            $s = sprintf ("%s-%s-%s",$ret['day'], $ret['month'], $ret['year']);
            break;
       }
       return $s;
}

function utf_to_win($str,$to = "w") {
    if (function_exists('iconv'))
    {
        return iconv("UTF-8", $to == 'w' ? "WINDOWS-1251" : "KOI8-R", $str);
    }
    if (function_exists('mb_convert_encoding'))
    {
        return mb_convert_encoding($str, $to == 'w' ? "WINDOWS-1251" : "KOI8-R", "UTF-8");
    }

    $outstr='';
    $recode=array();
    $recode['k']=array(
    0x2500,0x2502,0x250c,0x2510,0x2514,0x2518,0x251c,0x2524,
    0x252c,0x2534,0x253c,0x2580,0x2584,0x2588,0x258c,0x2590,
    0x2591,0x2592,0x2593,0x2320,0x25a0,0x2219,0x221a,0x2248,
    0x2264,0x2265,0x00a0,0x2321,0x00b0,0x00b2,0x00b7,0x00f7,
    0x2550,0x2551,0x2552,0x0451,0x2553,0x2554,0x2555,0x2556,
    0x2557,0x2558,0x2559,0x255a,0x255b,0x255c,0x255d,0x255e,
    0x255f,0x2560,0x2561,0x0401,0x2562,0x2563,0x2564,0x2565,
    0x2566,0x2567,0x2568,0x2569,0x256a,0x256b,0x256c,0x00a9,
    0x044e,0x0430,0x0431,0x0446,0x0434,0x0435,0x0444,0x0433,
    0x0445,0x0438,0x0439,0x043a,0x043b,0x043c,0x043d,0x043e,
    0x043f,0x044f,0x0440,0x0441,0x0442,0x0443,0x0436,0x0432,
    0x044c,0x044b,0x0437,0x0448,0x044d,0x0449,0x0447,0x044a,
    0x042e,0x0410,0x0411,0x0426,0x0414,0x0415,0x0424,0x0413,
    0x0425,0x0418,0x0419,0x041a,0x041b,0x041c,0x041d,0x041e,
    0x041f,0x042f,0x0420,0x0421,0x0422,0x0423,0x0416,0x0412,
    0x042c,0x042b,0x0417,0x0428,0x042d,0x0429,0x0427,0x042a
    );
    $recode['w']=array(
    0x0402,0x0403,0x201A,0x0453,0x201E,0x2026,0x2020,0x2021,
    0x20AC,0x2030,0x0409,0x2039,0x040A,0x040C,0x040B,0x040F,
    0x0452,0x2018,0x2019,0x201C,0x201D,0x2022,0x2013,0x2014,
    0x0000,0x2122,0x0459,0x203A,0x045A,0x045C,0x045B,0x045F,
    0x00A0,0x040E,0x045E,0x0408,0x00A4,0x0490,0x00A6,0x00A7,
    0x0401,0x00A9,0x0404,0x00AB,0x00AC,0x00AD,0x00AE,0x0407,
    0x00B0,0x00B1,0x0406,0x0456,0x0491,0x00B5,0x00B6,0x00B7,
    0x0451,0x2116,0x0454,0x00BB,0x0458,0x0405,0x0455,0x0457,
    0x0410,0x0411,0x0412,0x0413,0x0414,0x0415,0x0416,0x0417,
    0x0418,0x0419,0x041A,0x041B,0x041C,0x041D,0x041E,0x041F,
    0x0420,0x0421,0x0422,0x0423,0x0424,0x0425,0x0426,0x0427,
    0x0428,0x0429,0x042A,0x042B,0x042C,0x042D,0x042E,0x042F,
    0x0430,0x0431,0x0432,0x0433,0x0434,0x0435,0x0436,0x0437,
    0x0438,0x0439,0x043A,0x043B,0x043C,0x043D,0x043E,0x043F,
    0x0440,0x0441,0x0442,0x0443,0x0444,0x0445,0x0446,0x0447,
    0x0448,0x0449,0x044A,0x044B,0x044C,0x044D,0x044E,0x044F
    );
    $recode['i']=array(
    0x0080,0x0081,0x0082,0x0083,0x0084,0x0085,0x0086,0x0087,
    0x0088,0x0089,0x008A,0x008B,0x008C,0x008D,0x008E,0x008F,
    0x0090,0x0091,0x0092,0x0093,0x0094,0x0095,0x0096,0x0097,
    0x0098,0x0099,0x009A,0x009B,0x009C,0x009D,0x009E,0x009F,
    0x00A0,0x0401,0x0402,0x0403,0x0404,0x0405,0x0406,0x0407,
    0x0408,0x0409,0x040A,0x040B,0x040C,0x00AD,0x040E,0x040F,
    0x0410,0x0411,0x0412,0x0413,0x0414,0x0415,0x0416,0x0417,
    0x0418,0x0419,0x041A,0x041B,0x041C,0x041D,0x041E,0x041F,
    0x0420,0x0421,0x0422,0x0423,0x0424,0x0425,0x0426,0x0427,
    0x0428,0x0429,0x042A,0x042B,0x042C,0x042D,0x042E,0x042F,
    0x0430,0x0431,0x0432,0x0433,0x0434,0x0435,0x0436,0x0437,
    0x0438,0x0439,0x043A,0x043B,0x043C,0x043D,0x043E,0x043F,
    0x0440,0x0441,0x0442,0x0443,0x0444,0x0445,0x0446,0x0447,
    0x0448,0x0449,0x044A,0x044B,0x044C,0x044D,0x044E,0x044F,
    0x2116,0x0451,0x0452,0x0453,0x0454,0x0455,0x0456,0x0457,
    0x0458,0x0459,0x045A,0x045B,0x045C,0x00A7,0x045E,0x045F
    );
    $recode['a']=array(
    0x0410,0x0411,0x0412,0x0413,0x0414,0x0415,0x0416,0x0417,
    0x0418,0x0419,0x041a,0x041b,0x041c,0x041d,0x041e,0x041f,
    0x0420,0x0421,0x0422,0x0423,0x0424,0x0425,0x0426,0x0427,
    0x0428,0x0429,0x042a,0x042b,0x042c,0x042d,0x042e,0x042f,
    0x0430,0x0431,0x0432,0x0433,0x0434,0x0435,0x0436,0x0437,
    0x0438,0x0439,0x043a,0x043b,0x043c,0x043d,0x043e,0x043f,
    0x2591,0x2592,0x2593,0x2502,0x2524,0x2561,0x2562,0x2556,
    0x2555,0x2563,0x2551,0x2557,0x255d,0x255c,0x255b,0x2510,
    0x2514,0x2534,0x252c,0x251c,0x2500,0x253c,0x255e,0x255f,
    0x255a,0x2554,0x2569,0x2566,0x2560,0x2550,0x256c,0x2567,
    0x2568,0x2564,0x2565,0x2559,0x2558,0x2552,0x2553,0x256b,
    0x256a,0x2518,0x250c,0x2588,0x2584,0x258c,0x2590,0x2580,
    0x0440,0x0441,0x0442,0x0443,0x0444,0x0445,0x0446,0x0447,
    0x0448,0x0449,0x044a,0x044b,0x044c,0x044d,0x044e,0x044f,
    0x0401,0x0451,0x0404,0x0454,0x0407,0x0457,0x040e,0x045e,
    0x00b0,0x2219,0x00b7,0x221a,0x2116,0x00a4,0x25a0,0x00a0
    );
    $recode['d']=$recode['a'];
    $recode['m']=array(
    0x0410,0x0411,0x0412,0x0413,0x0414,0x0415,0x0416,0x0417,
    0x0418,0x0419,0x041A,0x041B,0x041C,0x041D,0x041E,0x041F,
    0x0420,0x0421,0x0422,0x0423,0x0424,0x0425,0x0426,0x0427,
    0x0428,0x0429,0x042A,0x042B,0x042C,0x042D,0x042E,0x042F,
    0x2020,0x00B0,0x00A2,0x00A3,0x00A7,0x2022,0x00B6,0x0406,
    0x00AE,0x00A9,0x2122,0x0402,0x0452,0x2260,0x0403,0x0453,
    0x221E,0x00B1,0x2264,0x2265,0x0456,0x00B5,0x2202,0x0408,
    0x0404,0x0454,0x0407,0x0457,0x0409,0x0459,0x040A,0x045A,
    0x0458,0x0405,0x00AC,0x221A,0x0192,0x2248,0x2206,0x00AB,
    0x00BB,0x2026,0x00A0,0x040B,0x045B,0x040C,0x045C,0x0455,
    0x2013,0x2014,0x201C,0x201D,0x2018,0x2019,0x00F7,0x201E,
    0x040E,0x045E,0x040F,0x045F,0x2116,0x0401,0x0451,0x044F,
    0x0430,0x0431,0x0432,0x0433,0x0434,0x0435,0x0436,0x0437,
    0x0438,0x0439,0x043A,0x043B,0x043C,0x043D,0x043E,0x043F,
    0x0440,0x0441,0x0442,0x0443,0x0444,0x0445,0x0446,0x0447,
    0x0448,0x0449,0x044A,0x044B,0x044C,0x044D,0x044E,0x00A4
    );
    $and=0x3F;
    for ($i=0;$i<strlen($str);$i++) {
        $letter=0x0;
        $octet=array();
        $octet[0]=ord($str[$i]);
        $octets=1;
        $andfirst=0x7F;
        if (($octet[0]>>1)==0x7E) {
            $octets=6;
            $andfirst=0x1;
        } elseif (($octet[0]>>2)==0x3E) {
            $octets=5;
            $andfirst=0x3;
        } elseif (($octet[0]>>3)==0x1E) {
            $octets=4;
            $andfirst=0x7;
        } elseif (($octet[0]>>4)==0xE) {
            $octets=3;
            $andfirst=0xF;
        } elseif (($octet[0]>>5)==0x6) {
            $octets=2;
            $andfirst=0x1F;
        }
        $octet[0]=$octet[0] & $andfirst;
        $octet[0]=$octet[0] << ($octets-1)*6;
        $letter+=$octet[0];
        for ($j=1;$j<$octets;$j++) {
            $i++;
            $octet[$j]=ord($str[$i]) & $and;
            $octet[$j]=$octet[$j] << ($octets-1-$j)*6;
            $letter+=$octet[$j];
        }
        if ($letter<0x80) {
            $outstr.=chr($letter);
        } else {
            if (in_array($letter,$recode[$to])) {
                $outstr.=chr(array_search($letter,$recode[$to])+128);
            }
        }
    }
    return($outstr);
}

function win_to_utf($str, $from = "w") {
    if (function_exists('iconv'))
    {
        return iconv($from == 'w' ? "WINDOWS-1251" : "KOI8-R", "UTF-8", $str);
    }
    if (function_exists('mb_convert_encoding'))
    {
        return mb_convert_encoding($str, "UTF-8", $from == 'w' ? "WINDOWS-1251" : "KOI8-R");
    }

    $recode['w']=array(
    0x0402,0x0403,0x201A,0x0453,0x201E,0x2026,0x2020,0x2021,
    0x20AC,0x2030,0x0409,0x2039,0x040A,0x040C,0x040B,0x040F,
    0x0452,0x2018,0x2019,0x201C,0x201D,0x2022,0x2013,0x2014,
    0x0000,0x2122,0x0459,0x203A,0x045A,0x045C,0x045B,0x045F,
    0x00A0,0x040E,0x045E,0x0408,0x00A4,0x0490,0x00A6,0x00A7,
    0x0401,0x00A9,0x0404,0x00AB,0x00AC,0x00AD,0x00AE,0x0407,
    0x00B0,0x00B1,0x0406,0x0456,0x0491,0x00B5,0x00B6,0x00B7,
    0x0451,0x2116,0x0454,0x00BB,0x0458,0x0405,0x0455,0x0457,
    0x0410,0x0411,0x0412,0x0413,0x0414,0x0415,0x0416,0x0417,
    0x0418,0x0419,0x041A,0x041B,0x041C,0x041D,0x041E,0x041F,
    0x0420,0x0421,0x0422,0x0423,0x0424,0x0425,0x0426,0x0427,
    0x0428,0x0429,0x042A,0x042B,0x042C,0x042D,0x042E,0x042F,
    0x0430,0x0431,0x0432,0x0433,0x0434,0x0435,0x0436,0x0437,
    0x0438,0x0439,0x043A,0x043B,0x043C,0x043D,0x043E,0x043F,
    0x0440,0x0441,0x0442,0x0443,0x0444,0x0445,0x0446,0x0447,
    0x0448,0x0449,0x044A,0x044B,0x044C,0x044D,0x044E,0x044F
    );

    $recode['k']=array(
    0x2500,0x2502,0x250c,0x2510,0x2514,0x2518,0x251c,0x2524,
    0x252c,0x2534,0x253c,0x2580,0x2584,0x2588,0x258c,0x2590,
    0x2591,0x2592,0x2593,0x2320,0x25a0,0x2219,0x221a,0x2248,
    0x2264,0x2265,0x00a0,0x2321,0x00b0,0x00b2,0x00b7,0x00f7,
    0x2550,0x2551,0x2552,0x0451,0x2553,0x2554,0x2555,0x2556,
    0x2557,0x2558,0x2559,0x255a,0x255b,0x255c,0x255d,0x255e,
    0x255f,0x2560,0x2561,0x0401,0x2562,0x2563,0x2564,0x2565,
    0x2566,0x2567,0x2568,0x2569,0x256a,0x256b,0x256c,0x00a9,
    0x044e,0x0430,0x0431,0x0446,0x0434,0x0435,0x0444,0x0433,
    0x0445,0x0438,0x0439,0x043a,0x043b,0x043c,0x043d,0x043e,
    0x043f,0x044f,0x0440,0x0441,0x0442,0x0443,0x0436,0x0432,
    0x044c,0x044b,0x0437,0x0448,0x044d,0x0449,0x0447,0x044a,
    0x042e,0x0410,0x0411,0x0426,0x0414,0x0415,0x0424,0x0413,
    0x0425,0x0418,0x0419,0x041a,0x041b,0x041c,0x041d,0x041e,
    0x041f,0x042f,0x0420,0x0421,0x0422,0x0423,0x0416,0x0412,
    0x042c,0x042b,0x0417,0x0428,0x042d,0x0429,0x0427,0x042a
    );

    for ($i=0;$i<strlen($str);$i++) {
        $letter = ord($str[$i]);
        if ($letter>=0x80) {
           $letter -= 128;
           $c2 = "10111111" & "10".substr(decbin($recode[$from][$letter]), -6);
           $c1 = "11011111" & "110".str_repeat("0", 5-strlen(substr(decbin($recode[$from][$letter]), 0, -6))).substr(decbin($recode[$from][$letter]), 0, -6);
           $res .= chr(bindec($c1));
           $res .= chr(bindec($c2));
        } else {
           $res .= $str[$i];
        }
    }
    return $res;
}


function convert_charcode($data, $from, $to) {
    if (is_string($data)) {
        $data = trim($data);
        switch(true) {
           case $to == "utf-8" && $from == "win1251":
                $data = win_to_utf($data);
                break;
           case $to == "utf-8" && $from == "koi8r":
                $data = win_to_utf($data, "k");
                break;
           case $to == "win1251" && $from == "utf-8":
                $data = utf_to_win($data);
                break;
           case $to == "win1251" && $from == "koi8r":
                $data = convert_cyr_string($data, "k", "w");
                break;
        }
        return $data;
    } elseif(is_array($data) || is_object($data)) {
        foreach($data as $key => $value) {
            $data[$key] = convert_charcode($value, $from, $to);
        }
    }
    return $data;
}

class Db {

    var $conn;
        var $db_host = "";
        var $db_login = "";
        var $db_password = "";
        var $db_name = "";
        var $query_log = array();
        var $query_last = "";


    function Db() {
             global $db_login, $db_password, $db_name, $db_host;

             if (!empty($db_name) && !empty($db_login) && !empty($db_password))
             {
                 $this->db_name = $db_name;
                 $this->db_host = $db_host;
                 $this->db_login = $db_login;
                 $this->db_password = $db_password;

                 if (empty($this->db_name)) die("Не указана база данных.");
                 $this->connect();
             }
    }

      function connect() {
        $this->conn = mysql_connect($this->db_host,$this->db_login,$this->db_password);
        if (!$this->conn) die(mysql_error());
        if (!mysql_select_db($this->db_name)) die("База данных указана не верно.");
        }

    function disconnect() {
        if ($this->conn) mysql_close($this->conn);
        }

    function query ($query, $data = array(), $notaddslashes = false) {
        if (is_array($data) && sizeof($data) > 0 && preg_match_all("!{([a-z0-9_]{1,})}!i", $query, $regs, PREG_SET_ORDER)) {
           foreach($regs as $field) {
               $query = str_replace($field[0], $data[$field[1]], $query);
           }
        }

        if (sizeof($this->query_log) == 10) array_shift($this->query_log);
        $this->query_last = $this->query_log[] = $query;


        return mysql_query($query);
        }

    function rows ($result) {
        if (!$result) return -1;
        return mysql_num_rows($result);
        }

    function fetch ($result,$i=-1) {
        if (is_string($result)) $result = $this->query($result);
        if (!$result) return array();
        if (mysql_num_rows($result) > 0) mysql_data_seek($result, 0);
        if ($i==-1) {
            $a = mysql_fetch_assoc ($result);
            if ($a && is_array($a)) {
               foreach($a as $key => $value) {
                  if (is_string($value)) $a[$key] = $value = /*magic_quotes_runtime() ? stripslashes($value) :*/ $value;
               }
            }
            return $a;
        } else {
            $data = @mysql_result ($result, 0, $i);
            if (is_string($data)) $data = magic_quotes_runtime() ? stripslashes($data) : $data;
            return $data;
        }
        }

    function fetchall ($result, $byPrimary = false) {
        $r=array();

        if (is_string($result)) $result = $this->query($result);
        if ($result) {
            while ($a=mysql_fetch_assoc($result)) {
                if ($a && is_array($a)) {
                   foreach($a as $key => $value) {
                        if (is_string($value)) $a[$key] = $value = /*magic_quotes_runtime() ? stripslashes($value) :*/ $value;
                   }
                }
                if (!empty($byPrimary) && (is_numeric($a[$byPrimary]) && $a[$byPrimary] > 0 || is_string($a[$byPrimary]) && !empty($a[$byPrimary]))) {
                    $r[$a[$byPrimary]]=$a;
                } else {
                    $r[]=$a;
                }
            }
        }
        return $r;
        }

    function fetchccol ($result,$i=0) {
        $r=array();
        if (is_string($result)) $result = $this->query($result);
        if ($result) {
            if (mysql_num_rows($result)>0) mysql_data_seek($result, 0);
            while ($a=mysql_fetch_array($result,MYSQL_NUM)) {$r[]= magic_quotes_runtime() ? stripslashes($a[$i]) : $a[$i];}
        }
        return $r;
        }

    function fetchcol ($result,$i=0) {
        return ($this->fetchccol($result,$i));
        }

    function last_id() {
        return mysql_insert_id();
        }

}

  class Template {

    var $dir  = "";
    var $files  = array();

    function Template ($dir = '') {
      global $_SERVER;
      if ($dir!="") {
        $this->dir = $dir;
      } else {
        $this->dir = TEMPLATESDIR;
      }
    }


    function fid_load ($fid, $filename, $vars='') {
      if (!file_exists($this->dir.$filename)) die("No file: $this->dir.$filename");
      $this->files[$fid] = file_get_contents($this->dir.$filename);

      if (get_magic_quotes_runtime()) $this->files[$fid] = stripslashes($this->files[$fid]);
      if ($vars!='') {
         $this->fid_vars($fid, $vars);
      }

      $this->fid_array($fid, get_defined_constants());
    }


    function fid_pass ($filename) {
      readfile ($this->dir.$filename);
      }



    function fid_include ($fid, $filename){
      $include = @file_get_contents($filename);
      $this->files[$fid] = str_replace("<include $filename>", $include, $this->files[$fid]);
    }

    function fid_read ($filename){

      $a = fread($fp = fopen($filename, 'r'), filesize($filename));
      fclose($fp);

      return $a;
    }


    function fid_parse ($fid) {
      while(is_long($pos = strpos($this->files[$fid], '<include '))){
        $pos += 9;
        $endpos = strpos($this->files[$fid], '>', $pos);
        $filename = substr($this->files[$fid], $pos, $endpos-$pos);
        $this->fid_include($fid, $filename);
        }
    }

    function fid_vars ($fid,$vars="") {
      if ($vars=="") return;

      $v = explode(',', $vars);
      foreach($v as $key) {
        $tvr = trim($key);
        if(strpos($this->files[$fid], '{'.$tvr.'}') !== false ) {
          global $$tvr;
          if (isset($$tvr)) $this->files[$fid] = str_replace('{'.$tvr.'}', $$tvr, $this->files[$fid]);
        }
      }
    }


    function fid_array ($fid, &$ar, $strip_if = false) {
      if (is_array($ar)) {
        foreach ($ar as $key => $value) {
          if(strpos($this->files[$fid], '{'.$key.'}') !== false ) {
            $this->files[$fid] = str_replace('{'.$key.'}', $value, $this->files[$fid]);
          }
          if ((strpos($this->files[$fid],"<if $key") !== false || strpos($this->files[$fid],"<if !$key") !== false) && $strip_if) {
            if ($value) {
                $this->files[$fid]=preg_replace("|(<if $key>(.*)</if $key>)|Ums","\\2",$this->files[$fid]);
                $this->files[$fid]=preg_replace("|(<if !$key>(.*)</if !$key>)|Ums","",$this->files[$fid]);
            } else {
                $this->files[$fid]=preg_replace("|(<if $key>(.*)</if $key>)|Ums","",$this->files[$fid]);
                $this->files[$fid]=preg_replace("|(<if !$key>(.*)</if !$key>)|Ums","\\2",$this->files[$fid]);
            }
          }
        }
      }
    }


    function fid_object ($fid, &$ob) {
      $ar = $ob;
      foreach ($ar as $key => $value) {
        if(strpos($this->files[$fid], '{'.$key.'}') !== false ) {
            $this->files[$fid] = str_replace('{'.$key.'}', $value, $this->files[$fid]);
        }
        if (strpos($this->files[$fid],"<if $key") !== false ) {
          if ($value ) {
            $this->files[$fid]=preg_replace("|<if $key>(.*)</if $key>|Ums","\\1",$this->files[$fid]);
            $this->files[$fid]=preg_replace("|<if !$key>(.*)</if !$key>|Ums","",$this->files[$fid]);
          } else {
            $this->files[$fid]=preg_replace("|<if $key>(.*)</if $key>|Ums","",$this->files[$fid]);
            $this->files[$fid]=preg_replace("|<if !$key>(.*)</if !$key>|Ums","\\1",$this->files[$fid]);
          }
        }
      }
    }

    function strip_loops($fid) {
      $this->files[$fid]=preg_replace("/<loop.+<\/loop [1-9A-Za-z_]+>/","",$this->files[$fid]);
    }

  function fid_select($fid, $select, $a, $active = 0, $ass = false, $ass_title = ""){
    $loopcode = '';
    $n = sizeof($a);
    if ($ass) {
    foreach($a as $i => $value) {
      $selector .= "<option value=\"".$i."\"";
      if ((!is_array($active) && $i == $active) || (is_array($active) && in_array($i, $active))) {$selector .= " selected";}
      $selector .= ">".$value[$ass_title]."</option>\n";
    }
    } else {
    foreach($a as $i => $value) {
      $selector .= "<option value=\"".current($a[$i])."\"";
      if ((!is_array($active) && current($a[$i]) == $active) || (is_array($active) && in_array(current($a[$i]), $active))) {$selector .= " selected";}
      $selector .= ">".next($a[$i])."</option>\n";
    }
    }
    $this->files[$fid] = preg_replace("!<selector ".addcslashes($select, "[].!?\\()-*")."([^>]{0,})>!Ums", "<select name=".$select."\\1>".$selector."</select>", $this->files[$fid]);
  }


  function fid_loop ($fid, $loop, $a){
      $loopcode = '';
      $n = count($a);

      $pos1 = strpos($this->files[$fid], '<loop '.$loop.'>') + strlen('<loop '.$loop.'>');
      $pos2 = strpos($this->files[$fid], '</loop '.$loop.'>');

      $loopcode = substr($this->files[$fid], $pos1, $pos2-$pos1);

      $tag1 = substr($this->files[$fid], strpos($this->files[$fid], '<loop '.$loop.'>'),strlen('<loop '.$loop.'>'));
      $tag2 = substr($this->files[$fid], strpos($this->files[$fid], '</loop '.$loop.'>'),strlen('</loop '.$loop.'>'));
      if($loopcode != ''){
        $newcode = '';
        if (is_array($a)) {
          foreach($a as $row){
            $tempcode = $loopcode;
            foreach ($row as $key => $value) {
                if (!is_array($value)) $tempcode = str_replace('{'.$key.'}',$value, $tempcode);
                if (strpos($tempcode,"<if $key") !== false || strpos($tempcode,"<if !$key") !== false) {
                        if ($value) {
			                    $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums","\\1",$tempcode);
                                $tempcode=preg_replace("|<if !$key>(.*)</if !$key>|Ums","",$tempcode);
			            } else {
			                    $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums","",$tempcode);
                                $tempcode=preg_replace("|<if !$key>(.*)</if !$key>|Ums","\\1",$tempcode);
			            }
	            }
            }
            $newcode .= $tempcode;
          }
        }
        $this->files[$fid] = str_replace($tag1.$loopcode.$tag2, $newcode, $this->files[$fid]);
      }
    }


    function fid_loop2d ($fid, $loop, $a){

      $loopcode = '';

      $n = count($a);

      $pos1 = strpos($this->files[$fid], '<loop '.$loop.'>') + strlen('<loop '.$loop.'>');
      $pos2 = strpos($this->files[$fid], '</loop '.$loop.'>');

      $loopcode = substr($this->files[$fid], $pos1, $pos2-$pos1);

      if(!is_array($a) || !preg_match_all("/<loop ".$loop.">(.*<2d(\d{1,})>(.*)<\/2d>.*)<\/loop ".$loop.">/Ums", $this->files[$fid], $matches, PREG_SET_ORDER)) {
        return;
      }
      $loopcode = Array();

      $temp_a = $a;

      foreach($matches as $key => $match) {
             $counter = 1;
             $num = 0;

             $temp = array();
             $a = $temp_a;
             $c = ceil(sizeof($a) / $match[2]);
             for($i = 0; $i < $c; $i++) {
                $key = 0;
                for($j = 0; $j < $match[2]; $j++) {
                   if (is_array($a[$i + $j*$c])) $temp[] = $a[$i + $j*$c];
                }
             }
             $a = $temp;

             foreach($a as $a_key => $value) {
                 $tempcode = $match[3];
                 foreach ($value as $k => $v) {
                    if (!is_array($v)) {
                        $tempcode = str_replace('{'.$k.'}',$v, $tempcode);
	                    if (strpos($tempcode,"<if $k") !== false ) {
                            if ($v) {
                                $tempcode=preg_replace("|<if $k>(.*)</if $k>|Ums","\\1",$tempcode);
                                $tempcode=preg_replace("|<if !$k>(.*)</if !$k>|Ums","",$tempcode);
                            } else {
                                $tempcode=preg_replace("|<if $k>(.*)</if $k>|Ums","",$tempcode);
                                $tempcode=preg_replace("|<if !$k>(.*)</if !$k>|Ums","\\1",$tempcode);
			                }
	                    }

                    }
                 }

                 $d_code.= $tempcode;
                 $counter++;
                 if ($counter > $match[2] || $num+1 == sizeof($a)) {
                     $counter = 1;
                     $loopcode[] = str_replace('<2d'.$match[2].'>'.$match[3].'</2d>', $d_code, $match[1]);
                     $d_code = "";
                 }
                 $num++;
             }

             if (sizeof($a) % $match[2] != 0) {
                 $counter = floor(sizeof($a) / $match[2]) * $match[2];
             }
             $this->files[$fid] = str_replace($match[0], implode("\n", $loopcode), $this->files[$fid]);
             $loopcode = Array();
      }

    }


    function fid_tree ($fid, $tree, $a){
      $loopcode = '';

      function DrawChilds($code, $a) {
         if (is_array($a) && sizeof($a) > 0) {
             foreach($a as $row) {
                $tempcode = $code;
                foreach ($row as $key => $value) {
                   if (!is_array($value)) $tempcode = str_replace('{'.$key.'}',$value, $tempcode);
	               if (strpos($tempcode,"<if $key") !== false ) {
	                  if ($value) {
			             $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums", "\\1",$tempcode);
                         $tempcode=preg_replace("|<if \!$key>(.*)</if \!$key>|Ums", "",$tempcode);
			          } else {
			             $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums","",$tempcode);
                         $tempcode=preg_replace("|<if \!$key>(.*)</if \!$key>|Ums", "\\1",$tempcode);
			          }
	               }

                }
                $childs = DrawChilds($code, $row["childs"]);
                $result .= str_replace("<childs>", $childs, $tempcode);
             }
             return $result;
         } else {
             return "";
         }
      }

      $n = count($a);
      if (!is_array($a) || !preg_match_all("/<tree $tree>(.*)<\/tree $tree>/Ums", $this->files[$fid], $rec, PREG_SET_ORDER)) {
         preg_replace("/<tree $tree>(.*)<\/tree $tree>/Ums", "", $this->files[$fid]);
         return;
      }

      foreach($rec as $child) {
         $this->files[$fid] = str_replace($child[0], DrawChilds($child[1], $a), $this->files[$fid]);
      }
    }


    function fid_loop_obj ($fid, $loop, $r, $with_if = false){

      $loopcode = '';

      $pos1 = strpos($this->files[$fid], '<loop '.$loop.'>') + strlen('<loop '.$loop.'>');
      $pos2 = strpos($this->files[$fid], '</loop '.$loop.'>');

      $loopcode = substr($this->files[$fid], $pos1, $pos2-$pos1);

      $tag1 = substr($this->files[$fid], strpos($this->files[$fid], '<loop '.$loop.'>'),strlen('<loop '.$loop.'>'));
      $tag2 = substr($this->files[$fid], strpos($this->files[$fid], '</loop '.$loop.'>'),strlen('</loop '.$loop.'>'));

      if (!sizeof($r)>0)
        { $this->files[$fid] = str_replace($tag1.$loopcode.$tag2,"",$this->files[$fid]); return -1; }

      if($loopcode != ''){
        $newcode = '';
        if ($r) {

          foreach ($r as $b => $a ) {
            $tempcode = $loopcode;
            $ar=get_object_vars($a);

            if ($with_if) {
              $tempcode = $this->fid_if_block ($tempcode,$ar);
            }

            foreach ($a as $key => $value) {
              if (!is_array($value)) {
                        $tempcode = str_replace('{'.$key.'}',$value, $tempcode);
                        if (strpos($tempcode,"<if $key") !== false ) {
	                        if ($value) {
			                $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums","\\1",$tempcode);
			        } else {
			                $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums","",$tempcode);
			        }
	                }
              }
            }
            $newcode .= $tempcode;
          }
        }
        $this->files[$fid] = str_replace($tag1.$loopcode.$tag2, $newcode, $this->files[$fid]);
      }
    }

    function fid_loom ($fid, $loop, $r, $numfield=false, $numstart=0){

      $loopcode = '';

      $pos1 = strpos($this->files[$fid], '<loop '.$loop.'>') + strlen('<loop '.$loop.'>');
      $pos2 = strpos($this->files[$fid], '</loop '.$loop.'>');

      $loopcode = substr($this->files[$fid], $pos1, $pos2-$pos1);

      $tag1 = substr($this->files[$fid], strpos($this->files[$fid], '<loop '.$loop.'>'),strlen('<loop '.$loop.'>'));
      $tag2 = substr($this->files[$fid], strpos($this->files[$fid], '</loop '.$loop.'>'),strlen('</loop '.$loop.'>'));

      if (!$r || mysql_num_rows($r)==0)
        { $this->files[$fid] = str_replace($tag1.$loopcode.$tag2,"",$this->files[$fid]); return -1; }

      if($loopcode != ''){
        $newcode = '';
        $i=0;
        while ($a=mysql_fetch_assoc($r)) {
          $i++;
          $tempcode = $loopcode;
          foreach ($a as $key => $value) {


            if (!is_array($value)) {
              $tempcode = str_replace('{'.$key.'}',$value, $tempcode);
            }
            if (strpos($tempcode,"<if $key") !== false ) {
              if ($value >'' ) {
                $tempcode=preg_replace("/<if $key>(.*)</if $key>/","\\1",$tempcode);
              } else {
                $tempcode=preg_replace("/<if $key>(.*)</if $key>/","",$tempcode);
              }
            }
            if ($numfield) {
              $tempcode = str_replace('{'.$numfield.'}',htmlspecialchars($numstart+$i), $tempcode);
            }
          }
          $newcode .= $tempcode;
        }
        $this->files[$fid] = str_replace($tag1.$loopcode.$tag2, $newcode, $this->files[$fid]);
      }
    }

    function fid_echo ($fid) {

        echo $this->files[$fid];

    }

    function fid_get ($fid) {

      if (isset($this->files[$fid]))
        return $this->files[$fid];
      else
        return "";

    }


    function fid_show ($fid, $stripempty=true) {
      foreach($this->files as $key => $file) {
         $this->files[$key] = preg_replace_callback("!<include ([^>]+)>!", "r", $this->files[$key]);
      }

      do {
        $replaced=0;
        for(reset($this->files); $key = key($this->files); next($this->files)) {
          if ($key!=$fid) {
            if (strpos($this->files[$fid],"<tpl $key>") !== false ) {
              $replaced++;
              $this->files[$fid]=str_replace("<tpl ".$key.">",$this->files[$key],$this->files[$fid]);
            }
          }
        }
      } while ($replaced>0);

          if ($stripempty) {
            //$this->files[$fid]=preg_replace("/<loop(.*)\/loop [a-zA-Z0-9_]{1,}>/Ums","",$this->files[$fid]);
            //$this->files[$fid]=preg_replace("/<if(.*)\/if [a-zA-Z0-9_]{1,}>/Ums","",$this->files[$fid]);
            if (preg_match_all("!<loop ([^>]+)>!", $this->files[$fid], $regs)) {
               foreach($regs[1] as $loop) {
                  $this->files[$fid]=preg_replace("/<loop $loop>(.*)<\/loop $loop>/Ums","",$this->files[$fid]);
               }
            }
            if (preg_match_all("!<if ([^>]+)>!", $this->files[$fid], $regs)) {
               foreach($regs[1] as $if) {
                  $this->files[$fid]=preg_replace("/<if $if>(.*)<\/if $if>/Ums","",$this->files[$fid]);
               }
            }

            $this->files[$fid]=preg_replace("/{[_a-zA-Z0-9\-\._]+}/Ums","",$this->files[$fid]);
            $this->files[$fid]=preg_replace("/<selector(.*)>/Ums","",$this->files[$fid]);
            $this->files[$fid]=preg_replace("/<tpl(.*)>/Ums","",$this->files[$fid]);
            $this->files[$fid]=preg_replace("/<tree(.*)\/tree [a-zA-Z0-9_]{1,}>/Ums","",$this->files[$fid]);

          }
      echo $this->files[$fid];

    }


    function fid_if_obj ($fid,$ar) {
      $this->fid_if($fid,get_object_vars($ar));
    }


    function fid_if ($fid,$ar) {
      if (!is_array($ar)) return;
      foreach($ar as $key => $value) {
         if ($value == true || !empty($value)) {
            $this->files[$fid] = preg_replace("/(<if $key>(.*)<\/if $key>)/Ums", "\\2", $this->files[$fid]);
            $this->files[$fid] = preg_replace("/(<if !$key>(.*)<\/if !$key>)/Ums", "", $this->files[$fid]);
         } else {
            $this->files[$fid] = preg_replace("/(<if $key>(.*)<\/if $key>)/Ums", "", $this->files[$fid]);
            $this->files[$fid] = preg_replace("/(<if !$key>(.*)<\/if !$key>)/Ums", "\\2", $this->files[$fid]);
         }
      }
      /*
      while (is_long($pos = strpos($this->files[$fid], '<if '))) {

        $pos1 = strpos($this->files[$fid], '<if ');
        $pos2 = strpos($this->files[$fid], '>', $pos1);

        $ifname = substr ($this->files[$fid],$pos1+4,$pos2-$pos1-4);
        $ifcode = substr ($this->files[$fid],$pos2+1,strpos($this->files[$fid],'</if '.$ifname.'>',$pos2)-$pos2-1);

        $newcode = $ifcode;

        if ($ar[$ifname] == true || !empty($ar[$ifname])) {
          foreach ($ar as $key => $value)
            if (strpos($newcode, '{'.$key.'}'))
              $newcode = str_replace('{'.$key.'}', $value, $newcode);
          }
        else
          $newcode='';

        $this->files[$fid] = str_replace('<if '.$ifname.'>'.$ifcode.'</if '.$ifname.'>', $newcode, $this->files[$fid]);

        }
        */

      }

    function fid_if_block ($block,$ar) {

      while (is_long($pos = strpos($block, '<if '))) {

        $pos1 = strpos($block, '<if ');
        $pos2 = strpos($block, '>', $pos1);

        $ifname = substr ($block,$pos1+4,$pos2-$pos1-4);
        $ifcode = substr ($block,$pos2+1,strpos($block,'</if '.$ifname.'>',$pos2)-$pos2-1);

        $newcode = $ifcode;

        if (isset($ar[$ifname]) && $ar[$ifname]>'' ) {
          foreach ($ar as $key => $value)
            if (strpos($newcode, '{'.$key.'}') !== false )
              $newcode = str_replace('{'.$key.'}', $value, $newcode);
          }
        else
          $newcode='';

        $block = str_replace('<if '.$ifname.'>'.$ifcode.'</if '.$ifname.'>', $newcode, $block);

        }

      return $block;

      }

  } // End of class


function polygraf($text) {
global $tags;

$patterns[] = "/[ ]{2,}/Ums";
$patterns[] = "/=[ ]{0,}\"(.*)\"/Ums";
$patterns[] = "/\xA7/Ums";
$patterns[] = "/\([CcСс]\)|\xA9/Ums";
$patterns[] = "/\([rR]\)|\xAE/Ums";
$patterns[] = "/\((tm|TM|тм|ТМ)\)|\x99/Ums";
$patterns[] = "/\xB0/Ums";
$patterns[] = "/(\x85|\.{3})/Ums";
$patterns[] = "/\x92/Ums";
$patterns[] = "/\x95/Ums";
$patterns[] = "/\xB1/Ums";
$patterns[] = "/\+-/Ums";
$patterns[] = "/[a-zA-Zа-яА-Я0-9_]\s+([.,?!:^;])/Ums";
$patterns[] = "/№[ ]{0,}([0-9\/-]*)/ms";
//$patterns[] = "/([\w\s.,!?;<>-][^=][\s]{1,}|>|<)\"(.*)\"/Ums";
$patterns[] = "/([a-zA-Zа-яА-Я0-9_](?:[.,!?])?)\"/Ums";
$patterns[] = "/\"([a-zA-Zа-яА-Я0-9_])/Ums";
$patterns[] = "/([a-zA-ZА-Яа-я]{1,}-[a-zA-ZА-Яа-я0-9]{1,})/ms";
$patterns[] = "/([ ]{1,}-[ ]{0,})|([ ]{0,}[-|-][ ]{1,})/ms";
$patterns[] = "/ ([a-zA-Zа-яА-я]{1,3})[\s]{1,}/Ums";
$patterns[] = "/&nbsp;([a-zA-Zа-яА-я]{1,3})[\s]{1,}/ms";

$replacements[] = " ";
$replacements[] = "='\\1'";
$replacements[] = "&#167;";
$replacements[] = "&#169;";
$replacements[] = "&#174;";
$replacements[] = "<sup><small>&trade;</small></sup>";
$replacements[] = "&#176;";
$replacements[] = "&#133;";
$replacements[] = "&#146;";
$replacements[] = "&#149;";
$replacements[] = "&#177;";
$replacements[] = "&plusmn;";
$replacements[] = "\\1";
$replacements[] = "<nobr>&#8470&nbsp;\\1</nobr>";
//$replacements[] = "\\1&laquo;\\2&raquo;";
$replacements[] = "\\1&raquo;";
$replacements[] = "&laquo;\\1";
$replacements[] = "<nobr>\\1</nobr>";
$replacements[] = "&nbsp;&#151; ";
$replacements[] = " \\1&nbsp;";
$replacements[] = "&nbsp;\\1&nbsp;";


$text = preg_replace_callback("/<(.*)>/Ums", "tags", $text);

$text = preg_replace_callback("/(\+[\d]{1,}[ ]{0,1}){0,1}(\([\d]{1,}\)[ ]{0,1}){0,1}([\d]{3})[-]{0,1}([\d]{2})[-]{0,1}([\d]{2})/ms", "tel", $text);
$text = preg_replace($patterns, $replacements, $text);
if(is_array($tags) && sizeof($tags)>0)
foreach($tags as $key => $value) {
  $text = str_replace("<tag".$key.">", "<".$value.">", $text);
}

return $text;
}
function tel($matches) {
  if (!empty($matches[1])) $text .= $matches[1]." ";
  if (!empty($matches[2])) $text .= $matches[2]." ";
  $text .= $matches[3]."-".$matches[4]."-".$matches[5];
  return " <nobr>".$text."</nobr>";
}

function tags($matches) {
  global $tags;
  $tags[] = $matches[1];
  return "<tag".(sizeof($tags)-1).">";
}

function r($m) {
  if (strtolower(substr($m[1], 0, 7)) != "http://" && strtolower(substr($m[1], -4)) == ".php") {
     ob_start();
     $c = preg_replace("!<\?php|<\?|\?>!", "", file_get_contents($m[1]));
     if (!empty($c)) $asd = @eval($c);
     $asd === false ? ob_clean() : $return = ob_get_clean();
  } else {
     $return = @file_get_contents($m[1]);
  }
 return $return;
}


function save_images($big_image, $med_image, $small_image, $base_name, $big_overwrite, $med_overwrite, $small_overwrite, $image_sizes)
{
/*
если данные поступают с формы редактирования товара - приоритет у загружаемых файлов
перезапись и уменьшение происходит только при отсутствии файла или установленном флаге "удалить" для загрузки файлов
и "загрузить" для урлов

если не задано базовое имя - возврат
если задана маленькая картинка и установлен флаг перезаписать
    если она доступна
        удаляем текущий маленький файл
        копируем новую картинку в jpg и уменьшаем, если надо
        устанавливаем флаг "маленькая перезаписана" в 1
    иначе
        если текущая картинка есть - оставляем ее
        выставляем флаг "маленькая перезаписана" в 1, чтобы она не сгенерировалась из большой

если задана средняя картинка и установлен флаг перезаписать
    если она доступна
        удаляем текущий средний файл
        копируем новую картинку в jpg и уменьшаем, если надо
        устанавливаем флаг "средняя перезаписана" в 1
    иначе
        если текущая картинка есть - оставляем ее
        выставляем флаг "средняя перезаписана" в 1, чтобы она не сгенерировалась из большой

если задана большая картинка и установлен флаг перезаписать
    если она доступна
        удаляем текущий средний файл
        копируем новую картинку в jpg и уменьшаем, если надо
        устанавливаем флаг "большая перезаписана" в 1
    иначе
        если текущая картинка есть - оставляем ее

если не установлен флаг "маленькая перезаписана" и установлен "перезаписать" или не указана маленькая картинка или файл отсутствует
    если есть большая
        делаем маленькую из большой
    иначе если есть средняя
        делаем маленькую из средней

если не установлен флаг "средняя перезаписана" и установлен "перезаписать" или не укзана средняя картинка или файл отсутствует
    если есть большая
        делаем среднюю из большой
*/

    if (empty($base_name)) return;

    $small_overwrited = $med_overwrited = $big_overwrited = false;

    if (!empty($small_image) && ($small_overwrite || !file_exists(DOWNLOAD_IMAGES_DIR."small_".$base_name.".jpg")))
    {
        $s = @getimagesize($small_image);
        if ($s !== false)
        {
            if (file_exists(DOWNLOAD_IMAGES_DIR."small_".$base_name.".jpg")) unlink(DOWNLOAD_IMAGES_DIR."small_".$base_name.".jpg");
            elseif (file_exists(DOWNLOAD_IMAGES_DIR."small_".$base_name.".gif")) unlink(DOWNLOAD_IMAGES_DIR."small_".$base_name.".gif");
            if (copyImage("", $image_sizes[2], 0, $small_image, DOWNLOAD_IMAGES_DIR."small_".$base_name.".jpg")) $small_overwrited = true;
        }
        else
        {
            $small_overwrited = true;
        }
    }

    if (!empty($med_image) && ($med_overwrite || !file_exists(DOWNLOAD_IMAGES_DIR."".$base_name.".jpg")))
    {
        $s = @getimagesize($med_image);
        if ($s !== false)
        {
            if (file_exists(DOWNLOAD_IMAGES_DIR."".$base_name.".jpg")) unlink(DOWNLOAD_IMAGES_DIR."".$base_name.".jpg");
            elseif (file_exists(DOWNLOAD_IMAGES_DIR."".$base_name.".gif")) unlink(DOWNLOAD_IMAGES_DIR."".$base_name.".gif");
            if (copyImage("", $image_sizes[1], 0, $med_image, DOWNLOAD_IMAGES_DIR."".$base_name.".jpg")) $med_overwrited = true;
        }
        else
        {
            $med_overwrited = true;
        }
    }

    if (!empty($big_image) && ($big_overwrite || !file_exists(DOWNLOAD_IMAGES_DIR."big_".$base_name.".jpg")))
    {
        $s = @getimagesize($big_image);
        if ($s !== false)
        {
            if (file_exists(DOWNLOAD_IMAGES_DIR."big_".$base_name.".jpg")) unlink(DOWNLOAD_IMAGES_DIR."".$base_name.".jpg");
            elseif (file_exists(DOWNLOAD_IMAGES_DIR."big_".$base_name.".gif")) unlink(DOWNLOAD_IMAGES_DIR."".$base_name.".gif");
            if (copyImage("", $image_sizes[0], 0, $big_image, DOWNLOAD_IMAGES_DIR."big_".$base_name.".jpg")) $big_overwrited = true;
        }
        else
        {
            $big_overwrited = true;
        }
    }

    if (!$small_overwrited && $small_overwrite || empty($small_image) || !file_exists(DOWNLOAD_IMAGES_DIR."small_".$base_name.".jpg"))
    {
        if (file_exists(DOWNLOAD_IMAGES_DIR."big_".$base_name.".jpg"))
            copyImage("", $image_sizes[2], 0, DOWNLOAD_IMAGES_DIR."big_".$base_name.".jpg", DOWNLOAD_IMAGES_DIR."small_".$base_name.".jpg");
        elseif (file_exists(DOWNLOAD_IMAGES_DIR."".$base_name.".jpg"))
            copyImage("", $image_sizes[2], 0, DOWNLOAD_IMAGES_DIR."".$base_name.".jpg", DOWNLOAD_IMAGES_DIR."small_".$base_name.".jpg");
    }

    if (!$med_overwrited && $med_overwrite || empty($med_image) || !file_exists(DOWNLOAD_IMAGES_DIR."".$base_name.".jpg"))
    {
        if (file_exists(DOWNLOAD_IMAGES_DIR."big_".$base_name.".jpg"))
            copyImage("", $image_sizes[1], 0, DOWNLOAD_IMAGES_DIR."big_".$base_name.".jpg", DOWNLOAD_IMAGES_DIR."".$base_name.".jpg");
    }
}


function copyImage($type, $width, $height, $file_link, $file_save, $width_limit = false) {
    $photo = $file_link;
    if (!file_exists($photo)) {
        return FALSE;
    }
    $big_photo = FALSE;
    $type = strtolower($type);

    $photo_size = getimagesize($photo);
    switch($photo_size[2])
    {
        case 1:
            $big_photo = @imagecreatefromgif($photo);
            break;
        case 2:
            $big_photo = @imagecreatefromjpeg($photo);
            break;
        case 3:
            $big_photo = @imagecreatefrompng($photo);
            break;
        case 6:
            $big_photo = imagecreatefromwbmp($photo);
            break;
        default: return FALSE;
    }

    if ($photo_size[0] > $width)
    {
        $photo_width = $photo_size[0];
        $photo_height = $photo_size[1];
        if ($width != 0 && $height == 0) {
           $height = ceil(($photo_height * $width) / $photo_width);
        }
        if ($width == 0 && $height != 0) {
           $width = ceil(($photo_width * $height) / $photo_height);
        }

        $big_size = getimagesize ($file_link);
        $big_weight = $big_size[0];
        $big_height = $big_size[1];

        $small_photo = imagecreatetruecolor ($width, $height);
        if (!$small_photo) {
            return FALSE;
        }

        imagecopyresampled ($small_photo, $big_photo, 0,0, 0, 0, $width, $height, $photo_size[0], $photo_size[1]);
        imagejpeg ($small_photo, $file_save,75);
    }
    else
    {
        copy($photo, $file_save);
    }

    return TRUE;
}

function minimizeImage($type, $width, $height, $file_link, $file_save) {
    $photo = $file_link;
    if (!file_exists($photo)) {
        return FALSE;
    }
    $big_photo = FALSE;
    $type = strtolower($type);

    if (($type == '.jpg')||($type=='.jpeg')){
        $big_photo = imagecreatefromjpeg ($photo);
    }
    if ($type == '.gif'){
        $big_photo = imagecreatefromgif($photo);
    }
    if ($type == '.png'){
        $big_photo = imagecreatefrompng($photo);
    }
    if (!$big_photo){
        $big_photo = @imagecreatefromjpeg($photo);
    }
    if (!$big_photo){
        $big_photo = @imagecreatefromgif($photo);
    }
    if (!$big_photo){
        $big_photo = @imagecreatefrompng($photo);
    }
    if (!$big_photo){
        $big_photo = imagecreatefromxbm($photo);
    }
    if (!$big_photo){
        return FALSE;
    }

    $photo_size = getimagesize($photo);
    //if (!$photo_size || $photo_size[0] == 0 || $photo_size[1] == 0) return false;
    $photo_width = $photo_size[0];
    $photo_height = $photo_size[1];
    if ($width != 0 && $height == 0) {
       $height = ($photo_height * $width) / $photo_width;
    }
    if ($width == 0 && $height != 0) {
       $width = ($photo_width * $height) / $photo_height;
    }

    $kw = $photo_width / $width;
    $kh = $photo_height / $height;
    if (($kh != 0)&&($kw != 0))   {
        if ($kw > $kh) {
            $photo_width = $photo_width / $kw;
            $photo_height = $photo_height / $kw;
        } else   {
            $photo_width = $photo_width / $kh;
            $photo_height = $photo_height / $kh;
        }
    } else {
        return FALSE;
    }

    $big_size = getimagesize ($file_link);
    $big_weight = $big_size[0];
    $big_height = $big_size[1];
    $small_photo = imagecreatetruecolor ($photo_width, $photo_height);
    if (!$small_photo) {
        return FALSE;
    }

    imagecopyresampled ($small_photo, $big_photo, 0,0, 0, 0, $photo_width, $photo_height, $photo_size[0], $photo_size[1]);
    imagejpeg ($small_photo, $file_save,100);
    return TRUE;
}
?>