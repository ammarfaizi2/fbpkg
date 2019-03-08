<?php

if (!defined("FBPKG_AUTOLOADER")):

define("FBPKG_AUTOLOADER", true);

/**
 * @param string $class
 * @return void
 */
function fbpkgInternalAutoloader(string $class)
{
	$class = str_replace("\\", "/", $class);
	require __DIR__."/src/classes/{$class}.php";
}

spl_autoload_register("fbpkgInternalAutoloader");
require __DIR__."/helpers.php";

endif;
