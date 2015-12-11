Either you or someone else at IP address <b><?php echo $IP; ?></b> requested instructions to<br />
regain access to the website <a href="<?php echo wfUtils::getSiteBaseURL(); ?>"><b><?php echo $siteName; ?></b></a>.<br />
<br />
Request was generated at: <?php echo wfUtils::localHumanDate(); ?><br />
<br />
If you did not request these instructions then you can safely ignore them.<br />
These instructions <b>will be valid for 30 minutes</b>
from the time they were sent.
<ul>
<li>
<a href="<?php echo $unlockHref; ?>&func=unlockMyIP">Click here to unlock your ability to sign-in and to access to the site.</a> Do this if you simply need to regain access because you were accidentally locked out.
</li>
<li>
<a href="<?php echo $unlockHref; ?>&func=unlockAllIPs">Click here to unblock all IP addresses.</a> Do this if you still can't regain access using the link above. It causes everyone who is blocked or locked out to be able to access your site again.
</li>
<li>
<a href="<?php echo $unlockHref; ?>&func=disableRules">Click here to unlock all IP addresses and disable the Wordfence Firewall and Wordfence login security for all users.</a> Do this if you keep getting locked out or blocked and can't access your site. You can re-enable login security and the firewall once you sign-in to the site by visiting the Wordfence options menu and checking the boxes under advanced options to enable the firewall and login security.
</li>
</ul>
<br />
<br />
<br />
