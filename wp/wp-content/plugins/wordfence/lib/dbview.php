<?php if ( ! wfUtils::isAdmin() ) {
	exit();
} ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head>
	<title>Wordfence DB Table Viewer</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel='stylesheet' id='wordfence-main-style-css'
	      href='<?php echo wfUtils::getBaseURL(); ?>/css/phpinfo.css?ver=<?php echo WORDFENCE_VERSION; ?>'
	      type='text/css' media='all'/>
<body>
<h1>Wordfence Database Table Viewer</h1>
<p style="width: 400px;">This page is used for debugging and shows a list of database tables and their status on your system. Our staff may ask you to send them the
data on this page as part of a troubleshooting process.</p>
<?php
$wfdb = new wfDB();
$q = $wfdb->querySelect("show table status");
foreach($q as $val){
	foreach($val as $tkey => $tval){
		echo '<span style="color: #999; font-style: italic;">' . $tkey . ':</span> ' . $tval . ' ';
	}
	echo '<br />-----------------------------------------------------------------------------------------<br />';
}

?>

<div class="diffFooter">&copy;&nbsp;2011 to 2015 Wordfence &mdash; Visit <a
		href="http://wordfence.com/">Wordfence.com</a> for help, security updates and more.</div>
</body>
</html>
