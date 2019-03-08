<?php

namespace Fbpkg;

use Fbpkg\Exceptions\FbpkgException;

defined("FBPKG_DIR") or die("FBPKG_DIR is not defined yet!\n");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.1
 * @package \Fbpkg
 */
final class Facebook
{
	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $cookieFile;

	/**
	 * @var string
	 */
	private $cookieDir;

	/**
	 * @param string $email
	 * @param string $password
	 * @param string $cookieFile
	 *
	 * Constructor.
	 */
	public function __construct(string $email, string $password, string $cookieFile = null)
	{
		if (is_null($cookieFile)) {
			$cookieFile = sha1($email);
		}

		$this->cookieDir = FBPKG_DIR."/cookies/";
		$this->cookieFile = "{$this->cookieDir}/{$cookieFile}";
		is_dir(FBPKG_DIR) or mkdir(FBPKG_DIR);
		is_dir($this->cookieDir) or mkdir($this->cookieDir);

		if (!is_dir(FBPKG_DIR)) {
			throw new FbpkgException(sprintf("Couldn't create FBPKG_DIR in: %s", FBPKG_DIR));
		}

		if (!is_dir($this->cookieDir)) {
			throw new FbpkgException(sprintf("Couldn't create cookie dir in: %s", $this->cookieDir));
		}

		if (!is_writable($this->cookieDir)) {
			throw new FbpkgException(sprintf("Cookie dir is not writeable in: %s", $this->cookieDir));
		}

		file_exists($this->cookieFile) or file_put_contents($this->cookieFile, "");

		if (!file_exists($this->cookieFile)) {
			throw new FbpkgException(sprintf("Couldn't create cookie file in: %s", $this->cookieFile));
		}

		if (!is_writable($this->cookieFile)) {
			throw new FbpkgException(sprintf("Cookie file is not writeable in: %s", $this->cookieFile));	
		}

		$this->email = $email;
		$this->password = $password;
	}
}
