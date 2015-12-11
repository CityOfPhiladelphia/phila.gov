<?php
define('WORDFENCE_API_VERSION', '2.20');
define('WORDFENCE_API_URL_SEC', 'https://noc1.wordfence.com/');
define('WORDFENCE_API_URL_NONSEC', 'http://noc1.wordfence.com/');
define('WORDFENCE_HACKATTEMPT_URL', 'http://noc3.wordfence.com:9050/');
define('WORDFENCE_MAX_SCAN_TIME', 86400); //Increased this from 10 mins to 1 day because very big scans run for a long time. Users can use kill.
define('WORDFENCE_TRANSIENTS_TIMEOUT', 3600); //how long are items cached in seconds e.g. files downloaded for diffing
define('WORDFENCE_MAX_IPLOC_AGE', 86400); //1 day
define('WORDFENCE_CRAWLER_VERIFY_CACHE_TIME', 604800); 
define('WORDFENCE_REVERSE_LOOKUP_CACHE_TIME', 86400);
define('WORDFENCE_MAX_FILE_SIZE_TO_PROCESS', 52428800); //50 megs
?>
