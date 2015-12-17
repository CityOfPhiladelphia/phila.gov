<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package phila-gov
 */

get_header(); ?>

<section id="primary" class="content-area archive row">

  <?php
    if ( have_posts() ) : ?>
      <header class="columns">
        <h1>
          <?php
            if ( is_category() ) :
              $post = get_post_type( );
                if ( $post == 'post' ) :
                  _e('Posts | ');
                  single_cat_title();
                else:
                  single_cat_title();
                endif;
              elseif ( is_tag() ) :
               '<span>' . single_tag_title('Tagged in: ') . '</span>';

              elseif ( is_author() ) :
                printf( __( 'Author: %s', 'phila-gov' ), '<span class="vcard">' . get_the_author() . '</span>' );

              elseif ( is_day() ) :
                printf( __( 'Day: %s', 'phila-gov' ), '<span>' . get_the_date() . '</span>' );

              elseif ( is_month() ) :
                printf( __( 'Month: %s', 'phila-gov' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'phila-gov' ) ) . '</span>' );

              elseif ( is_year() ) :
                printf( __( 'Year: %s', 'phila-gov' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'phila-gov' ) ) . '</span>' );

              else :
                _e( 'Archives', 'phila-gov' );

              endif;
          ?>
        </h1>
      </header><!-- .page-header -->
      <main id="main" class="site-main small-24 columns" role="main">
        <?php while ( have_posts() ) : the_post(); ?>

          <?php get_template_part( 'partials/content', 'list-featured-image' ) ?>

        <?php endwhile; ?>

        <?php phila_gov_paging_nav(); ?>

        <?php else : ?>

          <?php get_template_part( 'partials/content', 'none' ); ?>

        <?php endif; ?>

      </main><!-- #main -->
</section><!-- #primary -->
<?php get_footer(); ?>
