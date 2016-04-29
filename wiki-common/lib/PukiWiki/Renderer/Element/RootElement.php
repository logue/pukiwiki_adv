<?php
/**
 * 基底要素クラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: RootElement.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Align;
use PukiWiki\Renderer\Element\Blockquote;
use PukiWiki\Renderer\Element\ContentsList;
use PukiWiki\Renderer\Element\Element;
use PukiWiki\Renderer\Element\ElementFactory;
use PukiWiki\Renderer\Element\HRule;
use PukiWiki\Renderer\Element\Heading;
use PukiWiki\Renderer\Element\InlineElement;
use PukiWiki\Renderer\Element\OList;
use PukiWiki\Renderer\Element\Pre;
use PukiWiki\Renderer\Element\SharpPre;
use PukiWiki\Renderer\Element\UList;
use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Text\Rules;
use PukiWiki\Utility;

/**
 * RootElement
 */
class RootElement extends Element
{
	const MULTILINE_DELIMITER = "\r";
	
	protected $id;
	protected $count = 0;
	protected $contents;
	protected $contents_last;
	protected $comments = array();

	public function __construct($id, $is_guiedit = false)
	{
		$this->id            = $id;
		$this->contents      = new Element();
		$this->contents_last = $this->contents;
		$this->is_guiedit    = $is_guiedit;
		parent::__construct();
	}

	public function parse($lines)
	{
		$this->last = & $this;
		$matches = array();

		while (! empty($lines)) {
			$line = array_shift($lines);

			// Escape comments
			if (substr($line, 0, 2) === '//'){
				if ($this->is_guiedit){
					$this->comments[] = substr($line, 2);
					$line = '___COMMENT___';
				}else{
					continue;
				}
			}

			// Extend TITLE by miko
			if (preg_match('/^(TITLE):(.*)$/',$line,$matches))
			{
				global $newtitle;
				static $newbase;
				if (!isset($newbase)) {
					$newbase = trim(Utility::stripHtmlTags(RendererFactory::factory($matches[2])));
					// For BugTrack/132.
					$newtitle = Utility::htmlsc($newbase);
				}
				continue;
			}

			if (preg_match('/^(LEFT|CENTER|RIGHT|JUSTIFY):(.*)$/', $line, $matches)) {
				// <div style="text-align:...">
				$this->last = $this->last->add(new Align(strtolower($matches[1])));
				if (empty($matches[2])) continue;
				$line = $matches[2];
			}

			$line = rtrim($line, "\t\r\n\0\x0B");	// スペース以外の空白文字をトリム

			// Empty
			if ( empty($line) ) {
				$this->last = & $this;
				continue;
			}

			// Horizontal Rule
			if (substr($line, 0, 4) == '----') {
				$this->insert(new HRule($this, $line));
				continue;
			}

			// Multiline-enabled block plugin #plugin{{ ... }}
			if (preg_match('/^#[^{]+(\{\{+)\s*$/', $line, $matches)) {
				$len = strlen($matches[1]);
				$line .= self::MULTILINE_DELIMITER;
				while (! empty($lines)) {
					$next_line = preg_replace('/['.self::MULTILINE_DELIMITER.'\n]*$/', '', array_shift($lines));
					if (preg_match('/\}{' . $len . '}/', $next_line)) {
						$line .= $next_line;
						break;
					} else {
						$line .= $next_line .= self::MULTILINE_DELIMITER;
					}
				}
			}

			// The first character
			$head = $line{0};

			// Heading
			if ($head === '*') {
				$this->insert(new Heading($this, $line));
				continue;
			}

			// Pre
			if ($head === ' ' || $head === "\t") {
				$this->last = $this->last->add(new Pre($this, $line));
				continue;
			}

			// CPre (Plus!)
			if (substr($line,0,2) === '# ' or substr($line,0,2) == "#\t") {
				$this->last = $this->last->add(new SharpPre($this,$line));
				continue;
			}

			// Line Break
			if (substr($line, -1) === '~')
				$line = substr($line, 0, -1) . "\r";

			// Other Character
			if (gettype($this->last) === 'object') {
				switch ($head) {
					case '-':
						$content = new UList($this, $line);
						break;
					case '+':
						$content = new OList($this, $line);
						break;
					case '>':
					case '<':
						$content = new Blockquote($this, $line);
						break;
					// ここからはファクトリークラスを通す
					case ':':
						$content = ElementFactory::factory('DList', $this, $line);
						break;
					case '|':
						$content = ElementFactory::factory('Table', $this, $line);
						break;
					case ',':
						$content = ElementFactory::factory('YTable', $this, $line);
						break;
					case '#':
						if ($this->is_guiedit){
							$content = ElementFactory::factory('PluginDummy', $this, $line);
						}else{
							$content = ElementFactory::factory('Plugin', $this, $line);
						}
						break;
					default:
						$content = ElementFactory::factory('InlineElement', null, $line);
						break;
				}

				// MathJax Expression
				if (PluginRenderer::hasPlugin('mathjax')){
					$end_mark = '';
					// 開始行によって終了行を判定する
					if (substr($line, 0, 2) === '\\[') {
						$end_mark = '\\]';
					} else if (substr($line, 0, 6) === '\\begin') {
						$end_mark = '\\end';
					}

					if ($end_mark) {
						while (! empty($lines)) {
							if (strpos($line, $end_mark) !== false) {
								break;
							}
							$next_line = preg_replace("/[\r\n]*$/", '', array_shift($lines)) ;
							$line .= "\n" . ' ' . $next_line;
						}
						$this->last  = $this->last->add(new BlockPlugin(array(null, 'mathjax', $line)));
						continue;
					}
				}

				// Default
				$this->last = $this->last->add($content);
				unset($content);
				continue;
			}
		}
	}

	public function getAnchor($text, $level)
	{
		global $top;
		global $fixed_heading_edited;	// Plus!

		// Heading id (auto-generated)
		$autoid = 'content_' . $this->id . '_' . $this->count;
		$this->count++;
		$anchor = '';

		// Heading id (specified by users)
		
		list($_text, $id, $level) = Rules::getHeading($text, false); // Cut fixed-anchor from $text
		
		//var_dump(Rules::getHeading($text, false));

		if (empty($id)) {
			// Not specified
			$id     = $autoid;
		} else if ($fixed_heading_edited){
			//$anchor = ' &aname(' . $id . ',super,full){' . $_symbol_anchor . '};';
			//if ($fixed_heading_edited) $anchor .= " &edit(,$id);";
			$anchor = ' &edit(,' . $id . ');';
		}
		//var_dump($text, $id, $level);

		// Add 'page contents' link to its heading
		$this->contents_last = $this->contents_last->add(new ContentsList($_text, $level, $id));

		// Add heding
		return array($_text . $anchor, $this->count > 1 ? "\n" . $top : '', $autoid);
	}

	public function insert(& $obj)
	{
		if ($obj instanceof InlineElement) $obj = $obj->toPara();
		return parent::insert($obj);
	}

	public function toString()
	{
		if ($this->is_guiedit) {
			$text = parent::toString();
			$text = preg_replace_callback("/___COMMENT___(\n___COMMENT___)*/", array(&$this, 'comment'), $text);
			return $text . "\n";
		}
		// #contents
		return preg_replace_callback('/<#_contents_>/', array($this, 'replaceContents'), parent::toString());
	}
	
	private function comment($matches)
	{
		$comments = explode("\n", $matches[0]);
		foreach ($comments as &$comment) {
			$comment = array_shift($this->comments);
		}
		$comment = join("\n", $comments);
		return '<span class="fa fa-comment" title="' . Utility::htmlsc($comment) . '"></span>';
	}

	private function replaceContents()
	{
		//var_dump($this->contents->toString());
		return '<div class="contents" id="contents_' . $this->id . '">' . "\n" .
			$this->contents->toString() .
			'</div>' . "\n";
	}
}

/* End of file RootElement.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/RootElement.php */