<?php
class wfScan {
	public static $debugMode = false;
	public static $errorHandlingOn = true;
	private static $peakMemAtStart = 0;
	public static function wfScanMain(){
		self::$peakMemAtStart = memory_get_peak_usage();
		$db = new wfDB();
		if($db->errorMsg){
			self::errorExit("Could not connect to database to start scan: " . $db->errorMsg);
		}
		if(! wordfence::wfSchemaExists()){
			self::errorExit("Looks like the Wordfence database tables have been deleted. You can fix this by de-activating and re-activating the Wordfence plugin from your Plugins menu.");
		}
		if( isset( $_GET['test'] ) && $_GET['test'] == '1'){
			echo "WFCRONTESTOK:" . wfConfig::get('cronTestID');
			self::status(4, 'info', "Cron test received and message printed");
			exit();
		}
		/* ----------Starting cronkey check -------- */
		self::status(4, 'info', "Scan engine received request.");
		self::status(4, 'info', "Checking cronkey");
		if(! $_GET['cronKey']){ 
			self::status(4, 'error', "Wordfence scan script accessed directly, or WF did not receive a cronkey.");
			echo "If you see this message it means Wordfence is working correctly. You should not access this URL directly. It is part of the Wordfence security plugin and is designed for internal use only.";
			exit();
		}
		self::status(4, 'info', "Fetching stored cronkey for comparison.");
		$currentCronKey = wfConfig::get('currentCronKey', false);
		wfConfig::set('currentCronKey', '');
		if(! $currentCronKey){
			wordfence::status(4, 'error', "Wordfence could not find a saved cron key to start the scan so assuming it started and exiting.");
			exit();
		}
		self::status(4, 'info', "Exploding stored cronkey"); 
		$savedKey = explode(',',$currentCronKey);
		if(time() - $savedKey[0] > 86400){ 
			self::errorExit("The key used to start a scan expired. The value is: " . $savedKey[0] . " and split is: " . $currentCronKey . " and time is: " . time());
		} //keys only last 60 seconds and are used within milliseconds of creation
		self::status(4, 'info', "Checking saved cronkey against cronkey param");
		if($savedKey[1] != $_GET['cronKey']){ 
			self::errorExit("Wordfence could not start a scan because the cron key does not match the saved key. Saved: " . $savedKey[1] . " Sent: " . $_GET['cronKey'] . " Current unexploded: " . $currentCronKey);
		}
		/* --------- end cronkey check ---------- */

		self::status(4, 'info', "Becoming admin for scan");
		self::becomeAdmin();
		self::status(4, 'info', "Done become admin");

		$isFork = ($_GET['isFork'] == '1' ? true : false);

		if(! $isFork){
			self::status(4, 'info', "Checking if scan is already running");
			if(! wfUtils::getScanLock()){
				self::errorExit("There is already a scan running.");
			}
		}
		self::status(4, 'info', "Requesting max memory");
		wfUtils::requestMaxMemory();
		self::status(4, 'info', "Setting up error handling environment");
		set_error_handler('wfScan::error_handler', E_ALL);
		register_shutdown_function('wfScan::shutdown');
		if(! self::$debugMode){
			ob_start('wfScan::obHandler');
		}
		@error_reporting(E_ALL);
		wfUtils::iniSet('display_errors','On');
		self::status(4, 'info', "Setting up scanRunning and starting scan");
		if($isFork){
			$scan = wfConfig::get_ser('wfsd_engine', false, true);
			if($scan){
				self::status(4, 'info', "Got a true deserialized value back from 'wfsd_engine' with type: " . gettype($scan));
				wfConfig::set('wfsd_engine', '', true);
			} else {
				self::status(2, 'error', "Scan can't continue - stored data not found after a fork. Got type: " . gettype($scan));
				wfConfig::set('wfsd_engine', '', true);
				exit();
			}
		} else {
			wordfence::statusPrep(); //Re-initializes all status counters
			$scan = new wfScanEngine();
			$scan->deleteNewIssues();
		}
		try {
			$scan->go();
		} catch (Exception $e){
			wfUtils::clearScanLock();
			self::status(2, 'error', "Scan terminated with error: " . $e->getMessage());
			self::status(10, 'info', "SUM_KILLED:Previous scan terminated with an error. See below.");
			exit();
		}
		wfUtils::clearScanLock();
		self::logPeakMemory();
		self::status(2, 'info', "Wordfence used " . sprintf('%.2f', (wfConfig::get('wfPeakMemory') - self::$peakMemAtStart) / 1024 / 1024) . "MB of memory for scan. Server peak memory usage was: " . sprintf('%.2f', wfConfig::get('wfPeakMemory') / 1024 / 1024) . "MB");
	}
	private static function logPeakMemory(){
		$oldPeak = wfConfig::get('wfPeakMemory', 0);
		$peak = memory_get_peak_usage();
		if($peak > $oldPeak){
			wfConfig::set('wfPeakMemory', $peak);
		}
	}
	public static function obHandler($buf){
		if(strlen($buf) > 1000){
			$buf = substr($buf, 0, 255);
		}
		if(empty($buf) === false && preg_match('/[a-zA-Z0-9]+/', $buf)){
			self::status(1, 'error', $buf);
		}
	}
	public static function error_handler($errno, $errstr, $errfile, $errline){
		if(self::$errorHandlingOn){
			if(preg_match('/wordfence\//', $errfile)){
				$level = 1; //It's one of our files, so level 1
			} else {
				$level = 4; //It's someone elses plugin so only show if debug is enabled
			}
			self::status($level, 'error', "$errstr ($errno) File: $errfile Line: $errline");
		}
		return false;
	}
	public static function shutdown(){
		self::logPeakMemory();
	}
	private static function errorExit($msg){
		wordfence::status(1, 'error', "Scan Engine Error: $msg");
		exit();	
	}
	public static function becomeAdmin(){
		$db = new wfDB();
		global $wpdb;
		$userSource = '';
		if(is_multisite()){
			$users = get_users('role=super&fields=ID');
			if(sizeof($users) < 1){
				$supers = get_super_admins();
				if(sizeof($supers) > 0){
					foreach($supers as $superLogin){
						$superDat = get_user_by('login', $superLogin);
						if($superDat){
							$users = array($superDat->ID);
							$userSource = 'multisite get_super_admins() function';
							break;
						}
					}
				}
			} else {
				$userSource = 'multisite get_users() function';
			}
		} else {
			$users = get_users('role=administrator&fields=ID');
			if(sizeof($users) < 1){
				$supers = get_super_admins();
				if(sizeof($supers) > 0){
					foreach($supers as $superLogin){
						$superDat = get_user_by('login', $superLogin);
						if($superDat){
							$users = array($superDat->ID);
							$userSource = 'singlesite get_super_admins() function';
							break;
						}
					}
				}
			} else {
				$userSource = 'singlesite get_users() function';
			}
		}
		if(sizeof($users) > 0){
			sort($users, SORT_NUMERIC);
			$adminUserID = $users[0];
		} else {
			//Last ditch attempt
			$adminUserID = $db->querySingle("select user_id from " . $wpdb->usermeta . " where meta_key='" . $wpdb->base_prefix . "user_level' order by meta_value desc, user_id asc limit 1");
			if(! $adminUserID){
				//One final attempt for those who have changed their table prefixes but the meta_key is still wp_ prefixed...
				$adminUserID = $db->querySingle("select user_id from " . $wpdb->usermeta . " where meta_key='wp_user_level' order by meta_value desc, user_id asc limit 1");
				if(! $adminUserID){
					self::status(1, 'error', "Could not get the administrator's user ID. Scan can't continue.");
					exit();
				}
			}
			$userSource = 'manual DB query';
		}
		$adminUsername = $db->querySingle("select user_login from " . $wpdb->users . " where ID=%d", $adminUserID);
		self::status(4, 'info', "Scan will run as admin user '$adminUsername' with ID '$adminUserID' sourced from: $userSource");
		wp_set_current_user($adminUserID);
		if(! is_user_logged_in()){
			self::status(1, 'error', "Scan could not sign in as user '$adminUsername' with ID '$adminUserID' from source '$userSource'. Scan can't continue.");
			exit();
		}
		self::status(4, 'info', "Scan authentication complete.");
	}
	private static function status($level, $type, $msg){
		wordfence::status($level, $type, $msg);
	}
}
?>
