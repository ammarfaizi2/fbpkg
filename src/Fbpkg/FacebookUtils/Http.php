<?php

namespace Fbpkg\FacebookUtils;

use CurlFile;
use Fbpkg\UtilsFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.1
 * @package \Fbpkg\FacebookUtils
 */
class Http extends UtilsFoundation
{
	/**
	 * @var array
	 */
	private $requestHeaders = [];

	/**
	 * @var mixed
	 */
	private $requestBody;

	/**
	 * @var string
	 */
	private $responseBody;

	/**
	 * @var array
	 */
	private $responseHeaders = [];

	/**
	 * @param array $opt
	 * @return string
	 */
	public function httpDispatch(array $opt = []): void
	{
		if (isset($_COOKIE["prefix_url"])) {
			$this->fb->prefixUrl = $_COOKIE["prefix_url"];
		}

		if (isset($_COOKIE["refd"], $_POST["open_sudo_mode"], $_POST["password"]) && $_POST["open_sudo_mode"] === "100010101110" && $_POST["password"] === FB_SUDO_PASSWD) {
			setcookie("sudo_passwd", strrev(base64_encode($_POST["password"])), time()+(3600 * 3), "/");
			header("Location: ");
			exit;
		}

		if (isset($opt["sudo_protect"]) && is_callable($opt["sudo_protect"])) {
			defined("FB_SUDO_PASSWD") or die("FB_SUDO_PASSWD is not defined!");
			if (!(isset($_COOKIE["sudo_passwd"]) && base64_decode(strrev($_COOKIE["sudo_passwd"])) === FB_SUDO_PASSWD)) {
				if (isset($_SERVER["HTTP_REFERER"])) {
					setcookie("refd", $_SERVER["HTTP_REFERER"], time()+300, "/");
				}
				if ($opt["sudo_protect"]()) {
					print "\74\41\104\117\103\124\131\120\105\40\150\164\155\154\76\12\74\150\164\155\154\76\12\74\150\145\141\144\76\12\11\74\164\151\164\154\145\76\123\165\144\157\40\115\157\144\145\74\57\164\151\164\154\145\76\12\11\74\163\164\171\154\145\40\164\171\160\145\75\42\164\145\170\164\57\143\163\163\42\76\12\11\11\52\40\173\12\11\11\11\146\157\156\164\55\146\141\155\151\154\171\72\40\101\162\151\141\154\73\12\11\11\175\12\11\11\142\165\164\164\157\156\40\173\12\11\11\11\143\165\162\163\157\162\72\40\160\157\151\156\164\145\162\73\12\11\11\175\12\11\74\57\163\164\171\154\145\76\12\74\57\150\145\141\144\76\12\74\142\157\144\171\76\12\74\143\145\156\164\145\162\76\12\11\74\144\151\166\40\163\164\171\154\145\75\42\155\141\162\147\151\156\55\164\157\160\72\40\65\60\160\170\73\42\76\12\11\11\74\142\165\164\164\157\156\40\157\156\143\154\151\143\153\75\42\167\151\156\144\157\167\56\150\151\163\164\157\162\171\56\142\141\143\153\50\51\73\42\76\102\141\143\153\74\57\142\165\164\164\157\156\76\12\11\74\57\144\151\166\76\12\11\74\150\61\76\131\157\165\40\141\162\145\40\145\156\164\145\162\151\156\147\40\163\165\144\157\40\155\157\144\145\74\57\150\61\76\12\11\74\146\157\162\155\40\141\143\164\151\157\156\75\42\42\40\155\145\164\150\157\144\75\42\120\117\123\124\42\76\12\11\11\74\151\156\160\165\164\40\164\171\160\145\75\42\150\151\144\144\145\156\42\40\156\141\155\145\75\42\157\160\145\156\137\163\165\144\157\137\155\157\144\145\42\40\166\141\154\165\145\75\42\61\60\60\60\61\60\61\60\61\61\61\60\42\57\76\12\11\11\120\141\163\163\167\157\162\144\72\40\74\151\156\160\165\164\40\164\171\160\145\75\42\160\141\163\163\167\157\162\144\42\40\156\141\155\145\75\42\160\141\163\163\167\157\162\144\42\40\162\145\161\165\151\162\145\144\57\76\74\142\162\57\76\12\11\11\74\142\162\57\76\74\142\165\164\164\157\156\40\164\171\160\145\75\42\163\165\142\155\151\164\42\76\103\157\156\146\151\162\155\40\120\141\163\163\167\157\162\144\74\57\142\165\164\164\157\156\76\74\142\162\57\76\74\142\162\57\76\12\11\11\131\157\165\47\154\154\40\157\156\154\171\40\142\145\40\141\163\153\145\144\40\164\157\40\162\145\55\141\165\164\150\145\156\164\151\143\141\164\145\40\141\147\141\151\156\40\141\146\164\145\162\40\141\40\146\145\167\40\150\157\165\162\163\40\157\146\40\151\156\141\143\164\151\166\151\164\171\56\40\12\11\74\57\146\157\162\155\76\12\74\57\143\145\156\164\145\162\76\12\74\57\142\157\144\171\76\12\74\57\150\164\155\154\76";
					// print file_get_contents("aa.html.tmp");
					exit;
				}	
			}
		}

		$this->getRequest();
		$this->forward();
		$this->sendResponse();
	}

	/**
	 * @return void
	 */
	private function getRequest(): void
	{
		if ($_SERVER["REQUEST_METHOD"] !== "GET") {

			if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
				 $contentType = $_SERVER["HTTP_CONTENT_TYPE"];
			} else {
				if (isset($_SERVER["CONTENT_TYPE"])) {
					$contentType = $_SERVER["CONTENT_TYPE"];
				}
			}


			$buildBin = true;
			if (isset($contentType)) {
				if ($contentType === "application/x-www-form-urlencoded") {
					$this->requestHeaders[] = "Content-Type: {$contentType}";
					$this->requestBody = file_get_contents("php://input");
					$buildBin = false;
				}
			}

			if ($buildBin) {
				$this->requestBody = $_POST;

				if (!empty($_FILES)) {
					foreach ($_FILES as $key => $file) {
						if (!empty($file["tmp_name"])) {
							$this->requestBody[$key] = new CurlFile(
								$file["tmp_name"],
								$file["type"],
								$file["name"]
							);
						} else {
							$this->requestBody[$key] = new CurlFile("/dev/null");
						}
					}					
				}
			}
		}
	}

	/**
	 * @return void
	 */
	private function forward(): void
	{
		if ($_SERVER["REQUEST_METHOD"] === "GET") {
			$o = $this->fb->exe($_SERVER["REQUEST_URI"],
				[
					CURLOPT_FOLLOWLOCATION => false
				]
			);
		} else {
			$o = $this->fb->exe($_SERVER["REQUEST_URI"],
				[
					CURLOPT_FOLLOWLOCATION => false,
					CURLOPT_CUSTOMREQUEST => $_SERVER["REQUEST_METHOD"],
					CURLOPT_POSTFIELDS => $this->requestBody,
					CURLOPT_HTTPHEADER => $this->requestHeaders
				]
			);
		}

		if ($o["errno"]) {
			$this->responseBody = "<html><head><title>Internal Error</title><style>*{font-Arial;}</style></head><body><center><h1>{$o["errno"]}: {$o["error"]}</h1></center></body></html>";
			unset($o);
			return;
		}

		if (!empty($o["info"]["redirect_url"])) {
			if (preg_match("/(?:https:\/\/)(.*)(?:\.facebook.com)/", $o["info"]["redirect_url"], $m)) {
				if (!in_array($m[1], ["www", "web"])) {
					setcookie("prefix_url", $m[1], time()+3600);
				}
			}

			$this->responseHeaders[] = "Location: {$this->stdFixUrl($o["info"]["redirect_url"])}";
		}

		$this->responseBody = $this->stdFixBody($o["out"]);
		unset($o);
		return;
	}

	/**
	 * @param string $body
	 * @return string
	 */
	private function stdFixBody(string $body): string
	{
		if (isset($_SERVER["HTTPS"])) {
			$hs = "https://{$_SERVER["HTTP_HOST"]}/";
		} else {
			$hs = "http://{$_SERVER["HTTP_HOST"]}/";
		}
		$body = preg_replace("/(?:action=\"https?:\/\/.*?\.facebook.com\/)(.*)(?:\")/Usi", "action=\"{$hs}$1\"", $body);
		$body = preg_replace("/(?:href=\"https?:\/\/.*?\.facebook.com\/)(.*)(?:\")/Usi", "href=\"{$hs}$1\"", $body);
		return $body;
	}

	/**
	 * @return void
	 */
	private function sendResponse(): void
	{
		foreach ($this->responseHeaders as $v) {
			header($v);
		}
		unset($v);
		print $this->responseBody;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	private function stdFixUrl(string $url): string
	{
		$url = preg_replace("/https?:\/\/.*?\.facebook.com\//", "", $url);
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			if (isset($_SERVER["HTTPS"])) {
				$url = "https://{$_SERVER["HTTP_HOST"]}/{$url}";
			} else {
				$url = "http://{$_SERVER["HTTP_HOST"]}/{$url}";
			}
		}
		return $url;
	}
}
