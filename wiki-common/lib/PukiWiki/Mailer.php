<?php
/**
 * メーラークラス
 *
 * @package   PukiWiki\Mailer
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013,2015 PukiWiki Advance Developers Team
 * @create    2013/03/15
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Mail.php,v 1.0.1 2015/03/09 00:31:00 Logue Exp $
 */
 
namespace PukiWiki;

use Zend\Mime;
use Zend\Mail;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp;

/**
 * メール送信関連のクラス
 */
class Mailer{
	/**
	 * 追加情報のセパレーター
	 */
	const SUMMRY_SEPARATOR = '------------------------------';
	
	/**
	 * 通知用のメールを作成する
	 * @param string $subject メールの表題
	 * @param string $message メールの内容
	 * @param array $summary 特に記載したい追加情報
	 * @param boolean $summary_position 追加情報を記載する位置
	 * @return void
	 */
	function notify($subject, $message, $summary = array(), $summary_position = FALSE){
		global $notify_from, $notify_to;
		if (empty($subject) || (empty($message) && empty($summary))) return FALSE;

		// Subject:
		if (isset($summary['PAGE'])) $subject = str_replace('$page', $summary['PAGE'], $subject);

		// Summary
		if (isset($summary['REMOTE_ADDR'])) $summary['REMOTE_ADDR'] = & $_SERVER['REMOTE_ADDR'];
		if (isset($summary['USER_AGENT']))
			$summary['USER_AGENT']  = '(' . UA_PROFILE . ') ' . UA_NAME . '/' . UA_VERS;

		if (! empty($summary)) {
			$_separator = empty($message) ? '' : self::SUMMRY_SEPARATOR . "\n";
			foreach($summary as $key => &$value) {
				$value = $key . ': ' . $value . "\n";
			}
			// Top or Bottom
			if ($summary_position) {
				$message = join('', $summary) . $_separator . "\n" . $message;
			} else {
				$message .= "\n" . $_separator . join('', $summary);
			}
			unset($summary);
		}
		self::send($notify_from, $notify_to, $subject, $message);
	}
	/**
	 * メールを送信する
	 * 参考：http://doremi.s206.xrea.com/zend/ref/zend_mail.html
	 * @param string $from 送信元のメールアドレス
	 * @param string $to 送信先のメールアドレス
	 * @param string $subject メールの表題
	 * @param string $body メールの内容
	 * @param string $form_label 重要度フラグなど
	 * @return void
	 */
	public static function send($from, $to, $subject, $body, $from_label=''){
		global $smtp_server;
		// mb_encode_mimeheader挙動にかかわる大事な指定
		mb_internal_encoding('JIS');
		
		$mail = new Mail('ISO-2022-JP');
		// 送信元および、名前
		$mail->setFrom($from, mb_encode_mimeheader(self::to_jis($from_label), 'JIS', 'B'));
		// 送信先
		$mail->addTo($to);
		// 長すぎる日本語件名を分割する
		$mail->setSubject(preg_replace('/\s+/', ' ', mb_encode_mimeheader(self::to_jis($subject), 'JIS', 'B')));
		// 返信先を自分に
		$mail->setReplyTo($from);
		// メールの内容
		$mail->setBodyText(self::to_jis($body), "ISO-2022-JP", Mime::ENCODING_7BIT);
		// エンコード
		$mail->setHeaderEncoding(Mime::ENCODING_BASE64);
		// 本文の文字コード
		$mail->addHeader('Content-Type', 'text/plain; charset=iso-2022-jp');
		// エラーなら自分に（不要ですが）
		$mail->addHeader('Errors-to', $from);
		// 先頭ビット使ってません
		$mail->addHeader('Content-Transfer-Encoding', '7bit');
		// メール送信者
		$mail->addHeader('X-Mailer', S_APPNAME . ' ' . S_VERSION);
		// STMPサーバーが指定されていない場合Sendmailでメールを送る
		if (empty($stmp_server)){
			// "-fﾒｱﾄﾞ"でReturn-Path設定
			$mail->send(new Sendmail('-f{'. $notify_from . '}'));
		}else{
			$mail->send(new Stmp($smtp_server));
		}
		unset($mail);
	}
	/**
	 * JISエンコードに変換
	 * @param string $s 入力文字列
	 * @return string
	 */
	private function to_jis($s) {
		return mb_convert_encoding($s, 'JIS', 'ASCII,JIS,UTF-8,CP51932,SJIS-win');
	}
}
