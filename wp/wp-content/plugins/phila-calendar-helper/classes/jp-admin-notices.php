<?php
/*
Plugin Name: JolekPress Easy Admin Notices
Plugin URI: https://github.com/JolekPress/Easy-WordPress-Admin-Notifications
Description: Allows for easy setting and display of WordPress admin notices
Version: 0.1.0
Author: John Oleksowicz
Author URI: http://jolekpress.com
*/

/**
 * Class JP_Easy_Admin_Notices
 *
 * Handles setting and displaying dismissable admin notices.
 *
 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
 */
class JP_Easy_Admin_Notices
{
    const NOTICES_OPTION_KEY = 'johns_admin_notices';

    public static function init()
    {
        add_action('admin_notices', [__CLASS__, 'output_notices']);
    }

    /**
     * Checks for any stored notices and outputs them. Hooked to admin_notices action.
     */
    public static function output_notices()
    {
        $notices = self::get_notices();
        if (empty($notices)) {
            return;
        }

        // Iterate over stored notices and output them.
        foreach ($notices as $type => $messages) {
            foreach ($messages as $message) {
                printf('<div class="notice notice-%1$s is-dismissible">
                    <p>%2$s</p>
                </div>',
                    $type,
                    $message
                );
            }
        }

        // All stored notices have been output. Update the stored array of notices to be an empty array.
        self::update_notices([]);
    }

    /**
     * Retrieves any stored notices.
     *
     * @return array|void
     */
    private static function get_notices()
    {
        $notices = get_option(self::NOTICES_OPTION_KEY, []);

        return $notices;
    }

    /**
     * Update the stored notices in the options table with a new array.
     *
     * @param array $notices
     */
    private static function update_notices(array $notices)
    {
        update_option(self::NOTICES_OPTION_KEY, $notices);
    }

    /**
     * Adds a notice to the stored notices to be displayed the next time the admin_notices action runs.
     *
     * @param $message
     * @param string $type
     */
    private static function add_notice($message, $type = 'success')
    {
        $notices = self::get_notices();

        $notices[$type][] = $message;

        self::update_notices($notices);
    }

    /**
     * Success messages are green
     *
     * @param $message
     */
    public static function add_success($message)
    {
        self::add_notice($message, 'success');
    }

    /**
     * Errors are red
     *
     * @param $message
     */
    public static function add_error($message)
    {
        self::add_notice($message, 'error');
    }

    /**
     * Warnings are yellow
     *
     * @param $message
     */
    public static function add_warning($message)
    {
        self::add_notice($message, 'warning');
    }

    /**
     * Info is blue
     *
     * @param $message
     */
    public static function add_info($message)
    {
        self::add_notice($message, 'info');
    }
}

JP_Easy_Admin_Notices::init();

function jp_notices_add_success($message)
{
    JP_Easy_Admin_Notices::add_success($message);
}

function jp_notices_add_error($message)
{
    JP_Easy_Admin_Notices::add_error($message);
}

function jp_notices_add_warning($message)
{
    JP_Easy_Admin_Notices::add_warning($message);
}

function jp_notices_add_info($message)
{
    JP_Easy_Admin_Notices::add_info($message);
}
