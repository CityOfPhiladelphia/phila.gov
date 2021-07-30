<?php
function mo_saml_show_addons_page(){
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    $addons_displayed = array();
    $addon_desc = array(

        'scim'                          =>  __('Allows real-time user sync (automatic user create, delete, and update) from your Identity Provider such as Azure, Okta, Onelogin into your WordPress site.','miniorange-saml-20-single-sign-on'),
        'page_restriction'              =>  __('Restrict access to WordPress pages/posts based on user roles and their login status, thereby protecting these pages/posts from unauthorized access.','miniorange-saml-20-single-sign-on'),
        'file_prevention'               =>  __('Restrict any kind of media files such as images, audio, videos, documents, etc, and any extension (configurable) such as png, pdf, jpeg, jpg, bmp, gif, etc.','miniorange-saml-20-single-sign-on'),
        'ssologin'                      =>  __('SSO Login Audit tracks all the SSO users and generates detailed reports. The advanced search filters in audit reports makes it easy to find and keep track of your users.','miniorange-saml-20-single-sign-on'),
        'buddypress'                    =>  __('Integrate user information sent by the SAML Identity Provider in SAML Assertion with the BuddyPress profile fields.','miniorange-saml-20-single-sign-on'),
        'learndash'                     =>  __('Allows mapping your users to different LearnDash LMS plugin groups as per their group information sent by configured  SAML Identity Provider.','miniorange-saml-20-single-sign-on'),
        'attribute_based_redirection'   =>  __('Enables you to redirect your users to different pages after they log into your site, based on the attributes sent by your Identity Provider.','miniorange-saml-20-single-sign-on'),
        'ssosession'                    =>  __('Helps you in managing the login session time of your users based on their WordPress roles. Session time for roles can be specified.','miniorange-saml-20-single-sign-on'),
        'fsso'                          =>  __('Allows secure access to the site using various federations such as InCommon, HAKA, HKAF, etc. Users can log into the WordPress site using their university credentials.','miniorange-saml-20-single-sign-on'),
        'memberpress'                   =>  __('Map users to different membership levels created by the MemberPress plugin using the group information sent by your Identity Provider.','miniorange-saml-20-single-sign-on'),
        'wp_members'                    =>  __('Integrate WP-members fields using the attributes sent by your SAML Identity Provider in the SAML Assertion.','miniorange-saml-20-single-sign-on'),
        'woocommerce'                   =>  __('Map WooCommerce checkout page fields using the attributes sent by your IDP. This also allows you to map the users in different WooCommerce roles based on their IDP groups.','miniorange-saml-20-single-sign-on'),
        'guest_login'                   =>  __('Allows users to SSO into your site without creating a user account for them. This is useful when you dont want to manage the user accounts at the WordPress site.','miniorange-saml-20-single-sign-on'),
        'paid_mem_pro'                  =>  __('Map your users to different Paid MembershipPro membership levels as per the group information sent by your Identity Provider.','miniorange-saml-20-single-sign-on'),
        'profile_picture_add_on'        =>  __('Maps raw image data or URL received from your Identity Provider into Gravatar for the user.','miniorange-saml-20-single-sign-on')
        );
    ?>
    <div id="miniorange-addons" style="position:relative;z-index: 1">
        <p id="recommended_section" style="font-size:20px;padding-left:10px;margin-top:5px;display:none"><b><?php _e('Recommended Add-ons for you','miniorange-saml-20-single-sign-on');?>:</b></p>

        <?php
        foreach(mo_saml_options_addons::$RECOMMENDED_ADDONS_PATH as $key => $value){
            if (is_plugin_active($value)) {
                $addon = $key;
                $addons_displayed[$addon] = $addon;
                echo get_addon_tile($addon, mo_saml_options_addons::$ADDON_TITLE[$addon],$addon_desc[$addon], mo_saml_options_addons::$ADDON_URL[$addon], true);
            }
        }
        if(!empty($addons_displayed)){
            ?>
            <script>
                document.getElementById("recommended_section").style.removeProperty("display");
            </script>
            <hr class="recommended_section" style="clear:both;color: blue;visibility: hidden;">
            <br/>
            <?php
        }
        ?>
        <p style="font-size:20px;padding-left:10px;margin-top:5px;"><b><?php _e('Check out all our add-ons','miniorange-saml-20-single-sign-on');?>:</b></p>

        <?php
        foreach ($addon_desc as $key => $value) {
            if(!in_array($key, $addons_displayed))
                echo get_addon_tile($key, mo_saml_options_addons::$ADDON_TITLE[$key],$value, mo_saml_options_addons::$ADDON_URL[$key], false);
        }
        ?>
    </div> <?php
}

function get_addon_tile($addon_name, $addon_title, $addon_desc, $addon_url, $active){

    $icon_url = plugins_url("images/addons_logos/" . $addon_name .".png", __FILE__);

    ?>
    <div class="row">
        <div class="grid_view"> <?php
        if($active){ ?>
            <div class="card text-xs-center"> <?php
        } else { ?>
                <div class="card"> <?php
        } ?>
                    <div class="addon-header">
                        <img src="<?php echo $icon_url ?>" class="addon-img">
                        <div class="addon-heading">
                            <div class="card-title"> <?php echo $addon_title ?> </div>
                        </div>
                    </div>
                    <div style="width:100%;">
                        <p class="card-text"><?php echo $addon_desc ?></p>
                        <div><button class="btn-addon btn-addon-grad" onclick="window.open('<?php echo $addon_url ?>');"><?php _e('Learn More','miniorange-saml-20-single-sign-on');?></button></div>
                    </div>
                </div>
            </div>
        </div>
    <?php
}
?>