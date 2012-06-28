/*
*---------------------------------------------------------
*
*	OSC-CMS - Open Source Shopping Cart Software
*	http://osc-cms.com
*
*---------------------------------------------------------
*/

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

// Simple Message
(function($)
{//$.jmessage('Text', 3000, 'cssStyle');//test_message
	$.jmessage = function(message, lifetime, class_name)
	{
		var stack_box = $('#jm_stack_box');

		if (!$(stack_box).length)
		{
			stack_box = $('<div id="jm_stack_box"></div>').prependTo(document.body);
		}

		var message_box = $('<div class="jm_message ' + class_name + '">' + message + '</div>');

		$(message_box).css('opacity', 0).appendTo('#jm_stack_box').animate({opacity: 1}, 300);

		$(message_box).click(function()
		{
			$(this).animate({opacity: 0}, 300, function()
			{
				$(this).remove();
			});
		});

		if ((lifetime = parseInt(lifetime)) > 0)
		{
			setTimeout(function()
			{
				$(message_box).animate({opacity: 0}, 300, function()
				{
					$(this).remove();
				});
			}, lifetime);
		}
	};
})(jQuery);