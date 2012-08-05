/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

function CheckMultiForm ()
  {
    var ml = document.multi_action_form;
    var len = ml.elements.length;
    for (var i = 0; i < len; i++) 
    {
      var e = ml.elements[i];
      if (e.name == "multi_products[]" || e.name == "multi_categories[]" || e.name == "multi_orders[]") 
      {
          if (e.checked == true) {
              return true;
          }
      }
    }
    alert('Выделите хотя бы один элемент!\nPlease check at least one element!');
    return false;
  }

//for reverting checkboxes
function SwitchCheck ()
  {
    var maf = document.multi_action_form;
    var len = maf.length;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "multi_products[]" || e.name == "multi_categories[]" || e.name == "multi_orders[]" || e.name == "groups[]") 
      {
          if (e.checked == true) {
              e.checked = false;
          } else {
              e.checked = true;
          }
      }
    }
  }


  
function Checkb(oForm, checked)
{

	if (typeof(oForm['multi_products[]']) !='undefined')
	{
	    if (typeof(oForm['multi_products[]'].length) != 'undefined' )
	       {
	           for (var i=0; i < oForm['multi_products[]'].length; i++) 
                 {
                     oForm['multi_products[]'][i].checked = checked;
	             }  
	       }
	}
	
	if (typeof(oForm['multi_categories[]']) !='undefined')
	{
	  if (typeof(oForm['multi_categories[]'].length) != 'undefined')
        {
           for (var i=0; i < oForm['multi_categories[]'].length; i++) 
               {
                  oForm['multi_categories[]'][i].checked = checked;
	           } 
	    }
	}
  	
}
  
  
  //for checking all checkboxes
function CheckAll (wert)
  {
    var maf = document.forms[multi_action_form].elements['multi_categories[]'];
    var len = maf.length;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "multi_products[]" || e.name == "multi_categories[]" || e.name == "multi_orders[]" || e.name == "groups[]")  
      {
        e.checked = wert;
      }
    }
  }
function CheckAll (wert)
  {
    var maf = document.edit_content;
    var len = maf.length;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "groups[]")  
      {
        e.checked = wert;
      }
    }
  }
  
//for checking products only
function SwitchProducts ()
  {
    var maf = document.multi_action_form;
    var len = maf.length;
    var flag = false;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "multi_products[]") 
      {
          if (flag == false) { 
              if (e.checked == true) { 
                  wert = false; 
              } else { 
                  wert = true; 
              } 
              flag = true; 
          }
          e.checked = wert;
      }
    }
  }

//for checking categories only
function SwitchCategories ()
  {
    var maf = document.multi_action_form;
    var len = maf.length;
    var flag = false;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "multi_categories[]") 
      {
          if (flag == false) { 
              if (e.checked == true) { 
                  wert = false; 
              } else { 
                  wert = true; 
              } 
              flag = true; 
          }
          e.checked = wert;
      }
    }
  }   

function getListOfProductImages(el, i, p_dir, p_image)
{
	var dir = document.getElementsByName(i == 0 ? 'upload_dir_image_0' : 'mo_pics_upload_dir_image_'+(i - 1))[0];
	$("#divOfImages"+i).text('loading ...');
	$("#divOfImages"+i).load("ajax_product_images.php", {index:i, product_dir:p_dir, product_image:p_image, product_subdir:dir.value}, function(){getListOfProductComplete(i);});
}
function getListOfProductComplete(i)
{
	var el = document.getElementsByName(i == 0 ? 'get_file_image_0' : 'mo_pics_get_file_image_'+(i - 1))[0];
	var dir = document.getElementsByName(i == 0 ? 'upload_dir_image_0' : 'mo_pics_upload_dir_image_'+(i - 1))[0];
	el.onchange = function()
	{
		document.getElementById('getListOfProductPrevios_'+i).src = '../images/product_images/thumbnail_images/'+dir.value+el.value;
	}
}