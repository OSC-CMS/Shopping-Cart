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

function quote_oanda_currency($code, $base = DEFAULT_CURRENCY) 
{
    $page = file('http://www.oanda.com/convert/fxdaily?value=1&redirected=1&exch=' . $code .  '&format=CSV&dest=Get+Table&sel_list=' . $base);

    $match = array();

    preg_match('/(.+),(\w{3}),([0-9.]+),([0-9.]+)/i', implode('', $page), $match);

    if (sizeof($match) > 0) {
      return $match[3];
    } else {
      return false;
    }
}

function quote_xe_currency($to, $from = DEFAULT_CURRENCY) 
{
    $page = file('http://www.xe.net/ucc/convert.cgi?Amount=1&From=' . $from . '&To=' . $to);

    $match = array();

    preg_match('/[0-9.]+\s*' . $from . '\s*=\s*([0-9.]+)\s*' . $to . '/', implode('', $page), $match);

    if (sizeof($match) > 0) {
      return $match[1];
    } else {
      return false;
    }
}
  
// BOF CBR
// Синхронизация курса валют с текущим курсом Центрального банка России
// http://www.cbr.ru/scripts/Root.asp?Prtid=SXML
function quote_cbr_currency($code, $base = DEFAULT_CURRENCY) 
{ 
    static $rateCacheCBR = false;

    if (!$rateCacheCBR) {
      $rateCacheCBR = array();
      $rateCacheCBR['RUR'] = $rateCacheCBR['RUB'] = 1;

      $url = 'http://www.cbr.ru/scripts/XML_daily.asp';
      $data = '';
      // check via file() ... may fail if php file Wrapper disabled.
      $page = @file($url . '?' . $data);
      if (!is_array($page) && function_exists('curl_init')) {
        // check via cURL instead.  May fail if proxy not set, esp with GoDaddy.
        $page = doCurlCurrencyRequest('GET', $url, $data) ;
        $page = explode("\n", $page);
      }
      if (is_array($page) || $page != '') {
        $page = implode('', $page);
        preg_match_all("|<CharCode>(.*?)</CharCode>|is", $page, $CharCode);
        preg_match_all("|<Nominal>(.*?)</Nominal>|is", $page, $Nominal);
        preg_match_all("|<Value>(.*?)</Value>|is", $page, $Value);
//        echo '<pre>';var_export($CharCode[1]);echo '<pre>';
//        echo '<pre>';var_export($Nominal[1]);echo '<pre>';
//        echo '<pre>';var_export($Value[1]);echo '<pre>';
        for($i=0, $n=sizeof($CharCode[1]); $i<$n; $i++) {
          $Value[1][$i] = str_replace(',', '.', $Value[1][$i]);
//        	echo $CharCode[1][$i] . ' ' . $Nominal[1][$i] . ' ' . $Value[1][$i] . ' ' . $Value[1][$i]/$Nominal[1][$i] . '<br />';
          $rateCacheCBR[$CharCode[1][$i]] = $Value[1][$i]/$Nominal[1][$i];
        }
//        echo '<pre>';var_export($rateCacheCBR);echo '<pre>';
//        die;
      }
    }
//    echo '<pre>';var_dump($code, isset($rateCacheCBR[$code]), $base, isset($rateCacheCBR[$base]));echo '<pre>';
    if (isset($rateCacheCBR[$code]) && isset($rateCacheCBR[$base])) 
	{
      $rate = round($rateCacheCBR[$base]/$rateCacheCBR[$code],4);
      return (string)$rate;
    } else {
      return false;
    }
  }
// EOF CBR

  function doCurlCurrencyRequest($method, $url, $vars) {
    //echo '-----------------<br />';
    //echo 'URL: ' . $url . ' VARS: ' . $vars . '<br />';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//  curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
//  curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
    if (strtoupper($method) == 'POST') {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
    }
    if (CURL_PROXY_REQUIRED == 'True') {
      $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
      curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
      curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      curl_setopt ($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
    }
    $data = curl_exec($ch);
    $error = curl_error($ch);
    //$info=curl_getinfo($ch);
    curl_close($ch);

    if ($error != '') {
      global $messageStack;
      $messageStack->add_session('cURL communication ERROR: ' . $error, 'error');
    }
    //echo 'INFO: <pre>'; print_r($info); echo '</pre><br />';
    //echo 'ERROR: ' . $error . '<br />';
    //print_r($data) ;

    if ($data != '') {
      return $data;
    } else {
      return $error;
    }
  }
  
?>