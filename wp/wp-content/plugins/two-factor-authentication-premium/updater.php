<?php

if (!defined('ABSPATH')) die('No direct access.');

if (!class_exists('Updraft_Manager_Updater_1_8')) require_once(__DIR__.'/vendor/davidanderson684/simba-plugin-manager-updater/class-udm-updater.php');

try {
	$simba_tfa_updater = new Updraft_Manager_Updater_1_8('https://www.simbahosting.co.uk/s3', 1, 'two-factor-authentication-premium/two-factor-login.php');
} catch (Exception $e) {
	error_log($e->getMessage());
}

//$simba_tfa_updater->updater->debug = true;
