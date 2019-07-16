<?php // Uninstall Plugin

if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) exit();

delete_option('disable_gutenberg_options');
