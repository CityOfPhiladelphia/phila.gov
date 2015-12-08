<?php
class wfConfig {
	public static $diskCache = array();
	private static $diskCacheDisabled = false; //enables if we detect a write fail so we don't keep calling stat()
	private static $cacheDisableCheckDone = false;
	private static $table = false;
	private static $cache = array();
	private static $DB = false;
	private static $tmpFileHeader = "<?php\n/* Wordfence temporary file security header */\necho \"Nothing to see here!\\n\"; exit(0);\n?>";
	private static $tmpDirCache = false;
	public static $securityLevels = array(
		array( //level 0
			"checkboxes" => array(
				"alertOn_critical" => false,
				"alertOn_update" => false,
				"alertOn_warnings" => false,
				"alertOn_throttle" => false,
				"alertOn_block" => false,
				"alertOn_loginLockout" => false,
				"alertOn_lostPasswdForm" => false,
				"alertOn_adminLogin" => false,
				"alertOn_nonAdminLogin" => false,
				"liveTrafficEnabled" => true,
				"advancedCommentScanning" => false,
				"checkSpamIP" => false,
				"spamvertizeCheck" => false,
				"liveTraf_ignorePublishers" => true,
				//"perfLoggingEnabled" => false,
				"scheduledScansEnabled" => false,
				"scansEnabled_public" => false,
				"scansEnabled_heartbleed" => true,
				"scansEnabled_core" => false,
				"scansEnabled_themes" => false,
				"scansEnabled_plugins" => false,
				"scansEnabled_malware" => false,
				"scansEnabled_fileContents" => false,
				"scansEnabled_database" => false,
				"scansEnabled_posts" => false,
				"scansEnabled_comments" => false,
				"scansEnabled_passwds" => false,
				"scansEnabled_diskSpace" => false,
				"scansEnabled_options" => false,
				"scansEnabled_dns" => false,
				"scansEnabled_scanImages" => false,
				"scansEnabled_highSense" => false,
				"scansEnabled_oldVersions" => false,
				"firewallEnabled" => false,
				"blockFakeBots" => false,
				"autoBlockScanners" => false,
				"loginSecurityEnabled" => false,
				"loginSec_lockInvalidUsers" => false,
				"loginSec_maskLoginErrors" => false,
				"loginSec_blockAdminReg" => false,
				"loginSec_disableAuthorScan" => false,
				"other_hideWPVersion" => false,
				"other_noAnonMemberComments" => false,
				"other_blockBadPOST" => false,
				"other_scanComments" => false,
				"other_pwStrengthOnUpdate" => false,
				"other_WFNet" => true,
				"other_scanOutside" => false,
				"deleteTablesOnDeact" => false,
				"autoUpdate" => false,
				"disableCookies" => false,
				"startScansRemotely" => false,
				"disableConfigCaching" => false,
				"addCacheComment" => false,
				"disableCodeExecutionUploads" => false,
				"allowHTTPSCaching" => false,
				"debugOn" => false,
				'email_summary_enabled' => true,
				'email_summary_dashboard_widget_enabled' => true,
				'ssl_verify' => true,
			),
			"otherParams" => array(
				'securityLevel' => '0',
				"alertEmails" => "", "liveTraf_ignoreUsers" => "", "liveTraf_ignoreIPs" => "", "liveTraf_ignoreUA" => "",  "apiKey" => "", "maxMem" => '256', 'scan_exclude' => '', 'whitelisted' => '', 'bannedURLs' => '', 'maxExecutionTime' => '', 'howGetIPs' => '', 'actUpdateInterval' => '', 'alert_maxHourly' => 0, 'loginSec_userBlacklist' => '',
				"neverBlockBG" => "neverBlockVerified",
				"loginSec_countFailMins" => "5",
				"loginSec_lockoutMins" => "5",
				'loginSec_strongPasswds' => '',
				'loginSec_maxFailures' => "500",
				'loginSec_maxForgotPasswd' => "500",
				'maxGlobalRequests' => "DISABLED",
				'maxGlobalRequests_action' => "throttle",
				'maxRequestsCrawlers' => "DISABLED",
				'maxRequestsCrawlers_action' => "throttle",
				'maxRequestsHumans' => "DISABLED",
				'maxRequestsHumans_action' => "throttle",
				'max404Crawlers' => "DISABLED",
				'max404Crawlers_action' => "throttle",
				'max404Humans' => "DISABLED",
				'max404Humans_action' => "throttle",
				'maxScanHits' => "DISABLED",
				'maxScanHits_action' => "throttle",
				'blockedTime' => "300",
				'email_summary_interval' => 'biweekly',
				'email_summary_excluded_directories' => 'wp-content/cache,wp-content/wfcache,wp-content/plugins/wordfence/tmp',
				'allowed404s' => "/favicon.ico\n/apple-touch-icon*.png\n/*@2x.png",
			)
		),
		array( //level 1
			"checkboxes" => array(
				"alertOn_critical" => true,
				"alertOn_update" => false,
				"alertOn_warnings" => false,
				"alertOn_throttle" => false,
				"alertOn_block" => true,
				"alertOn_loginLockout" => true,
				"alertOn_lostPasswdForm" => false,
				"alertOn_adminLogin" => true,
				"alertOn_nonAdminLogin" => false,
				"liveTrafficEnabled" => true,
				"advancedCommentScanning" => false,
				"checkSpamIP" => false,
				"spamvertizeCheck" => false,
				"liveTraf_ignorePublishers" => true,
				//"perfLoggingEnabled" => false,
				"scheduledScansEnabled" => true,
				"scansEnabled_public" => false,
				"scansEnabled_heartbleed" => true,
				"scansEnabled_core" => true,
				"scansEnabled_themes" => false,
				"scansEnabled_plugins" => false,
				"scansEnabled_malware" => true,
				"scansEnabled_fileContents" => true,
				"scansEnabled_database" => true,
				"scansEnabled_posts" => true,
				"scansEnabled_comments" => true,
				"scansEnabled_passwds" => true,
				"scansEnabled_diskSpace" => true,
				"scansEnabled_options" => true,
				"scansEnabled_dns" => true,
				"scansEnabled_scanImages" => false,
				"scansEnabled_highSense" => false,
				"scansEnabled_oldVersions" => true,
				"firewallEnabled" => true,
				"blockFakeBots" => false,
				"autoBlockScanners" => true,
				"loginSecurityEnabled" => true,
				"loginSec_lockInvalidUsers" => false,
				"loginSec_maskLoginErrors" => true,
				"loginSec_blockAdminReg" => true,
				"loginSec_disableAuthorScan" => true,
				"other_hideWPVersion" => true,
				"other_noAnonMemberComments" => true,
				"other_blockBadPOST" => false,
				"other_scanComments" => true,
				"other_pwStrengthOnUpdate" => true,
				"other_WFNet" => true,
				"other_scanOutside" => false,
				"deleteTablesOnDeact" => false,
				"autoUpdate" => false,
				"disableCookies" => false,
				"startScansRemotely" => false,
				"disableConfigCaching" => false,
				"addCacheComment" => false,
				"disableCodeExecutionUploads" => false,
				"allowHTTPSCaching" => false,
				"debugOn" => false,
				'email_summary_enabled' => true,
				'email_summary_dashboard_widget_enabled' => true,
				'ssl_verify' => true,
			),
			"otherParams" => array(
				'securityLevel' => '1',
				"alertEmails" => "", "liveTraf_ignoreUsers" => "", "liveTraf_ignoreIPs" => "", "liveTraf_ignoreUA" => "",  "apiKey" => "", "maxMem" => '256', 'scan_exclude' => '', 'whitelisted' => '', 'bannedURLs' => '', 'maxExecutionTime' => '', 'howGetIPs' => '', 'actUpdateInterval' => '', 'alert_maxHourly' => 0, 'loginSec_userBlacklist' => '',
				"neverBlockBG" => "neverBlockVerified",
				"loginSec_countFailMins" => "5",
				"loginSec_lockoutMins" => "5",
				'loginSec_strongPasswds' => 'pubs',
				'loginSec_maxFailures' => "50",
				'loginSec_maxForgotPasswd' => "50",
				'maxGlobalRequests' => "DISABLED",
				'maxGlobalRequests_action' => "throttle",
				'maxRequestsCrawlers' => "DISABLED",
				'maxRequestsCrawlers_action' => "throttle",
				'maxRequestsHumans' => "DISABLED",
				'maxRequestsHumans_action' => "throttle",
				'max404Crawlers' => "DISABLED",
				'max404Crawlers_action' => "throttle",
				'max404Humans' => "DISABLED",
				'max404Humans_action' => "throttle",
				'maxScanHits' => "DISABLED",
				'maxScanHits_action' => "throttle",
				'blockedTime' => "300",
				'email_summary_interval' => 'biweekly',
				'email_summary_excluded_directories' => 'wp-content/cache,wp-content/wfcache,wp-content/plugins/wordfence/tmp',
				'allowed404s' => "/favicon.ico\n/apple-touch-icon*.png\n/*@2x.png",
			)
		),
		array( //level 2
			"checkboxes" => array(
				"alertOn_critical" => true,
				"alertOn_update" => false,
				"alertOn_warnings" => true,
				"alertOn_throttle" => false,
				"alertOn_block" => true,
				"alertOn_loginLockout" => true,
				"alertOn_lostPasswdForm" => true,
				"alertOn_adminLogin" => true,
				"alertOn_nonAdminLogin" => false,
				"liveTrafficEnabled" => true,
				"advancedCommentScanning" => false,
				"checkSpamIP" => false,
				"spamvertizeCheck" => false,
				"liveTraf_ignorePublishers" => true,
				//"perfLoggingEnabled" => false,
				"scheduledScansEnabled" => true,
				"scansEnabled_public" => false,
				"scansEnabled_heartbleed" => true,
				"scansEnabled_core" => true,
				"scansEnabled_themes" => false,
				"scansEnabled_plugins" => false,
				"scansEnabled_malware" => true,
				"scansEnabled_fileContents" => true,
				"scansEnabled_database" => true,
				"scansEnabled_posts" => true,
				"scansEnabled_comments" => true,
				"scansEnabled_passwds" => true,
				"scansEnabled_diskSpace" => true,
				"scansEnabled_options" => true,
				"scansEnabled_dns" => true,
				"scansEnabled_scanImages" => false,
				"scansEnabled_highSense" => false,
				"scansEnabled_oldVersions" => true,
				"firewallEnabled" => true,
				"blockFakeBots" => false,
				"autoBlockScanners" => true,
				"loginSecurityEnabled" => true,
				"loginSec_lockInvalidUsers" => false,
				"loginSec_maskLoginErrors" => true,
				"loginSec_blockAdminReg" => true,
				"loginSec_disableAuthorScan" => true,
				"other_hideWPVersion" => true,
				"other_noAnonMemberComments" => true,
				"other_blockBadPOST" => false,
				"other_scanComments" => true,
				"other_pwStrengthOnUpdate" => true,
				"other_WFNet" => true,
				"other_scanOutside" => false,
				"deleteTablesOnDeact" => false,
				"autoUpdate" => false,
				"disableCookies" => false,
				"startScansRemotely" => false,
				"disableConfigCaching" => false,
				"addCacheComment" => false,
				"disableCodeExecutionUploads" => false,
				"allowHTTPSCaching" => false,
				"debugOn" => false,
				'email_summary_enabled' => true,
				'email_summary_dashboard_widget_enabled' => true,
				'ssl_verify' => true,
			),
			"otherParams" => array(
				'securityLevel' => '2',
				"alertEmails" => "", "liveTraf_ignoreUsers" => "", "liveTraf_ignoreIPs" => "", "liveTraf_ignoreUA" => "",  "apiKey" => "", "maxMem" => '256', 'scan_exclude' => '', 'whitelisted' => '', 'bannedURLs' => '', 'maxExecutionTime' => '', 'howGetIPs' => '', 'actUpdateInterval' => '', 'alert_maxHourly' => 0, 'loginSec_userBlacklist' => '',
				"neverBlockBG" => "neverBlockVerified",
				"loginSec_countFailMins" => "240",
				"loginSec_lockoutMins" => "240",
				'loginSec_strongPasswds' => 'pubs',
				'loginSec_maxFailures' => "20",
				'loginSec_maxForgotPasswd' => "20",
				'maxGlobalRequests' => "DISABLED",
				'maxGlobalRequests_action' => "throttle",
				'maxRequestsCrawlers' => "DISABLED",
				'maxRequestsCrawlers_action' => "throttle",
				'maxRequestsHumans' => "DISABLED",
				'maxRequestsHumans_action' => "throttle",
				'max404Crawlers' => "DISABLED",
				'max404Crawlers_action' => "throttle",
				'max404Humans' => "DISABLED",
				'max404Humans_action' => "throttle",
				'maxScanHits' => "DISABLED",
				'maxScanHits_action' => "throttle",
				'blockedTime' => "300",
				'email_summary_interval' => 'biweekly',
				'email_summary_excluded_directories' => 'wp-content/cache,wp-content/wfcache,wp-content/plugins/wordfence/tmp',
				'allowed404s' => "/favicon.ico\n/apple-touch-icon*.png\n/*@2x.png",
			)
		),
		array( //level 3
			"checkboxes" => array(
				"alertOn_critical" => true,
				"alertOn_update" => false,
				"alertOn_warnings" => true,
				"alertOn_throttle" => false,
				"alertOn_block" => true,
				"alertOn_loginLockout" => true,
				"alertOn_lostPasswdForm" => true,
				"alertOn_adminLogin" => true,
				"alertOn_nonAdminLogin" => false,
				"liveTrafficEnabled" => true,
				"advancedCommentScanning" => false,
				"checkSpamIP" => false,
				"spamvertizeCheck" => false,
				"liveTraf_ignorePublishers" => true,
				//"perfLoggingEnabled" => false,
				"scheduledScansEnabled" => true,
				"scansEnabled_public" => false,
				"scansEnabled_heartbleed" => true,
				"scansEnabled_core" => true,
				"scansEnabled_themes" => false,
				"scansEnabled_plugins" => false,
				"scansEnabled_malware" => true,
				"scansEnabled_fileContents" => true,
				"scansEnabled_database" => true,
				"scansEnabled_posts" => true,
				"scansEnabled_comments" => true,
				"scansEnabled_passwds" => true,
				"scansEnabled_diskSpace" => true,
				"scansEnabled_options" => true,
				"scansEnabled_dns" => true,
				"scansEnabled_scanImages" => false,
				"scansEnabled_highSense" => false,
				"scansEnabled_oldVersions" => true,
				"firewallEnabled" => true,
				"blockFakeBots" => false,
				"autoBlockScanners" => true,
				"loginSecurityEnabled" => true,
				"loginSec_lockInvalidUsers" => false,
				"loginSec_maskLoginErrors" => true,
				"loginSec_blockAdminReg" => true,
				"loginSec_disableAuthorScan" => true,
				"other_hideWPVersion" => true,
				"other_noAnonMemberComments" => true,
				"other_blockBadPOST" => false,
				"other_scanComments" => true,
				"other_pwStrengthOnUpdate" => true,
				"other_WFNet" => true,
				"other_scanOutside" => false,
				"deleteTablesOnDeact" => false,
				"autoUpdate" => false,
				"disableCookies" => false,
				"startScansRemotely" => false,
				"disableConfigCaching" => false,
				"addCacheComment" => false,
				"disableCodeExecutionUploads" => false,
				"allowHTTPSCaching" => false,
				"debugOn" => false,
				'email_summary_enabled' => true,
				'email_summary_dashboard_widget_enabled' => true,
				'ssl_verify' => true,
			),
			"otherParams" => array(
				'securityLevel' => '3',
				"alertEmails" => "", "liveTraf_ignoreUsers" => "", "liveTraf_ignoreIPs" => "", "liveTraf_ignoreUA" => "",  "apiKey" => "", "maxMem" => '256', 'scan_exclude' => '', 'whitelisted' => '', 'bannedURLs' => '', 'maxExecutionTime' => '', 'howGetIPs' => '', 'actUpdateInterval' => '', 'alert_maxHourly' => 0, 'loginSec_userBlacklist' => '',
				"neverBlockBG" => "neverBlockVerified",
				"loginSec_countFailMins" => "1440",
				"loginSec_lockoutMins" => "1440",
				'loginSec_strongPasswds' => 'all',
				'loginSec_maxFailures' => "10",
				'loginSec_maxForgotPasswd' => "10",
				'maxGlobalRequests' => "960",
				'maxGlobalRequests_action' => "throttle",
				'maxRequestsCrawlers' => "960",
				'maxRequestsCrawlers_action' => "throttle",
				'maxRequestsHumans' => "60",
				'maxRequestsHumans_action' => "throttle",
				'max404Crawlers' => "60",
				'max404Crawlers_action' => "throttle",
				'max404Humans' => "60",
				'max404Humans_action' => "throttle",
				'maxScanHits' => "30",
				'maxScanHits_action' => "throttle",
				'blockedTime' => "1800",
				'email_summary_interval' => 'biweekly',
				'email_summary_excluded_directories' => 'wp-content/cache,wp-content/wfcache,wp-content/plugins/wordfence/tmp',
				'allowed404s' => "/favicon.ico\n/apple-touch-icon*.png\n/*@2x.png",
			)
		),
		array( //level 4
			"checkboxes" => array(
				"alertOn_critical" => true,
				"alertOn_update" => false,
				"alertOn_warnings" => true,
				"alertOn_throttle" => false,
				"alertOn_block" => true,
				"alertOn_loginLockout" => true,
				"alertOn_lostPasswdForm" => true,
				"alertOn_adminLogin" => true,
				"alertOn_nonAdminLogin" => false,
				"liveTrafficEnabled" => true,
				"advancedCommentScanning" => false,
				"checkSpamIP" => false,
				"spamvertizeCheck" => false,
				"liveTraf_ignorePublishers" => true,
				//"perfLoggingEnabled" => false,
				"scheduledScansEnabled" => true,
				"scansEnabled_public" => false,
				"scansEnabled_heartbleed" => true,
				"scansEnabled_core" => true,
				"scansEnabled_themes" => false,
				"scansEnabled_plugins" => false,
				"scansEnabled_malware" => true,
				"scansEnabled_fileContents" => true,
				"scansEnabled_database" => true,
				"scansEnabled_posts" => true,
				"scansEnabled_comments" => true,
				"scansEnabled_passwds" => true,
				"scansEnabled_diskSpace" => true,
				"scansEnabled_options" => true,
				"scansEnabled_dns" => true,
				"scansEnabled_scanImages" => false,
				"scansEnabled_highSense" => false,
				"scansEnabled_oldVersions" => true,
				"firewallEnabled" => true,
				"blockFakeBots" => true,
				"autoBlockScanners" => true,
				"loginSecurityEnabled" => true,
				"loginSec_lockInvalidUsers" => true,
				"loginSec_maskLoginErrors" => true,
				"loginSec_blockAdminReg" => true,
				"loginSec_disableAuthorScan" => true,
				"other_hideWPVersion" => true,
				"other_noAnonMemberComments" => true,
				"other_blockBadPOST" => false,
				"other_scanComments" => true,
				"other_pwStrengthOnUpdate" => true,
				"other_WFNet" => true,
				"other_scanOutside" => false,
				"deleteTablesOnDeact" => false,
				"autoUpdate" => false,
				"disableCookies" => false,
				"startScansRemotely" => false,
				"disableConfigCaching" => false,
				"addCacheComment" => false,
				"disableCodeExecutionUploads" => false,
				"allowHTTPSCaching" => false,
				"debugOn" => false,
				'email_summary_enabled' => true,
				'email_summary_dashboard_widget_enabled' => true,
				'ssl_verify' => true,
			),
			"otherParams" => array(
				'securityLevel' => '4',
				"alertEmails" => "", "liveTraf_ignoreUsers" => "", "liveTraf_ignoreIPs" => "", "liveTraf_ignoreUA" => "",  "apiKey" => "", "maxMem" => '256', 'scan_exclude' => '', 'whitelisted' => '', 'bannedURLs' => '', 'maxExecutionTime' => '', 'howGetIPs' => '', 'actUpdateInterval' => '', 'alert_maxHourly' => 0, 'loginSec_userBlacklist' => '',
				"neverBlockBG" => "neverBlockVerified",
				"loginSec_countFailMins" => "1440",
				"loginSec_lockoutMins" => "1440",
				'loginSec_strongPasswds' => 'all',
				'loginSec_maxFailures' => "5",
				'loginSec_maxForgotPasswd' => "5",
				'maxGlobalRequests' => "960",
				'maxGlobalRequests_action' => "throttle",
				'maxRequestsCrawlers' => "960",
				'maxRequestsCrawlers_action' => "throttle",
				'maxRequestsHumans' => "30",
				'maxRequestsHumans_action' => "block",
				'max404Crawlers' => "30",
				'max404Crawlers_action' => "block",
				'max404Humans' => "60",
				'max404Humans_action' => "block",
				'maxScanHits' => "10",
				'maxScanHits_action' => "block",
				'blockedTime' => "7200",
				'email_summary_interval' => 'biweekly',
				'email_summary_excluded_directories' => 'wp-content/cache,wp-content/wfcache,wp-content/plugins/wordfence/tmp',
				'allowed404s' => "/favicon.ico\n/apple-touch-icon*.png\n/*@2x.png",
			)
		)
	);
	public static function setDefaults(){
		foreach(self::$securityLevels[2]['checkboxes'] as $key => $val){
			if(self::get($key) === false){
				self::set($key, $val ? '1' : '0');
			}
		}
		foreach(self::$securityLevels[2]['otherParams'] as $key => $val){
			if(self::get($key) === false){
				self::set($key, $val);
			}
		}
		self::set('encKey', substr(wfUtils::bigRandomHex(),0 ,16) );
		if(self::get('maxMem', false) === false ){
			self::set('maxMem', '256');
		}
		if(self::get('other_scanOutside', false) === false){
			self::set('other_scanOutside', 0);
		}

		if (self::get('email_summary_enabled')) {
			wfActivityReport::scheduleCronJob();
		} else {
			wfActivityReport::disableCronJob();
		}
	}
	public static function getExportableOptionsKeys(){
		$ret = array();
		foreach(self::$securityLevels[2]['checkboxes'] as $key => $val){
			$ret[] = $key;
		}
		foreach(self::$securityLevels[2]['otherParams'] as $key => $val){
			if($key != 'apiKey'){
				$ret[] = $key;
			}
		}
		foreach(array('cbl_action', 'cbl_countries', 'cbl_redirURL', 'cbl_loggedInBlocked', 'cbl_loginFormBlocked', 'cbl_restOfSiteBlocked', 'cbl_bypassRedirURL', 'cbl_bypassRedirDest', 'cbl_bypassViewURL') as $key){
			$ret[] = $key;
		}
		return $ret;
	}
	public static function parseOptions(){
		$ret = array();
		foreach(self::$securityLevels[2]['checkboxes'] as $key => $val){ //value is not used. We just need the keys for validation
			$ret[$key] = isset($_POST[$key]) ? '1' : '0';
		}
		foreach(self::$securityLevels[2]['otherParams'] as $key => $val){
			if(isset($_POST[$key])){
				$ret[$key] = stripslashes($_POST[$key]);
			} else {
				error_log("Missing options param \"$key\" when parsing parameters.");
			}
		}
		/* for debugging only:
		foreach($_POST as $key => $val){
			if($key != 'action' && $key != 'nonce' && (! array_key_exists($key, self::$checkboxes)) && (! array_key_exists($key, self::$otherParams)) ){
				error_log("Unrecognized option: $key");
			}
		}
		*/
		return $ret;
	}
	public static function setArray($arr){
		foreach($arr as $key => $val){
			self::set($key, $val);
		}
	}
	public static function clearCache(){
		self::$cache = array();
	}
	public static function getHTML($key){
		return esc_html(self::get($key));
	}
	public static function inc($key){
		$val = self::get($key, false);
		if(! $val){
			$val = 0;
		}
		self::set($key, $val + 1);
	}
	public static function set($key, $val){
		if($key == 'disableConfigCaching'){
			self::getDB()->queryWrite("insert into " . self::table() . " (name, val) values ('%s', '%s') ON DUPLICATE KEY UPDATE val='%s'", $key, $val, $val);
			return;
		}
	
		if(is_array($val)){
			$msg = "wfConfig::set() got an array as second param with key: $key and value: " . var_export($val, true);
			wordfence::status(1, 'error', $msg);
			return;
		}

		self::getDB()->queryWrite("insert into " . self::table() . " (name, val) values ('%s', '%s') ON DUPLICATE KEY UPDATE val='%s'", $key, $val, $val);
		self::$cache[$key] = $val;
		self::clearDiskCache();
	}
	private static function getCacheFile(){
		return wfUtils::getPluginBaseDir() . 'wordfence/tmp/configCache.php';
	}
	public static function clearDiskCache(){
		//When we write to the cache we just trash the whole cache on the first write. Second write won't get called because we've disabled the cache.
		// Neither will anything be loaded from the cache for the rest of this request and it also won't be updated.
		// On the next request presumably we won't be doing a set() and so the cache will be populated again and continue to be used 
		// for each request as long as set() isn't called which would start the whole process over again.
		if(! self::$diskCacheDisabled){ //We haven't had a write error to cache (so the cache is working) and clearDiskCache has not been called already
			$cacheFile = self::getCacheFile();
			@unlink($cacheFile);
			wfConfig::$diskCache = array();
		}
		self::$diskCacheDisabled = true;
	}
	public static function get($key, $default = false){
		if($key == 'disableConfigCaching'){
			$val = self::getDB()->querySingle("select val from " . self::table() . " where name='%s'", $key);
			return $val;
		}

		if(! self::$cacheDisableCheckDone){
			self::$cacheDisableCheckDone = true;
			$cachingDisabledSetting = self::getDB()->querySingle("select val from " . self::table() . " where name='%s'", 'disableConfigCaching');
			if($cachingDisabledSetting == '1'){
				self::$diskCacheDisabled = true;
			}
		}

		if(!array_key_exists($key, self::$cache)){ 
			$val = self::loadFromDiskCache($key);
			//$val = self::getDB()->querySingle("select val from " . self::table() . " where name='%s'", $key);
			self::$cache[$key] = $val;
		}
		$val = self::$cache[$key];
		return $val !== null ? $val : $default;
	}
	public static function loadFromDiskCache($key){
		if(! self::$diskCacheDisabled){
			if(isset(wfConfig::$diskCache[$key])){
				return wfConfig::$diskCache[$key];
			}

			$cacheFile = self::getCacheFile();
			if(is_file($cacheFile)){
				//require($cacheFile); //will only require the file on first parse through this code. But we dynamically update the var and update the file with each get
				try {
					$cont = @file_get_contents($cacheFile);
					if(strpos($cont, '<?php') === 0){ //"<?php die() XX"
						$cont = substr($cont, strlen(self::$tmpFileHeader));
						wfConfig::$diskCache = @unserialize($cont);
						if(isset(wfConfig::$diskCache) && is_array(wfConfig::$diskCache) && isset(wfConfig::$diskCache[$key])){
							return wfConfig::$diskCache[$key];
						}
					} //Else don't return a cached value because this is an old file without the php header so we're going to rewrite it. 
				} catch(Exception $err){ } //file_get or unserialize may fail, so just fail quietly.
			}
		}
		$val = self::getDB()->querySingle("select val from " . self::table() . " where name='%s'", $key);
		if(self::$diskCacheDisabled){ 
			return $val; 
		}
		wfConfig::$diskCache[$key] = isset($val) ? $val : '';
		try {
			$bytesWritten = @file_put_contents($cacheFile, self::$tmpFileHeader . serialize(wfConfig::$diskCache), LOCK_EX);
		} catch(Exception $err2){}
		if(! $bytesWritten){
			self::$diskCacheDisabled = true;
		}
		return $val;
	}
	public static function get_ser($key, $default, $canUseDisk = false){ //When using disk, reading a value deletes it.
		//If we can use disk, check if there are any values stored on disk first and read them instead of the DB if there are values
		if($canUseDisk){
			$filename = 'wordfence_tmpfile_' . $key . '.php';
			$dir = self::getTempDir();
			if($dir){
				$obj = false;
				$fullFile = $dir . $filename;
				if(file_exists($fullFile)){
					wordfence::status(4, 'info', "Loading serialized data from file $fullFile");
					$obj = unserialize(substr(file_get_contents($fullFile), strlen(self::$tmpFileHeader))); //Strip off security header and unserialize
					if(! $obj){
						wordfence::status(2, 'error', "Could not unserialize file $fullFile");
					}
					self::deleteOldTempFile($fullFile);
				}
				if($obj){ //If we managed to deserialize something, clean ALL tmp dirs of this file and return obj
					return $obj;
				}
			}
		}

		$res = self::getDB()->querySingle("select val from " . self::table() . " where name=%s", $key);
		self::getDB()->flush(); //clear cache
		if($res){
			return unserialize($res);
		}
		return $default;
	}
	public static function set_ser($key, $val, $canUseDisk = false){
		//We serialize some very big values so this is memory efficient. We don't make any copies of $val and don't use ON DUPLICATE KEY UPDATE
		// because we would have to concatenate $val twice into the query which could also exceed max packet for the mysql server
		$serialized = serialize($val);
		$tempFilename = 'wordfence_tmpfile_' . $key . '.php';
		if((strlen($serialized) * 1.1) > self::getDB()->getMaxAllowedPacketBytes()){ //If it's greater than max_allowed_packet + 10% for escaping and SQL
			if($canUseDisk){
				$dir = self::getTempDir();
				$potentialDirs = self::getPotentialTempDirs();
				if($dir){
					$fullFile = $dir . $tempFilename;
					self::deleteOldTempFile($fullFile);
					$fh = fopen($fullFile, 'w');
					if($fh){ 
						wordfence::status(4, 'info', "Serialized data for $key is " . strlen($serialized) . " bytes and is greater than max_allowed packet so writing it to disk file: " . $fullFile);
					} else {
						wordfence::status(1, 'error', "Your database doesn't allow big packets so we have to use files to store temporary data and Wordfence can't find a place to write them. Either ask your admin to increase max_allowed_packet on your MySQL database, or make one of the following directories writable by your web server: " . implode(', ', $potentialDirs));
						return false;
					}
					fwrite($fh, self::$tmpFileHeader);
					fwrite($fh, $serialized);
					fclose($fh);
					return true;
				} else {
					wordfence::status(1, 'error', "Your database doesn't allow big packets so we have to use files to store temporary data and Wordfence can't find a place to write them. Either ask your admin to increase max_allowed_packet on your MySQL database, or make one of the following directories writable by your web server: " . implode(', ', $potentialDirs));
					return false;
				}
					
			} else {
				wordfence::status(1, 'error', "Wordfence tried to save a variable with name '$key' and your database max_allowed_packet is set to be too small. This particular variable can't be saved to disk. Please ask your administrator to increase max_allowed_packet. Thanks.");
				return false;
			}
		} else {
			//Delete temp files on disk or else the DB will be written to but get_ser will see files on disk and read them instead
			$tempDir = self::getTempDir();
			if($tempDir){
				self::deleteOldTempFile($tempDir . $tempFilename);
			}
			$exists = self::getDB()->querySingle("select name from " . self::table() . " where name='%s'", $key);
			if($exists){
				self::getDB()->queryWrite("update " . self::table() . " set val=%s where name=%s", $serialized, $key);
			} else {
				self::getDB()->queryWrite("insert IGNORE into " . self::table() . " (name, val) values (%s, %s)", $key, $serialized);
			}
		}
		self::getDB()->flush();
		return true;
	}
	private static function deleteOldTempFile($filename){
		if(file_exists($filename)){
			@unlink($filename);
		}
	}
	public static function getTempDir(){
		if(! self::$tmpDirCache){
			$dirs = self::getPotentialTempDirs();
			$finalDir = 'notmp';
			wfUtils::errorsOff();
			foreach($dirs as $dir){
				$dir = rtrim($dir, '/') . '/';
				$fh = @fopen($dir . 'wftmptest.txt', 'w');
				if(! $fh){ continue; }
				$bytes = @fwrite($fh, 'test');
				if($bytes != 4){ @fclose($fh); continue; }
				@fclose($fh);
				if(! @unlink($dir . 'wftmptest.txt')){ continue; }
				$finalDir = $dir;
				break;
			}
			wfUtils::errorsOn();
			self::$tmpDirCache = $finalDir;
		}
		if(self::$tmpDirCache == 'notmp'){
			return false;
		} else {
			return self::$tmpDirCache;
		}
	}
	private static function getPotentialTempDirs() {
		return array(wfUtils::getPluginBaseDir() . 'wordfence/tmp/', sys_get_temp_dir(), ABSPATH . 'wp-content/uploads/');
	}
	public static function f($key){
		echo esc_attr(self::get($key));
	}
	public static function cbp($key){
		if(self::get('isPaid') && self::get($key)){
			echo ' checked ';
		}
	}
	public static function cb($key){
		if(self::get($key)){
			echo ' checked ';
		}
	}
	public static function sel($key, $val, $isDefault = false){
		if((! self::get($key)) && $isDefault){ echo ' selected '; }
		if(self::get($key) == $val){ echo ' selected '; }
	}
	public static function getArray(){
		$q = self::getDB()->querySelect("select name, val from " . self::table());
		foreach($q as $row){
			self::$cache[$row['name']] = $row['val'];
		}
		return self::$cache;
	}
	private static function getDB(){
		if(! self::$DB){ 
			self::$DB = new wfDB();
		}
		return self::$DB;
	}
	private static function table(){
		if(! self::$table){
			global $wpdb;
			self::$table = $wpdb->base_prefix . 'wfConfig';
		}
		return self::$table;
	}
	public static function haveAlertEmails(){
		$emails = self::getAlertEmails();
		return sizeof($emails) > 0 ? true : false;
	}
	public static function getAlertEmails(){
		$dat = explode(',', self::get('alertEmails'));
		$emails = array();
		foreach($dat as $email){
			if(preg_match('/\@/', $email)){
				$emails[] = trim($email);
			}
		}
		return $emails;
	}
	public static function getAlertLevel(){
		if(self::get('alertOn_warnings')){
			return 2;
		} else if(self::get('alertOn_critical')){
			return 1;
		} else {
			return 0;
		}
	}
	public static function liveTrafficEnabled(){
		if( (! self::get('liveTrafficEnabled')) || self::get('cacheType') == 'falcon' || self::get('cacheType') == 'php'){ return false; }
		return true;
	}
	public static function enableAutoUpdate(){
		wfConfig::set('autoUpdate', '1');
		wp_clear_scheduled_hook('wordfence_daily_autoUpdate');
		if (is_main_site()) {
			wp_schedule_event(time(), 'daily', 'wordfence_daily_autoUpdate');
		}
	}
	public static function disableAutoUpdate(){
		wfConfig::set('autoUpdate', '0');	
		wp_clear_scheduled_hook('wordfence_daily_autoUpdate');
	}
	public static function autoUpdate(){
		try {
			if(getenv('noabort') != '1' && stristr($_SERVER['SERVER_SOFTWARE'], 'litespeed') !== false){
				$lastEmail = self::get('lastLiteSpdEmail', false);
				if( (! $lastEmail) || (time() - (int)$lastEmail > (86400 * 30))){
					self::set('lastLiteSpdEmail', time());
					 wordfence::alert("Wordfence Upgrade not run. Please modify your .htaccess", "To preserve the integrity of your website we are not running Wordfence auto-update.\n" .
						"You are running the LiteSpeed web server which has been known to cause a problem with Wordfence auto-update.\n" .
						"Please go to your website now and make a minor change to your .htaccess to fix this.\n" .
						"You can find out how to make this change at:\n" .
						"https://support.wordfence.com/solution/articles/1000129050-running-wordfence-under-litespeed-web-server-and-preventing-process-killing-or\n" .
						"\nAlternatively you can disable auto-update on your website to stop receiving this message and upgrade Wordfence manually.\n",
						'127.0.0.1'
						);
				}
				return;
			}
			require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
			require_once(ABSPATH . 'wp-admin/includes/misc.php');
			/* We were creating show_message here so that WP did not write to STDOUT. This had the strange effect of throwing an error about redeclaring show_message function, but only when a crawler hit the site and triggered the cron job. Not a human. So we're now just require'ing misc.php which does generate output, but that's OK because it is a loopback cron request.  
			if(! function_exists('show_message')){ 
				function show_message($msg = 'null'){}
			}
			*/
			if(! defined('FS_METHOD')){ 
				define('FS_METHOD', 'direct'); //May be defined already and might not be 'direct' so this could cause problems. But we were getting reports of a warning that this is already defined, so this check added. 
			}
			require_once(ABSPATH . 'wp-includes/update.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			wp_update_plugins();
			ob_start();
			$upgrader = new Plugin_Upgrader();
			$upret = $upgrader->upgrade('wordfence/wordfence.php');
			if($upret){
				$cont = file_get_contents(WP_PLUGIN_DIR . '/wordfence/wordfence.php');
				if(wfConfig::get('alertOn_update') == '1' && preg_match('/Version: (\d+\.\d+\.\d+)/', $cont, $matches) ){
					wordfence::alert("Wordfence Upgraded to version " . $matches[1], "Your Wordfence installation has been upgraded to version " . $matches[1], '127.0.0.1');
				}
			}
			$output = @ob_get_contents();
			@ob_end_clean();
		} catch(Exception $e){}
	}
	
	/**
	 * .htaccess file contents to disable all script execution in a given directory.
	 */
	private static $_disable_scripts_htaccess = '# BEGIN Wordfence code execution protection
<IfModule mod_php5.c>
php_flag engine 0
</IfModule>

AddHandler cgi-script .php .phtml .php3 .pl .py .jsp .asp .htm .shtml .sh .cgi
Options -ExecCGI
# END Wordfence code execution protection
';
	
	private static function _uploadsHtaccessFilePath() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/.htaccess';
	}

	/**
	 * Add/Merge .htaccess file in the uploads directory to prevent code execution.
	 *
	 * @return bool
	 * @throws wfConfigException
	 */
	public static function disableCodeExecutionForUploads() {
		$uploads_htaccess_file_path = self::_uploadsHtaccessFilePath();
		$uploads_htaccess_has_content = false;
		if (file_exists($uploads_htaccess_file_path)) {
			$htaccess_contents = file_get_contents($uploads_htaccess_file_path);
			
			// htaccess exists and contains our htaccess code to disable script execution, nothing more to do
			if (strpos($htaccess_contents, self::$_disable_scripts_htaccess) !== false) {
				return true;
			}
			$uploads_htaccess_has_content = strlen(trim($htaccess_contents)) > 0;
		}
		if (@file_put_contents($uploads_htaccess_file_path, ($uploads_htaccess_has_content ? "\n\n" : "") . self::$_disable_scripts_htaccess, FILE_APPEND | LOCK_EX) === false) {
			throw new wfConfigException("Unable to save the .htaccess file needed to disable script execution in the uploads directory.  Please check your permissions on that directory.");
		}
		return true;
	}

	/**
	 * Remove script execution protections for our the .htaccess file in the uploads directory.
	 *
	 * @return bool
	 * @throws wfConfigException
	 */
	public static function removeCodeExecutionProtectionForUploads() {
		$uploads_htaccess_file_path = self::_uploadsHtaccessFilePath();
		if (file_exists($uploads_htaccess_file_path)) {
			$htaccess_contents = file_get_contents($uploads_htaccess_file_path);

			// Check that it is in the file
			if (strpos($htaccess_contents, self::$_disable_scripts_htaccess) !== false) {
				$htaccess_contents = str_replace(self::$_disable_scripts_htaccess, '', $htaccess_contents);

				$error_message = "Unable to remove code execution protections applied to the .htaccess file in the uploads directory.  Please check your permissions on that file.";
				if (strlen(trim($htaccess_contents)) === 0) {
					// empty file, remove it
					if (!@unlink($uploads_htaccess_file_path)) {
						throw new wfConfigException($error_message);
					}

				} elseif (@file_put_contents($uploads_htaccess_file_path, $htaccess_contents, LOCK_EX) === false) {
					throw new wfConfigException($error_message);
				}
			}
		}
		return true;
	}
}

class wfConfigException extends Exception {}

?>
