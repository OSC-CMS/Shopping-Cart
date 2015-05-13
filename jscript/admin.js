/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

// При выборе select открывает div
function selectShowHide(value, id)
{
  $('.'+id+' > div').hide();
  $('.div-'+id+'-'+value).show();
}


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

function onchange_products_page_url()
{
   str = ru2en.translit(document.getElementById('products_name').value); 
   str = str.toLowerCase();
   str = str + '.html';
   
   str = str.replace(/\s+/g,"-");
   str = str.replace(/[!;$,'":*^%«»#@\[\]&{}]+/g,"");

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

function SwitchCheck ()
  {
    var maf = document.multi_action_form;
    var len = maf.length;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "multi_products[]" || e.name == "plugins[]" || e.name == "access[]" || e.name == "multi_categories[]" || e.name == "multi_orders[]" || e.name == "groups[]")
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

// bundles
$(function() {
	// Удаление пункта набора
	$('a.del_bundles').click(function() {
		$(this).closest("tr").fadeOut(200, function() { $(this).remove(); });
		return false;
	});

	// Удаление нового пункта набора
	$('a.del_bundles_new').click(function() {
		$(this).closest("table").fadeOut(200, function() { $(this).remove(); });
		return false;
	});

	// Добавление пункта набора
	var bundles = $('#new_bundles').clone(true);
	$('#new_bundles').remove().removeAttr('id');
	$('#bundles-block #add-new-bundles').click(function() {
		$(bundles).clone(true).appendTo('#bundles').fadeIn('slow');
		return false;
	});
});

$(document).ready(function()
{
	// табы категорий и товаров - редактирование и добавление
	$('#productTabs a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});

	$('.selectAllCheckbox').on('click', function() {
		$('.table-big-list tr').toggleClass("selected-tr");
	});
	$('.selectAllCheckboxProducts').on('click', function() {
		$('.products_tr').toggleClass("selected-tr");
	});
	$('.selectAllCheckboxCategories').on('click', function() {
		$('.categories_tr').toggleClass("selected-tr");
	});
	$(':checkbox').on('click', function() {
		$(this).parents('tr').toggleClass("selected-tr");
	});

	$('.btn-disabled').attr('disabled', 'disabled');
	$('[type="checkbox"]').on('click', function() {
		if ($('[type="checkbox"]:checked').length) {
			$('.btn-disabled').removeAttr('disabled');
		} else {
			$('.btn-disabled').attr('disabled', 'disabled');
		}
	});
});









$(document).ready(function ()
{
	// Выделить все

	$('#checkAll').on('click', function() {
		$('#tableList input[type="checkbox"]').attr('checked', $('#tableList input[type="checkbox"]:not(:checked)').length>0);
	});

	/*
	------------------------------------------------------
		Разное
	------------------------------------------------------
	*/
	$('.tt').tooltip('hide');
	$(".alert").alert();
	$('#addMsg').modal('hide');

	/*
	------------------------------------------------------
		Показать-спрятать tr таблицы
	------------------------------------------------------
	*/
	$('a.show_or_hide').on('click', function(e) {
		e.preventDefault();
		// id элемента
		var itemId = $(this).data('item');
		// подсвечиваем таблицу
		$('.item_selected_'+itemId).toggleClass('info-tr');
		// открываем-закрываем таблицу
		$('.item_'+itemId).toggle();
	});

	/*
	------------------------------------------------------
		Динамическое изменение ширины админки
	------------------------------------------------------
	*/
	/*
	if ($.cookie('fluid_layout') == 1)
	{
		$('.container').addClass('fluid-layout');
	}
	$('.change-layout-width').on('click', function()
	{
		if ($.cookie('fluid_layout') == 0 && !$('.container').hasClass('fluid-layout'))
		{
			$.cookie('fluid_layout', '1');
			$('.container').toggleClass('fluid-layout');
		}
		else
		{
			$.cookie('fluid_layout', '0');
			$('.container').toggleClass('fluid-layout');
		}

		return false;
	});
	*/

	/*
	------------------------------------------------------
		Плавающая панель
	------------------------------------------------------
	*/
	/*$('.trigger').on('click', function() {
		$(".panel").toggle("fast");
		return false;
	});*/

    /*
     ------------------------------------------------------
     Панель настроек и помощи
     ------------------------------------------------------
     */
    $('.sm-link-setting').on('click', function() {
        $(this).toggleClass('active');
        $("#setting-menu-wrap").toggle();
        return false;
    });
    $('.sm-link-help').on('click', function() {
        $(this).toggleClass('active');
        $("#help-menu-wrap").toggle();
        return false;
    });

	/*
	------------------------------------------------------
		Табы
	------------------------------------------------------
	*/
	$('.default-tabs a').on('click', function() {
		$(this).tab('show');
		return false;
	});

	/*
	------------------------------------------------------
		DateTime
	------------------------------------------------------
	*/
	$('.formDatetime').on('click', function() {
		$(this).datetimepicker('show');
	});

	/*
	------------------------------------------------------
		Сохранение форм
	------------------------------------------------------
	*/
	$('.ajax-save-form').on('click', function()
	{
		// ID формы
		var aFormId = $('.ajax-save-form').closest('form').attr('id');
		// Экшон
		var aAction = $(this).data('form-action');
		// Нужно ли перезагружать страницу
		var aReload = $(this).data('reload-page');

		// Проверка полей
		$('#'+aFormId).parsley({
			//successClass: 'success',
			errorClass: 'error',
			errors: {
			classHandler: function(el) {
				return $(el).closest('.control-group');
			},
				errorsWrapper: '<span class="help-block"></span>',
				errorElem: '<span></span>'
			}
		});

		// ajaxForm http://jquery.malsup.com/form/
		$('#'+aFormId).ajaxForm({
			type: "post",
			url: aSetting.urlAdmin+"ajax.php?ajax_action="+aAction,
			dataType: "json",
			cache: false,
			beforeSend: function() {
				$('.ajax-load-mask').removeClass('off');
			},
			success: function(returnData) {
				$('.ajax-load-mask').addClass('off');

				// если что-то вернулось
				if (returnData)
				{
					// показываем уведомление, если оно есть
					if (returnData.msg)
					{
						$.jnotify(returnData.msg, returnData.type);
					}

					// выполняем действия после сохранения
					if (aReload == 1 || returnData.urlBack != undefined || returnData.back == 1)
					{
						if (returnData.back == 1)
							setTimeout('window.history.back()', aSetting.timeout);
						else if (returnData.urlBack != undefined)
							setTimeout(function(){location.href = aSetting.urlAdmin+returnData.urlBack} , aSetting.timeout);
						else
							setTimeout('window.location.reload()', aSetting.timeout);
					}
				}
			},
			error: function(returnData) {
				// если что-то вернулось
				if (returnData)
				{
					// показываем уведомление, если оно есть
					if (returnData.msg)
					{
						$.jnotify(returnData.msg, returnData.type);
					}
				}
			}
		});
	});

	/*
	 ------------------------------------------------------
		Выделение главной картинки у товара при новом назначении
	 ------------------------------------------------------
	 */
	$('.ajax-change-main-image').on('click', function()
	{
		$(this).closest("li").toggleClass('selected-main');
	});

	/*
	 ------------------------------------------------------

	 ------------------------------------------------------
	 */
	$('.ajax-action').on('click', function()
	{
		// Экшон
		var aAction = $(this).data('action');
		// Нужно ли перезагружать страницу
		var aReload = $(this).data('reload-page');

		$.ajax({
			type: "get",
			url: aSetting.urlAdmin+"ajax.php?ajax_action="+aAction,
			dataType: "json",
			cache: false,
			beforeSend: function() {
				$('.ajax-load-mask').removeClass('off');
			},
			success: function(returnData) {
				$('.ajax-load-mask').addClass('off');

				// если что-то вернулось
				if (returnData)
				{
					// показываем уведомление, если оно есть
					if (returnData.msg)
					{
						$.jnotify(returnData.msg, returnData.type);
					}

					// Перезагружаем страницу, если нужно
					if (aReload == 1)
					{
						setTimeout('window.location.reload()', aSetting.timeout);
					}
				}
			},
			error: function(returnData) {
				// если что-то вернулось
				if (returnData)
				{
					// показываем уведомление, если оно есть
					if (returnData.msg)
					{
						$.jnotify(returnData.msg, returnData.type);
					}
				}
			}
		});
	});

	$('.ajax-change-select').on('change', function()
	{
		// параметры берем из data атрибутов
		var thisId = $(this).val(),
			ajax_action = $(this).data('ajax-action'),
			sub_select = $(this).data('sub-select'),
			sub_select_value = $(this).data('sub-select-value'),
			sub_select_title = $(this).data('sub-select-title'),
			sub_select_selected = $(this).data('sub-select-selected');

		dinamicSelect(thisId, ajax_action, sub_select, sub_select_value, sub_select_title, sub_select_selected);
	});

	/*
	#######################################################

		Модальные окна и все что с ними связано

	#######################################################
	*/
	var $modal = $('#ajax-modal');

	/*
	------------------------------------------------------
		Открытие ajax модального окна
	------------------------------------------------------
	*/
	$('.ajax-load-page').on('click', function()
	{
		// Эффект загрузки
		$('body').modalmanager('loading');

		// какую страницу подгружать
		var load_page = $(this).data('load-page');

		// ID формы
		var aFormId = $('.ajax-load-page').closest('form').attr('id');
		oForm = $('#'+aFormId).serializeArray();

		// если есть container, то окно Full Width, если нет, то ширина 760px
		if ($(this).data('container') == 1)
			$modal.addClass('container');
		else
			$modal.data("width", 760);

		$modal.load(aSetting.urlAdmin+'ajax.php?ajax_page='+load_page, oForm, function() {
			$modal.modal();
		});

		return false;
	});

/*
		$("#form_next").live("click", function(event)
		{
			var obj = $("#form").serializeArray();

			$("#form").load("form.php?ajax=y&section=request&cid="+$("#country_id").val(), obj, function(response, status, xhr) {
				alert(response);
			});
			return false;
		});
* */


	/*
	------------------------------------------------------
		Сохранение формы в модальных окнах
	------------------------------------------------------
	*/
	$modal.on('click', '.save-form', function()
	{
		// Эффект загрузки
		$modal.modal('loading');
		// ID формы
		var aFormId = $('.save-form').closest('form').attr('id');//get(0).form.id;
		// Экшон
		var aAction = $(this).data('form-action');
		// Нужно ли перезагружать страницу
		var aReload = $(this).data('reload-page');

		// ajaxForm http://jquery.malsup.com/form/
		$('#'+aFormId).ajaxForm({
			type: "post",
			url: aSetting.urlAdmin+"ajax.php?ajax_action="+aAction,
			dataType: "json",
			cache: false,
			success: function(returnData) {
				// закрываем окно
				$modal.modal('hide');
				// если что-то вернулось
				if (returnData)
				{
					// показываем уведомление, если оно есть
					if (returnData.msg)
					{
						$.jnotify(returnData.msg, returnData.type);
					}

					// Перезагружаем страницу, если нужно
					if (aReload == 1)
					{
						setTimeout('window.location.reload()', aSetting.timeout);
					}
				}
			},
			error: function(returnData) {
				// закрываем окно
				$modal.modal('hide');
				// если что-то вернулось
				if (returnData)
				{
					// показываем уведомление, если оно есть
					if (returnData.msg)
					{
						$.jnotify(returnData.msg, returnData.type);
					}
				}
			}
		});
	});

	/*
	------------------------------------------------------
		Действия в модальных окнах по ссылке + автообновление контента в модальном окне
	------------------------------------------------------
	*/
	$modal.on('click', '.preload', function()
	{
		$modal.modal('loading');

		// Экшон
		var aAction = $(this).data('action');
		// Параметры которые нужно передать
		var aParams = $(this).data('params');
		// Страница которую загружаем
		var aLoadpage = $(this).data('load-page');

		$.ajax({
			type: "get",
			url: aSetting.urlAdmin+"ajax.php?ajax_action="+aAction+"&"+aParams,
			dataType: "html",
			cache: false,
			success: function(returnData) {
				$modal.load(aSetting.urlAdmin+'ajax.php?ajax_page='+aLoadpage+"&"+aParams, '', function() {
					$modal.modal();
				});
			}
		});
		return false;
	});

	/*
	------------------------------------------------------
		Зависимые селекты в модальных окнах
	------------------------------------------------------
		Пример
	------------------------------------------------------
		<select 
			class="change_select" - Класс
			data-ajax-action="load_products" - Какое действие выполняем в ajax.php
			data-sub-select="products_id" - Второй select в котором будет результат
			data-sub-select-value="products_id" - Значения value второго select
			data-sub-select-title="products_name" - Значения названия второго select
			name="categories_select"
		>
			<option value="" selected="selected">Список категорий</option>
			<option value="1">Категория 1</option>
			<option value="2">Категория 2</option>
		</select>

		<select id="products_id" name="products_id">
			<option value="1">Товар 1</option>
			<option value="2">Товар 2</option>
		</select>
	------------------------------------------------------
	*/
	$modal.on('change', '.change_select', function()
	{
		// параметры берем из data атрибутов
		var thisId = $(this).val(),
			ajax_action = $(this).data('ajax-action'),
			sub_select = $(this).data('sub-select'),
			sub_select_value = $(this).data('sub-select-value'),
			sub_select_title = $(this).data('sub-select-title'),
			sub_select_selected = $(this).data('sub-select-selected');

		dinamicSelect(thisId, ajax_action, sub_select, sub_select_value, sub_select_title, sub_select_selected);
	});
	/*
	------------------------------------------------------
		DateTime
	------------------------------------------------------
	*/
	$modal.on('click', '.form_datetime', function()
	{
		$(this).datetimepicker('show');
	});
});

/*
------------------------------------------------------
	Изменение статусов (on/off)
------------------------------------------------------
*/
$(function(){
	$('.ajax-change-status').on('click', function()
	{
		// column
		var data_column = $(this).data('column');
		// action
		var action = $(this).data('action');
		// id
		var data_id = $(this).data('id');
		// new status set
		var data_status = $(this).data('status');
		// show status
		var data_show_status = $(this).data('show-status');

		$.ajax({
			url: aSetting.urlAdmin+"ajax.php?ajax_action="+action,
			type: "post",
			data: {id: data_id, status: data_status, column: data_column},
			cache: false,
			dataType: "json",
			success: function(returnData)
			{
				// если что-то вернулось
				if (returnData)
				{
					$.jnotify(returnData.msg, returnData.type);
					$(".status_"+data_id+"_"+data_status+"_"+data_column).hide();
					$(".status_"+data_id+"_"+data_show_status+"_"+data_column).show();
				}
			},
			error: function(returnData)
			{
				// если что-то вернулось
				if (returnData)
				{
					$.jnotify(returnData.msg, returnData.type);
				}
			}
		});
		return false;
	});
});

/*
------------------------------------------------------
	Удаление товара из заказа
------------------------------------------------------
*/
$(function(){
	$('.ajax_delete_order_product').on('click', function()
	{
		var item = $(this);
		// id
		var data_id = item.data('id');
		// action
		var action = item.data('action');

		$.ajax({
			url: aSetting.urlAdmin+"ajax.php?ajax_action="+action,
			type: "post",
			data: {id: data_id},
			cache: false,
			dataType: "json",
			success: function(returnData)
			{
				// удаляем tr
				item.parents("tr").fadeOut(200);
				// если что-то вернулось
				if (returnData)
				{
					$.jnotify(returnData.msg, returnData.type);
				}
			}
		});
	});
});

/*
------------------------------------------------------
	Editable - быстрое редактирование данных
	INPUT TEXT
------------------------------------------------------
*/
$(function(){
	$('.ajax_editable').editable({
		ajaxOptions: {
			dataType: 'json'
		},
		success: function(response, newValue) {
			// если что-то вернулось
			if (response) {
				$.jnotify(response.msg, response.type);
			}
		}
	});

	$('.ajax_editable').each(function(){
		$(this).editable('option', 'url', aSetting.urlAdmin+'ajax.php?ajax_action='+$(this).data('action'));
	});
});

/*
------------------------------------------------------
	Editable - быстрое редактирование данных
	SELECT
------------------------------------------------------
*/
$(function(){
    $('.ae_select').editable({
		ajaxOptions: {
			type: 'get',
			dataType: 'json'
		},
        showbuttons: false,
        source: function()
        {
        	return aSetting.urlAdmin+"ajax.php?ajax_action="+$(this).data('action');
        },
        success: function(response, newValue) {
			// если что-то вернулось
			if (response) {
				$.jnotify(response.msg, response.type);
			}
		}
    });

	$('.ae_select').each(function(){
		$(this).editable('option', 'url', aSetting.urlAdmin+'ajax.php?ajax_action='+$(this).data('url'));
	});
});

/*
------------------------------------------------------
	Обработка зависимых селектов в один уровень
------------------------------------------------------
*/
function dinamicSelect(this_id, ajax_action, sub_select, sub_select_value, sub_select_title, sub_select_selected)
{
	sub_select_selected = sub_select_selected || '';
	var subSelect = $('select[name="'+sub_select+'"]');

	subSelect.attr('disabled', 'disabled');

	$.getJSON(aSetting.urlAdmin+"ajax.php", {ajax_action:ajax_action, this_id:this_id}, function(returnList){
		if (returnList.length != 0)
		{
			subSelect.empty();

			$.each(returnList, function(){
				// проверям выбранные заранее
				if (sub_select_selected != '' && sub_select_selected == this[sub_select_value])
					selectedOption = 'selected';
				else
					selectedOption = '';

				subSelect.append('<option value="'+this[sub_select_value]+'" '+selectedOption+'>'+this[sub_select_title]+'</option>');
			});

			subSelect.removeAttr('disabled');
		}
		else
			subSelect.empty();
	});
}

/*
------------------------------------------------------
	Добавление товаров в заказ
------------------------------------------------------
*/
$(function(){
	// Удаление товара из быстрого добавления
	$('.del_new_product').live('click', function() {
		$(this).parents("table").fadeOut(200, function() {
			$(this).remove();
		});
	});

	// Добавление товара к заказу
	var new_product = $('#new_product').clone(true);
	$('#new_product').remove().removeAttr('id');

	$("#add_product").autocomplete({
		serviceUrl:aSetting.urlAdmin+'ajax.php?ajax_action=products_searchByName_get',
		minChars:0,
		noCache: false,
		onSelect: function(value, data){
			newProduct = new_product.clone().appendTo('#products_app');
			newProduct.removeAttr('id');
			newProduct.find('a.product_name').html(data.products_name);
			newProduct.find('a.product_name').attr('href', 'categories.php?pID='+data.products_id+'&action=new_product');
			newProduct.find('.products_model').html(data.products_model);
			newProduct.find('input[name*=products_id]').val(data.products_id);
			newProduct.find('input[name*=products_price]').val(data.products_price);
			newProduct.find('input[name*=products_model]').val(data.products_model);
			newProduct.find('input[name*=products_shippingtime]').val(data.products_shippingtime);
			newProduct.find('input[name*=products_name]').val(data.products_name);
			$("#add_product").val(''); 
			newProduct.show();
		}
	});
});

/*
------------------------------------------------------
	Сохранение атрибутов
------------------------------------------------------
*/
$(function(){
	$('.ajax_save_attr').on('click', function()
	{
		var aNameValues = {},
			aFields = {},
			current_product_id = $('input[name="current_product_id"]').val(),
			cPathID = $('input[name="cPathID"]').val(),
			action = $('input[name="action"]').val();

		// получаем все чекбоксы какие есть в форме
		$("input[type=checkbox]").each(function()
		{
			// только выбранные чекбоксы
			if ($(this).attr("checked"))
			{
				fieldId = $(this).attr("name").match(/\[(\d+)\]\s?(.*)/);

				// формируем массив из полей у которых выбран соответствующий чекбокс
				var aNameValues = {
					price: $('input[name="'+fieldId[1]+'_price"]').val(),
					prefix: $('select[name="'+fieldId[1]+'_prefix"]').val(),
					sortorder: $('input[name="'+fieldId[1]+'_sortorder"]').val(),
					weight_prefix: $('select[name="'+fieldId[1]+'_weight_prefix"]').val(),
					model: $('input[name="'+fieldId[1]+'_model"]').val(),
					stock: $('input[name="'+fieldId[1]+'_stock"]').val(),
					weight: $('input[name="'+fieldId[1]+'_weight"]').val(),
					download_file: $('select[name="'+fieldId[1]+'_download_file"]').val(),
					download_count: $('input[name="'+fieldId[1]+'_download_count"]').val(),
					download_expire: $('input[name="'+fieldId[1]+'_download_expire"]').val()
				};

				aFields[fieldId[1]] = aNameValues;
			}
		});

		$.ajax({
			url: aSetting.urlAdmin+"ajax.php?ajax_action=products_setAttributes",
			type: "post",
			data: {action: action, cPathID: cPathID, current_product_id: current_product_id, attributes: aFields},
			cache: false,
			dataType: "json",
			success: function(returnData)
			{
				$.jnotify(returnData.msg, returnData.type);
			},
			error: function(returnData)
			{
				$.jnotify(returnData.msg, returnData.type);
			}
		});
		return false;
	});
});

/*
------------------------------------------------------
	Подтверждение действия Да\Нет (confirmation)
------------------------------------------------------
*/
$(function(){
	$('a[data-confirm]').on('click', function()
	{
		var item = $(this);
		// Экшон
		var aAction = item.data('action');
		// Нужно ли перезагружать страницу
		var aReload = item.data('reload-page');
		// Идентификатор того, что нужно удалить
		var aId = item.data('id');
		// Нужно ли скрывать строку и какую
		var aRemove = item.data('remove-parent');

		if (!$('#dataConfirmModal').length)
		{
			$('body').append('<div id="dataConfirmModal" class="modal fade custom-confirm"><div class="modal-body"><div class="modal-body-text"></div><div class="controls"><a class="btn btn-success" id="dataConfirmOK">'+aText.yes+'</a> <button class="btn" data-dismiss="modal">'+aText.no+'</button></div></div></div>');
		}
		$('#dataConfirmModal').find('.modal-body-text').text(item.attr('data-confirm'));
		$('#dataConfirmModal').modal({show:true});

		$('#dataConfirmOK').on('click', function(){
			$.ajax({
				url: aSetting.urlAdmin+"ajax.php?ajax_action="+aAction,
				type: "post",
				data: {id: aId},
				cache: false,
				dataType: "json",
				success: function(returnData)
				{
					// если что-то вернулось
					if (returnData)
					{
						$('#dataConfirmModal').modal('hide');
						$.jnotify(returnData.msg, returnData.type);

						// Скрываем строку, если нужно
						if (aRemove)
						{
							item.parents(aRemove).fadeOut(200);
						}

						// Перезагружаем страницу, если нужно
						if (aReload == 1)
						{
							setTimeout('window.location.reload()', aSetting.timeout);
						}
					}
				},
				error: function(returnData)
				{
					// если что-то вернулось
					if (returnData)
					{
						$.jnotify(returnData.msg, returnData.type);
					}
				}
			});
		});

		return false;
	});
});

/*
------------------------------------------------------
	Боковая колонка
------------------------------------------------------
*/
jQuery(function() {
	if ($.cookie('sidebar_collapse') == 1)
	{
		$("#sidebar").addClass("menu-min");
	}

	var menuMin = false;
	$("#sidebar-collapse").on("click", function () {

		if ($.cookie('sidebar_collapse') == 0 && !$("#sidebar").hasClass('menu-min'))
		{
			$.cookie('sidebar_collapse', '1');
			$("#sidebar").toggleClass("menu-min");
		}
		else
		{
			$.cookie('sidebar_collapse', '0');
			$("#sidebar").toggleClass("menu-min");
		}

		menuMin = $("#sidebar").hasClass("menu-min");
		if (menuMin) {
			$(".open > ul").removeClass("open")
		}
	});

	$(".nav-list .dropdown-toggle").each(function () {
		var nextItem = $(this).next().get(0);
		$(this).on("click", function () {
			if (menuMin) {
				return false;
			}
			$(".open > ul").each(function () {
				if (this != nextItem && !$(this.parentNode).hasClass("active")) {
					$(this).slideUp(200).parent().removeClass("open")
				}
			});
			$(nextItem).slideToggle(200).parent().toggleClass("open");
			return false;
		});
	});

	// Выделяем активные пункты меню
	$.activeMenuItems = function (el, url) {
		if (url === location) {
			url = url.pathname + url.search;
		}

		url = url.replace("/admin/", "");
		var search = url.split('?')[1] || '';

		if (search) {
			search = '?'+search;
		}

		$(el).find('.active').removeClass('active').end().find('a[href~="'+url+'"]').filter(function() {
			return this.search.split('&').shift() === search.split('&').shift();
		}).parents('li').addClass('active');
	};

	$.activeMenuItems("#mainMenu", location);
});

/*
------------------------------------------------------
	Сортировка меню
------------------------------------------------------
*/
jQuery(function() {
	var sortUl = $("ul#admin_menu_list").sortable({
		handle: 'i.icon-move',
		itemSelector: 'li',
		placeholder: '<li class="placeholder"/>',
		serialize: function ($parent, $children, parentIsContainer)
		{
			var result = $.extend({}, $parent.data());
			if (parentIsContainer)
				return $children;
			else if ($children[0]) 
				result.children = $children;
			delete result.sortable;
			delete result.subContainer;
			return result;
		},
		onDrop: function (item, container, _super)
		{
			var aPositions = sortUl.sortable("serialize").get();

			$.ajax({
				url: aSetting.urlAdmin+"ajax.php?ajax_action=menu_savePosition",
				type: "post",
				data: {positions: aPositions},
				cache: false,
				dataType: "json",
				success: function(returnData)
				{
					// если что-то вернулось
					if (returnData)
					{
						$.jnotify(returnData.msg, returnData.type);
					}
				},
				error: function(returnData)
				{
					// если что-то вернулось
					if (returnData)
					{
						$.jnotify(returnData.msg, returnData.type);
					}
				}
			});
			_super(item, container);
		}
	});
});

/*
------------------------------------------------------
	Загрузка доп. изображений
------------------------------------------------------
*/
jQuery(function(){

	$('#image_upload').click(function()
	{
		$('<input class="input-block-level" name="images[]" type="file" multiple>').appendTo('#images').focus().click();
	});

	$('#image_url').click(function()
	{
		$('<input class="input-block-level" name="images_urls[]" type="text" value="http://">').appendTo('#images').focus().select();
	});

	$(".productImages .delete input").live('click', function()
	{
		$(this).closest("li").toggleClass('selected');
	});

	$('#checkAllImages').on('click', function()
	{
		$('#tableImagesList input[type="checkbox"]').attr('checked', $('#tableImagesList input[type="checkbox"]:not(:checked)').length > 0);
		$('#tableImagesList input[type="checkbox"]').closest("li").toggleClass('selected');
	});

	$(".productImages a.delete_href").live('click', function()
	{
		$(this).closest("li").fadeOut(200, function() { $(this).remove(); });
		return false;
	});

	iNum = 5;
	iItem = 0;

	$('#image_auto').click(function()
	{
		productName = $('input[id=products_name]').val();

		$.ajax({
			type: 'post',
			url: aSetting.urlAdmin+"ajax.php?ajax_action=products_getImages",
			data: {product_name: productName, start: iItem},
			dataType: 'json',
			success: function(returnData)
			{
				if (returnData)
				{
					for(i = 0; i < Math.min(returnData.length, iNum); i++)
					{
						$('<li class="added_new_img">'+
						'<div class="title">'+
						'</div>'+
						'<span class="delete"><a href="javascript:;" class="delete_href"><i class="icon-trash"></i></a></span>'+
						'<div class="product-image-thumb">'+
						'<div class="product-image">'+
						'<a href="'+returnData[i]+'" target="_blank"><img onerror="$(this).closest("li").remove();" src="'+returnData[i]+'" /></a>'+
						'<input type="hidden" name="images_urls[]" value="'+returnData[i]+'">'+
						'</div>'+
						'</div>'+
						'</li>').appendTo('.productImages ul');
					}

					iItem += iNum;
				}
			}
		});
		return false;
	});
});

/*
------------------------------------------------------
	Загрузка описания доп. полей
------------------------------------------------------
*/
jQuery(function(){

	var cloneExtraFields = $('#addEf').clone(true),
		items = $('.table-big-list tr'),
		aItems = [];

	$('#addEf').remove().removeAttr('id');

	$('#add_new_ef').click(function() {
		$(cloneExtraFields).clone(true).appendTo('.add_ef').fadeIn('slow').find("input[name*=efName]").focus();
		return false;
	});

	$('#extraFieldsData').click(function() {

		productName = $('input[id=products_name]').val();

		$.ajax({
			type: 'post',
			url: aSetting.urlAdmin+"ajax.php?ajax_action=products_getEfInfo",
			data: {keyword: productName},
			dataType: 'json',
			success: function(returnData)
			{
				if (returnData)
				{
					var key;

					for (var i in returnData['options'])
					{
						key = $.inArray(returnData['options'][i]['name'], aItems);

						if (key > -1)
						{
							items.eq(key).find('input:text').val(returnData['options'][i]['value']);
						}
						else
						{
							f = $(cloneExtraFields).clone(true);
							f.find('input[name*=efName]').val(returnData['options'][i]['name']);
							f.find('input[name*=efValue]').val(returnData['options'][i]['value']);
							f.appendTo('.add_ef').fadeIn('slow');
						}
					}

				}
			}
		});

		items.each(function() {
			aItems.push($(this).find('.ef_name').text());
		});

		return false;
	});
});

/*
 ------------------------------------------------------
 Панели на главной
 ------------------------------------------------------
 */
jQuery(function(){
    jQuery('.admin-setting-actions').on('click', function()
    {
        // ID формы
        var sFormId = jQuery(this).closest('form').attr('id');
        var oForm = jQuery('#'+sFormId);
        // Какой экшон выполнять
        var sFormAction = oForm.data('action');
        // Группа настроек
        var sFormGroup = oForm.data('group');

        jQuery.ajax({
            type: "post",
            url: aSetting.urlAdmin+"ajax.php?ajax_action="+sFormAction,
            dataType: "json",
            data: oForm.serialize(),
            success: function(returnData) {
                // если что-то вернулось
                if (returnData)
                {
                    jQuery.each(returnData, function(name, val) {
                        if (val == 0)
                            $('.'+sFormGroup+'_'+name).css('display', 'none');
                        else if (val == 1)
                            $('.'+sFormGroup+'_'+name).css('display', 'block');
                    });
                }
            },
            error: function(returnData) {
                // если что-то вернулось
                if (returnData)
                {
                    // показываем уведомление, если оно есть
                    if (returnData.msg)
                    {
                        jQuery.jnotify(returnData.msg, returnData.type);
                    }
                }
            }
        });
    })
});