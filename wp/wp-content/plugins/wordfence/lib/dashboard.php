
<table border="0">
<?php 
	$lastAdminLogin = wfConfig::get_ser('lastAdminLogin', false);
	if($lastAdminLogin){
?>
<tr><td style="padding-right: 20px;">Last Admin Username Login:</td><td>
<?php
	echo '<strong>' . $lastAdminLogin['username'] . '</strong> [' . $lastAdminLogin['firstName'] . ' ' . $lastAdminLogin['lastName'] . ']</td></tr><tr style="padding-right: 20px;"><td>Last Admin Login Time</td><td>' . $lastAdminLogin['time'] . '</td></tr><tr><td style="padding-right: 20px;">Last Admin Login IP:</td><td>' . $lastAdminLogin['IP'] . '</td></tr>';
?>
</td></tr>
<?php } ?>
<?php if(wfConfig::get('firewallEnabled')){ ?><tr><td style="padding-right: 20px;">Firewall Enabled:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('loginSecurityEnabled')){ ?><tr><td style="padding-right: 20px;">Login Security Enabled:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('other_scanComments')){ ?><tr><td style="padding-right: 20px;">Comment Filter Enabled:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('other_hideWPVersion')){ ?><tr><td style="padding-right: 20px;">WordPress Version Hiding Enabled:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('other_pwStrengthOnUpdate')){ ?><tr><td style="padding-right: 20px;">Password Strength Checking Enabled:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('other_WFNet')){ ?><tr><td style="padding-right: 20px;">Security Network Active:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('loginSec_maskLoginErrors')){ ?><tr><td style="padding-right: 20px;">Username Hiding in login form errors:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('loginSec_disableAuthorScan')){ ?><tr><td style="padding-right: 20px;">Prevent username discovery by bots:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('loginSec_blockAdminReg')){ ?><tr><td style="padding-right: 20px;">Protect 'admin' username from being registered:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('loginSec_userBlacklist')){ ?><tr><td style="padding-right: 20px;">Instant blocking of specific username attempts:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('other_noAnonMemberComments')){ ?><tr><td style="padding-right: 20px;">Hold anon comments by existing usernames:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('loginSec_lockInvalidUsers')){ ?><tr><td style="padding-right: 20px;">Instant lockout of invalid usernames:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('loginSec_strongPasswds') == 'all' || wfConfig::get('loginSec_strongPasswds') == 'pubs'){ ?><tr><td style="padding-right: 20px;">Enforce strong passwords:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>

<?php if(wfConfig::get('scheduledScansEnabled')){ ?>
<?php if(wfConfig::get('scheduledScansEnabled')){ ?><tr><td style="padding-right: 20px;">Security Scans Enabled:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_public')){ ?><tr><td style="padding-right: 20px;">Scan public facing site:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_heartbleed')){ ?><tr><td style="padding-right: 20px;">Scan for HeartBleed Vulnerability:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_core')){ ?><tr><td style="padding-right: 20px;">Scan Core Files:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_themes')){ ?><tr><td style="padding-right: 20px;">Scan Themes:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_plugins')){ ?><tr><td style="padding-right: 20px;">Scan Plugins:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_fileContents')){ ?><tr><td style="padding-right: 20px;">Scan Other Files:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_database')){ ?><tr><td style="padding-right: 20px;">Scan Database:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_posts')){ ?><tr><td style="padding-right: 20px;">Scan Posts:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_comments')){ ?><tr><td style="padding-right: 20px;">Scan Comments:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_oldVersions')){ ?><tr><td style="padding-right: 20px;">Scan for Old Software:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_highSense')){ ?><tr><td style="padding-right: 20px;">High sensitivity scans enabled:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_scanImages')){ ?><tr><td style="padding-right: 20px;">Scan image files for executable code:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('other_scanOutside')){ ?><tr><td style="padding-right: 20px;">Scan files outside WordPress install:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_dns')){ ?><tr><td style="padding-right: 20px;">Scan for DNS changes:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php if(wfConfig::get('scansEnabled_diskSpace')){ ?><tr><td style="padding-right: 20px;">Monitor disk space:</td><td style="color: #0F0;">&#10004;</td></tr> <?php } ?>
<?php } ?>
<?php if(wfConfig::get('debugOn')){ ?><tr><td style="padding-right: 20px;">Wordfence DEBUG mode enabled:</td><td style="color: #F00;">DEBUG ENABLED</td></tr> <?php } ?>


</table>

