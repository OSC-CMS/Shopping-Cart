/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

function loadXMLDoc(ajax_page, put_vars, caching) {
  var url="./index_ajax.php?ajax_page=" + ajax_page;
  var req = new JsHttpRequest();
  req.onreadystatechange = function() {
    if (req.readyState == 4) {
      if (req.responseJS) {
        for (var id in req.responseJS) {
          if (document.getElementById(id)) {
            document.getElementById(id).innerHTML = req.responseJS[id];
          }
        }
      }
      if(typeof(AJAX_DEBUG) == 'boolean' && AJAX_DEBUG == true) {
        if (req.responseText) {
          var divDBG = document.getElementById('ajax-debug');
          if(!divDBG) {
            divDBG = document.createElement("div");
            divDBG.id = "ajax-debug";
            divDBG.style.position = "absolute";
            divDBG.style.overflown = "hidden";
            divDBG.style.left = 0 + "px";
            divDBG.style.top = 0 + "px";
            divDBG.style.top = 0 + "px";
            divDBG.style.backgroundColor = "White";
            divDBG.style.margin = "10px";
            divDBG.style.padding = "4px";
            divDBG.style.border = "3px dashed red";
            document.body.appendChild(divDBG);
          }
  //        vardump(divDBG.style);
          if (divDBG) {
            divDBG.innerHTML = req.responseText;
          }
        }
      }
    }
  }
  if(typeof(caching) != 'boolean')
  	caching = true;
  req.caching = caching;
  req.open('POST', url, true);
  req.send(put_vars);
}

function hashFormFields(of) {
  var hsh = new Object();
  for(var i=0;i<of.length;i++) {
    var sb=of[i].name;
    if(!(/submit/i.test(of[i].type))) {
      if((/checkbox/i.test(of[i].type))) {
        if(of[i].checked) {
          hsh[sb]=of[i].value;
        }
      } else {
        hsh[sb]=of[i].value;
      }
    }
  }
  return hsh;
}

function clearFormFields(of, val) {
	var val = val || 0;
  for(var i=0;i<of.length;i++) {
    var sb=of[i].name;
    if(!(/submit/i.test(of[i].type)) && !(/hidden/i.test(of[i].type))) {
      if((/checkbox/i.test(of[i].type))) {
        if(of[i].checked) {
          of[i].checked = false;
        }
      } else {
        of[i].value = val;
      }
    }
  }
  return false;
}
