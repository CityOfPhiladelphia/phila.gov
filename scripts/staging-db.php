#!/usr/bin/php

<?php

define('SHORTINIT', true);
require('wp/wp-load.php');
global $wpdb;

$sql = "select option_value from wp_options where option_name='tantan_wordpress_s3'";
$rows = $wpdb->get_results($sql);

foreach ($rows as $row) {
  $id = $row->option_id;
  $val = unserialize($row->option_value);
  var_dump($val['cloudfront']);
  //$key = $val['key'];
  //if (strpos($key, 'media') !== 0) {
  //  $val['key'] = 'media/' . $key;
  //}
  //$sql = $wpdb->prepare('update wp_postmeta set meta_value = %s where meta_id = %d', serialize($val), $id);
  //$wpdb->query($sql);
}
