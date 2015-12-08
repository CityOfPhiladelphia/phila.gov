<?php

class wfActivityReport {

	/**
	 * @var int
	 */
	private $limit = 10;

	/**
	 * @var wpdb
	 */
	private $db;

	/**
	 * @param int $limit
	 */
	public function __construct($limit = 10) {
		global $wpdb;
		$this->db = $wpdb;
		$this->limit = $limit;
	}

	/**
	 * Schedule the activity report cron job.
	 */
	public static function scheduleCronJob() {
		self::clearCronJobs();

		if (!wfConfig::get('email_summary_enabled', 1)) {
			return;
		}

		if (is_main_site()) {
			list(, $end_time) = wfActivityReport::getReportDateRange();
			wp_schedule_single_event($end_time, 'wordfence_email_activity_report');
		}
	}

	/**
	 * Remove the activity report cron job.
	 */
	public static function disableCronJob() {
		self::clearCronJobs();
	}

	public static function clearCronJobs() {
		wp_clear_scheduled_hook('wordfence_email_activity_report');
	}

	/**
	 * Send out the report and reschedule the next report's cron job.
	 */
	public static function executeCronJob() {
		$report = new self();
		$report->sendReportViaEmail(wfConfig::getAlertEmails());
		self::scheduleCronJob();
	}

	/**
	 * Output a compact version of the email for the WP dashboard.
	 */
	public static function outputDashboardWidget() {
		$report = new self(5);
		echo $report->toWidgetView();
	}

	/**
	 * @return array
	 */
	public static function getReportDateRange() {
		$interval = wfConfig::get('email_summary_interval', 'weekly');
		$offset = get_option('gmt_offset');
		return self::_getReportDateRange($interval, $offset);
	}

	/**
	 * Testable code.
	 *
	 * @param string $interval
	 * @param int    $offset
	 * @param null   $time
	 * @return array
	 */
	public static function _getReportDateRange($interval = 'weekly', $offset = 0, $time = null) {
		if ($time === null) {
			$time = time();
		}

		$day = (int) gmdate('w', $time);
		$month = (int) gmdate("n", $time);
		$day_of_month = (int) gmdate("j", $time);
		$year = (int) gmdate("Y", $time);

		$start_time = 0;
		$end_time = 0;

		switch ($interval) {
			// Send a report 4pm every Monday
			case 'weekly':
				$start_time = gmmktime(16, 0, 0, $month, $day_of_month - $day + 1, $year) + (-$offset * 60 * 60);
				$end_time = $start_time + (86400 * 7);
				break;

			// Send a report 4pm every other Monday
			case 'biweekly':
				$start_time = gmmktime(16, 0, 0, $month, $day_of_month - $day + 1, $year) + (-$offset * 60 * 60);
				$end_time = $start_time + (86400 * 14);
				break;

			// Send a report at 4pm the first of every month
			case 'monthly':
				$start_time = gmmktime(16, 0, 0, $month, 1, $year) + (-$offset * 60 * 60);
				$end_time = gmmktime(16, 0, 0, $month + 1, 1, $year) + (-$offset * 60 * 60);
				break;
		}

		return array($start_time, $end_time);
	}

	/**
	 * @return int
	 */
	public static function getReportDateFrom() {
		$interval = wfConfig::get('email_summary_interval', 'weekly');
		return self::_getReportDateFrom($interval);
	}

	/**
	 * @param string $interval
	 * @param null   $time
	 * @return int
	 */
	public static function _getReportDateFrom($interval = 'weekly', $time = null) {
		if ($time === null) {
			$time = time();
		}

		// weekly
		$from = $time - (86400 * 7);
		switch ($interval) {
			case 'biweekly':
				$from = $time - (86400 * 14);
				break;

			// Send a report at 4pm the first of every month
			case 'monthly':
				$from = strtotime('-1 month', $time);
				break;
		}

		return $from;
	}

	/**
	 * @return array
	 */
	public function getFullReport() {
		$start_time = microtime(true);
		return array(
			'top_ips_blocked'         => $this->getTopIPsBlocked($this->limit),
			'top_countries_blocked'   => $this->getTopCountriesBlocked($this->limit),
			'top_failed_logins'       => $this->getTopFailedLogins($this->limit),
			'recently_modified_files' => $this->getRecentFilesModified($this->limit),
			'updates_needed'          => $this->getUpdatesNeeded(),
			'microseconds'            => microtime(true) - $start_time,
		);
	}

	/**
	 * @return array
	 */
	public function getWidgetReport() {
		$start_time = microtime(true);
		return array(
			'top_ips_blocked'       => $this->getTopIPsBlocked($this->limit),
			'top_countries_blocked' => $this->getTopCountriesBlocked($this->limit),
			'top_failed_logins'     => $this->getTopFailedLogins($this->limit),
			'updates_needed'        => $this->getUpdatesNeeded(),
			'microseconds'          => microtime(true) - $start_time,
		);
	}

	/**
	 * @param int $limit
	 * @return mixed
	 */
	public function getTopIPsBlocked($limit = 10) {
		$where = $this->getBlockedIPWhitelistWhereClause();
		if ($where) {
			$where = 'WHERE NOT (' . $where . ')';
		}

		$results = $this->db->get_results($this->db->prepare(<<<SQL
SELECT *,
SUM(blockCount) as blockCount
FROM {$this->db->prefix}wfBlockedIPLog
$where
GROUP BY IP
ORDER BY blockCount DESC
LIMIT %d
SQL
			, $limit));
		if ($results) {
			foreach ($results as &$row) {
				$row->countryName = $this->getCountryNameByCode($row->countryCode);
			}
		}
		return $results;
	}

	/**
	 * @param int $limit
	 * @return array
	 */
	public function getTopCountriesBlocked($limit = 10) {
		$where = $this->getBlockedIPWhitelistWhereClause();
		if ($where) {
			$where = 'WHERE NOT (' . $where . ')';
		}

		$results = $this->db->get_results($this->db->prepare(<<<SQL
SELECT *, COUNT(IP) as totalIPs, SUM(blockCount) as totalBlockCount
FROM {$this->db->base_prefix}wfBlockedIPLog
$where
GROUP BY countryCode
ORDER BY totalBlockCount DESC
LIMIT %d
SQL
			, $limit));
		if ($results) {
			foreach ($results as &$row) {
				$row->countryName = $this->getCountryNameByCode($row->countryCode);
			}
		}
		return $results;
	}

	/**
	 * @param int $limit
	 * @return mixed
	 */
	public function getTopFailedLogins($limit = 10) {
		$interval = 'UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 7 day))';
		switch (wfConfig::get('email_summary_interval', 'weekly')) {
			case 'biweekly':
				$interval = 'UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 14 day))';
				break;
			case 'monthly':
				$interval = 'UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 month))';
				break;
		}

		$results = $this->db->get_results($this->db->prepare(<<<SQL
SELECT *,
sum(fail) as fail_count,
max(userID) as is_valid_user
FROM {$this->db->base_prefix}wfLogins
WHERE fail = 1
AND ctime > $interval
GROUP BY username
ORDER BY fail_count DESC
LIMIT %d
SQL
			, $limit));
		return $results;
	}

	/**
	 * Generate SQL from the whitelist.  Uses the return format from wfLog::getWhitelistedIPs
	 *
	 * @see wfLog::getWhitelistedIPs
	 * @param array $whitelisted_ips
	 * @return string
	 */
	public function getBlockedIPWhitelistWhereClause($whitelisted_ips = null) {
		if ($whitelisted_ips === null) {
			$whitelisted_ips = wordfence::getLog()->getWhitelistedIPs();
		}
		if (!is_array($whitelisted_ips)) {
			return false;
		}

		$where = '';
		/** @var array|wfUserIPRange|string $ip_range */
		foreach ($whitelisted_ips as $ip_range) {
			if (is_array($ip_range) && count($ip_range) == 2) {
				$where .= $this->db->prepare('IP BETWEEN %s AND %s', $ip_range[0], $ip_range[1]) . ' OR ';
			} elseif (is_a($ip_range, 'wfUserIPRange')) {
				$where .= $ip_range->toSQL('IP') . ' OR ';
			} elseif (is_string($ip_range) || is_numeric($ip_range)) {
				$where .=  $this->db->prepare('IP = %s', $ip_range) . ' OR ';
			}
		}
		if ($where) {
			// remove the extra ' OR '
			$where = substr($where, 0, -4);
		}

		return $where;
	}

	/**
	 * Returns any updates needs or false if everything is up to date.
	 *
	 * @return array|bool
	 */
	public function getUpdatesNeeded() {
		$update_check = new wfUpdateCheck();
		$needs_update = $update_check->checkAllUpdates()
			->needsAnyUpdates();
		if ($needs_update) {
			return array(
				'core'    => $update_check->getCoreUpdateVersion(),
				'plugins' => $update_check->getPluginUpdates(),
				'themes'  => $update_check->getThemeUpdates(),
			);
		}
		return false;
	}

	/**
	 * Returns list of files modified within given timeframe.
	 *
	 * @todo Add option to configure the regex used to filter files allowed in this list.
	 * @todo Add option to exclude directories (such as cache directories).
	 *
	 * @param string $directory Search for files within this directory
	 * @param int    $time_range One week
	 * @param int    $limit Max files to return in results
	 * @param int    $directory_limit Hard limit for number of files to search within a directory.
	 * @return array
	 */
	public function getRecentFilesModified($limit = 300, $directory = ABSPATH, $time_range = 604800, $directory_limit = 20000) {
		$recently_modified = new wfRecentlyModifiedFiles($directory);
		$recently_modified->run();
		return $recently_modified->mostRecentFiles($limit);
	}

	/**
	 * Remove entries older than a week in the IP log.
	 */
	public function rotateIPLog() {
		// default to weekly
		$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 7 day)) / 86400)';
		switch (wfConfig::get('email_summary_interval', 'weekly')) {
			case 'biweekly':
				$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 14 day)) / 86400)';
				break;
			case 'monthly':
				$interval = 'FLOOR(UNIX_TIMESTAMP(DATE_SUB(NOW(), interval 1 month)) / 86400)';
				break;
		}
		$this->db->query(<<<SQL
DELETE FROM {$this->db->base_prefix}wfBlockedIPLog
WHERE unixday < $interval
SQL
		);
	}

	/**
	 * @param mixed $ip_address
	 * @param int|null $unixday
	 */
	public static function logBlockedIP($ip_address, $unixday = null) {
		/** @var wpdb $wpdb */
		global $wpdb;

		if (wfUtils::isValidIP($ip_address)) {
			$ip_bin = wfUtils::inet_pton($ip_address);
		} else {
			$ip_bin = $ip_address;
			$ip_address = wfUtils::inet_ntop($ip_bin);
		}

		$blocked_table = "{$wpdb->base_prefix}wfBlockedIPLog";

		$unixday_insert = 'FLOOR(UNIX_TIMESTAMP() / 86400)';
		if (is_int($unixday)) {
			$unixday_insert = absint($unixday);
		}

		$country = wfUtils::IP2Country($ip_address);

		$wpdb->query($wpdb->prepare(<<<SQL
INSERT INTO $blocked_table (IP, countryCode, blockCount, unixday)
VALUES (%s, %s, 1, $unixday_insert)
ON DUPLICATE KEY UPDATE blockCount = blockCount + 1
SQL
			, $ip_bin, $country));
	}

	/**
	 * @param $code
	 * @return string
	 */
	public function getCountryNameByCode($code) {
		static $wfBulkCountries;
		if (!isset($wfBulkCountries)) {
			include 'wfBulkCountries.php';
		}
		return array_key_exists($code, $wfBulkCountries) ? $wfBulkCountries[$code] : "";
	}

	/**
	 * @return wfActivityReportView
	 */
	public function toView() {
		return new wfActivityReportView('reports/activity-report', $this->getFullReport() + array(
				'limit' => $this->getLimit(),
			));
	}

	/**
	 * @return wfActivityReportView
	 */
	public function toWidgetView() {
		return new wfActivityReportView('reports/activity-report', $this->getWidgetReport() + array(
				'limit' => $this->getLimit(),
			));
	}

	/**
	 * @return wfActivityReportView
	 */
	public function toEmailView() {
		return new wfActivityReportView('reports/activity-report-email-inline', $this->getFullReport());
	}

	/**
	 * @param $email_addresses string|array
	 * @return bool
	 */
	public function sendReportViaEmail($email_addresses) {
		$shortSiteURL = preg_replace('/^https?:\/\//i', '', site_url());
		return wp_mail($email_addresses, 'Wordfence activity for ' . date_i18n(get_option('date_format')) . ' on ' . $shortSiteURL, $this->toEmailView()->__toString(), 'Content-Type: text/html');
	}

	/**
	 * @return string
	 * @throws wfViewNotFoundException
	 */
	public function render() {
		return $this->toView()
			->render();
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->toView()
			->__toString();
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * @param int $limit
	 */
	public function setLimit($limit) {
		$this->limit = $limit;
	}
}


class wfRecentlyModifiedFiles extends wfDirectoryIterator {

	/**
	 * @var int
	 */
	private $time_range = 604800;

	/**
	 * @var array
	 */
	private $files = array();
	private $excluded_directories;

	/**
	 * @param string $directory
	 * @param int    $max_files_per_directory
	 * @param int    $max_iterations
	 * @param int    $time_range
	 */
	public function __construct($directory = ABSPATH, $max_files_per_directory = 20000, $max_iterations = 250000, $time_range = 604800) {
		parent::__construct($directory, $max_files_per_directory, $max_iterations);
		$this->time_range = $time_range;
		$excluded_directories = explode(',', (string) wfConfig::get('email_summary_excluded_directories'));
		$this->excluded_directories = array();
		foreach ($excluded_directories  as $index => $path) {
			if (($dir = realpath(ABSPATH . $path)) !== false) {
				$this->excluded_directories[$dir] = 1;
			}
		}
	}

	/**
	 * @param $dir
	 * @return bool
	 */
	protected function scan($dir) {
		if (!array_key_exists(realpath($dir), $this->excluded_directories)) {
			return parent::scan($dir);
		}
		return true;
	}


	/**
	 * @param string $file
	 */
	public function file($file) {
		$mtime = filemtime($file);
		if (time() - $mtime < $this->time_range) {
			$this->files[] = array($file, $mtime);
		}
	}

	/**
	 * @param int $limit
	 * @return array
	 */
	public function mostRecentFiles($limit = 300) {
		usort($this->files, array(
			$this,
			'_sortMostRecentFiles',
		));
		return array_slice($this->files, 0, $limit);
	}

	/**
	 * Sort in descending order.
	 *
	 * @param $a
	 * @param $b
	 * @return int
	 */
	private function _sortMostRecentFiles($a, $b) {
		if ($a[1] > $b[1]) {
			return -1;
		}
		if ($a[1] < $b[1]) {
			return 1;
		}
		return 0;
	}

	/**
	 * @return mixed
	 */
	public function getFiles() {
		return $this->files;
	}
}


class wfActivityReportView extends wfView {

	/**
	 * @param $file
	 * @return string
	 */
	public function displayFile($file) {
		if (stripos($file, ABSPATH) === 0) {
			return substr($file, strlen(ABSPATH));
		}
		return $file;
	}

	/**
	 * @param null $unix_time
	 * @return string
	 */
	public function modTime($unix_time = null) {
		if ($unix_time === null) {
			$unix_time = time();
		}
		return date_i18n('F j, Y g:ia', $unix_time);
	}
}
