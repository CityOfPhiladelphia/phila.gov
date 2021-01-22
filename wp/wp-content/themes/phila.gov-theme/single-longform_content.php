<?php 
$pages = new WP_Query( array( 'post_type' => 'longform_content', 'post_parent' => the_ID()) );

var_dump($pages);
// foreach ($pages as $page ) {
  // var_dump($page->ID);
  // $root=count($ancestors)-1;
  // var_dump($root);
// }
$error_message_title = 'this is a test';
$error_messages = [];
$item1['link'] = 'publications';
$item1['text'] = 'wow';
$item2['link'] = 'departments';
$item2['text'] = 'this is a test';
$item3['link'] = '#';
$item3['text'] = 'what';
array_push($error_messages, $item1, $item2, $item3);
?>

<?php include(locate_template('partials/error-message.php')); ?>

<div id='longform-content-single-container'>
<?php
/**
 * The template used for displaying longform content
 *
 * @package phila-gov
*/

get_header();

?>

<div id="post-<?php the_ID(); ?>">
  <?php include(locate_template('partials/departments/v2/longform-content.php')); ?>

</div><!-- #post-## -->

<?php get_footer(); ?>
</div>