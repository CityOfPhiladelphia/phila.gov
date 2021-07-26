<?php
include_once 'Import-export.php';
include_once 'mo_saml_licensing_plans.php';
include 'mo_saml_addons.php';

function mo_saml_register_saml_sso() {
    if ( isset( $_GET['tab'] ) ) {
        $active_tab = $_GET['tab'];
        if($active_tab== 'addons')
        {
            echo "<script type='text/javascript'>
            jQuery(document).ready(function()
            {
                jQuery('#mo_saml_addons_submenu').parent().parent().parent().find('li').removeClass('current');
                jQuery('#mo_saml_addons_submenu').parent().parent().addClass('current');
            });
            </script>";

        }

    }else if ( mo_saml_is_customer_registered_saml() ) {
        $active_tab = 'save';
    } else {
        $active_tab = 'login';
    }
    ?>
    <?php
    if ( ! mo_saml_is_curl_installed() ) {
        ?>
        <p><span style="color: #FF0000; ">(Warning: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP
                    cURL extension</a> is not installed or disabled)</span></p>
        <?php
    }

    if ( ! mo_saml_is_openssl_installed() ) {
        ?>
        <p><span style="color: #FF0000; ">(Warning: <a href="http://php.net/manual/en/openssl.installation.php" target="_blank">PHP
                    openssl extension</a> is not installed or disabled)</span></p>
        <?php
    }

	if ( ! mo_saml_is_dom_installed() ) {
		?>
        <p><span style="color: #FF0000; ">(Warning: PHP
                    dom extension is not installed or disabled)</span></p>
		<?php
	}

    ?>
    <div id="mo_saml_settings" >
        <?php
        $addon_displayed=array();
        foreach(mo_saml_options_addons::$RECOMMENDED_ADDONS_PATH as $key => $value){
            if (is_plugin_active($value)) {
                $addon = $key;
                $addon_displayed[$addon] = $addon;
            }
        }

        if(!empty($addon_displayed)){
            if(! get_option('mo_saml_show_addons_notice')){
                echo '
                <form name="f" method="post" action="" id="mo_saml_addons_notice_form">
                <input type="hidden" name="option" value="mo_saml_addons_message"/>
                <div class="notice notice-info" style="padding-top: 7px; padding-right: 38px;position: relative; height:26px;">
                We have a <a href="';echo admin_url ( 'admin.php?page=mo_saml_settings&tab=addons' ); echo'"><b>good recommendation</b></a> for you. Please check out our ';
                $count=0;
                foreach($addon_displayed as $key => $value){
                    foreach(mo_saml_options_addons::$ADDON_TITLE as $id => $name){
                        if($addon_displayed[$key] == $id){
                            $count+=1;
                            if($count==1){
                                echo '<b>';
                                echo $name;
                                echo '</b>';
                            }else{
                                echo ', <b>';
                                echo $name;
                                echo '</b>';
                            }
                        }    
                    }
                }
                echo' addon.
                    <button type="button" class="notice-dismiss" id="mo_addons_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                    </div>
                    </form>
                    <script>
                        jQuery("#mo_addons_notice_dismiss").click(function () {
                            jQuery("#mo_saml_addons_notice_form").submit();
                        });
                    </script>
                ';
            }
            
        }?>
        
        <form name="f" method="post" id="show_pointers">
            <?php wp_nonce_field("clear_pointers");?>
            <input type="hidden" name="option" value="clear_pointers"/>
            <input type="hidden" name="button_name" id="button_name" />
        </form>

        <form name="f" method="post" id="restart-plugin-tour">
            <?php wp_nonce_field("restart_plugin_tour");?>
            <input type="hidden" name="option" value="restart_plugin_tour"/>
        </form>

        <form name="f" method="post" id="skip-plugin-tour">
            <?php wp_nonce_field("skip_plugin_tour");?>
            <input type="hidden" name="option" value="skip_plugin_tour"/>
        </form>


        <div class="wrap">
            <h1>

                <?php if($active_tab == 'licensing' || (isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_saml_licensing')){ ?>
                <!--<div id="query-response" style="display:none; text-align:center; padding:px; border-radius:5px; border-style: solid; border-color:#2f6062">
                    <h4 style="color:green"> Thanks for getting in touch! You will receive the call details on your email shortly.</h4>
                </div>-->
                <div style="text-align:center;"><?php _e('miniOrange SSO using SAML 2.0', 'miniorange-saml-20-single-sign-on');?></div>
                    <div style="float:left;"><a  class="add-new-h2 add-new-hover" style="font-size: 16px; color: #000;" href="<?php echo mo_saml_add_query_arg( array( 'tab' => 'save' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><span class="dashicons dashicons-arrow-left-alt" style="vertical-align: bottom;"></span> Back To Plugin Configuration</a></div>
                    <br /><div style="text-align:center; color: rgb(233, 125, 104);"><?php _e('You are currently on the Free version of the plugin', 'miniorange-saml-20-single-sign-on'); ?> <span style="font-size: 16px; margin-bottom: 0Px;">
                    <li style="color: dimgray; margin-top: 0px;list-style-type: none;">
                    <a tabindex="0"  style="cursor: pointer;color:dimgray;" id="popoverfree" data-toggle="popover" data-trigger="focus" title="<h3><?php _e('Why should I upgrade to premium plugin?','miniorange-saml-20-single-sign-on'); ?></h3>" data-placement="bottom" data-html="true"
                               data-content="<p><?php _e('You should upgrade to seek the support of our SSO expert team.','miniorange-saml-20-single-sign-on'); ?><br /><br /> <?php _e('Free version does not support attribute mapping, role mapping, single logout features and Multisite Network Installation.','miniorange-saml-20-single-sign-on');?> <br /><br /> <?php _e( 'Premium version support Signed SAML Request and Encrypted Assertion which are recommended from security point of view.','miniorange-saml-20-single-sign-on'); ?><br /><br /> <?php _e('Auto-Redirect to IdP which protect your site with IdP login is a part of premium version of the plugin.','miniorange-saml-20-single-sign-on'); ?><br /><br /> <?php _e('Check the features given in the Licensing Plans for more detail.','miniorange-saml-20-single-sign-on'); ?></p>">
                    <br><?php _e('Why should I upgrade?','miniorange-saml-20-single-sign-on'); ?></a>
                    </li></span></div>
                <?php }else{
                    update_option('mo_license_plan_from_feedback', '');
                    update_option('mo_saml_license_message', '');
                    ?>

                <?php _e('miniOrange SSO using SAML 2.0','miniorange-saml-20-single-sign-on');?>&nbsp
                <a id="license_upgrade" class="add-new-h2 add-new-hover" style="background-color: orange !important; border-color: orange; font-size: 16px; color: #000;" href="<?php echo add_query_arg( array( 'tab' => 'licensing' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><?php _e('Premium Plans | Upgrade Now','miniorange-saml-20-single-sign-on'); ?></a>
                <a class="add-new-h2" href="https://faq.miniorange.com/kb/saml-single-sign-on/" target="_blank"><?php _e('FAQs','miniorange-saml-20-single-sign-on');?></a>
                <a class="add-new-h2" href="https://forum.miniorange.com/" target="_blank"><?php _e('Ask questions on our forum','miniorange-saml-20-single-sign-on');?></a>

                <span style="position: relative; float: right;background-color:white;border-radius:4px;" id="miniorange-plugin-restart-tour">
                     <button type="button"  id="entire-plugin-tour" class="button button-primary button-large" onclick="restart_tours(this)"><i class="icon-refresh"></i><?php _e('  Restart Plugin Tour','miniorange-saml-20-single-sign-on');?></button>
                </span>
                <?php } ?>

            </h1>

        </div>



<input type="hidden" value="<?php echo get_option("mo_is_new_user")?>" id="mo_modal_value">

<div id="getting-started" class="modal">

    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">
            <span style="float: right;cursor: pointer;padding-top: 20px" onclick="skip_plugin_tour();" ><i class="dashicons dashicons-dismiss" ></i></span>
            <div class="modal-header">
                <h2 class="modal-title" style="text-align: center; font-size: 40px; color: #2980b9"><?php esc_html_e('Let\'s get started!','miniorange-saml-20-single-sign-on');?></h2>
            </div>

            <div class="modal-body" style="
    max-height: calc(100vh - 210px);
    overflow-y: auto;
    overflow-x: hidden;
">

                <p style="font-size: medium"><?php _e('Hey, Thank you for installing <b style="color: #E85700">miniOrange SSO using SAML 2.0 plugin','miniorange-saml-20-single-sign-on');?></b>.</p>
                <p style="font-size: medium"><?php _e('We support all SAML 2.0 compliant Identity Providers. ','miniorange-saml-20-single-sign-on');

                _e('Please find some of the well-known <b>IdP configuration guides</b> below.','miniorange-saml-20-single-sign-on');
                _e(' If you do not find your IDP guide here, do not worry! mail us at <a href="mailto:info@xecurify.com">info@xecurify.com</a>','miniorange-saml-20-single-sign-on');?> </p>
                <p style="font-size: medium"><?php _e('Make sure to check out the list of supported add-ons to increase the functionality of your WordPress site.','miniorange-saml-20-single-sign-on');?></p>

                <?php
                $index=0;
                    foreach (mo_saml_options_plugin_idp::$IDP_GUIDES as $key=>$value){

                        $url_string = 'https://plugins.miniorange.com/saml-single-sign-on-sso-wordpress-using-'.trim($value);

                        if($index%5===0){?>
                            <div class="idp-guides-btns">
                            <?php } ?>
                         <button class="guide-btn" onclick="window.open('<?php echo $url_string?>','_blank')"><img class="idp-guides-logo <?php echo $key?>" src="<?php echo plugin_dir_url( __FILE__ ) . 'images'.DIRECTORY_SEPARATOR.'idp-guides-logos'.DIRECTORY_SEPARATOR.$value.'.png'; ?>" /><?php echo $key?></button>
                        <?php

                        if($index%5===4){
                            echo '</div>';
                            $index=-1;
                        }
                        $index++;
                    }

                ?>
                </div>
                <p style="font-size: large;text-align: center;font-weight: 500;"><?php _e('Take a quick tour of setting up the plugin with ADFS','miniorange-saml-20-single-sign-on');?> <i style="font-size: small">(<?php _e('Press Esc to skip','miniorange-saml-20-single-sign-on');?>)</i><br/><br/></p>
            </div></div>
            <div class="modal-footer" style="height:275px;">
                <button type="button" style="margin-right:5%; width:175px;" class="button button-primary button-large modal-button" id="start-plugin-tour" onclick="jQuery('#restart-plugin-tour').submit();"><?php _e('Start tour','miniorange-saml-20-single-sign-on');?></button>
                <button type="button" style="width:175px;" class="button button-primary button-large modal-button" id="skip-plugin-tour" onclick="skip_plugin_tour()" ><?php _e('Skip tour','miniorange-saml-20-single-sign-on');?></button><br/><br/>
            </div>

        </div>

    </div>

</div>

<script>

let getting_started_modal = document.getElementById("getting-started");

let entire_plugin_tour = document.getElementById("entire-plugin-tour");

let span = document.getElementsByClassName("close1")[0];

document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        skip_plugin_tour();
    }
};

function skip_plugin_tour(){

    let data = {
        action: 'skip_entire_plugin_tour',
    };

    jQuery.post(ajaxurl, data, function(response) {
        getting_started_modal.style.display = "none";
    });

}


if(entire_plugin_tour!=null){
    entire_plugin_tour.onclick = function() {
        getting_started_modal.style.display = "block";
    }
}


function restart_tours(button){

    jQuery('#button_name').val(button.id);
    jQuery('#show_pointers').submit();

}

document.addEventListener("DOMContentLoaded", function()
{
    let modal_value = document.getElementById("mo_modal_value");
    if(modal_value.value==='')
    {
        getting_started_modal.style.display = "block";

    }
});


</script>

        <div class="miniorange_container" id="container">

                    <?php if($active_tab != 'licensing' && !(isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_saml_licensing')) { ?>
                <table style="width:100%;">
                <tr>
                    <h2 class="nav-tab-wrapper">
                        <form id="dismiss_pointers" method="post" action="">
                            <?php wp_nonce_field('dismiss_pointers');?>
                            <input type="hidden" name="option" value="dismiss_pointers"/>
                        </form>
                        <a id="sp-setup-tab" class="nav-tab <?php echo $active_tab == 'save' ? 'nav-tab-active' : ''; ?>"
                           href="<?php echo add_query_arg( array( 'tab' => 'save' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><?php _e('Service Provider Setup','miniorange-saml-20-single-sign-on');?></a>
                        <a id="sp-meta-tab" class="nav-tab <?php echo $active_tab == 'config' ? 'nav-tab-active' : ''; ?>"
                           href="<?php echo add_query_arg( array( 'tab' => 'config' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><?php _e('Service Provider Metadata','miniorange-saml-20-single-sign-on');?></a>
                        <a id="attr-role-tab" class="nav-tab <?php echo $active_tab == 'opt' ? 'nav-tab-active' : ''; ?>"
                           href="<?php echo add_query_arg( array( 'tab' => 'opt' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><?php _e('Attribute/Role Mapping','miniorange-saml-20-single-sign-on');?></a>

                        <a id="redir-sso-tab" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"
                           href="<?php echo add_query_arg( array( 'tab' => 'general' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><?php _e('Redirection & SSO Links','miniorange-saml-20-single-sign-on');?></a>
                        <a id="addon-tab" class="nav-tab <?php echo $active_tab == 'addons' ? 'nav-tab-active' : ''; ?>"
                           href="<?php echo add_query_arg( array( 'tab' => 'addons' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><?php _e('Add-Ons','miniorange-saml-20-single-sign-on');?></a>
                        <a class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>"
                           href="<?php echo add_query_arg( array( 'tab' => 'support' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><?php _e('Demo Request','miniorange-saml-20-single-sign-on');?></a>
                        <a class="nav-tab <?php echo $active_tab == 'account-setup' ? 'nav-tab-active' : ''; ?>"
                           href="<?php echo add_query_arg( array( 'tab' => 'account-setup' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><?php _e('Account Setup','miniorange-saml-20-single-sign-on');?></a>

                    </h2>
                    <td style="vertical-align:top;width:65%;
                     <?php
                        if($active_tab == 'addons'){
                            echo "background-color:#FFFFFF;border:1px solid #CCCCCC; padding:10px 20px 30px 10px";
                        }
                     ?>
                    ">
                        <?php
                        if ( $active_tab == 'save' ) {
                            ?>
                            <div id="save_tab" style="display: block;">
                                 <?php mo_saml_apps_config_saml();?>
                            </div>
                            <div id="config_tab" style="display:none">
                                <?php mo_saml_configuration_steps();?>
                            </div>
                            <div id="opt_tab" style="display:none">
                                 <?php mo_saml_save_optional_config();?>
                             </div>

                            <div id="redir_sso_tab" style="display:none">
                                 <?php mo_saml_general_login_page();?>
                             </div>
                            <div id="addons_tab" style="display:none">
                                 <?php mo_saml_show_addons_page();?>
                             </div>
                            <?php
                        }
                        else if ( $active_tab == 'opt' ) {
                            mo_saml_save_optional_config();
                        }
                        else if ( $active_tab == 'config' ) {
                            mo_saml_configuration_steps();
                        }
                        else if ( $active_tab == 'general' ) {
                            mo_saml_general_login_page();
                        }
                        else if($active_tab == 'addons'){
                            mo_saml_show_addons_page();
                        }
                        else if($active_tab == 'support'){
                            miniorange_demo_request_saml();
                        }
                        else if($active_tab == 'account-setup'){
                            if(mo_saml_is_customer_registered_saml(false)){
                                mo_saml_show_customer_details();
                            }else{
                                if ( get_option( 'mo_saml_verify_customer' ) == 'true' ) {
                                    mo_saml_show_verify_password_page_saml();
                                }else{
                                    mo_saml_show_new_registration_page_saml();
                                }
                            }
                        }
                        else {
                                mo_saml_apps_config_saml();
                        }
                        ?>
                    </td>
                    <td style="vertical-align:top;padding-left:1%;" id="support-form">
                        <?php 
                        if($active_tab === 'opt' && get_option('mo_saml_test_config_attrs')){
                          echo  mo_saml_display_attrs_list();
                        } else if($active_tab==='support'){
                            miniorange_support_saml($active_tab,false,false);
                        }else if($active_tab!=='save'){
                            miniorange_support_saml($active_tab,true,false);
                        }
                        else{
                            miniorange_support_saml($active_tab);
                        }?>
                    </td>

                </tr>
                </table>
                    <?php }else if ( $active_tab == 'licensing' || 	(isset($_REQUEST['page']) && $_REQUEST['page'] == 'mo_saml_licensing')){ ?>

                            <?php
                            //mo_saml_show_pricing_page();
                            mo_saml_show_licensing_page();

                        }?>


        </div>
        <div class='overlay' id="overlay" hidden></div>
        <script>
            jQuery("#mo_saml_mo_idp").click(function () {
                jQuery("#mo_saml_mo_idp_form").submit();
            });

        </script>

        <?php
        }

        function mo_saml_is_curl_installed() {
            if ( in_array( 'curl', get_loaded_extensions() ) ) {
                return 1;
            } else {
                return 0;
            }
        }

        function mo_saml_is_openssl_installed() {

            if ( in_array( 'openssl', get_loaded_extensions() ) ) {
                return 1;
            } else {
                return 0;
            }
        }

        function mo_saml_is_dom_installed(){

	        if ( in_array( 'dom', get_loaded_extensions() ) ) {
		        return 1;
	        } else {
		        return 0;
	        }
        }

        function mo_saml_is_iconv_installed(){

            if ( in_array( 'iconv', get_loaded_extensions() ) ) {
                return 1;
            } else {
                return 0;
            }
        }

        function mo_saml_get_attribute_mapping_url(){

            return add_query_arg( array('tab' => 'opt'), $_SERVER['REQUEST_URI'] );
        }

        function mo_saml_get_service_provider_url(){

                return add_query_arg( array('tab' => 'save'), $_SERVER['REQUEST_URI'] );


        }

        function mo_saml_show_customer_details(){
            ?>
            <div class="mo_saml_table_layout" >
                <h2><?php _e('Thank you for registering with miniOrange.','miniorange-saml-20-single-sign-on');?></h2>

                <table border="1"
                   style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:85%">
                <tr>
                    <td style="width:45%; padding: 10px;"><?php _e('miniOrange Account Email','miniorange-saml-20-single-sign-on');?></td>
                    <td style="width:55%; padding: 10px;"><?php echo get_option( 'mo_saml_admin_email' ); ?></td>
                </tr>
                <tr>
                    <td style="width:45%; padding: 10px;"><?php _e('Customer ID','miniorange-saml-20-single-sign-on');?></td>
                    <td style="width:55%; padding: 10px;"><?php echo get_option( 'mo_saml_admin_customer_key' ) ?></td>
                </tr>
                </table>
                <br /><br />

            <table>
            <tr>
            <td>
            <form name="f1" method="post" action="" id="mo_saml_goto_login_form">
            <?php wp_nonce_field("change_miniorange");?>
                <input type="hidden" value="change_miniorange" name="option"/>
                <input type="submit" value="<?php _e('Change Email Address','miniorange-saml-20-single-sign-on');?>" class="button button-primary button-large"/>
            </form>
            </td><td>
            <a href="<?php echo add_query_arg( array( 'tab' => 'licensing' ), htmlentities( $_SERVER['REQUEST_URI'] ) ); ?>"><input type="button" class="button button-primary button-large" value="<?php _e('Check Licensing Plans','miniorange-saml-20-single-sign-on');?>"/></a>
            </td>
            </tr>
            </table>

                        <br />
            </div>

            <?php
        }

        function mo_saml_show_new_registration_page_saml() {
            update_option( 'mo_saml_new_registration', 'true' );

            ?>
            <form name="f" method="post" action="">
                <input type="hidden" name="option" value="mo_saml_register_customer"/>
                <?php wp_nonce_field("mo_saml_register_customer");?>
                <div class="mo_saml_table_layout">


                    <h2><?php _e('Register with miniOrange','miniorange-saml-20-single-sign-on');?></h2>

                    <div id="panel1">
                        <p style="font-size:14px;"><b><?php _e('Why should I register?','miniorange-saml-20-single-sign-on');?> </b></p>
                        <div id="help_register_desc" style="background: aliceblue; padding: 10px 10px 10px 10px; border-radius: 10px;">
                            <?php _e('You should register so that in case you need help, we can help you with step by step instructions. We support all known IdPs - ADFS, Okta, Salesforce, Shibboleth, SimpleSAMLphp, OpenAM, Centrify, Ping, RSA, IBM, Oracle, OneLogin, Bitium, WSO2 etc.','miniorange-saml-20-single-sign-on');?>
                                <b><?php _e('You will also need a miniOrange account to upgrade to the premium version of the plugins.','miniorange-saml-20-single-sign-on');?></b><?php _e(' We do not store any information except the email that you will use to register with us.','miniorange-saml-20-single-sign-on');?>
                        </div>
                        </p>
                        <table class="mo_saml_settings_table">
                            <tr>
                                <td><b><font color="#FF0000">*</font><?php _e('Email','miniorange-saml-20-single-sign-on');?>:</b></td>
                                <td><input class="mo_saml_table_textbox" type="email" name="email"
                                           required placeholder="person@example.com"
                                           value="<?php echo ( get_option( 'mo_saml_admin_email' ) == '' ) ? get_option( 'admin_email' ) : get_option( 'mo_saml_admin_email' ); ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><b><font color="#FF0000">*</font><?php _e('Password','miniorange-saml-20-single-sign-on');?>:</b></td>
                                <td><input class="mo_saml_table_textbox" required type="password"
                                           name="password" placeholder="<?php _e('Choose your password (Min. length 6)','miniorange-saml-20-single-sign-on');?>"
                                           minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
                                           title="<?php _e('Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present.','miniorange-saml-20-single-sign-on');?>"
                                           /></td>
                            </tr>
                            <tr>
                                <td><b><font color="#FF0000">*</font><?php _e('Confirm Password','miniorange-saml-20-single-sign-on');?>:</b></td>
                                <td><input class="mo_saml_table_textbox" required type="password"
                                           name="confirmPassword" placeholder="<?php _e('Confirm your password','miniorange-saml-20-single-sign-on');?>"
                                           minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
                                           title="<?php _e('Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present.','miniorange-saml-20-single-sign-on');?>"

                                           /></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td><br><input type="submit" name="submit" value="<?php _e('Register','miniorange-saml-20-single-sign-on');?>"
                                               class="button button-primary button-large"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                               <input type="button" name="mo_saml_goto_login" id="mo_saml_goto_login"
                                           value="<?php _e('Already have an account?','miniorange-saml-20-single-sign-on');?>" class="button button-primary button-large"/>&nbsp;&nbsp;

                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
            <form name="f1" method="post" action="" id="mo_saml_goto_login_form">
            <?php wp_nonce_field("mo_saml_goto_login");?>
                <input type="hidden" name="option" value="mo_saml_goto_login"/>
            </form>

            <script>
                jQuery('#mo_saml_goto_login').click(function () {
                    jQuery('#mo_saml_goto_login_form').submit();
                });
            </script>
            <?php
        }


        function mo_saml_show_verify_password_page_saml() {
            ?>
            <form name="f" method="post" action="">
            <?php wp_nonce_field("mo_saml_verify_customer");?>
                <input type="hidden" name="option" value="mo_saml_verify_customer"/>
                <div class="mo_saml_table_layout">
                    <div id="toggle1" class="panel_toggle">
                        <h3><?php _e('Login with miniOrange','miniorange-saml-20-single-sign-on');?></h3>
                    </div>
                    <div id="panel1">
                        <p><b><?php _e('It seems you already have an account with miniOrange. Please enter your miniOrange email and password.','miniorange-saml-20-single-sign-on');?><br/> <a target="_blank"
                          href="https://auth.miniorange.com/moas/idp/resetpassword"><?php _e('Click here if you forgot your password?','miniorange-saml-20-single-sign-on');?></a></b></p>
                        <br/>
                        <table class="mo_saml_settings_table">
                            <tr>
                                <td><b><font color="#FF0000">*</font><?php _e('Email','miniorange-saml-20-single-sign-on');?>:</b></td>
                                <td><input class="mo_saml_table_textbox" type="email" name="email"
                                           required placeholder="person@example.com"
                                           value="<?php echo get_option( 'mo_saml_admin_email' ); ?>"/></td>
                            </tr>
                            <tr>
                                <td><b><font color="#FF0000">*</font><?php _e('Password','miniorange-saml-20-single-sign-on');?>:</b></td>
                                <td><input class="mo_saml_table_textbox" required type="password"
                                           name="password" placeholder="<?php _e('Enter your password','miniorange-saml-20-single-sign-on');?>"
                                           minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
                                           title="<?php _e('Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present.','miniorange-saml-20-single-sign-on');?>"

                                           /></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <input type="submit" name="submit" value="<?php _e('Login','miniorange-saml-20-single-sign-on');?>"
                                           class="button button-primary button-large"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="button" name="mo_saml_goback" id="mo_saml_goback" value="<?php _e('Back','miniorange-saml-20-single-sign-on');?>"
                                           class="button button-primary button-large"/>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
            <form name="f" method="post" action="" id="mo_saml_goback_form">
                <?php wp_nonce_field("mo_saml_go_back")?>
                <input type="hidden" name="option" value="mo_saml_go_back"/>
            </form>
            <form name="f" method="post" action="" id="mo_saml_forgotpassword_form">
            <?php wp_nonce_field("mo_saml_forgot_password_form_option");?>
                <input type="hidden" name="option" value="mo_saml_forgot_password_form_option"/>
            </form>
            <script>
                jQuery('#mo_saml_goback').click(function () {
                    jQuery('#mo_saml_goback_form').submit();
                });
                jQuery("a[href=\"#mo_saml_forgot_password_link\"]").click(function () {
                    jQuery('#mo_saml_forgotpassword_form').submit();
                });
            </script>
            <?php
        }


function mo_saml_general_login_page() {

    ?>
    <?php if ( mo_saml_is_customer_registered_saml() ) { ?>
        <div style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 2% 0px 2%;position: relative" id="minorange-use-widget">

            <h3><b><?php _e('Option 1: Use a Widget','miniorange-saml-20-single-sign-on');?></b><sup style="font-size: 12px;">[<?php _e('Available in current version of the plugin','miniorange-saml-20-single-sign-on');?>]</sup>
            <span style="position: relative; float: right;padding-left: 13px;padding-right:13px;background-color:white;border-radius:4px;" id="miniorange-redirection-sso-restart-tour">
             <button type="button"  id="redirection-sso-links" class="button button-primary button-large" onclick="restart_tours(this)"><i class="icon-refresh"></i>  <?php _e('Take Tab-Tour','miniorange-saml-20-single-sign-on');?></button>

            </span>

            </h3>
            <div style="margin:2% 0 2% 17px;">
                <p><?php _e('Add the SSO Widget by following the instructions below. This will add the SSO link on your site.','miniorange-saml-20-single-sign-on');?></p>
                <div id="mo_saml_add_widget_steps">
                    <ol>
                        <li><?php _e('Go to Appearances','miniorange-saml-20-single-sign-on');?> > <a href="<?php echo get_admin_url().'widgets.php';?>"><?php _e('Widgets','miniorange-saml-20-single-sign-on');?>.</a></li>
                        <li><?php echo sprintf(__('Select "Login with %s','miniorange-saml-20-single-sign-on'),get_option( 'saml_identity_name' ));?>". <?php _e('Drag and drop to your favourite location and save.','miniorange-saml-20-single-sign-on');?>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <br/>
        <div style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 2% 0px 2%;position: relative" id="miniorange-auto-redirect">
            <h3><?php _e('Option 2: Auto-Redirection from site','miniorange-saml-20-single-sign-on');?><sup style="font-size: 12px;">
            [<a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><?php _e('Available in Standard, Premium, Enterprise and All-Inclusive plans','miniorange-saml-20-single-sign-on');?></a>]</sup></h3>
            <span><?php _e('1. Select this option if you want to restrict your site to only logged in users.','miniorange-saml-20-single-sign-on');
            _e(' Selecting this option will redirect the users to your IdP if logged in session is not found.','miniorange-saml-20-single-sign-on');?></span>
            <br /><br/>
            <label class="switch">
            <input type="checkbox" style="background: #DCDAD1;" disabled/>
            <span class="slider round"></span>
            </label><span style="padding-left:5px"><b><span style="color: red;">*</span><?php _e('Redirect to IdP if user not logged in','miniorange-saml-20-single-sign-on');?>  &nbsp; [<?php _e('PROTECT COMPLETE SITE','miniorange-saml-20-single-sign-on');?>]</b></span>
            <br/>
            <br />
            <span><?php _e('2. It will force user to provide credentials on your IdP on each login attempt even if the user is already logged in to IdP.','miniorange-saml-20-single-sign-on');
            _e(' This option may require some additional setting in your IdP to force it depending on your Identity Provider.','miniorange-saml-20-single-sign-on');?></span>
            <br /><br />
            <label class="switch">
            <input type="checkbox" style="background: #DCDAD1;" disabled>
            <span class="slider round"></span>
            </label><span style="padding-left:5px"><b><span style="color: red;">*</span><?php _e('Force authentication with your IdP on each login attempt','miniorange-saml-20-single-sign-on');?></b></span>
           <br />
            <br/>
        </div>
        <div style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 2% 0px 2%;position: relative" id="miniorange-auto-redirect-login-page">
         <h3><?php _e('Option 3: Auto-Redirection from WordPress Login','miniorange-saml-20-single-sign-on');?><sup style="font-size: 12px;">
         [<a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><?php _e('Available in Standard, Premium, Enterprise and All-Inclusive plans','miniorange-saml-20-single-sign-on');?></a>]</sup></h3>
            <span><?php _e('1. Select this option if you want the users visiting any of the following URLs to get redirected to your configured IdP for authentication','miniorange-saml-20-single-sign-on');?>:</span>
                <br/><code><b><?php echo wp_login_url(); ?></b></code> <?php _e('or','miniorange-saml-20-single-sign-on');?>
                <code><b><?php echo admin_url(); ?></b></code><br /><br/>
            <label class="switch">
            <input type="checkbox" style="background: #DCDAD1;" disabled>
            <span class="slider round"></span>
					</label><span style="padding-left:5px"><b><span style="color: red;">*</span> <?php _e('Redirect to IdP from WordPress Login Page','miniorange-saml-20-single-sign-on');?></b></span>
            <br /><br/>

            <span><?php _e('2. Select this option to enable backdoor login if auto-redirect from WordPress Login is enabled.','miniorange-saml-20-single-sign-on');?></span>
            <br/><br/>
            <label class="switch">
            <input type="checkbox" style="background: #DCDAD1;" disabled>
            <span class="slider round"></span>
					</label><span style="padding-left:5px"><b>
                        <span style="color: red;">*</span> <?php _e('Checking this option creates a backdoor to login to your Website using WordPress credentials incase you get locked out of your IdP','miniorange-saml-20-single-sign-on');?></b></span><br/>
                        <br/><i>(<?php _e('Note down this URL','miniorange-saml-20-single-sign-on');?>: <code><b><?php echo site_url(); ?>/wp-login.php?saml_sso=false</b></code> )</i>
            <br /><br />
        </div>
        <div style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 2% 0px 2%;" >
            <div style="background-color:#FFFFFF;position: relative" id="miniorange-short-code">
            <h3><?php _e('Option 4: Use a ShortCode','miniorange-saml-20-single-sign-on');?><sup style="font-size: 12px;">
            [<a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><?php _e('Available in Standard, Premium, Enterprise and All-Inclusive plans','miniorange-saml-20-single-sign-on');?></a>]</sup></h3>
                        <label class="switch">
                        <input type="checkbox" style="background: #DCDAD1;"
                           disabled <?php if ( ! mo_saml_is_sp_configured() )
                        echo 'disabled title="' . __('Disabled. Configure your Service Provider','miniorange-saml-20-single-sign-on') . '"' ?> value="true">
                         <span class="slider round"></span>
					</label><span style="padding-left:5px"><b><span
                            style="color: red">*</span><?php _e('Check this option if you want to add a shortcode to your page','miniorange-saml-20-single-sign-on');?></b></span>
                    <br/>
            </div>
            <div style="display:block;text-align:center;margin:2%;">
                <input type="button"
                       onclick="window.location.href='<?php echo wp_logout_url( site_url() ); ?>'" <?php if ( ! mo_saml_is_sp_configured() )
                    echo 'disabled title="' . __('Disabled. Configure your Service Provider','miniorange-saml-20-single-sign-on') . '"' ?>
                       class="button button-primary button-large" value="<?php _e('Log Out and Test','miniorange-saml-20-single-sign-on');?>">
            </div>
            <?php if ( get_option( 'mo_saml_free_version' ) ) { ?>
                <span style="color:red;">*</span>
                <a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><b><?php _e('These options are configurable in the Standard, Premium, Enterprise and All-Inclusive version of the plugin.','miniorange-saml-20-single-sign-on');?></b></a></h3>
                <br/><br/>
            <?php } ?>
        </div>
        <br/>
    <?php }
}

function mo_saml_configuration_steps() {
    $sp_base_url = site_url();
    $sp_entity_id = get_option('mo_saml_sp_entity_id')?:$sp_base_url.'/wp-content/plugins/miniorange-saml-20-single-sign-on/';
    ?>
    <!-- <form  name="saml_form_am" method="post" action="" id="mo_saml_idp_config">-->
    <input type="hidden" name="option" value="mo_saml_idp_config"/>
    <div id="instructions_idp"></div>
    <table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:2%;padding-top: 0px">
        <tr>
                    <td colspan="2" style="padding: 13px;padding-top: 0px;padding-bottom: 0px">
                        <h3><?php _e('Gather Metadata for IDP','miniorange-saml-20-single-sign-on');?> &nbsp &nbsp
             <span style="padding-left:13px;padding-right:13px; background-color: white;position: relative; float: right;border-radius:2px;" id="identity-provider-restart-tour">
                <button type="button" id="identity-provider-setup" class="button button-primary button-large" onclick="restart_tours(this)" ><i class="icon-refresh"></i>  <?php _e('Take Tab-tour','miniorange-saml-20-single-sign-on');?></button>
             </span>
                </h3></td>
                </tr>

                <tr>
                    <td colspan="4">
                        <hr>
                    </td>
                </tr>
                <tr>
                <td colspan="4">
                <h3><?php _e('Service Provider Endpoints','miniorange-saml-20-single-sign-on');?></h3>
                <form width="98%" border="0" method="post" id="mo_saml_update_idp_settings_form" action="">
		<?php wp_nonce_field('mo_saml_update_idp_settings_option');?>
			<input type="hidden" name="option" value="mo_saml_update_idp_settings_option" />
				<table width="98%">
                <tr>
                <td>SP EntityID / Issuer:</td>
						<td><input type="text" name="mo_saml_sp_entity_id" placeholder="<?php _e('Enter Service Provider Entity ID','miniorange-saml-20-single-sign-on');?>" style="width: 95%;" value="<?php echo $sp_entity_id; ?>" required /></td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                    <i><b><?php _e('Note:','miniorange-saml-20-single-sign-on');?></b> <?php _e('If you have already shared the below URLs or Metadata with your IdP, do <b>NOT</b> change SP EntityID. It might break your existing login flow.','miniorange-saml-20-single-sign-on');?></i>
                    </td>
                </tr>
                <tr>
                    <td>SP Base URL:</td>
					<td><input type="text" placeholder="<?php _e('You site base URL','miniorange-saml-20-single-sign-on');?>" style="width: 95%;background: #DCDAD1;" value="<?php echo $sp_base_url; ?>" disabled /></td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td colspan="2"><span style="color:red;">*</span>
                        <a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><b><?php _e('Configurable ACS URL / SP Base URL available in the Paid versions of the plugin.','miniorange-saml-20-single-sign-on');?></b></a>
                    </td>
                </tr>		
                    <td colspan="2" style="text-align: center"><br><input type="submit" name="submit" style="width:100px;" value="<?php _e('Update','miniorange-saml-20-single-sign-on');?>" class="button button-primary button-large"/></td>
                </tr>
                </table></form>



        <tr>
            <td colspan="2">

                <h3>
                <?php
                echo '
                <div id="metadata_url" style="position:relative;background: white;border-radius:5px;padding-left: 13px;">';
                 echo '<p><b>' . __('Provide this metadata URL to your Identity Provider or download the .xml file to upload it in your idp','miniorange-saml-20-single-sign-on') . ':</b></p>
            <p>' . __('Metadata URL','miniorange-saml-20-single-sign-on') . ':   <code style="margin-right:20px"><b><a id="sp_metadata_url" target="_blank" href="'.$sp_base_url.'/?option=mosaml_metadata">'. $sp_base_url.'/?option=mosaml_metadata</a></b>
            </code>
            <i class="icon-copy mo_copy copytooltip" onclick="copyToClipboard(this, \'#sp_metadata_url\', \'#metadata_url_copy\');" ><span id="metadata_url_copy" class="copytooltiptext">' . __('Copy to Clipboard','miniorange-saml-20-single-sign-on') . '</span></i>
            </p>
            <p>' . __('Metadata XML File','miniorange-saml-20-single-sign-on') . ': &nbsp;&nbsp;<a href="#" class="button button-primary" onclick="document.forms[\'mo_saml_download_metadata\'].submit();" >' . __('Download','miniorange-saml-20-single-sign-on') . '</a></p></div>';

            echo '<p style="text-align: center;font-size: 13pt;font-weight: bold;">' . __('OR','miniorange-saml-20-single-sign-on') . '</p>';?>

                <div style="font-size: 13px;position: relative;background-color: white;border-radius: 5px;padding-left: 13px;padding-right:13px;padding-bottom:13px;" id="metadata_manual">
                    <h4><?php _e('Link to Configure the Plug in','miniorange-saml-20-single-sign-on');?>:
                        <a href="https://plugins.miniorange.com/wordpress-saml-guides" target='_blank'><?php _e('Click Here to see the Guide for Configuring the plugin','miniorange-saml-20-single-sign-on');?></b></a><h4><?php _e('You will need the following information to configure your IdP. Copy it and keep it handy','miniorange-saml-20-single-sign-on');?>:</h4>
                        <table border="1"
                               style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px; border-collapse: collapse; width:98%">

                            <tr>
                                <td style="width:40%; padding: 15px;"><b><?php _e('SP-EntityID / Issuer','miniorange-saml-20-single-sign-on'); ?></b></td>

                                    <td style="width:60%; padding: 15px;font-weight: 400"><table width="100%"><tr><td><span id="entity_id"><?php echo $sp_entity_id; ?></span></td>
                                    <td><i class="icon-copy mo_copy copytooltip" style="float:right;" onclick="copyToClipboard(this, '#entity_id', '#entity_id_copy');"><span id="entity_id_copy" class="copytooltiptext"><?php _e('Copy to Clipboard','miniorange-saml-20-single-sign-on');?></span></i></td></tr></table>
                                    </td>

                            </tr>


                            <tr>
                                <td style="width:40%; padding: 15px;"><b><?php _e('ACS (AssertionConsumerService) URL','miniorange-saml-20-single-sign-on');?></b></td>

                                    <td style="width:60%;  padding: 15px;font-weight: 400"><table width="100%"><tr><td><span id="base_url"><?php echo site_url() . '/' ?></span></td>
                                    <td><i class="icon-copy mo_copy copytooltip" style="float:right;" onclick="copyToClipboard(this, '#base_url', '#base_url_copy');"><span id="base_url_copy" class="copytooltiptext"><?php _e('Copy to Clipboard','miniorange-saml-20-single-sign-on');?></span></i></td></tr></table>
                                    </td>

                            </tr>


                            <tr>
                                <td style="width:40%; padding: 15px;"><b><?php _e('Audience URI','miniorange-saml-20-single-sign-on');?></b></td>

                                    <td style="width:60%; padding: 15px;font-weight: 400"><table width="100%"><tr><td><span id="audience"><?php echo $sp_entity_id; ?></span></td>
                                    <td><i class="icon-copy mo_copy copytooltip" style="float:right;" onclick="copyToClipboard(this, '#audience','#audience_copy');"><span id="audience_copy" class="copytooltiptext"><?php _e('Copy to Clipboard','miniorange-saml-20-single-sign-on');?></span></i></td></tr></table>
                                    </td>

                            </tr>


                            <tr>
                                <td style="width:40%; padding: 15px;"><b><?php _e('NameID format','miniorange-saml-20-single-sign-on');?></b></td>

                                    <td style="width:60%; padding: 15px;font-weight: 400"><table width="100%"><tr><td><span id="nameid">
                                        urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress
                                    </span></td>
                                    <td><i class="icon-copy mo_copy copytooltip" style="float:right;" onclick="copyToClipboard(this, '#nameid', '#nameid_copy');"><span id="nameid_copy" class="copytooltiptext"><?php _e('Copy to Clipboard','miniorange-saml-20-single-sign-on');?></span></i></td></tr></table>
                                    </td>

                            </tr>


                            <tr>
                                <td style="width:40%; padding: 15px;"><b><?php _e('Recipient URL','miniorange-saml-20-single-sign-on');?></b></td>

                                    <td style="width:60%;  padding: 15px;font-weight: 400"><table width="100%"><tr><td><span id="recipient"><?php echo site_url() . '/' ?></span></td>
                                    <td><i class="icon-copy mo_copy copytooltip" style="float:right;" onclick="copyToClipboard(this, '#recipient','#recipient_copy');"><span id="recipient_copy" class="copytooltiptext"><?php _e('Copy to Clipboard','miniorange-saml-20-single-sign-on');?></span></i></td></tr></table>
                                    </td>

                            </tr>


                            <tr>
                                <td style="width:40%; padding: 15px;font-weight: 400"><b><?php _e('Destination URL','miniorange-saml-20-single-sign-on');?></b></td>

                                    <td style="width:60%;  padding: 15px;font-weight: 400"><table width="100%"><tr><td><span id="destination"><?php echo site_url() . '/' ?></span></td>
                                    <td><i class="icon-copy mo_copy copytooltip" style="float:right;" onclick="copyToClipboard(this, '#destination','#destination_copy');"><span id="destination_copy" class="copytooltiptext"><?php _e('Copy to Clipboard','miniorange-saml-20-single-sign-on');?></span></i></td></tr></table>
                                    </td>

                            </tr>


                            <?php if ( ! get_option( 'mo_saml_free_version' ) ) { ?>
                                <tr>
                                    <td style="width:40%; padding: 15px;"><b><?php _e('Default Relay State (Optional)','miniorange-saml-20-single-sign-on');?></b></td>

                                        <td style="width:60%;  padding: 15px;font-weight: 400"><table width="100%"><tr><td><span id="relaystate"><?php echo site_url() . '/' ?></span></td>
                                        <td><i class="icon-copy mo_copy copytooltip"  style="float:right;" onclick="copyToClipboard(this, '#relaystate', '#relaystate_copy');"><span id="relaystate_copy" class="copytooltiptext"><?php _e('Copy to Clipboard','miniorange-saml-20-single-sign-on');?></span></i></td></tr></table>
                                        </td>

                                </tr>

                                    <tr>
                                        <td style="width:40%; padding: 15px;font-weight: 400"><b><?php _e('Certificate (Optional)','miniorange-saml-20-single-sign-on');?></b></td>
                                        <?php if ( ! mo_saml_is_customer_registered_saml() ) { ?>
                                            <td style="width:60%;  padding: 15px;"><?php _e('Download','miniorange-saml-20-single-sign-on');?> <i>(<?php _e('Register to download the certificate','miniorange-saml-20-single-sign-on');?></i></td>
                                        <?php } else { ?>
                                            <td style="width:60%;  padding: 15px;font-weight: 400"><a
                                                        href="<?php echo plugins_url( 'resources/sp-certificate.crt', __FILE__ ); ?>"><?php _e('Download','miniorange-saml-20-single-sign-on');?></a>
                                            </td>
                                        <?php } ?>
                                    </tr>

                            <?php } else { ?>
                                <tr>
                                    <td style="width:40%; padding: 15px;"><b><?php _e('Default Relay State (Optional)','miniorange-saml-20-single-sign-on');?></b></td>
                                    <td style="width:60%;  padding: 15px;font-weight: 400">
                                    <a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><b><?php _e('Available in the Standard, Premium, Enterprise and All-Inclusive plans of the plugin.','miniorange-saml-20-single-sign-on');?></b></a>
                                    </td>
                                </tr>

                                    <tr>
                                        <td style="width:40%; padding: 15px;"><b><?php _e('Certificate (Optional)','miniorange-saml-20-single-sign-on');?></b></td>
                                        <td style="width:60%;  padding: 15px;font-weight: 400">
                                        <a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><b><?php _e('Available in the Standard, Premium, Enterprise and All-Inclusive plans of the plugin.','miniorange-saml-20-single-sign-on');?></b></a>
                                        </td>
                                    </tr>

                            <?php } ?>
                        </table>
                    </div>



            </td>
        </tr>

        <!--STEP-2-->



    </table>
    <script>
    function copyToClipboard(copyButton, element, copyelement) {
        var temp = jQuery("<input>");
        jQuery("body").append(temp);
        temp.val(jQuery(element).text()).select();
        document.execCommand("copy");
        temp.remove();
        jQuery(copyelement).text("<?php _e('Copied','miniorange-saml-20-single-sign-on');?>");

        jQuery(copyButton).mouseout(function(){
            jQuery(copyelement).text("<?php _e('Copy to Clipboard','miniorange-saml-20-single-sign-on');?>");
        });
    }
    </script>
    <form name="mo_saml_download_metadata" method="post" action="">
    <?php wp_nonce_field("mosaml_metadata_download");?>
            <input type="hidden" name="option" value="mosaml_metadata_download"/>

</form>
    <?php
}

function mo_saml_apps_config_saml() {
    $sync_url                        = get_option( 'saml_metadata_url_for_sync' );

    if ( isset( $_GET['action'] ) && $_GET['action'] == 'upload_metadata' ) {
        echo '<div border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px;">
        <table style="width:100%;">
            <tr>
                <td colspan="3">
                    <h3>' . __('Upload IDP Metadata','miniorange-saml-20-single-sign-on') . '
                        <span style="float:right;margin-right:25px;">
                            <a href="' . admin_url() . 'admin.php?page=mo_saml_settings&tab=save' . '"><input type="button" class="button" value="' . __('Cancel','miniorange-saml-20-single-sign-on') . '"/></a>
                        </span>
                    </h3>
                </td>
            </tr>
            <tr><td colspan="4"><hr></td></tr>
            <tr>';

        echo '
            <form name="saml_form" method="post" action="' . admin_url() . 'admin.php?page=mo_saml_settings&tab=save' . '" enctype="multipart/form-data">


        <tr>
                <td width="30%"><strong>' . __('Identity Provider Name','miniorange-saml-20-single-sign-on') . '<span style="color:red;">*</span>:</strong></td>
                <td><input type="text" name="saml_identity_metadata_provider" placeholder="' . __('Identity Provider name like ADFS, SimpleSAML','miniorange-saml-20-single-sign-on') . '" pattern="\w+" title="' . __('Only alphabets, numbers and underscore is allowed','miniorange-saml-20-single-sign-on') . '" style="width: 100%;" value="" required /></td>
                </tr>

                <tr>';

        echo '
                <input type="hidden" name="option" value="saml_upload_metadata" />';
                wp_nonce_field("saml_upload_metadata");
               echo' <input type="hidden" name="action" value="upload_metadata" />

                    <td>' . __('Upload Metadata','miniorange-saml-20-single-sign-on') . '  :</td>
                    <td colspan="2"><input type="file" name="metadata_file" />
                    <input type="submit" class="button button-primary button-large" value="' . __('Upload','miniorange-saml-20-single-sign-on') . '"/></td>
                    </tr>';
        echo '<tr>
                <td colspan="2"><p style="font-size:13pt;text-align:center;"><b>' . __('OR','miniorange-saml-20-single-sign-on') . '</b></p></td>
            </tr>';
        echo '

            <tr>
                <input type="hidden" name="option" value="saml_upload_metadata" />
                <input type="hidden" name="action" value="fetch_metadata" />
                <td width="20%">' . __('Enter metadata URL','miniorange-saml-20-single-sign-on') . ':</td>
                <td><input type="url" name="metadata_url" placeholder="' . __('Enter metadata URL of your IdP.','miniorange-saml-20-single-sign-on') . '" style="width:100%" value="' . $sync_url . '"/></td>
                <td width="20%">&nbsp;&nbsp;<input type="submit" class="button button-primary button-large" value="' . __('Fetch Metadata','miniorange-saml-20-single-sign-on') . '"/></td>
            </tr>
            </form>';
        echo '</table><br /></div>';


    } else {
        global $wpdb;
        $entity_id = get_option( 'entity_id' );
        if ( ! $entity_id ) {
            $entity_id = 'https://auth.miniorange.com/moas';
        }
        $sso_url = get_option( 'sso_url' );
        $cert_fp = get_option( 'cert_fp' );

        //Broker Service
        $saml_identity_name    = get_option( 'saml_identity_name' );
        $saml_login_url        = get_option( 'saml_login_url' );
        $saml_issuer           = get_option( 'saml_issuer' );
        $saml_x509_certificate = maybe_unserialize( get_option( 'saml_x509_certificate' ) );
        $saml_x509_certificate = ! is_array( $saml_x509_certificate ) ? array( 0 => $saml_x509_certificate ) : $saml_x509_certificate;
        $saml_response_signed  = get_option( 'saml_response_signed' );
        $mo_saml_identity_provider_identifier_name = get_option('mo_saml_identity_provider_identifier_name')?get_option('mo_saml_identity_provider_identifier_name'):"";

        $saml_is_encoding_enabled = get_option('mo_saml_encoding_enabled')!==false?get_option('mo_saml_encoding_enabled'):'checked';

        $saml_b2c_tenant = get_option('saml_b2c_tenant_id');
        $saml_IdentityExperienceFramework_id = get_option('saml_IdentityExperienceFramework_id');
        $saml_ProxyIdentityExperienceFramework_id = get_option('saml_ProxyIdentityExperienceFramework_id');
        if ( $saml_response_signed == null ) {
            $saml_response_signed = 'checked';
        }
        $saml_assertion_signed = get_option( 'saml_assertion_signed' );
        if ( $saml_assertion_signed == null ) {
            $saml_assertion_signed = 'Yes';
        }

        $idp_config = get_option( 'mo_saml_idp_config_complete' );
        ?>
        <div id="mo_saml_idps_grid_form">
        <form id="mo_saml_idps_grid_form" width="98%" border="0"
              style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px;"
              method="post" action="">

              <table style="width:100%;">
                <tr>
                    <td><h3><?php _e('Select your Identity Provider','miniorange-saml-20-single-sign-on');?>

                    <span id="configure-service-restart-tour" style="position: relative;float: right;background: white;border-radius: 10px;padding: 6px;">
                        <button type="button" id="service-provider-setup" class="button button-primary button-large"  onclick="restart_tours(this)"><i class="icon-refresh"></i> <?php _e('Take Tab-tour','miniorange-saml-20-single-sign-on');?></button>
                            </span></h3></td>
                </tr>


<style>

#mo_saml_idps_grid_div li{
    border: 1px solid #0000001f;
    width: 100px;
    display:inline-block;
    text-align:center;
    background:ghostwhite;
    padding: 10px 0px 5px 0px;
}

#mo_saml_idps_grid_div img{
    width: 4em;
    height: 4em;
}

#mo_saml_selected_idp_icon_div{
    width: 100px;
    display:inline-block;
    text-align:center;
    padding: 10px 0px 5px 0px;
}

#mo_saml_selected_idp_icon_div:hover{
    -moz-box-shadow: 2px 2px 5px #999;
    -webkit-box-shadow: 2px 2px 5px #999;
    box-shadow:  2px 2px 5px #999;
}

#mo_saml_selected_idp_icon_div img{
    margin: 5px 0px 0px 0px;
    width: 3em;
    height: 3em;
}

#mo_saml_selected_idp_icon_div h4{
    margin: 6px 0px 6px 0px;
}

#mo_saml_selected_idp_icon_div a{
    text-decoration:none;
    border: none;
    color: black;
    display: block;
    min-height: 80px;
}

#mo_saml_idps_grid_div a{
    text-decoration:none;
    border: none;
    color: black;
    display: block;
    min-height: 80px;
}

#mo_saml_idps_grid_div h4{
    margin: 6px 0px 6px 0px;
}

#mo_saml_idps_grid_div li:hover{
    -moz-box-shadow: 2px 2px 5px #999;
    -webkit-box-shadow: 2px 2px 5px #999;
    box-shadow:  2px 2px 5px #999;
}

</style>
<script>
jQuery(document).ready(function(){

displayAzureElemets();

<?php if(get_option('mo_saml_identity_provider_identifier_name')==""){
    ?>
    document.getElementById('mo_saml_idps_grid_form').style.display = "";
    document.getElementById('mo_saml_selected_idp_div').style.display = "none";
    <?php
}else{
?>

        jQuery("#mo_saml_idps_grid_div li").filter(function(){
            var p = jQuery(this).find('a');
            var value = "<?php echo get_option('mo_saml_identity_provider_identifier_name');?>"; 
            var di = p.html();
            var div1 = di.split('<br>')[1].split('<h4>')[1].split('</h4>')[0];
            if(div1.toLowerCase().indexOf(value.toLowerCase())>-1){
                document.getElementById("mo_saml_selected_idp_icon_div").innerHTML = jQuery(this).html();
                 var guide_link = jQuery(this).find('a').data('href');
                document.getElementById("saml_idp_guide_link").href = guide_link;
                jQuery('html, body').animate({
           'scrollTop' : jQuery('#service_provider_setup').position().top}, 500);
            }



        });


    document.getElementById('mo_saml_selected_idp_div').style.display = "";
<?php 
}?>

jQuery('#mo_saml_search_idp_list').focus(function(){
    
document.getElementById("mo_saml_idps_grid_div").style.display="";
});
    
    jQuery('#mo_saml_search_idp_list').keyup(function(){
        var value = jQuery(this).val().toLowerCase();
        var customidp = '';
        var counter = 0;
        document.getElementById('mo_saml_search_custom_idp_message').style.display = "none";
        jQuery("#mo_saml_idps_grid_div li").filter(function(){
            var p = jQuery(this).find('a');
            var di = p.html();
            var div1 = di.split('<br>')[1].split('<h4>')[1].split('</h4>')[0];
            if(div1.toLowerCase().indexOf(value)>-1){
                jQuery(this).css("display","inline-block");
                counter+=1;
            }else{
                jQuery(this).css("display","none");
            }
            if(div1.toLowerCase().indexOf('custom idp')>-1){
                customidp = jQuery(this);
            }

        });
        if(counter == 0){
            customidp.css('display','inline-block');
            document.getElementById('mo_saml_search_custom_idp_message').style.display = "";
        }
    });

    jQuery('#mo_saml_idps_grid_div li').on('click',function(){

        document.getElementById('mo_saml_selected_idp_div').style.display = "";
        var video_link = jQuery(this).find('a').data('video');
        var video_index = jQuery(this).find('a').data('idp-value');
        if(video_index == ''){
            document.getElementById('saml_idp_video_link').style.display = "none";
        }
        else{
            document.getElementById('saml_idp_video_link').style.display = "";
            document.getElementById("saml_idp_video_link").href = video_link;
        }

        var guide_link = jQuery(this).find('a').data('href');
        document.getElementById("saml_idp_guide_link").href = guide_link;
        document.getElementById("mo_saml_selected_idp_icon_div").innerHTML = jQuery(this).html();
        document.getElementById("mo_saml_identity_provider_identifier_name").value = jQuery(this).html().split('<br>')[1].split('<h4>')[1].split('</h4>')[0];
        if(document.getElementById("mo_saml_identity_provider_identifier_name").value==="Custom IDP"){
            document.getElementById('custom_idp_selected').style.display = "block";
            document.getElementById("custom_idp_selected").innerHTML = "<p style=\"font-size: 18px;background: #f3f5f6;padding-top: 10px;padding-bottom: 10px;padding-left: 9px;border-radius: 16px;\"><i><b><?php _e('Note:','miniorange-saml-20-single-sign-on');?></b> <?php _e('Please feel free to reach out to us in case of any issues for setting up the Custom IDP using the Contact Us dialog.','miniorange-saml-20-single-sign-on');?></i></p>"
        }
        else{
           document.getElementById('custom_idp_selected').style.display = "none";
        }
         document.getElementById('selected_idp_div').style.zIndex = 2;

         displayAzureElemets();

        jQuery('html, body').animate({
           'scrollTop' : 470
}, 600);
    });

    jQuery('#saml_change_idp').on('click',function(){
         jQuery('html, body').animate({
           'scrollTop' : jQuery('#mo_saml_idps_grid_form').position().top
}, 500);
    });


});

function displayAzureElemets(){
    if(document.getElementById("mo_saml_identity_provider_identifier_name").value==="Azure B2C"){
        var azureb2celements = document.getElementsByClassName("mo_saml_azure_b2c");
        for(var i = 0; i < azureb2celements.length; i++){
            azureb2celements[i].style.removeProperty("display");
            azureb2celements[i].children[1].firstChild.required = true;
        }
        var idpelements = document.getElementsByClassName("mo_saml_idp");
        for(var i = 0; i < idpelements.length; i++){
            idpelements[i].style.display = "none";
            idpelements[i].children[1].firstChild.required = false;
        }

        var azureb2celementspad = document.getElementsByClassName("mo_saml_azure_b2c_pad");
        for(var i = 0; i < azureb2celementspad.length; i++){
            azureb2celementspad[i].style.removeProperty("display");
        }
        var idpelementspad = document.getElementsByClassName("mo_saml_idp_pad");
        for(var i = 0; i < idpelementspad.length; i++){
            idpelementspad[i].style.display = "none";
        }

    } else {
        var azureb2celements = document.getElementsByClassName("mo_saml_azure_b2c");
        for(var i = 0; i < azureb2celements.length; i++){
            azureb2celements[i].style.display = "none";
            azureb2celements[i].children[1].firstChild.required = false;
        }
        var idpelements = document.getElementsByClassName("mo_saml_idp");
        for(var i = 0; i < idpelements.length; i++){
            idpelements[i].style.removeProperty("display");
            idpelements[i].children[1].firstChild.required = true;
        }

        var azureb2celementspad = document.getElementsByClassName("mo_saml_azure_b2c_pad");
        for(var i = 0; i < azureb2celementspad.length; i++){
            azureb2celementspad[i].style.display = "none";
        }
        var idpelementspad = document.getElementsByClassName("mo_saml_idp_pad");
        for(var i = 0; i < idpelementspad.length; i++){
            idpelementspad[i].style.removeProperty("display");
        }
    }

    <?php if(get_option('saml_identity_name')) { ?>
        var idpelements = document.getElementsByClassName("mo_saml_idp");
        for(var i = 0; i < idpelements.length; i++){
            idpelements[i].style.removeProperty("display");
            idpelements[i].children[1].firstChild.required = true;
        }
        var idpelementspad = document.getElementsByClassName("mo_saml_idp_pad");
        for(var i = 0; i < idpelementspad.length; i++){
            idpelementspad[i].style.removeProperty("display");
        }

    <?php } ?>
}

</script>

                <tr>
                    <td colspan="2" ><?php _e('Select your Identity Provider from the list below, and you can find the link to the guide for setting up SAML below.','miniorange-saml-20-single-sign-on');?>
                        <br/><?php esc_html_e('Please contact us if you don\'t find your IDP in the list.','miniorange-saml-20-single-sign-on');?>

                        <br/><br/></td>
                </tr>
                <tr >
                    <td colspan="2"><input type="text" id="mo_saml_search_idp_list" style="width:95%;" placeholder="<?php _e('Start typing your identity provider name here..','miniorange-saml-20-single-sign-on');?>"></td>
                </tr>
                <tr>
                    <td colspan="2"><br><span id="mo_saml_search_custom_idp_message" style="width:95%;display:none;"><?php _e('It looks like your identity provider is not listed below, you can select<strong> Custom IDP </strong>to configure the plugin. Please send us query using support form given aside for more details.','miniorange-saml-20-single-sign-on');?></span></td>
                </tr>
                <tr style="position: relative">
                    <td colspan="2" style="position:relative;">
                        <div id="mo_saml_idps_grid_div" style="position: relative">
                            <ul>
                                <?php
                                $image_path = "images".DIRECTORY_SEPARATOR."idp-guides-logos".DIRECTORY_SEPARATOR;
                                foreach(mo_saml_options_plugin_idp::$IDP_GUIDES as $key=>$value){
                                    $idp_videos = mo_saml_options_plugin_idp_videos::$IDP_VIDEOS;
                                    $idp_video_index = $idp_videos[$value];
                                    ?>
                                    <li>
                                    <a target="_blank" style="cursor: pointer" data-idp-value="<?php echo $idp_video_index?>"
                                        data-href="https://plugins.miniorange.com/saml-single-sign-on-sso-wordpress-using-<?php echo $value?>"
                                        data-video="https://www.youtube.com/watch?v=<?php echo $idp_video_index?>">
                                        <img src = "<?php echo plugins_url( $image_path.$value.'.png', __FILE__ );?>">
                                        <br><h4><?php echo $key?></h4>
                                    </a>
                                </li>

                                <?php
                                }


                                ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
        <br>
        </div>
        <form width="98%" border="0"
              style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px;" name="saml_form"
              method="post" action="">
               <?php
            if ( function_exists('wp_nonce_field') )
                wp_nonce_field('login_widget_saml_save_settings');?>
            <input type="hidden" name="option" value="login_widget_saml_save_settings"/>
            <table style="width:100%;" id="service_provider_setup">
                <tr>
                    <td colspan="2" >
                        <h3><?php _e('Configure Service Provider','miniorange-saml-20-single-sign-on');?> &nbsp &nbsp
                        <span style="position: relative;padding-bottom: 4px;padding-top:4px;background: white;border-radius: 10px;float:right;padding-right: 22px;" id="upload-metadata" >

                            <a href="<?php echo admin_url(); ?>admin.php?page=mo_saml_settings&tab=save&action=upload_metadata" style="margin-left: 15px">

                            <input type="button" class="button button-primary button-large"
                                            value="<?php _e('Upload IDP Metadata File/XML','miniorange-saml-20-single-sign-on');?>" style="font-size: medium"
                                        <?php
                                        if ( ! mo_saml_is_customer_registered_saml() ) {
                                            echo "disabled";
                                        }
                                        ?>

                                    /></a>&nbsp &nbsp
                </span></h3>
                </tr>
                <tr>
                    <td colspan="4">
                        <hr>
                    </td>
                </tr>
                <tr id="mo_saml_selected_idp_div" style="position: relative">
                        <td><strong><?php _e('Identity Provider','miniorange-saml-20-single-sign-on');?> :</strong></td>
                        <td>
                            <div style="position: relative;background-color: white" id="selected_idp_div">
                            <div id="mo_saml_selected_idp_icon_div" style="border:1px solid #7e8993;border-radius: 4px;box-shadow: 0 0 0 transparent">
                            </div>
                            <a target="_blank" class="button button-primary" style="margin-top:5%; margin-left: 5%;" id="saml_idp_guide_link" href="" id="select_your_idp"><?php _e('Click here to open Guide','miniorange-saml-20-single-sign-on');?></a>
                            <a target="_blank" class="button button-primary" style="margin-top:5%; margin-left: 2%;" id="saml_idp_video_link"><?php _e('Click here to view Setup Video','miniorange-saml-20-single-sign-on');?></a>
                            <input type="hidden" id="mo_saml_identity_provider_identifier_name" name="mo_saml_identity_provider_identifier_name" value="<?php echo $mo_saml_identity_provider_identifier_name;?>"  />
                                     <span style="position: relative;/* padding-bottom: 0.1%; */padding-top: 3.5%;background: white;border-radius: 10px;padding-left: -7px;/* padding-right: 9px; */float: right;padding-right: 5%;"
                                      id="mo_saml_idps_grid_div" >
                        </span>
                            </div>
                        </td>
                </tr>
                <tr>
                    <td colspan="3">
                    <div id="custom_idp_selected" hidden></div>
                    <br>
                    </td>
                </tr>


                <tr class="mo_saml_azure_b2c" >
                    <td><strong><?php _e('Azure B2C tenant Name','miniorange-saml-20-single-sign-on');?> <span style="color:red;">*</span>:</strong></td>
                    <td><input type="text" name="saml_b2c_tenant_id"
                               placeholder="<?php _e('Enter your Azure B2C tenant Name. Example: xyb2c.onmicrosoft.com','miniorange-saml-20-single-sign-on');?>"
                               style="width: 95%;" value="<?php echo $saml_b2c_tenant; ?>"
                               required <?php if ( ! mo_saml_is_customer_registered_saml() )
                            echo 'disabled' ?> /></td>
                </tr>
                <tr class="mo_saml_azure_b2c_pad"><td><br/></td></tr>
                <tr class="mo_saml_azure_b2c">
                    <td><strong><?php _e('IdentityExperienceFramework app ID','miniorange-saml-20-single-sign-on');?> <span style="color:red;">*</span>:</strong></td>
                    <td><input type="text" name="saml_IdentityExperienceFramework_id"
                               placeholder="<?php _e('Enter the application ID for the IdentityExperienceFramework app','miniorange-saml-20-single-sign-on');?>"
                               style="width: 95%;" value="<?php echo $saml_IdentityExperienceFramework_id; ?>"
                               required <?php if ( ! mo_saml_is_customer_registered_saml() )
                            echo 'disabled' ?> /></td>
                </tr>
                <tr class="mo_saml_azure_b2c_pad"><td><br/></td></tr>
                <tr class="mo_saml_azure_b2c">
                    <td><strong><?php _e('ProxyIdentityExperienceFramework app ID','miniorange-saml-20-single-sign-on');?> <span style="color:red;">*</span>:</strong></td>
                    <td><input type="text" name="saml_ProxyIdentityExperienceFramework_id"
                               placeholder="<?php _e('Enter the application ID for the ProxyIdentityExperienceFramework app','miniorange-saml-20-single-sign-on');?>"
                               style="width: 95%;" value="<?php echo $saml_ProxyIdentityExperienceFramework_id; ?>"
                               required <?php if ( ! mo_saml_is_customer_registered_saml() )
                            echo 'disabled' ?> /></td>
                </tr>
                <tr class="mo_saml_azure_b2c_pad"><td><br/></td></tr>
                <tr class="mo_saml_idp">
                    <td style="width:200px;"><strong><?php _e('Identity Provider Name','miniorange-saml-20-single-sign-on');?> <span style="color:red;">*</span>:</strong>
                    </td>
                    <td><input type="text" name="saml_identity_name"
                               placeholder="<?php _e('Identity Provider name like ADFS, SimpleSAML, Salesforce','miniorange-saml-20-single-sign-on');?>"
                               style="width: 95%;" value="<?php echo $saml_identity_name; ?>"
                               required <?php if ( ! mo_saml_is_customer_registered_saml() )
                            echo 'disabled' ?> title="<?php _e('Only alphabets, numbers and underscore is allowed','miniorange-saml-20-single-sign-on');?> pattern="\w+"/></td>
                </tr>
                <tr class="mo_saml_idp_pad">
                    <td>&nbsp;</td>
                </tr>

                <tr class="mo_saml_idp">
                    <td><strong><?php _e('IdP Entity ID or Issuer','miniorange-saml-20-single-sign-on');?> <span style="color:red;">*</span>:</strong></td>
                    <td><input type="text" name="saml_issuer" placeholder="<?php _e('Identity Provider Entity ID or Issuer','miniorange-saml-20-single-sign-on');?>"
                               style="width: 95%;" value="<?php echo $saml_issuer; ?>"
                               required <?php if ( ! mo_saml_is_customer_registered_saml() )
                            echo 'disabled' ?>/></td>
                </tr>

                <tr class="mo_saml_idp">
                    <td></td>
                    <td><b><?php _e('Note','miniorange-saml-20-single-sign-on');?></b> : <?php _e('You can find the <b>EntityID</b> in Your IdP-Metadata XML file enclosed in <code>EntityDescriptor</code> tag having attribute as <code>entityID</code>','miniorange-saml-20-single-sign-on');?></td>

                </tr>

                <tr class="mo_saml_idp_pad">
                    <td>&nbsp;</td>
                </tr>

                <tr class="mo_saml_idp">
                    <td><strong><?php _e('SAML Login URL','miniorange-saml-20-single-sign-on');?> <span style="color:red;">*</span>:</strong></td>
                    <td><input type="url" name="saml_login_url"
                               placeholder="<?php _e('Single Sign On Service URL (HTTP-Redirect binding) of your IdP','miniorange-saml-20-single-sign-on');?>"
                               style="width: 95%;" value="<?php echo $saml_login_url; ?>"
                               required <?php if ( ! mo_saml_is_customer_registered_saml() )
                            echo 'disabled' ?>/></td>
                </tr>

                <tr class="mo_saml_idp">
                    <td></td>

                    <td><b><?php _e('Note','miniorange-saml-20-single-sign-on');?></b> : <?php _e('You can find the <b>SAML Login URL</b> in Your IdP-Metadata XML file enclosed in <code>SingleSignOnService</code> tag (Binding type: HTTP-Redirect)','miniorange-saml-20-single-sign-on');?>


                </tr>

                <tr class="mo_saml_idp_pad">
                    <td>&nbsp;</td>
                </tr>
                <?php
                foreach ( $saml_x509_certificate as $key => $value ) {
                    echo '<tr class="mo_saml_idp">
                <td><strong>' . __('X.509 Certificate','miniorange-saml-20-single-sign-on') . ' <span style="color:red;">*</span>:</strong></td>
                <td><textarea rows="6" cols="5" name="saml_x509_certificate[' . $key . ']" placeholder="' . __('Copy and Paste the content from the downloaded certificate or copy the content enclosed in X509Certificate tag (has parent tag KeyDescriptor use=signing) in IdP-Metadata XML file','miniorange-saml-20-single-sign-on') . '" style="width: 95%;"';
                    echo ' >' . $value . '</textarea></td>
                </tr>
                <tr class="mo_saml_idp">
                    <td>&nbsp;</td>
                    <td><b>' . __('NOTE','miniorange-saml-20-single-sign-on') . ':</b>' . __('Format of the certificate','miniorange-saml-20-single-sign-on') . ':<br/><b>-----BEGIN CERTIFICATE-----<br/>XXXXXXXXXXXXXXXXXXXXXXXXXXX<br/>-----END CERTIFICATE-----</b></i><br/>
                </tr>';
                }


                ?>
                <tr class="mo_saml_idp_pad">
                    <td>&nbsp;</td>
                </tr>
                <tr class="mo_saml_idp">
                    <td><strong><label for="enable_iconv"><?php _e('Character encoding','miniorange-saml-20-single-sign-on');?> :</label></strong></td>
                    <td>
                    <label class="switch">
                        <input type="checkbox" name="enable_iconv" id="enable_iconv"  <?php echo $saml_is_encoding_enabled;?>/>
                        <span class="slider round"></span>
					</label>
                    </td>
                </tr>
                <tr class="mo_saml_idp">
                    <td>&nbsp;</td>
                    <td><b><?php _e('NOTE','miniorange-saml-20-single-sign-on');?>: </b><?php _e('Uses iconv encoding to convert X509 certificate into correct encoding.','miniorange-saml-20-single-sign-on');?> </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><br/><input type="submit" name="submit" style="width:150px;margin-right: 3%;" value="<?php _e('Save','miniorange-saml-20-single-sign-on');?>"
                                    class="button button-primary button-large"<?php if ( ! mo_saml_is_customer_registered_saml() )
                            echo 'disabled' ?>/>
                        <input type="button" id="test_config"
                               title= "<?php if(!mo_saml_is_openssl_installed()){
                                _e('Enable openssl extension to test your configuration.','miniorange-saml-20-single-sign-on');
                               }
                                else{
                               _e('You can only test your Configuration after saving your Service Provider Settings.','miniorange-saml-20-single-sign-on'); }?>"
                            onclick="showTestWindow();" <?php if ( ! mo_saml_is_sp_configured() || ! get_option( 'saml_x509_certificate' ) || !mo_saml_is_openssl_installed() )
                            echo 'disabled' ?> value="<?php _e('Test configuration','miniorange-saml-20-single-sign-on');?>" class="button button-primary button-large"
                               style="margin-right: 3%;width: 150px;position: absolute"/>
                    </td>
                </tr>
                <tr class="mo_saml_azure_b2c">
                <td></td>
                <td>
                <br/>
                <input type="button" name="generate_b2c_policies" id="generate_b2c_policies" style="width:150px;margin-right: 3%;"
                title="<?php _e('Generate Azure B2C SSO Policies','miniorange-saml-20-single-sign-on');?>" value="<?php _e('Generate SSO Policies','miniorange-saml-20-single-sign-on');?>" class="button button-primary button-large"

                <?php
                if(empty($saml_b2c_tenant) || empty($saml_IdentityExperienceFramework_id) || empty($saml_ProxyIdentityExperienceFramework_id)){
                    echo " disabled ";
                }
                ?>

                 style="width:320px;position: relative" onclick="jQuery('#genetateB2CPolicies').submit();">

                 <input type="button" name="update_sso_configuration" id="update_sso_configuration"
                title="<?php _e('Update SSO Configuration','miniorange-saml-20-single-sign-on');?>" value="<?php _e('Update Configuration','miniorange-saml-20-single-sign-on');?>" class="button button-primary button-large"

                <?php
                if(empty($saml_b2c_tenant) || empty($saml_IdentityExperienceFramework_id) || empty($saml_ProxyIdentityExperienceFramework_id)){
                    echo " disabled ";
                }
                ?>

                style="margin-right: 3%;width: 150px;position: absolute" onclick="jQuery('#updateConfig').submit();"
                >

                 </td>
                </tr>


                <tr>
                    <td></td>
                    <td><br /><input type="button" name="saml_request" id="export-import-config"
                               title="<?php _e('Export Plugin Configuration','miniorange-saml-20-single-sign-on');?>"
                        <?php if ( ! mo_saml_is_sp_configured() || ! get_option( 'saml_x509_certificate' ) ) {
                            echo 'disabled';
                        } ?> value="<?php _e('Export Plugin Configuration','miniorange-saml-20-single-sign-on');?>" class="button button-primary button-large" style="width:320px;position: relative"
                        onclick="jQuery('#mo_export').submit();"/></td>
                </tr>
            </table>
            <br/>
        </form>
        <form method="get" target="_blank" action="" id="getIDPguides"></form>
        <form method="post" action="#" id="updateConfig">
        <?php wp_nonce_field('update_sso_config');?>
        <input type="hidden" name="saml_identity_metadata_provider" value="<?php echo $saml_b2c_tenant; ?>">
        <input type="hidden" name="option" value="update_sso_config">
        </form>
        <form method="post" action="" id="genetateB2CPolicies">
        <?php wp_nonce_field('generate_b2c_policies'); ?>
        <input type="hidden" name="option" value="generate_b2c_policies">
        </form>
        <form method="post" action="" name="mo_export" id="mo_export">
        <?php
           wp_nonce_field('mo_saml_export');?>
		<input type="hidden" name="option" value="mo_saml_export" /></form>

        <script>
            function showTestWindow() {
                var myWindow = window.open("<?php echo mo_saml_get_test_url(); ?>", "TEST SAML IDP", "scrollbars=1 width=800, height=600");
            }

            function getidpguide(){
                var dropdown = document.getElementById('idpguide');

                var action = 'https://plugins.miniorange.com/saml-single-sign-on-sso-wordpress-using-'+dropdown.value;
                if(dropdown.value==='Other'){
                    action = "";
                    jQuery('#idplink').hide();

                }
                else if(dropdown.value==='miniorange'){
                    action = 'https://plugins.miniorange.com/saml-single-sign-on-sso-wordpress-using-miniorange';
                }
                if(action!==""){

                jQuery('#idplink').attr("href",action).show();
                jQuery('#getIDPguides').attr('action',action);
                jQuery('#getIDPguides').submit();

                }
            }

            function redirect_to_attribute_mapping(){
				window.location.href= "<?php echo mo_saml_get_attribute_mapping_url(); ?>";
			}

			function  redirect_to_service_provider() {
			  	window.location.href= "<?php echo mo_saml_get_service_provider_url(); ?>";

			}
        </script>
        <?php
    }
}


function mo_saml_save_optional_config() {


    $saml_am_first_name = get_option( 'saml_am_first_name' );
    $saml_am_last_name  = get_option( 'saml_am_last_name' );

    ?>
    <form name="saml_form_am" method="post" action="">
        <input type="hidden" name="option" value="login_widget_saml_attribute_mapping"/>
        <?php wp_nonce_field("login_widget_saml_attribute_mapping");?>
        <table width="98%" border="0"
               style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px;position: relative;" id="miniorange-attribute-mapping">
            <tr>
                <td colspan="2">
                    <h3><?php _e('Attribute Mapping','miniorange-saml-20-single-sign-on');?>
            <span style="float: right; padding-left:13px;padding-right:13px; border-radius:4px; background-color: white;position: relative" id="attribute-mapping-restart-tour" >
            <button type="button" id="attribute-role-mapping" class="button button-primary button-large"  onclick="restart_tours(this)"><i class="icon-refresh"></i>  <?php _e('Take Tab-tour','miniorange-saml-20-single-sign-on');?></button>
            </span>
</h3>
                </td>

            </tr>

            <tr>
                <td colspan="2">[ <a href="https://docs.miniorange.com/documentation/saml-handbook/attribute-role-mapping" id="attribute_mapping" target="_blank    "><?php _e('Click Here to know how this is useful ','miniorange-saml-20-single-sign-on');?></a>]

                </td>
            </tr>
            <tr><td><td></tr>
            <tr>
                <td colspan="2"><br/>
                <label class="switch">
                <input type="checkbox" disabled style="background: #DCDAD1;"/>
                <span class="slider round"></span>
					</label><span style="padding-left:5px"><b><?php _e('Anonymous Login','miniorange-saml-20-single-sign-on');?></b></span>
                    <a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><sup><b><?php _e('[Available in All-Inclusive version of the plugin]','miniorange-saml-20-single-sign-on');?></b></a></sup><br/>
                    Enable this option if you want to allow users to login to the WordPress site without creating a WordPress user account for them.<br/><br/></td>
            </tr>

            <tr>
                <td colspan="2"><b><?php _e('NOTE','miniorange-saml-20-single-sign-on');?>: </b><?php _e('Use attribute name <code>NameID</code> if Identity is in the <i>NameIdentifier</i> element of the subject statement in SAML Response.','miniorange-saml-20-single-sign-on');?><br/><br/></td>
            </tr>

                <tr>
                    <td style="width:150px;"><span style="color:red;">*</span><strong><?php _e('Username (required)','miniorange-saml-20-single-sign-on');?>:</strong></td>
                    <td><b>NameID</b></td>
                </tr>
                <tr>
                    <td><span style="color:red;">*</span><strong><?php _e('Email (required)','miniorange-saml-20-single-sign-on');?>:</strong></td>
                    <td><b>NameID</b></td>
                </tr>

            <tr>
                <td><span style="color:red;">*</span><strong><?php _e('First Name','miniorange-saml-20-single-sign-on');?>:</strong></td>
                <td><input type="text" disabled name="saml_am_first_name" placeholder="<?php _e('Enter attribute name for First Name','miniorange-saml-20-single-sign-on');?>"
                           style="width: 350px;background: #DCDAD1;"
                           value="<?php echo $saml_am_first_name; ?>" <?php if ( ! mo_saml_is_customer_registered_saml() )
                        echo 'disabled' ?>/></td>
            </tr>
            <tr>
                <td><span style="color:red;">*</span><strong><?php _e('Last Name','miniorange-saml-20-single-sign-on');?>:</strong></td>
                <td><input type="text" disabled name="saml_am_last_name" placeholder="<?php _e('Enter attribute name for Last Name','miniorange-saml-20-single-sign-on');?>"
                           style="width: 350px;background: #DCDAD1;"
                           value="<?php echo $saml_am_last_name; ?>" <?php if ( ! mo_saml_is_customer_registered_saml() )
                        echo 'disabled' ?>/></td>
            </tr>

                <tr>
                    <td><span style="color:red;">*</span><strong><?php _e('Group/Role','miniorange-saml-20-single-sign-on');?>:</strong></td>
                    <td><input type="text" disabled placeholder="<?php _e('Enter attribute name for Group/Role','miniorange-saml-20-single-sign-on');?>"
                               style="width: 350px;background: #DCDAD1;"/></td>
                </tr>
                <tr>
                    <td colspan="2">
                    <br/><span style="color:red;">*</span>
                     <a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><b><?php _e('These attributes are configurable in Standard, Premium, Enterprise and All-Inclusive versions of the plugin.','miniorange-saml-20-single-sign-on');?></b></a><br/>
                                <h3><?php _e('Map Custom Attributes','miniorange-saml-20-single-sign-on');?></h3>
                                <?php _e('Customized Attribute Mapping means you can map any attribute of the IDP to the <b>usermeta</b> table of your database.','miniorange-saml-20-single-sign-on');?><br/>
                                <a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><b><?php _e('Customized Attribute Mapping is configurable in the Premium, Enterprise and All-Inclusive versions of the plugin.','miniorange-saml-20-single-sign-on');?></b></a><br/><br/>
                    </td>
                </tr>

        </table>
    </form>
    <br/>
    <form name="saml_form_am_role_mapping" method="post" action="">
        <?php
                wp_nonce_field('login_widget_saml_role_mapping');?>
        <input type="hidden" name="option" value="login_widget_saml_role_mapping"/>
        <table width="98%" border="0" id="miniorange-role-mapping"
               style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px;position: relative">
            <tr>
                <td colspan="2">
                    <h3><?php _e('Role Mapping','miniorange-saml-20-single-sign-on');?></h3>
                </td>
            </tr>
            <tr>
                <td colspan="2">[ <a href="https://docs.miniorange.com/documentation/saml-handbook/attribute-role-mapping/role-mapping" target="_blank"><?php _e('Click Here to know how this is useful ','miniorange-saml-20-single-sign-on');?></a> ]

                </td>
            </tr>
            <tr>
                <td colspan="2"><br/><b><?php _e('NOTE','miniorange-saml-20-single-sign-on');?>: </b><?php esc_html_e('Role will be assigned only to new users. Existing Wordpress users\' role remains same.','miniorange-saml-20-single-sign-on');?><br/><br/></td>
            </tr>
            <tr>
                <td colspan="2">
                <label class="switch">
                <input type="checkbox" disabled style="background: #DCDAD1;"/>
                <span class="slider round"></span>
					</label><span style="padding-left:5px"><b><span
                            style="color:red;">*</span><?php _e('Do not auto create users if roles are not mapped here','miniorange-saml-20-single-sign-on');?></b></span><br/></td>
            </tr>

                <tr>
                    <td colspan="2">
                    <label class="switch">
                    <input type="checkbox" style="background: #DCDAD1;" disabled/>
                    <span class="slider round"></span>
					</label><span style="padding-left:5px"><b><span
                                style="color:red;">*</span><?php _e('Do not assign role to unlisted users','miniorange-saml-20-single-sign-on');?></b></span><br/><br/></td>
                </tr>

            <tr>
                <td><strong><?php _e('Default Role','miniorange-saml-20-single-sign-on');?>:</strong></td>
                <td>
                    <?php
                    $disabled = '';
                    if ( ! mo_saml_is_customer_registered_saml() ) {
                        $disabled = 'disabled';
                    }
                    ?>
                    <select id="saml_am_default_user_role" name="saml_am_default_user_role" <?php echo $disabled ?>
                            style="width:150px;">
                        <?php
                        $default_role = get_option( 'saml_am_default_user_role' );
                        if ( empty( $default_role ) ) {
                            $default_role = get_option( 'default_role' );
                        }
                        echo wp_dropdown_roles( $default_role );
                        ?>
                    </select>
                    &nbsp;&nbsp;&nbsp;&nbsp;<i><?php _e('Select the default role to assign to Users.','miniorange-saml-20-single-sign-on');?></i>
                </td>
            </tr>
            <?php
            $is_disabled = "";
            if ( ! mo_saml_is_customer_registered_saml() ) {
                $is_disabled = "disabled";
            }
            $wp_roles         = new WP_Roles();
            $roles            = $wp_roles->get_names();
            $roles_configured = get_option( 'saml_am_role_mapping' );
            foreach ( $roles as $role_value => $role_name ) {
                if ( ! get_option( 'mo_saml_free_version' ) ) {
                    echo '<tr><td><b>' . $role_name . '</b></td><td><input type="text" name="saml_am_group_attr_values_' . $role_value . '" value="' . $roles_configured[ $role_value ] . '" placeholder="' . sprintf(__('Semi-colon(;) separated Group/Role value for %s','miniorange-saml-20-single-sign-on'),  $role_name) . '" style="width: 400px;"' . $is_disabled . ' /></td></tr>';
                } else {
                    echo '<tr><td><span style="color:red;">*</span><b>' . $role_name . '</b></td><td><input type="text" placeholder="' . sprintf(__('Semi-colon(;) separated Group/Role value for %s','miniorange-saml-20-single-sign-on') , $role_name) . '" style="width: 400px;background: #DCDAD1" disabled /></td></tr>';
                }
            }
            ?>
            <?php if ( get_option( 'mo_saml_free_version' ) ) { ?>
                <tr>
                    <td colspan="2"><br/><span style="color:red;">*</span>
                    <a href="<?php echo admin_url( 'admin.php?page=mo_saml_settings&tab=licensing' ); ?>"><b><?php _e('Customized Role Mapping options are configurable in the Premium, Enterprise and All-Inclusive versions of the plugin.','miniorange-saml-20-single-sign-on');?></b></a>
                       <br/><?php _e('In the standard version, you can only assign the default role to the user.','miniorange-saml-20-single-sign-on');?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td>&nbsp;</td>
                <td><br/><input type="submit" style="width:100px;" name="submit" value="<?php _e('Save','miniorange-saml-20-single-sign-on');?>"
                                class="button button-primary button-large" <?php if ( ! mo_saml_is_customer_registered_saml() )
                        echo 'disabled' ?>/> &nbsp;
                    <br/><br/>
                </td>
            </tr>
        </table>
    </form>
    <?php
}


function mo_saml_get_test_url() {

        $url = site_url() . '/?option=testConfig';


    return $url;
}

function mo_saml_is_customer_registered_saml($check_guest=true) {

    $email       = get_option( 'mo_saml_admin_email' );
    $customerKey = get_option( 'mo_saml_admin_customer_key' );

    if(mo_saml_is_guest_enabled() && $check_guest)
        return 1;
    if ( ! $email || ! $customerKey || ! is_numeric( trim( $customerKey ) ) ) {
        return 0;
    } else {
        return 1;
    }
}

function mo_saml_is_guest_enabled(){
    $guest_enabled = get_option('mo_saml_guest_enabled');

    return $guest_enabled;
}

function mo_saml_is_multisite_enabled(){
    if( is_multisite()){
        return "<b><font color='green'> enabled </font></b>";
    }
    return "<b><font color='red'> disabled </font></b>";
}

function mo_saml_is_sp_configured() {
    $saml_login_url = get_option( 'saml_login_url' );


    if ( empty( $saml_login_url ) ) {
        return 0;
    } else {
        return 1;
    }
}

function mo_saml_download_logs($error_msg,$cause_msg) {

    echo '<div style="font-family:Calibri;padding:0 3%;">';
    echo '<hr class="header"/>';
    echo '          <p style="font-size: larger       ">' . __('Please try the solution given above.If the problem persists,download the plugin configuration by clicking on Export Plugin Configuration and mail us at <a href="mailto:info@xecurify.com">info@xecurify.com</a>','miniorange-saml-20-single-sign-on') . '.</p>
                    <p>' . __('We will get back to you soon!','miniorange-saml-20-single-sign-on') . '<p>
                    </div>
                    <div style="margin:3%;display:block;text-align:center;">
                    <div style="margin:3%;display:block;text-align:center;">
                    <form method="get" action="" name="mo_export" id="mo_export">';
                    wp_nonce_field('mo_saml_export');
				echo '<input type="hidden" name="option" value="export_configuration" />
				<input type="submit" class="miniorange-button" value="' . __('Export Plugin Configuration','miniorange-saml-20-single-sign-on') . '">
				<input class="miniorange-button" type="button" value="' . __('Close','miniorange-saml-20-single-sign-on') . '" onclick="self.close()"></form>
                
               ';
    echo '&nbsp;&nbsp;';

    $samlResponse = htmlspecialchars($_POST['SAMLResponse']);
    update_option('MO_SAML_RESPONSE',$samlResponse);
    $error_array  = array("Error"=>$error_msg,"Cause"=>$cause_msg);
    update_option('MO_SAML_TEST',$error_array);
    update_option('MO_SAML_TEST_STATUS',0);
    ?>
    <style>
    .miniorange-button {
    padding:1%;
    background: #0091CD none repeat scroll 0% 0%;
    cursor: pointer;font-size:15px;
    border-width: 1px;border-style: solid;
    border-radius: 3px;white-space: nowrap;
    box-sizing: border-box;border-color: #0073AA;
    box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;
    margin: 22px;
    }
</style>
    <?php

    exit();


}




function miniorange_support_saml($active_tab, $display_support_layout=true, $display_keep_configuration_intact=true) {
    if($display_support_layout)
    {?>
        <div class="mo_saml_support_layout" id="mo_saml_support_layout" style=" position: relative;">
            <h3><?php _e('Feature Request/Contact Us (24*7 Support)','miniorange-saml-20-single-sign-on');?></h3>
            <div style="padding-right: 10px;display: block;overflow: auto;">
                <div style="float:left;width:10%;"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/phone.svg'?>" width="32" height="32"></div>
                <div style="float:left;width:88%;padding-left:5px;padding-top:5px;font-size:14px;line-height:20px;"><b><?php _e('Need any help? Just give us a call at +1 978 658 9387','miniorange-saml-20-single-sign-on');?></b></div>
            </div>
            <p style="padding-right: 10px;">
                <?php _e('We can help you with configuring your Identity Provider. Just send us a query and we will get back to you soon.','miniorange-saml-20-single-sign-on');?><br>
            </p>

            <form method="post" action="">
                <?php wp_nonce_field("mo_saml_contact_us_query_option");?>
                <input type="hidden" name="option" value="mo_saml_contact_us_query_option"/>
                <table class="mo_saml_settings_table">
                    <tr>
                        <td><input style="width:95%" type="email" id="mo_saml_support_email"
                                placeholder="<?php _e('Enter your email','miniorange-saml-20-single-sign-on');?>"
                                class="mo_saml_table_textbox"
                                name="mo_saml_contact_us_email"
                                value="<?php echo ( get_option( 'mo_saml_admin_email' ) == '' ) ? get_option( 'admin_email' ) : get_option( 'mo_saml_admin_email' ); ?>"
                                required>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="tel" style="width:95%" id="contact_us_phone"
                                pattern="[\+]?[0-9]{1,4}[\s]?([0-9]{4,12})*" class="mo_saml_table_textbox"
                                name="mo_saml_contact_us_phone"
                                value="<?php echo get_option( 'mo_saml_admin_phone' ); ?>"
                                placeholder="<?php _e('Enter your phone','miniorange-saml-20-single-sign-on');?>">
                        </td>
                    </tr>
                    <tr>
                        <td><textarea class="mo_saml_table_textbox" style="width:95%" onkeypress="mo_saml_valid_query(this)"
                                    onkeyup="mo_saml_valid_query(this)" onblur="mo_saml_valid_query(this)"
                                    name="mo_saml_contact_us_query" rows="4" style="resize: vertical;" required
                                    placeholder="<?php _e('Write your query here','miniorange-saml-20-single-sign-on');?>" id="mo_saml_query"></textarea>
                        </td>
                    </tr>
                </table>

                <div class="call-setup-div">
                    <h3 class="call-setup-heading"><?php _e('Setup a Call / Screen-share session with miniOrange Technical Team','miniorange-saml-20-single-sign-on');?></h3>
                    <label class="switch" style="margin-left: 8px;">
                        <input type="checkbox" style="background: #DCDAD1;" id="saml_setup_call" name="saml_setup_call"/>
                        <span class="slider round"></span>
                    </label>
                    <span class="call-setup-label">
                        <b><label for="saml_setup_call"></label><?php _e('Enable this option to setup a call','miniorange-saml-20-single-sign-on');?></b><br><br>
                    </span>

                    <div id="call_setup_dets" class="call-setup-details">
                        <div>
                            <div style="width: 21%; float:left;"><strong><?php _e('TimeZone','miniorange-saml-20-single-sign-on');?><font color="#FF0000">*</font>:</strong></div>
                            <div style="width: 79% !important; float: left">
                                <select id="js-timezone" name="mo_saml_setup_call_timezone">
                                <?php $zones = mo_saml_time_zones::$time_zones; ?>
                                    <option value="" selected disabled>---------<?php _e('Select your timezone','miniorange-saml-20-single-sign-on');?>--------</option> <?php
                                    foreach($zones as $zone=>$value) {
                                        if($value == 'Etc/GMT'){ ?>
                                            <option value="<?php echo $value; ?>" selected><?php echo $zone; ?></option>
                                        <?php
                                        }
                                        else { ?>
                                            <option value="<?php echo $value; ?>"><?php echo $zone; ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <br><br><br>

                        <div class="call-setup-datetime">
                            <strong> <?php _e('Date','miniorange-saml-20-single-sign-on');?><font color="#FF0000">*</font>:</strong><br>
                            <input type="text" id="datepicker" class="call-setup-textbox" placeholder="<?php _e('Select Meeting Date','miniorange-saml-20-single-sign-on');?>" autocomplete="off" name="mo_saml_setup_call_date">
                        </div>
                        <div class="call-setup-datetime">
                            <strong> <?php _e('Time (24-hour)','miniorange-saml-20-single-sign-on');?><font color="#FF0000">*</font>:</strong><br>
                            <input type="text" id="timepicker" placeholder="<?php _e('Select Meeting Time','miniorange-saml-20-single-sign-on');?>" class="call-setup-textbox" autocomplete="off" name="mo_saml_setup_call_time">
                        </div> <br><br><br>
                        <div>
                            <p class="call-setup-notice">
                                <b><font color="#dc143c"><?php _e('Call and Meeting details will be sent to your email. Please verify the email before submitting your query.','miniorange-saml-20-single-sign-on');?></font></b>
                            </p>
                        </div>
                    </div>
                </div>
                <div style="text-align:center;">
                    <input type="submit" name="submit" style="margin:15px; width:120px;" class="button button-primary button-large"/>
                </div>
            </form>
        </div><br>
        
    <?php
    }
    ?>
    <?php
    if($display_keep_configuration_intact)
        mo_saml_miniorange_keep_configuration_saml();
    else{
        $addon_slug='page-restriction';
        if($active_tab=='account-setup' || $active_tab=='opt')
            $addon_slug='scim-user-sync';
        elseif($active_tab=='support'){
            $addon_slug2 = 'scim-user-sync';
            mo_saml_display_add_ons_iframe(mo_saml_options_addons::$WP_ADDON_URL[$addon_slug2]);?><br><br><?php
        }
        mo_saml_display_add_ons_iframe(mo_saml_options_addons::$WP_ADDON_URL[$addon_slug]);
    }

    ?>

	<script>
        jQuery("#contact_us_phone").intlTelInput();
        jQuery("#phone_contact").intlTelInput();

        jQuery( function() {
            jQuery("#call_setup_dets").hide();
            jQuery("#js-timezone").select2();

            jQuery("#js-timezone").click(function() {
                var name = $('#name').val();
                var email = $('#email').val();
                var message = $('#message').val();
                jQuery.ajax ({
                    type: "POST",
                    url: "form_submit.php",
                    data: { "name": name, "email": email, "message": message },
                    success: function (data) {
                        jQuery('.result').html(data);
                        jQuery('#contactform')[0].reset();
                    }
                });
            });

            jQuery("#saml_setup_call").click(function() {
                if(jQuery(this).is(":checked")) {
                    jQuery("#call_setup_dets").show();
                    document.getElementById("js-timezone").required = true;
                    document.getElementById("datepicker").required = true;
                    document.getElementById("timepicker").required = true;
                    document.getElementById("mo_saml_query").required = false;

                    jQuery("#datepicker").datepicker("setDate", +1);
                    jQuery('#timepicker').timepicker('option', 'minTime', '00:00');

                } else {
                    jQuery("#call_setup_dets").hide();
                    document.getElementById("timepicker").required = false;
                    document.getElementById("datepicker").required = false;
                    document.getElementById("js-timezone").required = false;
                    document.getElementById("mo_saml_query").required = true;
                }
            });
            jQuery( "#datepicker" ).datepicker({
                minDate: +1,
                dateFormat: 'M dd, yy'
            });
        });

        jQuery('#timepicker').timepicker({
            timeFormat: 'HH:mm',
            interval: 30,
            minTime: new Date(),
            disableTextInput: true,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            forceRoundTime: true
        });

        function mo_saml_valid_query(f) {
            !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
                /[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
        }
    </script>

<?php }

function mo_saml_display_attrs_list(){
    $idp_attrs = get_option('mo_saml_test_config_attrs');
    $idp_attrs = maybe_unserialize($idp_attrs);
    if(!empty($idp_attrs)){ ?>
        <div class="mo_saml_support_layout" style="padding-bottom:20px; padding-right:5px;">
        <h3><?php _e('Attributes sent by the Identity Provider','miniorange-saml-20-single-sign-on');?>:</h3>
                <div>
                    <table style="border-collapse:collapse;border-spacing:0;table-layout: fixed; width: 96%;background-color:#ffffff;">
                    <tr style="text-align:center;"><td style="font-weight:bold;border:0px solid #949090;padding:2%; width:65%;background-color:#0085ba;color:white;"><?php _e('ATTRIBUTE NAME','miniorange-saml-20-single-sign-on');?></td><td style="font-weight:bold;padding:2%;border-left:1px solid #ffffff; border-right:1px solid #0085ba; word-wrap:break-word; width:35%;background-color:#0085ba;color:white;"><?php _e('ATTRIBUTE VALUE','miniorange-saml-20-single-sign-on');?></td></tr>

                            <?php foreach($idp_attrs as $attr_name => $values){ ?>
                                <tr style="text-align:center;"><td style="font-weight:bold;border:1px solid #949090;padding:2%; word-wrap:break-word;"> <?php echo $attr_name; ?></td>
                                <td style="padding:2%;border:1px solid #949090; word-wrap:break-word;"> <?php echo implode("<hr/>",$values); ?> </td>
                                </tr>
                            <?php } ?>
                        
                        </table>
                        <br/>
                        <p style="text-align:center;"><input type="button" class="button-primary" value="<?php _e('Clear Attributes List','miniorange-saml-20-single-sign-on');?>" onclick="document.forms['attrs_list_form'].submit();"></p>
                        <div style="padding-right:8px;">
                        <p><b><?php _e('NOTE','miniorange-saml-20-single-sign-on');?> :</b> <?php _e('Please clear this list after configuring the plugin to hide your confidential attributes.','miniorange-saml-20-single-sign-on');?><br/>
                        <?php _e('Click on <b>Test configuration</b> in <b>Service Provider Setup</b> tab to populate the list again.','miniorange-saml-20-single-sign-on');?></p>
                        </div>
                        <form method="post" action="" id="attrs_list_form">
                        <?php wp_nonce_field('clear_attrs_list'); ?>
                        <input type="hidden" name="option" value="clear_attrs_list">
                        </form>
                        </div>
        </div>
        <?php
    }
}

function miniorange_demo_request_saml(){
    $mo_saml_admin_email = get_option('mo_saml_admin_email');
    ?>
    <div style="background-color: #FFFFFF; border: 1px solid #CCCCCC; padding: 10px 10px 10px 10px;">
    <h3><?php _e('Request for Demo','miniorange-saml-20-single-sign-on');?></h3><hr>
    <?php esc_html_e('Want to try out the paid features before purchasing the license? Just let us know which plan you\'re interested in and we will setup a demo for you.','miniorange-saml-20-single-sign-on');?>
    <br/><br/>
    <div class="demo-request-div">
    <?php   $support_Email = "samlsupport@xecurify.com";
            $support_Email = esc_url( sprintf( 'mailto:%s', antispambot( sanitize_email( $support_Email ) ) ), array( 'mailto' ) );
    ?>
    <?php printf(esc_html__('The demo credentials will be sent to the below mentioned email address. You can configure the plugin with your Identity Provider and test out complete functionality right away. Feel free to contact us at %1$s incase of any issues or concerns.','miniorange-saml-20-single-sign-on'),sprintf('<a style="color:blue" href="%s">%s</a>',$support_Email,esc_html__('samlsupport@xecurify.com','miniorange-saml-20-single-sign-on'))); ?>
    </div>
    <div class="mo_demo_layout" style="padding-bottom:20px; padding-right:5px;">
    
    <form method="post" action="">
    <?php wp_nonce_field("mo_saml_demo_request_option");?>
        <input type="hidden" name="option" value="mo_saml_demo_request_option"/>
        <table cellpadding="4" cellspacing="4">
        <tr>
        <td><strong><?php _e('Email','miniorange-saml-20-single-sign-on');?><p style="display:inline;color:red;">*</p> :</strong></td>
        <td><input type="text" name="mo_saml_demo_email" placeholder="<?php _e('We will use this email to setup the demo for you','miniorange-saml-20-single-sign-on');?>" required style="width:350px" value="<?php echo !empty($mo_saml_admin_email) ? $mo_saml_admin_email: ''; ?>"></td>
        </tr>

        <?php
            $license_plans = mo_saml_license_plans::$license_plans;
        ?>
        <tr>
        <td><strong><?php _e('Request a demo for','miniorange-saml-20-single-sign-on');?><p style="display:inline;color:red;">*</p> :</strong></td>
        <td><select name="mo_saml_demo_plan" id="mo_saml_demo_plan" style="width:350px" required onchange="mo_saml_show_description();">
        <option hidden disabled selected value="">--<?php _e('Select a license plan','miniorange-saml-20-single-sign-on');?>--</option>
        <?php
        foreach($license_plans as $key => $value){
            ?>
            <option value="<?php echo $key;?>"><?php echo $value;?>
            <?php
        }
        ?>
        </select></td>
        </tr>
        <tr id="demo_description">
        <td><strong><?php _e('Description','miniorange-saml-20-single-sign-on');?> :</strong></td>
        <td><textarea type="text" name="mo_saml_demo_description" style="resize: vertical; width:350px; height:100px;" rows="4" placeholder="<?php _e('Write us about your requirement.','miniorange-saml-20-single-sign-on');?>"></textarea></td>
        </tr>
        <tr id="add-on-list">
        <?php 
            $addons = mo_saml_options_addons::$ADDON_TITLE;
        ?>
        <td colspan="2">
        <p><strong><?php _e('Select the Add-ons you are interested in (Optional)','miniorange-saml-20-single-sign-on');?> :</strong></p>
        <p><i><strong>(<?php _e('Note','miniorange-saml-20-single-sign-on');?>: </strong> <?php _e('All-Inclusive plan entitles all the addons in the license cost itself.','miniorange-saml-20-single-sign-on');?> )</i></p>
        <div style="padding-left:20px;">
        <?php
        foreach($addons as $key => $value){?>
            <input type="checkbox" style="margin-top:2px;margin-bottom:2px;" name="<?php echo $key; ?>" value="true"> <?php echo $value ?><br/>
            <?php
        }
        ?>
        </div></td>
        </tr>
        <tr><td><br/></td></tr>
        <tr style="text-align:center;"><td colspan="2" ><input type="submit" value="<?php _e('Send Request','miniorange-saml-20-single-sign-on');?>" class="button button-primary button-large"/></td></tr>
        </table>
    </form>
    </div>
    </div>
<?php
}





function mo_saml_add_query_arg($query_arg, $url){
    if(strpos($url, 'mo_saml_licensing') !== false){
        $url = str_replace('mo_saml_licensing', 'mo_saml_settings', $url);
    }
    $url = add_query_arg($query_arg, $url);
    return $url;
}

function mo_saml_miniorange_generate_metadata($download=false) {

    $sp_base_url = get_option( 'mo_saml_sp_base_url' );
    if ( empty( $sp_base_url ) ) {
        $sp_base_url = site_url();
    }
    if ( substr( $sp_base_url, - 1 ) == '/' ) {
        $sp_base_url = substr( $sp_base_url, 0, - 1 );
    }
    $sp_entity_id = get_option( 'mo_saml_sp_entity_id' );
    if ( empty( $sp_entity_id ) ) {
        $sp_entity_id = $sp_base_url . '/wp-content/plugins/miniorange-saml-20-single-sign-on/';
    }

    $entity_id   = $sp_entity_id;
    $acs_url     = $sp_base_url . '/';

    if(ob_get_contents())
        ob_clean();
    header( 'Content-Type: text/xml' );
    if($download)
            header('Content-Disposition: attachment; filename="Metadata.xml"');
    echo '<?xml version="1.0"?>
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" validUntil="2022-10-28T23:59:59Z" cacheDuration="PT1446808792S" entityID="' . $entity_id . '">
  <md:SPSSODescriptor AuthnRequestsSigned="false" WantAssertionsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    <md:NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress</md:NameIDFormat>
    <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="' . $acs_url . '" index="1"/>
  </md:SPSSODescriptor>
  <md:Organization>
    <md:OrganizationName xml:lang="en-US">miniOrange</md:OrganizationName>
    <md:OrganizationDisplayName xml:lang="en-US">miniOrange</md:OrganizationDisplayName>
    <md:OrganizationURL xml:lang="en-US">http://miniorange.com</md:OrganizationURL>
  </md:Organization>
  <md:ContactPerson contactType="technical">
    <md:GivenName>miniOrange</md:GivenName>
    <md:EmailAddress>info@xecurify.com</md:EmailAddress>
  </md:ContactPerson>
  <md:ContactPerson contactType="support">
    <md:GivenName>miniOrange</md:GivenName> 
    <md:EmailAddress>info@xecurify.com</md:EmailAddress>
  </md:ContactPerson>
</md:EntityDescriptor>';
    exit;

}

?>