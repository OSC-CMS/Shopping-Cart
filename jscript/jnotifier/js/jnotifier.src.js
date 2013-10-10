/*
Plugin: jNotifier
Version: 2.1
Author: Tarahonich Yuriy a.k.a. Sofcase
*/

//$.jnotify('текст', 'css стиль', {lifeTime: 4000, click: function () { /* код колбека */ }});

(function($) {
	$.jnotify = function(text, cssType, options) {
		var stackContainer, messageBox, cssType, messageBody, messageTextBox, closeButton, image;

		options = $.extend({
			lifeTime: 4000,
			click: undefined,
			close: undefined,
		}, options);

		// get stack container or create
		stackContainer = $('#notifier-box');
		if (!stackContainer.length) {
			stackContainer = $('<div>', {id: 'notifier-box'}).prependTo(document.body);
		}

		messageBox = $('<div>', {'class': 'message-box '+cssType, css: {display: 'none'}});
		messageBody = $('<div>', {'class': 'message-body'});
		messageTextBox = $('<span>', {html: text});

		closeButton = $('<a>', {
			'class': 'message-close',
			href: '#',
			title: 'Click for close this message',
			click: function() {
				event.preventDefault();
				$(this).parent().fadeOut(500, function() {
					$(this).remove();
				});
			}
		});

		messageBox.appendTo(stackContainer).fadeIn(500);
		closeButton.appendTo(messageBox);
		messageBody.appendTo(messageBox);

		messageTextBox.appendTo(messageBody);

		// set message life time
		if (options.lifeTime > 0) {
			setTimeout(function() {
				$(messageBox).fadeOut(500, function() {
					$(this).remove();
				});
			}, options.lifeTime);
		}

		// if callback for click is exists, then run
		if (typeof options.click != 'undefined') {
			messageBox.click(function(e) {
				if (!jQuery(e.target).is('.message-close')) {
					options.click.call(this);
				}
			});
		}

		// if callback for close is exists, then run
		if (typeof options.close != 'undefined') {
			messageBox.click(function(e) {
				if (jQuery(e.target).is('.message-close')) {
					options.close.call(this);
				}
			});
		}

		return this;
	}
})(jQuery);
