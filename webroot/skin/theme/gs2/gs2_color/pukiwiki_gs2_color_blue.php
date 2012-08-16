<?php
// GS2 skin color settings
// BLUE

if (isset($_SKIN)){
	$_SKIN['ui_theme'] = 'redmond';		// jQuery UI Theme
}else{
	define('SKIN_CSS_CTS_BGCOLOR', 	'#F5FAFF');	// contents box bg
	define('SKIN_CSS_CTS_BGCOLOR2',	'#e5f2ff');	// contents box bg2
	define('SKIN_CSS_CTS_BDCOLOR', 	'#0064C8');	// contents box border
	
	define('SKIN_CSS_BGCOLOR', 		'#D6E1EC');	// background
	define('SKIN_CSS_FGCOLOR', 		'#060606');	// foreground
	
	define('SKIN_CSS_BOX_BGCOLOR', 	'#ADC3D9');	// normal box bg
	define('SKIN_CSS_BOX_BDCOLOR', 	'#769BC0');	// normal box border
	
	define('SKIN_CSS_PRE_BGCOLOR', 	'#EEEEEE');	// pre bg
	define('SKIN_CSS_PRE_BDCOLOR', 	'#888899');	// pre border
	
	define('SKIN_CSS_H1_BGCOLOR', 	'#7EA1C2');	// heading1 bg
	define('SKIN_CSS_H2_BGCOLOR', 	'#ADC3D9');	// heading2 bg
	define('SKIN_CSS_H3_BGCOLOR', 	'#ADC3D9');	// heading3 bg
	define('SKIN_CSS_H4_BGCOLOR', 	'#ADC3D9');	// heading4 bg
	define('SKIN_CSS_H5_BGCOLOR', 	'#ADC3D9');	// heading5 bg
	define('SKIN_CSS_H6_BGCOLOR', 	'#ADC3D9');	// heading6 bg
	define('SKIN_CSS_H1_FGCOLOR', 	'#00000F');	// heading1 fg
	define('SKIN_CSS_H2_FGCOLOR', 	'#00000F');	// heading2 fg
	define('SKIN_CSS_H3_FGCOLOR', 	'#00000F');	// heading3 fg
	define('SKIN_CSS_H4_FGCOLOR', 	'#00000F');	// heading4 fg
	define('SKIN_CSS_H5_FGCOLOR', 	'#00000F');	// heading5 fg
	define('SKIN_CSS_H6_FGCOLOR', 	'#00000F');	// heading6 fg
	define('SKIN_CSS_H1_BDCOLOR', 	'#2B3643');	// heading1 border
	define('SKIN_CSS_H2_BDCOLOR', 	'#384658');	// heading2 border
	define('SKIN_CSS_H3_BDCOLOR', 	'#46586E');	// heading3 border
	define('SKIN_CSS_H4_BDCOLOR', 	'#50667F');	// heading4 border
	define('SKIN_CSS_H5_BDCOLOR', 	'#60768F');	// heading5 border
	define('SKIN_CSS_H6_BDCOLOR', 	'#70869F');	// heading6 border

	define('SKIN_CSS_A_LINK',	 	'#215dc6');	// a link
	define('SKIN_CSS_A_VISITED', 	'#06358A');	// a visited
	define('SKIN_CSS_A_HOVER',	 	'#268671');	// a hover
	define('SKIN_CSS_A_ACTIVE',	 	'#c6215d');	// a active
}
?>
