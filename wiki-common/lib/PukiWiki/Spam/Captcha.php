<?php
/**
 * Captchaクラス
 *
 * @package   PukiWiki\Spam
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/02/03
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Captcha.php,v 1.0.0 2013/02/02 17:28:00 Logue Exp $
 **/

namespace PukiWiki\Spam;

use PukiWiki\Render;
use PukiWiki\Spam\PostId;
use PukiWiki\Utility;
use ZendService\ReCaptcha\ReCaptcha;
use Zend\Captcha\Figlet;
use Zend\Captcha\Image;
use PukiWiki\Router;
use DirectoryIterator;
/**
 * Captcha認証クラス
 */
class Captcha{
	// CAPTCHAセッションの接頭辞（セッション名は、ticketに閲覧者のリモートホストを加えたもののmd5値とする）
	const CAPTCHA_SESSION_PREFIX = 'captcha-';
	// CAPTCHA認証済みセッションの有効期間
	const CAPTCHA_SESSION_EXPIRE = 60;	// 1分
	// CAPTCHA画像のフォント（GDを使用する場合）
	const CAPTCHA_IMAGE_FONT = 'fonts/Vera.ttf';
	// CAPTCHA画像の一時保存先（GDを使用する場合）
	const CAPTCHA_IMAGE_DIR_NAME = 'captcha/';
	// CAPTCHA認証の有効期間
	const CAPTCHA_TIMEOUT = 120;	// 2分間
	// CAPTCHA認証の入力文字数
	const CAPTCHA_WORD_LENGTH = 6;

	/**
	 * CAPTCHAチェック
	 * @param boolean $save セッションに保存するか
	 * @param string $message エラーメッセージの内容
	 */
	public static function check($save = true, $message = ''){
		global $recaptcha_public_key, $recaptcha_private_key, $vars, $session;

		// Captchaのセッション名（ticketとリモートホストの加算値。ticketはプログラマーから見てもわからない）
		$session_name = self::CAPTCHA_SESSION_PREFIX . md5(Utility::getTicket() . REMOTE_ADDR);

		if ($save && $session->offsetExists($session_name) && $session->offsetGet($session_name) === true){
			// CAPTCHA認証済みの場合
			// return array('msg'=>'CAPTCHA','body'=>'Your host was already to challenged.');
			return;
		}
		if (isset($recaptcha_public_key) && isset($recaptcha_private_key) ){
			// reCaptchaを使う場合
			$captcha = new ReCaptcha($recaptcha_public_key, $recaptcha_private_key);
			// 入力があった場合
			if ( isset($vars['recaptcha_challenge_field']) && isset($vars['recaptcha_response_field']) ){
				if ($captcha->verify($vars['recaptcha_challenge_field'], $vars['recaptcha_response_field']) ) {
					if ($save){
						// captcha認証済セッションを保存
						$session->offsetSet($session_name, true);
						// captcha認証済セッションの有効期間を設定
						$session->setExpirationSeconds($session_name, self::CAPTCHA_SESSION_EXPIRE);
					}
					// return array('msg'=>'CAPTCHA','body'=>'OK!');
					return;	// ここで書き込み処理に戻る
				}else{
					// CAPTCHA認証失敗ログをつける
					Utility::dump('captcha');
					$message = 'Failed to authenticate.';
				}
				// チャレンジ＆レスポンスデーターを削除
				unset($vars['recaptcha_challenge_field'], $vars['recaptcha_response_field']);
			}
			// 念のためcaptcha認証済みセッションを削除
			$session->offsetUnset($session_name);
			// reCaptchaの設定をオーバーライド
			$captcha->setOption('lang',substr(LANG,0,2));
			$captcha->setOption('theme','clean');
			$form = $captcha->getHTML();
		}else{
			// reCaptchaを使わない場合
			static $captcha_dir;
			if (empty($captcha_dir)){
				$captcha_dir = realpath(CACHE_DIR . self::CAPTCHA_IMAGE_DIR_NAME) . DIRECTORY_SEPARATOR;
			}
			
			if (isset($vars['challenge_field']) && isset($vars['response_field'] )){
				// Captchaチェック処理
				if ($session->offsetGet(self::CAPTCHA_SESSION_PREFIX . $vars['response_field']) === strtolower($vars['challenge_field'])) {
					if ($save){
						// captcha認証済セッションを保存
						$session->offsetSet($session_name, true);
						// captcha認証済セッションの有効期間を設定
						$session->setExpirationSeconds($session_name, self::CAPTCHA_SESSION_EXPIRE);
					}
					// 認証用セッションの削除
					$session->offsetUnset(self::CAPTCHA_SESSION_PREFIX . $vars['response_field']);
					if (file_exists($captcha_dir.$vars['response_field'].'.png')){
						// キャッシュ画像を削除
						unlink($captcha_dir.$vars['response_field'].'.png');
					}

					// return array('msg'=>'CAPTCHA','body'=>'OK!');
					return;	// ここで書き込み処理に戻る
				}else{
					// CAPTCHA認証失敗ログをつける
					Utility::dump('captcha');
					$message = 'Failed to authenticate.';
				}
				// チャレンジ＆レスポンスデーターを削除
				unset($vars['response_field'], $vars['challenge_field']);
			}
			// 念のためcaptcha認証済みセッションを削除
			$session->offsetUnset($session_name);
			if (extension_loaded('gd')) {
				// GDが使える場合、画像認証にする
				self::mkdir_r($captcha_dir);
				// 古い画像を削除する
				$di = new DirectoryIterator($captcha_dir );
				foreach ($di as $f){
					if (!$f->isFile()) continue;
					if (time() - $f->getMTime() > self::CAPTCHA_TIMEOUT) unlink($f->getRealPath());
				}
				
/*
				$handle = opendir($captcha_dir,null);
				if ($handle) {
					while( $entry = readdir($handle) ){
						if( $entry !== '.' && $entry !== '..'){
							$f = realpath($captcha_dir . $entry);
							if (time() - filectime($f) > self::CAPTCHA_TIMEOUT) unlink($f);
						}
					}
					closedir($handle);
				}
*/
				// 画像CAPTCHAを生成
				$captcha = new Image(array(
					'wordLen' => self::CAPTCHA_WORD_LENGTH,
					'timeout' => self::CAPTCHA_TIMEOUT,
					'font'	=> LIB_DIR . self::CAPTCHA_IMAGE_FONT,
					'ImgDir' => $di->getPath()
				));
				$captcha->generate();
				// cache_refプラグインを用いて画像を表示
				$form = '<img src="' . Router::get_cmd_uri('cache_ref', null,null,array('src'=> self::CAPTCHA_IMAGE_DIR_NAME . $captcha->getId().'.png')) . '" height="' . $captcha->getHeight() . '" width="' . $captcha->getWidth() . '" alt="' . Utility::htmlsc($captcha->getImgAlt()) . '" /><br />'."\n";	// 画像を取得
			}else{
				// GDがない場合アスキーアート
				$captcha = new Figlet(array(
					'wordLen' => self::CAPTCHA_WORD_LENGTH,
					'timeout' => self::CAPTCHA_TIMEOUT,
				));
				$captcha->generate();
				// ＼が￥に見えるのでフォントを明示的に指定。
				$form = '<pre style="font-family: Monaco, Menlo, Consolas, \'Courier New\' !important;">' .
					Utility::htmlsc($captcha->getFiglet()->render($captcha->getWord())) . '</pre>' . "\n" . '<br />' . "\n";	// AAを取得
			}
			// 識別子のセッション名
			$response_session = self::CAPTCHA_SESSION_PREFIX.$captcha->getId();
			// 識別子のセッションを発行
			$session->offsetSet($response_session, $captcha->getWord());
			// captchaの有効期間
			$session->setExpirationSeconds($response_session, self::CAPTCHA_TIMEOUT);
			$form .= '<input type="hidden" name="response_field" value="' . $captcha->getId() . '" />' . "\n";
			$form .= '<input type="text" class="form-control" name="challenge_field" maxlength="' . $captcha->getWordLen() . '" size="'.$captcha->getWordLen() . '" />';
			// $form .= $captcha->getWord();
		}
	//	$ret[] = $session->offsetExists($session_name) ? 'true' : 'false';
	//	$ret[] = Zend\Debug\Debug::Dump($vars);
	//	$ret[] = Zend\Debug\Debug::Dump($captcha->getSession());

		if (!empty($message)){
			$ret[] = '<div class="message_box ui-state-error ui-corner-all"><p><span class="ui-icon ui-icon-alert"></span>' . $message . '</p></div>';
		}

		// PostIdが有効な場合
		if ( isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] === 1){
			$vars['postid'] = PostId::generate($vars['cmd']);
		}

		$ret[] = '<fieldset>';
		$ret[] = '<legend>CAPTCHA</legend>';
		$ret[] = '<p>'.T_('Please enter the text that appears below.').'</p>';
		// フォームを出力
		$ret[] = '<form method="post" action="' . Router::get_script_uri() . '" method="post">';
		// ストアされている値を出力
		foreach ($vars as $key=>$value){
			$ret[] = !empty($value) ? '<input type="hidden" name="' . $key . '" value="' . Utility::htmlsc($value) . '" />' : null;
		}
		// CAPTCHAフォームを出力
		$ret[] = $form;
		$ret[] = '<input class="btn btn-primary" type="submit" value="submit"/>';
		$ret[] = '</form>';
		$ret[] = '</fieldset>';

		// return array('msg'=>'CAPTCHA','body'=>join("\n",$ret));
		new Render('CAPTCHA', join("\n",$ret));
		exit;
	}
	private static function mkdir_r($dirname){
		// 階層指定かつ親が存在しなければ再帰
		if (strpos($dirname, '/') && !file_exists(dirname($dirname))) {
			// 親でエラーになったら自分の処理はスキップ
			if (self::mkdir_r(dirname($dirname)) === false) return false;
		}
		//return mkdir($dirname);
	}
}