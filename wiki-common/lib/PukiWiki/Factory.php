<?php
/**
 * ファクトリークラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/03/02
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Factory.php,v 1.0.0 2013/03/23 09:30:00 Logue Exp $
 */

namespace PukiWiki;

/**
 * ファクトリークラス
 */
class Factory{
	public static function Wiki($page){
		return new Wiki($page);
	}
	public static function Backup($page){
		return new Backup($page);
	}
	public static function Referer($page){
		return new Referer($page);
	}
}
