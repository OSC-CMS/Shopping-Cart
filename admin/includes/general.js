/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.0
#####################################
*/

function showLayer(Name) {

        switch (Name) {
        case 'config':
        document.layers.l_export.visibility = "hide";
        document.layers.l_config.visibility = "show";
        document.layers.l_import.visibility = "show";
        break;

        case 'export':

        break;

        case 'import':

        break;
    }

}


function toggleBox(szDivID) {

  if (document.layers) { // NN4+
    if (document.layers[szDivID].visibility == 'visible') {
        document.layers[szDivID].visibility = "hide";
        document.layers[szDivID].display = "none";
        document.layers[szDivID+"SD"].fontWeight = "normal";
    } else {
        document.layers[szDivID].visibility = "show";
        document.layers[szDivID].display = "inline";
    }
  } else if (document.getElementById) { // gecko(NN6) + IE 5+
    var obj = document.getElementById(szDivID);
    if (obj.style.visibility == 'visible') {
        obj.style.visibility = "hidden";
        obj.style.display    = "none";
    } else {
        obj.style.visibility = "visible";
        obj.style.display    = "inline";
    }
  } else if (document.all) { // IE 4
    if (document.all[szDivID].style.visibility == 'visible') {
        document.all[szDivID].style.visibility = "hidden";
        document.all[szDivID].style.display = "none";
    } else {
        document.all[szDivID].style.visibility = "visible";
        document.all[szDivID].style.display = "inline";
    }
  }
}

var ru2en = { 
  ru_str : "АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя", 
  en_str : ['A','B','V','G','D','E','JO','ZH','Z','I','J','K','L','M','N','O','P','R','S','T',
    'U','F','H','C','CH','SH','SHH',String.fromCharCode(35),'I','','JE','JU',
    'JA','a','b','v','g','d','e','jo','zh','z','i','j','k','l','m','n','o','p','r','s','t','u','f',
    'h','c','ch','sh','shh',String.fromCharCode(35),'i','','je','ju','ja'], 
  translit : function(org_str) { 
    var tmp_str = ""; 
    for(var i = 0, l = org_str.length; i < l; i++) { 
      var s = org_str.charAt(i), n = this.ru_str.indexOf(s); 
      if(n >= 0) { tmp_str += this.en_str[n]; } 
      else { tmp_str += s; } 
    } 
    return tmp_str; 
  } 
}

function onchange_products_page_url ()
{
   str = ru2en.translit(document.getElementById('products_name').value); 
   str = str.toLowerCase();
   str = str + '.html';
   
   str = str.replace(/\s+/g,"-");
   str = str.replace(/[!;$,'":*^%#@\[\]&{}]+/g,"");

   document.getElementById('products_page_url').value = str;
}

function onchange_categories_url ()
{
   str = ru2en.translit(document.getElementById('categories_name').value); 
   str = str.toLowerCase();
   str = str + '.html';
   
   str = str.replace(/\s+/g,"-");
   str = str.replace(/[!;$,'":*^%#@\[\]&{}]+/g,"");

   document.getElementById('categories_url').value = str;
}

// news
function onchange_news_url ()
{
   str = ru2en.translit(document.getElementById('headline').value); 
   str = str.toLowerCase();
   str = str + '.html';
   
   str = str.replace(/\s+/g,"-");
   str = str.replace(/[!;$,'":*^%#@\[\]&{}]+/g,"");

   document.getElementById('news_page_url').value = str;
}

// articles
function onchange_articles_url ()
{
   str = ru2en.translit(document.getElementById('articles_name').value); 
   str = str.toLowerCase();
   str = str + '.html';
   
   str = str.replace(/\s+/g,"-");
   str = str.replace(/[!;$,'":*^%#@\[\]&{}]+/g,"");

   document.getElementById('articles_page_url').value = str;
}

function SwitchCheck ()
  {
    var maf = document.multi_action_form;
    var len = maf.length;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "plugins[]" || e.name == "access[]" || e.name == "multi_categories[]" || e.name == "multi_orders[]" || e.name == "groups[]") 
      {
          if (e.checked == true) {
              e.checked = false;
          } else {
              e.checked = true;
          }
      }
    }
  }
  
  function SwitchCheckAccounting ()
  {
    var maf = document.accounting;
    var len = maf.length;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "plugins[]" || e.name == "access[]" || e.name == "multi_categories[]" || e.name == "multi_orders[]" || e.name == "groups[]") 
      {
          if (e.checked == true) {
              e.checked = false;
          } else {
              e.checked = true;
          }
      }
    }
  }