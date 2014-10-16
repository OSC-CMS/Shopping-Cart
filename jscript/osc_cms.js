/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

jQuery(document).ready(function($){

	// Удаляем класс, если браузер поддерживает JS
	$('body').removeClass('no-js');

	// Подсвечивание выбранного пункта доставки или оплаты
	$('.selectMethodTable input[type=radio]').click(function()
	{
		$('.selectMethodTable input[type=radio]').parent().parent().filter('.selected').removeClass('selected');
		$(this).parent().parent().addClass('selected');
	});
	$('.selectMethodTable input[type=radio]').filter(':checked').parent().parent().addClass('selected');

	// Выбор страны
	$("#country").change(function()
	{
		var searchString = $(this).val();
		$.ajax({
			url: SITE_WEB_DIR+"index_ajax.php",
			dataType: "html",
			data: "ajax_page=loadStateXML&country_id="+searchString,
			type: "POST",
			success: function(msg)
			{
				$("#stateXML").html(msg);
			}
		});
	});

	// Search and Auto Completer
	$("#quick_find_keyword").autocomplete({
		serviceUrl: SITE_WEB_DIR+'index_ajax.php?ajax_page=autocompleter_search',
		minChars: 1,
		noCache: false,
		onSelect: function(value, data)
		{
			$("#quick_find_keyword").closest('form').submit();
		},
		fnFormatResult: function(value, data, currentValue)
		{
			var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
			var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
			return (data.products_image ? "<img align=\"absmiddle\" src='"+data.products_image+"'> " : '') + value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
		}
	});

	// Валидация форм
	$('.parsley-form').on('click', function()
	{
		// ID формы
		var formId = $('.parsley-form').closest('form').attr('id');

		// Проверка полей
		$('#'+formId).parsley({
			successClass: 'success',
			errorClass: 'error'
		});
	});

	// Обновление цены товара
	updateProductPrice();

	// Обновление страницы корзины
	if ($("#form_shopping_cart").length)
	{
		updateShoppingCart();
	}
});

// Пересчет цены товара + цена атрибутов + количество
function updateProductPrice()
{
	if ($("#cart_quantity").length)
	{
		$.ajax({
			url: SITE_WEB_DIR+"index_ajax.php?ajax_page=updateProductPrice",
			dataType: "json",
			data: $('#cart_quantity').serialize(),
			type: "POST",
			success: function(result)
			{
				if (result)
				{
					$("#productDisplayPrice").html(result);
				}
			}
		});
	}
	else
		return false;
}

// Пересчет цены товара в корзине
function updateShoppingCart()
{
	$("#mainContent").css('opacity', '0.5');
	$.ajax({
		url: SITE_WEB_DIR+"index_ajax.php?ajax_page=updateShoppingCart",
		dataType: "json",
		data: $('#form_shopping_cart').serialize(),
		type: "POST",
		success: function(result)
		{
			if (result)
			{
				$("#mainContent").html(result);
				$("#mainContent").css('opacity', '1');
			}
		}
	});
}

function func_qty_count_product(type)
{
	var countQty = $('#products_quantity');

	if (type == 'p')
		countQty.val(parseInt(countQty.val())+1);
	else if (type == 'm' && countQty.val() != 1)
		countQty.val(parseInt(countQty.val())-1);

	updateProductPrice();
}

// + и - количества товара в корзине
function func_qty_count(id, type, box)
{
	box = box || 0;

	var countQty = $('.sc_qty_'+id);

	if (type == 'p')
		countQty.val(parseInt(countQty.val())+1);
	else if (type == 'm' && countQty.val() != 1)
		countQty.val(parseInt(countQty.val())-1);

	if (box == 0)
		updateShoppingCart();
	else
		updateBoxCart();
}

// Reload Captcha Image
function reload_captcha()
{
	src = SITE_WEB_DIR+"captcha.php";
	document.captcha.src=SITE_WEB_DIR+'images/loading.gif';
	document.captcha.src=src+'?rand='+Math.random();
}

// Ajax Preloader
function js_preload(load)
{
	if (load == "hide")
		$("#js_preload").fadeOut(200);
	else
		$("#js_preload").fadeIn(400).show();
}