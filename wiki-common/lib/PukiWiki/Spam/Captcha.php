<?php
namespace PukiWiki\Spam;
use ZendService\ReCaptcha\ReCaptcha;
use PukiWiki\Utility;
use PukiWiki\File\File;

class Captcha{
	// CAPTCHAセッションの接頭辞（セッション名は、ticketに閲覧者のリモートホストを加えたもののmd5値とする）
	const CAPTCHA_SESSION_PREFIX = 'captcha-';
	// CAPTCHA認証済みセッションの有効期間
	const CAPTCHA_SESSION_EXPIRE = 3600;	// 1時間
	// CAPTCHA画像のフォント（GDを使用する場合）
	const CAPTCHA_IMAGE_FONT = 'Vera.ttf';
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
		$session_name = self::CAPTCHA_SESSION_PREFIX.md5(Utility::getTicket() . REMOTE_ADDR);

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
					self::write_challenged();
					$message = 'Failed to authenticate.';
				}
			}
			// 念のためcaptcha認証済みセッションを削除
			$session->offsetUnset($session_name);
			// reCaptchaの設定をオーバーライド
			$captcha->setOption('lang',substr(LANG,0,2));
			$captcha->setOption('theme','clean');
			$form = $captcha->getHTML();
		}else{
			// reCaptchaを使わない場合
			if (isset($vars['challenge_field']) && isset($vars['response_field'] )){
				// Captchaチェック処理
				if ($session->offsetGet(self::CAPTCHA_SESSION_PREFIX.$vars['response_field']) === strtolower($vars['challenge_field'])) {
					if ($save){
						// captcha認証済セッションを保存
						$session->offsetSet($session_name, true);
						// captcha認証済セッションの有効期間を設定
						$session->setExpirationSeconds($session_name, self::CAPTCHA_SESSION_EXPIRE);
					}
					// 認証用セッションの削除
					$session->offsetUnset(self::CAPTCHA_SESSION_PREFIX.$vars['response_field']);
					if (file_exists(self::CAPTCHA_IMAGE_CACHE_DIR.$vars['response_field'].'.png')){
						// キャッシュ画像を削除
						unlink(self::CAPTCHA_IMAGE_CACHE_DIR.$vars['response_field'].'.png');
					}

					// return array('msg'=>'CAPTCHA','body'=>'OK!');
					return;	// ここで書き込み処理に戻る
				}else{
					// CAPTCHA認証失敗ログをつける
					write_challenged();
					$message = 'Failed to authenticate.';
				}
			}
			// 念のためcaptcha認証済みセッションを削除
			$session->offsetUnset($session_name);
			if (extension_loaded('gd')) {
				// GDが使える場合、画像認証にする
				File::mkdir_r(CACHE_DIR . self::CAPTCHA_IMAGE_DIR_NAME);
				// 古い画像を削除する
				$handle = opendir(CACHE_DIR . self::CAPTCHA_IMAGE_DIR_NAME);
				if ($handle) {
					while( $entry = readdir($handle) ){
						if( $entry !== '.' && $entry !== '..'){
							$f = realpath(CACHE_DIR . self::CAPTCHA_IMAGE_DIR_NAME . $entry);
							if (time() - filectime($f) > self::CAPTCHA_TIMEOUT) unlink($f);
						}
					}
					closedir($handle);
				}
				$captcha = new Zend\Captcha\Image(array(
					'wordLen' => self::CAPTCHA_WORD_LENGTH,
					'timeout' => self::CAPTCHA_TIMEOUT,
					'font'	=> self::CAPTCHA_IMAGE_FONT,
					'ImgDir' => self::CAPTCHA_IMAGE_CACHE_DIR
				));
				$captcha->generate();
				// cache_refプラグインを用いて画像を表示
				$form = '<img src="'. get_cmd_uri('cache_ref', null,null,array('src'=>self::CAPTCHA_IMAGE_DIR_NAME.$captcha->getId().'.png')) . '" height="'.$captcha->getHeight().'" width="'.$captcha->getWidth().'" alt="'.$captcha->getImgAlt().'" /><br />'."\n";	// 画像を取得
			}else{
				// GDがない場合アスキーアート
				$captcha = new Zend\Captcha\Figlet(array(
					'wordLen' => self::CAPTCHA_WORD_LENGTH,
					'timeout' => self::CAPTCHA_TIMEOUT,
				));
				$captcha->generate();
				// ＼が￥に見えるのでフォントを明示的に指定。
				$form = '<pre style="font-family: Monaco, Menlo, Consolas, \'Courier New\' !important;">'.$captcha->getFiglet()->render($captcha->getWord()).'</pre>'."\n". '<br />'."\n";	// AAを取得
			}
			// 識別子のセッション名
			$response_session = self::CAPTCHA_SESSION_PREFIX.$captcha->getId();
			// 識別子のセッションを発行
			$session->offsetSet($response_session, $captcha->getWord());
			// captchaの有効期間
			$session->setExpirationSeconds($response_session, self::CAPTCHA_TIMEOUT);
			$form .= '<input type="hidden" name="response_field" value="'.$captcha->getId().'" />'."\n";
			$form .= '<input type="text" name="challenge_field" maxlength="'.$captcha->getWordLen().'" size="'.$captcha->getWordLen().'" />';
			// $form .= $captcha->getWord();
		}
	//	$ret[] = $session->offsetExists($session_name) ? 'true' : 'false';
	//	$ret[] = Zend\Debug\Debug::Dump($vars);
	//	$ret[] = Zend\Debug\Debug::Dump($captcha->getSession());


		if (!empty($message)){
			$ret[] = '<div class="message_box ui-state-error ui-corner-all"><p><span class="ui-icon ui-icon-alert"></span>'.$message.'</p></div>';
		}

		// PostIdが有効な場合
		if ( isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] === 1){
			$vars['postid'] = generate_postid($vars['cmd']);
		}

		$ret[] = '<fieldset>';
		$ret[] = '<legend>CAPTCHA</legend>';
		$ret[] = '<p>'.T_('Please enter the text that appears below.').'</p>';
		// フォームを出力
		$ret[] = '<form method="post" action="'.get_script_uri().'" method="post">';
		// ストアされている値を出力
		foreach ($vars as $key=>$value){
			$ret[] = !empty($value) ? '<input type="hidden" name="' . $key . '" value="' . htmlsc($value) . '" />' : null;
		}
		// CAPTCHAフォームを出力
		$ret[] = $form;
		$ret[] = '<input type="submit" />';
		$ret[] = '</form>';
		$ret[] = '</fieldset>';

		// return array('msg'=>'CAPTCHA','body'=>join("\n",$ret));
		catbody('CAPTCHA', $vars['page'], join("\n",$ret));
		exit;
	}
	/**
	 * CAPTCHA認証失敗したホストをログに保存
	 */
	private function write_challenged(){
		error_log(REMOTE_ADDR . "\t" . UTIME . "\t" . $_SERVER['HTTP_USER_AGENT'] . "\n", 3, CACHE_DIR . 'challenged.log');
	}
}