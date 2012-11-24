<?php
/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

include ('includes/top.php');

/**
 * Сумма прописью
 */
function SumProp($srcsumm, $val_rub = '', $val_kop = '')
{
  $point ='\,'; // десятичная запятая
  if (strtoupper($val_rub) == 'USD' || strtoupper($val_rub) == 'EUR') $point ='\.'; // десятичная точка
  $pattern = '/[^0-9'.$point.']/';
  $srcsumm = preg_replace($pattern,'',$srcsumm);
  $cifir= Array('од','дв','три','четыр','пят','шест','сем','восем','девят');
  $sotN = Array('сто','двести','триста','четыреста','пятьсот','шестьсот','семьсот','восемьсот','девятьсот');
  $milion= Array('триллион','миллиард','миллион','тысяч');
  $anDan = Array('','','','сорок','','','','','девяносто');
  $scet=4;
  $cifR='';
  $cfR='';
  $oboR= Array();
//==========================
  $splt = explode('.',"$srcsumm");
  if(count($splt)<2) $splt = explode(',',"$srcsumm");
  $xx = $splt[0];
  $xx1 = (empty($splt[1])? '00': $splt[1]);
  $xx1 = str_pad("$xx1", 2, "0", STR_PAD_RIGHT); // 2345.1 -> 10 копеек
//  $xx1 = round(($srcsumm-floor($srcsumm))*100);
  if ($xx>999999999999999) { $cfR=$srcsumm; return $cfR; }
  while($xx/1000>0){
     $yy=floor($xx/1000);
     $delen= round(($xx/1000-$yy)*1000);

     $sot= floor($delen/100)*100;
     $des=(floor($delen-$sot)>9? floor(($delen-$sot)/10)*10:0);
     $ed= floor($delen-$sot)-floor(($delen-$sot)/10)*10;

     $forDes=($des/10==2?'а':'');
     $forEd= ($ed==1 ? 'ин': ($ed==2?'е':'') );
     if ( floor($yy/1000)>=1000 ) { // делаю "единицы" для тысяч, миллионов...
         $ffD=($ed>4?'ь': ($ed==1 || $scet<3? ($ed<2?'ин': ($scet==3?'на': ($scet<4? ($ed==2?'а':( $ed==4?'е':'')) :'на') ) ) : ($ed==2 || $ed==4?'е':'') ) );
     }
     else { // единицы для "единиц
         $ffD=($ed>4?'ь': ($ed==1 || $scet<3? ($scet<3 && $ed<2?'ин': ($scet==3?'на': ($scet<4? ($ed==2?'а':( $ed==4?'е':'')) :'ин') ) ) : ( $ed==4?'е':($ed==2?'а':'')) ) );
     }
     if($ed==2) $ffD = ($scet==3)?'е':'а'; // два рубля-миллиона-миллиарда, но две тысячи

     $forTys=($des/10==1? ($scet<3?'ов':'') : ($scet<3? ($ed==1?'': ($ed>1 && $ed<5?'а':'ов') ) : ($ed==1? 'а': ($ed>1 && $ed<5?'и':'') )) );
     $nnn = floor($sot/100)-1;
     $oprSot=(!empty($sotN[$nnn]) ? $sotN[$nnn]:'');
     $nnn = floor($des/10);
     $oprDes=(!empty($cifir[$nnn-1])? ($nnn==1?'': ($nnn==4 || $nnn==9? $anDan[$nnn-1]:($nnn==2 || $nnn==3?$cifir[$nnn-1].$forDes.'дцать':$cifir[$nnn-1].'ьдесят') ) ) :'');

     $oprEd=(!empty($cifir[$ed-1])? $cifir[$ed-1].(floor($des/10)==1?$forEd.'надцать' : $ffD ) : ($des==10?'десять':'') );
     $oprTys=(!empty($milion[$scet]) && $delen>0) ? $milion[$scet].$forTys : '';

     $cifR= (strlen($oprSot) ? ' '.$oprSot:'').
       (strlen($oprDes)>1 ? ' '.$oprDes:'').
       (strlen($oprEd)>1  ? ' '.$oprEd:'').
       (strlen($oprTys)>1 ? ' '.$oprTys:'');
     $oboR[]=$cifR;
     $xx=floor($xx/1000);
     $scet--;
     if (floor($xx)<1 ) break;
  }
  $oboR = array_reverse($oboR);
  for ($i=0; $i<count($oboR); $i++){
      $probel = strlen($cfR)>0 ? ' ':'';
      $cfR .= (($oboR[$i]!='' && $cfR!='') ? $probel:'') . $oboR[$i];
  }
  if (strlen($cfR)<3) $cfR='ноль';

  $intsrc = $splt[0];
  $kopeiki = $xx1;
  $kop2 =str_pad("$xx1", 2, "0", STR_PAD_RIGHT);

  $sum2 = str_pad("$intsrc", 2, "0", STR_PAD_LEFT);
  $sum2 = substr($sum2, strlen($sum2)-2); // 676571-> '71'
  $sum21 = substr($sum2, strlen($sum2)-2,1); // 676571-> '7'
  $sum22 = substr($sum2, strlen($sum2)-1,1); // 676571-> '1'
  $kop1  = substr($kop2,0,1);
  $kop2  = substr($kop2,1,1);
  $ar234 = array('2','3','4'); // доллар-А, рубл-Я...
  // делаю спряжения у слова рубл-ей|я|ь / доллар-ов... / евро
  switch(strtoupper($val_rub)) {
    case 'BYR':
		$val1 = 'рубл';
		$val2 = 'копейка';
		if($sum22=='1' && $sum21!='1') $val1 .= 'ь'; // 01,21...91 рубль
		elseif(in_array($sum22, $ar234) && ($sum21!='1')) $val1 .= 'я';
		else $val1 .= 'ей';

		if(in_array($kop2, $ar234) && ($kop1!='1')) $val2 = 'копейки';
		elseif($kop2=='1' && $kop1!='1') $val2 = 'копейка'; // 01,21...91 копейка
		else $val2 = 'копеек';
		$val1 .= ' РБ';
		$cfR .= " $val1 $kopeiki $val2";
		break;
	case 'EUR':
		$val1 = 'евро'; // несклоняемое
		$val2 = 'цент';
		if($kop2=='1' && $kop1!='1') $val2 .= ''; // 01,21...91 цент
		elseif(in_array($kop2, $ar234) && ($kop1!='1')) $val2 .= 'a';
		else $val2 .= 'ов';

		$cfR .= " $val1 $kopeiki $val2";
		break;
	case 'KGS':
		$val1 = 'сом';
		$val2 = 'тыйын';
		if($sum22=='1' && $sum21!='1') $val1 .= ''; // 01,21...91 сом
		elseif(in_array($sum22, $ar234) && ($sum21!='1')) $val1 .= 'a';
		else $val1 .= 'ов';

		if($kop2=='1' && $kop1!='1') $val2 .= ''; // 01,21...91 тыйын
		elseif(in_array($kop2, $ar234) && ($kop1!='1')) $val2 .= 'a';
		else $val2 .= 'ов';

		$cfR .= " $val1 $kopeiki $val2";
		break;
	case 'KZT':
		$val1 = 'тенге'; // несклоняемое
		$val2 = 'тиын';
		if($kop2=='1' && $kop1!='1') $val2 .= ''; // 01,21...91 тиын
		elseif(in_array($kop2, $ar234) && ($kop1!='1')) $val2 .= 'a';
		else $val2 .= 'ов';

		$cfR .= " $val1 $kopeiki $val2";
		break;
    case 'RUB':
    case 'RUR':
		$val1 = 'рубл';
		$val2 = 'копейка';
		if($sum22=='1' && $sum21!='1') $val1 .= 'ь'; // 01,21...91 рубль
		elseif(in_array($sum22, $ar234) && ($sum21!='1')) $val1 .= 'я';
		else $val1 .= 'ей';

		if(in_array($kop2, $ar234) && ($kop1!='1')) $val2 = 'копейки';
		elseif($kop2=='1' && $kop1!='1') $val2 = 'копейка'; // 01,21...91 копейка
		else $val2 = 'копеек';
		$cfR .= " $val1 $kopeiki $val2";
		break;
	case 'TJS':
		$val1 = 'сомони'; // несклоняемое
		$val2 = 'дирам';
		if($kop2=='1' && $kop1!='1') $val2 .= ''; // 01,21...91 дирам
		elseif(in_array($kop2, $ar234) && ($kop1!='1')) $val2 .= 'a';
		else $val2 .= 'ов';

		$cfR .= " $val1 $kopeiki $val2";
		break;
	case 'UAH':
		$val1 = 'грив'; // ж.р.?
		$val2 = 'копейка';
		if($sum22=='1' && $sum21!='1') $val1 .= 'ня'; // 01,21...91 гривня
		elseif(in_array($sum22, $ar234) && ($sum21!='1')) $val1 .= 'ни';
		else $val1 .= 'ень';

		if(in_array($kop2, $ar234) && ($kop1!='1')) $val2 = 'копейки';
		elseif($kop2=='1' && $kop1!='1') $val2 = 'копейка'; // 01,21...91 копейка
		else $val2 = 'копеек';
		$cfR .= " $val1 $kopeiki $val2";
		break;
	case 'USD':
		$val1 = 'доллар';
		$val2 = 'цент';
		if($sum22=='1' && $sum21!='1') $val1 .= ''; // 01,21...91 доллар
		elseif(in_array($sum22, $ar234) && ($sum21!='1')) $val1 .= 'a';
		else $val1 .= 'ов';

		if($kop2=='1' && $kop1!='1') $val2 .= ''; // 01,21...91 цент
		elseif(in_array($kop2, $ar234) && ($kop1!='1')) $val2 .= 'a';
		else $val2 .= 'ов';
		$val1 .= ' США';
		$cfR .= " $val1 $kopeiki $val2";
		break;
	default:
		$cfR .= ' '.$val_rub;
		if($val_kop!='') $cfR .= " $kopeiki $val_kop";
		break;
  }
  $cfR = trim($cfR);
  return utf8_ucfirst($cfR);
}

/**
 * utf8_ucfirst func
 */
function utf8_ucfirst($string, $e = "utf-8", $lower_str_remaining = false)
{
	if (!defined('UTF8_NOMBSTRING') && function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string))
	{
		$first_letter = mb_strtoupper(mb_substr($string, 0, 1, $e), $e);
		$str_remaining = "";

		if ($lower_str_remaining)
			$str_remaining = mb_strtolower(mb_substr($string, 1, mb_strlen($string, $e), $e), $e);
		else
			$str_remaining = mb_substr($string, 1, mb_strlen($string, $e), $e);

		$string = $first_letter . $str_remaining;
	}
	else
		$string = ucfirst($string);

	return $string;
}

//--------------------------------------------------------------------------------------------------//
//--------------------------------------------------------------------------------------------------//

// Order ID
$oID = $_GET['oID'];

// check if custmer is allowed to see this order!
$order_query_check = os_db_query("SELECT customers_id FROM ".TABLE_ORDERS." WHERE orders_id='".(int)$oID."'");
$order_check = os_db_fetch_array($order_query_check);

if ($_SESSION['customer_id'] == $order_check['customers_id']) 
{
	// get order data
	include (dir_path('class').'order.php');
	$order = new order($oID);

	$company_query = os_db_query("SELECT * FROM ".TABLE_COMPANIES." WHERE orders_id='".(int)$oID."'");
	$company = os_db_fetch_array($company_query);
	$osTemplate->assign('company', $company);

	$osTemplate->assign('address_label_customer', os_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));

	// products
	$osTemplate->assign('order_data', $osccms->orders->getOrderData($oID));
	// total
	$order_total = $osccms->orders->getTotalData($oID);
	$osTemplate->assign('order_total', $order_total['data']);

	$osTemplate->assign('module_1', MODULE_PAYMENT_SCHET_1);
	$osTemplate->assign('module_2', MODULE_PAYMENT_SCHET_2);
	$osTemplate->assign('module_3', MODULE_PAYMENT_SCHET_3);
	$osTemplate->assign('module_4', MODULE_PAYMENT_SCHET_4);
	$osTemplate->assign('module_5', MODULE_PAYMENT_SCHET_5);
	$osTemplate->assign('module_6', MODULE_PAYMENT_SCHET_6);
	$osTemplate->assign('module_7', MODULE_PAYMENT_SCHET_7);
	$osTemplate->assign('module_8', MODULE_PAYMENT_SCHET_8);
	$osTemplate->assign('module_9', MODULE_PAYMENT_SCHET_9);
	$osTemplate->assign('module_10', MODULE_PAYMENT_SCHET_10);
	$osTemplate->assign('module_11', MODULE_PAYMENT_SCHET_11);
	$osTemplate->assign('module_12', MODULE_PAYMENT_SCHET_12);
	$osTemplate->assign('module_13', $order->customer['firstname']);
	$osTemplate->assign('module_14', $order->customer['lastname']);

	$osTemplate->assign('summa', SumProp($order->info['total'], $order->info['currency']));
	$osTemplate->assign('no_vat', '0');
	$osTemplate->assign('charset', $_SESSION['language_charset']); 
	$osTemplate->assign('oID', $oID);
	$osTemplate->assign('DATE', os_date_short($order->info['date_purchased']));
	$osTemplate->assign('ERROR', false);
}
else
{
	$osTemplate->assign('ERROR', 'You are not allowed to view this order!');
}

$osTemplate->caching = false;
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->display(CURRENT_TEMPLATE.'/module/schet.html');
?>