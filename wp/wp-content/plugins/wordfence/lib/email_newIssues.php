<p>This email was sent from your website "<?php echo get_bloginfo('name', 'raw'); ?>" by the Wordfence plugin.</p>

<p>Wordfence found the following new issues on "<?php echo get_bloginfo('name', 'raw'); ?>".</p>

<p>Alert generated at <?php echo wfUtils::localHumanDate(); ?></p>
	

<?php if (wfConfig::get('scansEnabled_highSense')): ?>
	<div style="margin: 12px 0;padding: 8px; background-color: #ffffe0; border: 1px solid #ffd975; border-width: 1px 1px 1px 10px;">
		<em>HIGH SENSITIVITY scanning is enabled, it may produce false positives</em>
	</div>
<?php endif ?>

<?php if($totalCriticalIssues > 0){ ?>
<p>Critical Problems:</p>

<?php foreach($issues as $i){ if($i['severity'] == 1){ ?>
<p>* <?php echo htmlspecialchars($i['shortMsg']) ?></p>
<?php if (!empty($i['tmplData']['badURL'])): ?>
<p><img src="<?php echo sprintf("http://noc1.wordfence.com/v2.14/?v=%s&s=%s&k=%s&action=image&txt=%s", rawurlencode(wfUtils::getWPVersion()), rawurlencode(home_url()), rawurlencode(wfConfig::get('apiKey')), rawurlencode(base64_encode($i['tmplData']['badURL']))) ?>" alt="" /></p>
<?php endif ?>

<?php } } } ?>

<?php if($level == 2 && $totalWarningIssues > 0){ ?>
<p>Warnings:</p>

<?php foreach($issues as $i){ if($i['severity'] == 2){  ?>
<p>* <?php echo htmlspecialchars($i['shortMsg']) ?></p>

<?php } } } ?>


<?php if(! $isPaid){ ?>
<p>NOTE: You are using the free version of Wordfence. Upgrading to the paid version of Wordfence gives you 
two factor authentication (sign-in via cellphone) and country blocking which are both effective methods to block attacks.
A Premium Wordfence license also includes remote scanning with each scan of your site which can detect 
several additional website infections. Premium members can also schedule when website scans occur and
can scan more than once per day.</p>

<p>As a Premium member you also get access to our priority support system located at http://support.wordfence.com/ and can file
priority support tickets using our ticketing system. </p>

<p>Click here to sign-up for the Premium version of Wordfence now.<br>
<a href="https://www.wordfence.com/zz2/wordfence-signup/">https://www.wordfence.com/zz2/wordfence-signup/</a></p>

<?php } ?>



