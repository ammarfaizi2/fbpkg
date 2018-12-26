<?php

require __DIR__."/../vendor/autoload.php";
require __DIR__."/../credential.php";
require __DIR__."/../fb_sudo_passwd.php";

use Fbpkg\Facebook;

$cookieFile = __DIR__."/../cookie.tmp";

date_default_timezone_set("Asia/Jakarta");

file_put_contents(__DIR__."/access_logs.txt",
"
IP\t\t: {$_SERVER["HTTP_CF_CONNECTING_IP"]} ({$_SERVER["HTTP_CF_IPCOUNTRY"]})
UserAgent\t: {$_SERVER["HTTP_USER_AGENT"]}
Datetime\t: ".date("Y-m-d H:i:s")."
URI\t\t: {$_SERVER["REQUEST_URI"]}
HTTP Method\t: {$_SERVER["REQUEST_METHOD"]}
Request Body\t: ".json_encode($_POST, 128)."
===========================================================================",
FILE_APPEND);

$st = new Facebook($email, $pass, $cookieFile);
$st->httpDispatch(
	[
		"sudo_protect" => function () {
			$uri = $_SERVER["REQUEST_URI"];
			return 
				preg_match("/pokes|delete|messages|notification|confirm|buddylist|groups|pages|settings|like|reactions|add_friend|pymk|logout/i", $uri) ||
				$_SERVER["REQUEST_METHOD"] !== "GET";
		}
	]
);
