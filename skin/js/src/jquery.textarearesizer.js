/* 
	jQuery TextAreaResizer plugin
	Created on 17th January 2008 by Ryan O'Dell 
	Version 1.0.4
	
	Converted from Drupal -> textarea.js
	Found source: http://plugins.jquery.com/misc/textarea.js
	$Id: textarea.js,v 1.11.2.1 2007/04/18 02:41:19 drumm Exp $

	1.0.1 Updates to missing global 'var', added extra global variables, fixed multiple instances, improved iFrame support
	1.0.2 Updates according to textarea.focus
	1.0.3 Further updates including removing the textarea.focus and moving private variables to top
	1.0.4 Re-instated the blur/focus events, according to information supplied by dec

	Modified by Logue
	for IE font fix.
*/
(function(c){var h,i;var d=0;var a=32;var e;c.fn.TextAreaResizer=function(){return this.each(function(){h=c(this).addClass("processed"),i=null;c(this).wrap('<div class="resizable-textarea"><span></span></div>').parent().append(c('<div class="grippie"></div>').bind("mousedown",{el:this},b));var k=c("div.grippie",c(this).parent())[0];k.style.marginRight=(k.offsetWidth-c(this)[0].offsetWidth)+"px"})};function b(k){h=c(k.data.el);h.blur();d=j(k).y;i=h.height()-d;h.animate({opacity:"0.25"},{duration:"fast"});c(document).mousemove(g).mouseup(f);return false}function g(m){var k=j(m).y;var l=i+k;if(d>=(k)){l-=5}d=k;l=Math.max(a,l);h.height(l+"px");if(l<a){f(m)}return false}function f(k){c(document).unbind("mousemove",g).unbind("mouseup",f);h.css("opacity","");h.focus();h=null;i=null;d=0}function j(k){return{x:k.clientX+document.documentElement.scrollLeft,y:k.clientY+document.documentElement.scrollTop}}})(jQuery);

