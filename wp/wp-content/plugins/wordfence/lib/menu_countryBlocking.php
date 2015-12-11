<?php
require('wfBulkCountries.php');
?>
<script type="text/javascript">
WFAD.countryMap = <?php echo json_encode($wfBulkCountries); ?>;
</script>
<div class="wordfenceModeElem" id="wordfenceMode_countryBlocking"></div>
<div class="wrap" id="paidWrap">
	<?php require('menuHeader.php'); ?>
	<?php $pageTitle = "Block Selected Countries from Accessing your Site"; $helpLink="http://docs.wordfence.com/en/Country_blocking"; $helpLabel="Learn more about Country Blocking"; include('pageTitle.php'); ?>
<?php if(! wfConfig::get('isPaid')){ ?>
		<div class="wfPaidOnlyNotice">
			<strong>Country Blocking is only available to Premium Members at this time</strong><br /><br />
			Country Blocking is a premium feature because we have licensed a very accurate commercial geolocation database to provide this feature. If you would like to
			activate this feature, simply <a href="https://www.wordfence.com/gnl1countryBlock1/wordfence-signup/" target="_blank">click here and get a premium Wordfence API Key</a>, and then copy and paste it into your options page. You can <a href="http://docs.wordfence.com/en/Country_blocking" target="_blank">learn more about Country Blocking on our documentation website</a>.
		</div>
<?php } ?>
		<?php if(wfConfig::get('cacheType') == 'falcon'){ ?>
		<div class="wfFalconNotice">
			<b>Note regarding country blocking with Falcon Engine enabled:</b><br /><br />
			Country blocking will only work on the login page and other dynamic pages with
			Wordfence Falcon Engine enabled. We do this to keep your site fast and avoid
			a country lookup on every request. 
			Serving cached pages only uses 2 to 3% of the resources that a non-cached page uses,
			so malicious countries won't eat up your server resources when they load cached pages.
			If you would like full country blocking, you can enable Basic Caching on the "Site Performance" page.
		</div>
		<?php } ?>

	<div class="wordfenceWrap" style="margin: 20px 20px 20px 30px;">
		<table class="wfConfigForm">
		<tr><td colspan="2"><h2>Country Blocking Options</h2></td></tr>
		<?php if(! wfConfig::get('firewallEnabled')){ ?><tr><td colspan="2"><div style="color: #F00; font-weight: bold;">Firewall is disabled. You can enable it on the <a href="admin.php?page=WordfenceSecOpt">Wordfence Options page</a> at the top.</div></td></tr><?php } ?>
		<tr><th>What to do when we block someone:</th><td>
			<select id="wfBlockAction">
				<option value="block"<?php if(wfConfig::get('cbl_action') == 'block'){ echo ' selected'; } ?>>Show the standard Wordfence blocked message</option>
				<option value="redir"<?php if(wfConfig::get('cbl_action') == 'redir'){ echo ' selected'; } ?>>Redirect to the URL below</option>
			</select>
			</td></tr>
		<tr><th>URL to redirect blocked users to:</th><td><input type="text" id="wfRedirURL" size="40" value="<?php if(wfConfig::get('cbl_redirURL')){ echo esc_attr(wfConfig::get('cbl_redirURL')); } ?>" />
		<br />
		<span style="color: #999;">Must start with http:// for example http://yoursite.com/blocked/</span></td></tr>
		<tr><th>Block countries even if they are logged in:</th><td><input type="checkbox" id="wfLoggedInBlocked" value="1" <?php if(wfConfig::get('cbl_loggedInBlocked')){ echo 'checked'; } ?> /></td></tr>
		<tr><th>Block access to the login form:</th><td><input type="checkbox" id="wfLoginFormBlocked" value="1" <?php if(wfConfig::get('cbl_loginFormBlocked')){ echo 'checked'; } ?> /></td></tr>
		<tr><th>Block access to the rest of the site (outside the login form):</th><td><input type="checkbox" id="wfRestOfSiteBlocked" value="1" <?php if(wfConfig::get('cbl_restOfSiteBlocked')){ echo 'checked'; } ?> /></td></tr>
		<tr><td colspan="2"><h2>Advanced Country Blocking Options</h2></td></tr>
		<tr><th colspan="2">
			If user hits the URL 
			<input type="text" id="wfBypassRedirURL" value="<?php echo esc_attr(wfConfig::get('cbl_bypassRedirURL'), array()); ?>" size="20" />
			then redirect that user to 
			<input type="text" id="wfBypassRedirDest" value="<?php echo esc_attr(wfConfig::get('cbl_bypassRedirDest'), array()); ?>" size="20" /> and set a cookie that will bypass all country blocking.
			</th></tr>
		<tr><th colspan="2">
			If user who is allowed to access the site views the URL 
			<input type="text" id="wfBypassViewURL" value="<?php echo esc_attr(wfConfig::get('cbl_bypassViewURL', ""), array()); ?>" size="20" />
			then set a cookie that will bypass country blocking in future in case that user hits the site from a blocked country. 
			</th></tr>

		</table>
		<h2>Select which countries to block</h2>
		<div id="wfBulkBlockingContainer" style="margin-bottom: 10px;">
			<a href="#" onclick="jQuery('.wfCountryCheckbox').prop('checked', true); return false;">Select All</a>&nbsp;&nbsp;
			<a href="#" onclick="jQuery('.wfCountryCheckbox').prop('checked', false); return false;">Deselect All</a>&nbsp;&nbsp;
			<table border="0" cellpadding="0" cellspacing="0">
			<tr>
			<?php 
				$counter = 0;
				asort($wfBulkCountries);
				foreach($wfBulkCountries as $code => $name){
					echo '<td style=""><input class="wfCountryCheckbox" id="wfCountryCheckbox_' . $code . '" type="checkbox" value="' . $code . '" />&nbsp;' . $name . '&nbsp;&nbsp;&nbsp;</td>';
					$counter++;
					if($counter % 5 == 0){
						echo "</tr><tr>\n";
					}
				}
			?>
			</tr>
			</table>
		</div>
		<table border="0" cellpadding="0" cellspacing="0"><tr>
			<td><input type="button" name="but4" class="button-primary" value="Save blocking options and country list" onclick="WFAD.saveCountryBlocking();" /></td>
			<td style="height: 24px;"><div class="wfAjax24"></div><span class="wfSavedMsg">&nbsp;Your changes have been saved!</span></td></tr>
		</table>
		<span style="font-size: 10px;">Note that we use an IP to country database that is 99.5% accurate to identify which country a visitor is from.</span>
	</div>
</div>
<script type="text/javascript">
jQuery(function(){ WFAD.setOwnCountry('<?php echo wfUtils::IP2Country(wfUtils::getIP()); ?>'); });
<?php
if(wfConfig::get('cbl_countries')){
?>
jQuery(function(){ WFAD.loadBlockedCountries('<?php echo wfConfig::get('cbl_countries'); ?>'); });
<?php
}
?>
</script>
<script type="text/x-jquery-template" id="wfWelcomeContentCntBlk">
<div>
<h3>Premium Feature: Block or redirect countries</h3>
<strong><p>Being targeted by hackers in a specific country?</p></strong>
<p>
	The premium version of Wordfence offers country blocking.
	This uses a commercial geolocation database to block hackers, spammers
	or other malicious traffic by country with a 99.5% accuracy rate.
</p>
<p>
<?php
if(wfConfig::get('isPaid')){
?>
	You have upgraded to the premium version of Wordfence and have full access
	to this feature along with our other premium features and priority support.
<?php
} else {
?>
	If you would like access to this premium feature, please 
	<a href="https://www.wordfence.com/gnl1countryBlock2/wordfence-signup/" target="_blank">upgrade to our premium version</a>.
</p>
<?php
}
?>
</div>
</script>
