<?php

require __DIR__."/vendor/autoload.php";
require __DIR__."/credential.php";

$cookieFile = __DIR__."/cookie.tmp";

$st = new Facebook($user, $pass, $cookieFile);