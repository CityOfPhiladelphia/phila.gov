<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ))
    exit();
if(!(get_option('mo_saml_keep_settings_on_deletion')==='true')) {
    if (!is_multisite()) {
        // delete all stored key-value pairs
        delete_option('mo_saml_host_name');
        delete_option('mo_saml_enable_cloud_broker');
        delete_option('mo_saml_new_registration');
        delete_option('mo_saml_admin_phone');
        delete_option('mo_saml_admin_email');
        delete_option('mo_saml_admin_password');
        delete_option('mo_saml_verify_customer');
        delete_option('mo_saml_admin_customer_key');
        delete_option('mo_saml_admin_api_key');
        delete_option('mo_saml_customer_token');
        delete_option('mo_saml_message');
        delete_option('mo_saml_registration_status');
        delete_option('saml_idp_config_id');
        delete_option('saml_identity_name');
        delete_option('mo_saml_identity_provider_identifier_name');
        delete_option('saml_login_url');
        delete_option('saml_logout_url');
        delete_option('saml_issuer');
        delete_option('saml_x509_certificate');
        delete_option('saml_response_signed');
        delete_option('saml_assertion_signed');
        delete_option('saml_am_first_name');
        delete_option('saml_am_username');
        delete_option('saml_am_email');
        delete_option('saml_am_last_name');
        delete_option('saml_am_default_user_role');
        delete_option('saml_am_role_mapping');
        delete_option('saml_am_group_name');
        delete_option('mo_saml_idp_config_complete');
        delete_option('mo_saml_enable_login_redirect');
        delete_option('mo_saml_allow_wp_signin');
        delete_option('saml_am_account_matcher');
        delete_option('mo_saml_transactionId');
        delete_option('mo_saml_force_authentication');
        delete_option('saml_am_dont_allow_unlisted_user_role');
        delete_option('mo_saml_free_version');
        delete_option('mo_saml_admin_company');
        delete_option('mo_saml_admin_first_name');
        delete_option('mo_saml_admin_last_name');
        delete_option('mo_saml_show_mo_idp_message');
        delete_option('mo_saml_guest_log');
        delete_option('mo_saml_guest_enabled');
        delete_option('mo_license_plan_from_feedback');
        delete_option('mo_saml_license_message');
	    delete_option('MO_SAML_REQUEST');
	    delete_option('MO_SAML_RESPONSE');
	    delete_option('MO_SAML_TEST');
	    delete_option('mo_saml_encoding_enabled');
        delete_option('mo_saml_show_upgrade_notice');
	    delete_option('mo_saml_show_addons_notice');
        $users = get_users(array());
        foreach ($users as $user) {
            delete_user_meta($user->ID, 'mo_saml_session_index');
            delete_user_meta($user->ID, 'mo_saml_name_id');
        }
    } else {
        global $wpdb;
        $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        $original_blog_id = get_current_blog_id();

        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            // delete all your options
            // E.g: delete_option( {option name} );
            delete_option('mo_saml_host_name');
            delete_option('mo_saml_enable_cloud_broker');
            delete_option('mo_saml_new_registration');
            delete_option('mo_saml_admin_phone');
            delete_option('mo_saml_admin_email');
            delete_option('mo_saml_admin_password');
            delete_option('mo_saml_verify_customer');
            delete_option('mo_saml_admin_customer_key');
            delete_option('mo_saml_admin_api_key');
            delete_option('mo_saml_customer_token');
            delete_option('mo_saml_message');
            delete_option('mo_saml_registration_status');
            delete_option('saml_idp_config_id');
            delete_option('saml_identity_name');
            delete_option('saml_login_url');
            delete_option('saml_logout_url');
            delete_option('saml_issuer');
            delete_option('saml_x509_certificate');
            delete_option('saml_response_signed');
            delete_option('saml_assertion_signed');
            delete_option('saml_am_first_name');
            delete_option('saml_am_username');
            delete_option('saml_am_email');
            delete_option('saml_am_last_name');
            delete_option('saml_am_default_user_role');
            delete_option('saml_am_role_mapping');
            delete_option('saml_am_group_name');
            delete_option('mo_saml_idp_config_complete');
            delete_option('mo_saml_enable_login_redirect');
            delete_option('mo_saml_allow_wp_signin');
            delete_option('saml_am_account_matcher');
            delete_option('mo_saml_transactionId');
            delete_option('mo_saml_force_authentication');
            delete_option('saml_am_dont_allow_unlisted_user_role');
            delete_option('mo_saml_free_version');
            delete_option('mo_saml_show_mo_idp_message');
            delete_option('mo_saml_guest_log');
            delete_option('mo_saml_guest_enabled');
            delete_option('mo_license_plan_from_feedback');
            delete_option('mo_saml_license_message');
	        delete_option('MO_SAML_REQUEST');
	        delete_option('MO_SAML_RESPONSE');
	        delete_option('MO_SAML_TEST');
            delete_option('mo_saml_encoding_enabled');
            $users = get_users(array());
            foreach ($users as $user) {
                delete_user_meta($user->ID, 'mo_saml_session_index');
                delete_user_meta($user->ID, 'mo_saml_name_id');
            }
        }
        switch_to_blog($original_blog_id);
    }
}
?>