<?php
/**
 * レンダラー定義クラス
 *
 * @package   PukiWiki\Lib\Renderer
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: RendererDefines.php,v 1.0.0 2013/02/01 19:54:00 Logue Exp $
 */
namespace PukiWiki\Lib\Renderer;

class RendererDefines{
	// InterWikiName
	const INTERWIKINAME_PATTERN = '(\[\[)?((?:(?!\s|:|\]\]).)+):(.+)(?(1)\]\])';
	// WikiName
	const WIKINAME_PATTERN ='(?:[A-Z][a-z]+){2,}(?!\w)';
	// \c3\9f through \c3\bf correspond to \df through \ff in ISO8859-1
	//const WIKINAME_PATTERN = '(?:[A-Z](?:[a-z]|\\xc3[\\x9f-\\xbf])+(?:[A-Z](?:[a-z]|\\xc3[\\x9f-\\xbf])+)+)(?!\w)';
	// BracketName
	const BRACKETNAME_PATTERN = '(?!\s):?[^\r\n\t\f\[\]<>#&":]+:?(?<!\s)';
	// Note
	const NOTE_PATTERN = '\(\(((?:(?>(?:(?!\(\()(?!\)\)(?:[^\)]|$)).)+)|(?R))*)\)\)';
}

/* End of file RendererDefines.php */
/* Location: ./vender/PukiWiki/Lib/Renderer/RendererDefines.php */