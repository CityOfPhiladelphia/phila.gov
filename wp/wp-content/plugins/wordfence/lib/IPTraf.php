<?php if(! wfUtils::isAdmin()){ exit(); } ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel='stylesheet' id='wordfence-main-style-css'  href='<?php echo wfUtils::getBaseURL(); ?>/css/iptraf.css?ver=<?php echo WORDFENCE_VERSION; ?>' type='text/css' media='all' />
<body>
<h1>Wordfence: All recent hits for IP address <?php echo wp_kses($IP, array()); if($reverseLookup){ echo '[' . wp_kses($reverseLookup, array()) . ']'; } ?></h1>
<table border="0" cellpadding="2" cellspacing="0" style="width: 900px;">
<?php foreach($results as $key => $v){ ?>
<tr><th>Time:</th><td><?php echo $v['timeAgo'] ?> ago -- <?php echo date(DATE_RFC822, $v['ctime']); ?> -- <?php echo $v['ctime']; ?> in Unixtime</td></tr>
<?php if($v['timeSinceLastHit']){ echo '<th>Secs since last hit:</th><td>' . $v['timeSinceLastHit'] . '</td></tr>'; } ?>
<?php if(wfUtils::hasXSS($v['URL'])){ ?>
<tr><th>URL:</th><td><span style="color: #F00;">Possible XSS code filtered out for your security</span></td></tr>
<?php } else { ?>
<tr><th>URL:</th><td><a href="<?php echo wp_kses($v['URL'], array()); ?>" target="_blank"><?php echo $v['URL']; ?></a></td></tr>
<?php } ?>
<tr><th>Type:</th><td><?php if($v['type'] == 'hit'){ echo 'Normal request'; } else if($v['type'] == '404'){ echo '<span style="color: #F00;">Page not found</span>'; } ?></td></tr>
<?php if($v['referer']){ ?><tr><th>Referrer:</th><td><a href="<?php echo $v['referer']; ?>" target="_blank"><?php echo $v['referer']; ?></a></td></tr><?php } ?>
<tr><th>Full Browser ID:</th><td><?php echo wp_kses($v['UA'], array()); ?></td></tr>
<?php if($v['user']){ ?>
<tr><th>User:</th><td><a href="<?php echo $v['user']['editLink']; ?>" target="_blank"><?php echo $v['user']['avatar'] . ' ' . $v['user']['display_name']; ?></a></td></tr>
<?php } ?>
<?php if($v['loc']){ ?>
<tr><th>Location:</th><td><img src="//www.wordfence.com/images/flags/<?php echo strtolower($v['loc']['countryCode']); ?>.png" width="16" height="11" alt="<?php echo $v['loc']['countryName']; ?>" title="<?php echo $v['loc']['countryName']; ?>" class="wfFlag" />
	<?php if($v['loc']['city']){ echo $v['loc']['city'] . ', '; } ?>
	<?php echo $v['loc']['countryName']; ?>
	</td></tr>
<?php } ?>
<tr><td colspan="2"><hr></td></tr>
<?php } ?>

</table>

<div class="footer">&copy;&nbsp;2011 to 2015 Wordfence &mdash; Visit <a href="http://wordfence.com/">Wordfence.com</a> for help, security updates and more.</div>
</body>
</html>
