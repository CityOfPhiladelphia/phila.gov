<?php
include_once dirname(__FILE__) . '/Utilities.php';
include_once dirname(__FILE__) . '/Response.php';
require_once dirname(__FILE__) . '/includes/lib/encryption.php';
require_once dirname(__FILE__) . '/includes/lib/mo-saml-options-enum.php';
include_once 'xmlseclibs.php';
use \RobRichards\XMLSecLibs\MoXMLSecurityKey;

include_once 'Import-export.php';
class mo_login_wid extends WP_Widget {
	public function __construct() {
		$identityName = get_option('saml_identity_name');
		parent::__construct(
	 		'Saml_Login_Widget',
			'Login with ' . $identityName,
			array( 'description' => __( 'This is a miniOrange SAML login widget.','miniorange-saml-20-single-sign-on' ),
					'customize_selective_refresh' => true,
				)
		);
	 }


	public function widget( $args, $instance ) {
		extract( $args );

		$wid_title = apply_filters( 'widget_title', $instance['wid_title'] );

		echo $args['before_widget'];
		if ( ! empty( $wid_title ) )
			echo $args['before_title'] . $wid_title . $args['after_title'];
			$this->loginForm();
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wid_title'] = htmlspecialchars( $new_instance['wid_title'] );
		return $instance;
	}


	public function form( $instance ) {
		$wid_title = '';
		if(array_key_exists('wid_title', $instance))
			$wid_title = $instance[ 'wid_title' ];
		?>
		<p><label for="<?php echo $this->get_field_id('wid_title'); ?>"><?php _e('Title:','miniorange-saml-20-single-sign-on'); ?> </label>
		<input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo $wid_title; ?>" />
		</p>
		<?php
	}

	public function loginForm(){
		if(!is_user_logged_in()){
		?>
		<script>
		function submitSamlForm(){ document.getElementById("miniorange-saml-sp-sso-login-form").submit(); }
		</script>
		<form name="miniorange-saml-sp-sso-login-form" id="miniorange-saml-sp-sso-login-form" method="post" action="">
		<input type="hidden" name="option" value="saml_user_login" />

		<font size="+1" style="vertical-align:top;"> </font><?php
		$identity_provider = get_option('saml_identity_name');
		$saml_x509_certificate=get_option('saml_x509_certificate');
		if(!empty($identity_provider) && !empty($saml_x509_certificate)){
				echo '<a href="#" onClick="submitSamlForm()">Login with ' . $identity_provider . '</a></form>';
		}else
			_e('Please configure the miniOrange SAML Plugin first.','miniorange-saml-20-single-sign-on');

		if( ! $this->mo_saml_check_empty_or_null_val(get_option('mo_saml_redirect_error_code')))
		{

			echo '<div></div><div title="Login Error"><font color="red">' . __('We could not sign you in. Please contact your Administrator.','miniorange-saml-20-single-sign-on') . '</font></div>';

				delete_option('mo_saml_redirect_error_code');
				delete_option('mo_saml_redirect_error_reason');
		}

		?>



			</ul>
		</form>
		<?php
		} else {
		$current_user = wp_get_current_user();
		$link_with_username = sprintf(__('Hello, %s','miniorange-saml-20-single-sign-on'),$current_user->display_name);
		?>
		<?php echo $link_with_username;?> | <a href="<?php echo wp_logout_url(saml_get_current_page_url()); ?>" title="<?php _e('Logout','miniorange-saml-20-single-sign-on');?>"><?php _e('Logout','miniorange-saml-20-single-sign-on');?></a></li>
		<?php
		}
	}

	public function mo_saml_check_empty_or_null_val( $value ) {
	if( ! isset( $value ) || empty( $value ) ) {
		return true;
	}
	return false;
	}
}

function mo_saml_login_validate(){

	if(isset($_REQUEST['option']) && $_REQUEST['option'] == 'mosaml_metadata'){
		mo_saml_miniorange_generate_metadata();
	}
	if(isset($_REQUEST['option']) && $_REQUEST['option'] == 'export_configuration'){
        if ( current_user_can( 'manage_options' ) )
            mo_saml_miniorange_import_export(true);
        exit;
    }
	if(isset($_REQUEST['option']) && $_REQUEST['option']=='mo_fix_certificate' && is_user_logged_in() && current_user_can('manage_options')){

	    $saml_required_certificate = get_option('mo_saml_required_certificate');
	    $saml_certificate =  maybe_unserialize(get_option(mo_saml_options_enum_service_providerMoSAML::X509_certificate));
	    $saml_certificate[0] = Utilities::sanitize_certificate($saml_required_certificate);
	    update_option(mo_saml_options_enum_service_providerMoSAML::X509_certificate,$saml_certificate);
        wp_redirect('?option=testConfig');exit;
    }
    if(isset($_REQUEST['option']) && $_REQUEST['option']=='mo_fix_entity_id' && is_user_logged_in() && current_user_can('manage_options')){

        $saml_required_issuer = get_option('mo_saml_required_issuer');
        update_option(mo_saml_options_enum_service_providerMoSAML::Issuer,$saml_required_issuer);
        wp_redirect('?option=testConfig');exit;
    }
	if((isset($_REQUEST['option']) && $_REQUEST['option'] == 'saml_user_login') || (isset($_REQUEST['option']) && $_REQUEST['option'] == 'testConfig')){

		if($_REQUEST['option'] == 'testConfig'){
			if(!is_user_logged_in() || is_user_logged_in() && !current_user_can('manage_options')){
				return;
			}
		}else{
		    if(is_user_logged_in())
		        return;
        }

	    if(mo_saml_is_sp_configured()) {
			if($_REQUEST['option'] == 'testConfig')
				$sendRelayState = 'testValidate';
			else if ( isset( $_REQUEST['redirect_to']) )
				$sendRelayState = htmlspecialchars($_REQUEST['redirect_to']);
			else
				$sendRelayState = saml_get_current_page_url();

			$sp_base_url = get_option( 'mo_saml_sp_base_url' );
			if ( empty( $sp_base_url ) ) {
				$sp_base_url = site_url();
			}

			$ssoUrl = htmlspecialchars_decode(get_option("saml_login_url"));
			$force_authn = get_option('mo_saml_force_authentication');
			$acsUrl = site_url()."/";
			$issuer = site_url().'/wp-content/plugins/miniorange-saml-20-single-sign-on/';
			$sp_entity_id = get_option('mo_saml_sp_entity_id');
			if(empty($sp_entity_id)) {
				$sp_entity_id = $sp_base_url.'/wp-content/plugins/miniorange-saml-20-single-sign-on/';
			}
			$samlRequest = Utilities::createAuthnRequest($acsUrl, $sp_entity_id, $force_authn);


			$redirect = $ssoUrl;

			if (strpos($ssoUrl,'?') !== false) {
				$redirect .= '&';
			} else {
				$redirect .= '?';
			}
			$redirect .= 'SAMLRequest=' . $samlRequest . '&RelayState=' . urlencode($sendRelayState);


			header('Location: '.$redirect);
			exit();
		}
	}
	if( array_key_exists('SAMLResponse', $_POST) && !empty($_POST['SAMLResponse']) ) {

		$samlResponse = htmlspecialchars($_POST['SAMLResponse']);

		if(array_key_exists('RelayState', $_POST) && !empty( $_POST['RelayState'] ) && $_POST['RelayState'] != '/') {
			$relayState = htmlspecialchars($_POST['RelayState']);
		} else {
			$relayState = '';
		}
        update_option('MO_SAML_RESPONSE',$samlResponse);

        $samlResponse = base64_decode($samlResponse);

		$document = new DOMDocument();
		$document->loadXML($samlResponse);
		$samlResponseXml = $document->firstChild;

		$doc = $document->documentElement;
		$xpath = new DOMXpath($document);
		$xpath->registerNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
		$xpath->registerNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');

		$status = $xpath->query('/samlp:Response/samlp:Status/samlp:StatusCode', $doc);
		$statusString = $status->item(0)->getAttribute('Value');
		$StatusMessage=$xpath->query('/samlp:Response/samlp:Status/samlp:StatusMessage', $doc)->item(0);
		if(!empty($StatusMessage))
			$StatusMessage = $StatusMessage->nodeValue;

		$statusArray = explode(':',$statusString);
		if(array_key_exists(7, $statusArray)){
			$status = $statusArray[7];
		}
		if($relayState=='displaySAMLResponse'){
			mo_saml_show_SAML_log($samlResponse,$relayState);
		}

		if($status!="Success"){
			mo_saml_show_status_error($status,$relayState,$StatusMessage);
		}

		$certFromPlugin = maybe_unserialize(get_option('saml_x509_certificate'));

		$acsUrl = site_url() .'/';
		$samlResponse = new SAML2_Response($samlResponseXml);
		$responseSignatureData = $samlResponse->	getSignatureData();
		$assertionSignatureData = current($samlResponse->getAssertions())->getSignatureData();

		if(empty($assertionSignatureData) && empty($responseSignatureData) ) {

			if($relayState=='testValidate'){

				$Error_message=mo_saml_options_error_constants::Error_no_certificate;
				$Cause_message = mo_saml_options_error_constants::Cause_no_certificate;
			echo '<div style="font-family:Calibri;padding:0 3%;">
			<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">' . __( 'ERROR','miniorange-saml-20-single-sign-on') . '</div>
			<div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>' . __('Error','miniorange-saml-20-single-sign-on') . ': '.$Error_message.' </strong></p>
			
			<p><strong>'. __('Possible Cause','miniorange-saml-20-single-sign-on') . ': '.$Cause_message.'</strong></p>
			
			</div></div>';
			mo_saml_download_logs($Error_message,$Cause_message);

			exit;
			}
			else
			{
			wp_die(__('We could not sign you in. Please contact administrator','miniorange-saml-20-single-sign-on'),'Error: Invalid SAML Response');
			}
		}
		if(is_array($certFromPlugin)) {
				foreach ($certFromPlugin as $key => $value) {
					$certfpFromPlugin = MoXMLSecurityKey::getRawThumbprint($value);

					$certfpFromPlugin = mo_saml_convert_to_windows_iconv($certfpFromPlugin);
					$certfpFromPlugin = preg_replace('/\s+/', '', $certfpFromPlugin);
					if(!empty($responseSignatureData)) {
						$validSignature = Utilities::processResponse($acsUrl, $certfpFromPlugin, $responseSignatureData, $samlResponse, $key, $relayState);
					}
					if(!empty($assertionSignatureData)) {
						$validSignature = Utilities::processResponse($acsUrl, $certfpFromPlugin, $assertionSignatureData, $samlResponse, $key, $relayState);
					}
					if($validSignature)
						break;
				}
			} else {
				$certfpFromPlugin = MoXMLSecurityKey::getRawThumbprint($certFromPlugin);
				$certfpFromPlugin = mo_saml_convert_to_windows_iconv($certfpFromPlugin);
				$certfpFromPlugin = preg_replace('/\s+/', '', $certfpFromPlugin);
				if(!empty($responseSignatureData)) {
					$validSignature = Utilities::processResponse($acsUrl, $certfpFromPlugin, $responseSignatureData, $samlResponse, 0, $relayState);
				}

				if(!empty($assertionSignatureData)) {
					$validSignature = Utilities::processResponse($acsUrl, $certfpFromPlugin, $assertionSignatureData, $samlResponse, 0, $relayState);
				}
			}

			if($responseSignatureData)
				$saml_required_certificate=$responseSignatureData['Certificates'][0];
			elseif($assertionSignatureData)
				$saml_required_certificate=$assertionSignatureData['Certificates'][0];
            update_option('mo_saml_required_certificate',$saml_required_certificate);
		if(!$validSignature) {
			if($relayState=='testValidate'){

				$Error_message=mo_saml_options_error_constants::Error_wrong_certificate;
				$Cause_message = mo_saml_options_error_constants::Cause_wrong_certificate;
				$pem = "-----BEGIN CERTIFICATE-----<br>" .
					chunk_split($saml_required_certificate, 64) .
					"<br>-----END CERTIFICATE-----";
				echo '<div style="font-family:Calibri;padding:0 3%;">';
			echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
			<div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>' . __('Error','miniorange-saml-20-single-sign-on') . ': '.$Error_message.' </strong></p>
			
			<p><strong>' . __('Possible Cause','miniorange-saml-20-single-sign-on'). ': '.$Cause_message.'</strong></p>
            <div>
			    <ol style="text-align: center">
                    <form method="post" action="" name="mo_fix_certificate" id="mo_fix_certificate">';
                    wp_nonce_field('mo_fix_certificate');
				    echo '<input type="hidden" name="option" value="mo_fix_certificate" />
				    <input type="submit" class="miniorange-button" style="width: 55%" value="Fix Issue">
				    </form>
                </ol> 
             </div>

					</div>
                    </div>';
            mo_saml_download_logs($Error_message,$Cause_message);
					exit;
	}
		else
		{
			wp_die(__('We could not sign you in. Please contact administrator','miniorange-saml-20-single-sign-on'),'Error: Invalid SAML Response');
		}
		}

		$sp_base_url = get_option( 'mo_saml_sp_base_url' );
		if ( empty( $sp_base_url ) ) {
			$sp_base_url = site_url();
		}
		// verify the issuer and audience from saml response
		$issuer = get_option('saml_issuer');
		$spEntityId = get_option('mo_saml_sp_entity_id');
		if(empty($spEntityId)) {
			$spEntityId = $sp_base_url.'/wp-content/plugins/miniorange-saml-20-single-sign-on/';
		}
		Utilities::validateIssuerAndAudience($samlResponse,$spEntityId, $issuer, $relayState);

		$ssoemail = current(current($samlResponse->getAssertions())->getNameId());
		$attrs = current($samlResponse->getAssertions())->getAttributes();
		$attrs['NameID'] = array("0" => $ssoemail);
		$sessionIndex = current($samlResponse->getAssertions())->getSessionIndex();

		mo_saml_checkMapping($attrs,$relayState,$sessionIndex);
	}

	if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'readsamllogin' ) !== false ) {
		require_once dirname(__FILE__) . '/includes/lib/encryption.php';

		if(isset($_POST['STATUS']) && $_POST['STATUS'] == 'ERROR')
		{
			update_option('mo_saml_redirect_error_code', htmlspecialchars($_POST['ERROR_REASON']));
			update_option('mo_saml_redirect_error_reason' , htmlspecialchars($_POST['ERROR_MESSAGE']));
		}
		else if(isset($_POST['STATUS']) && $_POST['STATUS'] == 'SUCCESS'){
			$redirect_to = '';
			if(isset($_REQUEST['redirect_to']) && !empty($_REQUEST['redirect_to']) && $_REQUEST['redirect_to'] != '/') {
				$redirect_to = htmlentities($_REQUEST['redirect_to']);
			}

			delete_option('mo_saml_redirect_error_code');
			delete_option('mo_saml_redirect_error_reason');

			try {

				$emailAttribute = get_option('saml_am_email');
				$usernameAttribute = get_option('saml_am_username');
				$firstName = get_option('saml_am_first_name');
				$lastName = get_option('saml_am_last_name');
				$groupName = get_option('saml_am_group_name');
				$defaultRole = get_option('saml_am_default_user_role');
				$dontAllowUnlistedUserRole = get_option('saml_am_dont_allow_unlisted_user_role');
				$checkIfMatchBy = get_option('saml_am_account_matcher');

				$firstName = str_replace(".", "_", $firstName);
				$firstName = str_replace(" ", "_", $firstName);
				if(!empty($firstName) && array_key_exists($firstName, $_POST) ) {
					$firstName = htmlspecialchars($_POST[$firstName]);
				}

				$lastName = str_replace(".", "_", $lastName);
				$lastName = str_replace(" ", "_", $lastName);
				if(!empty($lastName) && array_key_exists($lastName, $_POST) ) {
					$lastName = htmlspecialchars($_POST[$lastName]);
				}

				$usernameAttribute = str_replace(".", "_", $usernameAttribute);
				$usernameAttribute = str_replace(" ", "_", $usernameAttribute);
				if(!empty($usernameAttribute) && array_key_exists($usernameAttribute, $_POST)) {
					$userName = htmlspecialchars($_POST[$usernameAttribute]);
				} else {
					$userName = htmlspecialchars($_POST['NameID']);
				}

				$user_email = str_replace(".", "_", $emailAttribute);
				$user_email = str_replace(" ", "_", $emailAttribute);
				if(!empty($emailAttribute) && array_key_exists($emailAttribute, $_POST)) {
					$user_email = htmlspecialchars($_POST[$emailAttribute]);
				} else {
					$user_email = htmlspecialchars($_POST['NameID']);
				}

				$groupName = str_replace(".", "_", $groupName);
				$groupName = str_replace(" ", "_", $groupName);
				if(!empty($groupName) && array_key_exists($groupName, $_POST) ) {
					$groupName = htmlspecialchars($_POST[$groupName]);
				}

				if(empty($checkIfMatchBy)) {
					$checkIfMatchBy = "email";
				}

				//Decrypt email now.

				//Get customer token as a key to decrypt email
				$key = get_option('mo_saml_customer_token');

				if(isset($key) || trim($key) != '')
				{
					$deciphertext = AESEncryption::decrypt_data($user_email, $key);
					$user_email = $deciphertext;
				}

				//Decrypt firstname and lastName and username

				if(!empty($firstName) && !empty($key))
				{
					$decipherFirstName = AESEncryption::decrypt_data($firstName, $key);
					$firstName = $decipherFirstName;
				}
				if(!empty($lastName) && !empty($key))
				{
					$decipherLastName = AESEncryption::decrypt_data($lastName, $key);
					$lastName = $decipherLastName;
				}
				if(!empty($userName) && !empty($key))
				{
					$decipherUserName = AESEncryption::decrypt_data($userName, $key);
					$userName = $decipherUserName;
				}
				if(!empty($groupName) && !empty($key))
				{
					$decipherGroupName = AESEncryption::decrypt_data($groupName, $key);
					$groupName = $decipherGroupName;
				}
			}
			catch (Exception $e) {
				echo sprintf("An error occurred while processing the SAML Response.");
				exit;
			}
			$groupArray = array ( $groupName );
			mo_saml_login_user($user_email,$firstName,$lastName,$userName, $groupArray, $dontAllowUnlistedUserRole, $defaultRole,$redirect_to, $checkIfMatchBy);
		}

	}
}

function mo_saml_checkMapping($attrs,$relayState,$sessionIndex){
	try {
		//Get enrypted user_email
		$emailAttribute = get_option('saml_am_email');
        $mo_saml_identity_provider_identifier_name = get_option('mo_saml_identity_provider_identifier_name')?get_option('mo_saml_identity_provider_identifier_name'):"";
        if(!empty($mo_saml_identity_provider_identifier_name) and $mo_saml_identity_provider_identifier_name == 'Azure B2C'){
            $emailAttribute = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress';
        }
		$usernameAttribute = get_option('saml_am_username');
		$firstName = get_option('saml_am_first_name');
		$lastName = get_option('saml_am_last_name');
		$groupName = get_option('saml_am_group_name');
		$defaultRole = get_option('saml_am_default_user_role');
		$dontAllowUnlistedUserRole = get_option('saml_am_dont_allow_unlisted_user_role');
		$checkIfMatchBy = get_option('saml_am_account_matcher');
		$user_email = '';
		$userName = '';

		//Attribute mapping. Check if Match/Create user is by username/email:
		if(!empty($attrs)){
			if(!empty($firstName) && array_key_exists($firstName, $attrs))
				$firstName = $attrs[$firstName][0];
			else
				$firstName = '';

			if(!empty($lastName) && array_key_exists($lastName, $attrs))
				$lastName = $attrs[$lastName][0];
			else
				$lastName = '';

			if(!empty($usernameAttribute) && array_key_exists($usernameAttribute, $attrs))
				$userName = $attrs[$usernameAttribute][0];
			else
				$userName = $attrs['NameID'][0];

			if(!empty($emailAttribute) && array_key_exists($emailAttribute, $attrs))
				$user_email = $attrs[$emailAttribute][0];
			else
				$user_email = $attrs['NameID'][0];

			if(!empty($groupName) && array_key_exists($groupName, $attrs))
				$groupName = $attrs[$groupName];
			else
				$groupName = array();

			if(empty($checkIfMatchBy)) {
				$checkIfMatchBy = "email";
			}

		}


		if($relayState=='testValidate'){
		    update_option('MO_SAML_TEST',"Test successful");
		    update_option('MO_SAML_TEST_STATUS',1);
			mo_saml_show_test_result($firstName,$lastName,$user_email,$groupName,$attrs);
		}else{
			mo_saml_login_user($user_email, $firstName, $lastName, $userName, $groupName, $dontAllowUnlistedUserRole, $defaultRole, $relayState, $checkIfMatchBy, $sessionIndex, $attrs['NameID'][0]);
		}

	}
	catch (Exception $e) {
		echo sprintf("An error occurred while processing the SAML Response.");
		exit;
	}
}



function mo_saml_show_test_result($firstName,$lastName,$user_email,$groupName,$attrs){
	if(ob_get_contents())
		ob_end_clean();
	echo '<div style="font-family:Calibri;padding:0 3%;">';
	$name_id = $attrs['NameID'][0];
	if(!empty($user_email)){
		update_option('mo_saml_test_config_attrs', $attrs);
		echo '<div style="color: #3c763d;
				background-color: #dff0d8; padding:2%;margin-bottom:20px;text-align:center; border:1px solid #AEDB9A; font-size:18pt;">TEST SUCCESSFUL</div>
				<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;"src="'. plugin_dir_url(__FILE__) . 'images/green_check.png"></div>';
	}else{
		echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">TEST FAILED</div>
				<div style="color: #a94442;font-size:14pt; margin-bottom:20px;">WARNING: Some Attributes Did Not Match.</div>
				<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;"src="'. plugin_dir_url(__FILE__) . 'images/wrong.png"></div>';
	}

		if(strlen($name_id) > 60){
			echo '<p><font color="#FF0000" style="font-size:14pt;font-weight:bold">Warning: The NameID value is longer than 60 characters. User will not be created during SSO.</font></p>';
		}
		$matchAccountBy = get_option('saml_am_account_matcher')?get_option('saml_am_account_matcher'):'email';
		if($matchAccountBy=='email' && !filter_var($name_id, FILTER_VALIDATE_EMAIL))
		{
				echo '<p><font color="#FF0000" style="font-size:14pt;font-weight:bold">Warning: The NameID value is not a valid Email ID</font></p>';
		}
		echo '<span style="font-size:14pt;"><b>Hello</b>, '.$user_email.'</span>';


		echo'<br/><p style="font-weight:bold;font-size:14pt;margin-left:1%;">ATTRIBUTES RECEIVED:</p>
				<table style="border-collapse:collapse;border-spacing:0; display:table;width:100%; font-size:14pt;background-color:#EDEDED;">
				<tr style="text-align:center;"><td style="font-weight:bold;border:2px solid #949090;padding:2%;">ATTRIBUTE NAME</td><td style="font-weight:bold;padding:2%;border:2px solid #949090; word-wrap:break-word;">ATTRIBUTE VALUE</td></tr>';

	if(!empty($attrs)){
		foreach ($attrs as $key => $value)

			echo "<tr><td style='font-weight:bold;border:2px solid #949090;padding:2%;'>" .$key . "</td><td style='padding:2%;border:2px solid #949090; word-wrap:break-word;'>" .implode("<hr/>",$value). "</td></tr>";
		}
	else
			echo "No Attributes Received.";
		echo '</table></div>';
		echo '<div style="margin:3%;display:block;text-align:center;">
		<input style="padding:1%;width:250px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"
            type="button" value="Configure Attribute/Role Mapping" onClick="close_and_redirect_to_attribute_mapping();"> &nbsp;
		<input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;
		"type="button" value="Done" onClick="close_and_redirect();"></div>
		
		<script>
             function close_and_redirect_to_attribute_mapping(){
                 window.opener.redirect_to_attribute_mapping();
                 self.close();
             }   
             function close_and_redirect() {
               window.opener.redirect_to_service_provider();
                 self.close();
             }
             
            
		</script>';
		exit;
}


/**
 * @Author:Shubham Gupta
 *
 */
function mo_saml_convert_to_windows_iconv($certfpFromPlugin){
    $encoding_enabled = get_option('mo_saml_encoding_enabled');

    if($encoding_enabled==='' || !mo_saml_is_iconv_installed())
        return $certfpFromPlugin;
    return iconv("UTF-8", "CP1252//IGNORE", $certfpFromPlugin);

}

function mo_saml_login_user($user_email, $firstName, $lastName, $userName, $groupName, $dontAllowUnlistedUserRole, $defaultRole, $relayState, $checkIfMatchBy, $sessionIndex = '', $nameId = ''){
	$user_id = null;
    if(($checkIfMatchBy == 'username' && username_exists( $userName )) || username_exists( $userName) ) {
		$user 	= get_user_by('login', $userName);
		$user_id = $user->ID;


	} elseif(email_exists( $user_email )) {

		$user 	= get_user_by('email', $user_email );
		$user_id = $user->ID;


	} elseif ( !username_exists( $userName ) && !email_exists( $user_email ) ) {
		$random_password = wp_generate_password( 10, false );
		if(!empty($userName))
		{
			$user_id = wp_create_user( $userName, $random_password, $user_email );
		}
		else
		{
			$user_id = wp_create_user( $user_email, $random_password, $user_email );
		}
		if(is_wp_error($user_id)){
			wp_die('We couldn\'t sign you in. Please contact your administrator.', 'Error: User not created');
			exit();
		}

		if (!get_option('mo_saml_free_version')) {
			// Assign role
			$current_user = get_user_by('id', $user_id);
			$role_mapping = get_option('saml_am_role_mapping');
			if(!empty($groupName) && !empty($role_mapping)) {
				$role_to_assign = '';
				$found = false;
				foreach ($role_mapping as $role_value => $group_names) {
					$groups = explode(";", $group_names);
					foreach ($groups as $group) {
						if(in_array($group, $groupName, TRUE)) {
							$found = true;
							$current_user->add_role($role_value);
						}
					}
				}

				if($found !== true && !empty($dontAllowUnlistedUserRole) && $dontAllowUnlistedUserRole == 'checked') {
					$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => false ) );
				} elseif($found !== true && !empty($defaultRole)) {
					$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $defaultRole ) );
				}
			} elseif (!empty($dontAllowUnlistedUserRole) && strcmp( $dontAllowUnlistedUserRole, 'checked') == 0) {
				$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => false ) );
			} elseif(!empty($defaultRole)) {
				$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $defaultRole ) );
			} else {
				$defaultRole = get_option('default_role');
				$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $defaultRole ) );
			}
		} else {
			if(!empty($defaultRole)) {
				$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $defaultRole ) );
			}
		}

	}
	elseif ( username_exists( $userName ) && !email_exists( $user_email ) ){
		wp_die("Registration has failed as a user with the same username already exists in WordPress. Please ask your administrator to create an account for you with a unique username.","Error");
	 }
	 mo_saml_add_firstlast_name($user_id,$firstName,$lastName,$relayState);


}

function mo_saml_add_firstlast_name($user_id,$first_name,$last_name,$relay_state){
	if( !empty($first_name) )
	{
		$user_id = wp_update_user( array( 'ID' => $user_id, 'first_name' => $first_name ) );
	}
	if( !empty($last_name) )
	{
		$user_id = wp_update_user( array( 'ID' => $user_id, 'last_name' => $last_name ) );
	}

	wp_set_auth_cookie( $user_id, true );

	if(!empty($relay_state))
		wp_redirect( $relay_state );
	else
		wp_redirect( site_url() );
	exit;
}


function mo_saml_show_status_error($statusCode, $relayState, $statusmessage){
    $statusCode = htmlspecialchars($statusCode);
    $statusmessage = htmlspecialchars($statusmessage);
	if($relayState=='testValidate'){

                echo '<div style="font-family:Calibri;padding:0 3%;">';
                echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
                <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong> Invalid SAML Response Status.</p>
                <p><strong>Causes</strong>: Identity Provider has sent \''.$statusCode.'\' status code in SAML Response. Please check IdP logs.</p>
								<p><strong>Reason</strong>: '.mo_saml_get_status_message($statusCode).'</p> ';
				if(!empty($statusmessage))
				echo '<p><strong>Status Message in the SAML Response:</strong> <br/>'.$statusmessage.'</p><br>';
			echo '
                </div>

                <div style="margin:3%;display:block;text-align:center;">
                <div style="margin:3%;display:block;text-align:center;"><input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="button" value="Done" onClick="self.close();"></div>';
								exit;
              }
              else{
              		wp_die('We could not sign you in. Please contact your Administrator','Error:Invalid SAML Response Status');
              }

}

function mo_saml_get_status_message($statusCode){
	switch($statusCode){
		case 'Requester':
			return 'The request could not be performed due to an error on the part of the requester.';
			break;
		case 'Responder':
			return 'The request could not be performed due to an error on the part of the SAML responder or SAML authority.';
			break;
		case 'VersionMismatch':
			return 'The SAML responder could not process the request because the version of the request message was incorrect.';
			break;
		default:
			return 'Unknown';
	}
}
function mo_saml_show_SAML_log($samlRequestXML,$type){

	header("Content-Type: text/html");
	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = false;
	$doc->formatOutput = true;
	$doc->loadXML($samlRequestXML);
	if($type=='displaySAMLRequest')
		$show_value='SAML Request';
	else
		$show_value='SAML Response';
	$out = $doc->saveXML();

	$out1 = htmlentities($out);
    $out1 = rtrim($out1);

	$xml   = simplexml_load_string( $out );

	$json  = json_encode( $xml );

	$array = json_decode( $json );
	$url = plugins_url( 'includes/css/style_settings.css?ver=4.8.60', __FILE__ ) ;


	echo '<link rel=\'stylesheet\' id=\'mo_saml_admin_settings_style-css\'  href=\''.$url.'\' type=\'text/css\' media=\'all\' />
            
			<div class="mo-display-logs" ><p type="text"   id="SAML_type">'.$show_value.'</p></div>
			
			<div type="text" id="SAML_display" class="mo-display-block"><pre class=\'brush: xml;\'>'.$out1.'</pre></div>
			<br>
			<div style="margin:3%;display:block;text-align:center;">
            
			<div style="margin:3%;display:block;text-align:center;" >
	
            </div>
			<button id="copy" onclick="copyDivToClipboard()"  style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" >Copy</button>
			&nbsp;
               <input id="dwn-btn" style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="button" value="Download" 
               ">
			</div>
			</div>
			
		
			';

    ob_end_flush();?>

	<script>

        function copyDivToClipboard() {
            var aux = document.createElement("input");
            aux.setAttribute("value", document.getElementById("SAML_display").textContent);
            document.body.appendChild(aux);
            aux.select();
            document.execCommand("copy");
            document.body.removeChild(aux);
            document.getElementById('copy').textContent = "Copied";
            document.getElementById('copy').style.background = "grey";
            window.getSelection().selectAllChildren( document.getElementById( "SAML_display" ) );

        }

        function download(filename, text) {
            var element = document.createElement('a');
            element.setAttribute('href', 'data:Application/octet-stream;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', filename);

            element.style.display = 'none';
            document.body.appendChild(element);

            element.click();

            document.body.removeChild(element);
        }

        document.getElementById("dwn-btn").addEventListener("click", function () {

            var filename = document.getElementById("SAML_type").textContent+".xml";
            var node = document.getElementById("SAML_display");
            htmlContent = node.innerHTML;
            text = node.textContent;
            download(filename, text);
        }, false);





    </script>
<?php
	exit;
}
function saml_get_current_page_url() {
	$http_host = $_SERVER['HTTP_HOST'];
	if(substr($http_host, -1) == '/') {
		$http_host = substr($http_host, 0, -1);
	}
	$request_uri = $_SERVER['REQUEST_URI'];
	if(substr($request_uri, 0, 1) == '/') {
		$request_uri = substr($request_uri, 1);
	}
	if (strpos($request_uri, '?option=saml_user_login') !== false) {
    	return strtok($_SERVER["REQUEST_URI"],'?');;
	}
	$is_https = (isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') == 0);
	$relay_state = 'http' . ($is_https ? 's' : '') . '://' . $http_host . '/' . $request_uri;
	return $relay_state;
}
add_action( 'widgets_init',function(){register_widget( "mo_login_wid" );} );
add_action( 'init', 'mo_saml_login_validate');
?>