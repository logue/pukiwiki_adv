<?php
/**
 * PukiWiki Advance 外部認証処理
 *
 * @copyright   Copyright &copy; 2007-2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: auth_api.cls.php,v 0.6 2008/06/02 01:40:00 upk Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */

namespace PukiWiki\Auth;

use Exception;
use PukiWiki\Auth\Auth;
use PukiWiki\Utility;
use PukiWiki\Router;
use Zend\Crypt\BlockCipher;

/**
 * 外部認証基底クラス
 */
abstract class AuthApi
{
	// セッションの接頭辞
	const SESSION_PREFIX = 'auth-';
	// auth_session_put    - auth_name, field_name, response
	// responce_xml_parser - response
	public $auth_name, $field_name, $response;

	protected $bc, $session_name;
	/**
	 * コンストラクタ
	 */
	public function __construct(){
		global $adminpass, $vars;

		if (!isset($this->auth_name)) throw new Exception('$this->auth_name has not set.');

		// コールバック先のページ
		$page = isset($vars['page']) ? $vars['page'] : null;
		// 管理人のパスワードのハッシュを暗号／復号のキーとする
		list(, $salt) = Auth::passwd_parse($adminpass);
		// 暗号化／復号化用
		$this->bc = BlockCipher::factory('mcrypt', array(
			'algo' => 'des',
			'mode' => 'cfb',
			'hash' => 'sha512',
			'salt' => $salt,
			'padding' => 2
		));
		// コールバック先のURL。通常プラグインのコールバックアドレスが返される
		$this->callbackUrl = Router::get_resolve_uri($this->auth_name ,$vars['page'],'full');
		// セッション名
		$this->session_name = self::SESSION_PREFIX.md5(Router::get_script_absuri().session_id());
	}
	/**
	 * ログイン用のリンクを取得
	 * @return string
	 */
	public function make_login_link(){
		return $this->callbackUrl;
	}
	/**
	 * 認証
	 * @param string $frob
	 * @return int
	 */
	public function auth($frob){}
	/**
	 * ユーザ情報を取得
	 * @param string $token トークン
	 * @return int
	 */
	public function get_userinfo($token){}
	/**
	 * セッションを取得
	 * @return array
	 */
	public function getSession(){
		global $session;
		// des化された内容を平文に戻す
		if ($session->offsetExists($this->session_name)) {
			return self::parseValue($this->bc->decrypt($session->offsetGet($this->session_name)));
		}
		return array();
	}
	/**
	 * セッションを設定
	 * @return void
	 */
	public function setSession()
	{
		global $session;
		$value = '';
		foreach(array_merge(array('api','ts'),$this->field_name) as $key) {
			$value .= (empty($value)) ? '' : '::'; // delm
			$value .= $key.'$$';
			switch($key) {
			case 'api':
				$value .= $this->auth_name;
				break;
			case 'ts':
				$value .= UTIME;
				break;
			default:
				$value .= Utility::encode($this->response[$key]);
			}
		}
		// 復号化
		$session->offsetSet($this->session_name, $this->bc->encrypt($value));

		// OpenID認証の場合のみ
		if ($this->auth_name != 'openid_verify') {
			log_write('login','');
		}
	}
	/**
	 * セッションを削除
	 */
	public function unsetSession()
	{
		global $session;
		$session->offsetUnset($this->session_name);
	}
	/**
	 * セッションの値をパース
	 * @param type $message
	 * @return type
	 */
	private static function parseValue($message)
	{
		$rc = array();
		$tmp = explode('::',trim($message));
		for($i=0; $i<count($tmp); $i++) {
			if ($tmp[$i]) {
				$tmp2 = explode('$$',$tmp[$i]);
				if ( isset($tmp2[1]) ) {
					$rc[$tmp2[0]] = decode($tmp2[1]);
				}
			}
		}
		return $rc;
	}
	/**
	 * レスポンスのXMLをパース
	 * @param string $data 入力データー
	 * @return void
	 */
	protected function responce_xml_parser($data)
	{
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $data, $val, $index);
		xml_parser_free($xml_parser);

		foreach($val as $x) {
			if ($x['type'] != 'complete') continue;
			$this->response[strtolower($x['tag'])] = $x['value'];
		}
	}
}

/* End of file AuthApi.php */
/* Location: ./vendor/PukiWiki/AuthApi.php */
