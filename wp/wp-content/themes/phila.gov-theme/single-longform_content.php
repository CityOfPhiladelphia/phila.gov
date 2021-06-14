<?php include(locate_template('partials/errors/longform-errors.php')); ?>

<?php include(locate_template('partials/errors/error-message.php')); ?>
<?php
/**
 * The template used for displaying longform content
 *
 * @package phila-gov
*/

get_header();

?>
<div id='longform-content-single-container'>

<div id="post-<?php the_ID(); ?>">
  <?php include(locate_template('partials/departments/v2/longform-content.php')); ?>

</div><!-- #post-## -->

<?php get_footer(); ?>
</div>