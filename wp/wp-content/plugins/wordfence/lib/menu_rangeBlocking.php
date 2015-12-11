<div class="wordfenceModeElem" id="wordfenceMode_rangeBlocking"></div>
<div class="wrap" id="paidWrap">
	<?php require('menuHeader.php'); ?>
	<?php $helpLink="http://docs.wordfence.com/en/Advanced_Blocking"; $helpLabel="Learn more about Advanced Blocking"; $pageTitle = "Advanced Blocking"; include('pageTitle.php'); ?>
	<div class="wordfenceWrap" style="margin: 20px 20px 20px 30px;">
		<p>
			<?php if(! wfConfig::get('firewallEnabled')){ ?><div style="color: #F00; font-weight: bold;">Firewall is disabled. You can enable it on the <a href="admin.php?page=WordfenceSecOpt">Wordfence Options page</a> at the top.</div><br /><?php } ?>
			<table class="wfConfigForm">
				<tr><th>IP address range:</th><td><input id="ipRange" type="text" size="30" maxlength="255" value="<?php 
					if( isset( $_GET['wfBlockRange'] ) && preg_match('/^[\da-f\.\s\t\-:]+$/i', $_GET['wfBlockRange']) ){ echo wp_kses($_GET['wfBlockRange'], array()); }
					?>" onkeyup="WFAD.calcRangeTotal();">&nbsp;<span id="wfShowRangeTotal"></span></td></tr>
				<tr><td></td><td style="padding-bottom: 15px;"><strong>Examples:</strong> 192.168.200.200 - 192.168.200.220</td></tr>
				<tr><th>Hostname:</th><td><input id="hostname" type="text" size="30" maxlength="255" value="<?php
					if( isset( $_GET['wfBlockHostname'] ) ){ echo esc_attr($_GET['wfBlockHostname']); }
					?>" onkeyup="WFAD.calcRangeTotal();">&nbsp;<span id="wfShowRangeTotal"></span></td></tr>
				<tr><td><em class="small">
							Using this setting will make a DNS query<br>
							per unique IP address (per visitor),<br>
							and can add additional load. High traffic<br> sites may not want to use this feature.</em>
					</td><td style="padding-bottom: 15px;vertical-align: top;"><strong>Examples:</strong> *.amazonaws.com, *.linode.com</td></tr>
				<tr><th>User-Agent (browser) that matches:</th><td><input id="uaRange" type="text" size="30" maxlength="255" >&nbsp;(Case insensitive)</td></tr>
				<tr><td></td><td style="padding-bottom: 15px;"><strong>Examples:</strong> *badRobot*, AnotherBadRobot*, *someBrowserSuffix</td></tr>
				<tr><th>Referer (website visitor arrived from) that matches:</th><td><input id="wfreferer" type="text" size="30" maxlength="255" >&nbsp;(Case insensitive)</td></tr>
				<tr><td></td><td style="padding-bottom: 15px;"><strong>Examples:</strong> *badWebsite*, AnotherBadWebsite*, *someWebsiteSuffix</td></tr>
				<tr><th>Enter a reason you're blocking this visitor pattern:</th><td><input id="wfReason" type="text" size="30" maxlength="255"></td></tr>
				<tr><td></td><td style="padding-bottom: 15px;"><strong>Why a reason:</strong> The reason you specify above is for your own record keeping.</td></tr>
				<tr><td colspan="2" style="padding-top: 15px;">
					<input type="button" name="but3" class="button-primary" value="Block Visitors Matching this Pattern" onclick="WFAD.blockIPUARange(jQuery('#ipRange').val(), jQuery('#hostname').val(), jQuery('#uaRange').val(), jQuery('#wfreferer').val(), jQuery('#wfReason').val()); return false;" />
				</td></tr>
			</table>
		</p>
		<p>
			<h2>Current list of ranges and patterns you've blocked</h2>
			<div id="currentBlocks"></div>
		</p>
	</div>
</div>
<script type="text/x-jquery-template" id="wfBlockedRangesTmpl">
<div>
<div style="padding-bottom: 10px; margin-bottom: 10px;">
<table border="0" style="width: 100%" class="block-ranges-table">
{{each(idx, elem) results}}
<tr><td>
	{{if patternDisabled}}
	<div style="width: 500px; margin-top: 20px;">
		<span style="color: #F00;">Pattern Below has been DISABLED:</span> Falcon engine does not support advanced blocks that include combinations of IP range, browser pattern and referring website. You can only specify one of the three in patterns when using Falcon.
	</div>
	<div style="color: #AAA;">
	{{/if}}
	<div>
		<strong>IP Range:</strong>&nbsp;${ipPattern}
	</div>
	<div>
		<strong>Hostname:</strong>&nbsp;${hostnamePattern}
	</div>
	<div>
		<strong>Browser Pattern:</strong>&nbsp;${browserPattern}
	</div>
	<div>
		<strong>Source website:</strong>&nbsp;${refererPattern}
	</div>
	<div>
		<strong>Reason:</strong>&nbsp;${reason}
	</div>
	<div><a href="#" onclick="WFAD.unblockRange('${id}'); return false;">Delete this blocking pattern</a></div>
	{{if patternDisabled}}
	</div>
	{{/if}}
</td>
<td style="color: #999;">
	<ul>
	<li>${totalBlocked} blocked hits</li>
	{{if lastBlockedAgo}}
	<li>Last blocked: ${lastBlockedAgo}</li>
	{{/if}}
	</ul>
</td></tr>
{{/each}}
</table>
</div>
</div>
</script>
<script type="text/x-jquery-template" id="wfWelcomeContentRangeBlocking">
<div>
<h3>Block Networks &amp; Browsers</h3>
<strong><p>Easily block advanced attacks</p></strong>
<p>
	Advanced Blocking is a new feature in Wordfence that lets you block whole networks and certain types of web browsers.
	You'll sometimes find a smart attacker will change their IP address frequently to make it harder to identify and block
	the attack. Usually those attackers stick to a certain network or IP address range. 
	Wordfence lets you block entire networks using Advanced blocking to easily defeat advanced attacks.
</p>
<p>
	You may also find an attacker that is identifying themselves as a certain kind of web browser that your 
	normal visitors don't use. You can use our User-Agent or Browser ID blocking feature to easily block
	attacks like this.
</p>
<p>
	You can also block any combination of network address range and User-Agent by specifying both in Wordfence Advanced Blocking.
	As always we keep track of how many attacks have been blocked and when the last attack occured so that you know
	when it's safe to remove the blocking rule. 
</p>
</div>
</script>
