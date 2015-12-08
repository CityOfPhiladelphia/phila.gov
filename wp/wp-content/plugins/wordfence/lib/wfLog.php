<?php
require_once('wfDB.php');
require_once('wfUtils.php');
require_once('wfBrowscap.php');
class wfLog {
	private $hitsTable = '';
	private $apiKey = '';
	private $wp_version = '';
	private $db = false;
	private $googlePattern = '/\.(?:googlebot\.com|google\.[a-z]{2,3}|google\.[a-z]{2}\.[a-z]{2}|1e100\.net)$/i';
	private static $gbSafeCache = array();
	public function __construct($apiKey, $wp_version){
		$this->apiKey = $apiKey;
		$this->wp_version = $wp_version;
		global $wpdb;
		$this->hitsTable = $wpdb->base_prefix . 'wfHits';
		$this->loginsTable = $wpdb->base_prefix . 'wfLogins';
		$this->blocksTable = $wpdb->base_prefix . 'wfBlocks';
		$this->lockOutTable = $wpdb->base_prefix . 'wfLockedOut';
		$this->leechTable = $wpdb->base_prefix . 'wfLeechers';
		$this->badLeechersTable = $wpdb->base_prefix . 'wfBadLeechers';
		$this->scanTable = $wpdb->base_prefix . 'wfScanners';
		$this->throttleTable = $wpdb->base_prefix . 'wfThrottleLog';
		$this->statusTable = $wpdb->base_prefix . 'wfStatus';
		$this->ipRangesTable = $wpdb->base_prefix . 'wfBlocksAdv';
		$this->perfTable = $wpdb->base_prefix . 'wfPerfLog';
	}
	public function logPerf($IP, $UA, $URL, $data){
		$IP = wfUtils::inet_pton($IP);
		$this->getDB()->queryWrite("insert into " . $this->perfTable . " (IP, userID, UA, URL, ctime, fetchStart, domainLookupStart, domainLookupEnd, connectStart, connectEnd, requestStart, responseStart, responseEnd, domReady, loaded) values (%s, %d, '%s', '%s', unix_timestamp(), %d, %d, %d, %d, %d, %d, %d, %d, %d, %d)", 
			$IP, 
			$this->getCurrentUserID(), 
			$UA, 
			$URL,
			$data['fetchStart'],
			$data['domainLookupStart'],
			$data['domainLookupEnd'],
			$data['connectStart'],
			$data['connectEnd'],
			$data['requestStart'],
			$data['responseStart'],
			$data['responseEnd'],
			$data['domReady'],
			$data['loaded']
			);
	}
	public function logLogin($action, $fail, $username){
		if(! $username){
			return;
		}
		$user = get_user_by('login', $username);
		$userID = 0;
		if($user){
			$userID = $user->ID;
			if(! $userID){
				return;
			}
		}
		// change the action flag here if the user does not exist.
		if ($action == 'loginFailValidUsername' && $userID == 0) {
			$action = 'loginFailInvalidUsername';
		}
		//Else userID stays 0 but we do log this even though the user doesn't exist.
		$this->getDB()->queryWrite("insert into " . $this->loginsTable . " (ctime, fail, action, username, userID, IP, UA) values (%f, %d, '%s', '%s', %s, %s, '%s')", 
			sprintf('%.6f', microtime(true)),
			$fail,
			$action,
			$username,
			$userID,
			wfUtils::inet_pton(wfUtils::getIP()),
			(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '')
			);
	}
	private function getCurrentUserID(){
		$id = get_current_user_id();
		return $id ? $id : 0;
	}
	public function logLeechAndBlock($type){ //404 or hit
		if(wfConfig::get('firewallEnabled')){
			//Moved the following block into the "is fw enabled section" for optimization. 
			$IP = wfUtils::getIP();
			$IPnum = wfUtils::inet_pton($IP);
			if($this->isWhitelisted($IP)){
				return;
			}
			if (wfConfig::get('neverBlockBG') == 'neverBlockUA' && wfCrawl::isGoogleCrawler()) {
				return;
			}
			if (wfConfig::get('neverBlockBG') == 'neverBlockVerified' && wfCrawl::isVerifiedGoogleCrawler()) {
				return;
			}

			if ($type == '404') {
				$allowed404s = wfConfig::get('allowed404s');
				if (is_string($allowed404s)) {
					$allowed404s = array_filter(explode("\n", $allowed404s));
					$allowed404sPattern = '';
					foreach ($allowed404s as $allowed404) {
						$allowed404sPattern .= preg_replace('/\\\\\*/', '.*?', preg_quote($allowed404, '/')) . '|';
					}
					$uri = $_SERVER['REQUEST_URI'];
					if (($index = strpos($uri, '?')) !== false) {
						$uri = substr($uri, 0, $index);
					}
					if ($allowed404sPattern && preg_match('/^' . substr($allowed404sPattern, 0, -1) . '$/i', $uri)) {
						return;
					}
				}
			}


			if($type == '404'){
				$table = $this->scanTable;
			} else if($type == 'hit'){
				$table = $this->leechTable;
			} else {
				wordfence::status(1, 'error', "Invalid type to logLeechAndBlock(): $type");
				return;
			}
			$this->getDB()->queryWrite("insert into $table (eMin, IP, hits) values (floor(unix_timestamp() / 60), %s, 1) ON DUPLICATE KEY update hits = IF(@wfcurrenthits := hits + 1, hits + 1, hits + 1)", wfUtils::inet_pton($IP));
			$hitsPerMinute = $this->getDB()->querySingle("select @wfcurrenthits");
			//end block moved into "is fw enabled" section

			//Range blocking was here. Moved to wordfenceClass::veryFirstAction

			if(wfConfig::get('maxGlobalRequests') != 'DISABLED' && $hitsPerMinute > wfConfig::get('maxGlobalRequests')){ //Applies to 404 or pageview
				$this->takeBlockingAction('maxGlobalRequests', "Exceeded the maximum global requests per minute for crawlers or humans.");
			}
			if($type == '404'){
				global $wpdb; $p = $wpdb->base_prefix;
				if(wfConfig::get('other_WFNet')){
					$this->getDB()->queryWrite("insert IGNORE into $p"."wfNet404s (sig, ctime, URI) values (UNHEX(MD5('%s')), unix_timestamp(), '%s')", $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_URI']);
				}
				$pat = wfConfig::get('vulnRegex');
				if($pat){
					$URL = wfUtils::getRequestedURL();
					if(preg_match($pat, $URL)){
						$this->getDB()->queryWrite("insert IGNORE into $p"."wfVulnScanners (IP, ctime, hits) values (%s, unix_timestamp(), 1) ON DUPLICATE KEY UPDATE ctime = unix_timestamp(), hits = hits + 1", wfUtils::inet_pton($IP));
						if(wfConfig::get('maxScanHits') != 'DISABLED'){
							if( empty($_SERVER['HTTP_REFERER'] )){
								$this->getDB()->queryWrite("insert into " . $this->badLeechersTable . " (eMin, IP, hits) values (floor(unix_timestamp() / 60), %s, 1) ON DUPLICATE KEY update hits = IF(@wfblcurrenthits := hits + 1, hits + 1, hits + 1)", $IPnum); 
								$BL_hitsPerMinute = $this->getDB()->querySingle("select @wfblcurrenthits");
								if($BL_hitsPerMinute > wfConfig::get('maxScanHits')){
									$this->takeBlockingAction('maxScanHits', "Exceeded the maximum number of 404 requests per minute for a known security vulnerability.");
								}
							}
						}
					}
				}
			}
			if(isset($_SERVER['HTTP_USER_AGENT']) && wfCrawl::isCrawler($_SERVER['HTTP_USER_AGENT'])){
				if($type == 'hit' && wfConfig::get('maxRequestsCrawlers') != 'DISABLED' && $hitsPerMinute > wfConfig::get('maxRequestsCrawlers')){
					$this->takeBlockingAction('maxRequestsCrawlers', "Exceeded the maximum number of requests per minute for crawlers."); //may not exit
				} else if($type == '404' && wfConfig::get('max404Crawlers') != 'DISABLED' && $hitsPerMinute > wfConfig::get('max404Crawlers')){
					$this->takeBlockingAction('max404Crawlers', "Exceeded the maximum number of page not found errors per minute for a crawler.");
				}
			} else {
				if($type == 'hit' && wfConfig::get('maxRequestsHumans') != 'DISABLED' && $hitsPerMinute > wfConfig::get('maxRequestsHumans')){
					$this->takeBlockingAction('maxRequestsHumans', "Exceeded the maximum number of page requests per minute for humans.");
				} else if($type == '404' && wfConfig::get('max404Humans') != 'DISABLED' && $hitsPerMinute > wfConfig::get('max404Humans')){
					$this->takeBlockingAction('max404Humans', "Exceeded the maximum number of page not found errors per minute for humans.");
				}
			}
		}
	}

	/**
	 * @param string $IP Should be in dot or colon notation (127.0.0.1 or ::1)
	 * @return bool
	 */
	public function isWhitelisted($IP) {
		$wfIPBlock = new wfUserIPRange('69.46.36.[1-32]');
		if ($wfIPBlock->isIPInRange($IP)) { //IP is in Wordfence's IP block which would prevent our scanning server manually kicking off scans that are stuck
			return true;
		}
		//We now whitelist all private addrs 
		if (wfUtils::isPrivateAddress($IP)) {
			return true;
		}
		//These belong to sucuri's scanning servers which will get blocked by Wordfence as a false positive if you try a scan. So we whitelisted them.
		$externalWhite = array('97.74.127.171', '69.164.203.172', '173.230.128.135', '66.228.34.49', '66.228.40.185', '50.116.36.92', '50.116.36.93', '50.116.3.171', '198.58.96.212', '50.116.63.221', '192.155.92.112', '192.81.128.31', '198.58.106.244', '192.155.95.139', '23.239.9.227', '198.58.112.103', '192.155.94.43', '162.216.16.33', '173.255.233.124', '173.255.233.124', '192.155.90.179', '50.116.41.217', '192.81.129.227', '198.58.111.80');
		if (in_array($IP, $externalWhite)) {
			return true;
		}
		$list = wfConfig::get('whitelisted');
		if (!$list) {
			return false;
		}
		$list = explode(',', $list);
		if (sizeof($list) < 1) {
			return false;
		}
		foreach ($list as $whiteIP) {
			$white_ip_block = new wfUserIPRange($whiteIP);
			if ($white_ip_block->isIPInRange($IP)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get an array of static IPs, tuple for a numeric IP range, or a wfUserIPRange object to define and test a range
	 * like [127-128].0.0.[1-40]
	 *
	 * @see wfUserIPRange
	 * @param null $user_whitelisted
	 * @return array
	 */
	public function getWhitelistedIPs($user_whitelisted = null) {
		$white_listed_ips = array();
		// Wordfence's IP block which would prevent our scanning server manually kicking off scans that are stuck
		$white_listed_ips[] = array(1160651777, 1160651808);

		// Private range
		$private_range = wfUtils::getPrivateAddrs();
		foreach ($private_range as $ip_range) {
			$white_listed_ips[] = array($ip_range[1], $ip_range[2]);
		}

		// These belong to sucuri's scanning servers which will get blocked by Wordfence as a false positive if you try a scan. So we whitelisted them.
		$white_listed_ips = array_merge($white_listed_ips, array_map(array('wfUtils', 'inet_pton'), array('97.74.127.171', '69.164.203.172', '173.230.128.135', '66.228.34.49', '66.228.40.185', '50.116.36.92', '50.116.36.93', '50.116.3.171', '198.58.96.212', '50.116.63.221', '192.155.92.112', '192.81.128.31', '198.58.106.244', '192.155.95.139', '23.239.9.227', '198.58.112.103', '192.155.94.43', '162.216.16.33', '173.255.233.124', '173.255.233.124', '192.155.90.179', '50.116.41.217', '192.81.129.227', '198.58.111.80')));

		if ($user_whitelisted === null) {
			$user_whitelisted = wfConfig::get('whitelisted');
		}

		if ($user_whitelisted) {
			$user_whitelisted = explode(',', $user_whitelisted);
			foreach ($user_whitelisted as $whiteIP) {
				$white_listed_ips[] = new wfUserIPRange($whiteIP);
			}
		}

		return $white_listed_ips;
	}

	public function unblockAllIPs(){
		$this->getDB()->queryWrite("delete from " . $this->blocksTable);
		wfCache::updateBlockedIPs('add');
	}
	public function unlockAllIPs(){
		$this->getDB()->queryWrite("delete from " . $this->lockOutTable);
	}
	public function unblockIP($IP){
		$this->getDB()->queryWrite("delete from " . $this->blocksTable . " where IP=%s", wfUtils::inet_pton($IP));
		wfCache::updateBlockedIPs('add');
	}
	public function unblockRange($id){
		$this->getDB()->queryWrite("delete from " . $this->ipRangesTable . " where id=%d", $id);
		wfCache::updateBlockedIPs('add');
	}

	/**
	 *
	 * @param string $blockType
	 * @param string $range
	 * @param string $reason
	 * @return bool
	 */
	public function blockRange($blockType, $range, $reason){
		$reason = stripslashes($reason);
		$this->getDB()->queryWrite("insert IGNORE into " . $this->ipRangesTable . " (blockType, blockString, ctime, reason, totalBlocked, lastBlocked) values ('%s', '%s', unix_timestamp(), '%s', 0, 0)", $blockType, $range, $reason);
		wfCache::updateBlockedIPs('add');
		return true;
	}
	public function getRangesBasic(){
		$results = $this->getDB()->querySelect("select blockString from " . $this->ipRangesTable);
		if(is_array($results) && sizeof($results) > 0){
			$ret = array();
			foreach($results as $r){
				$ret[] = $r['blockString'];
			}
			return $ret;
		} else {
			return false;
		}
	}
	public function getRanges(){
		$results = $this->getDB()->querySelect("select id, blockType, blockString, unix_timestamp() - ctime as ctimeAgo, reason, totalBlocked, unix_timestamp() - lastBlocked as lastBlockedAgo, lastBlocked from " . $this->ipRangesTable . " order by ctime desc");
		foreach($results as &$elem){
			if($elem['blockType'] != 'IU'){ continue; } //We only use IU type for now, but have this for future different block types.
			$elem['ctimeAgo'] = wfUtils::makeTimeAgo($elem['ctimeAgo']);
			if($elem['lastBlocked'] > 0){
				$elem['lastBlockedAgo'] = wfUtils::makeTimeAgo($elem['lastBlockedAgo']) . ' ago';
			} else {
				$elem['lastBlockedAgo'] = 'Never';
			}
			$blockDat = explode('|', $elem['blockString']);
			$elem['ipPattern'] = "";
			$numBlockElements = 0;
			if($blockDat[0]){
				$numBlockElements++;
				list($start_range, $end_range) = explode('-', $blockDat[0]);
				if (!preg_match('/[\.:]/', $start_range)) {
					$start_range = long2ip($start_range);
					$end_range = long2ip($end_range);
				}
				$elem['ipPattern'] = "Block visitors with IP addresses in the range: " . $start_range . ' - ' . $end_range;
			} else {
				$elem['ipPattern'] = 'Allow all IP addresses';
			}
			if($blockDat[1]){
				$numBlockElements++;
				$elem['browserPattern'] = "Block visitors whos browsers match the pattern: " . $blockDat[1];
			} else {
				$elem['browserPattern'] = 'Allow all browsers';
			}
			if($blockDat[2]){
				$numBlockElements++;
				$elem['refererPattern'] = "Block visitors from websites that match the pattern: " . $blockDat[2];
			} else {
				$elem['refererPattern'] = "Allow visitors arriving from all websites";
			}
			if (! empty($blockDat[3])) {
				$elem['hostnamePattern'] = $blockDat[3];
			}
			$elem['patternDisabled'] = (wfConfig::get('cacheType') == 'falcon' && $numBlockElements > 1) ? true : false;
		}
		return $results;
	}
	public function blockIP($IP, $reason, $wfsn = false, $permanent = false, $maxTimeBlocked = false){ //wfsn indicates it comes from Wordfence secure network
		if($this->isWhitelisted($IP)){ return false; }
		$wfsn = $wfsn ? 1 : 0;
		$timeBlockOccurred = $this->getDB()->querySingle("select unix_timestamp() as ctime");
		$durationOfBlocks = wfConfig::get('blockedTime');
		if($maxTimeBlocked && $durationOfBlocks > $maxTimeBlocked){
			$timeBlockOccurred -= ($durationOfBlocks - $maxTimeBlocked);
		}
		if($permanent){
			//Insert permanent=1 or update existing perm or non-per block to be permanent
			$this->getDB()->queryWrite("insert into " . $this->blocksTable . " (IP, blockedTime, reason, wfsn, permanent) values (%s, %d, '%s', %d, %d) ON DUPLICATE KEY update blockedTime=%d, reason='%s', wfsn=%d, permanent=%d",
				wfUtils::inet_pton($IP),
				$timeBlockOccurred,
				$reason,
				$wfsn,
				1,
				$timeBlockOccurred,
				$reason,
				$wfsn,
				1
				);
		} else {
			//insert perm=0 but don't update and make perm blocks non-perm. 
			$this->getDB()->queryWrite("insert into " . $this->blocksTable . " (IP, blockedTime, reason, wfsn, permanent) values (%s, %d, '%s', %d, %d) ON DUPLICATE KEY update blockedTime=%d, reason='%s', wfsn=%d",
				wfUtils::inet_pton($IP),
				$timeBlockOccurred,
				$reason,
				$wfsn,
				0,
				$timeBlockOccurred,
				$reason,
				$wfsn
				);
		}

		wfActivityReport::logBlockedIP($IP);

		wfCache::updateBlockedIPs('add');
		wfConfig::inc('totalIPsBlocked');
		return true;
	}
	public function lockOutIP($IP, $reason){
		if($this->isWhitelisted($IP)){ return false; }
		$reason = stripslashes($reason);
		$this->getDB()->queryWrite("insert into " . $this->lockOutTable . " (IP, blockedTime, reason) values (%s, unix_timestamp(), '%s') ON DUPLICATE KEY update blockedTime=unix_timestamp(), reason='%s'",
			wfUtils::inet_pton($IP),
			$reason,
			$reason
			);

		wfActivityReport::logBlockedIP($IP);

		wfConfig::inc('totalIPsLocked');
		return true;
	}
	public function unlockOutIP($IP){
		$this->getDB()->queryWrite("delete from " . $this->lockOutTable . " where IP=%s", wfUtils::inet_pton($IP));
	}
	public function isIPLockedOut($IP){
		if($this->getDB()->querySingle("select IP from " . $this->lockOutTable . " where IP=%s and blockedTime + %s > unix_timestamp()", wfUtils::inet_pton($IP), wfConfig::get('loginSec_lockoutMins') * 60)){
			$this->getDB()->queryWrite("update " . $this->lockOutTable . " set blockedHits = blockedHits + 1, lastAttempt = unix_timestamp() where IP=%s", wfUtils::inet_pton($IP));
			return true;
		} else {
			return false;
		}
	}
	public function getThrottledIPs(){
		$results = $this->getDB()->querySelect("select IP, startTime, endTime, timesThrottled, lastReason, unix_timestamp() - startTime as startTimeAgo, unix_timestamp() - endTime as endTimeAgo from " . $this->throttleTable . " order by endTime desc limit 50");
		foreach($results as &$elem){
			$elem['startTimeAgo'] = wfUtils::makeTimeAgo($elem['startTimeAgo']);
			$elem['endTimeAgo'] = wfUtils::makeTimeAgo($elem['endTimeAgo']);
		}
		$this->resolveIPs($results);
		foreach($results as &$elem){
			$elem['IP'] = wfUtils::inet_ntop($elem['IP']);
		}
		return $results;
	}
	public function getLockedOutIPs(){
		$lockoutSecs = wfConfig::get('loginSec_lockoutMins') * 60;
		$results = $this->getDB()->querySelect("select IP, unix_timestamp() - blockedTime as createdAgo, reason, unix_timestamp() - lastAttempt as lastAttemptAgo, lastAttempt, blockedHits, (blockedTime + %s) - unix_timestamp() as blockedFor from " . $this->lockOutTable . " where blockedTime + %s > unix_timestamp() order by blockedTime desc", $lockoutSecs, $lockoutSecs);
		foreach($results as &$elem){
			$elem['lastAttemptAgo'] = $elem['lastAttempt'] ? wfUtils::makeTimeAgo($elem['lastAttemptAgo']) : '';
			$elem['blockedForAgo'] = wfUtils::makeTimeAgo($elem['blockedFor']);
		}
		$this->resolveIPs($results);
		foreach($results as &$elem){
			$elem['IP'] = wfUtils::inet_ntop($elem['IP']);
		}
		return $results;
	}
	public function getBlockedIPsAddrOnly(){
		$results = $this->getDB()->querySelect("select IP from " . $this->blocksTable . " where (permanent=1 OR (blockedTime + %s > unix_timestamp()))", wfConfig::get('blockedTime'), wfConfig::get('blockedTime'));
		$ret = array();
		foreach($results as $elem){
			$ret[] = wfUtils::inet_ntop($elem['IP']);
		}
		return $ret;
	}
	public function getBlockedIPs(){
		$results = $this->getDB()->querySelect("select IP, unix_timestamp() - blockedTime as createdAgo, reason, unix_timestamp() - lastAttempt as lastAttemptAgo, lastAttempt, blockedHits, (blockedTime + %s) - unix_timestamp() as blockedFor, permanent from " . $this->blocksTable . " where (permanent=1 OR (blockedTime + %s > unix_timestamp())) order by blockedTime desc", wfConfig::get('blockedTime'), wfConfig::get('blockedTime'));
		foreach($results as &$elem){
			$lastHitAgo = 0;
			$totalHits = 0;
			$serverTime = $this->getDB()->querySingle("select unix_timestamp()");
			$lastLeech = $this->getDB()->querySingleRec("select max(eMin) * 60 as lastHit, sum(hits) as totalHits from " . $this->leechTable . " where IP=%s", $elem['IP']);
			//$lastLeech will be true because we use aggregation functions, so check actual values
			if($lastLeech['lastHit']){ 
				$totalHits += $lastLeech['totalHits']; 
				$lastHitAgo = $serverTime - $lastLeech['lastHit'];
				$elem['lastHit'] = $lastLeech['lastHit'];
			}
			$lastScan = $this->getDB()->querySingleRec("select max(eMin) * 60 as lastHit, sum(hits) as totalHits from " . $this->scanTable . " where IP=%s", $elem['IP']);
			if($lastScan['lastHit']){ //Checking actual value because we will get a row back from aggregation funcs
				$totalHits += $lastScan['totalHits'];
				$lastScanAgo = $serverTime - $lastScan['lastHit']; 
				if($lastScanAgo < $lastHitAgo){
					$lastHitAgo = $lastScanAgo;
					$elem['lastHit'] = $lastScan['lastHit'];
				}
			}
			$elem['totalHits'] = $totalHits;
			$elem['lastHitAgo'] = $lastHitAgo ? wfUtils::makeTimeAgo($lastHitAgo) : '';
			$elem['lastAttemptAgo'] = $elem['lastAttempt'] ? wfUtils::makeTimeAgo($elem['lastAttemptAgo']) : '';
			$elem['blockedForAgo'] = wfUtils::makeTimeAgo($elem['blockedFor']);
		}
		$this->resolveIPs($results);
		foreach($results as &$elem){
			$elem['blocked'] = 1;
			$elem['IP'] = wfUtils::inet_ntop($elem['IP']);
		}
		return $results;
	}
	public function getLeechers($type){
		if($type == 'topScanners'){
			$table = $this->scanTable;
		} else if($type == 'topLeechers'){
			$table = $this->leechTable;
		} else {
			wordfence::status(1, 'error', "Invalid type to getLeechers(): $type");
			return false;
		}
		$results = $this->getDB()->querySelect("select IP, sum(hits) as totalHits, eMin * 60 as timestamp, (UNIX_TIMESTAMP() - (eMin * 60)) as timeAgo  from $table where eMin > ((unix_timestamp() - 86400) / 60) group by IP order by totalHits desc limit 20");
		$this->resolveIPs($results);
		foreach($results as &$elem){
			$elem['timeAgo'] = wfUtils::makeTimeAgo($elem['timeAgo']);
			$elem['blocked'] = $this->getDB()->querySingle("select blockedTime from " . $this->blocksTable . " where IP=%s and ((blockedTime + %s > unix_timestamp()) OR permanent = 1)", $elem['IP'], wfConfig::get('blockedTime'));
			//take action
			$elem['IP'] = wfUtils::inet_ntop($elem['IP']);
		}
		return $results;
	}
	public function logHit(){
		if(! wfConfig::liveTrafficEnabled()){ return; }	
		$headers = array();
		foreach($_SERVER as $h=>$v){
			if(preg_match('/^HTTP_(.+)$/', $h, $matches) ){
				$headers[$matches[1]] = $v;
			}
		}
		$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$this->getDB()->queryWrite("insert into " . $this->hitsTable . " (ctime, is404, isGoogle, IP, userID, newVisit, URL, referer, UA, jsRun) values (%f, %d, %d, %s, %s, %d, '%s', '%s', '%s', %d)",
			sprintf('%.6f', microtime(true)),
			(is_404() ? 1 : 0),
			(wfCrawl::isGoogleCrawler() ? 1 : 0),
			wfUtils::inet_pton(wfUtils::getIP()),
			$this->getCurrentUserID(),
			(wordfence::$newVisit ? 1 : 0),
			wfUtils::getRequestedURL(),
			(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),
			$ua,
			(int) (isset($_COOKIE['wordfence_verifiedHuman']) && wp_verify_nonce($_COOKIE['wordfence_verifiedHuman'], 'wordfence_verifiedHuman' . $ua . wfUtils::getIP()))
			);
		return $this->getDB()->querySingle("select last_insert_id()");
	}
	public function getPerfStats($afterTime, $limit = 50){
		$serverTime = $this->getDB()->querySingle("select unix_timestamp()");
		$results = $this->getDB()->querySelect("select * from " . $this->perfTable . " where ctime > %f order by ctime desc limit %d", $afterTime, $limit);
		$this->resolveIPs($results);
		$browscap = new wfBrowscap();
		foreach($results as &$res){
			$res['timeAgo'] = wfUtils::makeTimeAgo($serverTime - $res['ctime']);
			$res['IP'] = wfUtils::inet_ntop($res['IP']);
			$res['browser'] = false;
			if($res['UA']){
				$b = $browscap->getBrowser($res['UA']);
				if($b){
					$res['browser'] = array(
						'browser' => $b['Browser'],
						'version' => $b['Version'],
						'platform' => $b['Platform'],
						'isMobile' => $b['isMobileDevice'],
						'isCrawler' => $b['Crawler']
						);
				}
			}
			if($res['userID']){
				$ud = get_userdata($res['userID']);
				if($ud){
					$res['user'] = array(
						'editLink' => wfUtils::editUserLink($res['userID']),
						'display_name' => $ud->display_name,
						'ID' => $res['userID']
						);
					$res['user']['avatar'] = get_avatar($res['userID'], 16);
				}
			} else {
				$res['user'] = false;
			}
		}
		return $results;
	}
	public function getHits($hitType /* 'hits' or 'logins' */, $type, $afterTime, $limit = 50, $IP = false){
		$serverTime = $this->getDB()->querySingle("select unix_timestamp()");
		$IPSQL = "";
		if($IP){
			$IPSQL = " and IP=%s ";
			$sqlArgs = array($afterTime, wfUtils::inet_pton($IP), $limit);
		} else {
			$sqlArgs = array($afterTime, $limit);
		}
		if($hitType == 'hits'){
			if($type == 'hit'){
				$typeSQL = " ";
			} else if($type == 'crawler'){
				$now = time();
				$typeSQL = " and jsRun = 0 and $now - ctime > 30 ";
			} else if($type == 'gCrawler'){
				$typeSQL = " and isGoogle = 1 ";
			} else if($type == '404'){
				$typeSQL = " and is404 = 1 ";
			} else if($type == 'human'){
				$typeSQL = " and jsRun = 1 ";
			} else if($type == 'ruser'){
				$typeSQL = " and userID > 0 ";
			} else {
				wordfence::status(1, 'error', "Invalid log type to wfLog: $type");
				return false;
			}
			array_unshift($sqlArgs, "select * from " . $this->hitsTable . " where ctime > %f $IPSQL $typeSQL order by ctime desc limit %d");
			$results = call_user_func_array(array($this->getDB(), 'querySelect'), $sqlArgs);

		} else if($hitType == 'logins'){
			array_unshift($sqlArgs, "select * from " . $this->loginsTable . " where ctime > %f $IPSQL order by ctime desc limit %d");
			$results = call_user_func_array(array($this->getDB(), 'querySelect'), $sqlArgs ); 

		} else {
			wordfence::status(1, 'error', "getHits got invalid hitType: $hitType");
			return false;
		}
		$this->resolveIPs($results);
		$ourURL = parse_url(site_url());
		$ourHost = strtolower($ourURL['host']);
		$ourHost = preg_replace('/^www\./i', '', $ourHost);
		$browscap = new wfBrowscap();

		$advanced_blocking_results = $this->getDB()->querySelect('SELECT * FROM ' . $this->ipRangesTable);
		$advanced_blocking = array();
		foreach ($advanced_blocking_results as $advanced_blocking_row) {
			list($blocked_range) = explode('|', $advanced_blocking_row['blockString']);
			$blocked_range = explode('-', $blocked_range);
			if (count($blocked_range) == 2) {
				// Still using v5 32 bit int style format.
				if (!preg_match('/[\.:]/', $blocked_range[0])) {
					$blocked_range[0] = long2ip($blocked_range[0]);
					$blocked_range[1] = long2ip($blocked_range[1]);
				}
				$advanced_blocking[] = array(wfUtils::inet_pton($blocked_range[0]), wfUtils::inet_pton($blocked_range[1]), $advanced_blocking_row['id']);
			}
		}

		foreach($results as &$res){
			$res['type'] = $type;
			$res['timeAgo'] = wfUtils::makeTimeAgo($serverTime - $res['ctime']);
			$res['blocked'] = $this->getDB()->querySingle("select blockedTime from " . $this->blocksTable . " where IP=%s and (permanent = 1 OR (blockedTime + %s > unix_timestamp()))", $res['IP'], wfConfig::get('blockedTime'));
			$res['rangeBlocked'] = false;
			$res['ipRangeID'] = -1;
			foreach ($advanced_blocking as $advanced_blocking_row) {
				if (strcmp($res['IP'], $advanced_blocking_row[0]) >= 0 && strcmp($res['IP'], $advanced_blocking_row[1]) <= 0) {
					$res['rangeBlocked'] = true;
					$res['ipRangeID'] = $advanced_blocking_row[2];
					break;
				}
			}
			$res['IP'] = wfUtils::inet_ntop($res['IP']);
			$res['extReferer'] = false;
			if(isset( $res['referer'] ) && $res['referer']){
				if(wfUtils::hasXSS($res['referer'] )){ //filtering out XSS
					$res['referer'] = '';
				}
			}
			if( isset( $res['referer'] ) && $res['referer']){
				$refURL = parse_url($res['referer']);
				if(is_array($refURL) && isset($refURL['host']) && $refURL['host']){
					$refHost = strtolower(preg_replace('/^www\./i', '', $refURL['host']));
					if($refHost != $ourHost){
						$res['extReferer'] = true;
						//now extract search terms
						$q = false;
						if(preg_match('/(?:google|bing|alltheweb|aol|ask)\./i', $refURL['host'])){
							$q = 'q';
						} else if(stristr($refURL['host'], 'yahoo.')){
							$q = 'p';
						} else if(stristr($refURL['host'], 'baidu.')){
							$q = 'wd';
						}
						if($q){
							$queryVars = array();
							if( isset( $refURL['query'] ) ) {
								parse_str($refURL['query'], $queryVars);
								if(isset($queryVars[$q])){
									$res['searchTerms'] = $queryVars[$q];
								}
							}
						}
					}
				}
				if($res['extReferer']){
					if ( isset( $referringPage ) && stristr( $referringPage['host'], 'google.' ) )
					{
						parse_str( $referringPage['query'], $queryVars );
						echo $queryVars['q']; // This is the search term used
					}
				}
			}
			$res['browser'] = false;
			if($res['UA']){
				$b = $browscap->getBrowser($res['UA']);
				if($b){
					$res['browser'] = array(
						'browser'   => !empty($b['Browser']) ? $b['Browser'] : "",
						'version'   => !empty($b['Version']) ? $b['Version'] : "",
						'platform'  => !empty($b['Platform']) ? $b['Platform'] : "",
						'isMobile'  => !empty($b['isMobileDevice']) ? $b['isMobileDevice'] : "",
						'isCrawler' => !empty($b['Crawler']) ? $b['Crawler'] : "",
					);
				}
			}

						
			if($res['userID']){
				$ud = get_userdata($res['userID']);
				if($ud){
					$res['user'] = array(
						'editLink' => wfUtils::editUserLink($res['userID']),
						'display_name' => $ud->display_name,
						'ID' => $res['userID']
						);
					$res['user']['avatar'] = get_avatar($res['userID'], 16);
				}
			} else {
				$res['user'] = false;
			}
		}
		return $results;
	}
	public function resolveIPs(&$results){
		if(sizeof($results) < 1){ return; }
		$IPs = array();
		foreach($results as &$res){
			if($res['IP']){ //Can also be zero in case of non IP events
				$IPs[] = $res['IP'];
			}
		}
		$IPLocs = wfUtils::getIPsGeo($IPs); //Creates an array with IP as key and data as value

		foreach($results as &$res){
			$ip_printable = wfUtils::inet_ntop($res['IP']);
			if(isset($IPLocs[$ip_printable])){
				$res['loc'] = $IPLocs[$ip_printable];
			} else {
				$res['loc'] = false;
			}
		}
	}
	public function logHitOK(){
		if(is_admin()){ return false; } //Don't log admin pageviews
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			if(preg_match('/WordPress\/' . $this->wp_version . '/i', $_SERVER['HTTP_USER_AGENT'])){ return false; } //Ignore requests generated by WP UA.
		}
		if($userID = get_current_user_id()){
			if(wfConfig::get('liveTraf_ignorePublishers') && (current_user_can('publish_posts') || current_user_can('publish_pages')) ){ return false; } //User is logged in and can publish, so we don't log them. 
			$user = get_userdata($userID);
			if($user){
				if(wfConfig::get('liveTraf_ignoreUsers')){
					foreach(explode(',', wfConfig::get('liveTraf_ignoreUsers')) as $ignoreLogin){
						if($user->user_login == $ignoreLogin){
							return false;
						}
					}
				}
			}
		}
		if(wfConfig::get('liveTraf_ignoreIPs')){
			$IPs = explode(',', wfConfig::get('liveTraf_ignoreIPs'));
			$IP = wfUtils::getIP();
			foreach($IPs as $ignoreIP){
				if($ignoreIP == $IP){
					return false;
				}
			}
		}
		if( isset($_SERVER['HTTP_USER_AGENT']) && wfConfig::get('liveTraf_ignoreUA') ){
			if($_SERVER['HTTP_USER_AGENT'] == wfConfig::get('liveTraf_ignoreUA')){
				return false;
			}
		}

		return true;
	}
	private function getDB(){
		if(! $this->db){
			$this->db = new wfDB();
		}
		return $this->db;
	}
	public function firewallBadIPs(){
		$IP = wfUtils::getIP();
		if($this->isWhitelisted($IP)){
			return;
		}
		$IPnum = wfUtils::inet_pton($IP);
		$hostname = null;

		//New range and UA pattern blocking:
		$r1 = $this->getDB()->querySelect("select id, blockType, blockString from " . $this->ipRangesTable);
		foreach($r1 as $blockRec){
			if($blockRec['blockType'] == 'IU'){
				$ipRangeBlocked = false;
				$uaPatternBlocked = false;
				$refBlocked = false;

				$bDat = explode('|', $blockRec['blockString']);
				$ipRange = $bDat[0];
				$uaPattern = $bDat[1];
				$refPattern = isset($bDat[2]) ? $bDat[2] : '';
				if($ipRange){
					list($start_range, $end_range) = explode('-', $ipRange);
					if (preg_match('/[\.:]/', $start_range)) {
						$start_range = wfUtils::inet_pton($start_range);
						$end_range = wfUtils::inet_pton($end_range);
					} else {
						$start_range = wfUtils::inet_pton(long2ip($start_range));
						$end_range = wfUtils::inet_pton(long2ip($end_range));
					}

					if (strcmp($IPnum, $start_range) >= 0 && strcmp($IPnum, $end_range) <= 0) {
						$ipRangeBlocked = true;
					}
				}
				if (! empty($bDat[3])) {
					$ipRange = true; /* We reuse the ipRangeBlocked variable */
					if ($hostname === null) {
						$hostname = wfUtils::reverseLookup($IP);
					}
					if (preg_match(wfUtils::patternToRegex($bDat[3]), $hostname)) {
						$ipRangeBlocked = true;
					}
				}
				if($uaPattern){
					if(wfUtils::isUABlocked($uaPattern)){	
						$uaPatternBlocked = true;
					}
				}
				if($refPattern){
					if(wfUtils::isRefererBlocked($refPattern)){
						$refBlocked = true;
					}
				}
				$doBlock = false;
				if($uaPattern && $ipRange && $refPattern){
					if($uaPatternBlocked && $ipRangeBlocked && $refBlocked){
						$doBlock = true;
					}
				}
				if($uaPattern && $ipRange){
					if($uaPatternBlocked && $ipRangeBlocked){
						$doBlock = true;
					}
				}
				if($uaPattern && $refPattern){
					if($uaPatternBlocked && $refBlocked){
						$doBlock = true;
					}
				}
				if($ipRange && $refPattern){
					if($ipRangeBlocked && $refBlocked){
						$doBlock = true;
					}
				} else if($uaPattern){
					if($uaPatternBlocked){
						$doBlock = true;
					}
				} else if($ipRange){
					if($ipRangeBlocked){
						$doBlock = true;
					}
				} else if($refPattern){
					if($refBlocked){
						$doBlock = true;
					}
				}
				if($doBlock){
					$this->getDB()->queryWrite("update " . $this->ipRangesTable . " set totalBlocked = totalBlocked + 1, lastBlocked = unix_timestamp() where id=%d", $blockRec['id']);
					wfActivityReport::logBlockedIP($IP);
					$this->do503(3600, "Advanced blocking in effect.");
				}
			}
		}
		//End range/UA blocking

		// Country blocking
		if (wfConfig::get('isPaid')) {
			$blockedCountries = wfConfig::get('cbl_countries', false);
			$bareRequestURI = wfUtils::extractBareURI($_SERVER['REQUEST_URI']);
			$bareBypassRedirURI = wfUtils::extractBareURI(wfConfig::get('cbl_bypassRedirURL', ''));
			$skipCountryBlocking = false;

			if($bareBypassRedirURI && $bareRequestURI == $bareBypassRedirURI){ //Run this before country blocking because even if the user isn't blocked we need to set the bypass cookie so they can bypass future blocks.
				$bypassRedirDest = wfConfig::get('cbl_bypassRedirDest', '');
				if($bypassRedirDest){
					self::setCBLCookieBypass();
					$this->redirect($bypassRedirDest); //exits
				}
			}
			$bareBypassViewURI = wfUtils::extractBareURI(wfConfig::get('cbl_bypassViewURL', ''));
			if($bareBypassViewURI && $bareBypassViewURI == $bareRequestURI){
				self::setCBLCookieBypass();
				$skipCountryBlocking = true;
			}

			if((! $skipCountryBlocking) && $blockedCountries && (! self::isCBLBypassCookieSet()) ){
				if(is_user_logged_in() && (! wfConfig::get('cbl_loggedInBlocked', false)) ){ //User is logged in and we're allowing logins
					//Do nothing
				} else if(strpos($_SERVER['REQUEST_URI'], '/wp-login.php') !== false && (! wfConfig::get('cbl_loginFormBlocked', false))  ){ //It's the login form and we're allowing that
					//Do nothing
				} else if(strpos($_SERVER['REQUEST_URI'], '/wp-login.php') === false && (! wfConfig::get('cbl_restOfSiteBlocked', false))  ){ //It's the rest of the site and we're allowing that
					//Do nothing
				} else {
					if($country = wfUtils::IP2Country($IP) ){
						foreach(explode(',', $blockedCountries) as $blocked){
							if(strtoupper($blocked) == strtoupper($country)){ //At this point we know the user has been blocked
								if(wfConfig::get('cbl_action') == 'redir'){
									$redirURL = wfConfig::get('cbl_redirURL');
									$eRedirHost = wfUtils::extractHostname($redirURL);
									$isExternalRedir = false;
									if($eRedirHost && $eRedirHost != wfUtils::extractHostname(home_url())){ //It's an external redirect...
										$isExternalRedir = true;
									}
									if( (! $isExternalRedir) && wfUtils::extractBareURI($redirURL) == $bareRequestURI){ //Is this the URI we want to redirect to, then don't block it
										//Do nothing
										/* Uncomment the following if page components aren't loading for the page we redirect to.
										   Uncommenting is not recommended because it means that anyone from a blocked country
										   can crawl your site by sending the page blocked users are redirected to as the referer for every request.
										   But it's your call.
										} else if(wfUtils::extractBareURI($_SERVER['HTTP_REFERER']) == $redirURL){ //If the referer the page we want to redirect to? Then this might be loading as a component so don't block.
											//Do nothing
										*/
									} else {
										$this->redirect(wfConfig::get('cbl_redirURL'));
									}
								} else {
									$this->do503(3600, "Access from your area has been temporarily limited for security reasons");
									wfConfig::inc('totalCountryBlocked');
								}
							}
						}
					}
				}
			}
		}

		if($rec = $this->getDB()->querySingleRec("select blockedTime, reason from " . $this->blocksTable . " where IP=%s and (permanent=1 OR (blockedTime + %s > unix_timestamp()))", $IPnum, wfConfig::get('blockedTime'))){
			$this->getDB()->queryWrite("update " . $this->blocksTable . " set lastAttempt=unix_timestamp(), blockedHits = blockedHits + 1 where IP=%s", $IPnum);
			$now = $this->getDB()->querySingle("select unix_timestamp()");
			$secsToGo = ($rec['blockedTime'] + wfConfig::get('blockedTime')) - $now;
			if(wfConfig::get('other_WFNet') && strpos($_SERVER['REQUEST_URI'], '/wp-login.php') !== false){ //We're on the login page and this IP has been blocked
				wordfence::wfsnReportBlockedAttempt($IP, 'login');
			}
			$this->do503($secsToGo, $rec['reason']); 
		}
	}
	public function getCBLCookieVal(){
		$val = wfConfig::get('cbl_cookieVal', false);
		if(! $val){
			$val = uniqid();
			wfConfig::set('cbl_cookieVal', $val);
		}
		return $val;
	}
	public function setCBLCookieBypass(){
		wfUtils::setcookie('wfCBLBypass', self::getCBLCookieVal(), time() + (86400 * 365), '/', null, null, true);
	}
	public function isCBLBypassCookieSet(){
		if(isset($_COOKIE['wfCBLBypass']) && $_COOKIE['wfCBLBypass'] == wfConfig::get('cbl_cookieVal')){
			return true;
		}
		return false;
	}
	private function takeBlockingAction($configVar, $reason){
		if($this->googleSafetyCheckOK()){
			$action = wfConfig::get($configVar . '_action');
			if(! $action){
				//error_log("Wordfence action missing for configVar: $configVar");
				return;
			}
			$secsToGo = 0;
			if($action == 'block'){
				$IP = wfUtils::getIP();
				$this->blockIP($IP, $reason);
				$secsToGo = wfConfig::get('blockedTime');
				//Moved the following code AFTER the block to prevent multiple emails.
				if(wfConfig::get('alertOn_block')){
					wordfence::alert("Blocking IP $IP", "Wordfence has blocked IP address $IP.\nThe reason is: \"$reason\".", $IP);
				}
				wordfence::status(2, 'info', "Blocking IP $IP. $reason");
			} else if($action == 'throttle'){
				$IP = wfUtils::getIP();
				$this->getDB()->queryWrite("insert into " . $this->throttleTable . " (IP, startTime, endTime, timesThrottled, lastReason) values (%s, unix_timestamp(), unix_timestamp(), 1, '%s') ON DUPLICATE KEY UPDATE endTime=unix_timestamp(), timesThrottled = timesThrottled + 1, lastReason='%s'", wfUtils::inet_pton($IP), $reason, $reason);
				wordfence::status(2, 'info', "Throttling IP $IP. $reason");
				wfConfig::inc('totalIPsThrottled');
				$secsToGo = 60;
			}
			$this->do503($secsToGo, $reason);
		} else {
			return;
		}
	}
	public function do503($secsToGo, $reason){
		wfConfig::inc('total503s');
		wfUtils::doNotCache();
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		if($secsToGo){
			header('Retry-After: ' . $secsToGo);
		}
		require_once('wf503.php');
		exit();
	}
	private function redirect($URL){
		wp_redirect($URL, 302);
		exit();
	}
	private function googleSafetyCheckOK(){ //returns true if OK to block. Returns false if we must not block.
		$cacheKey = md5( (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . ' ' . wfUtils::getIP());
		//Cache so we can call this multiple times in one request
		if(! isset(self::$gbSafeCache[$cacheKey])){
			$nb = wfConfig::get('neverBlockBG');
			if($nb == 'treatAsOtherCrawlers'){
				self::$gbSafeCache[$cacheKey] = true; //OK to block because we're treating google like everyone else
			} else if($nb == 'neverBlockUA' || $nb == 'neverBlockVerified'){
				if(wfCrawl::isGoogleCrawler()){ //Check the UA using regex
					if($nb == 'neverBlockVerified'){
						if(wfCrawl::verifyCrawlerPTR($this->googlePattern, wfUtils::getIP())){ //UA check passed, now verify using PTR if configured to
							self::$gbSafeCache[$cacheKey] = false; //This is a verified Google crawler, so no we can't block it
						} else {
							self::$gbSafeCache[$cacheKey] = true; //This is a crawler claiming to be Google but it did not verify
						}
					} else { //neverBlockUA
						self::$gbSafeCache[$cacheKey] = false; //User configured us to only do a UA check and this claims to be google so don't block
					}
				} else {
					self::$gbSafeCache[$cacheKey] = true; //This isn't a Google UA, so it's OK to block
				}
			} else {
				//error_log("Wordfence error: neverBlockBG option is not set.");
				self::$gbSafeCache[$cacheKey] = false; //Oops the config option is not set. This should never happen because it's set on install. So we return false to indicate it's not OK to block just for safety.
			}
		}
		if(! isset(self::$gbSafeCache[$cacheKey])){
			//error_log("Wordfence assertion fail in googleSafetyCheckOK: cached value is not set.");
			return false; //for safety
		}
		return self::$gbSafeCache[$cacheKey]; //return cached value
	}
	public function addStatus($level, $type, $msg){
		//$msg = '[' . sprintf('%.2f', memory_get_usage(true) / (1024 * 1024)) . '] ' . $msg;
		$this->getDB()->queryWrite("insert into " . $this->statusTable . " (ctime, level, type, msg) values (%s, %d, '%s', '%s')", sprintf('%.6f', microtime(true)), $level, $type, $msg);
	}
	public function getStatusEvents($lastCtime){
		if($lastCtime < 1){
			$lastCtime = $this->getDB()->querySingle("select ctime from " . $this->statusTable . " order by ctime desc limit 1000,1");
			if(! $lastCtime){
				$lastCtime = 0;
			}
		}
		$results = $this->getDB()->querySelect("select ctime, level, type, msg from " . $this->statusTable . " where ctime > %f order by ctime asc", $lastCtime);
		$timeOffset = 3600 * get_option('gmt_offset');
		foreach($results as &$rec){
			//$rec['timeAgo'] = wfUtils::makeTimeAgo(time() - $rec['ctime']);
			$rec['date'] = date('M d H:i:s', $rec['ctime'] + $timeOffset);
			$rec['msg'] = wp_kses_data( (string) $rec['msg']);
		}
		return $results;
	}
	public function getSummaryEvents(){
		$results = $this->getDB()->querySelect("select ctime, level, type, msg from " . $this->statusTable . " where level = 10 order by ctime desc limit 100");
		$timeOffset = 3600 * get_option('gmt_offset');
		foreach($results as &$rec){
			$rec['date'] = date('M d H:i:s', $rec['ctime'] + $timeOffset);
			if(strpos($rec['msg'], 'SUM_PREP:') === 0){
				break;
			}
		}
		return array_reverse($results);
	}

	/**
	 * @return string
	 */
	public function getGooglePattern() {
		return $this->googlePattern;
	}

}

/**
 *
 */
class wfUserIPRange {

	/**
	 * @var string|null
	 */
	private $ip_string;

	/**
	 * @param string|null $ip_string
	 */
	public function __construct($ip_string = null) {
		$this->setIPString($ip_string);
	}

	/**
	 * Check if the supplied IP address is within the user supplied range.
	 *
	 * @param string $ip
	 * @return bool
	 */
	public function isIPInRange($ip) {
		$ip_string = $this->getIPString();

		// IPv4 range
		if (strpos($ip_string, '.') !== false && strpos($ip, '.') !== false) {
			if (preg_match('/\[\d+\-\d+\]/', $ip_string)) {
				$IPparts = explode('.', $ip);
				$whiteParts = explode('.', $ip_string);
				$mismatch = false;
				for ($i = 0; $i <= 3; $i++) {
					if (preg_match('/^\[(\d+)\-(\d+)\]$/', $whiteParts[$i], $m)) {
						if ($IPparts[$i] < $m[1] || $IPparts[$i] > $m[2]) {
							$mismatch = true;
						}
					} else if ($whiteParts[$i] != $IPparts[$i]) {
						$mismatch = true;
					}
				}
				if ($mismatch === false) {
					return true; // Is whitelisted because we did not get a mismatch
				}
			} else if ($ip_string == $ip) {
				return true;
			}

		// IPv6 range
		} else if (strpos($ip_string, ':') !== false && strpos($ip, ':') !== false) {
			if (preg_match('/\[[a-f0-9]+\-[a-f0-9]+\]/', $ip_string)) {
				$IPparts = explode(':', strtolower(wfUtils::expandIPv6Address($ip)));
				$whiteParts = explode(':', strtolower(self::expandIPv6Range($ip_string)));
				$mismatch = false;
				for ($i = 0; $i <= 7; $i++) {
					if (preg_match('/^\[([a-f0-9]+)\-([a-f0-9]+)\]$/i', $whiteParts[$i], $m)) {
						$ip_group = hexdec($IPparts[$i]);
						$range_group_from = hexdec($m[1]);
						$range_group_to = hexdec($m[2]);
						if ($ip_group < $range_group_from || $ip_group > $range_group_to) {
							$mismatch = true;
							break;
						}
					} else if ($whiteParts[$i] != $IPparts[$i]) {
						$mismatch = true;
						break;
					}
				}
				if ($mismatch === false) {
					return true; // Is whitelisted because we did not get a mismatch
				}
			} else if ($ip_string == $ip) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Return a set of where clauses to use in MySQL.
	 *
	 * @param string $column
	 * @return false|null|string
	 */
	public function toSQL($column = 'ip') {
		/** @var wpdb $wpdb */
		global $wpdb;
		$ip_string = $this->getIPString();

		if (strpos($ip_string, '.') !== false && preg_match('/\[\d+\-\d+\]/', $ip_string)) {
			$whiteParts = explode('.', $ip_string);
			$sql = "(SUBSTR($column, 1, 12) = LPAD(CHAR(0xff, 0xff), 12, CHAR(0)) AND ";

			for ($i = 0, $j = 24; $i <= 3; $i++, $j -= 8) {
				// MySQL can only perform bitwise operations on integers
				$conv = sprintf('CAST(CONV(HEX(SUBSTR(%s, 13, 8)), 16, 10) as UNSIGNED INTEGER)', $column);
				if (preg_match('/^\[(\d+)\-(\d+)\]$/', $whiteParts[$i], $m)) {
					$sql .= $wpdb->prepare("$conv >> $j & 0xFF BETWEEN %d AND %d", $m[1], $m[2]);
				} else {
					$sql .= $wpdb->prepare("$conv >> $j & 0xFF = %d", $whiteParts[$i]);
				}
				$sql .= ' AND ';
			}
			$sql = substr($sql, 0, -5) . ')';
			return $sql;

		} else if (strpos($ip_string, ':') !== false && preg_match('/\[[a-f0-9]+\-[a-f0-9]+\]/', $ip_string)) {
			$whiteParts = explode(':', strtolower(self::expandIPv6Range($ip_string)));
			$sql = '(';

			for ($i = 0; $i <= 7; $i++) {
				// MySQL can only perform bitwise operations on integers
				$conv = sprintf('CAST(CONV(HEX(SUBSTR(%s, %d, 8)), 16, 10) as UNSIGNED INTEGER)', $column, $i < 4 ? 1 : 9);
				$j = 16 * (3 - ($i % 4));
				if (preg_match('/^\[([a-f0-9]+)\-([a-f0-9]+)\]$/', $whiteParts[$i], $m)) {
					$sql .= $wpdb->prepare("$conv >> $j & 0xFFFF BETWEEN 0x%x AND 0x%x", hexdec($m[1]), hexdec($m[2]));
				} else {
					$sql .= $wpdb->prepare("$conv >> $j & 0xFFFF = 0x%x", hexdec($whiteParts[$i]));
				}
				$sql .= ' AND ';
			}
			$sql = substr($sql, 0, -5) . ')';
			return $sql;
		}
		return $wpdb->prepare("($column = %s)", wfUtils::inet_pton($ip_string));
	}

	/**
	 * Expand a compressed printable range representation of an IPv6 address.
	 *
	 * @todo Hook up exceptions for better error handling.
	 * @todo Allow IPv4 mapped IPv6 addresses (::ffff:192.168.1.1).
	 * @param string $ip_range
	 * @return string
	 */
	public static function expandIPv6Range($ip_range) {
		$colon_count = substr_count($ip_range, ':');
		$dbl_colon_count = substr_count($ip_range, '::');
		if ($dbl_colon_count > 1) {
			return false;
		}
		$dbl_colon_pos = strpos($ip_range, '::');
		if ($dbl_colon_pos !== false) {
			$ip_range = str_replace('::', str_repeat(':0000',
					(($dbl_colon_pos === 0 || $dbl_colon_pos === strlen($ip_range) - 2) ? 9 : 8) - $colon_count) . ':', $ip_range);
			$ip_range = trim($ip_range, ':');
		}
		$colon_count = substr_count($ip_range, ':');
		if ($colon_count != 7) {
			return false;
		}

		$groups = explode(':', $ip_range);
		$expanded = '';
		foreach ($groups as $group) {
			if (preg_match('/\[([a-f0-9]{1,4})\-([a-f0-9]{1,4})\]/i', $group, $matches)) {
				$expanded .= sprintf('[%s-%s]', str_pad(strtolower($matches[1]), 4, '0', STR_PAD_LEFT), str_pad(strtolower($matches[2]), 4, '0', STR_PAD_LEFT)) . ':';
			} else if (preg_match('/[a-f0-9]{1,4}/i', $group)) {
				$expanded .= str_pad(strtolower($group), 4, '0', STR_PAD_LEFT) . ':';
			} else {
				return false;
			}
		}
		return trim($expanded, ':');
	}

	/**
	 * @return bool
	 */
	public function isValidRange() {
		return $this->isValidIPv4Range() || $this->isValidIPv6Range();
	}

	/**
	 * @return bool
	 */
	public function isValidIPv4Range() {
		$ip_string = $this->getIPString();
		if (preg_match_all('/(\d+)/', $ip_string, $matches) > 0) {
			foreach ($matches[1] as $match) {
				$group = (int) $match;
				if ($group > 255 || $group < 0) {
					return false;
				}
			}
		}

		$group_regex = '([0-9]{1,3}|\[[0-9]{1,3}\-[0-9]{1,3}\])';
		return preg_match('/^' . str_repeat("$group_regex.", 3) . $group_regex . '$/i', $ip_string) > 0;
	}

	/**
	 * @return bool
	 */
	public function isValidIPv6Range() {
		$ip_string = $this->getIPString();
		if (strpos($ip_string, '::') !== false) {
			$ip_string = self::expandIPv6Range($ip_string);
		}
		if (!$ip_string) {
			return false;
		}
		$group_regex = '([a-f0-9]{1,4}|\[[a-f0-9]{1,4}\-[a-f0-9]{1,4}\])';
		return preg_match('/^' . str_repeat("$group_regex:", 7) . $group_regex . '$/i', $ip_string) > 0;
	}



	/**
	 * @return string|null
	 */
	public function getIPString() {
		return $this->ip_string;
	}

	/**
	 * @param string|null $ip_string
	 */
	public function setIPString($ip_string) {
		$this->ip_string = $ip_string;
	}
}

?>
