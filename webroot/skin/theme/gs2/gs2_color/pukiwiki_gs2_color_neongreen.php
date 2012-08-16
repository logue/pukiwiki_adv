<?php
// GS2 skin color settings
// NEONGREEN

if (isset($_SKIN)){
	$_SKIN['ui_theme'] = 'le-frog';			// jQuery UI Theme
}else{
	define('SKIN_CSS_CTS_BGCOLOR', 	'#000000');	// contents box bg
	define('SKIN_CSS_CTS_BGCOLOR2',	'#003300');	// contents box bg2
	define('SKIN_CSS_CTS_BDCOLOR', 	'#00AA00');	// contents box border
	
	define('SKIN_CSS_BGCOLOR', 		'#101010');	// background
	define('SKIN_CSS_FGCOLOR', 		'#F0F0F0');	// foreground
	
	define('SKIN_CSS_BOX_BGCOLOR', 	'#000000');	// normal box bg
	define('SKIN_CSS_BOX_BDCOLOR', 	'#009900');	// normal box border
	
	define('SKIN_CSS_PRE_BGCOLOR', 	'#404040');	// pre bg
	define('SKIN_CSS_PRE_BDCOLOR', 	'#888888');	// pre border
	
	define('SKIN_CSS_H1_BGCOLOR', 	'#000000');	// heading1 bg
	define('SKIN_CSS_H2_BGCOLOR', 	'#001100');	// heading2 bg
	define('SKIN_CSS_H3_BGCOLOR', 	'#001100');	// heading3 bg
	define('SKIN_CSS_H4_BGCOLOR', 	'#001100');	// heading4 bg
	define('SKIN_CSS_H5_BGCOLOR', 	'#001100');	// heading5 bg
	define('SKIN_CSS_H6_BGCOLOR', 	'#001100');	// heading6 bg
	define('SKIN_CSS_H1_FGCOLOR', 	'#00FF00');	// heading1 fg
	define('SKIN_CSS_H2_FGCOLOR', 	'#FFFFFF');	// heading2 fg
	define('SKIN_CSS_H3_FGCOLOR', 	'#FFFFFF');	// heading3 fg
	define('SKIN_CSS_H4_FGCOLOR', 	'#FFFFFF');	// heading4 fg
	define('SKIN_CSS_H5_FGCOLOR', 	'#FFFFFF');	// heading5 fg
	define('SKIN_CSS_H6_FGCOLOR', 	'#FFFFFF');	// heading6 fg
	define('SKIN_CSS_H1_BDCOLOR', 	'#99FF00');	// heading1 border
	define('SKIN_CSS_H2_BDCOLOR', 	'#8AE600');	// heading2 border
	define('SKIN_CSS_H3_BDCOLOR', 	'#8AE600');	// heading3 border
	define('SKIN_CSS_H4_BDCOLOR', 	'#8AE600');	// heading4 border
	define('SKIN_CSS_H5_BDCOLOR', 	'#8AE600');	// heading5 border
	define('SKIN_CSS_H6_BDCOLOR', 	'#8AE600');	// heading6 border

	define('SKIN_CSS_A_LINK',	 	'#99FF00');	// a link
	define('SKIN_CSS_A_VISITED', 	'#90F000');	// a visited
	define('SKIN_CSS_A_HOVER',	 	'#FF0000');	// a hover
	define('SKIN_CSS_A_ACTIVE',	 	'#F000CC');	// a active
}
?>
