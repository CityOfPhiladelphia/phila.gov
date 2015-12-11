<div class="wordfenceModeElem" id="wordfenceMode_perfStats"></div>
<style type="text/css">
.wfPerfContentParent {
	margin: 20px 0 20px 10px; 
	background-color: #FFF; 
	padding: 5px; 
	overflow-x: scroll; 
	width: 80%; 
	border: 1px solid #666; 
	border-radius: 5px;
}
.wfPerfContentChild {
	width: 900px; 
	overflow: auto;
}
.wfPerfItem {
	margin: 0 1px 0 1px; 
	float: left; 
	height: 17px; 
	border: 1px solid #999;
	font-size: 9px;
	font-family: Arial;
	color: #555;
	font-weight: normal;
	text-align: center;
	border-radius: 2px;
	line-height: 9px;
}
.wfPerfPrefix {
	background-color: #FFF; 
	padding: 5px 5px 0 5px;
	height: 12px;
	width: 200px;
	background-color: #EFEFEF;
}
.wfPerfKey {
	background-color: #999;
	padding: 5px 5px 0 5px;
	height: 12px;
	background-color: #EFEFEF;
}
.wfPerfKeyHead {
	width: 150px;
}
</style>

<div class="wrap">
	<?php require('menuHeader.php'); ?>
	<div class="wordfence-lock-icon wordfence-icon32"><br /></div>
	<h2 id="wfHeading">
		<div style="float: left;">
			Your Site Performance in Real-Time
		</div>
		<div class="wordfenceWrap" style="margin: 5px 0 0 15px; float: left;">
			<div class="wfOnOffSwitch" id="wfOnOffSwitchID">
				<input type="checkbox" name="wfOnOffSwitch" class="wfOnOffSwitch-checkbox" id="wfPerfOnOff" <?php if(wfConfig::get('perfLoggingEnabled')){ echo ' checked '; } ?>>
				<label class="wfOnOffSwitch-label" for="wfPerfOnOff">
					<div class="wfOnOffSwitch-inner"></div>
					<div class="wfOnOffSwitch-switch"></div>
				</label>
			</div>
		</div>
	</h2>
	<br clear="left" />
	<div style="margin: 20px; width: 1100px;">
		<div class="wfPerfItem wfPerfKey wfPerfKeyHead">Network &amp; Server Performance Key:</div>
		<div class="wfPerfItem wfPerfKey" style="background-color: #fdff47;">Time taken for DNS lookup</div>
		<div class="wfPerfItem wfPerfKey" style="background-color: #80ff80;">Time for browser to connect to your web server</div>
		<div class="wfPerfItem wfPerfKey" style="background-color: #89a1ff;">Time for browser to send its request</div>
		<div class="wfPerfItem wfPerfKey" style="background-color: #ff7878;">Time until browser receives the last byte of the response</div>
		<div style="clear: left; height: 5px; width: 100px;"></div>
		<div class="wfPerfItem wfPerfKey wfPerfKeyHead">Browser Performance Key:</div>
		<div class="wfPerfItem wfPerfKey" style="background-color: #ffaf54;">Time for web browser to build the document in memory [DOM is ready and page becomes visible]</div>
		<div class="wfPerfItem wfPerfKey" style="background-color: #FD7FFF;">Time for the web browser to fully load the page including all images and other resources [onload() fires]</div>
	</div>
	<br clear="both" />
	<div class="wfPerfContentParent">
		<div class="wfPerfContentChild" id="wfAvgSitePerf">
			<strong>Your site's average performance for the <select id="wfAvgPerfNum" onchange="WFAD.loadAvgSitePerf();" style="font-size: 10px; height: auto; line-height: 12px;">
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="20">20</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select> most recent page views.</strong>
			<div id="wfAvgSitePerfContent" style="margin-top: 10px;">
			</div>
		</div>
	</div>
	<div class="wfPerfContentParent">
		<strong>Recent performance data for each page view, updating in real-time</strong>
		<div class="wfPerfContentChild" id="wfPerfStats">
		</div>
	</div>
</div>
<script type="text/x-jquery-template" id="wfPerfStatTmpl">
<div class="wfPerfEvent" style="margin: 10px 10px 15px 10px; padding: 0 10px 15px 0; border-bottom: 1px solid #CCC;">
	<div class="wfPerfLine">
		{{if user}}
			<span class="wfAvatar">{{html user.avatar}}</span>
			<a href="${user.editLink}" target="_blank">${user.display_name}</a>
		{{/if}}
		{{if loc}}
			{{if user}}in {{/if}}
			<img src="//www.wordfence.com/images/flags/${loc.countryCode.toLowerCase()}.png" width="16" height="11" alt="${loc.countryName}" title="${loc.countryName}" class="wfFlag" />
			<a href="http://maps.google.com/maps?q=${loc.lat},${loc.lon}&z=6" target="_blank">{{if loc.city}}${loc.city}, {{/if}}${loc.countryName}</a>
		{{else}}
			An unknown location at IP <a href="${WFAD.makeIPTrafLink(IP)}" target="_blank">${IP}</a>
		{{/if}}
		visited
		<a href="${URL}" target="_blank">${URL}</a>
	</div>
	<div class="wfPerfLine">
		<span class="wfTimeAgo">${timeAgo} ago</span>&nbsp;&nbsp; <strong>IP:</strong> <a href="${WFAD.makeIPTrafLink(IP)}" target="_blank">${IP}</a>
	</div>
	{{if browser && browser.browser != 'Default Browser'}}<div class="wfPerfLine"><strong>Browser:</strong> ${browser.browser}{{if browser.version}} version ${browser.version}{{/if}}{{if browser.platform && browser.platform != 'unknown'}} running on ${browser.platform}{{/if}}</div>{{/if}}
	<div style="color: #AAA;">${UA}</div>
	<div style="clear: left; width: 100px; height: 5px;"></div>
	<div class="wfPerfItem wfPerfPrefix">Total DNS, Server &amp; Network Time: ${parseInt(domainLookupEnd) + parseInt(connectEnd) + parseInt(responseStart) + parseInt(responseEnd)}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(domainLookupEnd / scale) + min}px; background-color: #fdff47;">DNS<br />${domainLookupEnd}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(connectEnd / scale) + min}px; background-color: #80ff80;">Con<br />${connectEnd}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(responseStart / scale) + min}px; background-color: #89a1ff;">Req<br />${responseStart}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(responseEnd / scale) + min}px; background-color: #ff7878;">Res<br />${responseEnd}ms</div>
	<div style="clear: left; width: 100px; height: 3px;"></div>
	<div class="wfPerfItem wfPerfPrefix">Total Browser time to build &amp; display: ${parseInt(domReady) + parseInt(loaded)}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(domReady / scale) + min}px; background-color: #ffaf54;">Doc<br />${domReady}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(loaded / scale) + min}px; background-color: #FD7FFF;">Page<br />${loaded}ms</div>
	<br clear="both" />
</div>
</script>
<script type="text/x-jquery-template" id="wfWelcomeContentCaching">
<div>
<h3>See your site performance from the initial domain name lookup all the way to the final page render for every visit</h3>
<strong><p>What good is speeding up your site if you can't see how much faster it is?</p></strong>
<p>
	Wordfence uses a new feature available in most major browsers that lets you see the actual performance
	that every visitor to your site experiences. You can see how fast your site responded
	from the moment your visitor clicked a link taking them to your site (or hit enter in their browser location bar)
	all the way through to how long it took their browser to render your site HTML. We include 
	data that many providers don't include, like the actual time it took for a visitor to 
	look up your website domain name. 
</p>
<p>
	The statistics you get here are extremely accurate down to the millisecond. You can use
	these statistics to conduct experiments that improve performance and improve
	your site user experience. 
</p>
</div>
</script>
<script type="text/x-jquery-template" id="wfAvgPerfTmpl">
<div>
	<div class="wfPerfItem wfPerfPrefix">Total DNS, Server &amp; Network Time: ${parseInt(domainLookupEnd) + parseInt(connectEnd) + parseInt(responseStart) + parseInt(responseEnd)}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(domainLookupEnd / scale) + min}px; background-color: #fdff47;">DNS<br />${domainLookupEnd}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(connectEnd / scale) + min}px; background-color: #80ff80;">Con<br />${connectEnd}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(responseStart / scale) + min}px; background-color: #89a1ff;">Req<br />${responseStart}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(responseEnd / scale) + min}px; background-color: #ff7878;">Res<br />${responseEnd}ms</div>
	<div style="clear: left; width: 100px; height: 3px;"></div>
	<div class="wfPerfItem wfPerfPrefix">Total Browser time to build &amp; display: ${parseInt(domReady) + parseInt(loaded)}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(domReady / scale) + min}px; background-color: #ffaf54;">Doc<br />${domReady}ms</div>
	<div class="wfPerfItem" style="width: ${parseInt(loaded / scale) + min}px; background-color: #FD7FFF;">Page<br />${loaded}ms</div>
	<br clear="both" />

</div>
</script>
