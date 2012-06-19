<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.3
#####################################
*/

function open_flash_chart_object_str( $width, $height, $url, $use_swfobject=true, $base='' )
{
    return _ofc( $width, $height, $url, $use_swfobject, $base );
}

function open_flash_chart_object( $width, $height, $url, $use_swfobject=true, $base='' )
{
    echo _ofc( $width, $height, $url, $use_swfobject, $base );
}

function _ofc( $width, $height, $url, $use_swfobject, $base )
{

    $url = urlencode($url);
    $out = array();
    
    if (isset ($_SERVER['HTTPS']))
    {
        if (strtoupper ($_SERVER['HTTPS']) == 'ON')
        {
            $protocol = 'https';
        }
        else
        {
            $protocol = 'http';
        }
    }
    else
    {
        $protocol = 'http';
    }
    
    global $open_flash_chart_seqno;
    $obj_id = 'chart';
    $div_name = 'flashcontent';

    if( !isset( $open_flash_chart_seqno ) )
    {
        $open_flash_chart_seqno = 1;
        $out[] = '<script type="text/javascript" src="'.http_path('js').'swfobject/swfobject.js"></script>';
    }
    else
    {
        $open_flash_chart_seqno++;
        $obj_id .= '_'. $open_flash_chart_seqno;
        $div_name .= '_'. $open_flash_chart_seqno;
    }
    $url = http_path('lib').'flash_chart/chart_data.php';
    if( $use_swfobject )
    { 
    $out[] = '<div id="'. $div_name .'"></div>';
	$out[] = '<script type="text/javascript">';
	$out[] = 'var so = new SWFObject("'.http_path('lib').'flash_chart/flash_chart.swf", "'. $obj_id .'", "'. $width . '", "' . $height . '", "9", "#FFFFFF");';

	$out[] = 'so.addVariable("data", "'. $url . '");';
	$out[] = 'so.addParam("allowScriptAccess", "sameDomain");';
	$out[] = 'so.write("'. $div_name .'");';
	$out[] = '</script>';
	$out[] = '<noscript>';
    }

    $out[] = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="' . $protocol . '://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" ';
    $out[] = 'width="' . $width . '" height="' . $height . '" id="ie_'. $obj_id .'" align="middle">';
    $out[] = '<param name="allowScriptAccess" value="sameDomain" />';
    $out[] = '<param name="movie" value="'.http_path('lib').'flash_chart/flash_chart.swf?width='. $width .'&height='. $height . '&data='. $url .'" />';
    $out[] = '<param name="quality" value="high" />';
    $out[] = '<param name="opaque" value="wmode" />';
    $out[] = '<param name="bgcolor" value="#FFFFFF" />';
    $out[] = '<embed src="'.http_path('lib').'flash_chart/flash_chart.swf?data=' . $url .'" quality="high" wmode="opaque" bgcolor="#FFFFFF" width="'. $width .'" height="'. $height .'" name="'. $obj_id .'" align="middle" allowScriptAccess="sameDomain" ';
    $out[] = 'type="application/x-shockwave-flash" pluginspage="' . $protocol . '://www.macromedia.com/go/getflashplayer" id="'. $obj_id .'"/>';
    $out[] = '</object>';

    if ( $use_swfobject ) {
	$out[] = '</noscript>';
    }
    
    return implode("\n",$out);
}
?>