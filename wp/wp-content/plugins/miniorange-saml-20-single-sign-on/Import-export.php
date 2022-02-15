<?php
require_once dirname(__FILE__) . '/includes/lib/mo-saml-options-enum.php';
add_action( 'admin_init', 'mo_saml_miniorange_import_export');
define( "Tab_Class_Names", serialize( array(
	"SSO_Login"         => 'mo_saml_options_enum_sso_loginMoSAML',
	"Identity_Provider" => 'mo_saml_options_enum_identity_providerMoSAML',
	"Service_Provider"  => 'mo_saml_options_enum_service_providerMoSAML',
	"Attribute_Mapping" => 'mo_saml_options_enum_attribute_mappingMoSAML',
	"Role_Mapping"      => 'mo_saml_options_enum_role_mappingMoSAML',
    "Test_Configuration" => 'mo_saml_options_test_configuration'
) ) );

/**
 *Function to display block of UI for export Import
 */
function mo_saml_miniorange_keep_configuration_saml() {
	echo '<div class="mo_saml_support_layout" id="mo_saml_keep_configuration_intact">
        <div>
        <h3>' . __('Keep configuration Intact','miniorange-saml-20-single-sign-on') . '</h3>
        <form name="f" method="post" action="" id="settings_intact">';

    wp_nonce_field('mo_saml_keep_settings_on_deletion');
	echo '<input type="hidden" name="option" value="mo_saml_keep_settings_on_deletion"/>
		<label class="switch">
		<input type="checkbox" name="mo_saml_keep_settings_intact" ';
        echo checked(get_option('mo_saml_keep_settings_on_deletion')=='true');
		echo 'onchange="document.getElementById(\'settings_intact\').submit();"/>
		<span class="slider round"></span>
					</label><span style="padding-left:5px">' .
        __('Enabling this would keep your settings intact when plugin is uninstalled','miniorange-saml-20-single-sign-on') . '</span>
        <p><b>' . __('Please enable this option when you are updating to a Premium version.','miniorange-saml-20-single-sign-on') . '</b></p>
        </form>
        </div>
        <br /><br />
	</div>';
}

function mo_saml_display_add_ons_iframe($add_on_name){
    ?>
    <script type='text/javascript'>
        !function(a,b){"use strict";function c(){if(!e){e=!0;var a,c,d,f,g=-1!==navigator.appVersion.indexOf("MSIE 10"),h=!!navigator.userAgent.match(/Trident.*rv:11\./),i=b.querySelectorAll("iframe.wp-embedded-content");for(c=0;c<i.length;c++){if(d=i[c],!d.getAttribute("data-secret"))f=Math.random().toString(36).substr(2,10),d.src+="#?secret="+f,d.setAttribute("data-secret",f);if(g||h)a=d.cloneNode(!0),a.removeAttribute("security"),d.parentNode.replaceChild(a,d)}}}var d=!1,e=!1;if(b.querySelector)if(a.addEventListener)d=!0;if(a.wp=a.wp||{},!a.wp.receiveEmbedMessage)if(a.wp.receiveEmbedMessage=function(c){var d=c.data;if(d)if(d.secret||d.message||d.value)if(!/[^a-zA-Z0-9]/.test(d.secret)){var e,f,g,h,i,j=b.querySelectorAll('iframe[data-secret="'+d.secret+'"]'),k=b.querySelectorAll('blockquote[data-secret="'+d.secret+'"]');for(e=0;e<k.length;e++)k[e].style.display="none";for(e=0;e<j.length;e++)if(f=j[e],c.source===f.contentWindow){if(f.removeAttribute("style"),"height"===d.message){if(g=parseInt(d.value,10),g>1e3)g=1e3;else if(~~g<200)g=200;f.height=g}if("link"===d.message)if(h=b.createElement("a"),i=b.createElement("a"),h.href=f.getAttribute("src"),i.href=d.value,i.host===h.host)if(b.activeElement===f)a.top.location.href=d.value}else;}},d)a.addEventListener("message",a.wp.receiveEmbedMessage,!1),b.addEventListener("DOMContentLoaded",c,!1),a.addEventListener("load",c,!1)}(window,document);

    </script><iframe  sandbox="allow-scripts" security="restricted" src="<?php echo $add_on_name;?>" width="98%" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" class="wp-embedded-content"></iframe>

    <?php

}

/**
 *Function iterates through the enum to create array of values and converts to JSON and lets user download the file
 */
function mo_saml_miniorange_import_export($test_config_screen=false, $json_in_string=false) {

    if($test_config_screen)
        $_POST['option'] = 'mo_saml_export';

	if ( array_key_exists( "option", $_POST )  ) {
	    if($_POST['option']=='mo_saml_export'){
				if($test_config_screen and $json_in_string)
					$export_referer = check_admin_referer('mo_saml_contact_us_query_option');
				else
					$export_referer = check_admin_referer('mo_saml_export');

				if($export_referer){
					$tab_class_name = maybe_unserialize(Tab_Class_Names);
					$configuration_array = array();
					foreach ($tab_class_name as $key => $value) {
						$configuration_array[$key] = mo_saml_get_configuration_array($value);
					}
					$configuration_array["Version_dependencies"] = mo_saml_get_version_informations();
					$version = phpversion();
					if(substr($version,0 ,3) === '5.3'){
						$json_string=(json_encode($configuration_array, JSON_PRETTY_PRINT));
					} else {
						$json_string=(json_encode($configuration_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
					}

					if($json_in_string)
						return $json_string;
					header("Content-Disposition: attachment; filename=miniorange-saml-config.json");
					echo $json_string;
					exit;
				}
	    }
	    else if($_POST['option']=='mo_saml_keep_settings_on_deletion' and check_admin_referer('mo_saml_keep_settings_on_deletion')) {

            if (array_key_exists('mo_saml_keep_settings_intact', $_POST))
                update_option('mo_saml_keep_settings_on_deletion', 'true');
            else
                update_option('mo_saml_keep_settings_on_deletion', '');

        }

        return;


	}





}

function mo_saml_get_configuration_array($class_name ) {
	$class_object = call_user_func( $class_name . '::getConstants' );
	$mo_array = array();
	foreach ( $class_object as $key => $value ) {
		$mo_option_exists=get_option($value);

		if($mo_option_exists){
            $mo_option_exists = maybe_unserialize($mo_option_exists);
			$mo_array[ $key ] = $mo_option_exists;

		}

	}

	return $mo_array;
}

function mo_saml_update_configuration_array($configuration_array ) {
	$tab_class_name = maybe_unserialize( Tab_Class_Names );
	foreach ( $tab_class_name as $tab_name => $class_name ) {
		foreach ( $configuration_array[ $tab_name ] as $key => $value ) {
			$option_string = constant( "$class_name::$key" );
			$mo_option_exists = get_option($option_string);
			if ( $mo_option_exists) {
				if(is_array($value))
					$value = serialize($value);
				update_option( $option_string, $value );
			}
		}
	}

}

function mo_saml_get_version_informations(){
	$array_version = array();
	$array_version["Plugin_version"] = mo_saml_options_plugin_constants::Version;
	$array_version["PHP_version"] = phpversion();
	$array_version["Wordpress_version"] = get_bloginfo('version');
	$array_version["OPEN_SSL"] = mo_saml_is_openssl_installed();
	$array_version["CURL"] = mo_saml_is_curl_installed();
    $array_version["ICONV"] = mo_saml_is_iconv_installed();
    $array_version["DOM"] = mo_saml_is_dom_installed();

	return $array_version;

}

