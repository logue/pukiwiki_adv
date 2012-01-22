<?php
// GS2 skin color settings
// VIOLET

if (isset($_SKIN)){
	$_SKIN['ui_theme'] = 'eggplant';			// jQuery UI Theme
}else{
	define('SKIN_CSS_CTS_BGCOLOR', 	'#F0F0FF');	// contents box bg
	define('SKIN_CSS_CTS_BDCOLOR', 	'#90909F');	// contents box border
	
	define('SKIN_CSS_BGCOLOR', 		'#E0E0EF');	// background
	define('SKIN_CSS_FGCOLOR', 		'#10101F');	// foreground
	
	define('SKIN_CSS_BOX_BGCOLOR', 	'#D0D0DF');	// normal box bg
	define('SKIN_CSS_BOX_BDCOLOR', 	'#9999A8');	// normal box border
	
	define('SKIN_CSS_PRE_BGCOLOR', 	'#F9F6F0');	// pre bg
	define('SKIN_CSS_PRE_BDCOLOR', 	'#A9A6A0');	// pre border
	
	define('SKIN_CSS_H1_BGCOLOR', 	'#C0C0CF');	// heading1 bg
	define('SKIN_CSS_H2_BGCOLOR', 	'#D0D0DF');	// heading2 bg
	define('SKIN_CSS_H3_BGCOLOR', 	'#D0D0DF');	// heading3 bg
	define('SKIN_CSS_H4_BGCOLOR', 	'#D0D0DF');	// heading4 bg
	define('SKIN_CSS_H5_BGCOLOR', 	'#D0D0DF');	// heading5 bg
	define('SKIN_CSS_H6_BGCOLOR', 	'#D0D0DF');	// heading6 bg
	define('SKIN_CSS_H1_FGCOLOR', 	'#00000F');	// heading1 fg
	define('SKIN_CSS_H2_FGCOLOR', 	'#00000F');	// heading2 fg
	define('SKIN_CSS_H3_FGCOLOR', 	'#00000F');	// heading3 fg
	define('SKIN_CSS_H4_FGCOLOR', 	'#00000F');	// heading4 fg
	define('SKIN_CSS_H5_FGCOLOR', 	'#00000F');	// heading5 fg
	define('SKIN_CSS_H6_FGCOLOR', 	'#00000F');	// heading6 fg
	define('SKIN_CSS_H1_BDCOLOR', 	'#666675');	// heading1 border
	define('SKIN_CSS_H2_BDCOLOR', 	'#666675');	// heading2 border
	define('SKIN_CSS_H3_BDCOLOR', 	'#777786');	// heading3 border
	define('SKIN_CSS_H4_BDCOLOR', 	'#888897');	// heading4 border
	define('SKIN_CSS_H5_BDCOLOR', 	'#9999A8');	// heading5 border
	define('SKIN_CSS_H6_BDCOLOR', 	'#9999A8');	// heading6 border

	define('SKIN_CSS_A_LINK',	 	'#2F5BB3');	// a link
	define('SKIN_CSS_A_VISITED', 	'#336AC9');	// a visited
	define('SKIN_CSS_A_HOVER',	 	'#8D3E2B');	// a hover
	define('SKIN_CSS_A_ACTIVE',	 	'#32865E');	// a active
}
?>
