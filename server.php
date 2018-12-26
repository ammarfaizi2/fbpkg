<?php

require __DIR__."/vendor/autoload.php";
require __DIR__."/credential.php";
require __DIR__."/fb_sudo_passwd.php";

use Fbpkg\Facebook;

$cookieFile = __DIR__."/cookie.tmp";

$st = new Facebook($email, $pass, $cookieFile);
$st->httpDispatch(
	[
		"sudo_protect" => function () {
			$uri = $_SERVER["REQUEST_URI"];
			return 
				preg_match("/messages|notification|confirm|buddylist|groups|pages|settings|like|reactions|add_friend|pymk|logout/i", $uri) ||
				$_SERVER["REQUEST_METHOD"] !== "GET";
		}
	]
);
