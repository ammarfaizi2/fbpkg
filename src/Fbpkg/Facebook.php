<?php

namespace Fbpkg;

use Fbpkg\FacebookUtils\Login;
use Fbpkg\Exceptions\FacebookException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.1
 * @package \Fbpkg
 */
final class Facebook
{
	private const USER_AGENT = "Mozilla/5.0 (X11; Linux x86_64; rv:63.0) Gecko/20100101 Firefox/63.0";

	/**
	 * @var string
	 */
	private $email;

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
	private $prefixUrl = "m";

	/**
	 * @param string $email
	 * @param string $password
	 * @param string $cookieFile
	 *
	 * Constructor.
	 */
	public function __construct(string $email, string $password, string $cookieFile = null)
	{
		$this->email = $email;
		$this->password = $password;

		if (!is_string($cookieFile)) {
			$cookieFile = sha1($email);
		}

		if (!file_exists($cookieFile)) {
			fclose(fopen($cookieFile, "w"));
		}

		if (!file_exists($cookieFile)) {
			throw new FacebookException("Cannot create cookie file: {$cookieFile}");
		}

		$cookieFile = realpath($cookieFile);
		$this->cookieFile = $cookieFile;
	}

	/**
	 * @param string $path
	 * @param array $opt
	 * @return array
	 */
	public function exe(string $path, array $opt = []): array
	{
		$url = "";

		if (filter_var($path, FILTER_VALIDATE_URL)) {
			$url = $path;
		} else {
			$path = ltrim($path, "/");
			$url = "https://{$this->prefixUrl}.facebook.com/{$path}";
		}

		unset($path);

		$ch = curl_init($url);
		$optf = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_USERAGENT => self::USER_AGENT,
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_COOKIEJAR => $this->cookieFile,
			CURLOPT_COOKIEFILE => $this->cookieFile
		];

		foreach ($opt as $key => $value) {
			$optf[$key] = $value;
		}
		
		unset($opt, $key, $value, $url);
		curl_setopt_array($ch, $optf);

		$out = curl_exec($ch);
		$info = curl_getinfo($ch);
		$error = curl_error($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		unset($ch);

		return [
			"out" => $out,
			"info" => $info,
			"error" => $error,
			"errno" => $errno
		];
	}

	/**
	 * @param \Fbpkg\UtilsFoundation $obj
	 * @param string $method
	 * @param array $parameters
	 * @return mixed
	 */
	private function safeCall(UtilsFoundation $obj, string &$method, array &$parameters)
	{
		return call_user_func_array([$obj, $method], $parameters);
	}

	/**
	 * @param string $method
	 * @param array $parameters
	 * @return mixed
	 */
	public function __call(string $method, array $parameters = [])
	{
		switch ($method) {
			case "login":
				$o = new Login($this);
				break;
			default:
				throw new FacebookException("Invalid method {$method}");
				break;
		}

		return $this->safeCall($o, $method, $parameters);
	}
}
