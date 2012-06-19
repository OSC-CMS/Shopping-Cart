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

$_utf8win1251 = array(
"\xD0\x90"=>"\xC0","\xD0\x91"=>"\xC1","\xD0\x92"=>"\xC2","\xD0\x93"=>"\xC3","\xD0\x94"=>"\xC4",
"\xD0\x95"=>"\xC5","\xD0\x81"=>"\xA8","\xD0\x96"=>"\xC6","\xD0\x97"=>"\xC7","\xD0\x98"=>"\xC8",
"\xD0\x99"=>"\xC9","\xD0\x9A"=>"\xCA","\xD0\x9B"=>"\xCB","\xD0\x9C"=>"\xCC","\xD0\x9D"=>"\xCD",
"\xD0\x9E"=>"\xCE","\xD0\x9F"=>"\xCF","\xD0\xA0"=>"\xD0","\xD0\xA1"=>"\xD1","\xD0\xA2"=>"\xD2",
"\xD0\xA3"=>"\xD3","\xD0\xA4"=>"\xD4","\xD0\xA5"=>"\xD5","\xD0\xA6"=>"\xD6","\xD0\xA7"=>"\xD7",
"\xD0\xA8"=>"\xD8","\xD0\xA9"=>"\xD9","\xD0\xAA"=>"\xDA","\xD0\xAB"=>"\xDB","\xD0\xAC"=>"\xDC",
"\xD0\xAD"=>"\xDD","\xD0\xAE"=>"\xDE","\xD0\xAF"=>"\xDF","\xD0\x87"=>"\xAF","\xD0\x86"=>"\xB2",
"\xD0\x84"=>"\xAA","\xD0\x8E"=>"\xA1","\xD0\xB0"=>"\xE0","\xD0\xB1"=>"\xE1","\xD0\xB2"=>"\xE2",
"\xD0\xB3"=>"\xE3","\xD0\xB4"=>"\xE4","\xD0\xB5"=>"\xE5","\xD1\x91"=>"\xB8","\xD0\xB6"=>"\xE6",
"\xD0\xB7"=>"\xE7","\xD0\xB8"=>"\xE8","\xD0\xB9"=>"\xE9","\xD0\xBA"=>"\xEA","\xD0\xBB"=>"\xEB",
"\xD0\xBC"=>"\xEC","\xD0\xBD"=>"\xED","\xD0\xBE"=>"\xEE","\xD0\xBF"=>"\xEF","\xD1\x80"=>"\xF0",
"\xD1\x81"=>"\xF1","\xD1\x82"=>"\xF2","\xD1\x83"=>"\xF3","\xD1\x84"=>"\xF4","\xD1\x85"=>"\xF5",
"\xD1\x86"=>"\xF6","\xD1\x87"=>"\xF7","\xD1\x88"=>"\xF8","\xD1\x89"=>"\xF9","\xD1\x8A"=>"\xFA",
"\xD1\x8B"=>"\xFB","\xD1\x8C"=>"\xFC","\xD1\x8D"=>"\xFD","\xD1\x8E"=>"\xFE","\xD1\x8F"=>"\xFF",
"\xD1\x96"=>"\xB3","\xD1\x97"=>"\xBF","\xD1\x94"=>"\xBA","\xD1\x9E"=>"\xA2");
$_win1251utf8 = array(
"\xC0"=>"\xD0\x90","\xC1"=>"\xD0\x91","\xC2"=>"\xD0\x92","\xC3"=>"\xD0\x93","\xC4"=>"\xD0\x94",
"\xC5"=>"\xD0\x95","\xA8"=>"\xD0\x81","\xC6"=>"\xD0\x96","\xC7"=>"\xD0\x97","\xC8"=>"\xD0\x98",
"\xC9"=>"\xD0\x99","\xCA"=>"\xD0\x9A","\xCB"=>"\xD0\x9B","\xCC"=>"\xD0\x9C","\xCD"=>"\xD0\x9D",
"\xCE"=>"\xD0\x9E","\xCF"=>"\xD0\x9F","\xD0"=>"\xD0\xA0","\xD1"=>"\xD0\xA1","\xD2"=>"\xD0\xA2",
"\xD3"=>"\xD0\xA3","\xD4"=>"\xD0\xA4","\xD5"=>"\xD0\xA5","\xD6"=>"\xD0\xA6","\xD7"=>"\xD0\xA7",
"\xD8"=>"\xD0\xA8","\xD9"=>"\xD0\xA9","\xDA"=>"\xD0\xAA","\xDB"=>"\xD0\xAB","\xDC"=>"\xD0\xAC",
"\xDD"=>"\xD0\xAD","\xDE"=>"\xD0\xAE","\xDF"=>"\xD0\xAF","\xAF"=>"\xD0\x87","\xB2"=>"\xD0\x86",
"\xAA"=>"\xD0\x84","\xA1"=>"\xD0\x8E","\xE0"=>"\xD0\xB0","\xE1"=>"\xD0\xB1","\xE2"=>"\xD0\xB2",
"\xE3"=>"\xD0\xB3","\xE4"=>"\xD0\xB4","\xE5"=>"\xD0\xB5","\xB8"=>"\xD1\x91","\xE6"=>"\xD0\xB6",
"\xE7"=>"\xD0\xB7","\xE8"=>"\xD0\xB8","\xE9"=>"\xD0\xB9","\xEA"=>"\xD0\xBA","\xEB"=>"\xD0\xBB",
"\xEC"=>"\xD0\xBC","\xED"=>"\xD0\xBD","\xEE"=>"\xD0\xBE","\xEF"=>"\xD0\xBF","\xF0"=>"\xD1\x80",
"\xF1"=>"\xD1\x81","\xF2"=>"\xD1\x82","\xF3"=>"\xD1\x83","\xF4"=>"\xD1\x84","\xF5"=>"\xD1\x85",
"\xF6"=>"\xD1\x86","\xF7"=>"\xD1\x87","\xF8"=>"\xD1\x88","\xF9"=>"\xD1\x89","\xFA"=>"\xD1\x8A",
"\xFB"=>"\xD1\x8B","\xFC"=>"\xD1\x8C","\xFD"=>"\xD1\x8D","\xFE"=>"\xD1\x8E","\xFF"=>"\xD1\x8F",
"\xB3"=>"\xD1\x96","\xBF"=>"\xD1\x97","\xBA"=>"\xD1\x94","\xA2"=>"\xD1\x9E");

function utf8_win1251($a) 
{
    global $_utf8win1251;
    if (is_array($a))
	{
        foreach ($a as $k => $v) 
		{
            if (is_array($v)) 
			{
                $a[$k] = utf8_win1251($v);
            } 
			else 
			{
                $a[$k] = strtr($v, $_utf8win1251);
            }
        }
        return $a;
    } 
	else 
	{
        return strtr($a, $_utf8win1251);
    }
}

function win1251_utf8($a) 
{
    global $_win1251utf8;
    if (is_array($a)){
        foreach ($a as $k=>$v) 
		{
            if (is_array($v)) 
			{
                $a[$k] = utf8_win1251($v);
            } 
			else 
			{
                $a[$k] = strtr($v, $_win1251utf8);
            }
        }
        return $a;
    } 
	else
	{
        return strtr($a, $_win1251utf8);
    }
}

if (!isset($_SESSION['themes_a']))
{
   $_SESSION['themes_a'] = "default";
   $themes = $_SESSION['themes_a'];
}
else
{  
   if (isset($_GET['themes_a']))
   {
      $themes = htmlspecialchars($_GET['themes_a']);
      $themes = str_replace('..','',$themes);
      $_SESSION['themes_a'] =  $themes;
   }
   else
   {
      $themes = $_SESSION['themes_a'];
   }
}

if (isset($_GET['file_edit']))
{
   $file = htmlspecialchars(base64_decode($_GET['file_edit']));
   $file = str_replace('..','',$file);
     
}
else
{
    $file = "index.html";
}

if (isset($_POST['themes_text']) && !empty($_POST['themes_text']))
{
    file_save($file,$themes, $_POST['themes_text']);   
}

//Сохранения изменений в файл с адресом $fr, темой $tm и с содержанием $str

function file_save($fr, $tm, $str)
{
    if (is_file( dir_path('themes') . $tm.'/'.$fr)) //Проверка существования файла
    {
	    if (is_writeable( dir_path('themes') .$tm.'/'.$fr))//Проверка доступа для записи
        {
            $f = fopen( dir_path('themes') .$tm.'/'.$fr,'w+');
			$str = str_replace('\"','"',$str);
			$str = str_replace("\'","'",$str);
			if (base64_decode($_GET['file_edit']) == '/style.css') 
			{			
			   $str = utf8_win1251($str);
			}   
            fwrite($f,$str);   
            fclose($f);
        }
	}	
}

  add_action('head_admin', 'head_themes');
  
  function head_themes()
  {
     global $main;
	 $main->style('themes');
	 return true;
  }
  
?>
<?php $main->head(); ?>
<?php $main->top_menu(); ?>
<?php
   echo "<div style=\"position:absolute;top:100px; right:3%;\"><a target=\"_blank\" style=\"color:#4378a1\" href=\"http://osc-cms.com/themes/\">".THEMES_OTHER."</a></div>";
?>
<table border=0 width="100%"><tr><td valign="top">

<?php

?>
<?php $main->heading('themes.png', BOX_CONFIGURATION." / ".HEADING_TITLE); ?>
<?php
if ($dir = opendir(dir_path('themes'))) 
{
		while (($templates = readdir($dir)) !== false) 
		{
			if (is_dir( dir_path('themes')."//".$templates) && ($templates != "CVS") && ($templates != ".") && ($templates != "..") &&  ($templates != ".svn"))
			{
				$templates_array[] = $templates;
			}
		}
		closedir($dir);
		sort($templates_array);
}

?>

<table border=0 width="100%"><tr valign="top"><td width="50%">
		<div class="PortfolioContent">
			 <?php echo THEMES_TEXT; ?>: <SELECT NAME="navSelect" ONCHANGE="top.location.href = this.options[this.selectedIndex].value">
<?php
	
foreach($templates_array as $type)
{
    if (isset($_SESSION['themes_a']))
	{
	     if ($_SESSION['themes_a']==$type)
		 {
		     echo '<OPTION value="?themes_a='.$type.'" selected>'.$type.'</OPTION>'; 
		 }
		 else
		 {
		     echo '<OPTION value="?themes_a='.$type.'">'.$type.'</OPTION>'; 
		 }
    }
	else
	{
	    if ($type == 'default')
		{
	        echo '<OPTION selected value="?themes_a='.$type.'">'.$type.'</OPTION>';
        }
        else
		{
	        echo '<OPTION value="?themes_a='.$type.'">'.$type.'</OPTION>';
        }		   
	}	
}	
$GLOBALS["file_e"] = 0;
function get_file ($fr,$tm)
{ 
   if (is_file( dir_path('themes').$tm.'/'.$fr)) //Проверка существования файла
   {
        $f = @fopen( dir_path('themes').$tm.'/'.$fr,'r');
		
        while (!feof($f)) 
        {
            $text.= @fread($f,100);   
        }
		
        @fclose($f);
		
		if (base64_decode($_GET['file_edit']) == '/style.css') $text = win1251_utf8($text);
	    if (!is_writeable( dir_path('themes').$tm.'/'.$fr)) //Проверка доступа для записи
        {
           $GLOBALS["file_e"] = 1;		  
        }
		//преобразование спец. символов
		$text = htmlspecialchars($text);
	    return $text;
   }
   else
   {
      return false;
   }
   
}   


function all_file ($fl, $themes, $name)
{
    echo '<SELECT NAME="filename" ONCHANGE="top.location.href = this.options[this.selectedIndex].value">';
      if ($dir = opendir( dir_path('themes').$themes.$fl)) 
	  {
		while (($templates = readdir($dir)) !== false) 
		{
			if (($templates != "CVS") && ($templates != ".") && ($templates != ".svn") && ($templates != "..") && (substr_count($templates,".")==1)) 
			{
				$templates_array[] = $templates;
			}
		}
		closedir($dir);
	}

   echo "<OPTION selected>$name</OPTION>";
   foreach($templates_array as $type)
   {
	    echo "<OPTION value=\"?file_edit=".base64_encode($fl.$type)."\">".$type."</OPTION>";
   }	

    echo '</select>';

}

?>


<table border=0 width="100%">
<tr><td valign="top">
<?php 
   $st = '<b>/themes/<font color="#287dd3">'.$themes.'/'.$file.'</font></b>'; 
   $st = str_replace('//','/',$st);
   echo ($st);
   if (is_writeable(DIR_FS_CATALOG.'/themes/'.$themes.'/'.$file))
   {
      echo "<br /><font color='green'><b>".THEMES_WRITEABLE_YES."</b></font><br />";
   }
   else
   {
      echo "<br /><font color='red'><b>".THEMES_WRITEABLE_NO."</b></font><br />";
   }
?>

<form method="post" action="">
<input type="hidden" value="<?php echo $file; ?>" />
      <textarea name="themes_text" rows="20" cols="80"><?php echo get_file($file, $themes); ?></textarea><br /><br />

<?php
   if ($GLOBALS["file_e"] == 0) 
   {
      echo '<span class="button"><button type="submit" value="'.THEMES_SAVE.'" />'.THEMES_SAVE.'</span>';
   }
   else
   {
      echo '<span class="button"><button type="submit" disabled value="'.THEMES_SAVE.'" />'.THEMES_SAVE.'</span> - '.THEMES_P;
   }
?>	  

</form>
</td>
<td width="7%;"></td>
<td width="90%" valign="top">
<a href="?file_edit=<?php echo base64_encode('/index.html'); ?>">index.html</a><br />
<a href="?file_edit=<?php echo base64_encode('/style.css'); ?>">style.css</a><br /><br />
<?php
   all_file('/boxes/',$themes, '[ Р‘Р»РѕРєРё ]');
   echo "<br /><br />";
   all_file('/module/',$themes, '[ РњРѕРґСѓР»Рё ]');
   echo "<br /><br />";
   all_file('/module/product_listing/',$themes, '[ product_listing ]');
      echo "<br /><br />";
   all_file('/module/product_options/',$themes, '[ product_options ]');
      echo "<br /><br />";
   all_file('/module/product_info/',$themes, '[ product_info ]');
      echo "<br /><br />";
   all_file('/module/categorie_listing/',$themes, '[ categorie_listing ]');
?>

</td></tr>
</table>			

	 
<?

  echo '</div></td></tr></table></td></tr></table>';
?>
<?php $main->bottom(); ?>