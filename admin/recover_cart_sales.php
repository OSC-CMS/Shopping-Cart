<?php
/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

 require('includes/top.php');
 
 require_once(_LIB.'phpmailer/class.phpmailer.php');

$osTemplate = new osTemplate;
require (get_path('class_admin').'currencies.php');
$currencies = new currencies();

$custid = $_POST['custid'];


if ($_GET['action']=='delete') { 
   $reset_query_raw = "delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id=$_GET[customer_id]"; 
   os_db_query($reset_query_raw); 
   $reset_query_raw2 = "delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id=$_GET[customer_id]"; 
   os_db_query($reset_query_raw2); 
   os_redirect(os_href_link(FILENAME_RECOVER_CART_SALES, 'delete=1&customer_id='. $_GET['customer_id'] . '&tdate=' . $_GET['tdate'])); 
} 
if ($_GET['delete']) { 
   $messageStack->add(MESSAGE_STACK_CUSTOMER_ID . $_GET['customer_id'] . MESSAGE_STACK_DELETE_SUCCESS, 'success'); 
} 
  $EMAIL_TTL = 90;
  $BASE_DAYS = 10;
  $SHOW_ATTRIBUTES = FALSE;
  $IS_FRIENDLY_EMAIL_HEADER = TRUE;
  $CURCUST_COLOR = "#0000FF";
  $UNCONTACTED_COLOR = "#80FFFF";
  $CONTACTED_COLOR = "#FF9FA2";

  function seadate($day)
  {
    $rawtime = strtotime("-".$day." days");
    $ndate = date("Ymd", $rawtime);
    return $ndate;
  }

  function cart_date_short($raw_date) {
    if ( ($raw_date == '00000000') || ($raw_date == '') ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 4, 2);
    $day = (int)substr($raw_date, 6, 2);

    if (@date('Y', mktime(0, 0, 0, $month, $day, $year)) == $year) {
      return date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, $year));
    } else {
      return preg_replace('/2037/' . '$', $year, date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, 2037)));
    }

  }

?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="boxCenter" width="100%" valign="top">
	<?php os_header('portfolio_package.gif', HEADING_TITLE_RECOVER); ?> 
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td align="left" colspan="2">
          <tr>

 <?php if (count($custid) > 0 ) {  ?>
         <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main" align="left" colspan=8 width="100%"><?php echo HEADING_EMAIL_SENT; ?> </td>
            </tr>
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" align="left" colspan="1" width="15%" nowrap><?php echo TABLE_HEADING_CUSTOMER; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="30%" nowrap>&nbsp;</td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="25%" nowrap>&nbsp;</td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="10%" nowrap>&nbsp;</td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="10%" nowrap>&nbsp;</td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="10%" nowrap>&nbsp;</td>
            </tr><tr>&nbsp;<br></tr>
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" align="left"   colspan="1"  width="15%" nowrap><?php echo TABLE_HEADING_MODEL; ?></td>
              <td class="dataTableHeadingContent" align="left"   colspan="2"  width="55%" nowrap><?php echo TABLE_HEADING_DESCRIPTION; ?></td>
              <td class="dataTableHeadingContent" align="center" colspan="1"  width="10%" nowrap> <?php echo TABLE_HEADING_QUANTY; ?></td>
              <td class="dataTableHeadingContent" align="right"  colspan="1"  width="10%" nowrap><?php echo TABLE_HEADING_PRICE; ?></td>
              <td class="dataTableHeadingContent" align="right"  colspan="1"  width="10%" nowrap><?php echo TABLE_HEADING_TOTAL; ?></td>
            </tr>

<?php
    if (count($custid) > 0 ) {
      foreach ($custid as $cid) {
  $query1 = os_db_query("select    cb.products_id pid,
                                    cb.customers_basket_quantity qty,
                                    cb.customers_basket_date_added bdate,
                                    cus.customers_firstname fname,
                                    cus.customers_lastname lname,
                                    cus.customers_email_address email
                          from      " . TABLE_CUSTOMERS_BASKET . " cb,
                                    " . TABLE_CUSTOMERS . " cus
                          where     cb.customers_id = cus.customers_id  and
                                    cus.customers_id = '".$cid."'
                          order by  cb.customers_basket_date_added desc ");



  $knt = mysql_num_rows($query1);
  for ($i = 0; $i < $knt; $i++) {
    $inrec = os_db_fetch_array($query1);


    if ($lastcid != $cid) {
      if ($lastcid != "") {
        $tcart_formated = $currencies->format($tprice);
        $cline .= "
        <tr>
          <td class='dataTableContent' align='right' colspan='6' nowrap><b>" . TABLE_CART_TOTAL . "</b>" . $tcart_formated . "</td>
        </tr>
        <tr>
        <!-- Delete Button //-->
          <td colspan='6' align='right'><a class=button href=" . os_href_link(FILENAME_RECOVER_CART_SALES,"action=delete&customer_id=$curcus&tdate=$tdate") . "><span>" . BUTTON_DELETE . "</button></a></td>
        </tr>\n";
       echo $cline;
      }
    $cline = "<tr> <td class='dataTableContent' align='left' colspan='6' nowrap><a href='" . os_href_link(FILENAME_CUSTOMERS, 'search=' . $inrec['lname'], 'NONSSL') . "'>" . $inrec['fname'] . " " . $inrec['lname'] . "</a>".$customer."</td></tr>";
     $tprice = 0;
     }
     $lastcid = $cid;

    $query2 = os_db_query("select  p.products_price price,
                                    p.products_model model,
                                    pd.products_name name
                            from    " . TABLE_PRODUCTS . " p,
                                    " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                                    " . TABLE_LANGUAGES . " l
                            where   p.products_id = '" . $inrec['pid'] . "' and
                                    pd.products_id = p.products_id and
                                    pd.language_id = '" .  $_SESSION['languages_id'] . "' ");

    $inrec2 = os_db_fetch_array($query2);

    $tprice = $tprice + ($inrec['qty'] * $inrec2['price']);

      $pprice_formated  = $currencies->format($inrec2['price']);
      $tpprice_formated = $currencies->format(($inrec['qty'] * $inrec2['price']));
      $cline .= "<tr class='dataTableRow'>
                    <td class='dataTableContent' align='left'   width='15%' nowrap>" . $inrec2['model'] . "</td>
                    <td class='dataTableContent' align='left'  colspan='2' width='55%'><a href='" . os_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $inrec['pid'] . '&origin=' . FILENAME_RECOVER_CART_SALES . '?page=' . $_GET['page'], 'NONSSL') . "'>" . $inrec2['name'] . "</a></td>
                    <td class='dataTableContent' align='center' width='10%' nowrap>" . $inrec['qty'] . "</td>
                    <td class='dataTableContent' align='right'  width='10%' nowrap>" . $pprice_formated . "</td>
                    <td class='dataTableContent' align='right'  width='10%' nowrap>" . $tpprice_formated . "</td>
                 </tr>";

  $mline .= $inrec['qty']." x ".$inrec2['name']."\n";
  }

   $cline .= "</td></tr>";

$custname = $inrec['fname']." ".$inrec['lname'];

				$osTemplate->assign('language', $_SESSION['language_admin']);
				$osTemplate->caching = false;

				$osTemplate->assign('tpl_path', DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/');
				$osTemplate->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.DIR_FS_CATALOG.'themes/admin/'.CURRENT_TEMPLATE.'/img/');

				$osTemplate->assign('STORE_NAME', STORE_NAME);
				$osTemplate->assign('NAME', $custname);
				$osTemplate->assign('MESSAGE', $_POST['message']);
				$osTemplate->assign('PRODUCTS', $mline);

				$html_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/recover_cart_mail.html');
				$txt_mail = $osTemplate->fetch(_MAIL.'admin/'.$_SESSION['language_admin'].'/recover_cart_mail.txt');

				os_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $inrec['email'], $custname, '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_TEXT_SUBJECT, $html_mail, $txt_mail);

 os_db_query("insert into " . TABLE_SCART . " (customers_id, dateadded ) values ('" . $cid . "', '" . seadate('0') . "')");
   echo $cline;
   $cline = "";
}
}
      $tcart_formated = $currencies->format($tprice);
      echo  "<tr> <td class='dataTableContent' align='right' colspan='8'><b>" . TABLE_CART_TOTAL . "</b>" . $tcart_formated . "</td> </tr>";
  echo "<tr><td colspan=6 align=center><a class=button href=" . os_href_link(FILENAME_RECOVER_CART_SALES) . ">" . TEXT_RETURN . "</a></td></tr>";
} else {
//

?>
<?php ?>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="pageHeading" align="left" width="100%" colspan="8">
                <?php  $tdate = $_POST['tdate'];
                    if ($_POST['tdate'] == '') $tdate = $BASE_DAYS;?>
                <?php echo os_draw_form('form', FILENAME_RECOVER_CART_SALES); ?>
                  <table align="right" width="100%" border="0">
                    <tr class="dataTableContent" align="right">
                      <td><?php echo DAYS_FIELD_PREFIX; ?><input type=text size=4 width=4 value=<?php echo $tdate; ?> name=tdate><?php echo DAYS_FIELD_POSTFIX; ?><span class="button"><button type="submit" value="<?php echo DAYS_FIELD_BUTTON; ?>"><?php echo DAYS_FIELD_BUTTON; ?></button></span></td>
                    </tr>
                  </table>
                </form>
              </td>
            </tr>
<?php echo os_draw_form('form', FILENAME_RECOVER_CART_SALES); ?>
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" align="left" colspan="2" width="10%" nowrap><?php echo TABLE_HEADING_CONTACT; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="15%" nowrap><?php echo TABLE_HEADING_DATE; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="30%" nowrap><?php echo TABLE_HEADING_CUSTOMER; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="2" width="30%" nowrap><?php echo TABLE_HEADING_EMAIL; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="2" width="15%" nowrap><?php echo TABLE_HEADING_PHONE; ?></td>
            </tr><tr>&nbsp;<br></tr>
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" align="left"   colspan="2"  width="10%" nowrap>&nbsp; </td>
              <td class="dataTableHeadingContent" align="left"   colspan="1"  width="15%" nowrap><?php echo TABLE_HEADING_MODEL; ?></td>
              <td class="dataTableHeadingContent" align="left"   colspan="2" width="55%" nowrap><?php echo TABLE_HEADING_DESCRIPTION; ?></td>
              <td class="dataTableHeadingContent" align="center" colspan="1" width="5%" nowrap> <?php echo TABLE_HEADING_QUANTY; ?></td>
              <td class="dataTableHeadingContent" align="right"  colspan="1"  width="5%" nowrap><?php echo TABLE_HEADING_PRICE; ?></td>
              <td class="dataTableHeadingContent" align="right"  colspan="1" width="10%" nowrap><?php echo TABLE_HEADING_TOTAL; ?></td>
            </tr>

<?php
 $tdate = $_POST['tdate'];
 if ($_POST['tdate'] == '') $tdate = $BASE_DAYS;
 $ndate = seadate($tdate);
 $query1 = os_db_query("select cb.customers_id cid,
                                cb.products_id pid,
                                cb.customers_basket_quantity qty,
                                cb.customers_basket_date_added bdate,
                                cus.customers_firstname fname,
                                cus.customers_lastname lname,
                                cus.customers_telephone phone,
                                cus.customers_email_address email
                         from   " . TABLE_CUSTOMERS_BASKET . " cb,
                                " . TABLE_CUSTOMERS . " cus
                         where  cb.customers_basket_date_added >= '" . $ndate . "' and
                                cb.customers_id = cus.customers_id order by cb.customers_basket_date_added desc,
                                cb.customers_id ");
 $results = 0;
 $curcus = "";
 $tprice = 0;
 $totalAll = 0;
 $knt = mysql_num_rows($query1);
 $first_line = true;

 for ($i = 0; $i <= $knt; $i++)
 {
  $inrec = os_db_fetch_array($query1);

    if ($curcus != $inrec['cid'])
    {
      $totalAll += $tprice;
      $tcart_formated = $currencies->format($tprice);
      $cline .= "       </td>
                        <tr>
                          <td class='dataTableContent' align='right' colspan='8'><b>" . TABLE_CART_TOTAL . "</b>" . $tcart_formated . "</td>
                        </tr>
                        <tr>
                          <td colspan='8' align='right'><a class='button' href=" . os_href_link(FILENAME_RECOVER_CART_SALES,"action=delete&customer_id=$curcus&tdate=$tdate") . "><span>" . BUTTON_DELETE . "</span></a></td>
                        </tr>";

      if ($curcus != "")
        echo $cline;

      $curcus = $inrec['cid'];
      if ($curcus != "") {
      $tprice = 0;

  $fcolor = $UNCONTACTED_COLOR;
  $sentdate = "";
  $customer = "";
  $donequery =
      os_db_query("select * from ". TABLE_SCART ." where customers_id = '".$curcus."'");
  $emailttl = seadate($EMAIL_TTL);
  if (mysql_num_rows($donequery) > 0) {
    $ttl = os_db_fetch_array($donequery);
    if ($emailttl <= $ttl['dateadded']) {
      $sentdate = $ttl['dateadded'];
      $fcolor = $CONTACTED_COLOR;
    }
  }
  $ccquery = os_db_query("select * from " . TABLE_ORDERS . " where customers_id = '".$curcus."'" );
  if (mysql_num_rows($ccquery) > 0) $customer = '&nbsp;[<font color="' . $CURCUST_COLOR . '">' . TEXT_CURRENT_CUSTOMER . '</font>]';

    $sentInfo = TEXT_NOT_CONTACTED;

    if ($sentdate != ''){
      $sentInfo = cart_date_short($sentdate);
    }

      $cline = "
        <tr bgcolor=" . $fcolor . ">
          <td class='dataTableContent' align='center' width='1%'>" . os_draw_checkbox_field('custid[]', $curcus) . "</td>
          <td class='dataTableContent' align='left' width='9%' nowrap><b>" . $sentInfo . "</b></td>
          <td class='dataTableContent' align='left' width='15%' nowrap> " . cart_date_short($inrec['bdate']) . "</td>
          <td class='dataTableContent' align='left' width='30%' nowrap><a href='" . os_href_link(FILENAME_CUSTOMERS, 'search=' . $inrec['lname'], 'NONSSL') . "'>" . $inrec['fname'] . " " . $inrec['lname'] . "</a>".$customer."</td>
          <td class='dataTableContent' align='left' colspan='2' width='30%' nowrap><a href='" . os_href_link('mail.php', 'selected_box=tools&customer=' . $inrec['email']) . "'>" . $inrec['email'] . "</a></td>
          <td class='dataTableContent' align='left' colspan='2' width='15%' nowrap>" . $inrec['phone'] . "</td>
        </tr>";
      }
    }

    $query2 = os_db_query("select  p.products_price price,
                                    p.products_model model,
                                    pd.products_name name
                            from    " . TABLE_PRODUCTS . " p,
                                    " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                                    " . TABLE_LANGUAGES . " l
                            where   p.products_id = '" . $inrec['pid'] . "' and
                                    pd.products_id = p.products_id and
                                    pd.language_id = '" . $_SESSION['languages_id'] . "' ");

    $inrec2 = os_db_fetch_array($query2);

    $prodAttribs = '';

    if ($SHOW_ATTRIBUTES) {
      $attribquery = os_db_query("select  cba.products_id pid,
                                           po.products_options_name poname,
                                           pov.products_options_values_name povname
                                   from    " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " cba,
                                           " . TABLE_PRODUCTS_OPTIONS . " po,
                                           " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov,
                                           " . TABLE_LANGUAGES . " l
                                   where   cba.products_id ='" . $inrec['pid'] . "' and
                                           po.products_options_id = cba.products_options_id and
                                           pov.products_options_values_id = cba.products_options_value_id and
                                           po.language_id = '" . $_SESSION['languages_id'] . "' and
                                           pov.language_id = '" . $_SESSION['languages_id'] . "'
                                ");
      $hasAttributes = false;

      if (os_db_num_rows($attribquery)){
        $hasAttributes = true;
        $prodAttribs = '<br>';

        while ($attribrecs = os_db_fetch_array($attribquery)){
          $prodAttribs .= '<small><i> - ' . $attribrecs['poname'] . ' ' . $attribrecs['povname'] . '</i></small><br>';
        }
      }
    }

    $tprice = $tprice + ($inrec['qty'] * $inrec2['price']);

    if ($inrec['qty'] != 0)
    {
      $pprice_formated  = $currencies->format($inrec2['price']);
      $tpprice_formated = $currencies->format(($inrec['qty'] * $inrec2['price']));

      $cline .= "<tr class='dataTableRow'>
                    <td class='dataTableContent' align='left' vAlign='top' colspan='2' width='12%' nowrap> &nbsp;</td>
                    <td class='dataTableContent' align='left' vAlign='top' width='13%' nowrap>" . $inrec2['model'] . "</td>
                    <td class='dataTableContent' align='left' vAlign='top' colspan='2' width='55%'><a href='" . os_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $inrec['pid'] . '&origin=' . FILENAME_RECOVER_CART_SALES . '?page=' . $_GET['page'], 'NONSSL') . "'><b>" . $inrec2['name'] . "</b></a>
                    " . $prodAttribs . "
                    </td>
                    <td class='dataTableContent' align='center' vAlign='top' width='5%' nowrap>" . $inrec['qty'] . "</td>
                    <td class='dataTableContent' align='right'  vAlign='top' width='5%' nowrap>" . $pprice_formated . "</td>
                    <td class='dataTableContent' align='right'  vAlign='top' width='10%' nowrap>" . $tpprice_formated . "</td>
                 </tr>";
    }
  }
  $totalAll_formated = $currencies->format($totalAll);
  $cline = "<tr></tr><td class='dataTableContent' align='right' colspan='8'><b>" . TABLE_GRAND_TOTAL . "</b>" . $totalAll_formated . "</td>
              </tr>";

  echo $cline;
 echo "<tr><td colspan=8><b>". PSMSG ."</b><br>". os_draw_textarea_field('message', 'soft', '80', '5') ."<br><span class=\"button\"><button type=\"submit\" name=\"submit_button\" value=\"".TEXT_SEND_EMAIL."\">".TEXT_SEND_EMAIL."</button></span>
 </td></tr>";
?>
 </form>
<?php }

?>

            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?php $main->bottom(); ?>