<?php
// GS2 skin color settings
// BLACK

if (isset($_SKIN)){
	$_SKIN['ui_theme'] = 'dark-hive';			// jQuery UI Theme
}else{
	define('SKIN_CSS_CTS_BGCOLOR', 	'#303030');	// contents box bg
	define('SKIN_CSS_CTS_BDCOLOR', 	'#999999');	// contents box border
	
	define('SKIN_CSS_BGCOLOR', 		'#202020');	// background
	define('SKIN_CSS_FGCOLOR', 		'#F0F0F0');	// foreground
	
	define('SKIN_CSS_BOX_BGCOLOR', 	'#101010');	// normal box bg
	define('SKIN_CSS_BOX_BDCOLOR', 	'#666666');	// normal box border
	
	define('SKIN_CSS_PRE_BGCOLOR', 	'#404040');	// pre bg
	define('SKIN_CSS_PRE_BDCOLOR', 	'#888888');	// pre border
	
	define('SKIN_CSS_H1_BGCOLOR', 	'#000000');	// heading1 bg
	define('SKIN_CSS_H2_BGCOLOR', 	'#444444');	// heading2 bg
	define('SKIN_CSS_H3_BGCOLOR', 	'#444444');	// heading3 bg
	define('SKIN_CSS_H4_BGCOLOR', 	'#444444');	// heading4 bg
	define('SKIN_CSS_H5_BGCOLOR', 	'#444444');	// heading5 bg
	define('SKIN_CSS_H6_BGCOLOR', 	'#444444');	// heading6 bg
	define('SKIN_CSS_H1_FGCOLOR', 	'#FFFFFF');	// heading1 fg
	define('SKIN_CSS_H2_FGCOLOR', 	'#FFFFFF');	// heading2 fg
	define('SKIN_CSS_H3_FGCOLOR', 	'#FFFFFF');	// heading3 fg
	define('SKIN_CSS_H4_FGCOLOR', 	'#FFFFFF');	// heading4 fg
	define('SKIN_CSS_H5_FGCOLOR', 	'#FFFFFF');	// heading5 fg
	define('SKIN_CSS_H6_FGCOLOR', 	'#FFFFFF');	// heading6 fg
	define('SKIN_CSS_H1_BDCOLOR', 	'#777777');	// heading1 border
	define('SKIN_CSS_H2_BDCOLOR', 	'#777777');	// heading2 border
	define('SKIN_CSS_H3_BDCOLOR', 	'#666666');	// heading3 border
	define('SKIN_CSS_H4_BDCOLOR', 	'#555555');	// heading4 border
	define('SKIN_CSS_H5_BDCOLOR', 	'#444444');	// heading5 border
	define('SKIN_CSS_H6_BDCOLOR', 	'#444444');	// heading6 border

	define('SKIN_CSS_A_LINK',	 	'#5F8BD3');	// a link
	define('SKIN_CSS_A_VISITED', 	'#639AE9');	// a visited
	define('SKIN_CSS_A_HOVER',	 	'#9D4E3B');	// a hover
	define('SKIN_CSS_A_ACTIVE',	 	'#42966E');	// a active
}
?>
