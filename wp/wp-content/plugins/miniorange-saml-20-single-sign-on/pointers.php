<?php

require_once "mo_saml_settings_page.php";

$pointers = array();
$tab= 'default';
$test_status = get_option('MO_SAML_TEST_STATUS');
if(array_key_exists('tab',$_GET))
    $tab = $_GET['tab'];

if($tab == 'default' && get_option('plugin_wise_tour_initiated')==1)
{

    $guide_pointer_div = get_option('mo_saml_identity_provider_identifier_name')?'#selected_idp_div':'#mo_saml_idps_grid_div';

    $pointers['default-miniorange-select-your-idp'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Select ADFS as IDP (Step 1 of 9)','miniorange-saml-20-single-sign-on' ) ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Choose ADFS as your IDP, and refer to the setup guide  for complete instructions.','miniorange-saml-20-single-sign-on' ) ),
        'anchor_id' => $guide_pointer_div,
        'isdefault' => 'yes',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['default-miniorange-sp-metadata-url'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Service Provider Metadata URL (Step 2 of 9)','miniorange-saml-20-single-sign-on' ) ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Use this Metadata URL or file to configure ADFS.','miniorange-saml-20-single-sign-on' ) ),
        'anchor_id' => '#metadata_url',
        'isdefault' => 'yes',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['default-miniorange-upload-metadata'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Upload your metadata (Step 3 of 9)','miniorange-saml-20-single-sign-on' ) ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Once you have configured ADFS, you can use this button to upload the metadata received from ADFS.','miniorange-saml-20-single-sign-on' ) ),
        'anchor_id' => '#upload-metadata',
        'isdefault' => 'yes',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['default-miniorange-test-configuration'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Check your configurations (Step 4 of 9)' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'After uploading the metadata from ADFS, use this button to test the configurations between ADFS and WordPress.' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#test_config',
        'isdefault' => 'yes',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['default-miniorange-attribute-mapping'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Configure Attribute Mapping (Step 5 of 9)','miniorange-saml-20-single-sign-on' ) ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'While auto registering the users in your WordPress site these attributes will automatically get mapped to your WordPress user details.' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#miniorange-attribute-mapping',
        'isdefault' => 'yes',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );

    $pointers['default-miniorange-role-mapping'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Configure Role Mapping (Step 6 of 9)','miniorange-saml-20-single-sign-on' ) ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Select roles to be assigned to users when they are created in Wordpress.','miniorange-saml-20-single-sign-on' ) ),
        'anchor_id' => '#miniorange-role-mapping',
        'isdefault' => 'yes',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['default-minorange-use-widget'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Available with this version (Step 7 of 9)','miniorange-saml-20-single-sign-on' ) ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Add a widget to your Wordpress page and test out the SSO.','miniorange-saml-20-single-sign-on' ) ),
        'anchor_id' => '#minorange-use-widget',
        'isdefault' => 'yes',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['default-miniorange-addons'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Add-Ons (Step 8 of 9)' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Checkout all our add-ons to extend the SSO functionality.' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#miniorange-addons',
        'isdefault' => 'yes',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['default-miniorange-support-pointer'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'We are here!!' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Get in touch with us and we will help you setup the plugin in no time.','miniorange-saml-20-single-sign-on' ) ),
        'anchor_id' => '#mo_saml_support_layout',
        'isdefault' => 'yes',
        'edge'      => 'right',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
}
if(get_option('service_provider_setup_tour_initiated')){
    delete_option('service_provider_setup_tour_initiated');

    $guide_pointer_div = get_option('mo_saml_identity_provider_identifier_name')?'#selected_idp_div':'#mo_saml_idps_grid_div';


    $pointers['miniorange-select-your-idp'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Select your IDP','miniorange-saml-20-single-sign-on' ) ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Choose your IDP from the list of IDPs, and refer to the setup guides to proceed further' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => $guide_pointer_div,
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['miniorange-upload-metadata'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Upload your metadata' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'If you have a metadata URL or file provided by your IDP, click on this button.' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#upload-metadata',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['miniorange-upload-metadata'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Upload your metadata' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'If you have a metadata URL or file provided by your IDP, click on this button. You can configure the plugin manually as well' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#upload-metadata',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );

    if(mo_saml_is_sp_configured() || get_option('saml_x509_certificate')){
        $pointers['miniorange-test-configuration'] = array(
            'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Check your configurations' ,'miniorange-saml-20-single-sign-on') ),
            'content'   => sprintf( '<p>%s</p>', esc_html__( 'This will test if the configurations on IDP and SP are correct' ,'miniorange-saml-20-single-sign-on') ),
            'anchor_id' => '#test_config',
            'edge'      => 'left',
            'align'     => 'left',
            'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
        );
        $pointers['export-import-config'] = array(
            'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Export Configuration' ,'miniorange-saml-20-single-sign-on') ),
            'content'   => sprintf( '<p>%s</p>', esc_html__( 'If you are having trouble setting up the plugin, Export the configurations and mail us at info@xecurify.com.' ,'miniorange-saml-20-single-sign-on') ),
            'anchor_id' => '#export-import-config',
            'edge'      => 'left',
            'align'     => 'left',
            'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
        );
    }

    $pointers['configure-service-restart-tour'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Click when you need me!' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Revisit tour' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#configure-service-restart-tour',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );

}
if($tab == 'config'){

    $pointers['miniorange-sp-metadata-url'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Service Provider Metadata URL' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Use this Metadata URL or file to configure your IDP.' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#metadata_url',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['metadata_manual'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Service Provider Metadata URLs' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'If your IDP does not support metadata URL or file, you can even manually configure your IDP using the information given here' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#metadata_manual',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );


    $pointers['identity-provider-restart-tour'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Click when you need me!' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Revisit tour' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#identity-provider-restart-tour',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );

}
if($tab == 'opt'){

    $pointers['miniorange-attribute-mapping'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Configure Attribute Mapping' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'While auto registering the users in your WordPress site these attributes will automatically get mapped to your WordPress user details.' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#miniorange-attribute-mapping',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );

    $pointers['miniorange-role-mapping'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Configure Role Mapping' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Select roles to be assigned to users when they are created in Wordpress.' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#miniorange-role-mapping',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );


    $pointers['attribute-mapping-restart-tour'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Click when you need me!' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Revisit tour' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#attribute-mapping-restart-tour',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );


}

if( $tab =='general'){
    $pointers['minorange-use-widget'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Available with this version' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Add a widget to your Wordpress page and test out the SSO.' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#minorange-use-widget',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
    $pointers['miniorange-auto-redirect'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Premium Feature' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Redirect the users to your IdP if user not logged in.Protects your complete site from not logged in Users' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#miniorange-auto-redirect',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );


    $pointers['miniorange-auto-redirect-login-page'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Premium Feature' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Automatically redirect the user to the Identity Provider when they land on the WordPress Login Page.' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#miniorange-auto-redirect-login-page',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );

    $pointers['miniorange-short-code'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Premium Feature' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Add a shortcode to any page and SSO into your website' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#miniorange-short-code',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
	
	$pointers['miniorange-redirection-sso-restart-tour'] = array(
        'title'     => sprintf( '<h3>%s</h3>', esc_html__( 'Click when you need me!' ,'miniorange-saml-20-single-sign-on') ),
        'content'   => sprintf( '<p>%s</p>', esc_html__( 'Revisit tour' ,'miniorange-saml-20-single-sign-on') ),
        'anchor_id' => '#miniorange-redirection-sso-restart-tour',
        'edge'      => 'left',
        'align'     => 'left',
        'where'     => array( 'toplevel_page_mo_saml_settings' ) // <-- Please note this
    );
}






return $pointers;