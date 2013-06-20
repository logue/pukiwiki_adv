<?php

namespace PukiWiki\Spam;

use PukiWiki\Utility;
use PukiWiki\Router;

class Akismet{
	const DEFAULT_USER_NAME = 'Anonymous';
	public static function check($postdata){
		global $akismet_api_key;
		$akismet = new ZendService\Akismet(
			$akismet_api_key,
			Router::get_script_absuri()
		);
		if ($akismet->verifyKey($akismet_api_key)) {
			// 送信するデーターをセット
			$akismet_post = array(
				'user_ip' => Utility::getRemoteIp(),
				'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null,
				'comment_type' => 'comment',
				'comment_author' => isset($vars['name']) ? $vars['name'] : self::DEFAULT_USER_NAME,
			);
			if ($use_spam_check['akismet'] === 2){
				$akismet_post['comment_content'] = $postdata;
			}else{
				// 差分のみをAkismetに渡す
				$akismet_post['comment_content'] = $addedata;
			}

			if($akismet->isSpam($akismet_post)){
				
				Utility::dieMessage('Writing was limited by Akismet (Blocking SPAM).', $_title['prohibit'], 400);
			}
		}else{
			Utility::dieMessage('Akismet API key does not valied.', 500);
		}
	}
}