/*
#####################################
#  OSC-CMS: Shopping Cart Software.
#  Copyright (c) 2011-2012
#  http://osc-cms.com
#  http://osc-cms.com/forum
#  Ver. 1.0.2
#####################################
*/

{
	steps: {
		  mlcom   : { exp: /\/\*[^*]*\*+(?:[^\/][^*]*\*+)*\// }
		, com     : { exp: /\/\/.*/ }
		, regexp  : { exp: /\/[^\/\\\n]*(?:\\.[^\/\\\n]*)*\/[gim]*/ }
		, string  : { exp: /(?:\'[^\'\\\n]*(?:\\.[^\'\\\n]*)*\')|(?:\"[^\"\\\n]*(?:\\.[^\"\\\n]*)*\")/ }
		, numbers : { exp: /\b[+-]?(?:\d*\.?\d+|\d+\.?\d*)(?:[eE][+-]?\d+)?\b/ }
		, keywords: { exp: /\b(arguments|break|case|catch|continue|default|delete|do|else|false|for|function|if|in|instanceof|new|null|return|switch|this|true|try|typeof|var|void|while|with)\b/ }
		, global  : { exp: /\b(toString|valueOf|window|self|element|prototype|constructor|document|escape|unescape|parseInt|parseFloat|setTimeout|clearTimeout|setInterval|clearInterval|NaN|isNaN|Infinity)\b/ }
	}
}