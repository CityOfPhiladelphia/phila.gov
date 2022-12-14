<?php include(locate_template('partials/errors/longform-errors.php')); ?>

<?php include(locate_template('partials/errors/error-message.php')); ?>

<head>
  <link rel="shortcut icon" type="image/x-icon" href="//www.phila.gov/assets/images/favicon.ico">
</head>

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