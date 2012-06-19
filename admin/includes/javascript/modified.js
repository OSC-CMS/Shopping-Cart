/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

var MSG_BEFOREUNLOAD = "Часть данных исправленных вами в этой форме не будет сохранена.";
var form_modified = false;
var root = (window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null);

function add_event(o, e, f) { 
	if (o.addEventListener) o.addEventListener(e, f, false); 
	else if (o.attachEvent) o.attachEvent('on'+e, f); 
}

function init_modified() {
	if (typeof(root.onbeforeunload) != "undefined") root.onbeforeunload = check_modified;
	var btn_save = document.getElementById('btn_save');
	if (btn_save) btn_save.disabled = true;
	for (var i = 0; oCurrForm = document.forms[i]; i++) if (oCurrForm.getAttribute("cf")) {
		for (var j = 0; oCurrFormElem = oCurrForm.elements[j]; j++) {
			if (oCurrFormElem.getAttribute("cf")!='false') {
				add_event(oCurrFormElem, 'change', set_modified);
				add_event(oCurrFormElem, 'keypress', set_modified);
				add_event(oCurrFormElem, 'drop', set_modified);
			}
		}
		add_event(oCurrForm, 'submit', ignore_modified);
	}
}

function set_modified(e) {
	if (e) {
		var el = window.event ? window.event.srcElement : e.currentTarget;
		el.style.border = '2px red inset';
	}
	form_modified = true;
	var btn_save = document.getElementById('btn_save');
	if (btn_save) btn_save.disabled = false;
}

function ignore_modified(){
	if (typeof(root.onbeforeunload) != "undefined") root.onbeforeunload = null;
}

function check_modified(e) { 
	if (form_modified) {
		return MSG_BEFOREUNLOAD; 
	}
}

if (root) { add_event(root, 'load', init_modified); }
