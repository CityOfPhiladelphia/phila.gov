<?php
/*
 * Partial for rendering translated content.
 *
 */
?>
<?php
$translated_content = rwmb_meta('phila_v2_translated_content');

foreach ($translated_content as $content) {
  echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title'];
  echo $content['phila_custom_wysiwyg']['phila_wysiwyg_content'];
}
?>
