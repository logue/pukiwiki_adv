<?php
// GS2 skin color settings
// WHITE

if (isset($_SKIN)){
	$_SKIN['ui_theme'] = 'overcast';			// jQuery UI Theme
}else{
	define('SKIN_CSS_CTS_BGCOLOR', 	'#FFFFFF');	// contents box bg
	define('SKIN_CSS_CTS_BGCOLOR2', '#e6e6e6');	// contents box bg2
	define('SKIN_CSS_CTS_BDCOLOR', 	'#A0A0A0');	// contents box border
	
	define('SKIN_CSS_BGCOLOR', 		'#FAFAFA');	// background
	define('SKIN_CSS_FGCOLOR', 		'#808080');	// foreground
	
	define('SKIN_CSS_BOX_BGCOLOR', 	'#F0F0FF');	// normal box bg
	define('SKIN_CSS_BOX_BDCOLOR', 	'#AAAAAA');	// normal box border
	
	define('SKIN_CSS_PRE_BGCOLOR', 	'#F0F0FF');	// pre bg
	define('SKIN_CSS_PRE_BDCOLOR', 	'#A0A0A0');	// pre border
	
	define('SKIN_CSS_H1_BGCOLOR', 	'#E0E0F0');	// heading1 bg
	define('SKIN_CSS_H2_BGCOLOR', 	'#EEEEFE');	// heading2 bg
	define('SKIN_CSS_H3_BGCOLOR', 	'#EEEEFE');	// heading3 bg
	define('SKIN_CSS_H4_BGCOLOR', 	'#EEEEFE');	// heading4 bg
	define('SKIN_CSS_H5_BGCOLOR', 	'#EEEEFE');	// heading5 bg
	define('SKIN_CSS_H6_BGCOLOR', 	'#EEEEFE');	// heading6 bg
	define('SKIN_CSS_H1_FGCOLOR', 	'#666666');	// heading1 fg
	define('SKIN_CSS_H2_FGCOLOR', 	'#666666');	// heading2 fg
	define('SKIN_CSS_H3_FGCOLOR', 	'#666666');	// heading3 fg
	define('SKIN_CSS_H4_FGCOLOR', 	'#666666');	// heading4 fg
	define('SKIN_CSS_H5_FGCOLOR', 	'#666666');	// heading5 fg
	define('SKIN_CSS_H6_FGCOLOR', 	'#666666');	// heading6 fg
	define('SKIN_CSS_H1_BDCOLOR', 	'#A9A9B9');	// heading1 border
	define('SKIN_CSS_H2_BDCOLOR', 	'#C0C0D0');	// heading2 border
	define('SKIN_CSS_H3_BDCOLOR', 	'#C9C9D9');	// heading3 border
	define('SKIN_CSS_H4_BDCOLOR', 	'#C9C9D9');	// heading4 border
	define('SKIN_CSS_H5_BDCOLOR', 	'#D0D0E0');	// heading5 border
	define('SKIN_CSS_H6_BDCOLOR', 	'#D0D0E0');	// heading6 border

	define('SKIN_CSS_A_LINK',	 	'#86BEDA');	// a link
	define('SKIN_CSS_A_VISITED', 	'#7AB7D6');	// a visited
	define('SKIN_CSS_A_HOVER',	 	'#66CC66');	// a hover
	define('SKIN_CSS_A_ACTIVE',	 	'#32865E');	// a active
}
?>
