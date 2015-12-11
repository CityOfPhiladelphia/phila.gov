<?php
require_once('wordfenceConstants.php');
require_once('wfScanEngine.php');
require_once('wfCrawl.php');
require_once 'Diff.php';
require_once 'Diff/Renderer/Html/SideBySide.php';
require_once 'wfAPI.php';
require_once 'wfIssues.php';
require_once('wfDB.php');
require_once('wfUtils.php');
require_once('wfLog.php');
require_once('wfConfig.php');
require_once('wfSchema.php');
require_once('wfCache.php');
require_once('wfCrypt.php');
require_once 'wfView.php';
require_once 'wfHelperString.php';
require_once 'wfDirectoryIterator.php';
require_once 'wfUpdateCheck.php';
require_once 'wfActivityReport.php';
require_once 'wfHelperBin.php';

class wordfence {
	public static $printStatus = false;
	public static $wordfence_wp_version = false;
	/**
	 * @var WP_Error
	 */
	public static $authError;
	private static $passwordCodePattern = '/\s+(wf[a-z0-9]+)$/i';
	protected static $lastURLError = false;
	protected static $curlContent = "";
	protected static $curlDataWritten = 0;
	protected static $hasher = '';
	protected static $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	protected static $ignoreList = false;
	public static $newVisit = false;
	private static $wfLog = false;
	private static $hitID = 0;
	private static $debugOn = null;
	private static $runInstallCalled = false;
	public static $commentSpamItems = array();
	public static function installPlugin(){
		self::runInstall();
		//Used by MU code below
		update_option('wordfenceActivated', 1);
	}
	public static function uninstallPlugin(){
		//Check if caching is enabled and if it is, disable it and fix the .htaccess file.
		$cacheType = wfConfig::get('cacheType', false);
		if($cacheType == 'falcon'){
			wfCache::addHtaccessCode('remove');
			wfCache::updateBlockedIPs('remove');
			wfConfig::set('cacheType', false);

			//We currently don't clear the cache when plugin is disabled because it will take too long if done synchronously and won't work because plugin is disabled if done asynchronously.
			//wfCache::scheduleCacheClear();
		} else if($cacheType == 'php'){
			wfConfig::set('cacheType', false);
		}


		//Used by MU code below
		update_option('wordfenceActivated', 0);
		wp_clear_scheduled_hook('wordfence_daily_cron');
		wp_clear_scheduled_hook('wordfence_hourly_cron');
		wp_clear_scheduled_hook('wordfence_daily_autoUpdate');

		//Remove old legacy cron job if it exists
		wp_clear_scheduled_hook('wordfence_scheduled_scan');

		//Remove all scheduled scans.
		self::unscheduleAllScans();

		// Remove cron for email summary
		wfActivityReport::clearCronJobs();

		wfConfig::clearDiskCache();
		if(wfConfig::get('deleteTablesOnDeact')){
			$schema = new wfSchema();
			$schema->dropAll();
			foreach(array('wordfence_version', 'wordfenceActivated') as $opt){
				delete_option($opt);
			}
		}
	}
	public static function hourlyCron(){
		global $wpdb; $p = $wpdb->base_prefix;
		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());

		if(wfConfig::get('other_WFNet')){
			$wfdb = new wfDB();
			$q1 = $wfdb->querySelect("select URI from $p"."wfNet404s where ctime > unix_timestamp() - 3600 limit 1000");
			$URIs = array();
			foreach($q1 as $rec){
				$URIs[] = $rec['URI'];
			}
			$wfdb->truncate($p . "wfNet404s");
			if(sizeof($URIs) > 0){
				try {
					$api->call('send_net_404s', array(), array( 'URIs' => json_encode($URIs) ));
				} catch(Exception $e){
					//Ignore
				}
			}

			$q2 = $wfdb->querySelect("select IP from $p"."wfVulnScanners where ctime > unix_timestamp() - 3600");
			$scanCont = "";
			foreach($q2 as $rec){
				$scanCont .= $rec['IP'];
			}
			$wfdb->truncate($p . "wfVulnScanners");

			$q3 = $wfdb->querySelect("select IP from $p"."wfLockedOut where blockedTime > unix_timestamp() - 3600");
			$lockCont = "";
			foreach($q3 as $rec){
				$lockCont .= $rec['IP'];
			}
			if(strlen($lockCont) > 0 || strlen($scanCont) > 0){
				$cont = pack('N', strlen($lockCont) / 16) . $lockCont .
						pack('N', strlen($scanCont) / 16) . $scanCont;
				try {
					$resp = $api->binCall('get_net_bad_ips', $cont);
					if($resp['code'] == 200){
						$len = strlen($resp['data']);
						$reason = "WFSN: Blocked by Wordfence Security Network";
						$wfdb->queryWrite("delete from $p"."wfBlocks where wfsn=1 and permanent=0");
						if($len > 0 && $len % 16 == 0){
							for($i = 0; $i < $len; $i += 16){
								$ip_bin = substr($resp['data'], $i, 16);
								$IPStr = wfUtils::inet_ntop($ip_bin);
								if(! self::getLog()->isWhitelisted($IPStr)){
									self::getLog()->blockIP($IPStr, $reason, true);
								}
							}
						}
					}
				} catch(Exception $e){
					//Ignore
				}
			}
		}
	}
	private static function keyAlert($msg){
		self::alert($msg, $msg . " To ensure uninterrupted Premium Wordfence protection on your site,\nplease renew your API key by visiting http://www.wordfence.com/ Sign in, go to your dashboard,\nselect the key about to expire and click the button to renew that API key.", false);
	}
	public static function dailyCron(){
		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		$keyData = $api->call('ping_api_key');
		if(isset($keyData['_isPaidKey']) && $keyData['_isPaidKey']){
			$keyExpDays = $keyData['_keyExpDays'];
			$keyIsExpired = $keyData['_expired'];
			if (!empty($keyData['_autoRenew'])) {
				if ($keyExpDays > 12) {
					wfConfig::set('keyAutoRenew10Sent', '');
				} else if ($keyExpDays <= 12 && $keyExpDays > 0 && !wfConfig::get('keyAutoRenew10Sent')) {
					wfConfig::set('keyAutoRenew10Sent', 1);
					$email = "Your Premium Wordfence API Key is set to auto-renew in 10 days.";
					self::alert($email, "$email To update your API key settings please visit http://www.wordfence.com/zz9/dashboard", false);
				}
			} else {
				if($keyExpDays > 15){
					wfConfig::set('keyExp15Sent', '');
					wfConfig::set('keyExp7Sent', '');
					wfConfig::set('keyExp2Sent', '');
					wfConfig::set('keyExp1Sent', '');
					wfConfig::set('keyExpFinalSent', '');
				} else if($keyExpDays <= 15 && $keyExpDays > 0){
					if($keyExpDays <= 15 && $keyExpDays >= 11 && (! wfConfig::get('keyExp15Sent'))){
						wfConfig::set('keyExp15Sent', 1);
						self::keyAlert("Your Premium Wordfence API Key expires in less than 2 weeks.");
					} else if($keyExpDays <= 7 && $keyExpDays >= 4 && (! wfConfig::get('keyExp7Sent'))){
						wfConfig::set('keyExp7Sent', 1);
						self::keyAlert("Your Premium Wordfence API Key expires in less than a week.");
					} else if($keyExpDays == 2 && (! wfConfig::get('keyExp2Sent'))){
						wfConfig::set('keyExp2Sent', 1);
						self::keyAlert("Your Premium Wordfence API Key expires in 2 days.");
					} else if($keyExpDays == 1 && (! wfConfig::get('keyExp1Sent'))){
						wfConfig::set('keyExp1Sent', 1);
						self::keyAlert("Your Premium Wordfence API Key expires in 1 day.");
					}
				} else if($keyIsExpired && (! wfConfig::get('keyExpFinalSent')) ){
					wfConfig::set('keyExpFinalSent', 1);
					self::keyAlert("Your Wordfence Premium API Key has Expired!");
				}
			}
		}

		$wfdb = new wfDB();
		global $wpdb; $p = $wpdb->base_prefix;
		try {
			$patData = $api->call('get_known_vuln_pattern');
			if(is_array($patData) && $patData['pat']){
				if(@preg_match($patData['pat'], 'wordfence_test_vuln_match')){
					wfConfig::set('vulnRegex', $patData['pat']);
				}
			}
		} catch(Exception $e){
			wordfence::status(4, 'error', "Could not fetch vulnerability patterns in scheduled job: " . $e->getMessage());
		}

		$wfdb->queryWrite("delete from $p"."wfLocs where ctime < unix_timestamp() - %d", WORDFENCE_MAX_IPLOC_AGE);
		$wfdb->truncate($p . "wfBadLeechers"); //only uses date that's less than 1 minute old
		$wfdb->queryWrite("delete from $p"."wfBlocks where (blockedTime + %s < unix_timestamp()) and permanent=0", wfConfig::get('blockedTime'));
		$wfdb->queryWrite("delete from $p"."wfCrawlers where lastUpdate < unix_timestamp() - (86400 * 7)");

		$wfdb->truncate($p . "wfVulnScanners"); //We only report data within the last hour in hourlyCron.
		// So if we do a once a day truncate to be safe, we'll only potentially lose the hour right before the truncate.
		// Worth it to clean out the table completely once a day.


		$count = $wfdb->querySingle("select count(*) as cnt from $p"."wfHits");
		if($count > 20000){
			$wfdb->truncate($p . "wfHits"); //So we don't slow down sites that have very large wfHits tables
		} else if($count > 2000){
			$wfdb->queryWrite("delete from $p"."wfHits order by id asc limit %d", ($count - 100));
		}

/*
		$count6 = $wfdb->querySingle("select count(*) as cnt from $p"."wfPerfLog");
		if($count6 > 20000){
			$wfdb->truncate($p . "wfPerfLog"); //So we don't slow down sites that have very large wfHits tables
		} else if($count6 > 2000){
			$wfdb->queryWrite("delete from $p"."wfPerfLog order by id asc limit %d", ($count6 - 100));
		}
*/
		$maxRows = 1000; //affects stuff further down too
		foreach(array('wfLeechers', 'wfScanners') as $table){
			//This is time based per IP so shouldn't get too big
			$wfdb->queryWrite("delete from $p"."$table where eMin < ((unix_timestamp() - (86400 * 2)) / 60)");
		}
		$wfdb->queryWrite("delete from $p"."wfLockedOut where blockedTime + %s < unix_timestamp()", wfConfig::get('loginSec_lockoutMins') * 60);
		$count2 = $wfdb->querySingle("select count(*) as cnt from $p"."wfLogins");
		if($count2 > 20000){
			$wfdb->truncate($p . "wfLogins"); //in case of Dos
		} else if($count2 > $maxRows){
			$wfdb->queryWrite("delete from $p"."wfLogins order by ctime asc limit %d", ($count2 - 100));
		}
		$wfdb->queryWrite("delete from $p"."wfReverseCache where unix_timestamp() - lastUpdate > 86400");
		$count3 = $wfdb->querySingle("select count(*) as cnt from $p"."wfThrottleLog");
		if($count3 > 20000){
			$wfdb->truncate($p . "wfThrottleLog"); //in case of DoS
		} else if($count3 > $maxRows){
			$wfdb->queryWrite("delete from $p"."wfThrottleLog order by endTime asc limit %d", ($count3 - 100));
		}
		$count4 = $wfdb->querySingle("select count(*) as cnt from $p"."wfStatus");
		if($count4 > 100000){
			$wfdb->truncate($p . "wfStatus");
		} else if($count4 > 1000){ //max status events we keep. This determines how much gets emailed to us when users sends us a debug report.
			$wfdb->queryWrite("delete from $p"."wfStatus where level != 10 order by ctime asc limit %d", ($count4 - 1000));
			$count5 = $wfdb->querySingle("select count(*) as cnt from $p"."wfStatus where level=10");
			if($count5 > 100){
				$wfdb->queryWrite("delete from $p"."wfStatus where level = 10 order by ctime asc limit %d", ($count5 - 100) );
			}
		}

		$report = new wfActivityReport();
		$report->rotateIPLog();
	}
	public static function runInstall(){
		if(self::$runInstallCalled){ return; }
		self::$runInstallCalled = true;
		if (function_exists('ignore_user_abort')) {
			ignore_user_abort(true);
		}
		$previous_version = get_option('wordfence_version', '0.0.0');
		update_option('wordfence_version', WORDFENCE_VERSION); //In case we have a fatal error we don't want to keep running install.
		//EVERYTHING HERE MUST BE IDEMPOTENT

		//Remove old legacy cron job if exists
		wp_clear_scheduled_hook('wordfence_scheduled_scan');

		$schema = new wfSchema();
		$schema->createAll(); //if not exists
		wfConfig::setDefaults(); //If not set

		$restOfSite = wfConfig::get('cbl_restOfSiteBlocked', 'notset');
		if($restOfSite == 'notset'){
			wfConfig::set('cbl_restOfSiteBlocked', '1');
		}

		//Install new schedule. If schedule config is blank it will install the default 'auto' schedule.
		wordfence::scheduleScans();

		if(wfConfig::get('autoUpdate') == '1'){
			wfConfig::enableAutoUpdate(); //Sets up the cron
		}

		if(! wfConfig::get('apiKey')){
			$api = new wfAPI('', wfUtils::getWPVersion());
			try {
				$keyData = $api->call('get_anon_api_key');
				if($keyData['ok'] && $keyData['apiKey']){
					wfConfig::set('apiKey', $keyData['apiKey']);
				} else {
					throw new Exception("Could not understand the response we received from the Wordfence servers when applying for a free API key.");
				}
			} catch(Exception $e){
				error_log("Could not fetch free API key from Wordfence: " . $e->getMessage());
				return;
			}
		}
		wp_clear_scheduled_hook('wordfence_daily_cron');
		wp_clear_scheduled_hook('wordfence_hourly_cron');
		if (is_main_site()) {
			wp_schedule_event(time(), 'daily', 'wordfence_daily_cron'); //'daily'
			wp_schedule_event(time(), 'hourly', 'wordfence_hourly_cron');
		}

		$db = new wfDB();

		if($db->columnExists('wfHits', 'HTTPHeaders')){ //Upgrade from 3.0.4
			global $wpdb;
			$prefix = $wpdb->base_prefix;
			$count = $db->querySingle("select count(*) as cnt from $prefix"."wfHits");
			if($count > 20000){
				$db->queryWrite("delete from $prefix"."wfHits order by id asc limit " . ($count - 20000));
			}
			$db->dropColumn('wfHits', 'HTTPHeaders');
		}

		//Upgrading from 1.5.6 or earlier needs:
		$db->createKeyIfNotExists('wfStatus', 'level', 'k2');
		if(wfConfig::get('isPaid') == 'free'){
			wfConfig::set('isPaid', '');
		}
		//End upgrade from 1.5.6

		/** @var wpdb $wpdb */
		global $wpdb;
		$prefix = $wpdb->base_prefix;
		$db->queryWriteIgnoreError("alter table $prefix"."wfConfig modify column val longblob");
		$db->queryWriteIgnoreError("alter table $prefix"."wfBlocks add column permanent tinyint UNSIGNED default 0");
		$db->queryWriteIgnoreError("alter table $prefix"."wfStatus modify column msg varchar(1000) NOT NULL");
		//3.1.2 to 3.1.4
		$db->queryWriteIgnoreError("alter table $prefix"."wfBlocks modify column blockedTime bigint signed NOT NULL");
		//3.2.1 to 3.2.2
		$db->queryWriteIgnoreError("alter table $prefix"."wfLockedOut modify column blockedTime bigint signed NOT NULL");
		$db->queryWriteIgnoreError("drop table if exists $prefix"."wfFileQueue");
		$db->queryWriteIgnoreError("drop table if exists $prefix"."wfFileChanges");

		$result = $wpdb->get_row("SHOW FIELDS FROM {$prefix}wfStatus where field = 'id'");
		if (!$result || strtolower($result->Key) != 'pri') {
			//Adding primary key to this table because some backup apps use primary key during backup.
			$db->queryWriteIgnoreError("alter table {$prefix}wfStatus add id bigint UNSIGNED NOT NULL auto_increment PRIMARY KEY");
		}

		$optScanEnabled = $db->querySingle("select val from $prefix"."wfConfig where name='scansEnabled_options'");
		if($optScanEnabled != '0' && $optScanEnabled != '1'){
			$db->queryWrite("update $prefix"."wfConfig set val='1' where name='scansEnabled_options'");
		}

		$optScanEnabled = $db->querySingle("select val from $prefix"."wfConfig where name='scansEnabled_heartbleed'");
		if($optScanEnabled != '0' && $optScanEnabled != '1'){ //Enable heartbleed if no value is set.
			wfConfig::set('scansEnabled_heartbleed', 1);
		}

		if (wfConfig::get('cacheType') == 'php' || wfConfig::get('cacheType') == 'falcon') {
			wfCache::removeCacheDirectoryHtaccess();
		}

		// IPv6 schema changes for 6.0.1
		$tables_with_ips = array(
			'wfCrawlers',
			'wfBadLeechers',
			'wfBlockedIPLog',
			'wfBlocks',
			'wfHits',
			'wfLeechers',
			'wfLockedOut',
			'wfLocs',
			'wfLogins',
			'wfReverseCache',
			'wfScanners',
			'wfThrottleLog',
			'wfVulnScanners',
		);

		foreach ($tables_with_ips as $ip_table) {
			$result = $wpdb->get_row("SHOW FIELDS FROM {$prefix}{$ip_table} where field = 'IP'");
			if (!$result || strtolower($result->Type) == 'binary(16)') {
				continue;
			}

			$db->queryWriteIgnoreError("ALTER TABLE {$prefix}{$ip_table} MODIFY IP BINARY(16)");

			// Just to be sure we don't corrupt the data if the alter fails.
			$result = $wpdb->get_row("SHOW FIELDS FROM {$prefix}{$ip_table} where field = 'IP'");
			if (!$result || strtolower($result->Type) != 'binary(16)') {
				continue;
			}
			$db->queryWriteIgnoreError("UPDATE {$prefix}{$ip_table} SET IP = CONCAT(LPAD(CHAR(0xff, 0xff), 12, CHAR(0)), LPAD(
	CHAR(
		CAST(IP as UNSIGNED) >> 24 & 0xFF,
		CAST(IP as UNSIGNED) >> 16 & 0xFF,
		CAST(IP as UNSIGNED) >> 8 & 0xFF,
		CAST(IP as UNSIGNED) & 0xFF
	),
	4,
	CHAR(0)
))");
		}

		// Fix the data in the country column.
		// TODO: add version check so this doesn't run on every update.
		$ip_results = $wpdb->get_results("SELECT * FROM `{$prefix}wfBlockedIPLog` GROUP BY IP");
		if ($ip_results) {
			foreach ($ip_results as $ip_row) {
				$wpdb->query($wpdb->prepare("UPDATE `{$prefix}wfBlockedIPLog` SET countryCode = %s WHERE IP = %s", wfUtils::IP2Country(wfUtils::inet_ntop($ip_row->IP)), $ip_row->IP));
			}
		}

		if (wfConfig::get('other_hideWPVersion')) {
			wfUtils::hideReadme();
		}

		//Must be the final line
	}
	private static function doEarlyAccessLogging(){
		$wfLog = self::getLog();
		if($wfLog->logHitOK()){
			if( empty($wfFunc) && is_404() ){
				$wfLog->logLeechAndBlock('404');
			} else {
				$wfLog->logLeechAndBlock('hit');
			}
			if(wfConfig::liveTrafficEnabled()){
				self::$hitID = $wfLog->logHit();
				add_action('wp_head', 'wordfence::wfLogHumanHeader');
			}
			/*
			if(wfConfig::get('perfLoggingEnabled', false)){
				add_action('wp_head', 'wordfence::wfLogPerfHeader');
			}
			*/
		}
	}
	public static function initProtection(){
		if(preg_match('/\/wp\-admin\/admin\-ajax\.php/', $_SERVER['REQUEST_URI'])){
			if(
				(isset($_GET['action']) && $_GET['action'] == 'revslider_show_image' && isset($_GET['img']) && preg_match('/\.php$/i', $_GET['img'])) ||
				(isset($_POST['action']) && $_POST['action'] == 'revslider_show_image' && isset($_POST['img']) && preg_match('/\.php$/i', $_POST['img']))
			){
				self::getLog()->do503(86400, "URL not allowed. Slider Revolution Hack attempt detected. #2");
				exit(); //function above exits anyway
			}
		}
	}
	public static function install_actions(){
		self::initProtection();
		if(wfUtils::hasLoginCookie()){ //Fast way of checking if user may be logged in. Not secure, but these are only available if you're signed in.
			register_activation_hook(WP_PLUGIN_DIR . '/wordfence/wordfence.php', 'wordfence::installPlugin');
			register_deactivation_hook(WP_PLUGIN_DIR . '/wordfence/wordfence.php', 'wordfence::uninstallPlugin');
		}

		$versionInOptions = get_option('wordfence_version', false);
		if( (! $versionInOptions) || version_compare(WORDFENCE_VERSION, $versionInOptions, '>')){
			//Either there is no version in options or the version in options is greater and we need to run the upgrade
			self::runInstall();
		}
		//These access wfConfig::get('apiKey') and will fail if runInstall hasn't executed.
		wfCache::setupCaching();

		if(defined('MULTISITE') && MULTISITE === true){
			global $blog_id;
			if($blog_id == 1 && get_option('wordfenceActivated') != 1){ return; } //Because the plugin is active once installed, even before it's network activated, for site 1 (WordPress team, why?!)
		}
		//User may be logged in or not, so register both handlers
		add_action('wp_ajax_nopriv_wordfence_logHuman', 'wordfence::ajax_logHuman_callback');
		add_action('wp_ajax_nopriv_wordfence_doScan', 'wordfence::ajax_doScan_callback');
		add_action('wp_ajax_nopriv_wordfence_testAjax', 'wordfence::ajax_testAjax_callback');
		add_action('wp_ajax_nopriv_wordfence_perfLog', 'wordfence::ajax_perfLog_callback');
		if(wfUtils::hasLoginCookie()){ //may be logged in. Fast way to check. These aren't secure functions, this is just a perf optimization, along with every other use of hasLoginCookie()
			add_action('wp_ajax_wordfence_perfLog', 'wordfence::ajax_perfLog_callback');
			add_action('wp_ajax_wordfence_logHuman', 'wordfence::ajax_logHuman_callback');
			add_action('wp_ajax_wordfence_doScan', 'wordfence::ajax_doScan_callback');
			add_action('wp_ajax_wordfence_testAjax', 'wordfence::ajax_testAjax_callback');

			if (is_multisite()) {
				add_action('wp_network_dashboard_setup', 'wordfence::addDashboardWidget');
			} else {
				add_action('wp_dashboard_setup', 'wordfence::addDashboardWidget');
			}
		}


		add_action('wordfence_start_scheduled_scan', 'wordfence::wordfenceStartScheduledScan');
		add_action('wordfence_daily_cron', 'wordfence::dailyCron');
		add_action('wordfence_daily_autoUpdate', 'wfConfig::autoUpdate');
		add_action('wordfence_hourly_cron', 'wordfence::hourlyCron');
		add_action('plugins_loaded', 'wordfence::veryFirstAction');
		add_action('init', 'wordfence::initAction');
		add_action('template_redirect', 'wordfence::templateRedir', 1001);
		add_action('shutdown', 'wordfence::shutdownAction');

		if(version_compare(PHP_VERSION, '5.4.0') >= 0){
			add_action('wp_authenticate','wordfence::authActionNew', 1, 2);
		} else {
			add_action('wp_authenticate','wordfence::authActionOld', 1, 2);
		}
		add_filter('authenticate', 'wordfence::authenticateFilter', 99, 3);
		if (self::isLockedOut(wfUtils::getIP())) {
			add_filter('xmlrpc_enabled', '__return_false');
		}

		add_action('login_init','wordfence::loginInitAction');
		add_action('wp_login','wordfence::loginAction');
		add_action('wp_logout','wordfence::logoutAction');
		add_action('lostpassword_post', 'wordfence::lostPasswordPost', '1');
		if(wfUtils::hasLoginCookie()){
			add_action('user_profile_update_errors', 'wordfence::validateProfileUpdate', 0, 3 );
			add_action('profile_update', 'wordfence::profileUpdateAction', '99', 2);
			add_action('validate_password_reset', 'wordfence::validatePassword', 10, 2 );
		}
		add_action('publish_future_post', 'wordfence::publishFuturePost');
		add_action('mobile_setup', 'wordfence::jetpackMobileSetup'); //Action called in Jetpack Mobile Theme: modules/minileven/minileven.php

		// Add actions for the email summary
		add_action('wordfence_email_activity_report', array('wfActivityReport', 'executeCronJob'));

		//For debugging
		//add_filter( 'cron_schedules', 'wordfence::cronAddSchedules' );

		add_filter('wp_redirect', 'wordfence::wpRedirectFilter', 99, 2);
		add_filter('pre_comment_approved', 'wordfence::preCommentApprovedFilter', '99', 2);
		//html|xhtml|atom|rss2|rdf|comment|export
		if(wfConfig::get('other_hideWPVersion')){
			add_filter('style_loader_src', 'wordfence::replaceVersion');
			add_filter('script_loader_src', 'wordfence::replaceVersion');

			add_action('upgrader_process_complete', 'wordfence::hideReadme');
		}
		add_filter('get_the_generator_html', 'wordfence::genFilter', 99, 2);
		add_filter('get_the_generator_xhtml', 'wordfence::genFilter', 99, 2);
		add_filter('get_the_generator_atom', 'wordfence::genFilter', 99, 2);
		add_filter('get_the_generator_rss2', 'wordfence::genFilter', 99, 2);
		add_filter('get_the_generator_rdf', 'wordfence::genFilter', 99, 2);
		add_filter('get_the_generator_comment', 'wordfence::genFilter', 99, 2);
		add_filter('get_the_generator_export', 'wordfence::genFilter', 99, 2);
		add_filter('registration_errors', 'wordfence::registrationFilter', 99, 3);

		// Change GoDaddy's limit login mu-plugin since it can interfere with the two factor auth message.
		if (self::hasGDLimitLoginsMUPlugin()) {
			add_action('login_errors', array('wordfence', 'fixGDLimitLoginsErrors'), 11);
		}

		if(is_admin()){
			add_action('admin_init', 'wordfence::admin_init');
			if(is_multisite()){
				if(wfUtils::isAdminPageMU()){
					add_action('network_admin_menu', 'wordfence::admin_menus');
				} //else don't show menu
			} else {
				add_action('admin_menu', 'wordfence::admin_menus');
			}
			add_filter('pre_update_option_permalink_structure', 'wordfence::disablePermalinksFilter', 10, 2);
			if( preg_match('/^(?:falcon|php)$/', wfConfig::get('cacheType')) ){
				add_filter('post_row_actions', 'wordfence::postRowActions', 0, 2);
				add_filter('page_row_actions', 'wordfence::pageRowActions', 0, 2);
				add_action('post_submitbox_start', 'wordfence::postSubmitboxStart');
			}
		}

		add_action('request', 'wordfence::preventAuthorNScans');
	}
	/*
  	public static function cronAddSchedules($schedules){
		$schedules['wfEachMinute'] = array(
				'interval' => 60,
				'display' => __( 'Once a Minute' )
				);
		return $schedules;
	}
	*/
	/*
	public static function addDashboardWidget(){
		wp_add_dashboard_widget('wordfenceDashboardWidget', 'Wordfence Security Status', 'wordfence::displayDashboardWidget');
	}
	public static function displayDashboardWidget(){
		require('dashboard.php');
	}
	*/
	public static function jetpackMobileSetup(){
		define('WFDONOTCACHE', true); //Don't cache jetpack mobile theme pages.
	}
	public static function wpRedirectFilter($URL, $status){
		return $URL;
	}
	public static function ajax_testAjax_callback(){
		die("WFSCANTESTOK");
	}
	public static function ajax_doScan_callback(){
		ignore_user_abort(true);
		self::$wordfence_wp_version = false;
		//This is messy, but not sure of a better way to do this without gauranteeing we get $wp_version
		require(ABSPATH . 'wp-includes/version.php');
		self::$wordfence_wp_version = $wp_version;
		require('wfScan.php');
		wfScan::wfScanMain();

	} //END doScan
	public static function ajax_perfLog_callback(){
		$wfLog = self::getLog();
		$fields = array('fetchStart', 'domainLookupStart', 'domainLookupEnd', 'connectStart', 'connectEnd', 'requestStart', 'responseStart', 'responseEnd', 'domReady', 'loaded');
		foreach($fields as $f){
			if(preg_match('/^\d+$/', $_POST[$f])){
				$data[$f] = $_POST[$f];
			}
		}
		$UA = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$URL = $_POST['URL'];
		$wfLog->logPerf(wfUtils::getIP(), $UA, $URL, $data);
		die(json_encode(array('ok' => 1)));
	}
	public static function ajax_logHuman_callback(){
		$browscap = new wfBrowscap();
		$UA = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$isCrawler = false;
		if($UA){
			$b = $browscap->getBrowser($UA);
			if(!empty($b['Crawler']) || wfCrawl::isGoogleCrawler()){
				$isCrawler = true;
			}
		}

		@ob_end_clean();
		if(! headers_sent()){
			header('Content-type: text/javascript');
			header("Connection: close");
			header("Content-Length: 0");
			header("X-Robots-Tag: noindex");
			if (!$isCrawler) {
				setcookie('wordfence_verifiedHuman', wp_create_nonce('wordfence_verifiedHuman' . $UA . wfUtils::getIP()), time() + 86400, '/');
			}
		}
		flush();
		if(! $isCrawler){
			$hid = $_GET['hid'];
			$hid = wfUtils::decrypt($hid);
			if(! preg_match('/^\d+$/', $hid)){ exit(); }
			$db = new wfDB();
			global $wpdb; $p = $wpdb->base_prefix;
			$db->queryWrite("update $p"."wfHits set jsRun=1 where id=%d", $hid);
		}
		die("");
	}
	public static function ajaxReceiver(){
		if(! wfUtils::isAdmin()){
			die(json_encode(array('errorMsg' => "You appear to have logged out or you are not an admin. Please sign-out and sign-in again.")));
		}
		$func = (isset($_POST['action']) && $_POST['action']) ? $_POST['action'] : $_GET['action'];
		$nonce = (isset($_POST['nonce']) && $_POST['nonce']) ? $_POST['nonce'] : $_GET['nonce'];
		if(! wp_verify_nonce($nonce, 'wp-ajax')){
			die(json_encode(array('errorMsg' => "Your browser sent an invalid security token to Wordfence. Please try reloading this page or signing out and in again.")));
		}
		//func is e.g. wordfence_ticker so need to munge it
		$func = str_replace('wordfence_', '', $func);
		$returnArr = call_user_func('wordfence::ajax_' . $func . '_callback');
		if($returnArr === false){
			$returnArr = array('errorMsg' => "Wordfence encountered an internal error executing that request.");
		}

		if(! is_array($returnArr)){
			error_log("Function " . wp_kses($func, array()) . " did not return an array and did not generate an error.");
			$returnArr = array();
		}
		if(isset($returnArr['nonce'])){
			error_log("Wordfence ajax function return an array with 'nonce' already set. This could be a bug.");
		}
		$returnArr['nonce'] = wp_create_nonce('wp-ajax');
		die(json_encode($returnArr));
	}
	public static function publishFuturePost($id){
		if(wfConfig::get('clearCacheSched')){
			wfCache::scheduleCacheClear();
		}
	}
	public static function validateProfileUpdate($errors, $update, $userData){
		wordfence::validatePassword($errors, $userData);
	}
	public static function validatePassword($errors, $userData){
		$password = ( isset( $_POST[ 'pass1' ] ) && trim( $_POST[ 'pass1' ] ) ) ? $_POST[ 'pass1' ] : false;
		$user_id = isset( $userData->ID ) ? $userData->ID : false;
		$username = isset( $_POST["user_login"] ) ? $_POST["user_login"] : $userData->user_login;
		if($password == false){ return $errors; }
		if($errors->get_error_data("pass") ){ return $errors; }
		$enforce = false;
		if(wfConfig::get('loginSec_strongPasswds') == 'pubs'){
			if(user_can($user_id, 'publish_posts')){
				$enforce = true;
			}
		} else if(wfConfig::get('loginSec_strongPasswds')  == 'all'){
			$enforce = true;
		}
		if($enforce){
			if(! wordfence::isStrongPasswd($password, $username)){
				$errors->add('pass', "Please choose a stronger password. Try including numbers, symbols and a mix of upper and lower case letters and remove common words.");
				return $errors;
			}
		}
		$twoFactorUsers = wfConfig::get_ser('twoFactorUsers', array());
		if(preg_match(self::$passwordCodePattern, $password) && isset($twoFactorUsers) && is_array($twoFactorUsers) && sizeof($twoFactorUsers) > 0){
			$errors->add('pass', "Passwords containing a space followed by 'wf' without quotes are not allowed.");
			return $errors;
		}
		return $errors;
	}
	public static function isStrongPasswd($passwd, $username ) {
		$strength = 0;
		if(strlen( trim( $passwd ) ) < 5)
			return false;
		if(strtolower( $passwd ) == strtolower( $username ) )
			return false;
		if(preg_match('/(?:password|passwd|mypass|wordpress)/i', $passwd)){
			return false;
		}
		if($num = preg_match_all( "/\d/", $passwd, $matches) ){
			$strength += ((int)$num * 10);
		}
		if ( preg_match( "/[a-z]/", $passwd ) )
			$strength += 26;
		if ( preg_match( "/[A-Z]/", $passwd ) )
			$strength += 26;
		if ($num = preg_match_all( "/[^a-zA-Z0-9]/", $passwd, $matches)){
			$strength += (31 * (int)$num);

		}
		if($strength > 60){
			return true;
		}
	}
	public static function lostPasswordPost(){
		$IP = wfUtils::getIP();
		if(self::getLog()->isWhitelisted($IP)){
			return;
		}
		if(self::isLockedOut($IP)){
			require('wfLockedOut.php');
		}
		if(empty($_POST['user_login'])){ return; }
		$value = trim($_POST['user_login']);
		$user  = get_user_by('login', $value);
		if (!$user) {
			$user = get_user_by('email', $value);
		}

		if($user){
			if(wfConfig::get('alertOn_lostPasswdForm')){
				wordfence::alert("Password recovery attempted", "Someone tried to recover the password for user with email address: " . wp_kses($user->user_email, array()), $IP);
			}
		}
		if(wfConfig::get('loginSecurityEnabled')){
			$tKey = 'wffgt_' . bin2hex(wfUtils::inet_pton($IP));
			$forgotAttempts = get_transient($tKey);
			if($forgotAttempts){
				$forgotAttempts++;
			} else {
				$forgotAttempts = 1;
			}
			if($forgotAttempts >= wfConfig::get('loginSec_maxForgotPasswd')){
				self::lockOutIP($IP, "Exceeded the maximum number of tries to recover their password which is set at: " . wfConfig::get('loginSec_maxForgotPasswd') . ". The last username or email they entered before getting locked out was: '" . $_POST['user_login'] . "'");
				require('wfLockedOut.php');
			}
			set_transient($tKey, $forgotAttempts, wfConfig::get('loginSec_countFailMins') * 60);
		}
	}
	public static function lockOutIP($IP, $reason){
		//First we lock out IP
		self::getLog()->lockOutIP(wfUtils::getIP(), $reason);
		//Then we send the email because email sending takes time and we want to block the baddie asap. If we don't users can get a lot of emails about a single attacker getting locked out.
		if(wfConfig::get('alertOn_loginLockout')){
			wordfence::alert("User locked out from signing in", "A user with IP address $IP has been locked out from the signing in or using the password recovery form for the following reason: $reason", $IP);
		}
	}
	public static function isLockedOut($IP){
		return self::getLog()->isIPLockedOut($IP);
	}
	public static function veryFirstAction(){
		$wfFunc = isset($_GET['_wfsf']) ? @$_GET['_wfsf'] : false;
		if($wfFunc == 'unlockEmail'){
			if(! wp_verify_nonce(@$_POST['nonce'], 'wf-form')){
				die("Sorry but your browser sent an invalid security token when trying to use this form.");
			}
			$numTries = get_transient('wordfenceUnlockTries');
			if($numTries > 10){
				echo "<html><body><h1>Please wait 3 minutes and try again</h1><p>You have used this form too much. Please wait 3 minutes and try again.</p></body></html>";
				exit();
			}
			if(! $numTries){ $numTries = 1; } else { $numTries = $numTries + 1; }
			set_transient('wordfenceUnlockTries', $numTries, 180);

			$email = trim($_POST['email']);
			global $wpdb;
			$ws = $wpdb->get_results($wpdb->prepare("SELECT ID, user_login FROM $wpdb->users WHERE user_email = %s", $email));
			foreach($ws as $user){
				$userDat = get_userdata($user->ID);
				if(wfUtils::isAdmin($userDat)){
					if($email == $userDat->user_email){
						$found = true;
						break;
					}
				}
			}
			if(! $found){
				foreach(wfConfig::getAlertEmails() as $alertEmail){
					if($alertEmail == $email){
						$found = true;
						break;
					}
				}
			}
			if($found){
				$key = wfUtils::bigRandomHex();
				$IP = wfUtils::getIP();
				set_transient('wfunlock_' . $key, $IP, 1800);
				$content = wfUtils::tmpl('email_unlockRequest.php', array(
					'siteName' => get_bloginfo('name', 'raw'),
					'siteURL' => wfUtils::getSiteBaseURL(),
					'unlockHref' => wfUtils::getSiteBaseURL() . '?_wfsf=unlockAccess&key=' . $key,
					'key' => $key,
					'IP' => $IP
					));
				wp_mail($email, "Unlock email requested", $content, "Content-Type: text/html");
			}
			echo "<html><body><h1>Your request was received</h1><p>We received a request to email \"" . wp_kses($email, array()) . "\" instructions to unlock their access. If that is the email address of a site administrator or someone on the Wordfence alert list, then they have been emailed instructions on how to regain access to this sytem. The instructions we sent will expire 30 minutes from now.</body></html>";
			exit();
		} else if($wfFunc == 'unlockAccess'){
			if(! preg_match('/^\d+\.\d+\.\d+\.\d+$/', get_transient('wfunlock_' . $_GET['key']))){
				echo "Invalid key provided for authentication.";
				exit();
			}
			/* You can enable this for paranoid security leve.
			if(get_transient('wfunlock_' . $_GET['key']) != wfUtils::getIP()){
				echo "You can only use this link from the IP address you used to generate the unlock email.";
				exit();
			}
			*/
			$wfLog = new wfLog(wfConfig::get('apiKey'), wfUtils::getWPVersion());
			if($_GET['func'] == 'unlockMyIP'){
				$wfLog->unblockIP(wfUtils::getIP());
				$wfLog->unlockOutIP(wfUtils::getIP());
				delete_transient('wflginfl_' . bin2hex(wfUtils::inet_pton(wfUtils::getIP()))); //Reset login failure counter
				header('Location: ' . wp_login_url());
				exit();
			} else if($_GET['func'] == 'unlockAllIPs'){
				wordfence::status(1, 'info', "Request received via unlock email link to unblock all IP's.");
				$wfLog->unblockAllIPs();
				$wfLog->unlockAllIPs();
				delete_transient('wflginfl_' . bin2hex(wfUtils::inet_pton(wfUtils::getIP()))); //Reset login failure counter
				header('Location: ' . wp_login_url());
				exit();
			} else if($_GET['func'] == 'disableRules'){
				wfConfig::set('firewallEnabled', 0);
				wfConfig::set('loginSecurityEnabled', 0);
				wordfence::status(1, 'info', "Request received via unlock email link to unblock all IP's via disabling firewall rules.");
				$wfLog->unblockAllIPs();
				$wfLog->unlockAllIPs();
				delete_transient('wflginfl_' . bin2hex(wfUtils::inet_pton(wfUtils::getIP()))); //Reset login failure counter
				wfConfig::set('cbl_countries', ''); //unblock all countries
				header('Location: ' . wp_login_url());
				exit();
			} else {
				echo "Invalid function specified. Please check the link we emailed you and make sure it was not cut-off by your email reader.";
				exit();
			}
		}

		if(wfConfig::get('firewallEnabled')){
			$wfLog = self::getLog();
			$wfLog->firewallBadIPs();

			$IP = wfUtils::getIP();
			if($wfLog->isWhitelisted($IP)){
				return;
			}
			if (wfConfig::get('neverBlockBG') == 'neverBlockUA' && wfCrawl::isGoogleCrawler()) {
				return;
			}
			if (wfConfig::get('neverBlockBG') == 'neverBlockVerified' && wfCrawl::isVerifiedGoogleCrawler()) {
				return;
			}

			if(wfConfig::get('blockFakeBots')){
				if(wfCrawl::isGooglebot() && !wfCrawl::isVerifiedGoogleCrawler()){
					$wfLog->blockIP($IP, "Fake Google crawler automatically blocked");
					wordfence::status(2, 'info', "Blocking fake Googlebot at IP $IP");
					$wfLog->do503(3600, "Fake Google crawler automatically blocked.");
				}
			}
			if(wfConfig::get('bannedURLs', false)){
				$URLs = explode(',', wfConfig::get('bannedURLs'));
				foreach($URLs as $URL){
					if($_SERVER['REQUEST_URI'] == trim($URL)){
						$wfLog->blockIP($IP, "Accessed a banned URL.");
						$wfLog->do503(3600, "Accessed a banned URL.");
						//exits
					}
				}
			}

			if(wfConfig::get('other_blockBadPOST') == '1' && $_SERVER['REQUEST_METHOD'] == 'POST' && empty($_SERVER['HTTP_USER_AGENT']) && empty($_SERVER['HTTP_REFERER'])){
				$wfLog->blockIP($IP, "POST received with blank user-agent and referer");
				$wfLog->do503(3600, "POST received with blank user-agent and referer");
				//exits
			}
		}
	}
	public static function loginAction($username){
		if(sizeof($_POST) < 1){ return; } //only execute if login form is posted
		if(! $username){ return; }
		wfConfig::inc('totalLogins');
		$user = get_user_by('login', $username);
		$userID = $user ? $user->ID : 0;
		self::getLog()->logLogin('loginOK', 0, $username);
		if(wfUtils::isAdmin($user)){
			wfConfig::set_ser('lastAdminLogin', array(
				'userID' => $userID,
				'username' => $username,
				'firstName' => $user->first_name,
				'lastName' => $user->last_name,
				'time' => wfUtils::localHumanDateShort(),
				'IP' => wfUtils::getIP()
				));
		}

		if(user_can($userID, 'update_core')){
			if(wfConfig::get('alertOn_adminLogin')){
				wordfence::alert("Admin Login", "A user with username \"$username\" who has administrator access signed in to your WordPress site.", wfUtils::getIP());
			}
		} else {
			if(wfConfig::get('alertOn_nonAdminLogin')){
				wordfence::alert("User login", "A non-admin user with username \"$username\" signed in to your WordPress site.", wfUtils::getIP());
			}
		}
	}
	public static function registrationFilter($errors, $santizedLogin, $userEmail){
		if(wfConfig::get('loginSec_blockAdminReg') && $santizedLogin == 'admin'){
			$errors->add('user_login_error', '<strong>ERROR</strong>: You can\'t register using that username');
		}
		return $errors;
	}
	public static function authenticateFilter($authUser, $username, $passwd){
		wfConfig::inc('totalLoginHits'); //The total hits to wp-login.php including logins, logouts and just hits.
		$IP = wfUtils::getIP();
		$secEnabled = wfConfig::get('loginSecurityEnabled');
		if($secEnabled && (! self::getLog()->isWhitelisted($IP)) && wfConfig::get('isPaid') ){
			$twoFactorUsers = wfConfig::get_ser('twoFactorUsers', array());
			if(isset($twoFactorUsers) && is_array($twoFactorUsers) && sizeof($twoFactorUsers) > 0){
				$userDat = (isset($_POST['wordfence_userDat']) ? $_POST['wordfence_userDat'] : false);
				 if(is_object($userDat) && get_class($authUser) == 'WP_User'){ //Valid username and password either with or without the 'wf...' code. Users is now logged in at this point.
					if(isset($_POST['wordfence_authFactor']) && $_POST['wordfence_authFactor']){ //user entered a valid user and password with ' wf....' appended
						foreach($twoFactorUsers as &$t){
							if($t[0] == $userDat->ID && $t[3] == 'activated'){
								if($_POST['wordfence_authFactor'] == $t[2] && $t[4] > time()){
									//Do nothing and allow user to sign in. Their passwd has already been modified to be the passwd without the code.
								} else if($_POST['wordfence_authFactor'] == $t[2]){
									$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
									try {
										$codeResult = $api->call('twoFactor_verification', array(), array('phone' => $t[1]) );

										if(isset($codeResult['notPaid']) && $codeResult['notPaid']){
											break; //Let them sign in without two factor
										}
										if(isset($codeResult['ok']) && $codeResult['ok']){
											$t[2] = $codeResult['code'];
											$t[4] = time() + 1800; //30 minutes until code expires
											wfConfig::set_ser('twoFactorUsers', $twoFactorUsers); //save the code the user needs to enter and return an error.
											self::$authError = new WP_Error('twofactor_required', __('<strong>CODE EXPIRED. CHECK YOUR PHONE:</strong> The code you entered has expired. Codes are only valid for 30 minutes for security reasons. We have sent you a new code. Please sign in using your username and your password followed by a space and the new code we sent you.'));
											return self::$authError;
										} else {
											break; //No new code was received. Let them sign in with the expired code.
										}

									} catch (Exception $e) {
										// Couldn't connect to noc1, let them sign in since the password was correct.
										break;
									}
								} else { //Bad code, so cancel the login and return an error to user.
									self::$authError = new WP_Error( 'twofactor_required', __( '<strong>INVALID CODE</strong>: You need to enter your password followed by a space and the code we sent to your phone. The code should start with \'wf\' and should be four characters. e.g. wfAB12. In this case you would enter your password as: \'mypassword wfAB12\' without quotes.'));
									return self::$authError;
								}
							} //No user matches and has TF activated so let user sign in.
						}
					} else { //valid login with no code entered
						foreach($twoFactorUsers as &$t){
							if($t[0] == $userDat->ID && $t[3] == 'activated'){ //Yup, enabled, so lets send the code
								$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
								try {
									$codeResult = $api->call('twoFactor_verification', array(), array('phone' => $t[1]) );
									if(isset($codeResult['notPaid']) && $codeResult['notPaid']){
										break; //Let them sign in without two factor if their API key has expired or they're not paid and for some reason they have this set up.
									}
								} catch (Exception $e) {
									// Couldn't connect to noc1, let them sign in since the password was correct.
									break;
								}
								if(isset($codeResult['ok']) && $codeResult['ok']){
									$t[2] = $codeResult['code'];
									$t[4] = time() + 1800; //30 minutes until code expires
									wfConfig::set_ser('twoFactorUsers', $twoFactorUsers); //save the code the user needs to enter and return an error.

									if (self::hasGDLimitLoginsMUPlugin() && function_exists('limit_login_get_address')) {
										$retries = get_option('limit_login_retries', array());
										$ip = limit_login_get_address();

										if (!is_array($retries)) {
											$retries = array();
										}
										if (isset($retries[$ip]) && is_int($retries[$ip])) {
											$retries[$ip]--;
										} else {
											$retries[$ip] = 0;
										}
										update_option('limit_login_retries', $retries);
									}

									self::$authError = new WP_Error( 'twofactor_required', __( '<strong>CHECK YOUR PHONE</strong>: A code has been sent to your phone and will arrive within 30 seconds. Please sign in again and add a space and the code to the end of your password.' ) );
									return self::$authError;
								} else { //oops, our API returned an error.
									break; //Let them sign in without two factor because the API is broken and we don't want to lock users out of their own systems.
								}
							} //User is not present in two factor list or is not activated. Sign in without twofactor.
						} //Two facto users is empty. Sign in without two factor.
					}
				}
			}
		}

		if(self::getLog()->isWhitelisted($IP)){
			return $authUser;
		}
		if(wfConfig::get('other_WFNet') && is_wp_error($authUser) && ($authUser->get_error_code() == 'invalid_username' || $authUser->get_error_code() == 'incorrect_password') ){
			if($maxBlockTime = self::wfsnIsBlocked($IP, 'brute')){
				self::getLog()->blockIP($IP, "Blocked by Wordfence Security Network", true, false, $maxBlockTime);
			}

		}
		if($secEnabled){
			if(is_wp_error($authUser) && $authUser->get_error_code() == 'invalid_username'){
				if($blacklist = wfConfig::get('loginSec_userBlacklist')){
					$users = explode(',', $blacklist);
					foreach($users as $user){
						if(strtolower($username) == strtolower($user)){
							self::getLog()->blockIP($IP, "Blocked by login security setting.");
							$secsToGo = wfConfig::get('blockedTime');
							self::getLog()->do503($secsToGo, "Blocked by login security setting.");
							break;
						}
					}
				}
				if(wfConfig::get('loginSec_lockInvalidUsers')){
					if(strlen($username) > 0 && preg_match('/[^\r\s\n\t]+/', $username)){
						self::lockOutIP($IP, "Used an invalid username '" . $username . "' to try to sign in.");
					}
					require('wfLockedOut.php');
				}
			}
			$tKey = 'wflginfl_' . bin2hex(wfUtils::inet_pton($IP));
			if(is_wp_error($authUser) && ($authUser->get_error_code() == 'invalid_username' || $authUser->get_error_code() == 'incorrect_password') ){
				$tries = get_transient($tKey);
				if($tries){
					$tries++;
				} else {
					$tries = 1;
				}
				if($tries >= wfConfig::get('loginSec_maxFailures')){
					self::lockOutIP($IP, "Exceeded the maximum number of login failures which is: " . wfConfig::get('loginSec_maxFailures') . ". The last username they tried to sign in with was: '" . $username . "'");
					require('wfLockedOut.php');
				}
				set_transient($tKey, $tries, wfConfig::get('loginSec_countFailMins') * 60);
			} else if(get_class($authUser) == 'WP_User'){
				delete_transient($tKey); //reset counter on success
			}
		}
		if(is_wp_error($authUser)){
			if($authUser->get_error_code() == 'invalid_username'){
				self::getLog()->logLogin('loginFailInvalidUsername', 1, $username);
			} else {
				self::getLog()->logLogin('loginFailValidUsername', 1, $username);
			}
		}

		if(is_wp_error($authUser) && ($authUser->get_error_code() == 'invalid_username' || $authUser->get_error_code() == 'incorrect_password') && wfConfig::get('loginSec_maskLoginErrors')){
			return new WP_Error( 'incorrect_password', sprintf( __( '<strong>ERROR</strong>: The username or password you entered is incorrect. <a href="%2$s" title="Password Lost and Found">Lost your password</a>?' ), $username, wp_lostpassword_url() ) );
		}
		return $authUser;
	}
	public static function wfsnReportBlockedAttempt($IP, $type){
		try {
			$result = wp_remote_get(WORDFENCE_HACKATTEMPT_URL . 'hackAttempt/?blocked=1&k=' . rawurlencode(wfConfig::get('apiKey')) . '&IP=' . rawurlencode(wfUtils::inet_pton($IP)) . '&t=' . rawurlencode($type), array(
				'timeout' => 1,
				'user-agent' => "Wordfence.com UA " . (defined('WORDFENCE_VERSION') ? WORDFENCE_VERSION : '[Unknown version]'),
			));
			if (is_wp_error($result)) {
				return false;
			}
		} catch(Exception $err){
			return false;
		}
	}
	public static function wfsnIsBlocked($IP, $type){
		try {
			$result = wp_remote_get(WORDFENCE_HACKATTEMPT_URL . 'hackAttempt/?k=' . rawurlencode(wfConfig::get('apiKey')) . '&IP=' . rawurlencode(filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? wfUtils::inet_aton($IP) : wfUtils::inet_pton($IP)) . '&t=' . rawurlencode($type), array(
				'timeout' => 3,
				'user-agent' => "Wordfence.com UA " . (defined('WORDFENCE_VERSION') ? WORDFENCE_VERSION : '[Unknown version]'),
			));
			if (is_wp_error($result)) {
				return false;
			}
			if (preg_match('/BLOCKED:(\d+)/', $result['body'], $matches) && (!self::getLog()->isWhitelisted($IP))) {
				return $matches[1];
			}
			return false;
		} catch (Exception $err) {
			return false;
		}
	}
	public static function logoutAction(){
		$userID = get_current_user_id();
		$userDat = get_user_by('id', $userID);
		if(is_object($userDat)){
			self::getLog()->logLogin('logout', 0, $userDat->user_login);
		}
	}
	public static function loginInitAction(){
		if(self::isLockedOut(wfUtils::getIP())){
			require('wfLockedOut.php');
		}
	}
	public static function authActionNew($username, &$passwd){ //As of php 5.4 we must denote passing by ref in the function definition, not the function call (as WordPress core does, which is a bug in WordPress).
		if(self::isLockedOut(wfUtils::getIP())){
			require('wfLockedOut.php');
		}
		if(! $username){ return; }
		$userDat = get_user_by('login', $username);
		$_POST['wordfence_userDat'] = $userDat;
		if(preg_match(self::$passwordCodePattern, $passwd, $matches)){
			$_POST['wordfence_authFactor'] = $matches[1];
			$passwd = preg_replace('/^(.+)\s+(wf[a-z0-9]+)$/i', '$1', $passwd);
			$_POST['pwd'] = $passwd;
		}
	}
	public static function authActionOld($username, $passwd){ //Code is identical to Newer function above except passing by ref ampersand. Some versions of PHP are throwing an error if we include the ampersand in PHP prior to 5.4.
		if(self::isLockedOut(wfUtils::getIP())){
			require('wfLockedOut.php');
		}
		if(! $username){ return; }
		$userDat = get_user_by('login', $username);
		$_POST['wordfence_userDat'] = $userDat;
		if(preg_match(self::$passwordCodePattern, $passwd, $matches)){
			$_POST['wordfence_authFactor'] = $matches[1];
			$passwd = preg_replace('/^(.+)\s+(wf[a-z0-9]+)$/i', '$1', $passwd);
			$_POST['pwd'] = $passwd;
		}
	}
	public static function getWPFileContent($file, $cType, $cName, $cVersion){
		if($cType == 'plugin'){
			if(preg_match('#^/?wp-content/plugins/[^/]+/#', $file)){
				$file = preg_replace('#^/?wp-content/plugins/[^/]+/#', '', $file);
			} else {
				//If user is using non-standard wp-content dir, then use /plugins/ in pattern to figure out what to strip off
				$file = preg_replace('#^.*[^/]+/plugins/[^/]+/#', '', $file);
			}
		} else if($cType == 'theme'){
			if(preg_match('#/?wp-content/themes/[^/]+/#', $file)){
				$file = preg_replace('#/?wp-content/themes/[^/]+/#', '', $file);
			} else {
				$file = preg_replace('#^.*[^/]+/themes/[^/]+/#', '', $file);
			}
		} else if($cType == 'core'){

		} else {
			return array('errorMsg' => "An invalid type was specified to get file.");
		}
		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		try {
			$contResult = $api->binCall('get_wp_file_content', array(
				'v' => wfUtils::getWPVersion(),
				'file' => $file,
				'cType' => $cType,
				'cName' => $cName,
				'cVersion' => $cVersion
				));
			if($contResult['data']){
				return array('fileContent' => $contResult['data']);
			} else {
				throw new Exception("We could not fetch a core WordPress file from the Wordfence API.");
			}
		} catch (Exception $e){
			return array('errorMsg' => wp_kses($e->getMessage(), array()));
		}
	}
	public static function ajax_sendTestEmail_callback(){
		$result = wp_mail($_POST['email'], "Wordfence Test Email", "This is a test email from " . site_url() . ".\nThe IP address that requested this was: " . wfUtils::getIP());
		$result = $result ? 'True' : 'False';
		return array('result' => $result);
	}
	public static function ajax_loadAvgSitePerf_callback(){
		$limit = preg_match('/^\d+$/', $_POST['limit']) ? $_POST['limit'] : 10;
		$wfdb = new wfDB();
		global $wpdb;
		$p = $wpdb->base_prefix;
		$rec = $wfdb->querySingleRec("select round(avg(domainLookupEnd),0) as domainLookupEnd, round(avg(connectEnd),0) as connectEnd, round(avg(responseStart),0) as responseStart, round(avg(responseEnd),0) as responseEnd, round(avg(domReady),0) as domReady, round(avg(loaded),0) as loaded from (select domainLookupEnd, connectEnd, responseStart, responseEnd, domReady, loaded from $p"."wfPerfLog order by ctime desc limit %d) as T", $limit);
		return $rec;
	}
	public static function ajax_addTwoFactor_callback(){
		if(! wfConfig::get('isPaid')){
			return array('errorMsg' => 'Cellphone Sign-in is only available to paid members. <a href="https://www.wordfence.com/gnl1twoFac3/wordfence-signup/" target="_blank">Click here to upgrade now.</a>');
		}
		$username = sanitize_text_field($_POST['username']);
		$phone = sanitize_text_field($_POST['phone']);
		$user = get_user_by('login', $username);
		if(! $user){
			return array('errorMsg' => "The username you specified does not exist.");
		}
		if(! preg_match('/^\+\d[\d\-]+$/', $phone)){
			return array('errorMsg' => "The phone number you entered must start with a '+', then country code and then area code and number. It can only contain the starting plus sign and then numbers and dashes. It can not contain spaces. For example, a number in the United States with country code '1' would look like this: +1-123-555-1234");
		}
		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		try {
			$codeResult = $api->call('twoFactor_verification', array(), array('phone' => $phone));
		} catch(Exception $e){
			return array('errorMsg' => "Could not contact Wordfence servers to generate a verification code: " . wp_kses($e->getMessage(), array()) );
		}
		if(isset($codeResult['ok']) && $codeResult['ok']){
			$code = $codeResult['code'];
		} else if(isset($codeResult['errorMsg']) && $codeResult['errorMsg']){
			return array('errorMsg' => wp_kses($codeResult['errorMsg'], array()));
		} else {
			wordfence::status(4, 'info', "Could not gen verification code: " . var_export($codeResult, true));
			return array('errorMsg' => "We could not generate a verification code.");
		}
		self::twoFactorAdd($user->ID, $phone, $code);
		return array(
			'ok' => 1,
			'userID' => $user->ID,
			'username' => $username,
			'phone' => $phone
			);
	}
	public static function ajax_twoFacActivate_callback(){
		$userID = sanitize_text_field($_POST['userID']);
		$code = sanitize_text_field($_POST['code']);
		$twoFactorUsers = wfConfig::get_ser('twoFactorUsers', array());
		if(! is_array($twoFactorUsers)){
			$twoFactorUsers = array();
		}
		$found = false;
		$user = false;
		for($i = 0; $i < sizeof($twoFactorUsers); $i++){
			if($twoFactorUsers[$i][0] == $userID){
				if($twoFactorUsers[$i][2] == $code){
					$twoFactorUsers[$i][3] = 'activated';
					$found = true;
					$user = $twoFactorUsers[$i];
					break;
				} else {
					return array('errorMsg' => "That is not the correct code. Please look for an SMS containing an activation code on the phone with number: " . wp_kses($twoFactorUsers[$i][1], array()) );
				}
			}
		}
		if(! $found){
			return array('errorMsg' => "We could not find the user you are trying to activate. They may have been removed from the list of Cellphone Sign-in users. Please reload this page.");
		}
		wfConfig::set_ser('twoFactorUsers', $twoFactorUsers);
		$WPuser = get_userdata($userID);
		return array(
			'ok' => 1,
			'userID' => $userID,
			'username' => $WPuser->user_login,
			'phone' => $user[1],
			'status' => 'activated'
			);
	}
	private static function twoFactorAdd($ID, $phone, $code){
		$twoFactorUsers = wfConfig::get_ser('twoFactorUsers', array());
		if(! is_array($twoFactorUsers)){
			$twoFactorUsers = array();
		}
		for($i = 0; $i < sizeof($twoFactorUsers); $i++){
			if($twoFactorUsers[$i][0] == $ID || (! $twoFactorUsers[$i][0]) ){
				array_splice($twoFactorUsers, $i, 1);
				$i--;
			}
		}
		$twoFactorUsers[] = array($ID, $phone, $code, 'notActivated', time() + (86400 * 100)); //expiry of code is 100 days in future
		wfConfig::set_ser('twoFactorUsers', $twoFactorUsers);
	}
	public static function ajax_loadTwoFactor_callback(){
		$users = wfConfig::get_ser('twoFactorUsers', array());
		$ret = array();
		foreach($users as $user){
			$WPuser = get_userdata($user[0]);
			if($user){
				$ret[] = array(
					'userID' => $user[0],
					'username' => $WPuser->user_login,
					'phone' => $user[1],
					'status' => $user[3]
					);
			}
		}
		return array('ok' => 1, 'users' => $ret);
	}
	public static function ajax_twoFacDel_callback(){
		$ID = $_POST['userID'];
		$twoFactorUsers = wfConfig::get_ser('twoFactorUsers', array());
		if(! is_array($twoFactorUsers)){
			$twoFactorUsers = array();
		}
		$deleted = false;
		for($i = 0; $i < sizeof($twoFactorUsers); $i++){
			if($twoFactorUsers[$i][0] == $ID){
				array_splice($twoFactorUsers, $i, 1);
				$deleted = true;
				$i--;
			}
		}
		wfConfig::set_ser('twoFactorUsers', $twoFactorUsers);
		if($deleted){
			return array('ok' => 1, 'userID' => $ID);
		} else {
			return array('errorMsg' => "That user has already been removed from the list.");
		}
	}
	public static function ajax_saveScanSchedule_callback(){
		if(! wfConfig::get('isPaid')){
			return array('errorMsg' => "Sorry but this feature is only available for paid customers.");
		}
		$schedDays = explode('|', $_POST['schedTxt']);
		$schedule = array();
		for($day = 0; $day <= 6; $day++){
			$schedule[$day] = explode(',', $schedDays[$day]);
		}
		$schedMode = $_POST['schedMode'];
		wfConfig::set_ser('scanSched', $schedule);
		wfConfig::set('schedMode', $schedMode);
		wordfence::scheduleScans();
		$nextTime = self::getNextScanStartTime();
		return array(
			'ok' => 1,
			'nextStart' => ($nextTime ? $nextTime : '')
			);
	}
	public static function getNextScanStartTime(){
		$nextTime = false;
		$cron = _get_cron_array();
		foreach($cron as $key => $val){
			if(isset($val['wordfence_start_scheduled_scan'])){
				$nextTime = $key;
				break;
			}
		}
		return ($nextTime ? date('l jS \of F Y H:i:s A', $nextTime + (3600 * get_option('gmt_offset'))) : '');
	}
	public static function wordfenceStartScheduledScan(){

		//If scheduled scans are not enabled in the global config option, then don't run a scheduled scan.
		if(wfConfig::get('scheduledScansEnabled') != '1'){
			return;
		}

		//This prevents scheduled scans from piling up on low traffic blogs and all being run at once.
		//Only one scheduled scan runs within a given 60 min window. Won't run if another scan has run within 30 mins.
		$lastScanStart = wfConfig::get('lastScheduledScanStart', 0);
		if($lastScanStart && (time() - $lastScanStart) < 1800){
			//A scheduled scan was started in the last 30 mins, so skip this one.
			return;
		}
		wfConfig::set('lastScheduledScanStart', time());
		wordfence::status(1, 'info', "Scheduled Wordfence scan starting at " . date('l jS \of F Y h:i:s A', current_time('timestamp')) );

		//We call this before the scan actually starts to advance the schedule for the next week.
		//This  ensures that if the scan crashes for some reason, the schedule will hold.
		wordfence::scheduleScans();

		wfScanEngine::startScan();
	}
	public static function scheduleScans(){ //Idempotent. Deschedules everything and schedules the following week.
		self::unscheduleAllScans();
		$sched = wfConfig::get_ser('scanSched', array());
		$mode = wfConfig::get('schedMode');
		if($mode == 'manual' && is_array($sched) && is_array($sched[0]) ){
			//Use sched as it is
		} else { //Default to setting scans to run once a day at a randomly selected time.
			$sched = array();
			$runAt = rand(0,23);
			for($day = 0; $day <= 6; $day++){
				$sched[$day] = array();
				for($hour = 0; $hour <= 23; $hour++){
					if($hour == $runAt){
						$sched[$day][$hour] = 1;
					} else {
						$sched[$day][$hour] = 0;
					}
				}
			}
		}
		for($scheduledDay = 0; $scheduledDay <= 6; $scheduledDay++){
			//0 is sunday
			//6 is Saturday
			for($scheduledHour = 0; $scheduledHour <= 23; $scheduledHour++){
				if($sched[$scheduledDay][$scheduledHour]){
					$wpTime = current_time('timestamp');
					$currentDayOfWeek = date('w', $wpTime);
					$daysInFuture = $scheduledDay - $currentDayOfWeek; //It's monday and scheduledDay is Wed (3) then result is 2 days in future. It's Wed and sched day is monday, then result is 3 - 1 = -2
					if($daysInFuture < 0){ $daysInFuture += 7; } //Turns -2 into 5 days in future
					$currentHour = date('G', $wpTime);
					$secsOffset = ($scheduledHour - $currentHour) * 3600; //Offset from current hour, can be negative
					$secondsInFuture = ($daysInFuture * 86400) + $secsOffset; //Can be negative, so we schedule those 1 week ahead
					if($secondsInFuture < 1){
						$secondsInFuture += (86400 * 7); //Add a week
					}
					$futureTime = time() - (time() % 3600) + $secondsInFuture; //Modulo rounds down to top of the hour
					$futureTime += rand(0,3600); //Prevent a stampede of scans on our scanning server
					wordfence::status(4, 'info', "Scheduled time for day $scheduledDay hour $scheduledHour is: " . date('l jS \of F Y h:i:s A', $futureTime));
					self::scheduleSingleScan($futureTime);
				}
			}
		}
	}
	private static function scheduleSingleScan($futureTime){
		// Removed ability to activate on network site in v5.3.12
		if (is_main_site()) {
			wp_schedule_single_event($futureTime, 'wordfence_start_scheduled_scan');
		}
	}
	private static function unscheduleAllScans(){
		wp_clear_scheduled_hook('wordfence_start_scheduled_scan');
	}
	public static function ajax_saveCountryBlocking_callback(){
		if(! wfConfig::get('isPaid')){
			return array('errorMsg' => "Sorry but this feature is only available for paid customers.");
		}
		wfConfig::set('cbl_action', $_POST['blockAction']);
		wfConfig::set('cbl_countries', $_POST['codes']);
		wfConfig::set('cbl_redirURL', $_POST['redirURL']);
		wfConfig::set('cbl_loggedInBlocked', $_POST['loggedInBlocked']);
		wfConfig::set('cbl_loginFormBlocked', $_POST['loginFormBlocked']);
		wfConfig::set('cbl_restOfSiteBlocked', $_POST['restOfSiteBlocked']);
		wfConfig::set('cbl_bypassRedirURL', $_POST['bypassRedirURL']);
		wfConfig::set('cbl_bypassRedirDest', $_POST['bypassRedirDest']);
		wfConfig::set('cbl_bypassViewURL', $_POST['bypassViewURL']);
		return array('ok' => 1);
	}
	public static function ajax_sendActivityLog_callback(){
		$content = "SITE: " . site_url() . "\nPLUGIN VERSION: " . WORDFENCE_VERSION . "\nWP VERSION: " . wfUtils::getWPVersion() . "\nAPI KEY: " . wfConfig::get('apiKey') . "\nADMIN EMAIL: " . get_option('admin_email') . "\nLOG:\n\n";
		$wfdb = new wfDB();
		global $wpdb;
		$p = $wpdb->base_prefix;
		$q = $wfdb->querySelect("select ctime, level, type, msg from $p"."wfStatus order by ctime desc limit 10000");
		$timeOffset = 3600 * get_option('gmt_offset');
		foreach($q as $r){
			if($r['type'] == 'error'){
				$content .= "\n";
			}
			$content .= date(DATE_RFC822, $r['ctime'] + $timeOffset) . '::' . sprintf('%.4f', $r['ctime']) . ':' . $r['level'] . ':' . $r['type'] . '::' . wp_kses_data( (string) $r['msg']) . "\n";
		}
		$content .= "\n\n";

		ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_get_clean();

		$content .= $phpinfo;

		wp_mail($_POST['email'], "Wordfence Activity Log", $content);
		return array('ok' => 1);
	}
	public static function ajax_startTourAgain_callback(){
		wfConfig::set('tourClosed', 0);
		return array('ok' => 1);
	}
	public static function ajax_downgradeLicense_callback(){
		$api = new wfAPI('', wfUtils::getWPVersion());
		try {
			$keyData = $api->call('get_anon_api_key');
			if($keyData['ok'] && $keyData['apiKey']){
				wfConfig::set('apiKey', $keyData['apiKey']);
				wfConfig::set('isPaid', 0);
				//When downgrading we must disable all two factor authentication because it can lock an admin out if we don't.
				wfConfig::set_ser('twoFactorUsers', array());
			} else {
				throw new Exception("Could not understand the response we received from the Wordfence servers when applying for a free API key.");
			}
		} catch(Exception $e){
			return array('errorMsg' => "Could not fetch free API key from Wordfence: " . wp_kses($e->getMessage(), array()));
		}
		return array('ok' => 1);
	}
	public static function ajax_tourClosed_callback(){
		wfConfig::set('tourClosed', 1);
		return array('ok' => 1);
	}
	public static function ajax_welcomeClosed_callback(){
		wfConfig::set('welcomeClosed', 1);
		return array('ok' => 1);
	}
	public static function postRowActions($actions, $post){
		if(wfUtils::isAdmin()){
			$actions = array_merge($actions, array(
				'wfCachePurge' => '<a href="#" onclick="wordfenceExt.removeFromCache(\'' . $post->ID . '\'); return false;">Remove from Wordfence cache</a>'
				));
		}
		return $actions;
	}
	public static function pageRowActions($actions, $post){
		if(wfUtils::isAdmin()){
			$actions = array_merge($actions, array(
				'wfCachePurge' => '<a href="#" onclick="wordfenceExt.removeFromCache(\'' . $post->ID . '\'); return false;">Remove from Wordfence cache</a>'
				));
		}
		return $actions;
	}
	public static function postSubmitboxStart(){
		if(wfUtils::isAdmin()){
			global $post;
			echo '<div><a href="#" onclick="wordfenceExt.removeFromCache(\'' . $post->ID . '\'); return false;">Remove from Wordfence cache</a></div>';
		}
	}
	public static function disablePermalinksFilter($newVal, $oldVal){
		if(wfConfig::get('cacheType', false) == 'falcon' && $oldVal && (! $newVal) ){ //Falcon is enabled and admin is disabling permalinks
			wfCache::addHtaccessCode('remove');
			//if($err){ return $oldVal; } //We might want to not allow the user to disable permalinks if we can't disable falcon. Allowing it for now.
			wfCache::updateBlockedIPs('remove');
			//if($err){ return $oldVal; } //We might want to not allow the user to disable permalinks if we can't disable falcon. Allowing it for now.
			wfConfig::set('cacheType', false);
		}
		return $newVal;
	}
	public static function ajax_autoUpdateChoice_callback(){
		$choice = $_POST['choice'];
		wfConfig::set('autoUpdateChoice', '1');
		if($choice == 'yes'){
			wfConfig::set('autoUpdate', '1');
		} else {
			wfConfig::set('autoUpdate', '0');
		}
		return array('ok' => 1);
	}
	public static function ajax_removeFromCache_callback(){
		$id = $_POST['id'];
		$link = get_permalink($id);
		if(preg_match('/^https?:\/\/([^\/]+)(.*)$/i', $link, $matches)){
			$host = $matches[1];
			$URI = $matches[2];
			if(! $URI){
				$URI = '/';
			}
			$sslFile = wfCache::fileFromURI($host, $URI, true); //SSL
			$normalFile = wfCache::fileFromURI($host, $URI, false); //non-SSL
			@unlink($sslFile);
			@unlink($sslFile . '_gzip');
			@unlink($normalFile);
			@unlink($normalFile . '_gzip');
		}
		return array('ok' => 1);
	}
	public static function ajax_saveCacheOptions_callback(){
		$changed = false;
		if($_POST['allowHTTPSCaching'] != wfConfig::get('allowHTTPSCaching', false)){
			$changed = true;
		}
		wfConfig::set('allowHTTPSCaching', $_POST['allowHTTPSCaching'] == '1' ? 1 : 0);
		wfConfig::set('addCacheComment', $_POST['addCacheComment'] == 1 ? '1' : 0);
		wfConfig::set('clearCacheSched', $_POST['clearCacheSched'] == 1 ? '1' : 0);
		if($changed && wfConfig::get('cacheType', false) == 'falcon'){
			$err = wfCache::addHtaccessCode('add');
			if($err){
				return array('updateErr' => "Wordfence could not edit your .htaccess file. The error was: " . $err, 'code' => wfCache::getHtaccessCode() );
			}
		}
		wfCache::scheduleCacheClear();
		return array('ok' => 1);
	}
	public static function ajax_saveCacheConfig_callback(){
		$cacheType = $_POST['cacheType'];
		if($cacheType == 'falcon' || $cacheType == 'php'){
			$plugins = get_plugins();
			$badPlugins = array();
			foreach($plugins as $pluginFile => $data){
				if(is_plugin_active($pluginFile)){
					if($pluginFile == 'w3-total-cache/w3-total-cache.php'){
						$badPlugins[] = "W3 Total Cache";
					} else if($pluginFile == 'quick-cache/quick-cache.php'){
						$badPlugins[] = "Quick Cache";
					} else if($pluginFile == "wp-super-cache/wp-cache.php"){
						$badPlugins[] = "WP Super Cache";
					} else if($pluginFile == "wp-fast-cache/wp-fast-cache.php"){
						$badPlugins[] = "WP Fast Cache";
					} else if($pluginFile == "wp-fastest-cache/wpFastestCache.php"){
						$badPlugins[] = "WP Fastest Cache";
					}
				}
			}
			if(count($badPlugins) > 0){
				return array('errorMsg' => "You can not enable caching in Wordfence with other caching plugins enabled. This may cause conflicts. You need to disable other caching plugins first. Wordfence caching is very fast and does not require other caching plugins to be active. The plugins you have that conflict are: " . implode(', ', $badPlugins) . ". Disable these plugins, then return to this page and enable Wordfence caching.");
			}
			$siteURL = site_url();
			if(preg_match('/^https?:\/\/[^\/]+\/[^\/]+\/[^\/]+\/.+/i', $siteURL)){
				return array('errorMsg' => "Wordfence caching currently does not support sites that are installed in a subdirectory and have a home page that is more than 2 directory levels deep. e.g. we don't support sites who's home page is http://example.com/levelOne/levelTwo/levelThree");
			}
		}
		if($cacheType == 'falcon'){
			if(! get_option('permalink_structure', '')){
				return array('errorMsg' => "You need to enable Permalinks for your site to use Falcon Engine. You can enable Permalinks in WordPress by going to the Settings - Permalinks menu and enabling it there. Permalinks change your site URL structure from something that looks like /p=123 to pretty URLs like /my-new-post-today/ that are generally more search engine friendly.");
			}
		}
		$warnHtaccess = false;
		if($cacheType == 'disable' || $cacheType == 'php'){
			$removeError = wfCache::addHtaccessCode('remove');
			$removeError2 = wfCache::updateBlockedIPs('remove');
			if($removeError || $removeError2){
				$warnHtaccess = true;
			}
		}
		if($cacheType == 'php' || $cacheType == 'falcon'){
			$err = wfCache::cacheDirectoryTest();
			if($err){
				return array('ok' => 1, 'heading' => "Could not write to cache directory", 'body' => "To enable caching, Wordfence needs to be able to create and write to the /wp-content/wfcache/ directory. We did some tests that indicate this is not possible. You need to manually create the /wp-content/wfcache/ directory and make it writable by Wordfence. The error we encountered was during our tests was: $err");
			}
		}

		//Mainly we clear the cache here so that any footer cache diagnostic comments are rebuilt. We could just leave it intact unless caching is being disabled.
		if($cacheType != wfConfig::get('cacheType', false)){
			wfCache::scheduleCacheClear();
		}
		$htMsg = "";
		if($warnHtaccess){
			$htMsg = " <strong style='color: #F00;'>Warning: We could not remove the caching code from your .htaccess file. you need to remove this manually yourself.</strong> ";
		}
		if($cacheType == 'disable'){
			wfConfig::set('cacheType', false);
			return array('ok' => 1, 'heading' => "Caching successfully disabled.", 'body' => "{$htMsg}Caching has been disabled on your system.<br /><br /><center><input type='button' name='wfReload' value='Click here now to refresh this page' onclick='window.location.reload(true);' /></center>");
		} else if($cacheType == 'php'){
			wfConfig::set('cacheType', 'php');
			return array('ok' => 1, 'heading' => "Wordfence Basic Caching Enabled", 'body' => "{$htMsg}Wordfence basic caching has been enabled on your system.<br /><br /><center><input type='button' name='wfReload' value='Click here now to refresh this page' onclick='window.location.reload(true);' /></center>");
		} else if($cacheType == 'falcon'){
			if($_POST['noEditHtaccess'] != '1'){
				$err = wfCache::addHtaccessCode('add');
				if($err){
					return array('ok' => 1, 'heading' => "Wordfence could not edit .htaccess", 'body' => "Wordfence could not edit your .htaccess code. The error was: " . $err);
				}
			}
			wfConfig::set('cacheType', 'falcon');
			wfCache::scheduleUpdateBlockedIPs(); //Runs every 5 mins until we change cachetype
			return array('ok' => 1, 'heading' => "Wordfence Falcon Engine Activated!", 'body' => "Wordfence Falcon Engine has been activated on your system. You will see this icon appear on the Wordfence admin pages as long as Falcon is active indicating your site is running in high performance mode:<div class='wfFalconImage'></div><center><input type='button' name='wfReload' value='Click here now to refresh this page' onclick='window.location.reload(true);' /></center>");
		}
		return array('errorMsg' => "An error occurred.");
	}
	public static function ajax_getCacheStats_callback(){
		$s = wfCache::getCacheStats();
		if($s['files'] == 0){
			return array('ok' => 1, 'heading' => 'Cache Stats', 'body' => "The cache is currently empty. It may be disabled or it may have been recently cleared.");
		}
		$body = 'Total files in cache: ' . $s['files'] .
			'<br />Total directories in cache: ' . $s['dirs'] .
			'<br />Total data: ' . $s['data'] . 'KB';
		if($s['compressedFiles'] > 0){
			$body .= '<br />Files: ' . $s['uncompressedFiles'] .
				'<br />Data: ' . $s['uncompressedKBytes'] . 'KB' .
				'<br />Compressed files: ' . $s['compressedFiles'] .
				'<br />Compressed data: ' . $s['compressedKBytes'] . 'KB';
		}
		if($s['largestFile'] > 0){
			$body .= '<br />Largest file: ' . $s['largestFile'] . 'KB';
		}
		if($s['oldestFile'] !== false){
			$body .= '<br />Oldest file in cache created ';
			if(time() - $s['oldestFile'] < 300){
				$body .= (time() - $s['oldestFile']) . ' seconds ago';
			} else {
				$body .= human_time_diff($s['oldestFile']) . ' ago.';
			}
		}
		if($s['newestFile'] !== false){
			$body .= '<br />Newest file in cache created ';
			if(time() - $s['newestFile'] < 300){
				$body .= (time() - $s['newestFile']) . ' seconds ago';
			} else {
				$body .= human_time_diff($s['newestFile']) . ' ago.';
			}
		}

		return array('ok' => 1, 'heading' => 'Cache Stats', 'body' => $body);
	}
	public static function ajax_clearPageCache_callback(){
		$stats = wfCache::clearPageCache();
		if($stats['error']){
			$body = "A total of " . $stats['totalErrors'] . " errors occurred while trying to clear your cache. The last error was: " . $stats['error'];
			return array('ok' => 1, 'heading' => 'Error occurred while clearing cache', 'body' => $body );
		}
		$body = "A total of " . $stats['filesDeleted'] . ' files were deleted and ' . $stats['dirsDeleted'] . ' directories were removed. We cleared a total of ' . $stats['totalData'] . 'KB of data in the cache.';
		if($stats['totalErrors'] > 0){
			$body .=  ' A total of ' . $stats['totalErrors'] . ' errors were encountered. This probably means that we could not remove some of the files or directories in the cache. Please use your CPanel or file manager to remove the rest of the files in the directory: ' . WP_CONTENT_DIR . '/wfcache/';
		}
		return array('ok' => 1, 'heading' => 'Page Cache Cleared', 'body' => $body );
	}
	public static function ajax_updateConfig_callback(){
		$key = $_POST['key'];
		$val = $_POST['val'];
		wfConfig::set($key, $val);
		return array('ok' => 1);
	}
	public static function ajax_checkFalconHtaccess_callback(){
		if(wfUtils::isNginx()){
			return array('nginx' => 1);
		}
		$file = wfCache::getHtaccessPath();
		if(! $file){
			return array('err' => "We could not find your .htaccess file to modify it.", 'code' => wfCache::getHtaccessCode() );
		}
		$fh = @fopen($file, 'r+');
		if(! $fh){
			$err = error_get_last();
			return array('err' => "We found your .htaccess file but could not open it for writing: " . $err['message'], 'code' => wfCache::getHtaccessCode() );
		}
		return array('ok' => 1);
	}
	public static function ajax_downloadHtaccess_callback(){
		$url = site_url();
		$url = preg_replace('/^https?:\/\//i', '', $url);
		$url = preg_replace('/[^a-zA-Z0-9\.]+/', '_', $url);
		$url = preg_replace('/^_+/', '', $url);
		$url = preg_replace('/_+$/', '', $url);
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="htaccess_Backup_for_' . $url . '.txt"');
		$file = wfCache::getHtaccessPath();
		readfile($file);
		die();
	}
	public static function ajax_addCacheExclusion_callback(){
		$ex = wfConfig::get('cacheExclusions', false);
		if($ex){
			$ex = unserialize($ex);
		} else {
			$ex = array();
		}
		$ex[] = array(
			'pt' => $_POST['patternType'],
			'p' => $_POST['pattern'],
			'id' => microtime(true)
			);
		wfConfig::set('cacheExclusions', serialize($ex));
		wfCache::scheduleCacheClear();
		if(wfConfig::get('cacheType', false) == 'falcon' && preg_match('/^(?:uac|uaeq|cc)$/', $_POST['patternType'])){
			if(wfCache::addHtaccessCode('add')){ //rewrites htaccess rules
				return array('errorMsg' => "We added the rule you requested but could not modify your .htaccess file. Please delete this rule, check the permissions on your .htaccess file and then try again.");
			}
		}
		return array('ok' => 1);
	}
	public static function ajax_removeCacheExclusion_callback(){
		$id = $_POST['id'];
		$ex = wfConfig::get('cacheExclusions', false);
		if(! $ex){
			return array('ok' => 1);
		}
		$ex = unserialize($ex);
		$rewriteHtaccess = false;
		for($i = 0; $i < sizeof($ex); $i++){
			if((string)$ex[$i]['id'] == (string)$id){
				if(wfConfig::get('cacheType', false) == 'falcon' && preg_match('/^(?:uac|uaeq|cc)$/', $ex[$i]['pt'])){
					$rewriteHtaccess = true;
				}
				array_splice($ex, $i, 1);
				//Dont break in case of dups
			}
		}
		wfConfig::set('cacheExclusions', serialize($ex));
		if($rewriteHtaccess && wfCache::addHtaccessCode('add')){ //rewrites htaccess rules
			return array('errorMsg', "We removed that rule but could not rewrite your .htaccess file. You're going to have to manually remove this rule from your .htaccess file. Please reload this page now.");
		}
		return array('ok' => 1);
	}
	public static function ajax_loadCacheExclusions_callback(){
		$ex = wfConfig::get('cacheExclusions', false);
		if(! $ex){
			return array('ex' => false);
		}
		$ex = unserialize($ex);
		return array('ex' => $ex);
	}
	public static function ajax_saveConfig_callback(){
		$reload = '';
		$opts = wfConfig::parseOptions();
		$emails = array();
		foreach(explode(',', preg_replace('/[\r\n\s\t]+/', '', $opts['alertEmails'])) as $email){
			if(strlen($email) > 0){
				$emails[] = $email;
			}
		}
		if(sizeof($emails) > 0){
			$badEmails = array();
			foreach($emails as $email){
				if(! preg_match('/^[^@]+@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,11})$/i', $email)){
					$badEmails[] = $email;
				}
			}
			if(sizeof($badEmails) > 0){
				return array('errorMsg' => "The following emails are invalid: " . wp_kses(implode(', ', $badEmails), array()) );
			}
			$opts['alertEmails'] = implode(',', $emails);
		} else {
			$opts['alertEmails'] = '';
		}
		$opts['scan_exclude'] = wfUtils::cleanupOneEntryPerLine($opts['scan_exclude']);
		$whiteIPs = array();
		foreach(explode(',', preg_replace('/[\r\n\s\t]+/', '', $opts['whitelisted'])) as $whiteIP){
			if(strlen($whiteIP) > 0){
				$whiteIPs[] = $whiteIP;
			}
		}
		if(sizeof($whiteIPs) > 0){
			$badWhiteIPs = array();
			$range = new wfUserIPRange();
			foreach($whiteIPs as $whiteIP){
				$range->setIPString($whiteIP);
				if (!$range->isValidRange()) {
					$badWhiteIPs[] = $whiteIP;
				}
			}
			if(sizeof($badWhiteIPs) > 0){
				return array('errorMsg' => "Please make sure you separate your IP addresses with commas. The following whitelisted IP addresses are invalid: " . wp_kses(implode(', ', $badWhiteIPs), array()) );
			}
			$opts['whitelisted'] = implode(',', $whiteIPs);
		} else {
			$opts['whitelisted'] = '';
		}
		$validUsers = array();
		$invalidUsers = array();
		foreach(explode(',', $opts['liveTraf_ignoreUsers']) as $val){
			$val = trim($val);
			if(strlen($val) > 0){
				if(get_user_by('login', $val)){
					$validUsers[] = $val;
				} else {
					$invalidUsers[] = $val;
				}
			}
		}
		$userBlacklist = array();
		foreach(explode(',', $opts['loginSec_userBlacklist']) as $user){
			$user = trim($user);
			if(strlen($user) > 0){
				$userBlacklist[] = $user;
			}
		}
		if(sizeof($userBlacklist) > 0){
			$opts['loginSec_userBlacklist'] = implode(',', $userBlacklist);
		} else {
			$opts['loginSec_userBlacklist'] = '';
		}

		$opts['apiKey'] = trim($opts['apiKey']);
		if($opts['apiKey'] && (! preg_match('/^[a-fA-F0-9]+$/', $opts['apiKey'])) ){ //User entered something but it's garbage.
			return array('errorMsg' => "You entered an API key but it is not in a valid format. It must consist only of characters A to F and 0 to 9.");
		}

		if(sizeof($invalidUsers) > 0){
			return array('errorMsg' => "The following users you selected to ignore in live traffic reports are not valid on this system: " . wp_kses(implode(', ', $invalidUsers), array()) );
		}
		if(sizeof($validUsers) > 0){
			$opts['liveTraf_ignoreUsers'] = implode(',', $validUsers);
		} else {
			$opts['liveTraf_ignoreUsers'] = '';
		}

		$validIPs = array();
		$invalidIPs = array();
		foreach(explode(',', preg_replace('/[\r\n\s\t]+/', '', $opts['liveTraf_ignoreIPs'])) as $val){
			if(strlen($val) > 0){
				if(wfUtils::isValidIP($val)){
					$validIPs[] = $val;
				} else {
					$invalidIPs[] = $val;
				}
			}
		}
		if(sizeof($invalidIPs) > 0){
			return array('errorMsg' => "The following IPs you selected to ignore in live traffic reports are not valid: " . wp_kses(implode(', ', $invalidIPs), array()) );
		}
		if(sizeof($validIPs) > 0){
			$opts['liveTraf_ignoreIPs'] = implode(',', $validIPs);
		}

		if(preg_match('/[a-zA-Z0-9\d]+/', $opts['liveTraf_ignoreUA'])){
			$opts['liveTraf_ignoreUA'] = trim($opts['liveTraf_ignoreUA']);
		} else {
			$opts['liveTraf_ignoreUA'] = '';
		}
		if(! $opts['other_WFNet']){
			$wfdb = new wfDB();
			global $wpdb;
			$p = $wpdb->base_prefix;
			$wfdb->queryWrite("delete from $p"."wfBlocks where wfsn=1 and permanent=0");
		}
		if($opts['howGetIPs'] != wfConfig::get('howGetIPs', '')){
			$reload = 'reload';
		}
		$regenerateHtaccess = false;
		if(wfConfig::get('bannedURLs', false) != $opts['bannedURLs']){
			$regenerateHtaccess = true;
		}

		foreach($opts as $key => $val){
			if($key != 'apiKey'){ //Don't save API key yet
				wfConfig::set($key, $val);
			}
		}
		if($regenerateHtaccess && wfConfig::get('cacheType') == 'falcon'){
			wfCache::addHtaccessCode('add');
		}

		if($opts['autoUpdate'] == '1'){
			wfConfig::enableAutoUpdate();
		} else if($opts['autoUpdate'] == '0'){
			wfConfig::disableAutoUpdate();
		}

		try {
			if ($opts['disableCodeExecutionUploads']) {
				wfConfig::disableCodeExecutionForUploads();
			} else {
				wfConfig::removeCodeExecutionProtectionForUploads();
			}
		} catch (wfConfigException $e) {
			return array('errorMsg' => $e->getMessage());
		}

		if (!empty($opts['email_summary_enabled'])) {
			wfConfig::set('email_summary_enabled', 1);
			wfConfig::set('email_summary_interval', $opts['email_summary_interval']);
			wfConfig::set('email_summary_excluded_directories', $opts['email_summary_excluded_directories']);
			wfActivityReport::scheduleCronJob();
		} else {
			wfConfig::set('email_summary_enabled', 0);
			wfActivityReport::disableCronJob();
		}

		if (wfConfig::get('other_hideWPVersion')) {
			wfUtils::hideReadme();
		} else {
			wfUtils::showReadme();
		}

		$paidKeyMsg = false;


		if(! $opts['apiKey']){ //Empty API key (after trim above), then try to get one.
			$api = new wfAPI('', wfUtils::getWPVersion());
			try {
				$keyData = $api->call('get_anon_api_key');
				if($keyData['ok'] && $keyData['apiKey']){
					wfConfig::set('apiKey', $keyData['apiKey']);
					wfConfig::set('isPaid', 0);
					$reload = 'reload';
				} else {
					throw new Exception("We could not understand the Wordfence server's response because it did not contain an 'ok' and 'apiKey' element.");
				}
			} catch(Exception $e){
				return array('errorMsg' => "Your options have been saved, but we encountered a problem. You left your API key blank, so we tried to get you a free API key from the Wordfence servers. However we encountered a problem fetching the free key: " . wp_kses($e->getMessage(), array()) );
			}
		} else if($opts['apiKey'] != wfConfig::get('apiKey')){
			$api = new wfAPI($opts['apiKey'], wfUtils::getWPVersion());
			try {
				$res = $api->call('check_api_key', array(), array());
				if($res['ok'] && isset($res['isPaid'])){
					wfConfig::set('apiKey', $opts['apiKey']);
					$reload = 'reload';
					wfConfig::set('isPaid', $res['isPaid']); //res['isPaid'] is boolean coming back as JSON and turned back into PHP struct. Assuming JSON to PHP handles bools.
					if($res['isPaid']){
						$paidKeyMsg = true;
					}
				} else {
					throw new Exception("We could not understand the Wordfence API server reply when updating your API key.");
				}
			} catch (Exception $e){
				return array('errorMsg' => "Your options have been saved. However we noticed you changed your API key and we tried to verify it with the Wordfence servers and received an error: " . wp_kses($e->getMessage(), array()) );
			}
		} else {
			$api = new wfAPI($opts['apiKey'], wfUtils::getWPVersion());
			$api->call('ping_api_key', array(), array());
		}
		return array('ok' => 1, 'reload' => $reload, 'paidKeyMsg' => $paidKeyMsg );
	}
	public static function ajax_clearAllBlocked_callback(){
		$op = $_POST['op'];
		$wfLog = self::getLog();
		if($op == 'blocked'){
			wordfence::status(1, 'info', "Ajax request received to unblock All IP's including permanent blocks.");
			$wfLog->unblockAllIPs();
		} else if($op == 'locked'){
			$wfLog->unlockAllIPs();
		}
		return array('ok' => 1);
	}
	public static function ajax_unlockOutIP_callback(){
		$IP = $_POST['IP'];
		self::getLog()->unlockOutIP($IP);
		return array('ok' => 1);
	}
	public static function ajax_unblockIP_callback(){
		$IP = $_POST['IP'];
		self::getLog()->unblockIP($IP);
		return array('ok' => 1);
	}
	public static function ajax_permBlockIP_callback(){
		$IP = $_POST['IP'];
		self::getLog()->blockIP($IP, "Manual permanent block by admin", false, true);
		return array('ok' => 1);
	}
	public static function ajax_loadStaticPanel_callback(){
		$mode = $_POST['mode'];
		$wfLog = self::getLog();
		if($mode == 'topScanners' || $mode == 'topLeechers'){
			$results = $wfLog->getLeechers($mode);
		} else if($mode == 'blockedIPs'){
			$results = $wfLog->getBlockedIPs();
		} else if($mode == 'lockedOutIPs'){
			$results = $wfLog->getLockedOutIPs();
		} else if($mode == 'throttledIPs'){
			$results = $wfLog->getThrottledIPs();
		}
		return array('ok' => 1, 'results' => $results);
	}
	public static function ajax_loadBlockRanges_callback(){
		$results = self::getLog()->getRanges();
		return array('ok' => 1, 'results' => $results);
	}
	public static function ajax_unblockRange_callback(){
		$id = trim($_POST['id']);
		self::getLog()->unblockRange($id);
		return array('ok' => 1);
	}

	/**
	 * @return array
	 */
	public static function ajax_blockIPUARange_callback(){
		$ipRange = trim($_POST['ipRange']);
		$hostname = trim($_POST['hostname']);
		$uaRange = trim($_POST['uaRange']);
		$referer = trim($_POST['referer']);
		$reason = trim($_POST['reason']);
		if (preg_match('/\|+/', $ipRange . $uaRange . $referer . $hostname)) {
			return array('err' => 1, 'errorMsg' => "You are not allowed to include a pipe character \"|\" in your IP range, browser pattern or referer");
		}
		if ((!$ipRange) && wfUtils::isUABlocked($uaRange)) {
			return array('err' => 1, 'errorMsg' => "The browser pattern you specified will block you from your own website. We have not accepted this pattern to protect you from being blocked.");
		}
		if (fnmatch($referer, site_url(), FNM_CASEFOLD)) {
			return array('err' => 1, 'errorMsg' => "The referer pattern you specified matches your own website and will block visitors as they surf from one page to another on your site. You can't enter this pattern.");
		}

		if ($ipRange) {
			list($start_range, $end_range) = explode('-', $ipRange);
			if (!wfUtils::isValidIP($start_range) || !wfUtils::isValidIP($end_range)) {
				return array('err' => 1, 'errorMsg' => "The IP range you specified is not valid. Please specify an IP range like the following example: \"1.2.3.4 - 1.2.3.8\" without quotes.");
			}
			$ip1 = wfUtils::inet_pton($start_range);
			$ip2 = wfUtils::inet_pton($end_range);
			if (strcmp($ip1, $ip2) >= 0) {
				return array('err' => 1, 'errorMsg' => "The first IP address in your range must be less than the second IP address in your range.");
			}
			$clientIP = wfUtils::inet_pton(wfUtils::getIP());
			if (strcmp($ip1, $clientIP) <= 0 && strcmp($ip2, $clientIP) >= 0) {
				return array('err' => 1, 'errorMsg' => "You are trying to block yourself. Your IP address is " . wp_kses(wfUtils::getIP(), array()) . " which falls into the range " . wp_kses($ipRange, array()) . ". This blocking action has been cancelled so that you don't block yourself from your website.");
			}
			$ipRange = wfUtils::inet_ntop($ip1) . '-' . wfUtils::inet_ntop($ip2);
		}
		if ($hostname && !preg_match('/^[a-z0-9\.\*\-]+$/i', $hostname)) {
			return array('err' => 1, 'errorMsg' => 'The Hostname you specified is not valid');
		}
		$range = $ipRange . '|' . $uaRange . '|' . $referer . '|' . $hostname;
		self::getLog()->blockRange('IU', $range, $reason);
		return array('ok' => 1);
	}
	public static function ajax_whois_callback(){
		$val = trim($_POST['val']);
		$val = preg_replace('/[^a-zA-Z0-9\.\-:]+/', '', $val);
		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		$result = $api->call('whois', array(), array(
			'val' => $val,
			));
		return array('ok' => 1, 'result' => $result['result']);
	}
	public static function ajax_blockIP_callback(){
		$IP = trim($_POST['IP']);
		$perm = (isset($_POST['perm']) && $_POST['perm'] == '1') ? true : false;
		if (!wfUtils::isValidIP($IP)) {
			return array('err' => 1, 'errorMsg' => "Please enter a valid IP address to block.");
		}
		if ($IP == wfUtils::getIP()) {
			return array('err' => 1, 'errorMsg' => "You can't block your own IP address.");
		}
		if (self::getLog()->isWhitelisted($IP)) {
			return array('err' => 1, 'errorMsg' => "The IP address " . wp_kses($IP, array()) . " is whitelisted and can't be blocked or it is in a range of internal IP addresses that Wordfence does not block. You can remove this IP from the whitelist on the Wordfence options page.");
		}
		if (wfConfig::get('neverBlockBG') != 'treatAsOtherCrawlers') { //Either neverBlockVerified or neverBlockUA is selected which means the user doesn't want to block google
			if (wfCrawl::isVerifiedGoogleCrawler($IP)) {
				return array('err' => 1, 'errorMsg' => "The IP address you're trying to block belongs to Google. Your options are currently set to not block these crawlers. Change this in Wordfence options if you want to manually block Google.");
			}
		}
		self::getLog()->blockIP($IP, $_POST['reason'], false, $perm);
		return array('ok' => 1);
	}
	public static function ajax_reverseLookup_callback(){
		$ips = explode(',', $_POST['ips']);
		$res = array();
		foreach($ips as $ip){
			$res[$ip] = wfUtils::reverseLookup($ip);
		}
		return array('ok' => 1, 'ips' => $res);
	}
	public static function ajax_deleteIssue_callback(){
		$wfIssues = new wfIssues();
		$issueID = $_POST['id'];
		$wfIssues->deleteIssue($issueID);
		return array('ok' => 1);
	}
	public static function ajax_updateAllIssues_callback(){
		$op = $_POST['op'];
		$i = new wfIssues();
		if($op == 'deleteIgnored'){
			$i->deleteIgnored();
		} else if($op == 'deleteNew'){
			$i->deleteNew();
		} else if($op == 'ignoreAllNew'){
			$i->ignoreAllNew();
		} else {
			return array('errorMsg' => "An invalid operation was called.");
		}
		return array('ok' => 1);
	}
	public static function ajax_updateIssueStatus_callback(){
		$wfIssues = new wfIssues();
		$status = $_POST['status'];
		$issueID = $_POST['id'];
		if(! preg_match('/^(?:new|delete|ignoreP|ignoreC)$/', $status)){
			return array('errorMsg' => "An invalid status was specified when trying to update that issue.");
		}
		$wfIssues->updateIssue($issueID, $status);
		return array('ok' => 1);
	}
	public static function ajax_killScan_callback(){
		wordfence::status(1, 'info', "Scan kill request received.");
		wordfence::status(10, 'info', "SUM_KILLED:A request was received to kill the previous scan.");
		wfUtils::clearScanLock(); //Clear the lock now because there may not be a scan running to pick up the kill request and clear the lock
		wfScanEngine::requestKill();
		return array(
			'ok' => 1,
			);
	}
	public static function ajax_loadIssues_callback(){
		$i = new wfIssues();
		$iss = $i->getIssues();
		return array(
			'issuesLists' => $iss,
			'summary' => $i->getSummaryItems(),
			'lastScanCompleted' => wfConfig::get('lastScanCompleted')
			);
	}
	public static function ajax_ticker_callback(){
		$wfdb = new wfDB();
		global $wpdb;
		$p = $wpdb->base_prefix;

		$serverTime = $wfdb->querySingle("select unix_timestamp()");
		$jsonData = array(
			'serverTime' => $serverTime,
			'msg' => wp_kses_data( (string) $wfdb->querySingle("select msg from $p"."wfStatus where level < 3 order by ctime desc limit 1"))
			);
		$events = array();
		$alsoGet = $_POST['alsoGet'];
		if(preg_match('/^logList_(404|hit|human|ruser|crawler|gCrawler|loginLogout)$/', $alsoGet, $m)){
			$type = $m[1];
			$newestEventTime = $_POST['otherParams'];
			$listType = 'hits';
			if($type == 'loginLogout'){
				$listType = 'logins';
			}
			$events = self::getLog()->getHits($listType, $type, $newestEventTime);
		} else if($alsoGet == 'perfStats'){
			$newestEventTime = $_POST['otherParams'];
			$events = self::getLog()->getPerfStats($newestEventTime);
		}
		/*
		$longest = 0;
		foreach($events as $e){
			$length = $e['domainLookupEnd'] + $e['connectEnd'] + $e['responseStart'] + $e['responseEnd'] + $e['domReady'] + $e['loaded'];
			$longest = $length > $longest ? $length : $longest;
		}
		*/
		$jsonData['events'] = $events;
		$jsonData['alsoGet'] = $alsoGet; //send it back so we don't load data if panel has changed
		//$jsonData['longestLine'] = $longest;
		return $jsonData;
	}
	public static function ajax_activityLogUpdate_callback(){
		$issues = new wfIssues();
		return array(
			'ok' => 1,
			'items' => self::getLog()->getStatusEvents($_POST['lastctime']),
			'currentScanID' => $issues->getScanTime()
			);
	}
	public static function ajax_updateAlertEmail_callback(){
		$email = trim($_POST['email']);
		if(! preg_match('/[^\@]+\@[^\.]+\.[^\.]+/', $email)){
			return array( 'err' => "Invalid email address given.");
		}
		wfConfig::set('alertEmails', $email);
		return array('ok' => 1, 'email' => $email);
	}
	public static function ajax_bulkOperation_callback(){
		$op = sanitize_text_field($_POST['op']);
		if($op == 'del' || $op == 'repair'){
			$ids = $_POST['ids'];
			$filesWorkedOn = 0;
			$errors = array();
			$issues = new wfIssues();
			foreach($ids as $id){
				$id = intval($id); //Make sure input is a number.
				$issue = $issues->getIssueByID($id);
				if(! $issue){
					$errors[] = "Could not delete one of the files because we could not find the issue. Perhaps it's been resolved?";
					continue;
				}
				$file = $issue['data']['file'];
				$localFile = ABSPATH . '/' . preg_replace('/^[\.\/]+/', '', $file);
				$localFile = realpath($localFile);
				if(strpos($localFile, ABSPATH) !== 0){
					$errors[] = "An invalid file was requested: " . wp_kses($file, array());
					continue;
				}
				if($op == 'del'){
					if(@unlink($localFile)){
						$issues->updateIssue($id, 'delete');
						$filesWorkedOn++;
					} else {
						$err = error_get_last();
						$errors[] = "Could not delete file " . wp_kses($file, array()) . ". Error was: " . wp_kses($err['message'], array());
					}
				} else if($op == 'repair'){
					$dat = $issue['data'];
					$result = self::getWPFileContent($dat['file'], $dat['cType'], $dat['cName'], $dat['cVersion']);
					if($result['cerrorMsg']){
						$errors[] = $result['cerrorMsg'];
						continue;
					} else if(! $result['fileContent']){
						$errors[] = "We could not get the original file of " . wp_kses($file, array()) . " to do a repair.";
						continue;
					}

					if(preg_match('/\.\./', $file)){
						$errors[] = "An invalid file " . wp_kses($file, array()) . " was specified for repair.";
						continue;
					}
					$fh = fopen($localFile, 'w');
					if(! $fh){
						$err = error_get_last();
						if(preg_match('/Permission denied/i', $err['message'])){
							$errMsg = "You don't have permission to repair " . wp_kses($file, array()) . ". You need to either fix the file manually using FTP or change the file permissions and ownership so that your web server has write access to repair the file.";
						} else {
							$errMsg = "We could not write to " . wp_kses($file, array()) . ". The error was: " . $err['message'];
						}
						$errors[] = $errMsg;
						continue;
					}
					flock($fh, LOCK_EX);
					$bytes = fwrite($fh, $result['fileContent']);
					flock($fh, LOCK_UN);
					fclose($fh);
					if($bytes < 1){
						$errors[] = "We could not write to " . wp_kses($file, array()) . ". ($bytes bytes written) You may not have permission to modify files on your WordPress server.";
						continue;
					}
					$filesWorkedOn++;
					$issues->updateIssue($id, 'delete');
				}
			}
			$verb = $op == 'del' ? 'Deleted' : 'Repaired';
			$verb2 = $op == 'del' ? 'delete' : 'repair';
			if($filesWorkedOn > 0 && sizeof($errors) > 0){
				$headMsg = "$verb some files with errors";
				$bodyMsg = "$verb $filesWorkedOn files but we encountered the following errors with other files: " . implode('<br />', $errors);
			} else if($filesWorkedOn > 0){
				$headMsg = "$verb $filesWorkedOn files successfully";
				$bodyMsg = "$verb $filesWorkedOn files successfully. No errors were encountered.";
			} else if(sizeof($errors) > 0){
				$headMsg = "Could not $verb2 files";
				$bodyMsg = "We could not $verb2 any of the files you selected. We encountered the following errors: " . implode('<br />', $errors);
			} else {
				$headMsg = "Nothing done";
				$bodyMsg = "We didn't $verb2 anything and no errors were found.";
			}

			return array('ok' => 1, 'bulkHeading' => $headMsg, 'bulkBody' => $bodyMsg);
		} else {
			return array('errorMsg' => "Invalid bulk operation selected");
		}
	}
	public static function ajax_deleteFile_callback(){
		$issueID = intval($_POST['issueID']);
		$wfIssues = new wfIssues();
		$issue = $wfIssues->getIssueByID($issueID);
		if(! $issue){
			return array('errorMsg' => "Could not delete file because we could not find that issue.");
		}
		if(! $issue['data']['file']){
			return array('errorMsg' => "Could not delete file because that issue does not appear to be a file related issue.");
		}
		$file = $issue['data']['file'];
		$localFile = ABSPATH . '/' . preg_replace('/^[\.\/]+/', '', $file);
		$localFile = realpath($localFile);
		if(strpos($localFile, ABSPATH) !== 0){
			return array('errorMsg' => "An invalid file was requested for deletion.");
		}
		if ($localFile === ABSPATH . 'wp-config.php' && file_exists(ABSPATH . 'wp-config.php') && empty($_POST['forceDelete'])) {
			return array(
				'errorMsg' => "You must first download a backup copy of your <code>wp-config.php</code> prior to deleting the infected file.
				The <code>wp-config.php</code> file contains your database credentials which you will need to restore normal site operations.
				Your site will <b>NOT</b> function once the <code>wp-config.php</code> has been deleted.
				<p>
					<a class='button' href='/?_wfsf=download&nonce=" . wp_create_nonce('wp-ajax') . "&file=". rawurlencode($file) ."' target='_blank' onclick=\"jQuery('#wp-config-force-delete').show();\">Download a backup copy</a>
					<a style='display:none' id='wp-config-force-delete' class='button' href='#' target='_blank' onclick='WFAD.deleteFile($issueID, true); return false;'>Delete wp-config.php</a>
				</p>",
			);
		}
		if(@unlink($localFile)){
			$wfIssues->updateIssue($issueID, 'delete');
			return array(
				'ok' => 1,
				'localFile' => $localFile,
				'file' => $file
				);
		} else {
			$err = error_get_last();
			return array('errorMsg' => "Could not delete file " . wp_kses($file, array()) . ". The error was: " . wp_kses($err['message'], array()));
		}
	}
	public static function ajax_deleteDatabaseOption_callback(){
		/** @var wpdb $wpdb */
		global $wpdb;
		$issueID = intval($_POST['issueID']);
		$wfIssues = new wfIssues();
		$issue = $wfIssues->getIssueByID($issueID);
		if (!$issue) {
			return array('errorMsg' => "Could not remove the option because we could not find that issue.");
		}
		if (empty($issue['data']['option_name'])) {
			return array('errorMsg' => "Could not remove the option because that issue does not appear to be a database related issue.");
		}
		$prefix = $wpdb->get_blog_prefix($issue['data']['site_id']);
		if ($wpdb->query($wpdb->prepare("DELETE FROM {$prefix}options WHERE option_name = %s", $issue['data']['option_name']))) {
			$wfIssues->updateIssue($issueID, 'delete');
			return array(
				'ok'          => 1,
				'option_name' => $issue['data']['option_name'],
			);
		} else {
			return array('errorMsg' => "Could not remove the option " . esc_html($issue['data']['option_name']) . ". The error was: " . esc_html($wpdb->last_error));
		}
	}
	public static function ajax_restoreFile_callback(){
		$issueID = intval($_POST['issueID']);
		$wfIssues = new wfIssues();
		$issue = $wfIssues->getIssueByID($issueID);
		if(! $issue){
			return array('cerrorMsg' => "We could not find that issue in our database.");
		}
		$dat = $issue['data'];
		$result = self::getWPFileContent($dat['file'], $dat['cType'], (isset($dat['cName']) ? $dat['cName'] : ''), (isset($dat['cVersion']) ? $dat['cVersion'] : ''));
		$file = $dat['file'];
		if(isset($result['cerrorMsg']) && $result['cerrorMsg']){
			return $result;
		} else if(! $result['fileContent']){
			return array('cerrorMsg' => "We could not get the original file to do a repair.");
		}

		if(preg_match('/\.\./', $file)){
			return array('cerrorMsg' => "An invalid file was specified for repair.");
		}
		$localFile = ABSPATH . '/' . preg_replace('/^[\.\/]+/', '', $file);
		$fh = fopen($localFile, 'w');
		if(! $fh){
			$err = error_get_last();
			if(preg_match('/Permission denied/i', $err['message'])){
				$errMsg = "You don't have permission to repair that file. You need to either fix the file manually using FTP or change the file permissions and ownership so that your web server has write access to repair the file.";
			} else {
				$errMsg = "We could not write to that file. The error was: " . $err['message'];
			}
			return array('cerrorMsg' => $errMsg);
		}
		flock($fh, LOCK_EX);
		$bytes = fwrite($fh, $result['fileContent']);
		flock($fh, LOCK_UN);
		fclose($fh);
		if($bytes < 1){
			return array('cerrorMsg' => "We could not write to that file. ($bytes bytes written) You may not have permission to modify files on your WordPress server.");
		}
		$wfIssues->updateIssue($issueID, 'delete');
		return array(
			'ok' => 1,
			'file' => $localFile
			);
	}
	public static function ajax_scan_callback(){
		self::status(4, 'info', "Ajax request received to start scan.");
		$err = wfScanEngine::startScan();
		if($err){
			return array('errorMsg' => wp_kses($err, array()));
		} else {
			return array("ok" => 1);
		}
	}
	public static function ajax_exportSettings_callback(){
		/** @var wpdb $wpdb */
		global $wpdb;

		$keys = wfConfig::getExportableOptionsKeys();
		$export = array();
		foreach($keys as $key){
			$export[$key] = wfConfig::get($key, '');
		}
		$export['scanScheduleJSON'] = json_encode(wfConfig::get_ser('scanSched', array()));
		$export['schedMode'] = wfConfig::get('schedMode', '');

		// Any user supplied blocked IPs.
		$export['_blockedIPs'] = $wpdb->get_results('SELECT *, HEX(IP) as IP FROM ' . $wpdb->base_prefix . 'wfBlocks WHERE wfsn = 0 AND permanent = 1');

		// Any advanced blocking stuff too.
		$export['_advancedBlocking'] = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'wfBlocksAdv');

		try {
			$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
			$res = $api->call('export_options', array(), $export);
			if($res['ok'] && $res['token']){
				return array(
					'ok' => 1,
					'token' => $res['token'],
					);
			} else {
				throw new Exception("Invalid response: " . var_export($res, true));
			}
		} catch(Exception $e){
			return array('err' => "An error occurred: " . $e->getMessage());
		}
	}
	public static function importSettings($token){
		/** @var wpdb $wpdb */
		global $wpdb;

		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		$res = $api->call('import_options', array(), array('token' => $token));
		$totalSet = 0;
		if($res['ok'] && $res['options']){
			$keys = wfConfig::getExportableOptionsKeys();
			foreach($keys as $key){
				if(isset($res['options'][$key])){
					wfConfig::set($key, $res['options'][$key]);
					$totalSet++;
				}
			}
			if(isset($res['options']['scanScheduleJSON']) && isset($res['options']['schedMode'])){
				$scanSched = json_decode($res['options']['scanScheduleJSON']);
				wfConfig::set_ser('scanSched', $scanSched);
				wfConfig::set('schedMode', $res['options']['schedMode']);
				$totalSet += 2;
			}

			if (!empty($res['options']['_blockedIPs']) && is_array($res['options']['_blockedIPs'])) {
				foreach ($res['options']['_blockedIPs'] as $row) {
					if (!empty($row['IP'])) {
						$row['IP'] = pack('H*', $row['IP']);
						if (!$wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM ' . $wpdb->base_prefix . 'wfBlocks WHERE IP = %s', $row['IP']))) {
							$wpdb->insert($wpdb->base_prefix . 'wfBlocks', $row);
						}
					}
				}
			}

			if (!empty($res['options']['_advancedBlocking']) && is_array($res['options']['_advancedBlocking'])) {
				foreach ($res['options']['_advancedBlocking'] as $row) {
					if (!empty($row['blockString']) && !$wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM ' . $wpdb->base_prefix . 'wfBlocksAdv WHERE blockString = %s', $row['blockString']))) {
						unset($row['id']);
						$wpdb->insert($wpdb->base_prefix . 'wfBlocksAdv', $row);
					}
				}
			}

			return $totalSet;
		} else if($res['err']){
			throw new Exception($res['err']);
		} else {
			throw new Exception("Invalid response from Wordfence servers during import.");
		}
	}
	public static function ajax_startPasswdAudit_callback(){
		if(! wfAPI::SSLEnabled()){
			return array('errorMsg' => "Your server does not support SSL via cURL. To use this feature you need to make sure you have the PHP cURL library installed and enabled and have openSSL enabled so that you can communicate securely with our servers. This ensures that your password hashes remain secure and are double-encrypted when this feature is used. To fix this, please contact your hosting provider or site admin and ask him or her to install and enable cURL and openssl.");
		}
		if(! function_exists('openssl_public_encrypt')){
			return array('errorMsg' => "Your server does not have openssl installed. Specifically we require the openssl_public_encrypt() function to use this feature. Please ask your site admin or hosting provider to install 'openssl' and the openssl PHP libraries. We use these for public key encryption to securely send your password hashes to our server for auditing.");
		}
		global $wpdb; $p = $wpdb->base_prefix;
		$email = $_POST['emailAddr'];
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			return array(
				'errorMsg' => "Please enter a valid email address.",
				);
		}
		$auditType = $_POST['auditType'];
		$symKey = wfCrypt::makeSymHexKey(32); #hex digits which is 128 bits
		$wfdb = new wfDB();
		$q1 = $wfdb->querySelect("select ID, AES_ENCRYPT(user_pass, '%s') as crypt_pass from $p"."users", $symKey);
		$admins = "";
		$users = "";
		foreach($q1 as $rec) {
			$isAdmin = user_can($rec['ID'], 'manage_options');
			if($isAdmin && ($auditType == 'admin' || $auditType == 'both') ) {
				$admins .= $rec['ID'] . ':' . base64_encode($rec['crypt_pass']) . '|';
			} else if($auditType == 'user' || $auditType == 'both') {
				$users .= $rec['ID'] . ':' . base64_encode($rec['crypt_pass']) . '|';
			}

		}
		$admins = rtrim($admins,'|');
		$users = rtrim($users,'|');
		//error_log($admins);
		//error_log($users);

		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		try {
			$res = $api->call('password_audit', array(), array(
				'auditType' => $auditType,
				'email' => $email,
				'pubCryptSymKey' => wfCrypt::pubCrypt($symKey),
				'users' => $users,
				'admins' => $admins
				), true); //Force SSL
			if(is_array($res)){
				if(isset($res['ok']) && $res['ok'] == '1'){
					return array(
						'ok' => 1
					);
				} else if(isset($res['notPaid']) && $res['notPaid'] == '1'){
					return array(
						'errorMsg' => "You are not using a Premium version of Wordfence. This feature is available to Premium Wordfence members only.",
						);
				} else if(isset($res['tooManyJobs']) && $res['tooManyJobs'] == '1'){
					return array(
						'errorMsg' => "You already have a password audit running. Please wait until it finishes before starting another.",
						);
				} else {
					throw new Exception("An unrecognized response was received from the Wordfence servers.");
				}
			} else {
				return array(
					'errorMsg' => "We received an invalid response when trying to submit your password audit.",
					);
			}
		} catch(Exception $e){
			return array(
				'errMsg' => "We could not submit your password audit: " . $e,
				);
		}
	}
	public static function ajax_weakPasswordsFix_callback(){
		$mode = $_POST['mode'];
		$ids = explode(',', $_POST['ids']);
		$homeURL = home_url();
		$count = 0;
		if($mode == 'fix'){
			foreach($ids as $userID){
				$user = get_user_by('id', $userID);
				if($user){
					$passwd = wp_generate_password();
					$count++;
					wp_set_password($passwd, $userID);
					wp_mail($user->user_email, "Your Password on $homeURL Has Been Changed.", wfUtils::tmpl('email_passwdChanged.php', array(
						'siteURL' => site_url(),
						'homeURL' => home_url(),
						'loginURL' => wp_login_url(),
						'username' => $user->user_login,
						'passwd' => $passwd,
						)));
				}
			}
			return array(
				'ok' => 1,
				'title' => "Fixed $count Weak Passwords",
				'msg' => "We created new passwords for $count site members and emailed them the new password with instructions."
				);

		} else if($mode == 'email'){
			foreach($ids as $userID){
				$user = get_user_by('id', $userID);
				if($user){
					$count++;
					wp_mail($user->user_email, "Please Change Your Password on $homeURL", wfUtils::tmpl('email_pleaseChangePasswd.php', array(
						'siteURL' => site_url(),
						'homeURL' => home_url(),
						'username' => $user->user_login,
						'loginURL' => wp_login_url()
						)));
				}
			}
			return array(
				'ok' => 1,
				'title' => "Notified $count Users",
				'msg' => "We sent an email to $count site members letting them know that they have a weak password and suggesting that they sign in and change their password to a stronger one."
				);
		}
	}
	public static function ajax_passwdLoadResults_callback(){
		if(! wfAPI::SSLEnabled()){ return array('ok' => 1); } //If user hits start passwd audit they will get a helpful message. We don't want an error popping up for every ajax call if SSL is not supported.
		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		try {
			$res = $api->call('password_load_results', array(), array(), true);
		} catch(Exception $e){
			return array('errorMsg' => "Could not load password audit results: " . $e);
		}
		$finalResults = array();
		if(is_array($res) && $res['ok']){
			if(is_array($res['results'])){
				for($i = 0; $i < sizeof($res['results']); $i++){
					//$meta = get_user_meta($res['results'][$i]['userID'], 'wp_capabilities', true);
					//$res['results'][$i]['isAdmin'] = (isset($meta['administrator']) && $meta['administrator']) ? '1' : '';
					$user = new WP_User($res['results'][$i]['wpUserID']);
					if(is_object($user)){
						$passMD5 = strtoupper(md5($user->user_pass));
						if($passMD5 != $res['results'][$i]['hashMD5']){ //Password has changed, so exclude this result
							continue;
						}
						$item =  $res['results'][$i];
						$item['username'] = $user->user_login;
						$item['email'] = $user->user_email;
						$item['firstName'] = $user->first_name;
						$item['lastName'] = $user->last_name;
						$item['starredPassword'] = $res['results'][$i]['pwFirstLetter'] . str_repeat('*', $res['results'][$i]['pwLength'] - 1);
						//crackTime and crackDifficulty are fields too.
						$finalResults[] = $item;
					}
				}
			}

			return array(
				'ok' => 1,
				'results' => $finalResults,
				);
		} else {
			return array('ok' => 1); //fail silently
		}
	}	
	public static function ajax_passwdLoadJobs_callback(){
		if(! wfAPI::SSLEnabled()){ return array('ok' => 1); } //If user hits start passwd audit they will get a helpful message. We don't want an error popping up for every ajax call if SSL is not supported.
		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		try {
			$res = $api->call('password_load_jobs', array(), array(), true);
		} catch(Exception $e){
			return array('errorMsg' => "Could not load password audit jobs: " . $e); 
		}
		if(is_array($res) && $res['ok']){
			return array(
				'ok' => 1,
				'results' => $res['results'],
				);
		} else {
			return array('ok' => 1); //fail silently
		}
	}
	public static function ajax_killPasswdAudit_callback(){
		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		try {
			$res = $api->call('password_kill_job', array(), array( 'jobID' => $_POST['jobID'] ), true);
		} catch(Exception $e){
			return array('errorMsg' => "Could not stop requested audit: " . $e);
		}
		if(is_array($res) && $res['ok']){
			return array(
				'ok' => 1,
				);
		} else {
			return array('errorMsg' => "We could not stop the requested password audit."); //fail silently
		}
	}
	public static function ajax_deletePasswdAudit_callback(){
		$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		try {
			$res = $api->call('password_delete_job', array(), array( 'jobID' => $_POST['jobID']));
		} catch(Exception $e){
			return array('errorMsg' => "Could not delete the job you specified: " . $e);
		}
		return array('ok' => 1);
	}
	public static function ajax_importSettings_callback(){
		$token = $_POST['token'];
		try {
			$totalSet = self::importSettings($token);
			return array(
				'ok' => 1,
				'totalSet' => $totalSet,
				);
		} catch(Exception $e){
			return array('err' => "An error occurred: " . $e->getMessage());
		}
	}
	public static function startScan(){
		wfScanEngine::startScan();
	}
	public static function templateRedir(){
		if (!empty($_GET['wordfence_logHuman'])) {
			self::ajax_logHuman_callback();
			exit;
		}

		$wfFunc = !empty($_GET['_wfsf']) && is_string($_GET['_wfsf']) ? $_GET['_wfsf'] : '';

		//Logging
		self::doEarlyAccessLogging();
		//End logging


		if(! ($wfFunc == 'diff' || $wfFunc == 'view' || $wfFunc == 'viewOption' || $wfFunc == 'sysinfo' || $wfFunc == 'cronview' || $wfFunc == 'dbview' || $wfFunc == 'conntest' || $wfFunc == 'unknownFiles' || $wfFunc == 'IPTraf' || $wfFunc == 'viewActivityLog' || $wfFunc == 'testmem' || $wfFunc == 'testtime' || $wfFunc == 'download')){
			return;
		}
		if(! wfUtils::isAdmin()){
			return;
		}

		$nonce = $_GET['nonce'];
		if(! wp_verify_nonce($nonce, 'wp-ajax')){
			echo "Bad security token. It may have been more than 12 hours since you reloaded the page you came from. Try reloading the page you came from. If that doesn't work, please sign out and sign-in again.";
			exit(0);
		}
		if($wfFunc == 'diff'){
			self::wfFunc_diff();
		} else if($wfFunc == 'view'){
			self::wfFunc_view();
		} else if($wfFunc == 'viewOption'){
			self::wfFunc_viewOption();
		} else if($wfFunc == 'sysinfo') {
			require( 'sysinfo.php' );
		} else if($wfFunc == 'dbview'){
				require('dbview.php');
		} else if($wfFunc == 'cronview') {
			require('cronview.php');
		} else if($wfFunc == 'conntest'){
			require('conntest.php');
		} else if($wfFunc == 'unknownFiles'){
			require('unknownFiles.php');
		} else if($wfFunc == 'IPTraf'){
			self::wfFunc_IPTraf();
		} else if($wfFunc == 'viewActivityLog'){
			self::wfFunc_viewActivityLog();
		} else if($wfFunc == 'testmem'){
			self::wfFunc_testmem();
		} else if($wfFunc == 'testtime'){
			self::wfFunc_testtime();
		} else if($wfFunc == 'download'){
			self::wfFunc_download();
		}
		exit(0);
	}
	public static function memtest_error_handler($errno, $errstr, $errfile, $errline){
		echo "Error received: $errstr\n";
	}
	private static function wfFunc_testtime(){
		header('Content-Type: text/plain');
		@error_reporting(E_ALL);
		wfUtils::iniSet('display_errors','On');
		set_error_handler('wordfence::memtest_error_handler', E_ALL);

		echo "Wordfence process duration benchmarking utility version " . WORDFENCE_VERSION . ".\n";
		echo "This utility tests how long your WordPress host allows a process to run.\n\n--Starting test--\n";
		echo "Starting timed test. This will take at least three minutes. Seconds elapsed are printed below.\nAn error after this line is not unusual. Read it and the elapsed seconds to determine max process running time on your host.\n";
		for($i = 1; $i <= 180; $i++){
			echo "\n$i:";
			for($j = 0; $j < 1000; $j++){
				echo '.';
			}
			flush();
			sleep(1);
		}
		echo "\n--Test complete.--\n\nCongratulations, your web host allows your PHP processes to run at least 3 minutes.\n";
		exit();
	}
	private static function wfFunc_testmem(){
		header('Content-Type: text/plain');
		@error_reporting(E_ALL);
		wfUtils::iniSet('display_errors','On');
		set_error_handler('wordfence::memtest_error_handler', E_ALL);

		echo "Wordfence Memory benchmarking utility version " . WORDFENCE_VERSION . ".\n";
		echo "This utility tests if your WordPress host respects the maximum memory configured\nin their php.ini file, or if they are using other methods to limit your access to memory.\n\n--Starting test--\n";
		echo "Current maximum memory configured in php.ini: " . ini_get('memory_limit') . "\n";
		echo "Current memory usage: " . sprintf('%.2f', memory_get_usage(true) / (1024 * 1024)) . "M\n";
		echo "Setting max memory to 90M.\n";
		wfUtils::iniSet('memory_limit', '90M');
		echo "Starting memory benchmark. Seeing an error after this line is not unusual. Read the error carefully\nto determine how much memory your host allows. We have requested 90 megabytes.\n";
		if(memory_get_usage(true) < 1){
			echo "Exiting test because memory_get_usage() returned a negative number\n";
		}
		if(memory_get_usage(true) > (1024 * 1024 * 1024)){
			echo "Exiting because current memory usage is greater than a gigabyte.\n";
		}
		$arr = array();
		//256 bytes
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ012345678900000000000000000000000000000000000000000000000000000000000000000000000000000000000000011111111111111111222222222222222222233333333333333334444444444444444444444444555555555555666666666666666666";
		$finalUsage = '0';
		while(true){
			if(memory_get_usage(true) > 80 * 1024 * 1024){
				$finalUsage = sprintf('%.2f', (memory_get_usage(true) / 1024 / 1024));
				echo "Completing test after benchmarking up to " . $finalUsage . " megabytes.\n";
				break;
			}
			for($i = 0; $i < 1024; $i++){ //Roughly 1 megabyte if it's 256K and actual array size is 4x data size
				$arr[] = $chars;
			}
		}
		echo "--Test complete.--\n\nCongratulations, your web host allows you to use at least $finalUsage megabytes of memory for each PHP process hosting your WordPress site.\n";
		exit();
	}
	public static function wfLogPerfHeader(){
		$ajaxURL = admin_url('admin-ajax.php');
		$ajaxURL = preg_replace('/^https?:/i', '', $ajaxURL);
		$scriptURL = wfUtils::getBaseURL() . '/js/perf.js?v=' . WORDFENCE_VERSION;
		$scriptURL = preg_replace('/^https?:/i', '', $scriptURL);
		#Load as external script async so we don't slow page down.
		echo <<<EOL
<script type="text/javascript">
window['wordfenceAjaxURL'] = "$ajaxURL";
(function(url){
if(/(?:Chrome\/26\.0\.1410\.63 Safari\/537\.31|WordfenceTestMonBot)/.test(navigator.userAgent)){ return; }
var wfscr = document.createElement('script');
wfscr.type = 'text/javascript';
wfscr.async = true;
wfscr.src = url;
(document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(wfscr);
})('$scriptURL');
</script>
EOL;
	}
	public static function wfLogHumanHeader(){
		$URL = home_url('/?wordfence_logHuman=1&hid=' . wfUtils::encrypt(self::$hitID));
		$URL = addslashes(preg_replace('/^https?:/i', '', $URL));
		#Load as external script async so we don't slow page down.
		echo <<<HTML
<script type="text/javascript">
(function(url){
	if(/(?:Chrome\/26\.0\.1410\.63 Safari\/537\.31|WordfenceTestMonBot)/.test(navigator.userAgent)){ return; }
	var addEvent = function(evt, handler) {
		if (window.addEventListener) {
			document.addEventListener(evt, handler, false);
		} else if (window.attachEvent) {
			document.attachEvent('on' + evt, handler);
		}
	};
	var removeEvent = function(evt, handler) {
		if (window.removeEventListener) {
			document.removeEventListener(evt, handler, false);
		} else if (window.detachEvent) {
			document.detachEvent('on' + evt, handler);
		}
	};
	var evts = 'contextmenu dblclick drag dragend dragenter dragleave dragover dragstart drop keydown keypress keyup mousedown mousemove mouseout mouseover mouseup mousewheel scroll'.split(' ');
	var logHuman = function() {
		var wfscr = document.createElement('script');
		wfscr.type = 'text/javascript';
		wfscr.async = true;
		wfscr.src = url + '&r=' + Math.random();
		(document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(wfscr);
		for (var i = 0; i < evts.length; i++) {
			removeEvent(evts[i], logHuman);
		}
	};
	for (var i = 0; i < evts.length; i++) {
		addEvent(evts[i], logHuman);
	}
})('$URL');
</script>
HTML;
	}
	public static function shutdownAction(){
	}
	public static function wfFunc_viewActivityLog(){
		require('viewFullActivityLog.php');
		exit(0);
	}
	public static function wfFunc_IPTraf(){
		$IP = $_GET['IP'];
		if(!wfUtils::isValidIP($IP)){
			echo "An invalid IP address was specified.";
			exit(0);
		}
		$reverseLookup = wfUtils::reverseLookup($IP);
		$wfLog = new wfLog(wfConfig::get('apiKey'), wfUtils::getWPVersion());
		$results = array_merge(
			$wfLog->getHits('hits', 'hit', 0, 10000, $IP),
			$wfLog->getHits('hits', '404', 0, 10000, $IP)
			);
		usort($results, 'wordfence::iptrafsort');
		for($i = 0; $i < sizeof($results); $i++){
			if(array_key_exists($i + 1, $results)){
				$results[$i]['timeSinceLastHit'] = sprintf('%.4f', $results[$i]['ctime'] - $results[$i + 1]['ctime']);
			} else {
				$results[$i]['timeSinceLastHit'] = '';
			}
		}
		require('IPTraf.php');
		exit(0);
	}
	public static function iptrafsort($b, $a){
		if($a['ctime'] == $b['ctime']){ return 0; }
		return ($a['ctime'] < $b['ctime']) ? -1 : 1;
	}

	public static function wfFunc_viewOption() {
		/** @var wpdb $wpdb */
		global $wpdb;
		$site_id = !empty($_GET['site_id']) ? absint($_GET['site_id']) : get_current_blog_id();
		$option_name = !empty($_GET['option']) ? $_GET['option'] : false;

		$prefix = $wpdb->get_blog_prefix($site_id);

		$option_value = $wpdb->get_var($wpdb->prepare("SELECT option_value FROM {$prefix}options WHERE option_name = %s", $option_name));

		header('Content-type: text/plain');
		exit($option_value);
	}

	public static function wfFunc_view(){
		$localFile = ABSPATH . preg_replace('/^(?:\.\.|[\/]+)/', '', sanitize_text_field($_GET['file']));
		if(strpos($localFile, '..') !== false){
			echo "Invalid file requested. (Relative paths not allowed)";
			exit();
		}
		if(preg_match('/[\'\"<>\!\{\}\(\)\&\@\%\$\*\+\[\]\?]+/', $localFile)){
			echo "File contains illegal characters.";
			exit();
		}
		$cont = @file_get_contents($localFile);
		$isEmpty = false;
		if(! $cont){
			if(file_exists($localFile) && filesize($localFile) === 0){ //There's a remote possibility that very large files on 32 bit systems will return 0 here, but it's about 1 in 2 billion
				$isEmpty = true;
			} else {
				$err = error_get_last();
				echo "We could not open the requested file for reading. The error was: " . $err['message'];
				exit(0);
			}
		}
		$fileMTime = @filemtime($localFile);
		$fileMTime = date('l jS \of F Y h:i:s A', $fileMTime);
		try {
			if(wfUtils::fileOver2Gigs($localFile)){
				$fileSize = "Greater than 2 Gigs";
			} else {
				$fileSize = @filesize($localFile); //Checked if over 2 gigs above
				$fileSize = number_format($fileSize, 0, '', ',') . ' bytes';
			}
		} catch(Exception $e){ $fileSize = 'Unknown file size.'; }

		require 'wfViewResult.php';
		exit(0);
	}
	public static function wfFunc_diff(){
		if(preg_match('/[\'\"<>\!\{\}\(\)\&\@\%\$\*\+\[\]\?]+/', $_GET['file'])){
			echo "File contains illegal characters.";
			exit();
		}

		$result = self::getWPFileContent($_GET['file'], $_GET['cType'], $_GET['cName'], $_GET['cVersion']);
		if( isset( $result['errorMsg'] ) && $result['errorMsg']){
			echo wp_kses($result['errorMsg'], array());
			exit(0);
		} else if(! $result['fileContent']){
			echo "We could not get the contents of the original file to do a comparison.";
			exit(0);
		}

		$localFile = realpath(ABSPATH . '/' . preg_replace('/^[\.\/]+/', '', $_GET['file']));
		$localContents = file_get_contents($localFile);
		if($localContents == $result['fileContent']){
			$diffResult = '';
		} else {
			$diff = new Diff(
				//Treat DOS and Unix files the same
				preg_split("/(?:\r\n|\n)/", $result['fileContent']),
				preg_split("/(?:\r\n|\n)/", $localContents),
				array()
				);
			$renderer = new Diff_Renderer_Html_SideBySide;
			$diffResult = $diff->Render($renderer);
		}
		require 'diffResult.php';
		exit(0);
	}

	public static function wfFunc_download() {
		$localFile = ABSPATH . preg_replace('/^(?:\.\.|[\/]+)/', '', sanitize_text_field($_GET['file']));
		if (strpos($localFile, '..') !== false) {
			echo "Invalid file requested. (Relative paths not allowed)";
			exit();
		}
		if (preg_match('/[\'\"<>\!\{\}\(\)\&\@\%\$\*\+\[\]\?]+/', $localFile)) {
			echo "File contains illegal characters.";
			exit();
		}
		if (!file_exists($localFile)) {
			exit('File does not exist.');
		}

		$filename = basename($localFile);
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . filesize($localFile));
		readfile($localFile);
		exit;
	}

	public static function initAction(){
		if(wfConfig::liveTrafficEnabled() && (! wfConfig::get('disableCookies', false)) ){
			self::setCookie();
		}
	}
	private static function setCookie(){
		$cookieName = 'wfvt_' . crc32(site_url());
		$c = isset($_COOKIE[$cookieName]) ? isset($_COOKIE[$cookieName]) : false;
		if($c){
			self::$newVisit = false;
		} else {
			self::$newVisit = true;
		}
		wfUtils::setcookie($cookieName, uniqid(), time() + 1800, '/', null, null, true);
	}
	public static function admin_init(){
		if(! wfUtils::isAdmin()){ return; }
		foreach(array(
			'activate', 'scan', 'updateAlertEmail', 'sendActivityLog', 'restoreFile', 'startPasswdAudit',
			'deletePasswdAudit', 'weakPasswordsFix', 'passwdLoadResults', 'killPasswdAudit', 'passwdLoadJobs',
			'exportSettings', 'importSettings', 'bulkOperation', 'deleteFile', 'deleteDatabaseOption', 'removeExclusion',
			'activityLogUpdate', 'ticker', 'loadIssues', 'updateIssueStatus', 'deleteIssue', 'updateAllIssues',
			'reverseLookup', 'unlockOutIP', 'loadBlockRanges', 'unblockRange', 'blockIPUARange', 'whois', 'unblockIP',
			'blockIP', 'permBlockIP', 'loadStaticPanel', 'saveConfig', 'downloadHtaccess', 'checkFalconHtaccess',
			'updateConfig', 'saveCacheConfig', 'removeFromCache', 'autoUpdateChoice', 'saveCacheOptions', 'clearPageCache',
			'getCacheStats', 'clearAllBlocked', 'killScan', 'saveCountryBlocking', 'saveScanSchedule', 'tourClosed',
			'welcomeClosed', 'startTourAgain', 'downgradeLicense', 'addTwoFactor', 'twoFacActivate', 'twoFacDel',
			'loadTwoFactor', 'loadAvgSitePerf', 'sendTestEmail', 'addCacheExclusion', 'removeCacheExclusion',
			'loadCacheExclusions', 'email_summary_email_address_debug', 'unblockNetwork', 'permanentlyBlockAllIPs',
		) as $func){
			add_action('wp_ajax_wordfence_' . $func, 'wordfence::ajaxReceiver');
		}

		if(isset($_GET['page']) && preg_match('/^Wordfence/', @$_GET['page']) ){
			wp_enqueue_style('wp-pointer');
			wp_enqueue_script('wp-pointer');
			wp_enqueue_style('wordfence-main-style', wfUtils::getBaseURL() . 'css/main.css', '', WORDFENCE_VERSION);
			wp_enqueue_style('wordfence-colorbox-style', wfUtils::getBaseURL() . 'css/colorbox.css', '', WORDFENCE_VERSION);
			wp_enqueue_style('wordfence-dttable-style', wfUtils::getBaseURL() . 'css/dt_table.css', '', WORDFENCE_VERSION);


			wp_enqueue_script('json2');
			wp_enqueue_script('jquery.wftmpl', wfUtils::getBaseURL() . 'js/jquery.tmpl.min.js', array('jquery'), WORDFENCE_VERSION);
			wp_enqueue_script('jquery.wfcolorbox', wfUtils::getBaseURL() . 'js/jquery.colorbox-min.js', array('jquery'), WORDFENCE_VERSION);
			wp_enqueue_script('jquery.wfdataTables', wfUtils::getBaseURL() . 'js/jquery.dataTables.min.js', array('jquery'), WORDFENCE_VERSION);
			//wp_enqueue_script('jquery.tools', wfUtils::getBaseURL() . 'js/jquery.tools.min.js', array('jquery'));
			wp_enqueue_script('wordfenceAdminjs', wfUtils::getBaseURL() . 'js/admin.js', array('jquery'), WORDFENCE_VERSION);
			wp_enqueue_script('wordfenceAdminExtjs', wfUtils::getBaseURL() . 'js/tourTip.js', array('jquery'), WORDFENCE_VERSION);
			self::setupAdminVars();
		} else {
			wp_enqueue_style('wp-pointer');
			wp_enqueue_script('wp-pointer');
			wp_enqueue_script('wordfenceAdminjs', wfUtils::getBaseURL() . 'js/tourTip.js', array('jquery'), WORDFENCE_VERSION);
			self::setupAdminVars();
		}

	}
	private static function setupAdminVars(){
		$updateInt = wfConfig::get('actUpdateInterval', 2);
		if(! preg_match('/^\d+$/', $updateInt)){
			$updateInt = 2;
		}
		$updateInt *= 1000;

		wp_localize_script('wordfenceAdminjs', 'WordfenceAdminVars', array(
			'ajaxURL' => admin_url('admin-ajax.php'),
			'firstNonce' => wp_create_nonce('wp-ajax'),
			'siteBaseURL' => wfUtils::getSiteBaseURL(),
			'debugOn' => wfConfig::get('debugOn', 0),
			'actUpdateInterval' => $updateInt,
			'tourClosed' => wfConfig::get('tourClosed', 0),
			'welcomeClosed' => wfConfig::get('welcomeClosed', 0),
			'cacheType' => wfConfig::get('cacheType'),
			'liveTrafficEnabled' => wfConfig::liveTrafficEnabled()
			));
	}
	public static function activation_warning(){
		$activationError = get_option('wf_plugin_act_error', '');
		if(strlen($activationError) > 400){
			$activationError = substr($activationError, 0, 400) . '...[output truncated]';
		}
		if($activationError){
			echo '<div id="wordfenceConfigWarning" class="updated fade"><p><strong>Wordfence generated an error on activation. The output we received during activation was:</strong> ' . wp_kses($activationError, array()) . '</p></div>';
		}
		delete_option('wf_plugin_act_error');
	}
	public static function noKeyError(){
		echo '<div id="wordfenceConfigWarning" class="fade error"><p><strong>Wordfence could not get an API key from the Wordfence scanning servers when it activated.</strong> You can try to fix this by going to the Wordfence "options" page and hitting "Save Changes". This will cause Wordfence to retry fetching an API key for you. If you keep seeing this error it usually means your WordPress server can\'t connect to our scanning servers. You can try asking your WordPress host to allow your WordPress server to connect to noc1.wordfence.com.</p></div>';
	}
	public static function adminEmailWarning(){
		echo '<div id="wordfenceConfigWarning" class="fade error"><p><strong>You have not set an administrator email address to receive alerts for Wordfence.</strong> Please <a href="' . self::getMyOptionsURL() . '">click here to go to the Wordfence Options Page</a> and set an email address where you will receive security alerts from this site.</p></div>';
	}
	public static function autoUpdateNotice(){
		echo '<div id="wordfenceAutoUpdateChoice" class="fade error"><p><strong>Do you want Wordfence to stay up-to-date automatically?</strong>&nbsp;&nbsp;&nbsp;<a href="#" onclick="wordfenceExt.autoUpdateChoice(\'yes\'); return false;">Yes, enable auto-update.</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="wordfenceExt.autoUpdateChoice(\'no\'); return false;">No thanks.</a></p></div>';
	}
	public static function admin_menus(){
		if(! wfUtils::isAdmin()){ return; }
		$warningAdded = false;
		if(get_option('wf_plugin_act_error', false)){
			if(wfUtils::isAdminPageMU()){
				add_action('network_admin_notices', 'wordfence::activation_warning');
			} else {
				add_action('admin_notices', 'wordfence::activation_warning');
			}
			$warningAdded = true;
		}
		if(! wfConfig::get('apiKey')){
			if(wfUtils::isAdminPageMU()){
				add_action('network_admin_notices', 'wordfence::noKeyError');
			} else {
				add_action('admin_notices', 'wordfence::noKeyError');
			}
			$warningAdded = true;
		}
		if(! $warningAdded){
			if(wfConfig::get('tourClosed') == '1' && (! wfConfig::get('autoUpdate')) && (! wfConfig::get('autoUpdateChoice'))){
				$warningAdded = true;
				if(wfUtils::isAdminPageMU()){
					add_action('network_admin_notices', 'wordfence::autoUpdateNotice');
				} else {
					add_action('admin_notices', 'wordfence::autoUpdateNotice');
				}
			}
		}
		if(! $warningAdded){
			if(wfConfig::get('tourClosed') == '1' && (! wfConfig::get('alertEmails')) ){
				$warningAdded = true;
				if(wfUtils::isAdminPageMU()){
					add_action('network_admin_notices', 'wordfence::adminEmailWarning');
				} else {
					add_action('admin_notices', 'wordfence::adminEmailWarning');
				}
			}
		}

		add_submenu_page("Wordfence", "Scan", "Scan", "activate_plugins", "Wordfence", 'wordfence::menu_scan');
		add_menu_page('Wordfence', 'Wordfence', 'activate_plugins', 'Wordfence', 'wordfence::menu_scan', wfUtils::getBaseURL() . 'images/wordfence-logo-16x16.png');
		add_submenu_page("Wordfence", "Live Traffic", "Live Traffic", "activate_plugins", "WordfenceActivity", 'wordfence::menu_activity');
		/* add_submenu_page('Wordfence', 'Site Performance', 'Site Performance', 'activate_plugins', 'WordfenceSitePerfStats', 'wordfence::menu_sitePerfStats'); */
		add_submenu_page('Wordfence', 'Performance Setup', 'Performance Setup', 'activate_plugins', 'WordfenceSitePerf', 'wordfence::menu_sitePerf');
		add_submenu_page('Wordfence', 'Blocked IPs', 'Blocked IPs', 'activate_plugins', 'WordfenceBlockedIPs', 'wordfence::menu_blockedIPs');
		add_submenu_page('Wordfence', 'Password Audit', 'Password Audit', 'activate_plugins', 'WordfencePasswdAudit', 'wordfence::menu_passwd');

		add_submenu_page("Wordfence", "Cellphone Sign-in", "Cellphone Sign-in", "activate_plugins", "WordfenceTwoFactor", 'wordfence::menu_twoFactor');
		add_submenu_page("Wordfence", "Country Blocking", "Country Blocking", "activate_plugins", "WordfenceCountryBlocking", 'wordfence::menu_countryBlocking');
		add_submenu_page("Wordfence", "Scan Schedule", "Scan Schedule", "activate_plugins", "WordfenceScanSchedule", 'wordfence::menu_scanSchedule');
		add_submenu_page("Wordfence", "Whois Lookup", "Whois Lookup", "activate_plugins", "WordfenceWhois", 'wordfence::menu_whois');
		add_submenu_page("Wordfence", "Advanced Blocking", "Advanced Blocking", "activate_plugins", "WordfenceRangeBlocking", 'wordfence::menu_rangeBlocking');
		add_submenu_page("Wordfence", "Options", "Options", "activate_plugins", "WordfenceSecOpt", 'wordfence::menu_options');
	}
	public static function menu_options(){
		require 'menu_options.php';
	}
	public static function menu_sitePerf(){
		require 'menu_sitePerf.php';
	}
	public static function menu_sitePerfStats(){
		require 'menu_sitePerfStats.php';
	}
	public static function menu_blockedIPs(){
		require 'menu_blockedIPs.php';
	}
	public static function menu_passwd()
	{
		require 'menu_passwd.php';
	}
	public static function menu_scanSchedule(){
		require 'menu_scanSchedule.php';
	}
	public static function menu_twoFactor(){
		require 'menu_twoFactor.php';
	}
	public static function menu_countryBlocking(){
		require 'menu_countryBlocking.php';
	}
	public static function menu_whois(){
		require 'menu_whois.php';
	}
	public static function menu_rangeBlocking(){
		require 'menu_rangeBlocking.php';
	}
	public static function liveTrafficW3TCWarning(){
		echo self::cachingWarning("W3 Total Cache");
	}
	public static function liveTrafficSuperCacheWarning(){
		echo self::cachingWarning("WP Super Cache");
	}
	public static function cachingWarning($plugin){
		return '<div id="wordfenceConfigWarning" class="error fade"><p><strong>The Wordfence Live Traffic feature has been disabled because you have ' . $plugin . ' active which is not compatible with Wordfence Live Traffic.</strong> If you want to reenable Wordfence Live Traffic, you need to deactivate ' . $plugin . ' and then go to the Wordfence options page and reenable Live Traffic there. Wordfence does work with ' . $plugin . ', however Live Traffic will be disabled and the Wordfence firewall will also count less hits per visitor because of the ' . $plugin . ' caching function. All other functions should work correctly.</p></div>';
	}
	public static function menu_activity(){
		require 'menu_activity.php';
	}
	public static function menu_scan(){
		require 'menu_scan.php';
	}
	public static function status($level /* 1 has highest visibility */, $type /* info|error */, $msg){
		if($level > 3 && $level < 10 && (! self::isDebugOn())){ //level 10 and higher is for summary messages
			return false;
		}
		if($type != 'info' && $type != 'error'){ error_log("Invalid status type: $type"); return; }
		if(self::$printStatus){
			echo "STATUS: $level : $type : $msg\n";
		} else {
			self::getLog()->addStatus($level, $type, $msg);
		}
	}
	public static function profileUpdateAction($userID, $newDat = false){
		if(! $newDat){ return; }
		if(wfConfig::get('other_pwStrengthOnUpdate')){
			$oldDat = get_userdata($userID);
			if($newDat->user_pass != $oldDat->user_pass){
				$wf = new wfScanEngine();
				$wf->scanUserPassword($userID);
				$wf->emailNewIssues();
			}
		}
	}
	public static function replaceVersion($url)
	{
		global $wp_version;
		static $version = null;
		if ($version === null) {
			$version = wp_hash($wp_version . WORDFENCE_VERSION);
		}
		return preg_replace("/([&;\?]ver)=[0-9\.]+/", "$1={$version}", $url);
	}
	public static function genFilter($gen, $type){
		if(wfConfig::get('other_hideWPVersion')){
			return '';
		} else {
			return $gen;
		}
	}
	public static function pushCommentSpamIP($m){
		if(wfUtils::isValidIP($m[1]) && strpos($m[1], '127.0.0') !== 0 ){
			self::$commentSpamItems[] = trim($m[1]);
		}
	}
	public static function pushCommentSpamHost($m){
		self::$commentSpamItems[] = trim($m[1]);
	}
	public static function preCommentApprovedFilter($approved, $cData){
		if( $approved == 1 && (! is_user_logged_in()) && wfConfig::get('other_noAnonMemberComments') ){
			$user = get_user_by('email', trim($cData['comment_author_email']));
			if($user){
				wfConfig::inc('totalSpamStopped');
				return 0; //hold for moderation if the user is not signed in but used a members email
			}
		}

		if(($approved == 1 || $approved == 0) && wfConfig::get('other_scanComments')){
			$wf = new wfScanEngine();
			try {
				if($wf->isBadComment($cData['comment_author'], $cData['comment_author_email'], $cData['comment_author_url'],  $cData['comment_author_IP'], $cData['comment_content'])){
					wfConfig::inc('totalSpamStopped');
					return 'spam';
				}
			} catch(Exception $e){
				//This will most likely be an API exception because we can't contact the API, so we ignore it and let the normal comment mechanisms run.
			}
		}
		if(wfConfig::get('isPaid') && ($approved == 1 || $approved == 0) && wfConfig::get('advancedCommentScanning')){
			self::$commentSpamItems = array();
			preg_replace_callback('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', 'wordfence::pushCommentSpamIP', $cData['comment_content']);
			$IPs =  self::$commentSpamItems;
			self::$commentSpamItems = array();
			preg_replace_callback('/https?:\/\/([a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+[a-zA-Z0-9])/i', 'wordfence::pushCommentSpamHost', $cData['comment_content']);
			$hosts = self::$commentSpamItems;
			self::$commentSpamItems = array();
			try {
				$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
				$res = $api->call('advanced_comment_scan', array(), array(
					'author' => $cData['comment_author'],
					'email' =>  $cData['comment_author_email'],
					'URL' => $cData['comment_author_url'],
					'commentIP' => $cData['comment_author_IP'],
					'wfIP' => wfUtils::getIP(),
					'hosts' => (sizeof($hosts) > 0 ? implode(',', $hosts) : ''),
					'IPs' => (sizeof($IPs) > 0 ? implode(',', $IPs) : '')
					));
				if(is_array($res) && isset($res['spam']) && $res['spam'] == 1){
					wfConfig::inc('totalSpamStopped');
					return 'spam';
				}
			} catch(Exception $e){
				//API server is probably down
			}
		}
		wfConfig::inc('totalCommentsFiltered');
		return $approved;
	}
	public static function getMyHomeURL(){
		return network_admin_url('admin.php?page=Wordfence', 'http');
	}
	public static function getMyOptionsURL(){
		return network_admin_url('admin.php?page=WordfenceSecOpt', 'http');
	}

	public static function alert($subject, $alertMsg, $IP){
		wfConfig::inc('totalAlertsSent');
		$emails = wfConfig::getAlertEmails();
		if(sizeof($emails) < 1){ return; }

		$IPMsg = "";
		if($IP){
			$IPMsg = "User IP: $IP\n";
			$reverse = wfUtils::reverseLookup($IP);
			if($reverse){
				$IPMsg .= "User hostname: " . $reverse . "\n";
			}
			$userLoc = wfUtils::getIPGeo($IP);
			if($userLoc){
				$IPMsg .= "User location: ";
				if($userLoc['city']){
					$IPMsg .= $userLoc['city'] . ', ';
				}
				$IPMsg .= $userLoc['countryName'] . "\n";
			}
		}
		$content = wfUtils::tmpl('email_genericAlert.php', array(
			'isPaid' => wfConfig::get('isPaid'),
			'subject' => $subject,
			'blogName' => get_bloginfo('name', 'raw'),
			'adminURL' => get_admin_url(),
			'alertMsg' => $alertMsg,
			'IPMsg' => $IPMsg,
			'date' => wfUtils::localHumanDate(),
			'myHomeURL' => self::getMyHomeURL(),
			'myOptionsURL' => self::getMyOptionsURL()
			));
		$shortSiteURL = preg_replace('/^https?:\/\//i', '', site_url());
		$subject = "[Wordfence Alert] $shortSiteURL " . $subject;

		$sendMax = wfConfig::get('alert_maxHourly', 0);
		if($sendMax > 0){
			$sendArr = wfConfig::get_ser('alertFreqTrack', array());
			if(! is_array($sendArr)){
				$sendArr = array();
			}
			$minuteTime = floor(time() / 60);
			$totalSent = 0;
			for($i = $minuteTime; $i > $minuteTime - 60; $i--){
				$totalSent += isset($sendArr[$i]) ? $sendArr[$i] : 0;
			}
			if($totalSent >= $sendMax){
				return;
			}
			$sendArr[$minuteTime] = isset($sendArr[$minuteTime]) ? $sendArr[$minuteTime] + 1 : 1;
			wfConfig::set_ser('alertFreqTrack', $sendArr);
		}
		//Prevent duplicate emails within 1 hour:
		$hash = md5(implode(',', $emails) . ':' . $subject . ':' . $alertMsg . ':' . $IP); //Hex
		$lastHash = wfConfig::get('lastEmailHash', false);
		if($lastHash){
			$lastHashDat = explode(':', $lastHash); //[time, hash]
			if(time() - $lastHashDat[0] < 3600){
				if($lastHashDat[1] == $hash){
					return; //Don't send because this email is identical to the previous email which was sent within the last hour.
				}
			}
		}
		wfConfig::set('lastEmailHash', time() . ':' . $hash);
		wp_mail(implode(',', $emails), $subject, $content);
	}
	public static function getLog(){
		if(! self::$wfLog){
			$wfLog = new wfLog(wfConfig::get('apiKey'), wfUtils::getWPVersion());
			self::$wfLog = $wfLog;
		}
		return self::$wfLog;
	}
	public static function statusPrep(){
		wfConfig::set_ser('wfStatusStartMsgs', array());
		wordfence::status(10, 'info', "SUM_PREP:Preparing a new scan.");
	}
	//In the following functions statusStartMsgs is serialized into the DB so it persists between forks
	public static function statusStart($msg){
		$statusStartMsgs = wfConfig::get_ser('wfStatusStartMsgs', array());
		$statusStartMsgs[] = $msg;
		wfConfig::set_ser('wfStatusStartMsgs', $statusStartMsgs);
		self::status(10, 'info', 'SUM_START:' . $msg);
		return sizeof($statusStartMsgs) - 1;
	}
	public static function statusEnd($idx, $haveIssues, $successFailed = false){
		$statusStartMsgs = wfConfig::get_ser('wfStatusStartMsgs', array());
		if($haveIssues){
			if($successFailed){
				self::status(10, 'info', 'SUM_ENDFAILED:' . $statusStartMsgs[$idx]);
			} else {
				self::status(10, 'info', 'SUM_ENDBAD:' . $statusStartMsgs[$idx]);
			}
		} else {
			if($successFailed){
				self::status(10, 'info', 'SUM_ENDSUCCESS:' . $statusStartMsgs[$idx]);
			} else {
				self::status(10, 'info', 'SUM_ENDOK:' . $statusStartMsgs[$idx]);
			}
		}
		$statusStartMsgs[$idx] = '';
		wfConfig::set_ser('wfStatusStartMsgs', $statusStartMsgs);
	}
	public static function statusEndErr(){
		$statusStartMsgs = wfConfig::get_ser('wfStatusStartMsgs', array());
		for($i = 0; $i < sizeof($statusStartMsgs); $i++){
			if(empty($statusStartMsgs[$i]) === false){
				self::status(10, 'info', 'SUM_ENDERR:' . $statusStartMsgs[$i]);
				$statusStartMsgs[$i] = '';
			}
		}
	}
	public static function statusDisabled($msg){
		self::status(10, 'info', "SUM_DISABLED:" . $msg);
	}
	public static function statusPaidOnly($msg){
		self::status(10, 'info', "SUM_PAIDONLY:" . $msg);
	}
	public static function wfSchemaExists(){
		$db = new wfDB();
		global $wpdb; $prefix = $wpdb->base_prefix;
		$exists = $db->querySingle("show tables like '$prefix"."wfConfig'");
		return $exists ? true : false;
	}
	public static function isDebugOn(){
		if(is_null(self::$debugOn)){
			if(wfConfig::get('debugOn')){
				self::$debugOn = true;
			} else {
				self::$debugOn = false;
			}
		}
		return self::$debugOn;
	}
	//PUBLIC API
	public static function doNotCache(){ //Call this to prevent Wordfence from caching the current page.
		wfCache::doNotCache();
		return true;
	}
	public static function whitelistIP($IP){ //IP as a string in dotted quad notation e.g. '10.11.12.13'
		$IP = trim($IP);
		$user_range = new wfUserIPRange($IP);
		if (!$user_range->isValidRange()) {
			throw new Exception("The IP you provided must be in dotted quad notation or use ranges with square brackets. e.g. 10.11.12.13 or 10.11.12.[1-50]");
		}
		$whites = wfConfig::get('whitelisted', '');
		$arr = explode(',', $whites);
		$arr2 = array();
		foreach($arr as $e){
			if($e == $IP){
				return false;
			}
			$arr2[] = trim($e);
		}
		$arr2[] = $IP;
		wfConfig::set('whitelisted', implode(',', $arr2));
		return true;
	}

	public static function ajax_email_summary_email_address_debug_callback() {
		$email = !empty($_REQUEST['email']) ? $_REQUEST['email'] : null;
		$report = new wfActivityReport();
		return $report->sendReportViaEmail($email) ?
			array('ok' => 1, 'result' => 'Test email sent successfully') :
			array('err' => "Test email failed to send.");
	}

	public static function addDashboardWidget() {
		if (wfUtils::isAdmin() && (is_network_admin() || !is_multisite()) && wfConfig::get('email_summary_dashboard_widget_enabled')) {
			wp_enqueue_style('wordfence-activity-report-widget', wfUtils::getBaseURL() . 'css/activity-report-widget.css', '', WORDFENCE_VERSION);
			$report_date_range = 'week';
			switch (wfConfig::get('email_summary_interval')) {
				case 'biweekly':
					$report_date_range = '2 weeks';
					break;

				case 'monthly':
					$report_date_range = 'month';
					break;
			}
			wp_add_dashboard_widget(
				'wordfence_activity_report_widget',
				'Wordfence activity in the past ' . $report_date_range,
				array('wfActivityReport', 'outputDashboardWidget')
			);
		}
	}

	/**
	 * @return bool
	 */
	public static function hasGDLimitLoginsMUPlugin() {
		return defined('GD_SYSTEM_PLUGIN_DIR') && file_exists(GD_SYSTEM_PLUGIN_DIR . 'limit-login-attempts/limit-login-attempts.php')
			&& defined('LIMIT_LOGIN_DIRECT_ADDR');
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public static function fixGDLimitLoginsErrors($content) {
		if (self::$authError) {
			$content = str_replace(__('<strong>ERROR</strong>: Incorrect username or password.', 'limit-login-attempts') . "<br />\n", '', $content);
			$content .= '<br />' . self::$authError->get_error_message();
		}
		return $content;
	}

	/**
	 * Permanently blocks all temporarily locked out IPs.
	 */
	public static function ajax_permanentlyBlockAllIPs_callback() {
		/** @var wpdb $wpdb */
		global $wpdb;
		$IPs = array();
		$type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : null;
		$reason = !empty($_REQUEST['reason']) ? $_REQUEST['reason'] : 'Manual block by administrator';
		switch ($type) {
			case 'throttled':
				$IPs = $wpdb->get_col('SELECT DISTINCT IP FROM ' . $wpdb->base_prefix . 'wfThrottleLog');
				break;
			case 'lockedOut':
				$lockoutSecs = wfConfig::get('loginSec_lockoutMins') * 60;
				$IPs = $wpdb->get_col($wpdb->prepare('SELECT DISTINCT IP FROM ' . $wpdb->base_prefix . 'wfLockedOut
				WHERE blockedTime + %d > UNIX_TIMESTAMP()', $lockoutSecs));
				break;
			case 'blocked':
				$blockedTime = wfConfig::get('blockedTime');
				$IPs = $wpdb->get_col($wpdb->prepare('SELECT DISTINCT IP FROM ' . $wpdb->base_prefix . 'wfBlocks
				WHERE wfsn = 0
				AND permanent = 0
				AND blockedTime + %d > UNIX_TIMESTAMP()', $blockedTime));
				break;
		}
		if ($IPs && is_array($IPs)) {
			foreach ($IPs as $IP) {
				self::getLog()->blockIP(wfUtils::inet_ntop($IP), $reason, false, true);
			}
		}
		switch ($type) {
			case 'lockedOut':
				if ($IPs) {
					foreach ($IPs as &$IP) {
						$IP = $wpdb->prepare('%s', $IP);
					}
					$wpdb->query('DELETE FROM ' . $wpdb->base_prefix . 'wfLockedOut WHERE IP IN ('. join(', ', $IPs).')');
				}
				break;
		}
		return array('ok' => 1);
	}


	/**
	 * Modify the query to prevent username enumeration.
	 *
	 * @param array $query_vars
	 * @return array
	 */
	public static function preventAuthorNScans($query_vars) {
		if (wfConfig::get('loginSec_disableAuthorScan') && !is_admin() &&
			!empty($query_vars['author']) && is_numeric(preg_replace('/[^0-9]/', '', $query_vars['author'])) &&
			(
				(isset($_GET['author']) && is_numeric(preg_replace('/[^0-9]/', '', $_GET['author']))) ||
				(isset($_POST['author']) && is_numeric(preg_replace('/[^0-9]/', '', $_POST['author'])))
			)
		) {
			$query_vars['author'] = -1;
		}
		return $query_vars;
	}


	/**
	 * @param WP_Upgrader $updater
	 * @param array $hook_extra
	 */
	public static function hideReadme($updater, $hook_extra = null) {
		if (wfConfig::get('other_hideWPVersion')) {
			wfUtils::hideReadme();
		}
	}
}
?>
