<?php
// GS2 skin color settings
// YELLOW

if (isset($_SKIN)){
	$_SKIN['ui_theme'] = 'ui-lightness';			// jQuery UI Theme
}else{
	define('SKIN_CSS_CTS_BGCOLOR', 	'#FFFFEA');	// contents box bg
	define('SKIN_CSS_CTS_BDCOLOR', 	'#9F9F90');	// contents box border
	
	define('SKIN_CSS_BGCOLOR', 		'#EFEFDD');	// background
	define('SKIN_CSS_FGCOLOR', 		'#333320');	// foreground
	
	define('SKIN_CSS_BOX_BGCOLOR', 	'#E3E3CC');	// normal box bg
	define('SKIN_CSS_BOX_BDCOLOR', 	'#A8A899');	// normal box border
	
	define('SKIN_CSS_PRE_BGCOLOR', 	'#F0FFFF');	// pre bg
	define('SKIN_CSS_PRE_BDCOLOR', 	'#AFAFA0');	// pre border
	
	define('SKIN_CSS_H1_BGCOLOR', 	'#F5F5CF');	// heading1 bg
	define('SKIN_CSS_H2_BGCOLOR', 	'#F0F0CC');	// heading2 bg
	define('SKIN_CSS_H3_BGCOLOR', 	'#F0F0CC');	// heading3 bg
	define('SKIN_CSS_H4_BGCOLOR', 	'#F0F0CC');	// heading4 bg
	define('SKIN_CSS_H5_BGCOLOR', 	'#F0F0CC');	// heading5 bg
	define('SKIN_CSS_H6_BGCOLOR', 	'#F0F0CC');	// heading6 bg
	define('SKIN_CSS_H1_FGCOLOR', 	'#000000');	// heading1 fg
	define('SKIN_CSS_H2_FGCOLOR', 	'#000000');	// heading2 fg
	define('SKIN_CSS_H3_FGCOLOR', 	'#000000');	// heading3 fg
	define('SKIN_CSS_H4_FGCOLOR', 	'#000000');	// heading4 fg
	define('SKIN_CSS_H5_FGCOLOR', 	'#000000');	// heading5 fg
	define('SKIN_CSS_H6_FGCOLOR', 	'#000000');	// heading6 fg
	define('SKIN_CSS_H1_BDCOLOR', 	'#807766');	// heading1 border
	define('SKIN_CSS_H2_BDCOLOR', 	'#807766');	// heading2 border
	define('SKIN_CSS_H3_BDCOLOR', 	'#908776');	// heading3 border
	define('SKIN_CSS_H4_BDCOLOR', 	'#A09786');	// heading4 border
	define('SKIN_CSS_H5_BDCOLOR', 	'#B0A796');	// heading5 border
	define('SKIN_CSS_H6_BDCOLOR', 	'#B0A796');	// heading6 border

	define('SKIN_CSS_A_LINK',	 	'#BF8056');	// a link
	define('SKIN_CSS_A_VISITED', 	'#BF8F65');	// a visited
	define('SKIN_CSS_A_HOVER',	 	'#3E8D2B');	// a hover
	define('SKIN_CSS_A_ACTIVE',	 	'#325E86');	// a active
}
?>
