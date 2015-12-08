<?php
$w = new wfConfig();
?>
<script type="text/javascript">
	var WFSLevels = <?php echo json_encode(wfConfig::$securityLevels); ?>;
</script>
<div class="wordfenceModeElem" id="wordfenceMode_options"></div>
<div class="wrap">
	<?php require( 'menuHeader.php' ); ?>
	<?php $helpLink = "http://docs.wordfence.com/en/Wordfence_options";
	$helpLabel      = "Learn more about Wordfence Options";
	$pageTitle      = "Wordfence Options";
	include( 'pageTitle.php' ); ?>
	<div class="wordfenceLive">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><h2>Wordfence Live Activity:</h2></td>
				<td id="wfLiveStatus"></td>
			</tr>
		</table>
	</div>

	<form id="wfConfigForm">
		<table class="wfConfigForm">
			<tr>
				<td colspan="2"><h2>License</h2></td>
			</tr>

			<tr>
				<th>Your Wordfence API Key:<a href="http://docs.wordfence.com/en/Wordfence_options#Wordfence_API_Key"
				                              target="_blank" class="wfhelp"></a></th>
				<td><input type="text" id="apiKey" name="apiKey" value="<?php $w->f( 'apiKey' ); ?>" size="80"/></td>
			</tr>
			<tr>
				<th>Key type currently active:</th>
				<td>
					<?php if (wfConfig::get( 'isPaid' )){ ?>
						The currently active API Key is a Premium Key. <span style="font-weight: bold; color: #0A0;">Premium scanning enabled!</span>
					<?php } else { ?>
					The currently active API Key is a <span style="color: #F00; font-weight: bold;">Free Key</span>. <a
							href="https://www.wordfence.com/gnl1optAPIKey1/wordfence-signup/" target="_blank">Click Here to Upgrade to
							Wordfence Premium now.</a>
						<?php } ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php if ( wfConfig::get( 'isPaid' ) ) { ?>
						<table border="0">
							<tr>
								<td><a href="https://www.wordfence.com/gnl1optMngKys/manage-wordfence-api-keys/"
								       target="_blank"><input type="button" value="Renew your premium license"/></a>
								</td>
								<td>&nbsp;</td>
								<td><input type="button" value="Downgrade to a free license"
								           onclick="WFAD.downgradeLicense();"/></td>
							</tr>
						</table>
					<?php } ?>


			<tr>
				<td colspan="2"><h2>Basic Options<a href="http://docs.wordfence.com/en/Wordfence_options#Basic_Options"
				                                    target="_blank" class="wfhelp"></a></h2></td>
			</tr>
			<tr>
				<th class="wfConfigEnable">Enable firewall<a
						href="http://docs.wordfence.com/en/Wordfence_options#Enable_Firewall" target="_blank"
						class="wfhelp"></a></th>
				<td><input type="checkbox" id="firewallEnabled" class="wfConfigElem" name="firewallEnabled"
				           value="1" <?php $w->cb( 'firewallEnabled' ); ?> />&nbsp;<span
						style="color: #F00;">NOTE:</span> This checkbox enables ALL firewall functions including IP,
					country and advanced blocking and the "Firewall Rules" below.
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th class="wfConfigEnable">Enable login security<a
						href="http://docs.wordfence.com/en/Wordfence_options#Enable_login_security" target="_blank"
						class="wfhelp"></a></th>
				<td><input type="checkbox" id="loginSecurityEnabled" class="wfConfigElem" name="loginSecurityEnabled"
				           value="1" <?php $w->cb( 'loginSecurityEnabled' ); ?> />&nbsp;This option enables all "Login
					Security" options. You can modify individual options further down this page.
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th class="wfConfigEnable">Enable Live Traffic View<a
						href="http://docs.wordfence.com/en/Wordfence_options#Enable_Live_Traffic_View" target="_blank"
						class="wfhelp"></a></th>
				<td><input type="checkbox" id="liveTrafficEnabled" class="wfConfigElem" name="liveTrafficEnabled"
				           value="1" <?php $w->cb( 'liveTrafficEnabled' ); ?>
				           onclick="WFAD.reloadConfigPage = true; return true;"/>&nbsp;This option enables live traffic
					logging.
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th class="wfConfigEnable">Advanced Comment Spam Filter<a
						href="http://docs.wordfence.com/en/Wordfence_options#Advanced_Comment_Spam_Filter"
						target="_blank" class="wfhelp"></a></th>
				<td><input type="checkbox" id="advancedCommentScanning" class="wfConfigElem"
				           name="advancedCommentScanning" value="1" <?php $w->cbp( 'advancedCommentScanning' );
					if ( ! wfConfig::get( 'isPaid' )){ ?>onclick="alert('This is a paid feature because it places significant additional load on our servers.'); jQuery('#advancedCommentScanning').attr('checked', false); return false;" <?php } ?> />&nbsp;<span
						style="color: #F00;">Premium Feature</span> In addition to free comment filtering (see below)
					this option filters comments against several additional real-time lists of known spammers and
					infected hosts.
				</td>
			</tr>
			<tr>
				<th class="wfConfigEnable">Check if this website is being "Spamvertised"<a
						href="http://docs.wordfence.com/en/Wordfence_options#Check_if_this_website_is_being_.22Spamvertized.22"
						target="_blank" class="wfhelp"></a></th>
				<td><input type="checkbox" id="spamvertizeCheck" class="wfConfigElem" name="spamvertizeCheck" value="1"
				           <?php $w->cbp( 'spamvertizeCheck' );
				           if ( ! wfConfig::get( 'isPaid' )){ ?>onclick="alert('This is a paid feature because it places significant additional load on our servers.'); jQuery('#spamvertizeCheck').attr('checked', false); return false;" <?php } ?> />&nbsp;<span
						style="color: #F00;">Premium Feature</span> When doing a scan, Wordfence will check with spam
					services if your site domain name is appearing as a link in spam emails.
				</td>
			</tr>
			<tr>
				<th class="wfConfigEnable">Check if this website IP is generating spam<a
						href="http://docs.wordfence.com/en/Wordfence_options#Check_if_this_website_IP_is_generating_spam"
						target="_blank" class="wfhelp"></a></th>
				<td><input type="checkbox" id="checkSpamIP" class="wfConfigElem" name="checkSpamIP" value="1"
				           <?php $w->cbp( 'checkSpamIP' );
				           if ( ! wfConfig::get( 'isPaid' )){ ?>onclick="alert('This is a paid feature because it places significant additional load on our servers.'); jQuery('#checkSpamIP').attr('checked', false); return false;" <?php } ?> />&nbsp;<span
						style="color: #F00;">Premium Feature</span> When doing a scan, Wordfence will check with spam
					services if your website IP address is listed as a known source of spam email.
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<?php /* <tr><th class="wfConfigEnable">Enable Performance Monitoring</th><td><input type="checkbox" id="perfLoggingEnabled" class="wfConfigElem" name="perfLoggingEnabled" value="1" <?php $w->cb('perfLoggingEnabled'); ?> onclick="WFAD.reloadConfigPage = true; return true;" />&nbsp;This option enables performance monitoring.</td></tr> */ ?>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th class="wfConfigEnable">Enable automatic scheduled scans<a
						href="http://docs.wordfence.com/en/Wordfence_options#Enable_automatic_scheduled_scans"
						target="_blank" class="wfhelp"></a></th>
				<td><input type="checkbox" id="scheduledScansEnabled" class="wfConfigElem" name="scheduledScansEnabled"
				           value="1" <?php $w->cb( 'scheduledScansEnabled' ); ?> />&nbsp;Regular scans ensure your site
					stays secure.
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th class="wfConfigEnable">Update Wordfence automatically when a new version is released?<a
						href="http://docs.wordfence.com/en/Wordfence_options#Update_Wordfence_Automatically_when_a_new_version_is_released"
						target="_blank" class="wfhelp"></a></th>
				<td><input type="checkbox" id="autoUpdate" class="wfConfigElem" name="autoUpdate"
				           value="1" <?php $w->cb( 'autoUpdate' ); ?> />&nbsp;Automatically updates Wordfence to the
					newest version within 24 hours of a new release.<br/>
					<?php if (getenv( 'noabort' ) != '1' && stristr( $_SERVER['SERVER_SOFTWARE'], 'litespeed' ) !== false){ ?>
					<span style="color: #F00;">Warning: </span>You are running LiteSpeed web server and you don't have
					the "noabort" variable set in your .htaccess.<br/>
					<a href="https://support.wordfence.com/solution/articles/1000129050-running-wordfence-under-litespeed-web-server-and-preventing-process-killing-or"
					   target="_blank">Please read this article in our FAQ to make an important change that will ensure
						your site stability during an update.<br/>
						<?php } ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>

			<tr>
				<th>Where to email alerts:<a href="http://docs.wordfence.com/en/Wordfence_options#Where_to_email_alerts"
				                             target="_blank" class="wfhelp"></a></th>
				<td><input type="text" id="alertEmails" name="alertEmails" value="<?php $w->f( 'alertEmails' ); ?>"
				           size="50"/>&nbsp;<span class="wfTipText">Separate multiple emails with commas</span></td>
			</tr>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
			<tr>
				<th>Security Level:<a href="http://docs.wordfence.com/en/Wordfence_options#Security_Level"
				                      target="_blank" class="wfhelp"></a></th>
				<td>
					<select id="securityLevel" name="securityLevel" onchange="WFAD.changeSecurityLevel(); return true;">
						<option value="0"<?php $w->sel( 'securityLevel', '0' ); ?>>Level 0: Disable all Wordfence
							security measures
						</option>
						<option value="1"<?php $w->sel( 'securityLevel', '1' ); ?>>Level 1: Light protection. Just the
							basics
						</option>
						<option value="2"<?php $w->sel( 'securityLevel', '2' ); ?>>Level 2: Medium protection. Suitable
							for most sites
						</option>
						<option value="3"<?php $w->sel( 'securityLevel', '3' ); ?>>Level 3: High security. Use this when
							an attack is imminent
						</option>
						<option value="4"<?php $w->sel( 'securityLevel', '4' ); ?>>Level 4: Lockdown. Protect the site
							against an attack in progress at the cost of inconveniencing some users
						</option>
						<option value="CUSTOM"<?php $w->sel( 'securityLevel', 'CUSTOM' ); ?>>Custom settings</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>How does Wordfence get IPs:<a
						href="http://docs.wordfence.com/en/Wordfence_options#How_does_Wordfence_get_IPs" target="_blank"
						class="wfhelp"></a></th>
				<td>
					<select id="howGetIPs" name="howGetIPs">
						<option value="">Let Wordfence use the most secure method to get visitor IP addresses. Prevents
							spoofing and works with most sites.
						</option>
						<option value="REMOTE_ADDR"<?php $w->sel( 'howGetIPs', 'REMOTE_ADDR' ); ?>>Use PHP's built in
							REMOTE_ADDR and don't use anything else. Very secure if this is compatible with your site.
						</option>
						<option value="HTTP_X_FORWARDED_FOR"<?php $w->sel( 'howGetIPs', 'HTTP_X_FORWARDED_FOR' ); ?>>Use
							the X-Forwarded-For HTTP header. Only use if you have a front-end proxy or spoofing may
							result.
						</option>
						<option value="HTTP_X_REAL_IP"<?php $w->sel( 'howGetIPs', 'HTTP_X_REAL_IP' ); ?>>Use the
							X-Real-IP HTTP header. Only use if you have a front-end proxy or spoofing may result.
						</option>
						<option value="HTTP_CF_CONNECTING_IP"<?php $w->sel( 'howGetIPs', 'HTTP_CF_CONNECTING_IP' ); ?>>
							Use the Cloudflare "CF-Connecting-IP" HTTP header to get a visitor IP. Only use if you're
							using Cloudflare.
						</option>
					</select>
				</td>
			</tr>
		</table>
		<p>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><input type="button" id="button1" name="button1" class="button-primary" value="Save Changes"
				           onclick="WFAD.saveConfig();"/></td>
				<td style="height: 24px;">
					<div class="wfAjax24"></div>
					<span class="wfSavedMsg">&nbsp;Your changes have been saved!</span></td>
			</tr>
		</table>
		</p>
		<div class="wfMarker" id="wfMarkerBasicOptions"></div>
		<div style="margin-top: 25px;">
			<h2>Advanced Options:<a href="http://docs.wordfence.com/en/Wordfence_options#Advanced_Options"
			                        target="_blank" class="wfhelp"></a></h2>

			<p style="width: 600px;">
				Wordfence works great out of the box for most websites. Simply install Wordfence and your site and
				content is protected. For finer granularity of control, we have provided advanced options.
			</p>
		</div>
		<div id="wfConfigAdvanced">
			<table class="wfConfigForm">
				<tr>
					<td colspan="2"><h3 class="wfConfigHeading">Alerts<a
								href="http://docs.wordfence.com/en/Wordfence_options#Alerts" target="_blank"
								class="wfhelp"></a></h3></td>
				</tr>
				<?php
				$emails = wfConfig::getAlertEmails();
				if ( sizeof( $emails ) < 1 ) {
					echo "<tr><th colspan=\"2\" style=\"color: #F00;\">You have not configured an email to receive alerts yet. Set this up under \"Basic Options\" above.</th></tr>\n";
				}
				?>
				<tr>
					<th>Email me when Wordfence is automatically updated</th>
					<td><input type="checkbox" id="alertOn_update" class="wfConfigElem" name="alertOn_update"
					           value="1" <?php $w->cb( 'alertOn_update' ); ?>/>&nbsp;If you have automatic updates
						enabled (see above), you'll get an email when an update occurs.
					</td>
				</tr>
				<tr>
					<th>Alert on critical problems</th>
					<td><input type="checkbox" id="alertOn_critical" class="wfConfigElem" name="alertOn_critical"
					           value="1" <?php $w->cb( 'alertOn_critical' ); ?>/></td>
				</tr>
				<tr>
					<th>Alert on warnings</th>
					<td><input type="checkbox" id="alertOn_warnings" class="wfConfigElem" name="alertOn_warnings"
					           value="1" <?php $w->cb( 'alertOn_warnings' ); ?>/></td>
				</tr>
				<tr>
					<th>Alert when an IP address is blocked</th>
					<td><input type="checkbox" id="alertOn_block" class="wfConfigElem" name="alertOn_block"
					           value="1" <?php $w->cb( 'alertOn_block' ); ?>/></td>
				</tr>
				<tr>
					<th>Alert when someone is locked out from login</th>
					<td><input type="checkbox" id="alertOn_loginLockout" class="wfConfigElem"
					           name="alertOn_loginLockout" value="1" <?php $w->cb( 'alertOn_loginLockout' ); ?>/></td>
				</tr>
				<tr>
					<th>Alert when the "lost password" form is used for a valid user</th>
					<td><input type="checkbox" id="alertOn_lostPasswdForm" class="wfConfigElem"
					           name="alertOn_lostPasswdForm" value="1" <?php $w->cb( 'alertOn_lostPasswdForm' ); ?>/>
					</td>
				</tr>
				<tr>
					<th>Alert me when someone with administrator access signs in</th>
					<td><input type="checkbox" id="alertOn_adminLogin" class="wfConfigElem" name="alertOn_adminLogin"
					           value="1" <?php $w->cb( 'alertOn_adminLogin' ); ?>/></td>
				</tr>
				<tr>
					<th>Alert me when a non-admin user signs in</th>
					<td><input type="checkbox" id="alertOn_nonAdminLogin" class="wfConfigElem"
					           name="alertOn_nonAdminLogin" value="1" <?php $w->cb( 'alertOn_nonAdminLogin' ); ?>/></td>
				</tr>
				<tr>
					<th>Maximum email alerts to send per hour</th>
					<td>&nbsp;<input type="text" id="alert_maxHourly" name="alert_maxHourly"
					                 value="<?php $w->f( 'alert_maxHourly' ); ?>" size="4"/>0 or empty means unlimited
						alerts will be sent.
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="wfMarker" id="wfMarkerEmailSummary"></div>
						<h3 class="wfConfigHeading">Email Summary<a
								href="http://docs.wordfence.com/en/Wordfence_options#Email_Summary" target="_blank"
								class="wfhelp"></a></h3>
					</td>
				</tr>
				<tr>
					<th>Enable email summary:</th>
					<td>&nbsp;<input type="checkbox" id="email_summary_enabled" name="email_summary_enabled"
					                 value="1" <?php $w->cb('email_summary_enabled'); ?> />
					</td>
				</tr>
				<tr>
					<th>Email summary frequency:</th>
					<td>
						<select id="email_summary_interval" class="wfConfigElem" name="email_summary_interval">
							<option value="weekly"<?php $w->sel( 'email_summary_interval', 'weekly' ); ?>>Once a week</option>
							<option value="biweekly"<?php $w->sel( 'email_summary_interval', 'biweekly' ); ?>>Once every 2 weeks</option>
							<option value="monthly"<?php $w->sel( 'email_summary_interval', 'monthly' ); ?>>Once a month</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Comma-separated list of directories to exclude from recently modified file list:</th>
					<td>
						<input name="email_summary_excluded_directories" type="text" value="<?php $w->f('email_summary_excluded_directories') ?>"/>
					</td>
				</tr>
				<?php if ((defined('WP_DEBUG') && WP_DEBUG) || wfConfig::get('debugOn', 0)): ?>
					<tr>
						<th>Send test email:</th>
						<td>
							<input type="email" id="email_summary_email_address_debug" />
							<a class="button" href="javascript:void(0);" onclick="WFAD.ajax('wordfence_email_summary_email_address_debug', {email: jQuery('#email_summary_email_address_debug').val()});">Send Email</a>
						</td>
					</tr>
				<?php endif ?>
				<tr>
					<th>Enable activity report widget on dashboard:</th>
					<td>&nbsp;<input type="checkbox" id="email_summary_dashboard_widget_enabled" name="email_summary_dashboard_widget_enabled"
					                 value="1" <?php $w->cb('email_summary_dashboard_widget_enabled'); ?> />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="wfMarker" id="wfMarkerLiveTrafficOptions"></div>
						<h3 class="wfConfigHeading">Live Traffic View<a
								href="http://docs.wordfence.com/en/Wordfence_options#Live_Traffic_View" target="_blank"
								class="wfhelp"></a></h3>
					</td>
				</tr>
				<tr>
					<th>Don't log signed-in users with publishing access:</th>
					<td><input type="checkbox" id="liveTraf_ignorePublishers" name="liveTraf_ignorePublishers"
					           value="1" <?php $w->cb( 'liveTraf_ignorePublishers' ); ?> /></td>
				</tr>
				<tr>
					<th>List of comma separated usernames to ignore:</th>
					<td><input type="text" name="liveTraf_ignoreUsers" id="liveTraf_ignoreUsers"
					           value="<?php $w->f( 'liveTraf_ignoreUsers' ); ?>"/></td>
				</tr>
				<tr>
					<th>List of comma separated IP addresses to ignore:</th>
					<td><input type="text" name="liveTraf_ignoreIPs" id="liveTraf_ignoreIPs"
					           value="<?php $w->f( 'liveTraf_ignoreIPs' ); ?>"/></td>
				</tr>
				<tr>
					<th>Browser user-agent to ignore:</th>
					<td><input type="text" name="liveTraf_ignoreUA" id="liveTraf_ignoreUA"
					           value="<?php $w->f( 'liveTraf_ignoreUA' ); ?>"/></td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="wfMarker" id="wfMarkerScansToInclude"></div>
						<h3 class="wfConfigHeading">Scans to include<a
								href="http://docs.wordfence.com/en/Wordfence_options#Scans_to_Include" target="_blank"
								class="wfhelp"></a></h3></td>
				</tr>
				<?php if ( wfConfig::get( 'isPaid' ) ) { ?>
					<tr>
						<th>Scan public facing site for vulnerabilities?<a
								href="http://docs.wordfence.com/en/Wordfence_options#Scan_public_facing_site"
								target="_blank" class="wfhelp"></a></th>
						<td><input type="checkbox" id="scansEnabled_public" class="wfConfigElem"
						           name="scansEnabled_public" value="1" <?php $w->cb( 'scansEnabled_public' ); ?> /></td>
					</tr>
				<?php } else { ?>
					<tr>
						<th style="color: #F00;">Scan public facing site for vulnerabilities?<a
								href="http://docs.wordfence.com/en/Wordfence_options#Scan_public_facing_site"
								target="_blank" class="wfhelp"></a>(<a
								href="https://www.wordfence.com/gnl1optPdOnly1/wordfence-signup/" target="_blank">Paid members only</a>)
						</th>
						<td><input type="checkbox" id="scansEnabled_public" class="wfConfigElem"
						           name="scansEnabled_public" value="1" DISABLED /></td>
					</tr>
				<?php } ?>
				<tr>
					<th>Scan for the HeartBleed vulnerability?<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_for_the_HeartBleed_vulnerability"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_heartbleed" class="wfConfigElem"
					           name="scansEnabled_heartbleed" value="1" <?php $w->cb( 'scansEnabled_heartbleed' ); ?> />
					</td>
				</tr>
				<tr>
					<th>Scan core files against repository versions for changes<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_core_files_against_repository_version_for_changes"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_core" class="wfConfigElem" name="scansEnabled_core"
					           value="1" <?php $w->cb( 'scansEnabled_core' ); ?>/></td>
				</tr>

				<tr>
					<th>Scan theme files against repository versions for changes<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_theme_files_against_repository_versions_for_changes"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_themes" class="wfConfigElem" name="scansEnabled_themes"
					           value="1" <?php $w->cb( 'scansEnabled_themes' ); ?>/></td>
				</tr>
				<tr>
					<th>Scan plugin files against repository versions for changes<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_plugin_files_against_repository_versions_for_changes"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_plugins" class="wfConfigElem"
					           name="scansEnabled_plugins" value="1" <?php $w->cb( 'scansEnabled_plugins' ); ?>/></td>
				</tr>
				<tr>
					<th>Scan for signatures of known malicious files<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_for_signatures_of_known_malicious_files"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_malware" class="wfConfigElem"
					           name="scansEnabled_malware" value="1" <?php $w->cb( 'scansEnabled_malware' ); ?>/></td>
				</tr>
				<tr>
					<th>Scan file contents for backdoors, trojans and suspicious code<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_file_contents_for_backdoors.2C_trojans_and_suspicious_code"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_fileContents" class="wfConfigElem"
					           name="scansEnabled_fileContents"
					           value="1" <?php $w->cb( 'scansEnabled_fileContents' ); ?>/></td>
				</tr>
				<tr>
					<th>Scan database for backdoors, trojans and suspicious code<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_database_for_backdoors.2C_trojans_and_suspicious_code"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_database" class="wfConfigElem"
					           name="scansEnabled_database"
					           value="1" <?php $w->cb( 'scansEnabled_database' ); ?>/></td>
				</tr>
				<tr>
					<th>Scan posts for known dangerous URLs and suspicious content<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_posts_for_known_dangerous_URLs_and_suspicious_content"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_posts" class="wfConfigElem" name="scansEnabled_posts"
					           value="1" <?php $w->cb( 'scansEnabled_posts' ); ?>/></td>
				</tr>
				<tr>
					<th>Scan comments for known dangerous URLs and suspicious content<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_comments_for_known_dangerous_URLs_and_suspicious_content"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_comments" class="wfConfigElem"
					           name="scansEnabled_comments" value="1" <?php $w->cb( 'scansEnabled_comments' ); ?>/></td>
				</tr>
				<tr>
					<th>Scan for out of date plugins, themes and WordPress versions<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_for_out_of_date_plugins.2C_themes_and_WordPress_versions"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_oldVersions" class="wfConfigElem"
					           name="scansEnabled_oldVersions"
					           value="1" <?php $w->cb( 'scansEnabled_oldVersions' ); ?>/></td>
				</tr>
				<tr>
					<th>Check the strength of passwords<a
							href="http://docs.wordfence.com/en/Wordfence_options#Check_the_strength_of_passwords"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_passwds" class="wfConfigElem"
					           name="scansEnabled_passwds" value="1" <?php $w->cb( 'scansEnabled_passwds' ); ?>/></td>
				</tr>
				<tr>
					<th>Monitor disk space<a href="http://docs.wordfence.com/en/Wordfence_options#Monitor_disk_space"
					                         target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_diskSpace" class="wfConfigElem"
					           name="scansEnabled_diskSpace" value="1" <?php $w->cb( 'scansEnabled_diskSpace' ); ?>/>
					</td>
				</tr>
				<tr>
					<th>Scan for unauthorized DNS changes<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_for_unauthorized_DNS_changes"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_dns" class="wfConfigElem" name="scansEnabled_dns"
					           value="1" <?php $w->cb( 'scansEnabled_dns' ); ?>/></td>
				</tr>
				<tr>
					<th>Scan files outside your WordPress installation<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_files_outside_your_WordPress_installation"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="other_scanOutside" class="wfConfigElem" name="other_scanOutside"
					           value="1" <?php $w->cb( 'other_scanOutside' ); ?> /></td>
				</tr>
				<tr>
					<th>Scan images and binary files as if they were executable<a
							href="http://docs.wordfence.com/en/Wordfence_options#Scan_image_files_as_if_they_were_executable"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_scanImages" class="wfConfigElem"
					           name="scansEnabled_scanImages" value="1" <?php $w->cb( 'scansEnabled_scanImages' ); ?> />
					</td>
				</tr>
				<tr>
					<th>Enable HIGH SENSITIVITY scanning. May give false positives.<a
							href="http://docs.wordfence.com/en/Wordfence_options#Enable_HIGH_SENSITIVITY_scanning"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="scansEnabled_highSense" class="wfConfigElem"
					           name="scansEnabled_highSense" value="1" <?php $w->cb( 'scansEnabled_highSense' ); ?> />
					</td>
				</tr>
				<tr>
					<th>Exclude files from scan that match these wildcard patterns. (One per line).<a
							href="http://docs.wordfence.com/en/Wordfence_options#Exclude_files_from_scan_that_match_these_wildcard_patterns."
							target="_blank" class="wfhelp"></a></th>
					<td>
						<textarea id="scan_exclude" class="wfConfigElem" cols="40" rows="4"
							name="scan_exclude"><?php echo wfUtils::cleanupOneEntryPerLine($w->getHTML( 'scan_exclude' )); ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="wfMarker" id="wfMarkerFirewallRules"></div>
						<h3 class="wfConfigHeading">Firewall Rules<a
								href="http://docs.wordfence.com/en/Wordfence_options#Firewall_Rules" target="_blank"
								class="wfhelp"></a></h3>
					</td>
				</tr>
				<tr>
					<th>Immediately block fake Google crawlers:<a
							href="http://docs.wordfence.com/en/Wordfence_options#Immediately_block_fake_Google_crawlers:"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="blockFakeBots" class="wfConfigElem" name="blockFakeBots"
					           value="1" <?php $w->cb( 'blockFakeBots' ); ?>/></td>
				</tr>
				<tr>
					<th>How should we treat Google's crawlers<a
							href="http://docs.wordfence.com/en/Wordfence_options#How_should_we_treat_Google.27s_crawlers"
							target="_blank" class="wfhelp"></a></th>
					<td>
						<select id="neverBlockBG" class="wfConfigElem" name="neverBlockBG">
							<option value="neverBlockVerified"<?php $w->sel( 'neverBlockBG', 'neverBlockVerified' ); ?>>
								Verified Google crawlers have unlimited access to this site
							</option>
							<option value="neverBlockUA"<?php $w->sel( 'neverBlockBG', 'neverBlockUA' ); ?>>Anyone
								claiming to be Google has unlimited access
							</option>
							<option
								value="treatAsOtherCrawlers"<?php $w->sel( 'neverBlockBG', 'treatAsOtherCrawlers' ); ?>>
								Treat Google like any other Crawler
							</option>
						</select></td>
				</tr>
				<tr>
					<th>If anyone's requests exceed:<a
							href="http://docs.wordfence.com/en/Wordfence_options#If_anyone.27s_requests_exceed:"
							target="_blank" class="wfhelp"></a></th>
					<td><?php $rateName = 'maxGlobalRequests';
						require( 'wfRate.php' ); ?> then <?php $throtName = 'maxGlobalRequests_action';
						require( 'wfAction.php' ); ?></td>
				</tr>
				<tr>
					<th>If a crawler's page views exceed:<a
							href="http://docs.wordfence.com/en/Wordfence_options#If_a_crawler.27s_page_views_exceed"
							target="_blank" class="wfhelp"></a></th>
					<td><?php $rateName = 'maxRequestsCrawlers';
						require( 'wfRate.php' ); ?> then <?php $throtName = 'maxRequestsCrawlers_action';
						require( 'wfAction.php' ); ?></td>
				</tr>
				<tr>
					<th>If a crawler's pages not found (404s) exceed:<a
							href="http://docs.wordfence.com/en/Wordfence_options#If_a_crawler.27s_pages_not_found_.28404s.29_exceed"
							target="_blank" class="wfhelp"></a></th>
					<td><?php $rateName = 'max404Crawlers';
						require( 'wfRate.php' ); ?> then <?php $throtName = 'max404Crawlers_action';
						require( 'wfAction.php' ); ?></td>
				</tr>
				<tr>
					<th>If a human's page views exceed:<a
							href="http://docs.wordfence.com/en/Wordfence_options#If_a_human.27s_page_views_exceed"
							target="_blank" class="wfhelp"></a></th>
					<td><?php $rateName = 'maxRequestsHumans';
						require( 'wfRate.php' ); ?> then <?php $throtName = 'maxRequestsHumans_action';
						require( 'wfAction.php' ); ?></td>
				</tr>
				<tr>
					<th>If a human's pages not found (404s) exceed:<a
							href="http://docs.wordfence.com/en/Wordfence_options#If_a_human.27s_pages_not_found_.28404s.29_exceed"
							target="_blank" class="wfhelp"></a></th>
					<td><?php $rateName = 'max404Humans';
						require( 'wfRate.php' ); ?> then <?php $throtName = 'max404Humans_action';
						require( 'wfAction.php' ); ?></td>
				</tr>
				<tr>
					<th>If 404's for known vulnerable URL's exceed:<a
							href="http://docs.wordfence.com/en/Wordfence_options#If_404.27s_for_known_vulnerable_URL.27s_exceed"
							target="_blank" class="wfhelp"></a></th>
					<td><?php $rateName = 'maxScanHits';
						require( 'wfRate.php' ); ?> then <?php $throtName = 'maxScanHits_action';
						require( 'wfAction.php' ); ?></td>
				</tr>
				<tr>
					<th>How long is an IP address blocked when it breaks a rule:<a
							href="http://docs.wordfence.com/en/Wordfence_options#How_long_is_an_IP_address_blocked_when_it_breaks_a_rule"
							target="_blank" class="wfhelp"></a></th>
					<td>
						<select id="blockedTime" class="wfConfigElem" name="blockedTime">
							<option value="60"<?php $w->sel( 'blockedTime', '60' ); ?>>1 minute</option>
							<option value="300"<?php $w->sel( 'blockedTime', '300' ); ?>>5 minutes</option>
							<option value="1800"<?php $w->sel( 'blockedTime', '1800' ); ?>>30 minutes</option>
							<option value="3600"<?php $w->sel( 'blockedTime', '3600' ); ?>>1 hour</option>
							<option value="7200"<?php $w->sel( 'blockedTime', '7200' ); ?>>2 hours</option>
							<option value="21600"<?php $w->sel( 'blockedTime', '21600' ); ?>>6 hours</option>
							<option value="43200"<?php $w->sel( 'blockedTime', '43200' ); ?>>12 hours</option>
							<option value="86400"<?php $w->sel( 'blockedTime', '86400' ); ?>>1 day</option>
							<option value="172800"<?php $w->sel( 'blockedTime', '172800' ); ?>>2 days</option>
							<option value="432000"<?php $w->sel( 'blockedTime', '432000' ); ?>>5 days</option>
							<option value="864000"<?php $w->sel( 'blockedTime', '864000' ); ?>>10 days</option>
							<option value="2592000"<?php $w->sel( 'blockedTime', '2592000' ); ?>>1 month</option>
						</select></td>
				</tr>

				<tr>
					<td colspan="2">
						<div class="wfMarker" id="wfMarkerLoginSecurity"></div>
						<h3 class="wfConfigHeading">Login Security Options<a
								href="http://docs.wordfence.com/en/Wordfence_options#Login_Security_Options"
								target="_blank" class="wfhelp"></a></h3>
					</td>
				</tr>
				<tr>
					<th>Enforce strong passwords?<a
							href="http://docs.wordfence.com/en/Wordfence_options#Enforce_strong_passwords.3F"
							target="_blank" class="wfhelp"></a></th>
					<td>
						<select class="wfConfigElem" id="loginSec_strongPasswds" name="loginSec_strongPasswds">
							<option value="">Do not force users to use strong passwords</option>
							<option value="pubs"<?php $w->sel( 'loginSec_strongPasswds', 'pubs' ); ?>>Force admins and
								publishers to use strong passwords (recommended)
							</option>
							<option value="all"<?php $w->sel( 'loginSec_strongPasswds', 'all' ); ?>>Force all members to
								use strong passwords
							</option>
						</select>
				<tr>
					<th>Lock out after how many login failures<a
							href="http://docs.wordfence.com/en/Wordfence_options#Lock_out_after_how_many_login_failures"
							target="_blank" class="wfhelp"></a></th>
					<td>
						<select id="loginSec_maxFailures" class="wfConfigElem" name="loginSec_maxFailures">
							<option value="1"<?php $w->sel( 'loginSec_maxFailures', '1' ); ?>>1</option>
							<option value="2"<?php $w->sel( 'loginSec_maxFailures', '2' ); ?>>2</option>
							<option value="3"<?php $w->sel( 'loginSec_maxFailures', '3' ); ?>>3</option>
							<option value="4"<?php $w->sel( 'loginSec_maxFailures', '4' ); ?>>4</option>
							<option value="5"<?php $w->sel( 'loginSec_maxFailures', '5' ); ?>>5</option>
							<option value="6"<?php $w->sel( 'loginSec_maxFailures', '6' ); ?>>6</option>
							<option value="7"<?php $w->sel( 'loginSec_maxFailures', '7' ); ?>>7</option>
							<option value="8"<?php $w->sel( 'loginSec_maxFailures', '8' ); ?>>8</option>
							<option value="9"<?php $w->sel( 'loginSec_maxFailures', '9' ); ?>>9</option>
							<option value="10"<?php $w->sel( 'loginSec_maxFailures', '10' ); ?>>10</option>
							<option value="20"<?php $w->sel( 'loginSec_maxFailures', '20' ); ?>>20</option>
							<option value="30"<?php $w->sel( 'loginSec_maxFailures', '30' ); ?>>30</option>
							<option value="40"<?php $w->sel( 'loginSec_maxFailures', '40' ); ?>>40</option>
							<option value="50"<?php $w->sel( 'loginSec_maxFailures', '50' ); ?>>50</option>
							<option value="100"<?php $w->sel( 'loginSec_maxFailures', '100' ); ?>>100</option>
							<option value="200"<?php $w->sel( 'loginSec_maxFailures', '200' ); ?>>200</option>
							<option value="500"<?php $w->sel( 'loginSec_maxFailures', '500' ); ?>>500</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Lock out after how many forgot password attempts<a
							href="http://docs.wordfence.com/en/Wordfence_options#Lock_out_after_how_many_forgot_password_attempts"
							target="_blank" class="wfhelp"></a></th>
					<td>
						<select id="loginSec_maxForgotPasswd" class="wfConfigElem" name="loginSec_maxForgotPasswd">
							<option value="1"<?php $w->sel( 'loginSec_maxForgotPasswd', '1' ); ?>>1</option>
							<option value="2"<?php $w->sel( 'loginSec_maxForgotPasswd', '2' ); ?>>2</option>
							<option value="3"<?php $w->sel( 'loginSec_maxForgotPasswd', '3' ); ?>>3</option>
							<option value="4"<?php $w->sel( 'loginSec_maxForgotPasswd', '4' ); ?>>4</option>
							<option value="5"<?php $w->sel( 'loginSec_maxForgotPasswd', '5' ); ?>>5</option>
							<option value="6"<?php $w->sel( 'loginSec_maxForgotPasswd', '6' ); ?>>6</option>
							<option value="7"<?php $w->sel( 'loginSec_maxForgotPasswd', '7' ); ?>>7</option>
							<option value="8"<?php $w->sel( 'loginSec_maxForgotPasswd', '8' ); ?>>8</option>
							<option value="9"<?php $w->sel( 'loginSec_maxForgotPasswd', '9' ); ?>>9</option>
							<option value="10"<?php $w->sel( 'loginSec_maxForgotPasswd', '10' ); ?>>10</option>
							<option value="20"<?php $w->sel( 'loginSec_maxForgotPasswd', '20' ); ?>>20</option>
							<option value="30"<?php $w->sel( 'loginSec_maxForgotPasswd', '30' ); ?>>30</option>
							<option value="40"<?php $w->sel( 'loginSec_maxForgotPasswd', '40' ); ?>>40</option>
							<option value="50"<?php $w->sel( 'loginSec_maxForgotPasswd', '50' ); ?>>50</option>
							<option value="100"<?php $w->sel( 'loginSec_maxForgotPasswd', '100' ); ?>>100</option>
							<option value="200"<?php $w->sel( 'loginSec_maxForgotPasswd', '200' ); ?>>200</option>
							<option value="500"<?php $w->sel( 'loginSec_maxForgotPasswd', '500' ); ?>>500</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Count failures over what time period<a
							href="http://docs.wordfence.com/en/Wordfence_options#Count_failures_over_what_time_period"
							target="_blank" class="wfhelp"></a></th>
					<td>
						<select id="loginSec_countFailMins" class="wfConfigElem" name="loginSec_countFailMins">
							<option value="5"<?php $w->sel( 'loginSec_countFailMins', '5' ); ?>>5 minutes</option>
							<option value="10"<?php $w->sel( 'loginSec_countFailMins', '10' ); ?>>10 minutes</option>
							<option value="30"<?php $w->sel( 'loginSec_countFailMins', '30' ); ?>>30 minutes</option>
							<option value="60"<?php $w->sel( 'loginSec_countFailMins', '60' ); ?>>1 hour</option>
							<option value="120"<?php $w->sel( 'loginSec_countFailMins', '120' ); ?>>2 hours</option>
							<option value="360"<?php $w->sel( 'loginSec_countFailMins', '360' ); ?>>6 hours</option>
							<option value="720"<?php $w->sel( 'loginSec_countFailMins', '720' ); ?>>12 hours</option>
							<option value="1440"<?php $w->sel( 'loginSec_countFailMins', '1440' ); ?>>1 day</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Amount of time a user is locked out<a
							href="http://docs.wordfence.com/en/Wordfence_options#Amount_of_time_a_user_is_locked_out"
							target="_blank" class="wfhelp"></a></th>
					<td>
						<select id="loginSec_lockoutMins" class="wfConfigElem" name="loginSec_lockoutMins">
							<option value="5"<?php $w->sel( 'loginSec_lockoutMins', '5' ); ?>>5 minutes</option>
							<option value="10"<?php $w->sel( 'loginSec_lockoutMins', '10' ); ?>>10 minutes</option>
							<option value="30"<?php $w->sel( 'loginSec_lockoutMins', '30' ); ?>>30 minutes</option>
							<option value="60"<?php $w->sel( 'loginSec_lockoutMins', '60' ); ?>>1 hour</option>
							<option value="120"<?php $w->sel( 'loginSec_lockoutMins', '120' ); ?>>2 hours</option>
							<option value="360"<?php $w->sel( 'loginSec_lockoutMins', '360' ); ?>>6 hours</option>
							<option value="720"<?php $w->sel( 'loginSec_lockoutMins', '720' ); ?>>12 hours</option>
							<option value="1440"<?php $w->sel( 'loginSec_lockoutMins', '1440' ); ?>>1 day</option>
							<option value="2880"<?php $w->sel( 'loginSec_lockoutMins', '2880' ); ?>>2 days</option>
							<option value="7200"<?php $w->sel( 'loginSec_lockoutMins', '7200' ); ?>>5 days</option>
							<option value="14400"<?php $w->sel( 'loginSec_lockoutMins', '14400' ); ?>>10 days</option>
							<option value="28800"<?php $w->sel( 'loginSec_lockoutMins', '28800' ); ?>>20 days</option>
							<option value="43200"<?php $w->sel( 'loginSec_lockoutMins', '43200' ); ?>>30 days</option>
							<option value="86400"<?php $w->sel( 'loginSec_lockoutMins', '86400' ); ?>>60 days</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Immediately lock out invalid usernames<a
							href="http://docs.wordfence.com/en/Wordfence_options#Immediately_lock_out_invalid_usernames"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="loginSec_lockInvalidUsers" class="wfConfigElem"
					           name="loginSec_lockInvalidUsers" <?php $w->cb( 'loginSec_lockInvalidUsers' ); ?> /></td>
				</tr>
				<tr>
					<th>Don't let WordPress reveal valid users in login errors<a
							href="http://docs.wordfence.com/en/Wordfence_options#Don.27t_let_WordPress_reveal_valid_users_in_login_errors"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="loginSec_maskLoginErrors" class="wfConfigElem"
					           name="loginSec_maskLoginErrors" <?php $w->cb( 'loginSec_maskLoginErrors' ); ?> /></td>
				</tr>
				<tr>
					<th>Prevent users registering 'admin' username if it doesn't exist<a
							href="http://docs.wordfence.com/en/Wordfence_options#Prevent_users_registering_.27admin.27_username_if_it_doesn.27t_exist"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="loginSec_blockAdminReg" class="wfConfigElem"
					           name="loginSec_blockAdminReg" <?php $w->cb( 'loginSec_blockAdminReg' ); ?> /></td>
				</tr>
				<tr>
					<th>Prevent discovery of usernames through '/?author=N' scans<a
							href="http://docs.wordfence.com/en/Wordfence_options#Prevent_discovery_of_usernames_through_.27.3F.2Fauthor.3DN.27_scans"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="loginSec_disableAuthorScan" class="wfConfigElem"
					           name="loginSec_disableAuthorScan" <?php $w->cb( 'loginSec_disableAuthorScan' ); ?> />
					</td>
				</tr>
				<tr>
					<th>Immediately block the IP of users who try to sign in as these usernames<a
							href="http://docs.wordfence.com/en/Wordfence_options#Immediately_block_the_IP_of_users_who_try_to_sign_in_as_these_usernames"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="text" name="loginSec_userBlacklist" id="loginSec_userBlacklist"
					           value="<?php $w->f( 'loginSec_userBlacklist' ); ?>" size="40"/>&nbsp;(Comma
						separated. Existing users won't be blocked.)
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="wfMarker" id="wfMarkerOtherOptions"></div>
						<h3 class="wfConfigHeading">Other Options<a
								href="http://docs.wordfence.com/en/Wordfence_options#Other_Options" target="_blank"
								class="wfhelp"></a></h3>
					</td>
				</tr>

				<tr>
					<th>Whitelisted IP addresses that bypass all rules:<a
							href="http://docs.wordfence.com/en/Wordfence_options#Whitelisted_IP_addresses_that_bypass_all_rules"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="text" name="whitelisted" id="whitelisted"
					           value="<?php $w->f( 'whitelisted' ); ?>" size="40"/></td>
				</tr>
				<tr>
					<th colspan="2" style="color: #999;">Whitelisted IP's must be separated by commas. You can specify
						ranges using the following format: 123.23.34.[1-50]<br/>Wordfence automatically whitelists <a
							href="http://en.wikipedia.org/wiki/Private_network" target="_blank">private networks</a>
						because these are not routable on the public Internet.<br/><br/></th>
				</tr>

				<tr>
					<th>Immediately block IP's that access these URLs:<a
							href="http://docs.wordfence.com/en/Wordfence_options#Immediately_block_IP.27s_that_access_these_URLs"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="text" name="bannedURLs" id="bannedURLs"
					           value="<?php $w->f( 'bannedURLs' ); ?>" size="40"/></td>
				</tr>
				<tr>
					<th colspan="2" style="color: #999;">Separate multiple URL's with commas. If you see an attacker
						repeatedly probing your site for a known vulnerability you can use this to immediately block
						them.<br/>
						All URL's must start with a '/' without quotes and must be relative. e.g. /badURLone/,
						/bannedPage.html, /dont-access/this/URL/
						<br/><br/></th>
				</tr>

				<tr>
					<th style="vertical-align: top;">Whitelisted 404 URLs (one per line). <a href="http://docs.wordfence.com/en/Wordfence_options#Whitelisted_404_URLs" target="_blank" class="wfhelp"></a></th>
					<td><textarea name="allowed404s" id="" cols="40" rows="4"><?php echo $w->getHTML( 'allowed404s' ); ?></textarea></td>
				</tr>
				<tr>
					<th colspan="2" style="color: #999;">These URL patterns will be excluded from
						the throttling rules used to limit crawlers.
						<br/><br/></th>
				</tr>

				<tr>
					<th>Hide WordPress version<a
							href="http://docs.wordfence.com/en/Wordfence_options#Hide_WordPress_version" target="_blank"
							class="wfhelp"></a></th>
					<td><input type="checkbox" id="other_hideWPVersion" class="wfConfigElem" name="other_hideWPVersion"
					           value="1" <?php $w->cb( 'other_hideWPVersion' ); ?> /></td>
				</tr>
				<tr>
					<th>Block IP's who send POST requests with blank User-Agent and Referer<a
							href="http://docs.wordfence.com/en/Wordfence_options#Block_IP.27s_who_send_POST_requests_with_blank_User-Agent_and_Referer" target="_blank"
							class="wfhelp"></a></th>
					<td><input type="checkbox" id="other_blockBadPOST" class="wfConfigElem" name="other_blockBadPOST"
							   value="1" <?php $w->cb( 'other_blockBadPOST' ); ?> /></td>
				</tr>
				<tr>
					<th>Hold anonymous comments using member emails for moderation<a
							href="http://docs.wordfence.com/en/Wordfence_options#Hold_anonymous_comments_using_member_emails_for_moderation"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="other_noAnonMemberComments" class="wfConfigElem"
					           name="other_noAnonMemberComments"
					           value="1" <?php $w->cb( 'other_noAnonMemberComments' ); ?> /></td>
				</tr>
				<tr>
					<th>Filter comments for malware and phishing URL's<a
							href="http://docs.wordfence.com/en/Wordfence_options#Filter_comments_for_malware_and_phishing_URL.27s"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="other_scanComments" class="wfConfigElem" name="other_scanComments"
					           value="1" <?php $w->cb( 'other_scanComments' ); ?> /></td>
				</tr>
				<tr>
					<th>Check password strength on profile update<a
							href="http://docs.wordfence.com/en/Wordfence_options#Check_password_strength_on_profile_update"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="other_pwStrengthOnUpdate" class="wfConfigElem"
					           name="other_pwStrengthOnUpdate"
					           value="1" <?php $w->cb( 'other_pwStrengthOnUpdate' ); ?> /></td>
				</tr>
				<tr>
					<th>Participate in the Real-Time WordPress Security Network<a
							href="http://docs.wordfence.com/en/Wordfence_options#Participate_in_the_Real-Time_WordPress_Security_Network"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="other_WFNet" class="wfConfigElem" name="other_WFNet"
					           value="1" <?php $w->cb( 'other_WFNet' ); ?> /></td>
				</tr>
				<tr>
					<th>How much memory should Wordfence request when scanning<a
							href="http://docs.wordfence.com/en/Wordfence_options#How_much_memory_should_Wordfence_request_when_scanning"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="text" id="maxMem" name="maxMem" value="<?php $w->f( 'maxMem' ); ?>" size="4"/>Megabytes
					</td>
				</tr>
				<tr>
					<th>Maximum execution time for each scan stage<a
							href="http://docs.wordfence.com/en/Wordfence_options#Maximum_execution_time_for_each_scan_stage"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="text" id="maxExecutionTime" name="maxExecutionTime"
					           value="<?php $w->f( 'maxExecutionTime' ); ?>" size="4"/>Blank for default. Must be
						greater than 9.
					</td>
				</tr>
				<tr>
					<th>Update interval in seconds (2 is default)<a
							href="http://docs.wordfence.com/en/Wordfence_options#Update_interval_in_seconds"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="text" id="actUpdateInterval" name="actUpdateInterval"
					           value="<?php $w->f( 'actUpdateInterval' ); ?>" size="4"/>Setting higher will reduce
						browser traffic but slow scan starts, live traffic &amp; status updates.
					</td>
				</tr>
				<tr>
					<th>Enable debugging mode (increases database load)<a
							href="http://docs.wordfence.com/en/Wordfence_options#Enable_debugging_mode_.28increases_database_load.29"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="debugOn" class="wfConfigElem" name="debugOn"
					           value="1" <?php $w->cb( 'debugOn' ); ?> /></td>
				</tr>
				<tr>
					<th>Delete Wordfence tables and data on deactivation?<a
							href="http://docs.wordfence.com/en/Wordfence_options#Delete_Wordfence_tables_and_data_on_deactivation.3F"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="deleteTablesOnDeact" class="wfConfigElem" name="deleteTablesOnDeact"
					           value="1" <?php $w->cb( 'deleteTablesOnDeact' ); ?> /></td>
				</tr>


				<tr>
					<th>Disable Wordfence Cookies<a
							href="http://docs.wordfence.com/en/Wordfence_options#Disable_Wordfence_Cookies"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="disableCookies" class="wfConfigElem" name="disableCookies"
					           value="1" <?php $w->cb( 'disableCookies' ); ?> />(when enabled all visits in live traffic
						will appear to be new visits)
					</td>
				</tr>
				<tr>
					<th>Start all scans remotely<a
							href="http://docs.wordfence.com/en/Wordfence_options#Start_all_scans_remotely"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="startScansRemotely" class="wfConfigElem" name="startScansRemotely"
					           value="1" <?php $w->cb( 'startScansRemotely' ); ?> />(Try this if your scans aren't
						starting and your site is publicly accessible)
					</td>
				</tr>
				<tr>
					<th>Disable config caching<a
							href="http://docs.wordfence.com/en/Wordfence_options#Disable_config_caching" target="_blank"
							class="wfhelp"></a></th>
					<td><input type="checkbox" id="disableConfigCaching" class="wfConfigElem"
					           name="disableConfigCaching" value="1" <?php $w->cb( 'disableConfigCaching' ); ?> />(Try
						this if your options aren't saving)
					</td>
				</tr>
				<tr>
					<th>Add a debugging comment to HTML source of cached pages.<a
							href="http://docs.wordfence.com/en/Wordfence_options#Add_a_debugging_comment_to_HTML_source_of_cached_pages"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="addCacheComment" class="wfConfigElem" name="addCacheComment"
					           value="1" <?php $w->cb( 'addCacheComment' ); ?> />
						<?php if ($w->get('allowHTTPSCaching')): ?>
							<input type="hidden" name="allowHTTPSCaching" value="1"/>
						<?php endif ?>
					</td>
				</tr>
				<tr>
					<th><label for="disableCodeExecutionUploads">Disable Code Execution for Uploads directory</label><a
							href="http://docs.wordfence.com/en/Wordfence_options#Disable_Code_Execution_for_Uploads_directory"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="checkbox" id="disableCodeExecutionUploads" class="wfConfigElem"
					           name="disableCodeExecutionUploads"
					           value="1" <?php $w->cb( 'disableCodeExecutionUploads' ); ?> /></td>
				</tr>
				<tr>
					<th><label for="ssl_verify">Enable SSL Verification</label><a
							href="http://docs.wordfence.com/en/Wordfence_options#Enable_SSL_Verification"
							target="_blank" class="wfhelp"></a>
					</th>
					<td style="vertical-align: top;"><input type="checkbox" id="ssl_verify" class="wfConfigElem"
					           name="ssl_verify"
					           value="1" <?php $w->cb( 'ssl_verify' ); ?> />
						(Disable this if you are <strong><em>consistently</em></strong> unable to connect to the Wordfence servers.)
					</td>
				</tr>
				<tr>
					<th colspan="2"><a
							href="<?php echo wfUtils::siteURLRelative(); ?>?_wfsf=conntest&nonce=<?php echo wp_create_nonce( 'wp-ajax' ); ?>"
							target="_blank">Click to test connectivity to the Wordfence API servers</a><a
							href="http://docs.wordfence.com/en/Wordfence_options#Click_to_test_connectivity_to_the_Wordfence_API_servers"
							target="_blank" class="wfhelp"></a></th>
				</tr>
				<tr>
					<th colspan="2"><a
							href="<?php echo wfUtils::siteURLRelative(); ?>?_wfsf=sysinfo&nonce=<?php echo wp_create_nonce( 'wp-ajax' ); ?>"
							target="_blank">Click to view your system's configuration in a new window</a><a
							href="http://docs.wordfence.com/en/Wordfence_options#Click_to_view_your_system.27s_configuration_in_a_new_window"
							target="_blank" class="wfhelp"></a></th>
				</tr>
				<tr>
					<th colspan="2"><a
							href="<?php echo wfUtils::siteURLRelative(); ?>?_wfsf=cronview&nonce=<?php echo wp_create_nonce( 'wp-ajax' ); ?>"
							target="_blank">Click to view your systems scheduled jobs in a new window</a><a
							href="http://docs.wordfence.com/en/Wordfence_options#Click_to_view_your_system.27s_scheduled_jobs_in_a_new_window"
							target="_blank" class="wfhelp"></a></th>
				</tr>
				<tr>
					<th colspan="2"><a
							href="<?php echo wfUtils::siteURLRelative(); ?>?_wfsf=dbview&nonce=<?php echo wp_create_nonce( 'wp-ajax' ); ?>"
							target="_blank">Click to see a list of your system's database tables in a new window</a><a
							href="http://docs.wordfence.com/en/Wordfence_options#Click_to_see_a_list_of_your_system.27s_database_tables_in_a_new_window"
							target="_blank" class="wfhelp"></a></th>
				</tr>
				<tr>
					<th colspan="2"><a
							href="<?php echo wfUtils::siteURLRelative(); ?>?_wfsf=testmem&nonce=<?php echo wp_create_nonce( 'wp-ajax' ); ?>"
							target="_blank">Test your WordPress host's available memory</a><a
							href="http://docs.wordfence.com/en/Wordfence_options#Test_your_WordPress_host.27s_available_memory"
							target="_blank" class="wfhelp"></a></th>
				</tr>
				<tr>
					<th>Send a test email from this WordPress server to an email address:<a
							href="http://docs.wordfence.com/en/Wordfence_options#Send_a_test_email_from_this_WordPress_server_to_an_email_address"
							target="_blank" class="wfhelp"></a></th>
					<td><input type="text" id="testEmailDest" value="" size="20" maxlength="255" class="wfConfigElem"/>
						<input type="button" value="Send Test Email"
						       onclick="WFAD.sendTestEmail(jQuery('#testEmailDest').val());"/></td>
				</tr>

				<tr>
					<td colspan="2">
						<div class="wfMarker" id="wfMarkerExportOptions"></div>
						<h3 class="wfConfigHeading">Exporting and Importing Wordfence Settings<a
								href="http://docs.wordfence.com/en/Wordfence_options#Exporting_and_Importing_Wordfence_Settings"
								target="_blank" class="wfhelp"></a></h3>
					</td>
				</tr>

				<tr>
					<th>Export this site's Wordfence settings for import on another site:</th>
					<td><input type="button" id="exportSettingsBut" value="Export Wordfence Settings"
					           onclick="WFAD.exportSettings(); return false;"/></td>
				</tr>
				<tr>
					<th>Import Wordfence settings from another site using a token:</th>
					<td><input type="text" size="20" value="" id="importToken"/>&nbsp;<input type="button"
					                                                                         name="importSettingsButton"
					                                                                         value="Import Settings"
					                                                                         onclick="WFAD.importSettings(jQuery('#importToken').val()); return false;"/>
					</td>
				</tr>
			</table>
			<p>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><input type="button" id="button1" name="button1" class="button-primary" value="Save Changes"
					           onclick="WFAD.saveConfig();"/></td>
					<td style="height: 24px;">
						<div class="wfAjax24"></div>
						<span class="wfSavedMsg">&nbsp;Your changes have been saved!</span></td>
				</tr>
			</table>
			</p>
		</div>
	</form>
</div>
<script type="text/x-jquery-template" id="wfContentBasicOptions">
	<div>
		<h3>Basic Options</h3>

		<p>
			Using Wordfence is simple. Install Wordfence, enter an email address on this page to send alerts to, and
			then do your first scan and work through the security alerts we provide.
			We give you a few basic security levels to choose from, depending on your needs. Remember to hit the "Save"
			button to save any changes you make.
		</p>

		<p>
			If you use the free edition of Wordfence, you don't need to worry about entering an API key in the "API Key"
			field above. One is automatically created for you. If you choose to <a
				href="https://www.wordfence.com/gnl1optUpg1/wordfence-signup/" target="_blank">upgrade to Wordfence Premium
				edition</a>, you will receive an API key. You will need to copy and paste that key into the "API Key"
			field above and hit "Save" to activate your key.
		</p>
	</div>
</script>
<script type="text/x-jquery-template" id="wfContentLiveTrafficOptions">
	<div>
		<h3>Live Traffic Options</h3>

		<p>
			These options let you ignore certain types of visitors, based on their level of access, usernames, IP
			address or browser type.
			If you run a very high traffic website where it is not feasible to see your visitors in real-time, simply
			un-check the live traffic option and nothing will be written to the Wordfence tracking tables.
		</p>
	</div>
</script>
<script type="text/x-jquery-template" id="wfContentScansToInclude">
	<div>
		<h3>Scans to Include</h3>

		<p>
			This section gives you the ability to fine-tune what we scan.
			If you use many themes or plugins from the public WordPress directory we recommend you
			enable theme and plugin scanning. This will verify the integrity of all these themes and plugins and alert
			you of any changes.

		<p>

		<p>
			The option to "scan files outside your WordPress installation" will cause Wordfence to do a much wider
			security scan
			that is not limited to your base WordPress directory and known WordPress subdirectories. This scan may take
			longer
			but can be very useful if you have other infected files outside this WordPress installation that you would
			like us to look for.
		</p>
	</div>
</script>
<script type="text/x-jquery-template" id="wfContentFirewallRules">
	<div>
		<h3>Firewall Rules</h3>

		<p>
			<strong>NOTE:</strong> Before modifying these rules, make sure you have access to the email address
			associated with this site's administrator account. If you accidentally lock yourself out, you will be given
			the option
			to enter that email address and receive an "unlock email" which will allow you to regain access.
		</p>

		<p>
			<strong>Tips:</strong>

		<p>&#8226; If you choose to limit the rate at which your site can be accessed, you need to customize the
			settings for your site.</p>

		<p>&#8226; If your users usually skip quickly between pages, you should set the values for human visitors to be
			high.</p>

		<p>&#8226; If you are aggressively crawled by non-Google crawlers like Baidu, you should set the page view limit
			for crawlers to a high value.</p>

		<p>&#8226; If you are currently under attack and want to aggressively protect your site or your content, you can
			set low values for most options.</p>

		<p>&#8226; In general we recommend you don't block fake Google crawlers unless you have a specific problem with
			someone stealing your content.</p>

		<p>
			Remember that as long as you have your administrator email set correctly in this site's user administration,
			and you are able to receive email at that address,
			you will be able to regain access if you are accidentally locked out because your rules are too strict.
		</p>
	</div>
</script>
<script type="text/x-jquery-template" id="wfContentLoginSecurity">
	<div>
		<h3>Login Security</h3>

		<p>
			We have found that real brute force login attacks make hundreds or thousands of requests trying to guess
			passwords or user login names.
			So in general you can leave the number of failed logins before a user is locked out as a fairly high number.
			We have found that blocking after 20 failed attempts is sufficient for most sites and it allows your real
			site users enough
			attempts to guess their forgotten passwords without getting locked out.
		</p>
	</div>
</script>
<script type="text/x-jquery-template" id="wfContentOtherOptions">
	<div>
		<h3>Other Options</h3>

		<p>
			We have worked hard to make Wordfence memory efficient and much of the heavy lifting is done for your site
			by our cloud scanning servers in our Seattle data center.
			On most sites Wordfence will only use about 8 megabytes of additional memory when doing a scan, even if you
			have large files or a large number of files.
			You should not have to adjust the maximum memory that Wordfence can use, but we have provided the option.
			Remember that this does not affect the actual memory usage of Wordfence, simply the maximum Wordfence can
			use if it needs to.
		</p>

		<p>
			You may find debugging mode helpful if Wordfence is not able to start a scan on your site or
			if you are experiencing some other problem. Enable debugging by checking the box, save your options
			and then try to do a scan. You will notice a lot more output on the "Scan" page.
		</p>

		<p>
			If you decide to permanently remove Wordfence, you can choose the option to delete all data on deactivation.
			We also provide helpful links at the bottom of this page which lets you see your systems configuration and
			test how
			much memory your host really allows you to use.
		</p>

		<p>
			Thanks for completing this tour and I'm very happy to have you as our newest Wordfence customer. Don't
			forget to <a href="http://wordpress.org/extend/plugins/wordfence/" target="_blank">rate us 5 stars if you
				love Wordfence</a>.<br/>
			<br/>
			<strong>Mark Maunder</strong> - Wordfence Creator.
		</p>
	</div>
</script>

