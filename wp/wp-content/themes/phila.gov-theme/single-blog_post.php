<?php
/**
 * The template for displaying all single posts.
 *
 * @package phila-gov
 */

include(locate_template('partials/errors/post-errors.php'));

include(locate_template('partials/errors/error-message.php'));

get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">
<?php

    include(locate_template( 'templates/blog_post.php') );

?>
  </main>
</div>
<?php get_footer(); ?>