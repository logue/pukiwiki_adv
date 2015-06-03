<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: tooltip.inc.php,v 0.6.6 2015/06/02 00:19:00 Logue Exp $
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
use PukiWiki\Renderer\Header;
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
	$term = isset($vars['q']) ? trim($vars['q']) : null;
	if (empty($term)) { exit; }
	$glossary = Glossary::getGlossary($term);
	$glossary_lastmod = Glossary::getGlossaryTime();	// なんども通信するのを防ぐためlastmodを出力
	if ($glossary == FALSE) { exit; }
	$s_glossary = RendererFactory::factory($glossary);
	
	Header::writeResponse(Header::getHeaders('text/xml',$glossary_lastmod), 200, '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .$s_glossary);
}

//========================================================
function plugin_tooltip_inline($args)
{
	$args = func_get_args();
	$glossary  = array_pop($args);
	$term      = array_shift($args);

	if (empty($glossary)){
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
	if ( $wiki->isValied() && $wiki->isReadable()) {
		return '<abbr class="glossary" title="' . $s_glossary . ' ' . $wiki->passage(false,false) . '"><a href="' . $wiki->uri() . '">' . $term . '</a></abbr>';
	}

	return '<abbr title="' . $s_glossary . '">' . $term . '</abbr>';
}
//========================================================
function plugin_tooltip_get_page_title($term)
{
	$page = strip_bracket($term);
	$wiki = Factory::Wiki($page);
	
	if ( ! $wiki->has($page) ) return FALSE;
	
	$ct = 0;
	foreach ( $wiki->get() as $line ) {
		if ( $ct ++ > 99 ) break;
		if ( preg_match('/^\*{1,3}(.*)\[#[A-Za-z0-9][\w\-]+\].*$/', $line, $match) ){
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
