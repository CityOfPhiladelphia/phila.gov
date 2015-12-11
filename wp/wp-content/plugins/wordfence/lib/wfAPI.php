<?php
require_once('wordfenceConstants.php');
require_once('wordfenceClass.php');

class wfAPI {
	public $lastHTTPStatus = '';
	public $lastCurlErrorNo = '';
	private $curlContent = 0;
	private $APIKey = '';
	private $wordpressVersion = '';

	public function __construct($apiKey, $wordpressVersion) {
		$this->APIKey = $apiKey;
		$this->wordpressVersion = $wordpressVersion;
	}

	public function getStaticURL($url) { // In the form '/something.bin' without quotes
		return $this->getURL($this->getAPIURL() . $url);
	}

	public function call($action, $getParams = array(), $postParams = array(), $forceSSL = false) {
		$apiURL = $this->getAPIURL();
		//Sanity check. Developer should call wfAPI::SSLEnabled() to check if SSL is enabled before forcing SSL and return a user friendly msg if it's not.
		if ($forceSSL && (!preg_match('/^https:/i', $apiURL))) {
			//User's should never see this message unless we aren't calling SSLEnabled() to check if SSL is enabled before using call() with forceSSL
			throw new Exception("SSL is not supported by your web server and is required to use this function. Please ask your hosting provider or site admin to install cURL with openSSL to use this feature.");
		}
		$json = $this->getURL($apiURL . '/v' . WORDFENCE_API_VERSION . '/?' . $this->makeAPIQueryString() . '&' . self::buildQuery(
				array_merge(
					array('action' => $action),
					$getParams
				)), $postParams);
		if (!$json) {
			throw new Exception("We received an empty data response from the Wordfence scanning servers when calling the '$action' function.");
		}

		$dat = json_decode($json, true);
		if (isset($dat['_isPaidKey'])) {
			wfConfig::set('keyExpDays', $dat['_keyExpDays']);
			if ($dat['_keyExpDays'] > -1) {
				wfConfig::set('isPaid', 1);
			} else if ($dat['_keyExpDays'] < 0) {
				wfConfig::set('isPaid', '');
			}
		}

		if (!is_array($dat)) {
			throw new Exception("We received a data structure that is not the expected array when contacting the Wordfence scanning servers and calling the '$action' function.");
		}
		if (is_array($dat) && isset($dat['errorMsg'])) {
			throw new Exception($dat['errorMsg']);
		}
		return $dat;
	}

	protected function getURL($url, $postParams = array()) {
		wordfence::status(4, 'info', "Calling Wordfence API v" . WORDFENCE_API_VERSION . ":" . $url);

		if (!function_exists('wp_remote_post')) {
			require_once ABSPATH . WPINC . 'http.php';
		}

		$ssl_verify = (bool) wfConfig::get('ssl_verify');
		$args = array(
			'timeout'    => 900,
			'user-agent' => "Wordfence.com UA " . (defined('WORDFENCE_VERSION') ? WORDFENCE_VERSION : '[Unknown version]'),
			'body'       => $postParams,
			'sslverify'  => $ssl_verify,
		);
		if (!$ssl_verify) {
			// Some versions of cURL will complain that SSL verification is disabled but the CA bundle was supplied.
			$args['sslcertificates'] = false;
		}

		$response = wp_remote_post($url, $args);

		$this->lastHTTPStatus = (int) wp_remote_retrieve_response_code($response);

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			throw new Exception("There was an " . ($error_message ? '' : 'unknown ') . "error connecting to the the Wordfence scanning servers" . ($error_message ? ": $error_message" : '.'));
		}

		if (!empty($response['response']['code'])) {
			$this->lastHTTPStatus = (int) $response['response']['code'];
		}

		if (200 != $this->lastHTTPStatus) {
			throw new Exception("We received an error response when trying to contact the Wordfence scanning servers. The HTTP status code was [$this->lastHTTPStatus]");
		}

		$this->curlContent = wp_remote_retrieve_body($response);
		return $this->curlContent;
	}

	public function binCall($func, $postData) {
		$url = $this->getAPIURL() . '/v' . WORDFENCE_API_VERSION . '/?' . $this->makeAPIQueryString() . '&action=' . $func;

		$data = $this->getURL($url, $postData);

		if (preg_match('/\{.*errorMsg/', $data)) {
			$jdat = @json_decode($data, true);
			if (is_array($jdat) && $jdat['errorMsg']) {
				throw new Exception($jdat['errorMsg']);
			}
		}
		return array('code' => $this->lastHTTPStatus, 'data' => $data);
	}

	public function makeAPIQueryString() {
		$siteurl = '';
		if (function_exists('get_bloginfo')) {
			if (is_multisite()) {
				$siteurl = network_home_url();
				$siteurl = rtrim($siteurl, '/'); //Because previously we used get_bloginfo and it returns http://example.com without a '/' char.
			} else {
				$siteurl = home_url();
			}
		}
		return self::buildQuery(array(
			'v'       => $this->wordpressVersion,
			's'       => $siteurl,
			'k'       => $this->APIKey,
			'openssl' => function_exists('openssl_verify') && defined('OPENSSL_VERSION_NUMBER') ? OPENSSL_VERSION_NUMBER : '0.0.0',
			'phpv'    => phpversion(),
		));
	}

	private function buildQuery($data) {
		if (version_compare(phpversion(), '5.1.2', '>=')) {
			return http_build_query($data, '', '&'); //arg_separator parameter was only added in PHP 5.1.2. We do this because some PHP.ini's have arg_separator.output set to '&amp;'
		} else {
			return http_build_query($data);
		}
	}

	private function getAPIURL() {
		return self::SSLEnabled() ? WORDFENCE_API_URL_SEC : WORDFENCE_API_URL_NONSEC;
	}

	public static function SSLEnabled() {
		if (!function_exists('wp_http_supports')) {
			require_once ABSPATH . WPINC . 'http.php';
		}
		return wp_http_supports(array('ssl'));
	}
}

?>
