<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*
*	Based on: osCommerce, nextcommerce, xt:Commerce
*	Released under the GNU General Public License
*
*---------------------------------------------------------
*/

class AccountCheck {


/* Folgende Returncodes werden ьbergeben                                      */
/*                                                                            */
/* 0 -> Kontonummer & BLZ OK                                                  */
/* 1 -> Kontonummer & BLZ passen nicht                                        */
/* 2 -> Fьr diese Kontonummer ist kein Prьfziffernverfahren definiert         */
/* 3 -> Dieses Prьfziffernverfahren ist noch nicht implementiert              */
/* 4 -> Diese Kontonummer ist technisch nicht prьfbar                         */
/* 5 -> BLZ nicht gefunden                                                    */
/* 8 -> Keine BLZ ьbergeben                                                   */
/* 9 -> Keine Kontonummer ьbergeben                                           */
/* 10 -> Kein Kontoinhaber ьbergeben                                          */
/* 128 -> interner Fehler,der zeigt, das eine Methode nicht implementiert ist */
/*                                                                            */

var $Bankname; // Enthдlt den Namen der Bank bei der Suche nach BLZ
var $PRZ; //Enthдlt die Prьfziffer

////
// Diese function gibt die Bankinformationen aus der csv-Datei zurьck*/
  function csv_query($blz) {
    $cdata = -1;
    $fp = fopen(dir_path('includes') . 'data/blz.csv', 'r');
    while ($data = fgetcsv($fp, 1024, ";")) {
      if ($data[0] == $blz){
        $cdata = array ('blz' => $data[0],
                        'bankname' => $data[1],
                        'prz' => $data[2]);
      }
    }
    return $cdata;
  }


  function db_query($blz) {
    $blz_query = os_db_query("SELECT * from " . TABLE_BANKTRANSFER . " WHERE blz = '" . $blz . "'");
    if (os_db_num_rows($blz_query)){
      $data = os_db_fetch_array ($blz_query);
    }else
      $data = -1;
    return $data;
  }

  function query($blz) {
    if (MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ == 'true' && defined(MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ))
      $data = $this->db_query($blz);
    else
      $data = $this->csv_query($blz);
    return $data;
  }


  function OnlyOne($Digit) {
    return $Digit = $Digit % 10;
  }  
  function CrossSum($Digit) {
    $CrossSum = $Digit;
    if ($Digit > 9) {
      $Help1 = substr($Digit,0,1);
      $Help2 = substr($Digit,1,1);
      $CrossSum = (int) $Help1 + (int) $Help2;
    }
    return $CrossSum;
  } 
  function ExpandAccount($AccountNo) {
    $AccountNo = str_pad($AccountNo, 10, "0", STR_PAD_LEFT);

    while (strlen($AccountNo) > 10) {
      $AccountNo = substr($AccountNo,1);
    }
    return $AccountNo;
  }  


  function Method00($AccountNo,$Significance,$Checkpoint,$Modulator=10) {
     $Help = 0;
     $Method00 = 1;
     $AccountNo = $this->ExpandAccount($AccountNo);
     for ($Run = 0; $Run < strlen($Significance); $Run++) {
       $Help += $this->CrossSum(substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
     }
     $Help = $Help % $Modulator;
     $Checksum = $Modulator - $Help;

     if ($Checksum == $Modulator) {
       $Checksum = 0;
     }
     if ($Checksum == substr($AccountNo,$Checkpoint-1,1)) {
       $Method00 = 0;
     }
     return $Method00;
   } 


  function Method01($AccountNo, $Significance) {
    $Help = 0;
     $Method01 = 1;
     $AccountNo = $this->ExpandAccount($AccountNo);
     for ($Run = 0; $Run < strlen($Significance); $Run++) {
       $Help += substr($AccountNo,$Run,1) * substr($Significance,$Run,1);
     }
     $Help = $this->OnlyOne($Help);
     $Checksum = 10 - $Help;

     if ($Checksum == 10) {
       $Checksum = 0;
     }
     if ($Checksum == substr($AccountNo,-1)) {
       $Method01 = 0;
     }
     return $Method01;
  } 


  function Method02($AccountNo , $Significance, $Modified) {
    $Help = 0;
     $Method02 = 1;
     $AccountNo = $this->ExpandAccount($AccountNo);
     switch ($Modified) {
       case FALSE :
        for ($Run = 0;$Run < strlen($Significance);$Run++) {
          $Help += substr($AccountNo,$Run,1) * substr($Significance,$Run,1);
        }
        break;
      case TRUE :
        for ($Run = 0;$Run < strlen($Significance);$Run++) {
          $Help += substr($AccountNo,$Run,1) * HexDec(substr($Significance,$Run,1));
        }
        break;
    }
    $Help = $Help % 11;
    if ($Help == 0) {
      $Help = 11;
    }
    if ($Help <> 1) {
      $Checksum = 11 - $Help;
      if ($Checksum == substr($AccountNo,-1)) {
        $Method02 = 0;
      }
    }
     return $Method02;
  } 

  function Method06($AccountNo, $Significance, $Modified, $Checkpoint, $Modulator) {
    $Help = 0;
    $Method06 = 1;
     $AccountNo = $this->ExpandAccount($AccountNo);
     switch ($Modified) {
       case FALSE :
        for ($Run = 0; $Run < strlen($Significance);$Run++) {
          $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
        }
        break;
      case TRUE  :
        for ($Run = 0; $Run < strlen($Significance);$Run++) {
          $Help += (substr($AccountNo,$Run,1) * HexDec(substr($Significance,$Run,1)));
        }
        break;
    }
    $Help = $Help % $Modulator;
    $Checksum = $Modulator - $Help;
    if ($Help < 2 && $Modulator == 11) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,$Checkpoint-1,1)) {
      $Method06 = 0;
    }
    return $Method06;
  } 

  function Method16($AccountNo , $Significance, $Checkpoint) {
    $Help = 0;
    $Method16 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    $Checksum = 11 - $Help;
    if ($Help == 0) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,$Checkpoint-1,1)) {
       $Method16 = 0;
     }
     if ($Help == 1) {
       if ($Checksum == substr($AccountNo,Checkpoint - 2,1)) {
         $Method16 = 0;
       }
     }
     return $Method16;
   } 

  function Method90($AccountNo , $Significance ,$Checkpoint, $Modulator) {
    $Help = 0;
    $Method90 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % $Modulator;
    $Checksum = $Modulator - $Help;
    if ($Help == 0) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,$Checkpoint -1, 1)) {
      $Method90 = 0;
    }
    return $Method90;
  } 

  function Mark00($AccountNo) {
    $Mark00 = $this->Method00($AccountNo, '212121212', 10);
    return $Mark00;
  }  

  function Mark01($AccountNo) {
    $Mark01 = $this->Method01($AccountNo, '173173173');
    return $Mark01;
  }

  function Mark02($AccountNo) {
    $Mark02 = $this->Method02($AccountNo, '298765432', FALSE);
    return $Mark02;
  }

  function Mark03($AccountNo) {
    $Mark03 = $this->Method01($AccountNo, '212121212');
    return $Mark03;
  } 

  function Mark04($AccountNo) {
    $Mark04 = $this->Method02($AccountNo, '432765432', FALSE);
    return $Mark04;
  } 

  function Mark05($AccountNo) {
    $Mark05 = $this->Method01($AccountNo, '137137137');
    return $Mark05;
  } 

  function Mark06($AccountNo) {
    $Mark06 = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
    return $Mark06;
  }

  function Mark07($AccountNo) {
    $Mark07 = $this->Method02($AccountNo, 'A98765432', TRUE);
    return $Mark07;
  } 

  function Mark08($AccountNo) {
    $Mark08 = 1;
    if ($AccountNo > 60000) {
      $Mark08 = $this->Method00($AccountNo, '212121212', 10);
    }
    return $Mark08;
  } 

  function Mark09($AccountNo) {
    $Mark09 = 2;
    return $Mark09;
  } 

  function Mark10($AccountNo) {
    $Mark10 = $this->Method06($AccountNo, 'A98765432', TRUE, 10, 11);
    return $Mark10;
  } 

  function Mark11($AccountNo) {
    $Significance = 'A98765432';
    $Help = 0;
    $Mark11 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * HexDec(substr($Significance,$Run,1)));
    }
    $Help = $Help % 11;
    $Checksum = 11 - $Help;
    if ($Help == 0) {
      $Checksum = 0;
    }
    if ($Help == 1) {
      $Checksum = 9;
    }
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark11 = 0;
    }
    return $Mark11;
  }

  function Mark12($AccountNo) {
    $Mark12 = $this->Method01($AccountNo, '731731731');
    return $Mark12;
  }

  function Mark13($AccountNo) {
    $Help = $this->Method00($AccountNo, '0121212', 8);
    if ($Help == 1) {
      if (Substr($AccountNo,-2) <> '00') {
        $Help = $this->Method00(substr($this->ExpandAccount($AccountNo), 2) . '00', '0121212', 8);
      }
    }
    $Mark13 = $Help;
    return $Mark13;
  }

  function Mark14($AccountNo) {
    $Mark14 = $this->Method02($AccountNo, '000765432', FALSE);
    return $Mark14;
  } 

  function Mark15($AccountNo) {
    $Mark15 = $this->Method06($AccountNo, '000005432', FALSE, 10, 11);
    return $Mark15;
  } 

  function Mark16($AccountNo) {
    $Mark16 = $this->Method16($AccountNo, '432765432', 10);
    return $Mark16;
  }

  function Mark17($AccountNo) {
    $Significance = '0121212';
    $Help = 0;
    $Help2 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += ($this->CrossSum(substr($AccountNo,$Run,1) * substr($Significance,$Run,1)));
    }
    $Help = $Help -1;
    $Checksum = $Help % 11;
    $Checksum = 10 - $Checksum;
    if ($Checksum == 10) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,7,1)) {
      $Help2 = 0;
    }
    $Mark17 = $Help2;
    return $Mark17;
  } 

  function Mark18($AccountNo) {
    $Mark18 = $this->Method01($AccountNo, '317931793');
    return $Mark18;
  }  

  function Mark19($AccountNo) {
    $Mark19 = $this->Method06($AccountNo, '198765432', FALSE, 10, 11);
    return $Mark19;
  } 

  function Mark20($AccountNo) {
    $Mark20 = $this->Method06($AccountNo, '398765432', FALSE, 10, 11);
    return $Mark20;
  } 

  function Mark21($AccountNo) {
    $Significance = '212121212';
    $Help = 0;
    $Mark21 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += $this->CrossSum(substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $this->CrossSum($Help);
     $Checksum = 10 - $Help;
     if ($Checksum == 10) {
       $Checksum = 0;
     }
     if ($Checksum == substr($AccountNo,-1)) {
       $Mark21 = 0;
     }
     return $Mark21;
   } 

  function Mark22($AccountNo) {
    $Significance = '313131313';
    $Help = 0;
    $Mark22 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Zwischenwert = (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
       $Help += $this->OnlyOne($Zwischenwert);
     }
     $Checksum = ceil($Help/10)*10 - $Help;
     if ($Checksum == substr($AccountNo,-1)) {
      $Mark22 = 0;
    }
    return $Mark22;
  } 

  function Mark23($AccountNo) {
    $Mark23 = $this->Method16($AccountNo, '765432', 7);
    return $Mark23;
  }  

  function Mark24($AccountNo) {
    $Significance = '123123123';
    $Help = 0;
    $Mark24 = 1;
    switch (substr($AccountNo,0,1)) {
      case 3 :
      case 4 :
      case 5 :
      case 6 :
        break;
      case 9 :
        break;
    }
    while (substr($AccountNo,0,1)==0){
      $AccountNo = substr($AccountNo,1);
    }

    for ($Run = 0;$Run < strlen($AccountNo)-1;$Run++ ) {
      $ZwischenHilf = substr($AccountNo,$Run,1) * substr($Significance,$Run,1) + substr($Significance,$Run,1);
      $Help += $ZwischenHilf % 11;
    }

    $Checksum = $this->OnlyOne($Help);

    if ($Checksum == substr($AccountNo,-1)) {
      $Mark24 = 0;
    }
    return $Mark24;
  }  

  function Mark25($AccountNo) {
    $Significance = '098765432';
    $Falsch = FALSE;
    $Help = 0;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    $Checksum = 11 - $Help;
    if ($Checksum == 11) {
      $Checksum = 0;
    }
    if ($Checksum == 10) {
      $Checksum = 0;
      if ((substr($AccountNo,1,1) <> '8') and (substr($AccountNo,1,1) <> '9')) {
        $Mark25 = 1;
        $Falsch = TRUE;
      }
    }
    if ($Falsch == FALSE) {
      if ($Checksum == substr($AccountNo,-1)) {
        $Mark25 = 0;
      } else {
        $Mark25 = 1;
      }
    }
    return $Mark25;
  }  

  function Mark26($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (Substr($AccountNo,-2) == '00') {
      $AccountNo = Substr($AccountNo,2) . '00';
    }
    $Mark26 = $this->Method06($AccountNo,'2345672',FALSE,8,11);
    return $Mark26;
  } 

  function Mark27($AccountNo) {
    if ((int)$AccountNo < 1000000000) {
      $Mark27 = $this->Method00($AccountNo, '212121212', 10);
    } else {
      $Mark27 = $this->Mark29($AccountNo);
    }
    return $Mark27;
  }  

  function Mark28($AccountNo) {
    $Mark28 = $this->Method06($AccountNo, '8765432', FALSE, 8, 11);
    return $Mark28;
  } 

  function Mark29($AccountNo) {
    $Transform = '143214321';
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Mark29 = 1;
    $Help = 0;
    for ($Run = 0;$Run < strlen($Transform);$Run++) {
      $ToAdd = 0;
      switch (substr($Transform,$Run,1)) {
        case '1' :
          switch (substr($AccountNo,$Run,1)) {
            Case '0' :
              $ToAdd = 0;
              break;
            Case '1' :
              $ToAdd = 1;
              break;
            Case '2' :
              $ToAdd = 5;
              break;
            Case '3' :
              $ToAdd = 9;
              break;
            Case '4' :
              $ToAdd = 3;
              break;
            Case '5' :
              $ToAdd = 7;
              break;
            Case '6' :
              $ToAdd = 4;
              break;
            Case '7' :
              $ToAdd = 8;
              break;
            Case '8' :
              $ToAdd = 2;
              break;
            Case '9' :
              $ToAdd = 6;
              break;
          }
          break;
        case '2' :
          switch (substr($AccountNo,$Run,1)) {
            Case '0' :
              $ToAdd = 0;
              break;
            Case '1' :
              $ToAdd = 1;
              break;
            Case '2' :
              $ToAdd = 7;
              break;
            Case '3' :
              $ToAdd = 6;
              break;
            Case '4' :
              $ToAdd = 9;
              break;
            Case '5' :
              $ToAdd = 8;
              break;
            Case '6' :
              $ToAdd = 3;
              break;
            Case '7' :
              $ToAdd = 2;
              break;
            Case '8' :
              $ToAdd = 5;
              break;
            Case '9' :
              $ToAdd = 4;
              break;
          }
          break;
        case '3' :
          switch (substr($AccountNo,$Run,1)) {
            Case '0' :
              $ToAdd = 0;
              break;
            Case '1' :
              $ToAdd = 1;
              break;
            Case '2' :
              $ToAdd = 8;
              break;
            Case '3' :
              $ToAdd = 4;
              break;
            Case '4' :
              $ToAdd = 6;
              break;
            Case '5' :
              $ToAdd = 2;
              break;
            Case '6' :
              $ToAdd = 9;
              break;
            Case '7' :
              $ToAdd = 5;
              break;
            Case '8' :
              $ToAdd = 7;
              break;
            Case '9' :
              $ToAdd = 3;
              break;
          }
          break;
        case '4' :
          switch (substr($AccountNo,$Run,1)) {
            Case '0' :
              $ToAdd = 0;
              break;
            Case '1' :
              $ToAdd = 1;
              break;
            Case '2' :
              $ToAdd = 2;
              break;
            Case '3' :
              $ToAdd = 3;
              break;
            Case '4' :
              $ToAdd = 4;
              break;
            Case '5' :
              $ToAdd = 5;
              break;
            Case '6' :
              $ToAdd = 6;
              break;
            Case '7' :
              $ToAdd = 7;
              break;
            Case '8' :
              $ToAdd = 8;
              break;
            Case '9' :
              $ToAdd = 9;
              break;
          }
          break;
      }
      $Help += $ToAdd;
    }
    $Help = $this->OnlyOne($Help);
    $Checksum = 10 - $Help;
    if ($Checksum = substr($AccountNo,-1)) {
      $Mark29 = 0;
    }
    return $Mark29;
  }

  function Mark30($AccountNo) {
    $Significance = '200001212';
    $Help = 0;
    $Mark30 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++){
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $this->OnlyOne($Help);
    $Checksum = 10 - $Help;
    if ($Checksum == 10) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark30 = 0;
    }
    return $Mark30;
  }  

  function Mark31($AccountNo) {
    $Significance = '123456789';
    $Help = 0;
    $Mark31 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++){
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Checksum = $Help % 11;
    if($Checksum == substr($AccountNo,-1)) {
      $Mark31 = 0;
    }
    return $Mark31;
  }  

  function Mark32($AccountNo) {
    $Mark32 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
    return $Mark32;
  } 

  function Mark33($AccountNo) {
    $Mark33 = $this->Method06($AccountNo, '000065432', FALSE, 10, 11);
    return $Mark33;
  }

  function Mark34($AccountNo) {
    $Mark34 = $this->Method06($AccountNo, '79A5842', TRUE, 8, 11);
    return $Mark34;
  } 

  function Mark35($AccountNo) {
    $Mark35 = 3;
    return $Mark35;
  }  

  function Mark36($AccountNo) {
    $Mark36 = $this->Method06($AccountNo, '000005842', FALSE, 10, 11);
    return $Mark36;
  } 

  function Mark37($AccountNo) {
    $Mark37 = $this->Method06($AccountNo, '0000A5842', TRUE, 10, 11);
    return $Mark37;
  } 

  function Mark38($AccountNo) {
    $Mark38 = $this->Method06($AccountNo, '0009A5842', TRUE, 10, 11);
    return $Mark38;
  }  

  function Mark39($AccountNo) {
    $Mark39 = $this->Method06($AccountNo, '0079A5842', TRUE, 10, 11);
    return $Mark39;
  } 

  function Mark40($AccountNo) {
    $Mark40 = $this->Method06($AccountNo, '6379A5842', TRUE, 10, 11);
    return $Mark40;
  } 

  function Mark41($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,3,1) == '9') {
      $AccountNo = '000'. substr($AccountNo,3);
    }
    $Mark41 = $this->Method00($AccountNo,'212121212',10);
    return $Mark41;
  } 

  function Mark42($AccountNo) {
    $Mark42 = $this->Method06($AccountNo, '098765432', FALSE, 10, 11);
    return $Mark43;
  } 

  function Mark43($AccountNo) {
    $Significance = '987654321';
    $Help = 0;
    $Mark43 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 10;
    $Checksum = 10 - $Help;
    if ($Checksum == 10) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark43 = 0;
    }
    return $Mark43;
  } 

  function Mark44($AccountNo) {
    $Mark44 = $this->Method06($AccountNo, '0000A5842', TRUE, 10, 11);
    return $Mark44;
  } 

  function Mark45($AccountNo) {
    if (substr($AccountNo,0,1)=='0' or substr($AccountNo,4,1)=='1'){
      $Mark45 = 2;
    } else {
      $Mark45 = $this->Method00($AccountNo, '212121212', 10);
    }
    return $Mark45;
  } 

  function Mark46($AccountNo) {
    $Mark46 = $this->Method06($AccountNo, '0065432', FALSE, 8, 11);
    return $Mark46;
  }  

  function Mark47($AccountNo) {
    $Mark47 = $this->Method06($AccountNo, '00065432', FALSE, 9, 11);
    return $Mark47;
  }  

  function Mark48($AccountNo) {
    $Mark48 = $this->Method06($AccountNo, '00765432', FALSE, 9, 11);
    return $Mark48;
  } 

  function Mark49($AccountNo) {
    $Mark49=$this->Mark00($AccountNo);
    if ($Mark49 == 0)
      return $Mark49;
    $Mark49=$this->Mark01($AccountNo);
    return $Mark49;
  } 

  function Mark50($AccountNo) {
    $Help = $this->Method06($AccountNo, '765432', FALSE, 7, 11);
    if ($Help == 1) {
      if (strlen($AccountNo) < 7) {
        $Help = $this->Method06($AccountNo . '000', '765432', FALSE, 7, 11);
      }
    }
    $Mark50 = $Help;
    return $Mark50;
  }  

  function Mark51($AccountNo) {
      $AccountNo = $this->ExpandAccount($AccountNo);

      if (substr($AccountNo,2,1) != '9') {
        $Help = $this->Method06($AccountNo, '000765432', FALSE, 10, 11); 
          if ($Help != 0) {
            $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 11); 
            if ($Help != 0) {
              switch (substr($AccountNo,-1)) {
                case '7' :
                case '8' :
                case '9' :
                  $Help = 4;
                  break;
                default :
                  $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 7); 
                  break;
              }
           }
        }
      } else {
        $Help = $this->Method06($AccountNo, '008765432', FALSE, 10, 11);
        if ($Help != 0 ){
          $Help = $this->Method06($AccountNo, 'A98765432', TRUE, 10, 11);
        }
      }
      return $Help;
    } 

  function Mark52($AccountNo,$BLZ ) {
    $Significance = '4216379A5842';
    if ((strlen($AccountNo) == 10) && (substr($AccountNo,0,1) == '9')){
        $Correct = $this->Mark20($AccountNo);

    } else {
      $Help = 0;
      $Rest = 0;
      $AltKonto = substr($BLZ,-4).substr($AccountNo,0,2);
      $AccountNo = Substr($AccountNo,2);
      while (substr($AccountNo,0,1) == '0') {
        $AccountNo = Substr($AccountNo,1);
      }
      $AltKonto = $AltKonto . $AccountNo;
      $Checksum = substr($AltKonto,5,1);
      $AltKonto = substr($AltKonto,0,5).'0'.substr($AltKonto,6);
      $Laenge = strlen($AltKonto);

      $Significance=substr($Significance,(12 - $Laenge));
      for ($Run = 0; $Run < $Laenge;$Run++) {
        $Help += substr($AltKonto,$Run,1) * HexDec(substr($Significance,$Run,1));
      }
      $Rest = $Help % 11;
      $Gewicht = HexDec(substr($Significance,5,1));

      $PZ = -1;
      do {
        $PZ++;
        $Help2 = $Rest + ($PZ * $Gewicht);
        if ($PZ == 9) {
            break;
        }
      } while ($Help2 % 11 <>10);
      if ($Help2 % 11 == 10) {
         if ($PZ == $Checksum) {
           $Correct = 0;
         } else {
           $Correct = 1;
         }
       } else {
         $Correct = 1;
       }
     }
    return $Correct;
  } 

  function Mark53($AccountNo,$BLZ ) {
    $Significance = '4216379A5842';
    if (strlen($AccountNo) == 10) {
      if (substr($AccountNo,0,1) == '9') {
        $Correct = $this->Mark20($AccountNo);
      }
    } else {
      $Help = 0;
      $Rest = 0;

      $AltKonto = substr($BLZ,-4,2) . substr($AccountNo,1,1) . substr($BLZ,-1). substr($AccountNo,0,1) . substr($AccountNo,2,1);

      $AccountNo = Substr($AccountNo,3);

      while (substr($AccountNo,0,1) == '0') {
        $AccountNo = Substr($AccountNo,1);
      }

      $AltKonto = $AltKonto . $AccountNo;

      while (strlen($AltKonto)<12){
        $AltKonto = "0" . $AltKonto;
      }

      $Checksum = substr($AltKonto,5,1);
      $AltKonto = substr($AltKonto,0,5) . '0' . substr($AltKonto,6);
      $Laenge = strlen($AltKonto);

      for ($Run = 0; $Run < $Laenge;$Run++) {
        $Help += substr($AltKonto,$Run,1) * HexDec(substr($Significance,$Run,1));
      }

      $Rest = $Help % 11;

      $Gewicht = HexDec(substr($Significance,5,1));
      $PZ = -1;
      do {
        $PZ++;
        $Help2 = $Rest + ($PZ * $Gewicht);
       } while ($Help2 % 11 <> 10 or $PZ > 9);

      if ($Help2 % 11 == 10) {
        if ($PZ == $Checksum) {
          $Correct = 0;
        } else {
          $Correct = 1;
        }
      } else {
        $Correct = 1;
      }
    }
    return $Correct;
  } 

  function Mark54($AccountNo) {
    $Mark54 = 3;
    return $Mark54;
  }  

  function Mark55($AccountNo) {
    $Mark55 = $this->Method06($AccountNo, '878765432', FALSE, 10, 11);
    return $Mark55;
  } 

  function Mark56($AccountNo) {
    $Significance = '432765432';
    $Mark56 = 1;
    $Help = 0;
    $Correct = 0;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    $Help = 11 - $Help;
    $Checksum = $Help;
    switch (substr($AccountNo,1,1)) {
      case '9' :
        if ($Help == 11) {
          $Checksum = 8;
        }
        if ($Help == 10) {
          $Checksum = 7;
        }
      default :
        if ($Help == 11) {
          $Correct = 1;
        }
        if ($Help == 10) {
          $Correct = 1;
        }
    }
    if ($Correct == 0) {
      if ($Checksum == substr($AccountNo,-1)) {
        $Mark56 = 0;
      }
    }
    return $Mark56;
  } 

  function Mark57($AccountNo) {
    $Correct = 1;

    $AccountNo = $this->ExpandAccount($AccountNo);

    $help = substr($AccountNo,0,2);

    switch (true){
      case ($help <= 50):
      case ($help == 91):
      case ($help >= 96 && $help <= 99):
        return 0;
        break;
      default:
    }

    if (preg_match("/[87]{6}/", $AccountNo)) {
      return 0;
    }

    $Mark57 = $this->Method00($AccountNo, '121212121', 10);
    return $Mark57;

  }  

  function Mark58($AccountNo) {
    $Mark58 = $this->Method02($AccountNo, '000065432', FALSE);
    return $Mark58;
  }  

  function Mark59($AccountNo) {
    $Mark59 = 1;
      if (strlen($AccountNo) > 8) {
        $Mark59 = $this->Method00($AccountNo, '212121212', 10);
      }
      return $Mark59;
    } 

  function Mark60($AccountNo) {
    $Mark60 = $this->Method00($AccountNo, '002121212', 10);
    return $Mark60;
  }  

  function Mark61($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,8,1) == '8') {
      $Mark61 = $this->Method00($AccountNo, '2121212012', 8);
    } else {
      $Mark61 = $this->Method00($AccountNo, '2121212', 8);
    }
    return $Mark61;
  } 

  function Mark62($AccountNo) {
    $Mark62 = $this->Method00($AccountNo,'0021212',8);
    return $Mark62;
  }  

  function Mark63($AccountNo) {
    $Help = $this->Method00($AccountNo,'0121212',8);
    if ($Help == 1) {
      $Help = $this->Method00($AccountNo,'000121212',10);
    }
    $Mark63 = $Help;
    return $Mark63;
  }  

  function Mark64($AccountNo) {
    $Mark64 = $this->Method06($AccountNo, '9A5842', TRUE, 7, 11);
    return $Mark64;
  }  

  function Mark65($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,8,1) == '9') {
      $Mark65 = $this->Method00($AccountNo, '2121212012', 8);
    } else {
      $Mark65 = $this->Method00($AccountNo, '2121212', 8);
    }
    return $Mark65;
  }  

  function Mark66($AccountNo) {
    $Significance = '000065432';
    $Help = 0;
    $Mark66 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    $Checksum = 11 - $Help;
    if ($Help == 0) {
      $Checksum = 0;
    }
    if ($Help == 1) {
      $Checksum = 1;
    }
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark66 = 0;
    }
    return $Mark66;
  }  

  function Mark67($AccountNo) {
    $Mark67 = $this->Method00($AccountNo, '2121212', 8);
    return $Mark67;
  }

  function Mark68($AccountNo) {
    $Correct = 0;
    $Significance = '212121212';
    if (strlen($AccountNo) == 9) {
      if (substr($AccountNo,1,1) == '4') {
        $Correct = 4;
      }
    }
    if (strlen($AccountNo) == 10) {
      $Significance = '000121212';
    }
    if ($Correct == 0) {
      $Correct = $this->Method00($AccountNo,$Significance,10);
      if ($Correct == 1) {
        $Correct = $this->Method00($AccountNo,'210021212',10);
      }
    }
    $Mark68 = $Correct;
    return $Mark68;
  } 

  function Mark69($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Correct = 0;
    if (Substr($AccountNo,0,2) == '93') {
      $Correct = 2;
    }
    if ($Correct == 0) {
      $Correct = $this->Mark28($AccountNo);
      if ($Correct == 1) {
        $Correct = $this->Mark29($AccountNo);
      } elseif (Substr($AccountNo,0,2) == '97'){
         $Correct = $this->Mark29($AccountNo);
      }
    }
    $Mark69 = $Correct;
    return $Mark69;
  } 

  function Mark70($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,3,1) == '5'){
      $Mark70 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
    } elseif (Substr($AccountNo,3,2) == '69') {
      $Mark70 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
    } else {
      $Mark70 = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
   }
    return $Mark70;
  } 

  function Mark71($AccountNo) {
    $Significance='0654321';
    $Help = 0;
    $Mark71 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    $Checksum = 11 - $Help;
    if ($Help == 0) {
      $Checksum = 0;
    }
    if ($Help == 1) {
      $Checksum = 1;
    }
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark71 = 0;
    }
    return $Mark71;
  }  

  function Mark72($AccountNo) {
    $Mark72 = $this->Method00($AccountNo, '000121212', 10);
    return $Mark72;
  }   

  function Mark73($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,2,1) != '9') {
      $Mark73 = $this->Method00($AccountNo, '000121212', 10);
      if ($Mark73 != 0) {
        $Mark73 = $this->Method00($AccountNo, '000021212', 10); 
        if ($Mark73 != 0) {
          $Mark73 = $this->Method00($AccountNo, '000021212', 10, 7); 
        }
      }
    } else {
      $Mark73 = $this->Mark51($AccountNo);
    }
    return $Mark73;
  } 


  function Mark74($AccountNo) {
    $Help = 0;
    $V2 = 0;
    if (strlen($AccountNo) == 6) {
      $V2 = 1;
    }
    $Correct = $this->Method00($AccountNo,'212121212',10);
    if ($Correct == 1) {
      if ($V2 == 1) {
        $Significance = '212121212';
        $Correct = 1;
        $AccountNo = $this->ExpandAccount($AccountNo);
        for ($Run = 0;$Run < strlen($Significance);$Run++) {
          $Help += $this->CrossSum(substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
        }
        $Help = $this->OnlyOne($Help);
        $Help = 5 - $Help;
        if ($Help < 0) {
          $Help = 10 + $Help;
        }
        $Checksum = $Help;
        if ($Checksum == substr($AccountNo,-1)) {
          $Correct = 0;
        }
      }
    }
    return $Correct;
  } 

  function Mark75($AccountNo) {
    $Help = 1;
    switch (strlen($AccountNo)) {
      case 6 :
      case 7 :
        $Help = $this->Method00($AccountNo,'000021212',10);
        break;
      case 9 :
        if (substr($AccountNo,0,1) == '9') {
          $Help = $this->Method00($AccountNo,'0021212',8);
        } else {
          $Help = $this->Method00($AccountNo,'012121',7);
        }
        break;
      case 10 :
        $Help = $this->Method00($AccountNo,'012121',7);
        break;
    }
    return $Help;
  } 

  function Mark76($AccountNo) {
    $Help = 0;
    $Correct = 1;
    $Significance = '0765432';
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += substr($AccountNo,$Run,1) * substr($Significance,$Run,1);
    }
    $Help = $Help % 11;
    if ($Help == 10) {
      $Correct = 4;
    } else {
      if ($Help == substr($AccountNo,-3,1)) {
        $Correct = 0;
      } else {
        $Help=0;
        $Significance = '000765432';
        for ($Run = 0;$Run < strlen($Significance);$Run++) {
          $Help += substr($AccountNo,$Run,1) * substr($Significance,$Run,1);
        }
        $Help = $Help % 11;
        if ($Help == 10) {
          $Correct = 4;
        } else {
          if ($Help == substr($AccountNo,-1)) {
            $Correct = 0;
          } else {
            $Correct = 1;
          }
        }
      }
    }
    return $Correct;
  } 

  function Mark77($AccountNo) {
    $Help = 0;
    $Mark77 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Significance = '54321';
    for ($Run = 4;$Run == 9;$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    if ($Help <> 0) {
      $Help = 0;
      $Significance = '54345';
      for ($Run = 4;$Run < 10;$Run++) {
        $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
      }
      $Help = $Help % 11;
      if ($Help = 0) {
        $Mark77 = 0;
      }
    }
    return $Mark77;
  } 

  function Mark78($AccountNo) {
    if (strlen($AccountNo) == 8) {
      $Mark78 = 4;
    } else {
      $Mark78 = $this->Method00($AccountNo, '212121212', 10);
    }
    return $Mark78;
  }  

  function Mark79($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    switch (substr($AccountNo,0,1)) {
      case '0' :
      case '7' :
      case '8' :
        $Mark79 = 1;
        break;
      case '1' :
      case '2' :
      case '9' :
        $Mark79 = $this->Method00($AccountNo, '12121212', 9);
        break;
      case '3' :
      case '4' :
      case '5' :
      case '6' :
        $Mark79 = $this->Method00($AccountNo, '212121212', 10);
      default :
        $Mark79 = 1;
    }
    return $Mark79;
  }  

  function Mark80($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);

    if (substr($AccountNo,2,1) == '9') {
      $Mark80 = $this->Mark51($AccountNo);
    } else {
      $Mark80 = $this->Method00($AccountNo, '000021212', 10);
    }
    if ($Mark80 != 0){
      $Significance='000021212';
      $Help = 0;
       $Mark80 = 1;

       for ($Run = 0; $Run < strlen($Significance); $Run++) {
         $Help += $this->CrossSum(substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
       }
      $Help = $Help % 7;
       $Checksum = 7 - $Help;

       if ($Checksum == 10) {
         $Checksum = 0;
       }
       if ($Checksum == substr($AccountNo,9,1)) {
         $Mark80 = 0;
      }
    }
    return $Mark80;
  } 

  function Mark81($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,2,1) == '9') {
      $Mark81 = $this->Mark10($AccountNo);
    } else {
      $Mark81 = $this->Mark51($AccountNo);
    }
    return $Mark81;
  }  

  function Mark82($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
     if (substr($AccountNo,2, 2) == '99') {
       $Mark82 = $this->Mark10($AccountNo);
     } else {
       $Mark82 = $this->Mark33($AccountNo);
     }
     return $Mark82;
   } 

  function Mark83($AccountNo) {
    $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 11);
    if ($Help == 1) {
      $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 7);
    }
    $Mark83 = $Help;
    return $Mark83;
  }  

  function Mark84($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo, 2, 1) != '9') {
      $Help = $this->Mark33($AccountNo);
      if ($Help == 1) {
        $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 7);
      }
    } else {
        $Help = $this->Mark51($AccountNo);
    }
    return $Help;
  }  

  function Mark85($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
    $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 11);
    if ($Help == 1) {
      if (substr($AccountNo, 2, 2) == '99') {
        $Help = $this->Method02($AccountNo, '008765432', FALSE);
      } else {
        $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 7);
      }
    }
    $Mark85 = $Help;
      return $Mark85;
  } 

  function Mark86($AccountNo) {
    $Help = $this->Method00($AccountNo, '000121212', 10);
    if ($Help == 1) {
      if (substr($AccountNo,2,1) == '9') {
        $Help = $this->Method06($AccountNo, 'A98765432', TRUE, 10, 11);
      } else {
        $Help = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
      }
    }
    $Mark86 = $Help;
    return $Mark86;
  }  

  function Mark87($AccountNo) {
    $Tab1[0] = 0;
    $Tab1[1] = 4;
    $Tab1[2] = 3;
    $Tab1[3] = 2;
    $Tab1[4] = 6;

    $Tab2[0] = 7;
    $Tab2[1] = 1;
    $Tab2[2] = 5;
    $Tab2[3] = 9;
    $Tab2[4] = 8;

    $Result = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,2,1) == '9') {
      $Result = $this->Mark10($AccountNo);
    } else {
      for ($Run = 0; $Run < strlen($AccountNo); $Run++) {
        $AccountNoTemp[$Run] = (int) substr($AccountNo,$Run,1);
      }
      $i = 4;
      while ($AccountNoTemp[$i] == 0) {
        $i++;
      }

      $C2 = $i % 2;
      $D2 = 0;
      $A5 = 0;
      while ($i < 10) {
        switch ($AccountNoTemp[$i]) {
          case 0 :
            $AccountNoTemp[$i] = 5;
            break;
          case 1 :
            $AccountNoTemp[$i] = 6;
            break;
          case 5:
            $AccountNoTemp[$i] = 10;
            break;
          case 6:
            $AccountNoTemp[$i] = 1;
            break;
        }
        if ($C2 == $D2) {
          if ($AccountNoTemp[$i] > 5) {
            if(($C2 == 0) AND ($D2 == 0)) {
              $C2 = 1;
              $D2 = 1;
              $A5 = $A5 + 6 - ($AccountNoTemp[$i] - 6);
            } else {
              $C2 = 0;
              $D2 = 0;
              $A5 = $A5 + $AccountNoTemp[$i];
            } 
          } else {
            if (($C2 == 0) AND ($D2 == 0)) {
              $C2 = 1;
              $A5 = $A5 + $AccountNoTemp[$i];
            } else {
              $C2 = 0;
              $A5 = $A5 + $AccountNoTemp[$i];
            }
          }
        } else {
          if ($AccountNoTemp[$i] > 5) {
            if ($C2 == 0) {
              $C2 = 1;
              $D2 = 0;
              $A5 = $A5 - 6 + ($AccountNoTemp[$i] - 6);
            } else {
              $C2 = 0;
              $D2 = 1;
              $A5 = $A5 - $AccountNoTemp[$i];
            }
          } else {
            if ($C2 == 0) {
              $C2 = 1;
              $A5 = $A5 - $AccountNoTemp[$i];
            } else {
              $C2 = 0;
              $A5 = $A5 - $AccountNoTemp[$i];
            }
          }
        }
        $i++;
      }
      while (($A5 < 0) OR ($A5 > 4)) {
        if ($A5 > 4) {
          $A5 = $A5 - 5;
        } else {
          $A5 = $A5 + 5;
        }
      }
      if ($D2 == 0) {
        $P = $TAB1[$A5];
      } else {
        $P = $TAB2[$A5];
      }
      if ($P == $AccountNoTemp[10]) {
        $Result = 0;
      } else {
        if ($AccountNoTemp[4] == 0) {
          if ($P > 4) {
            $P = $P - 5;
          } else {
            $P = $P + 5;
          }
          if ($P == $AccountNoTemp[10] ) {
            $Result = 0;
          }
        }
      }
      if ($Result <> 0 ) {
        $Result = $this->Mark33($AccountNo);
        if ($Result <> 0 ) {
          $Result = $this->Method06($AccountNo,'000065432',FALSE,10,7);
        }
      }
    }
    return $Result;
  }  

  function Mark88($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,2,1) == '9') {
      $Mark88 = $this->Method06($AccountNo, '008765432', FALSE, 10, 11);
    } else {
      $Mark88 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
    }
    return $Mark88;
  } 

  function Mark89($AccountNo) {
    $Correct = 1;
    switch (strlen($AccountNo)) {
      case 1 :
      case 2 :
      case 3 :
      case 4 :
      case 5 :
      case 6 :
      case 10 :
        $Correct = 4;
        break;
      case 7 :
      case 9 :
        $AccountNo = $this->ExpandAccount($AccountNo);
        $Correct = $this->Method06($AccountNo,'098765432',FALSE,10,11);
        break;
      default :
        if ((((int)$AccountNo > 32000005) and ((int)$AccountNo < 38999995)) or (((int)$AccountNo >1999999999) AND ((int)$AccountNo <400000000))) {
          $Correct = $this->Mark10($AccountNo);
        }
    }
    return $Correct;
  }  

  function Mark90($AccountNo) {
    $Help = $this->Method06($AccountNo, '000765432', FALSE, 10, 11); // Methode A
      if ($Help != 0) {
        $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 11); // Methode B
        if ($Help != 0) {
          switch (substr($AccountNo,-1)) {
            case '7' :
            case '8' :
            case '9' :
              $Help = 4;
              break;
            default :
              $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 7); //Methode C
              break;
          }
        }
        if ($Help != 0) {
          $Help = $this->Method06($AccountNo, '000065432',FALSE, 10, 9);  //Methode D
        }
        if ($Help != 0) {
              $Help = $this->Method06($AccountNo, '000021212',FALSE, 10, 10); //Methode E
        }
      }
    return $Help;
  } 

  function Mark91($AccountNo) {
    $Help = $this->Method06($AccountNo, '765432', FALSE, 7, 11);
    if ($Help == 1) {
      $Help = $this->Method06($AccountNo, '234567', FALSE, 7, 11);
      if ($Help == 1) {
        $Help = $this->Method06($AccountNo, 'A987650432', TRUE, 7, 11);
        if ($Help == 1) {
          $Help = $this->Method06($AccountNo, '9A5842', TRUE, 7, 11);
        }
      }
    }
    return $Help;;
  } 

  function Mark92($AccountNo) {
    $Mark92 = $this->Method01($AccountNo, '000173173');
    return $Mark92;
  } 

  function Mark93($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Correct = 1;
      if (substr($AccountNo,0,4) == '0000') {
        $Correct = $this->Method06($AccountNo,'000065432',FALSE,10,11);
      } else {
        $Correct = $this->Method06($AccountNo,'65432',FALSE,10,11);
    }
      if ($Correct == 1) {
        if (substr($AccountNo,0,4) == '0000') {
          $Correct = $this->Method06($AccountNo,'000065432',FALSE,10,7);
        } else {
          $Correct = $this->Method06($AccountNo,'65432',FALSE,10,7);
      }
    }
    $Mark93 = $Correct;
    return $Mark93;
  }  

  function Mark94($AccountNo) {
    $Mark94 = $this->Method00($AccountNo, '121212121', 10);
    return $Mark94;
  } 

  function Mark95($AccountNo) {
    if (strlen($AccountNo) == 10) {
      if ((substr($AccountNo,3,1) == '5') Or (substr($AccountNo, 3, 2) == '69')) {
        $Mark95 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
      } else {
        $Mark95 = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
      }
    } else {
      $Mark95 = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
      if ((int) $AccountNo < 2000000) {
        $Mark95 = 4;
      }
      if ((int) $AccountNo > 699999999) {
        if ((int) $AccountNo < 800000000) {
          $Mark95 = 4;
        }
      }
      if ((int) $AccountNo > 395999999) {
        if ((int) $AccountNo < 500000000) {
          $Mark95 = 4;
        }
      }
    }
    return $Mark95;
  }  

  function Mark96($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Help = $this->Mark19($AccountNo);
      if ($Help == 1) {
        $Help = $this->Method00($AccountNo, '212121212', 10);
        if ($Help == 1) {
          if ((int) $AccountNo >1299999) {
            if ((int) $AccountNo < 99400000) {
              $Help = 0;
            }
          }
        }
      }
     return $Help;
    }  

  function Mark97($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Help = (int) substr($AccountNo, 0, 9) % 11;
      if ($Help == 10) {
        $Help = 0;
      }
      if (substr($AccountNo,-1) == $Help) {
        $Mark97 = 0;
      } else {
        $Mark97 = 1;
      }
      return $Mark97;
    }  

  function Mark98($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Correct = $this->Method01($AccountNo,'003713713');
    if ($Correct == 1) {
      $Correct = $this->Mark32($AccountNo);
    }
    $Mark98 = $Correct;
    return $Mark98;
  }  

  function Mark99($AccountNo) {
    $Mark99 = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
    if ((int) $AccountNo >= 396000000 && (int) $AccountNo <= 499999999) {
      $Mark99 = 4;
     }
    return $Mark99;
  }  

  function MarkA1($AccountNo) {
    if (strlen($AccountNo) == 8 OR strlen($AccountNo)==10){
      $AccountNo = $this->ExpandAccount($AccountNo);
      $MarkA1 = $this->Method00($AccountNo, '002121212', 10);
    } else {
      $MarkA1 = 1;
    }
    return $MarkA1;
  }  

  function MarkA2($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $MarkA2 = $this->Method00($AccountNo, '212121212', 10);
    if ($MarkA2 != 0){
      $MarkA2 = $this->Mark04($AccountNo);
    }
    return $MarkA2;
  }  

  function MarkA3($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = $this->Method00($AccountNo, '212121212', 10);
    if ($RetVal != 0){
      $RetVal = $this->Mark10($AccountNo);
    }
    return $RetVal;
  } 

  function MarkA4($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if ((int) substr($AccountNo, 2,2) != 99){

      $MarkA4 = $this->Method06($AccountNo,'000765432',FALSE, 10,11);
      if ($MarkA4 !=0){

        $Significance='000765432';
        $MarkA4 = 1;
        $Help = 0;
        for ($Run = 0; $Run < strlen($Significance); $Run++) {
          $Help += substr($AccountNo,$Run,1) * substr($Significance,$Run,1);
        }
        $Help = $Help % 7;
        $Checksum = 7 - $Help;

        if ($Help == 0) {
          $Checksum = 0;
        }
        if ($Checksum == substr($AccountNo,-1)) {
         $MarkA4 = 0;
        }
      }
      if ($MarkA4 != 0){
        $MarkA4 = $this->Mark93($AccountNo);
      }
    } else {
      $MarkA4 = $this->Method06($AccountNo,'000065432',FALSE,10,11);
      if ($MarkA4 != 0){
        $MarkA4 = $this->Mark93($AccountNo);
      }
    }
    return $MarkA4;
  }  

  function MarkA5($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $MarkA5 = $this->Method00($AccountNo, '212121212', 10);
    if ($MarkA5 != 0){
      if (substr($AccountNo,1,1) != "9"){
        $MarkA5 = $this->Mark10($AccountNo);
      } else {
        $MarkA5 = 1;
      }
    }
    return $MarkA5;
  } 

  function MarkA6($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,1,1) != "8"){
      $RetVal = $this->Method01($AccountNo, '173173173');
    } else {
      $RetVal = $this->Method00($AccountNo, '212121212', 10);
    }
    return $RetVal;
  }  

  function MarkA7($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = $this->Method00($AccountNo, '212121212', 10);
    if ($RetVal != 0){
      $RetVal = $this->Mark03($AccountNo);
    }
    return $RetVal;
  }  

  function MarkA8($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = $this->Mark81($AccountNo);
    if ($RetVal != 0){
      if (substr($AccountNo,2,1) != "9"){
        $RetVal = $this->Mark73($AccountNo);
      } else {
        $RetVal = 1;
      }
    }
    return $RetVal;
  }  
function MarkA9($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        $RetVal = $this->Method01($AccountNo, '173173173');
        if ($RetVal != 0){
                $RetVal = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
        }
        return $RetVal;
} 
function MarkB0($AccountNo) {
        if (strlen($AccountNo) != 10 OR substr($AccountNo,0,1) == "8"){
                $RetVal = 1;

        } else {
                if (substr($AccountNo,9,1) == "1" OR substr($AccountNo,9,1) == "2" OR substr($AccountNo,9,1) == "3" OR substr($AccountNo,9,1) == "6"){
                        $RetVal = 0;
                } else {
                        $RetVal = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
                }
        }
        return $RetVal;
}  
function MarkB1($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        $RetVal = $this->Method01($AccountNo, '137137137');
        if ($RetVal != 0){
                $RetVal = $this->Method01($AccountNo, '173173173');
        }
        return $RetVal;
}  


function MarkB2($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        if (substr($AccountNo,0,1) <= "7"){
                $RetVal = $this->Method02($AccountNo, '298765432', FALSE);
        } else {
                $RetVal = $this->Method00($AccountNo, '212121212', 10);
        }
        return $RetVal;
}  
function MarkB3($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        if (substr($AccountNo,0,1) <= "8"){
                $RetVal = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
        } else {
                $RetVal = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
        }
        return $RetVal;
}  
function MarkB4($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        if (substr($AccountNo,0,1) == "9"){
                $RetVal = $this->Method00($AccountNo, '212121212', 10);
        } else {
                $RetVal = $this->Method02($AccountNo, '298765432', FALSE);
        }
        return $RetVal;
}  
function MarkB5($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        $RetVal = $this->Method01($AccountNo, '137137137');
        if ($RetVal != 0){
                if ((substr($AccountNo,0,1) == '8') Or (substr($AccountNo,0,1) == '9')) {
                        return $RetVal;
                }
                $RetVal = $this->Method00($AccountNo, '212121212', 10);
        }
        return $RetVal;
} 
function MarkB6($AccountNo,$BLZ) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        if (substr($AccountNo,0,1) <= "9"){
                $RetVal = $this->Mark20($AccountNo);
        } else {
                $RetVal = $this->Mark53($AccountNo,$BLZ);
        }
        return $RetVal;
} 
function MarkB7($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        $AccountFloat = doubleval($AccountNo);
        if (($AccountFloat >= 1000000) And ($AccountFloat <= 5999999)) {
                $RetVal = $this->Method01($AccountNo, '173173173');
        } elseif (($AccountFloat >= 700000000) And ($AccountFloat <= 899999999)) {
                $RetVal = $this->Method01($AccountNo, '173173173');
        } else {
                $RetVal = 2;
        }
        return $RetVal;
} 

function MarkB8($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        $RetVal = $this->Mark20($AccountNo);
        if ($RetVal != 0){
                $RetVal = $this->Mark20($AccountNo);
        }
        return $RetVal;
} 
function MarkB9($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        $RetVal = 1;
        
        if ((substr($AccountNo,0,2) == "00")And (substr($AccountNo,2,1) != "0")){
                $Significance = '1231231';
                for ($Run = 0;$Run < strlen($Significance);$Run++) {
                        $Step1 = (substr($AccountNo,$Run + 2,1) * substr($Significance,$Run,1));
                        $Step2 = $Step1 + substr($Significance,$Run,1);
                        $Step3 += $Step2 % 11;
                }
                $Checksum = $Step3 % 10;
                if ($Checksum == substr($AccountNo,-1)) {
                        $RetVal = 0;
                } else {
                        $Checksum = $Checksum + 5;
                        if ($Checksum > 10) {
                                $Checksum = $Checksum - 10;
                        }
                        if ($Checksum == substr($AccountNo,-1)) {
                                $RetVal = 0;
                        }
                }

        } elseif ((substr($AccountNo,0,3) == "000")And (substr($AccountNo,3,1) != "0")){
                $Significance = '654321';
                for ($Run = 0;$Run < strlen($Significance);$Run++) {
                        $Step1 += (substr($AccountNo,$Run + 3,1) * substr($Significance,$Run,1));
                }
                $Checksum = $Step1 % 11;
                if ($Checksum == substr($AccountNo,-1)) {
                        $RetVal = 0;
                } else {
                        $Checksum = $Checksum + 5;
                        if ($Checksum > 10) {
                                $Checksum = $Checksum - 10;
                        }
                        if ($Checksum == substr($AccountNo,-1)) {
                                $RetVal = 0;
                        }
                }
        }
        return $RetVal;
} 
function MarkC0($AccountNo,$BLZ) {
        $AccountNo = $this->ExpandAccount($AccountNo);
        if ((substr($AccountNo,0,2) == "00") And (substr($AccountNo,0,3)!= "000")) {
                $RetVal = $this->Mark53($AccountNo,$BLZ);
                if ($RetVal != 0){
                        $RetVal = $this->Mark20($AccountNo);
                }       
        } else {
                $RetVal = $this->Mark20($AccountNo);
        }
        return $RetVal;
} 
  function CheckAccount($banktransfer_number, $banktransfer_blz) {
    $KontoNR = preg_replace ( '/[^0-9]/', '', $banktransfer_number);
    $BLZ = preg_replace ( '/[^0-9]/', '', $banktransfer_blz);

    $Result = 0;
    if ($BLZ == '' || strlen($BLZ) < 8) {
      return 8;  
    }
    if ($KontoNR == '') {
      return 9;  
    }

    $adata = $this->query($BLZ);
    if ($adata == -1) {
      $Result = 5;
      $PRZ = -1;
      $this->PRZ = $PRZ;
      $this->banktransfer_number=ltrim($banktransfer_number,"0");
      $this->banktransfer_blz=$banktransfer_blz;
    } else {
      $this->Bankname = $adata['bankname'];
      $this->PRZ = str_pad ($adata['prz'], 2, "0", STR_PAD_LEFT);
      $this->banktransfer_number=ltrim($banktransfer_number,"0");
      $this->banktransfer_blz=$banktransfer_blz;

      $PRZ = $adata['prz'];

      switch ($PRZ) {
        case "52" : $Result = $this->Mark52($KontoNR, $BLZ); break;
        case "53" : $Result = $this->Mark53($KontoNR, $BLZ); break;
        case "B6" : $Result = $this->MarkB6($KontoNR, $BLZ); break;
        case "C0" : $Result = $this->MarkC0($KontoNR, $BLZ); break;
        default:
          $MethodName = "Mark$PRZ";
          if (method_exists($this, $MethodName)){
            $Result = call_user_func (array($this, $MethodName), $KontoNR);
          } else {
            $Result = 3;
          }
        } 

      } 

      return $Result;
    } 
  }  
?>