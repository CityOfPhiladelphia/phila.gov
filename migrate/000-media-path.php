<?php

define('SHORTINIT', true);
require('wp/wp-load.php');
global $wpdb;

$sql = "select meta_id, meta_value from wp_postmeta where meta_key='amazons3_info'";
$metas = $wpdb->get_results($sql);

foreach ($metas as $meta) {
  $id = $meta->meta_id;
  $val = unserialize($meta->meta_value);
  $key = $val['key'];
  if (strpos($key, 'media') !== 0) {
    $val['key'] = 'media/' . $key;
  }
  $sql = $wpdb->prepare('update wp_postmeta set meta_value = %s where meta_id = %d', serialize($val), $id);
  $wpdb->query($sql);
}
