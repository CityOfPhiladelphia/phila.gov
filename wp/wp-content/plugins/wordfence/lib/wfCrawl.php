<?php
require_once('wfUtils.php');
class wfCrawl {
	public static function isCrawler($UA){
		$browscap = new wfBrowscap();
		$b = $browscap->getBrowser($UA);
		if($b && isset($b['Crawler']) && $b['Crawler']){
			return true;
		}
		return false;
	}
	public static function verifyCrawlerPTR($hostPattern, $IP){
		global $wpdb; $table = $wpdb->base_prefix . 'wfCrawlers';
		$db = new wfDB();
		$IPn = wfUtils::inet_pton($IP);
		$status = $db->querySingle("select status from $table where IP=%s and patternSig=UNHEX(MD5('%s')) and lastUpdate > unix_timestamp() - %d", $IPn, $hostPattern, WORDFENCE_CRAWLER_VERIFY_CACHE_TIME);
		if($status){
			if($status == 'verified'){
				return true;
			} else {
				return false;
			}
		}
		$host = wfUtils::reverseLookup($IP);
		if(! $host){ 
			$db->queryWrite("insert into $table (IP, patternSig, status, lastUpdate, PTR) values (%s, UNHEX(MD5('%s')), '%s', unix_timestamp(), '%s') ON DUPLICATE KEY UPDATE status='%s', lastUpdate=unix_timestamp(), PTR='%s'", $IPn, $hostPattern, 'noPTR', '', 'noPTR', '');
			return false; 
		}
		if(preg_match($hostPattern, $host)){
			$resultIPs = wfUtils::resolveDomainName($host);
			$addrsMatch = false;
			foreach($resultIPs as $resultIP){
				if($resultIP == $IP){
					$addrsMatch = true;
					break;
				}
			}
			if($addrsMatch){
				$db->queryWrite("insert into $table (IP, patternSig, status, lastUpdate, PTR) values (%s, UNHEX(MD5('%s')), '%s', unix_timestamp(), '%s') ON DUPLICATE KEY UPDATE status='%s', lastUpdate=unix_timestamp(), PTR='%s'", $IPn, $hostPattern, 'verified', $host, 'verified', $host);
				return true;
			} else {
				$db->queryWrite("insert into $table (IP, patternSig, status, lastUpdate, PTR) values (%s, UNHEX(MD5('%s')), '%s', unix_timestamp(), '%s') ON DUPLICATE KEY UPDATE status='%s', lastUpdate=unix_timestamp(), PTR='%s'", $IPn, $hostPattern, 'fwdFail', $host, 'fwdFail', $host);
				return false;
			}
		} else {
			$db->queryWrite("insert into $table (IP, patternSig, status, lastUpdate, PTR) values (%s, UNHEX(MD5('%s')), '%s', unix_timestamp(), '%s') ON DUPLICATE KEY UPDATE status='%s', lastUpdate=unix_timestamp(), PTR='%s'", $IPn, $hostPattern, 'badPTR', $host, 'badPTR', $host);
			return false;
		}
	}
	public static function isGooglebot(){
		$UA = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		if(preg_match('/Googlebot\/\d\.\d/', $UA)){ // UA: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html) or (rarely used): Googlebot/2.1 (+http://www.google.com/bot.html)
			return true;
		}
		return false;
	}
	public static function isGoogleCrawler($UA = null){
		if ($UA === null) {
			$UA = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		}

		foreach(self::$googPat as $pat){
			if(preg_match($pat . 'i', $UA)){
				return true;
			}
		}
		return false;
	}
	private static $googPat = array(
'@^Mozilla/5\\.0 \\(.*Google Keyword Tool.*\\)$@',
'@^Mozilla/5\\.0 \\(.*Feedfetcher\\-Google.*\\)$@',
'@^Feedfetcher\\-Google\\-iGoogleGadgets.*$@',
'@^searchbot admin\\@google\\.com$@',
'@^Google\\-Site\\-Verification.*$@',
'@^Google OpenSocial agent.*$@',
'@^.*Googlebot\\-Mobile/2\\..*$@',
'@^AdsBot\\-Google\\-Mobile.*$@',
'@^google \\(.*Enterprise.*\\)$@',
'@^Mediapartners\\-Google.*$@',
'@^GoogleFriendConnect.*$@',
'@^googlebot\\-urlconsole$@',
'@^.*Google Web Preview.*$@',
'@^Feedfetcher\\-Google.*$@',
'@^AppEngine\\-Google.*$@',
'@^Googlebot\\-Video.*$@',
'@^Googlebot\\-Image.*$@',
'@^Google\\-Sitemaps.*$@',
'@^Googlebot/Test.*$@',
'@^Googlebot\\-News.*$@',
'@^.*Googlebot/2\\.1.*$@',
'@^AdsBot\\-Google.*$@',
'@^Google$@'
	);


	/**
	 * Has correct user agent and PTR record points to .googlebot.com domain.
	 *
	 * @param string|null $ip
	 * @param string|null $ua
	 * @return bool
	 */
	public static function isVerifiedGoogleCrawler($ip = null, $ua = null) {
		static $verified;
		if (!isset($verified)) {
			$verified = array();
		}
		if ($ip === null) {
			$ip = wfUtils::getIP();
		}
		if (array_key_exists($ip, $verified)) {
			return $verified[$ip];
		}
		if (self::isGoogleCrawler($ua)) {
			if (self::verifyCrawlerPTR(wordfence::getLog()->getGooglePattern(), $ip)) {
				$verified[$ip] = true;
				return $verified[$ip];
			}
			if (self::verifyGooglebotViaNOC1($ip)) {
				$verified[$ip] = true;
				return $verified[$ip];
			}
		}
		$verified[$ip] = false;
		return $verified[$ip];
	}

	/**
	 * @param string|null $ip
	 * @return bool
	 */
	public static function verifyGooglebotViaNOC1($ip = null) {
		global $wpdb;
		$table = $wpdb->base_prefix . 'wfCrawlers';
		if ($ip === null) {
			$ip = wfUtils::getIP();
		}
		$db = new wfDB();
		$IPn = wfUtils::inet_pton($ip);
		$patternSig = 'googlenoc1';
		$status = $db->querySingle("select status from $table
				where IP=%s
				and patternSig=UNHEX(MD5('%s'))
				and lastUpdate > unix_timestamp() - %d",
				$IPn,
				$patternSig,
				WORDFENCE_CRAWLER_VERIFY_CACHE_TIME);
		if ($status === 'verified') {
			return true;
		} else if ($status === 'fakeBot') {
			return false;
		}

		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		try {
			$data = $api->call('verify_googlebot', array(
				'ip' => $ip,
			));
			if (is_array($data) && !empty($data['verified'])) {
				// Cache results
				$db->queryWrite("insert into $table (IP, patternSig, status, lastUpdate)
values (%s, UNHEX(MD5('%s')), '%s', unix_timestamp())
ON DUPLICATE KEY UPDATE status='%3\$s', lastUpdate=unix_timestamp()",
						$IPn, $patternSig, 'verified');
				return true;
			} else {
				$db->queryWrite("insert into $table (IP, patternSig, status, lastUpdate)
values (%s, UNHEX(MD5('%s')), '%s', unix_timestamp())
ON DUPLICATE KEY UPDATE status='%3\$s', lastUpdate=unix_timestamp()",
						$IPn, $patternSig, 'fakeBot');
			}
		} catch (Exception $e) {
			// Do nothing, bail
		}
		return false;
	}
}
?>
