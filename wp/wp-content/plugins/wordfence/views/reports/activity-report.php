<?php
/**
 * @var wfActivityReportView $this
 */
?>
<a class="wf-logo" href="//www.wordfence.com/zz8/"><img src="//www.wordfence.com/wp-content/themes/wordfence/img/logo.png" alt=""/></a>

<h2>Top <?php echo (int) $limit; ?> IP's Blocked</h2>

<?php wfHelperString::cycle(); ?>

<table class="wf-table">
	<thead>
		<tr>
			<th>IP</th>
			<th>Country</th>
			<th>Block Count</th>
		</tr>
	</thead>
	<tbody>
		<?php if ($top_ips_blocked): ?>
			<?php foreach ($top_ips_blocked as $row): ?>
				<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
					<td><code><?php echo wfUtils::inet_ntop($row->IP) ?></code></td>
					<td>
						<?php if ($row->countryCode): ?>
							<img src="//www.wordfence.com/images/flags/<?php echo esc_attr(strtolower($row->countryCode)) ?>.png" class="wfFlag" height="11" width="16" alt="<?php echo esc_attr($row->countryName) ?>" title="<?php echo esc_attr($row->countryName) ?>">
							&nbsp;
							<?php echo esc_html($row->countryCode) ?>
						<?php else: ?>
							(Unknown)
						<?php endif ?>
					</td>
					<td><?php echo (int) $row->blockCount ?></td>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<tr>
				<td colspan="3">
					We're collecting data, please check back soon.
				</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>

<p>
	<a class="button button-primary" href="<?php echo network_admin_url('admin.php?page=WordfenceBlockedIPs') ?>">Update Blocked IPs</a>
</p>

<?php wfHelperString::cycle(); ?>

<h2>Top <?php echo (int) $limit; ?> Countries Blocked</h2>

<table class="wf-table">
	<thead>
		<tr>
			<th>Country</th>
			<th>Total IPs Blocked</th>
			<th>Block Count</th>
		</tr>
	</thead>
	<tbody>
		<?php if ($top_countries_blocked): ?>
			<?php foreach ($top_countries_blocked as $row): ?>
				<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
					<td>
						<?php if ($row->countryCode): ?>
							<img src="//www.wordfence.com/images/flags/<?php echo strtolower($row->countryCode) ?>.png" class="wfFlag" height="11" width="16" alt="<?php echo esc_attr($row->countryName) ?>" title="<?php echo esc_attr($row->countryName) ?>">
							&nbsp;
							<?php echo esc_html($row->countryCode) ?>
						<?php else: ?>
							(Unknown)
						<?php endif ?>
					</td>
					<td><?php echo esc_html($row->totalIPs) ?></td>
					<td><?php echo (int) $row->totalBlockCount ?></td>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<tr>
				<td colspan="3">
					We're collecting data, please check back soon.
				</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>

<p>
	<a class="button button-primary" href="<?php echo network_admin_url('admin.php?page=WordfenceCountryBlocking') ?>">Update Blocked Countries</a>
</p>

<?php wfHelperString::cycle(); ?>

<h2>Top <?php echo (int) $limit; ?> Failed Logins</h2>

<table class="wf-table">
	<thead>
		<tr>
			<th>Username</th>
			<th>Login Attempts</th>
			<th>Existing User</th>
		</tr>
	</thead>
	<tbody>
		<?php if ($top_failed_logins): ?>
			<?php foreach ($top_failed_logins as $row): ?>
				<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
					<td><?php echo esc_html($row->username) ?></td>
					<td><?php echo esc_html($row->fail_count) ?></td>
					<td class="<?php echo sanitize_html_class($row->is_valid_user ? 'loginFailValidUsername' : 'loginFailInvalidUsername') ?>"><?php echo $row->is_valid_user ? 'Yes' : 'No' ?></td>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<tr>
				<td colspan="3">
					We're collecting data, please check back soon.
				</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>

<p>
	<a class="button button-primary" href="<?php echo network_admin_url('admin.php?page=WordfenceSecOpt#wfMarkerLoginSecurity') ?>">Update Login Security Options</a>
</p>

<?php wfHelperString::cycle(); ?>

<?php /*?>
<h2>Recently Modified Files</h2>

<table class="activity-table recently-modified-files">
	<thead>
		<tr>
			<th>Modified</th>
			<th>File</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($recently_modified_files as $file_row):
			list($file, $mod_time) = $file_row;
			?>
			<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
				<td style="white-space: nowrap;"><?php echo $this->modTime($mod_time) ?></td>
				<td class="display-file-table-cell">
					<pre class="display-file"><?php echo esc_html($this->displayFile($file)) ?></pre>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php */ ?>


<?php wfHelperString::cycle(); ?>

<h2>Updates Needed</h2>

<?php if ($updates_needed['core']): ?>
	<h4>Core</h4>
	<ul>
		<li>A new version of WordPress (v<?php echo esc_html($updates_needed['core']) ?>) is available.</li>
	</ul>
<?php endif ?>
<?php if ($updates_needed['plugins']): ?>
	<h4>Plugins</h4>
	<ul>
		<?php foreach ($updates_needed['plugins'] as $plugin): ?>
			<li>
				A new version of the plugin "<?php echo esc_html("{$plugin['Name']} (v{$plugin['newVersion']})") ?>" is available.
			</li>
		<?php endforeach ?>
	</ul>
<?php endif ?>
<?php if ($updates_needed['themes']): ?>
	<h4>Themes</h4>
	<ul>
		<?php foreach ($updates_needed['themes'] as $theme): ?>
			<li>
				A new version of the theme "<?php echo esc_html("{$theme['name']} (v{$theme['newVersion']})") ?>" is available.
			</li>
		<?php endforeach ?>
	</ul>
<?php endif ?>

<?php if ($updates_needed['core'] || $updates_needed['plugins'] || $updates_needed['themes']): ?>
	<p><a class="button button-primary" href="<?php echo esc_attr(network_admin_url('update-core.php')) ?>">Update Now</a></p>
<?php else: ?>
	<p>No updates are available at this time.</p>
<?php endif ?>
<?php if ((defined('WP_DEBUG') && WP_DEBUG) || wfConfig::get('debugOn')): ?>
	<p>Generated in <?php printf('%.4f seconds', $microseconds) ?></p>
<?php endif ?>
