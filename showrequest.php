<?php 

class SnowRequest {
	const NUM_REQUESTS = 3;

	protected $task = array();

	public function __construct($urls) {
		$this->initUrls($urls);
		$this->initCurl();
	}

	private function initUrls($urls) {
		foreach (array_unique($urls) as $url) {
			$this->task[$url] = array();
		}
	}

	private function initCurl() {
		return true;
	}

	public function processAllUrls() {
		foreach (array_keys($this->task) as $url) {
			print($url);
			$mess = "\n\t" . implode("\n\t", self::processUrl($url)) . "\n";
			print($mess);
		}
	}

	public static function processUrl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$count = 0;
		$redirect = false;
		$curUrl = $url;
		$result = array();

		do {
			curl_setopt($ch, CURLOPT_URL, $curUrl);
			$html = curl_exec($ch);
			$info = curl_getinfo($ch);

			if ($redirect = self::isRedirect($info)) {
				$result[] = $info['http_code'] . ' ' . $info['redirect_url'];
				$curUrl = $info['redirect_url'];
			}
			else {
				$result[] = $info['http_code'];
			}

		} while ($redirect && $count++ < self::NUM_REQUESTS);

		curl_close($ch);
		return $result;

	}

	private static function isRedirect($info) {
		return $info['http_code'] == 301 || $info['http_code'] == 302;
	}
}

$urls = array(
	'https://google.com',
	'https://ya.ru',
);

$sr = new SnowRequest($urls);
$sr->processAllUrls();
