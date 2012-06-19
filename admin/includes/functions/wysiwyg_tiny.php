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

defined( '_VALID_OS' ) or die( 'Прямой доступ  не допускается.' );

function os_wysiwyg_tiny($type, $lang, $langID = '') {

$js_src = http_path('includes_admin') .'javascript/tiny_mce/tiny_mce.js';
//$js_browser_src = http_path('includes_admin') .'javascript/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php';

	switch($type) {
                // WYSIWYG editor latest news textarea named latest_news
                case 'latest_news':
$val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

                // WYSIWYG editor faq textarea named faq
                case 'faq':
                        $val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

                // WYSIWYG editor latest news textarea named articles_description
                case 'articles_description':
                        $val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

                // WYSIWYG editor latest news textarea named topics_description
                case 'topics_description':
                        $val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

                // WYSIWYG editor products description textarea named products_description
                case 'products_description':
                        $val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

                // WYSIWYG editor products short description textarea named products_short_description
                case 'products_short_description':
                        $val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

                // WYSIWYG editor content manager
                case 'content_manager':
                        $val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

                // WYSIWYG editor products content manager
                case 'products_content':
                        $val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

                // WYSIWYG editor categories description
                case 'categories_description':
                        $val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

                // WYSIWYG editor newsletter
                case 'mail':
                        $val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

                // WYSIWYG editor gift voucher
                case 'gv_mail':
                        $val ='<script type="text/javascript" src="'.$js_src.'"></script>
                        <script type="text/javascript" src="'.$js_browser_src.'"></script>
                        	   <script type="text/javascript">
tinyMCE.init({
	mode : "none",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "'.$lang.'",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	//file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.HTTP_SERVER . DIR_WS_CATALOG.'admin/includes/javascript/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
                        	   	</script>';
                        break;

    }
    
   	return $val;

}
?>