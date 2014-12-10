<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

class inwords {

var $diw=Array(    0 =>    Array(    0  => Array( 0=> "ноль",    1=>1),
1  => Array( 0=> "",        1=>2),
2  => Array( 0=> "",        1=>3),
3  => Array( 0=> "три",        1=>0),
4  => Array( 0=> "четыре",    1=>0),
5  => Array( 0=> "пять",    1=>1),
6  => Array( 0=> "шесть",    1=>1),
7  => Array( 0=> "семь",    1=>1),
8  => Array( 0=> "восемь",    1=>1),
9  => Array( 0=> "девять",    1=>1),
10 => Array( 0=> "десять",    1=>1),
11 => Array( 0=> "одинадцать",    1=>1),
12 => Array( 0=> "двенадцать",    1=>1),
13 => Array( 0=> "тринадцать",    1=>1),
14 => Array( 0=> "четырнадцать",1=>1),
15 => Array( 0=> "пятнадцать",    1=>1),
16 => Array( 0=> "шестнадцать",    1=>1),
17 => Array( 0=> "семнадцать",    1=>1),
18 => Array( 0=> "восемнадцать",1=>1),
19 => Array( 0=> "девятнадцать",1=>1)
),
1 =>    Array(    2  => Array( 0=> "двадцать",    1=>1),
3  => Array( 0=> "тридцать",    1=>1),
4  => Array( 0=> "сорок",    1=>1),
5  => Array( 0=> "пятьдесят",    1=>1),
6  => Array( 0=> "шестьдесят",    1=>1),
7  => Array( 0=> "семьдесят",    1=>1),
8  => Array( 0=> "восемьдесят",    1=>1),
9  => Array( 0=> "девяносто",    1=>1)
),
2 =>    Array(    1  => Array( 0=> "сто",        1=>1),
2  => Array( 0=> "двести",    1=>1),
3  => Array( 0=> "триста",    1=>1),
4  => Array( 0=> "четыреста",    1=>1),
5  => Array( 0=> "пятьсот",    1=>1),
6  => Array( 0=> "шестьсот",    1=>1),
7  => Array( 0=> "семьсот",    1=>1),
8  => Array( 0=> "восемьсот",    1=>1),
9  => Array( 0=> "девятьсот",    1=>1)
)
);

var $nom=Array(    0 => Array(0=>"копейки",  1=>"копеек",    2=>"одна копейка", 3=>"две копейки"),
1 => Array(0=>"рубля",    1=>"рублей",    2=>"один рубль",   3=>"два рубля"),
2 => Array(0=>"тысячи",   1=>"тысяч",     2=>"одна тысяча",  3=>"две тысячи"),
3 => Array(0=>"миллиона", 1=>"миллионов", 2=>"один миллион", 3=>"два миллиона"),
4 => Array(0=>"миллиарда",1=>"миллиардов",2=>"один миллиард",3=>"два миллиарда"),
/* :))) */
5 => Array(0=>"триллиона",1=>"триллионов",2=>"один триллион",3=>"два триллиона")
);

var $out_rub;

function get($summ){
if($summ>=1) $this->out_rub=0;
else $this->out_rub=1;
$summ_rub= doubleval(sprintf("%0.0f",$summ));
if(($summ_rub-$summ)>0) $summ_rub--;
$summ_kop= doubleval(sprintf("%0.2f",$summ-$summ_rub))*100;
$kop=$this->get_string($summ_kop,0);
$retval="";
for($i=1;$i<6&&$summ_rub>=1;$i++):
$summ_tmp=$summ_rub/1000;
$summ_part=doubleval(sprintf("%0.3f",$summ_tmp-intval($summ_tmp)))*1000;
$summ_rub= doubleval(sprintf("%0.0f",$summ_tmp));
if(($summ_rub-$summ_tmp)>0) $summ_rub--;
$retval=$this->get_string($summ_part,$i)." ".$retval;
endfor;
if(($this->out_rub)==0) $retval.=" рублей";
return $retval." ".$kop;
}

function get_string($summ,$nominal){
$retval="";
$nom=-1;
$summ=round($summ);
if(($nominal==0&&$summ<100)||($nominal>0&&$nominal<6&&$summ<1000)):
$s2=intval($summ/100);
if($s2>0):
$retval.=" ".$this->diw[2][$s2][0];
$nom=$this->diw[2][$s2][1];
endif;
$sx=doubleval(sprintf("%0.0f",$summ-$s2*100));
if(($sx-($summ-$s2*100))>0) $sx--;
if(($sx<20&&$sx>0)||($sx==0&&$nominal==0)):
$retval.=" ".$this->diw[0][$sx][0];
$nom=$this->diw[0][$sx][1];
else:
$s1=doubleval(sprintf("%0.0f",$sx/10));
if(($s1-$sx/10)>0)$s1--;
$s0=doubleval($summ-$s2*100-$s1*10);
if($s1>0):
$retval.=" ".$this->diw[1][$s1][0];
$nom=$this->diw[1][$s1][1];
endif;
if($s0>0):
$retval.=" ".$this->diw[0][$s0][0];
$nom=$this->diw[0][$s0][1];
endif;
endif;
endif;
if($nom>=0):
$retval.=" ".$this->nom[$nominal][$nom];
if($nominal==1) $this->out_rub=1;
endif;
return trim($retval);
}

}

global $cartet, $osTemplate;

// Order ID
$oID = $_GET['oID'];

$order_query_check = os_db_query("SELECT customers_id FROM ".TABLE_ORDERS." WHERE orders_id='".(int)$oID."'");
$order_check = os_db_fetch_array($order_query_check);

if (($_SESSION['customer_id'] == $order_check['customers_id']) OR ($_SESSION['customers_status']['customers_status_id'] == 0))
{
	/*$company_query = os_db_query("SELECT * FROM ".TABLE_COMPANIES." WHERE orders_id='".(int)$_GET['oID']."'");
	$company = os_db_fetch_array($company_query);

	$osTemplate->assign('company', $company);*/

	include(get_path('class_admin') . 'order.php');
	$order = new order($oID);

	$osTemplate->assign('address_label_customer',os_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
	$osTemplate->assign('address_label_shipping',os_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
	$osTemplate->assign('address_label_payment',os_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
	$osTemplate->assign('phone',$order->customer['telephone']);
	$osTemplate->assign('email',$order->customer['email_address']);
	$osTemplate->assign('csID',$order->customer['csID']);

	/*$osTemplate->assign('1', MODULE_PAYMENT_SCHET_1);
	$osTemplate->assign('2', MODULE_PAYMENT_SCHET_2);
	$osTemplate->assign('3', MODULE_PAYMENT_SCHET_3);
	$osTemplate->assign('4', MODULE_PAYMENT_SCHET_4);
	$osTemplate->assign('5', MODULE_PAYMENT_SCHET_5);
	$osTemplate->assign('6', MODULE_PAYMENT_SCHET_6);
	$osTemplate->assign('7', MODULE_PAYMENT_SCHET_7);
	$osTemplate->assign('8', MODULE_PAYMENT_SCHET_8);
	$osTemplate->assign('9', MODULE_PAYMENT_SCHET_9);
	$osTemplate->assign('10', MODULE_PAYMENT_SCHET_10);
	$osTemplate->assign('11', MODULE_PAYMENT_SCHET_11);
	$osTemplate->assign('12', MODULE_PAYMENT_SCHET_12);
	$osTemplate->assign('13', $order->customer['firstname']);
	$osTemplate->assign('14', $order->customer['lastname']);
	$osTemplate->assign('15', $order->customer['telephone']);
	$osTemplate->assign('16', $order->customer['email_address']);*/

	/*$total_summ_query = os_db_query("select value from ".TABLE_ORDERS_TOTAL." where orders_id = '".(int)$oID."' and class = 'ot_total'");
	$total_summ = os_db_fetch_array($total_summ_query);

	$iw = new inwords;

	$osTemplate->assign('summa', $iw->get($total_summ['value']));*/

	$osTemplate->assign('language', $_SESSION['language']);
	$osTemplate->assign('charset', $_SESSION['language_charset']);
	$osTemplate->assign('logo_path', http_path('images'));
	$osTemplate->assign('oID', $oID);

	if ($order->info['payment_method']!='' && $order->info['payment_method']!='no_payment')
	{
		include (DIR_FS_DOCUMENT_ROOT.'/modules/payment/'.$order->info['payment_method'].'/'.$_SESSION['language'].'.php');
		$payment_method=constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
		$osTemplate->assign('PAYMENT_METHOD',$payment_method);
	}

	if ($order->info['shipping_class'] != '')
	{
		$shipping_method_query = os_db_query("select title from ".TABLE_ORDERS_TOTAL." where orders_id = '".(int)$oID."' and class = 'ot_shipping'");
		$shipping_method = os_db_fetch_array($shipping_method_query);
		$order_shipping_text = ((substr($shipping_method['title'], -1) == ':') ? substr(strip_tags($shipping_method['title']), 0, -1) : strip_tags($shipping_method['title']));
		$osTemplate->assign('SHIPPING_METHOD', $order_shipping_text);
	}

	$osTemplate->assign('COMMENTS', $order->info['comments']);
	$osTemplate->assign('DATE',os_date_short($order->info['date_purchased']));
	// products
	$osTemplate->assign('order_data', $cartet->orders->getProducts($oID));
	// total
	$order_total = $cartet->orders->getTotal($oID);
	$osTemplate->assign('order_total', $order_total['data']);
}
else
{
	$osTemplate->assign('ERROR', 'You are not allowed to view this order!');
}

$osTemplate->caching = false;
$osTemplate->assign('language', $_SESSION['language']);
$osTemplate->display(dirname(__FILE__).'/print_packingslip_page.html');