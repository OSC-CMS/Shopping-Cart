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
          mlcom : { exp: /\<!--(?:.|\n)*?--\>/ }
        , tag   : { exp: /(?:\<\!?[\w:]+)|(?:\>)|(?:\<\/[\w:]+\>)|(?:\/\>)/ }
		, php   : { exp: /(?:\<\?php\s)|(?:\<\?)|(?:\?\>)/ }
        , aname : { exp: /\s+?[\w-]+:?\w+(?=\s*=)/ }
        , avalue: { 
			  exp: /(=\s*)(([\"\'])(?:(?:[^\3\\]*?(?:\3\3|\\.))*?[^\3\\]*?)\3)/
			, replacement: '$1<span class="$0">$2</span>' }
        , entity: { exp: /&[\w#]+?;/ }
    }
}