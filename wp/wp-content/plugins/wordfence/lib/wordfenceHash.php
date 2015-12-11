<?php
require_once('wordfenceClass.php');
class wordfenceHash {
	private $engine = false;
	private $db = false;
	private $startTime = false;

	//Begin serialized vars
	public $striplen = 0;
	public $totalFiles = 0;
	public $totalDirs = 0;
	public $totalData = 0; //To do a sanity check, don't use 'du' because it gets sparse files wrong and reports blocks used on disk. Use : find . -type f -ls | awk '{total += $7} END {print total}'
	public $linesOfPHP = 0;
	public $linesOfJCH = 0; //lines of HTML, CSS and javascript
	public $stoppedOnFile = false;
	private $coreEnabled = false;
	private $pluginsEnabled = false;
	private $themesEnabled = false;
	private $malwareEnabled = false;
	private $knownFiles = false;
	private $malwareData = "";
	private $haveIssues = array();
	private $status = array();
	private $possibleMalware = array();
	private $path = false;
	private $only = false;
	private $totalForks = 0;

	/**
	 * @param string $striplen
	 * @param string $path
	 * @param array $only
	 * @param array $themes
	 * @param array $plugins
	 * @param wfScanEngine $engine
	 * @throws Exception
	 */
	public function __construct($striplen, $path, $only, $themes, $plugins, $engine){
		$this->striplen = $striplen;
		$this->path = $path;
		$this->only = $only;
		
		$this->startTime = microtime(true);

		if(wfConfig::get('scansEnabled_core')){
			$this->coreEnabled = true;
		}
		if(wfConfig::get('scansEnabled_plugins')){
			$this->pluginsEnabled = true;
		}
		if(wfConfig::get('scansEnabled_themes')){
			$this->themesEnabled = true;
		}
		if(wfConfig::get('scansEnabled_malware')){
			$this->malwareEnabled = true;
		}
		$this->db = new wfDB();

		//Doing a delete for now. Later we can optimize this to only scan modified files.
		//$this->db->queryWrite("update " . $this->db->prefix() . "wfFileMods set oldMD5 = newMD5");			
		$this->db->queryWrite("delete from " . $this->db->prefix() . "wfFileMods");
		$fetchCoreHashesStatus = wordfence::statusStart("Fetching core, theme and plugin file signatures from Wordfence");	
		$dataArr = $engine->api->binCall('get_known_files', json_encode(array(
				'plugins' => $plugins,
				'themes' => $themes
				)) );
		if($dataArr['code'] != 200){
			wordfence::statusEndErr();
			throw new Exception("Got error response from Wordfence servers: " . $dataArr['code']);
		}
		$this->knownFiles = @json_decode($dataArr['data'], true);
		if(! is_array($this->knownFiles)){
			wordfence::statusEndErr();
			throw new Exception("Invalid response from Wordfence servers.");
		}
		wordfence::statusEnd($fetchCoreHashesStatus, false, true);
		if($this->malwareEnabled){
			$malwarePrefixStatus = wordfence::statusStart("Fetching list of known malware files from Wordfence");
			$malwareData = $engine->api->getStaticURL('/malwarePrefixes.bin');
			if(! $malwareData){
				wordfence::statusEndErr();
				throw new Exception("Could not fetch malware signatures from Wordfence servers.");
			}
			if(strlen($malwareData) % 4 != 0){
				wordfence::statusEndErr();
				throw new Exception("Malware data received from Wordfence servers was not valid.");
			}
			$this->malwareData = array();
			for($i = 0; $i < strlen($malwareData); $i += 4){
				$this->malwareData[substr($malwareData, $i, 4)] = '1';
			}
			wordfence::statusEnd($malwarePrefixStatus, false, true);
		}

		if($this->path[strlen($this->path) - 1] != '/'){
			$this->path .= '/';
		}
		if(! is_readable($path)){
			throw new Exception("Could not read directory " . $this->path . " to do scan.");
		}
		$this->haveIssues = array(
			'core' => false,
			'themes' => false,
			'plugins' => false,
			'malware' => false
			);
		if($this->coreEnabled){ $this->status['core'] = wordfence::statusStart("Comparing core WordPress files against originals in repository"); } else { wordfence::statusDisabled("Skipping core scan"); }
		if($this->themesEnabled){ $this->status['themes'] = wordfence::statusStart("Comparing open source themes against WordPress.org originals"); } else { wordfence::statusDisabled("Skipping theme scan"); }
		if($this->pluginsEnabled){ $this->status['plugins'] = wordfence::statusStart("Comparing plugins against WordPress.org originals"); } else { wordfence::statusDisabled("Skipping plugin scan"); }
		if($this->malwareEnabled){ $this->status['malware'] = wordfence::statusStart("Scanning for known malware files"); } else { wordfence::statusDisabled("Skipping malware scan"); }
	}
	public function __sleep(){
		return array('striplen', 'totalFiles', 'totalDirs', 'totalData', 'linesOfPHP', 'linesOfJCH', 'stoppedOnFile', 'coreEnabled', 'pluginsEnabled', 'themesEnabled', 'malwareEnabled', 'knownFiles', 'malwareData', 'haveIssues', 'status', 'possibleMalware', 'path', 'only', 'totalForks');
	}
	public function __wakeup(){
		$this->db = new wfDB();
		$this->startTime = microtime(true);
		$this->totalForks++;
	}
	public function run($engine){ //base path and 'only' is a list of files and dirs in the bast that are the only ones that should be processed. Everything else in base is ignored. If only is empty then everything is processed.
		if($this->totalForks > 1000){
			throw new Exception("Wordfence file scanner detected a possible infinite loop. Exiting on file: " . $this->stoppedOnFile);
		}
		$this->engine = $engine;
		$files = scandir($this->path);
		foreach($files as $file){
			if($file == '.' || $file == '..'){ continue; }
			if(sizeof($this->only) > 0 && (! in_array($file, $this->only))){
				continue;
			}
			$file = $this->path . $file;
			wordfence::status(4, 'info', "Hashing item in base dir: $file");
			$this->_dirHash($file);
		}
		wordfence::status(2, 'info', "Analyzed " . $this->totalFiles . " files containing " . wfUtils::formatBytes($this->totalData) . " of data.");
		if($this->coreEnabled){ wordfence::statusEnd($this->status['core'], $this->haveIssues['core']); }
		if($this->themesEnabled){ wordfence::statusEnd($this->status['themes'], $this->haveIssues['themes']); }
		if($this->pluginsEnabled){ wordfence::statusEnd($this->status['plugins'], $this->haveIssues['plugins']); }
		if(sizeof($this->possibleMalware) > 0){
			$malwareResp = $engine->api->binCall('check_possible_malware', json_encode($this->possibleMalware));
			if($malwareResp['code'] != 200){
				wordfence::statusEndErr();
				throw new Exception("Invalid response from Wordfence API during check_possible_malware");
			}
			$malwareList = json_decode($malwareResp['data'], true);
			if(is_array($malwareList) && sizeof($malwareList) > 0){
				for($i = 0; $i < sizeof($malwareList); $i++){ 
					$file = $malwareList[$i][0];
					$md5 = $malwareList[$i][1];
					$name = $malwareList[$i][2];
					$this->haveIssues['malware'] = true;
					$this->engine->addIssue(
						'file', 
						1, 
						$this->path . $file, 
						$md5,
						'This file is suspected malware: ' . $file,
						"This file's signature matches a known malware file. The title of the malware is '" . $name . "'. Immediately inspect this file using the 'View' option below and consider deleting it from your server.",
						array(
							'file' => $file,
							'cType' => 'unknown',
							'canDiff' => false,
							'canFix' => false,
							'canDelete' => true
							)
						);
				}
			}
		}
		if($this->malwareEnabled){ wordfence::statusEnd($this->status['malware'], $this->haveIssues['malware']); }
	}
	private function _dirHash($path){
		if(substr($path, -3, 3) == '/..' || substr($path, -2, 2) == '/.'){
			return;
		}
		if(! is_readable($path)){ return; } //Applies to files and dirs
		if(is_dir($path)){
			$this->totalDirs++;
			if($path[strlen($path) - 1] != '/'){
				$path .= '/';
			}
			$cont = scandir($path);
			for($i = 0; $i < sizeof($cont); $i++){
				if($cont[$i] == '.' || $cont[$i] == '..'){ continue; }
				$file = $path . $cont[$i];
				if(is_file($file)){
					$this->processFile($file);
				} else if(is_dir($file)) {
					$this->_dirHash($file);
				}
			}
		} else {
			if(is_file($path)){
				$this->processFile($path);
			}
		}
	}
	private function processFile($realFile){
		$file = substr($realFile, $this->striplen);
		if( (! $this->stoppedOnFile) && microtime(true) - $this->startTime > $this->engine->maxExecTime){ //max X seconds but don't allow fork if we're looking for the file we stopped on. Search mode is VERY fast.
			$this->stoppedOnFile = $file;
			wordfence::status(4, 'info', "Calling fork() from wordfenceHash::processFile with maxExecTime: " . $this->engine->maxExecTime);
			$this->engine->fork();
			//exits
		}

		$exclude = WordfenceScanner::getExcludeFilePattern();
		if ($exclude && preg_match($exclude, $realFile)) {
			return;
		}


		//Put this after the fork, that way we will at least scan one more file after we fork if it takes us more than 10 seconds to search for the stoppedOnFile
		if($this->stoppedOnFile && $file != $this->stoppedOnFile){
			return;
		} else if($this->stoppedOnFile && $file == $this->stoppedOnFile){
			$this->stoppedOnFile = false; //Continue scanning
		}

		if(wfUtils::fileTooBig($realFile)){
			wordfence::status(4, 'info', "Skipping file larger than max size: $realFile");
			return;
		}
		if (function_exists('memory_get_usage')) {
			wordfence::status(4, 'info', "Scanning: $realFile (Mem:" . sprintf('%.1f', memory_get_usage(true) / (1024 * 1024)) . "M)");
		} else {
			wordfence::status(4, 'info', "Scanning: $realFile");
		}
		wfUtils::beginProcessingFile($file);
		$wfHash = self::wfHash($realFile); 
		if($wfHash){
			$md5 = strtoupper($wfHash[0]);
			$shac = strtoupper($wfHash[1]);
			$knownFile = 0;
			if($this->malwareEnabled && $this->isMalwarePrefix($md5)){
				$this->possibleMalware[] = array($file, $md5);
			}
			if(isset($this->knownFiles['core'][$file])){
				if(strtoupper($this->knownFiles['core'][$file]) == $shac){
					$knownFile = 1;
				} else {
					if($this->coreEnabled){
						$localFile = ABSPATH . '/' . preg_replace('/^[\.\/]+/', '', $file);
						$fileContents = @file_get_contents($localFile);
						if($fileContents && (! preg_match('/<\?' . 'php[\r\n\s\t]*\/\/[\r\n\s\t]*Silence is golden\.[\r\n\s\t]*(?:\?>)?[\r\n\s\t]*$/s', $fileContents))){ //<?php
							if(! $this->isSafeFile($shac)){
									
								$this->haveIssues['core'] = true;
								$this->engine->addIssue(
									'file', 
									1, 
									'coreModified' . $file . $md5, 
									'coreModified' . $file,
									'WordPress core file modified: ' . $file,
									"This WordPress core file has been modified and differs from the original file distributed with this version of WordPress.",
									array(
										'file' => $file,
										'cType' => 'core',
										'canDiff' => true,
										'canFix' => true,
										'canDelete' => false
										)
									);
							}
						}
					}
				}
			} else if(isset($this->knownFiles['plugins'][$file])){
				if(in_array($shac, $this->knownFiles['plugins'][$file])){
					$knownFile = 1;
				} else {
					if($this->pluginsEnabled){
						if(! $this->isSafeFile($shac)){
							$itemName = $this->knownFiles['plugins'][$file][0];
							$itemVersion = $this->knownFiles['plugins'][$file][1];
							$cKey = $this->knownFiles['plugins'][$file][2];
							$this->haveIssues['plugins'] = true;
							$this->engine->addIssue(
								'file', 
								2, 
								'modifiedplugin' . $file . $md5, 
								'modifiedplugin' . $file,
								'Modified plugin file: ' . $file,
								"This file belongs to plugin \"$itemName\" version \"$itemVersion\" and has been modified from the file that is distributed by WordPress.org for this version. Please use the link to see how the file has changed. If you have modified this file yourself, you can safely ignore this warning. If you see a lot of changed files in a plugin that have been made by the author, then try uninstalling and reinstalling the plugin to force an upgrade. Doing this is a workaround for plugin authors who don't manage their code correctly. [See our FAQ on www.wordfence.com for more info]",
								array(
									'file' => $file,
									'cType' => 'plugin',
									'canDiff' => true,
									'canFix' => true,
									'canDelete' => false,
									'cName' => $itemName,
									'cVersion' => $itemVersion,
									'cKey' => $cKey 
									)
								);
						}
					}

				}
			} else if(isset($this->knownFiles['themes'][$file])){
				if(in_array($shac, $this->knownFiles['themes'][$file])){
					$knownFile = 1;
				} else {
					if($this->themesEnabled){
						if(! $this->isSafeFile($shac)){
							$itemName = $this->knownFiles['themes'][$file][0];
							$itemVersion = $this->knownFiles['themes'][$file][1];
							$cKey = $this->knownFiles['themes'][$file][2];
							$this->haveIssues['themes'] = true;
							$this->engine->addIssue(
								'file', 
								2, 
								'modifiedtheme' . $file . $md5, 
								'modifiedtheme' . $file,
								'Modified theme file: ' . $file,
								"This file belongs to theme \"$itemName\" version \"$itemVersion\" and has been modified from the original distribution. It is common for site owners to modify their theme files, so if you have modified this file yourself you can safely ignore this warning.",
								array(
									'file' => $file,
									'cType' => 'theme',
									'canDiff' => true,
									'canFix' => true,
									'canDelete' => false,
									'cName' => $itemName,
									'cVersion' => $itemVersion,
									'cKey' => $cKey 
									)
								);
						}
					}

				}
			}
			// knownFile means that the file is both part of core or a known plugin or theme AND that we recognize the file's hash. 
			// we could split this into files who's path we recognize and file's who's path we recognize AND who have a valid sig.
			// But because we want to scan files who's sig we don't recognize, regardless of known path or not, we only need one "knownFile" field.
			$this->db->queryWrite("insert into " . $this->db->prefix() . "wfFileMods (filename, filenameMD5, knownFile, oldMD5, newMD5) values ('%s', unhex(md5('%s')), %d, '', unhex('%s')) ON DUPLICATE KEY UPDATE newMD5=unhex('%s'), knownFile=%d", $file, $file, $knownFile, $md5, $md5, $knownFile);

			//Now that we know we can open the file, lets update stats
			if(preg_match('/\.(?:js|html|htm|css)$/i', $realFile)){
				$this->linesOfJCH += sizeof(file($realFile));
			} else if(preg_match('/\.php$/i', $realFile)){
				$this->linesOfPHP += sizeof(file($realFile));
			}
			$this->totalFiles++;
			$this->totalData += filesize($realFile); //We already checked if file overflows int in the fileTooBig routine above
			if($this->totalFiles % 100 === 0){
				wordfence::status(2, 'info', "Analyzed " . $this->totalFiles . " files containing " . wfUtils::formatBytes($this->totalData) . " of data so far");
			}
		} else {
			//wordfence::status(2, 'error', "Could not gen hash for file (probably because we don't have permission to access the file): $realFile");
		}
		wfUtils::endProcessingFile();
	}
	public static function wfHash($file){
		wfUtils::errorsOff();
		$md5 = @md5_file($file, false);
		wfUtils::errorsOn();

		if(! $md5){ return false; }
		$fp = @fopen($file, "rb");
		if(! $fp){
			return false;
		}
		$ctx = hash_init('sha256');
		while (!feof($fp)) {
			hash_update($ctx, str_replace( array("\n","\r","\t"," ") ,"",fread($fp, 65536)));
		}
		$shac = hash_final($ctx, false);
		return array($md5, $shac);
	}
	private function isMalwarePrefix($hexMD5){
		$binPrefix = pack("H*", substr($hexMD5, 0, 8));
		if(isset($this->malwareData[$binPrefix])){
			return true;
		}
		return false;
	}
	private function isSafeFile($shac){
		$result = $this->engine->api->call('is_safe_file', array(), array('shac' => strtoupper($shac)));
		if(isset($result['isSafe']) && $result['isSafe'] == 1){
			return true;
		}
		return false;
	}
}
?>
