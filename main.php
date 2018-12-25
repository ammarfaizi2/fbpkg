<?php

require __DIR__."/vendor/autoload.php";
require __DIR__."/credential.php";

use Fbpkg\Facebook;

$cookieFile = __DIR__."/cookie.tmp";

$st = new Facebook($email, $pass, $cookieFile);
var_dump($st->login());

