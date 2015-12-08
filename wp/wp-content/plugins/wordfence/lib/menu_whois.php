<div class="wordfenceModeElem" id="wordfenceMode_whois"></div>
<div class="wrap" id="paidWrap">
	<?php require('menuHeader.php'); ?>
	<?php $pageTitle = "WHOIS Lookup"; $helpLink="http://docs.wordfence.com/en/Whois_Lookup"; $helpLabel="Learn more about Whois Lookups"; include('pageTitle.php'); ?>
	<div class="wordfenceWrap" style="margin: 20px 20px 20px 30px;">
<?php
if(! function_exists('fsockopen')){
?>
		<p style="color: #F00; width: 600px;">
			Sorry, but your web hosting provider has disabled the 'fsockopen' function on your WordPress server. That means you can't 
			use WHOIS. Please log a support call with them asking them to enable this function. Explain that you need it to be able to 
			perform Whois lookups on IP addresses which will allow you to determine who owns the IP's that are attacking your website.
			<br /><br />
			If you are hosting your own site, edit your php.ini config file and make sure 'fsockopen' does not appear
			next to disable_functions in php.ini. You may have to restart your web server for the changes to take effect.
		</p>
<?php
} else {
?>
		</p>
		<p>
			<input type="text" name="whois" id="wfwhois" value="" size="40" maxlength="255" onkeydown="if(event.keyCode == 13){ WFAD.whois(jQuery('#wfwhois').val()); }" />&nbsp;<input type="button" name="whoisbutton" id="whoisbutton" class="button-primary" value="Look up IP or Domain" onclick="WFAD.whois(jQuery('#wfwhois').val());" />

		</p>
		<?php if( isset( $_GET['wfnetworkblock'] ) && $_GET['wfnetworkblock']){ ?>
		<h2>How to block a network</h2>
		<p style="width: 600px;">
			You've chosen to block the network that <span style="color: #F00;"><?php echo wp_kses($_GET['whoisval'], array()); ?></span> is part of.
			We've marked the networks we found that this IP address belongs to in red below.
			Make sure you read all the WHOIS information so that you see all networks this IP belongs to. We recommend blocking the network with the lowest number of addresses.
			You may find this is listed at the end as part of the 'rWHOIS' query which contacts
			the local WHOIS server that is run by the network administrator.
		</p>
		<?php } ?>
		<div id="wfrawhtml">
		</div>
	</div>
</div>
<script type="text/x-jquery-template" id="wfBlockedRangesTmpl">
<div>
<div style="border-bottom: 1px solid #CCC; padding-bottom: 10px; margin-bottom: 10px;">
<table border="0" style="width: 100%">
{{each(idx, elem) results}}
<tr><td></td></tr>
{{/each}}
</table>
</div>
</div>
</script>
<script type="text/javascript">
var whoisval = "<?php if( isset( $_GET['whoisval'] ) ) { echo wp_kses($_GET['whoisval'], array()); } ?>";
if(whoisval){
	jQuery(function(){
		jQuery('#wfwhois').val(whoisval);
		WFAD.whois(whoisval);
		});
}
</script>
<script type="text/x-jquery-template" id="wfWelcomeContentWhois">
<div>
<h3>WHOIS: Look up domains and IP owners</h3>
<strong><p>Find out who's attacking you and report them!</p></strong>
<p>
	Wordfence includes a new feature called "WHOIS". This feature works hand-in-glove with our
	new "Advanced Blocking". Using WHOIS you can look up the owner of an IP address. 
	The owner information includes which networks the IP is part of. This information empowers you to do 
	several things. 
</p>
<p>
	Firstly you can report any malicious IP address to the network that owns it using the abuse email addresses provided. Secondly, you can simply
	click on the network ranges in the whois information and block that entire network.
</p>
<p>
	Wordfence WHOIS queries in real-time the WHOIS servers belonging to the Regional Internet Registries ARIN, RIPE, APNIC, AFRINIC and LACNIC.
	We then do a further query to any local WHOIS servers that administer the networks we find and this data is returned as a rWHOIS record
	at the bottom of the WHOIS result. 
</p>
</div>
</script>
<?php
} //closing paren for function_exists('fsockopen')
?>

