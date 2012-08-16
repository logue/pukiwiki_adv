<?php
// GS2 skin color settings
// WHITE

if (isset($_SKIN)){
	$_SKIN['ui_theme'] = 'smoothness';			// jQuery UI Theme
}else{
	define('SKIN_CSS_CTS_BGCOLOR', 	'#FAFAFA');	// contents box bg
	define('SKIN_CSS_CTS_BGCOLOR2', '#e6e6e6');	// contents box bg2
	define('SKIN_CSS_CTS_BDCOLOR', 	'#909090');	// contents box border
	
	define('SKIN_CSS_BGCOLOR', 		'#FFFFFF');	// background
	define('SKIN_CSS_FGCOLOR', 		'#101010');	// foreground
	
	define('SKIN_CSS_BOX_BGCOLOR', 	'#F0F0F0');	// normal box bg
	define('SKIN_CSS_BOX_BDCOLOR', 	'#999999');	// normal box border
	
	define('SKIN_CSS_PRE_BGCOLOR', 	'#F0F0FF');	// pre bg
	define('SKIN_CSS_PRE_BDCOLOR', 	'#A0A0A0');	// pre border
	
	define('SKIN_CSS_H1_BGCOLOR', 	'#FFFFFF');	// heading1 bg
	define('SKIN_CSS_H2_BGCOLOR', 	'#E0E0E0');	// heading2 bg
	define('SKIN_CSS_H3_BGCOLOR', 	'#E0E0E0');	// heading3 bg
	define('SKIN_CSS_H4_BGCOLOR', 	'#E0E0E0');	// heading4 bg
	define('SKIN_CSS_H5_BGCOLOR', 	'#E0E0E0');	// heading5 bg
	define('SKIN_CSS_H6_BGCOLOR', 	'#E0E0E0');	// heading6 bg
	define('SKIN_CSS_H1_FGCOLOR', 	'#000000');	// heading1 fg
	define('SKIN_CSS_H2_FGCOLOR', 	'#000000');	// heading2 fg
	define('SKIN_CSS_H3_FGCOLOR', 	'#000000');	// heading3 fg
	define('SKIN_CSS_H4_FGCOLOR', 	'#000000');	// heading4 fg
	define('SKIN_CSS_H5_FGCOLOR', 	'#000000');	// heading5 fg
	define('SKIN_CSS_H6_FGCOLOR', 	'#000000');	// heading6 fg
	define('SKIN_CSS_H1_BDCOLOR', 	'#333333');	// heading1 border
	define('SKIN_CSS_H2_BDCOLOR', 	'#444444');	// heading2 border
	define('SKIN_CSS_H3_BDCOLOR', 	'#444444');	// heading3 border
	define('SKIN_CSS_H4_BDCOLOR', 	'#555555');	// heading4 border
	define('SKIN_CSS_H5_BDCOLOR', 	'#666666');	// heading5 border
	define('SKIN_CSS_H6_BDCOLOR', 	'#666666');	// heading6 border

	define('SKIN_CSS_A_LINK',	 	'#2F5BB3');	// a link
	define('SKIN_CSS_A_VISITED', 	'#336AC9');	// a visited
	define('SKIN_CSS_A_HOVER',	 	'#8D3E2B');	// a hover
	define('SKIN_CSS_A_ACTIVE',	 	'#32865E');	// a active
}
?>
