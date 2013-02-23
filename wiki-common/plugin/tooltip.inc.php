<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: tooltip.inc.php,v 0.6.5 2011/02/05 12:45:00 Logue Exp $
//
/* 
*プラグイン tooltip
 ツールチップを表示

*Usage
 &tooltip(<term>);
 &tooltip(<term>){<glossary>};
// &tooltip(<term>,[<用語集>]);
// &tooltip(<term>,[<用語集>]){<glossary>};
 <term>にマウスカーソルを当てると、<glossary>が出現する。
*/
use PukiWiki\Factory;
use PukiWiki\Renderer\Inline\Glossary;
use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Utility;

//========================================================
function plugin_tooltip_init()
{
		$messages = array(
		'_tooltip_messages' => array(
			'page_glossary' => 'Glossary',
			'defaults' => array(
				'glossary'=> 'Glossary',
			),
		),
	);
	set_plugin_messages($messages);
}

///////////////////////////////////////
// Plus! ajax Glossary for UTF-8
function plugin_tooltip_action()
{
	global $vars;
	$term = $vars['q'];
	if (trim($term) == '') { exit; }
	
	$glossary = Glossary::getGlossary($term);
	$glossary_lastmod = Glossary::getGlossaryTime();	// なんども通信するのを防ぐためlastmodを出力
	if ($glossary == FALSE) { exit; }
	$s_glossary = RendererFactory::factory($glossary);
	
	
	pkwk_common_headers($glossary_lastmod);
	header('Content-type: text/xml');
	print '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	print $s_glossary;
	exit;
}

//========================================================
function plugin_tooltip_inline()
{
	$args = func_get_args();
	$glossary  = array_pop($args);
	$term      = array_shift($args);
//	$glossary_page = count($args) ? array_shift($args) : '';
	$glossary_page = '';

	if ( $glossary == '' ){
		$glossary = Glossary::getGlossary($term);
		// $debug .= "B=$glossary/";
		if ( $glossary === FALSE ) {
			$glossary = plugin_tooltip_get_page_title($term);
			if ( $glossary === FALSE ) $glossary = '';
		}
	}
	$s_glossary = Utility::htmlsc($glossary);

	$page = Utility::stripBracket($term);
		
	$wiki = Factory::Wiki($page);
	if ( $wiki->isValied() ) {
		return '<a href="' . $wiki->uri() . '"><abbr aria-describedby="tooltip" title="$s_glossary' . $wiki->passage(true,false). '">' . $term . '</abbr></a>';
	}
	return '<dfn aria-describedby="tooltip" title="' . $s_glossary . '">' . $term . '</dfn>';
}
//========================================================
function plugin_tooltip_get_page_title($term)
{
	$page = strip_bracket($term);
	if ( ! is_page($page) ) return FALSE;
	$src = get_source($page);
	$ct = 0;
	foreach ( $src as $line ) {
		if ( $ct ++ > 99 ) break;
		if ( preg_match('/^\*{1,3}(.*)\[#[A-Za-z][\w\-]+\].*$/', $line, $match) ){
			return trim($match[1]);
		}
		else if ( preg_match('/^\*{1,3}(.*)$/', $line, $match) ){
			return trim($match[1]);
		}
	}
	return FALSE;
}
/* End of file tooltip.inc.php */
/* Location: ./wiki-common/plugin/tooltip.inc.php */
