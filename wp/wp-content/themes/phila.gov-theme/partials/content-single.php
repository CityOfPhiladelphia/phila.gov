<?php
/**
 * The content of a single post
 * @package phila-gov
 */
?>

<article id="post-<?php the_ID(); ?>">
	<div class="row">
		<header class="entry-header small-24 columns">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
	</div>
	<div class="row">
        <div data-swiftype-index='true' class="entry-content small-24 medium-18 columns">
          <?php the_content(); ?>

						<?php get_template_part( 'partials/content', 'department-link' ) ?>
        </div><!-- .entry-content -->
    <?php
        get_sidebar('related-topics');
    ?>
	</div><!-- .row -->
	<?php get_template_part( 'partials/content', 'modified' ) ?>
</article><!-- #post-## -->
