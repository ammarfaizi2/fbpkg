<?php

namespace Fbpkg\FacebookUtils;

use Fbpkg\UtilsFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.1
 * @package \Fbpkg\FacebookUtils
 */
class Login extends UtilsFoundation
{	
	public const NO_ACTION = "no_action";
	public const NO_FORM = "no_form";
	public const LOGIN_OK = "login_ok";
	public const LOGIN_FAILED = "login_failed";

	/**
	 * @return string
	 */
	public function login(): string
	{
		$o = $this->fb->exe("/login.php");
		// file_put_contents("a.tmp", $o["out"]);
		$ref = $o["info"]["url"];

		// $o["out"] = file_get_contents("a.tmp");
		// $ref = "https://m.facebook.com/login.php";

		if (!preg_match("/(?:<form.+action=\")(.*?)(?:\")/", $o["out"], $m)) {
			return self::NO_ACTION;
		}

		$actionUrl = efb($m[1]);
		$postData = [
			"email" => $this->fb->email,
			"pass" => $this->fb->password
		];

		if (preg_match_all("/(?:<input)([^><]+?type=\"hidden\"[^><]+?)(?:>)/", $o["out"], $m)) {
			unset($m[0]);
			foreach ($m[1] as $key => $v) {
				if (preg_match("/name=\"(.+?)\"/", $v, $mm)) {
					$postData[$key = efb(trim($mm[1]))] = "";
					if (preg_match("/value=\"(.*?)\"/", $v, $mm)) {
						$postData[$key] = efb(trim($mm[1]));
					}
				}
			}
			unset($m, $key, $v);
		} else {
			return self::NO_FORM;
		}

		unset($m, $key, $v, $mm, $o);

		$postData["login"] = "Login";

		$this->fb->exe($actionUrl,
			[
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query($postData),
				CURLOPT_HTTPHEADER => [
					"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
					"Accept-Encoding: gzip, deflate, br",
					"Accept-Language: en-US,en;q=0.5",
					"Content-Type: application/x-www-form-urlencoded",
					"DNT: 1",
					"Connection: keep-alive",
					"Upgrade-Insecure-Requests: 1"
				],
				CURLOPT_REFERER => $ref
			]
		);

		return "";
	}
}
