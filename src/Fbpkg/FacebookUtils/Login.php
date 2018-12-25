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
	/**
	 * @return string
	 */
	public function login(): string
	{
		$this->fb->exe("/login.php");
	}
}
