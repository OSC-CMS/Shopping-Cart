var MSG_BEFOREUNLOAD = "";
var form_modified = false;
var root = (window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null);
var form_id = document.getElementsByTagName('form');

function add_event(o, e, f) {
	if (o.addEventListener) o.addEventListener(e, f, false);
	else if (o.attachEvent) o.attachEvent('on'+e, f);
}

function init_modified() {
	if (typeof(root.onbeforeunload) != "undefined") root.onbeforeunload = check_modified;
	var btn_save = document.getElementById('btn_save');
	if (btn_save) btn_save.disabled = true;
	for (var i = 0; oCurrForm = document.forms[i]; i++) if (oCurrForm.getAttribute("id")) {
		for (var j = 0; oCurrFormElem = oCurrForm.elements[j]; j++) {
			if (form_id ='contentform') {
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
		var el = window.event ? window.event.srcElement : e.target;
		el.style.border = '1px red inset';
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
