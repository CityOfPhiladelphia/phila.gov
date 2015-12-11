<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package phila-gov
 */
?>

<section class="no-results not-found">
        <header class="row">
            <div class="small-24 columns">
                <h1 class="h2"><?php _e( 'Nothing Found', 'phila-gov' ); ?></h1>
            </div>
        </header><!-- .page-header -->

    <div class="row">
      <div class="page-content small-24 columns">
        <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

            <p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'phila-gov' ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

        <?php elseif ( is_search() ) : ?>

            <p><?php _e( 'Sorry, nothing has matched your search terms.', 'phila-gov' ); ?></p>
            <?php still_migrating_content(); ?>

        <div class="panel center">
            <?php echo 'Can\'t find what you are looking for? <a href="';
                echo get_template_part( 'partials/content', 'feedback-url' );
                echo '" target="_blank"> Let us know. <span class="accessible">Opens in new window</span></a>'; ?>
        </div>
              <?php else : ?>
                <?php still_migrating_content();?>
            <?php endif; ?>
            </div><!-- .page-content -->
    </div><!-- .row-->
</section><!-- .no-results -->
