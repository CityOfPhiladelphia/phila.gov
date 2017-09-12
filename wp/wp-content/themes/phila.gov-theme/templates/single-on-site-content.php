<?php
/*
*
* Template part
* for displaying on-site department content
*
*/
?>
<?php $parent = phila_util_get_furthest_ancestor($post); ?>
<?php $user_selected_template = phila_get_selected_template(); ?>

<?php $staff_directory_listing = rwmb_meta( 'phila_staff_directory_selected' ); ?>

<?php $full_width_press_releases = rwmb_meta( 'phila_full_row_press_releases_selected' ); ?>

<?php $full_row_blog = rwmb_meta( 'phila_full_row_blog_selected' ); ?>
<?php $full_row_news = rwmb_meta( 'phila_full_row_news_selected' ); ?>

<?php $featured_meta = rwmb_meta( 'phila_v2_homepage_featured' ) ; ?>
<?php $featured = phila_loop_clonable_metabox($featured_meta); ?>

<?php if ( phila_util_is_v2_template( $parent->ID ) && phila_get_selected_template() !== 'homepage_v2') : ?>
  <div class="row mtl mbm">
    <div class="columns">
      <?php echo phila_breadcrumbs(); ?>
    </div>
  </div>
<?php endif; ?>
<article>
  <?php if ($user_selected_template == 'programs_initiatives') : ?>
    <?php get_template_part( 'partials/departments/content', 'programs-initiatives-header' ); ?>
  <?php else : ?>
    <header class="row">
      <div class="columns">
        <?php the_title( '<h2 class="sub-page-title contrast">', '</h2>' ); ?>
      </div>
    </header>
  <?php endif; ?>

  <div data-swiftype-index='true' class="entry-content">
    <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>

    <?php if ($user_selected_template != 'homepage_v2') : ?>

      <?php get_template_part( 'partials/departments/content', 'hero-header' ); ?>

    <?php endif; ?>

    <?php if( get_the_content() != '' ) : ?>
      <!-- WYSIWYG content -->
      <section class="wysiwyg-content">
        <div class="row">
          <div class="small-24 columns">
            <?php echo the_content();?>
          </div>
        </div>
      </section>
      <!-- End WYSIWYG content -->
    <?php endif; ?>

    <?php //Begin v2 homepage specific templates ?>
    <?php if ($user_selected_template == 'homepage_v2') : ?>

      <?php $args = array( 'post_type' => 'service_updates', 'category__in' => phila_util_cat_ids()); ?>
      <?php $service_updates_loop = new WP_Query( $args ); ?>
      <?php include( locate_template( 'partials/content-service-updates.php' ) ); ?>
      <?php wp_reset_query();?>

      <?php get_template_part( 'partials/departments/v2/content', 'curated-service-list' ); ?>

      <?php get_template_part( 'partials/departments/content', 'row-one' ); ?>

      <?php if ( $full_row_blog ): ?>
        <?php
          $blog_cat_override = rwmb_meta('phila_get_post_cats');

          $categories = get_the_category();
          $category_id = $categories[0]->cat_ID;

          if ( !empty( $blog_cat_override ) ) :
            $category_id = implode(", ", $blog_cat_override['phila_post_category']);
          endif;
          ?>
        <section class="row">
          <?php echo do_shortcode('[recent-posts posts="3" category=" ' . $category_id .' "]'); ?>
        </section>

        <?php
        if ( empty( $blog_cat_override ) ) :
          $see_all_URL = '/posts/' . $categories[0]->slug . '/';
        $see_all_content_type = 'posts';
        include( locate_template( 'partials/content-see-all.php' ) ); ?>
        <?php endif; ?>

      <?php endif; ?>

      <?php get_template_part( 'partials/departments/v2/content', 'homepage-full-width-cta'); ?>

      <?php if ( $full_row_news ): ?>
        <?php
          $news_cat_override = rwmb_meta('phila_get_news_cats');

          $categories = get_the_category();
          $category_id = $categories[0]->cat_ID;

          if ( !empty( $news_cat_override ) ) :
            $category_id = implode(", ", $news_cat_override['phila_news_category']);
          endif;
          ?>
        <section class="row">
          <?php echo do_shortcode('[recent-news posts="3" category=" ' . $category_id .' "]'); ?>
        </section>
      <?php endif; ?>

      <?php get_template_part( 'partials/departments/content', 'row-two' ); ?>

      <?php if ( $full_width_press_releases ): ?>
        <?php
          $press_cat_override = rwmb_meta('phila_get_press_cats');

          $categories = get_the_category();
          $category_id = $categories[0]->cat_ID;

          if ( !empty( $press_cat_override ) ) :
            $category_id = implode(", ", $press_cat_override['phila_press_release_category']);
          endif;
          ?>
        <div class="row">
          <?php echo do_shortcode('[press-releases posts=3 category="' . $category_id . '"]');?>
        </div>
      <?php endif; ?>

      <?php if ( $staff_directory_listing ): ?>
        <?php get_template_part( 'partials/departments/content', 'staff-directory' ); ?>
      <?php endif; ?>

      <?php get_template_part( 'partials/departments/content', 'call-to-action-multi' ); ?>

      <?php
        if ( !empty( $featured ) ):
          include(locate_template('partials/departments/v2/content-featured.php'));
        endif;
      ?>

    <?php endif;?>

    <?php //Begin v2 non-homepage templates ?>
    <?php if ($user_selected_template == 'all_services_v2') : ?>
      <?php get_template_part( 'partials/departments/v2/content', 'all-services' ); ?>
    <?php endif;?>

    <?php if ($user_selected_template == 'one_quarter_headings_v2') : ?>
      <?php get_template_part( 'partials/departments/v2/content', 'one-quarter' ); ?>
    <?php endif;?>

    <?php if ($user_selected_template == 'forms_and_documents_v2') : ?>
      <?php get_template_part( 'partials/departments/v2/content', 'forms-documents' ); ?>
    <?php endif;?>

    <?php if ($user_selected_template == 'contact_us_v2') : ?>
      <?php get_template_part( 'partials/departments/v2/content', 'contact-us' ); ?>
    <?php endif;?>

    <?php if ($user_selected_template == 'resource_list_v2') : ?>
      <section class="apply-template">
        <?php get_template_part( 'partials/resource', 'list' ); ?>
      </section>
    <?php endif;?>

    <?php if ($user_selected_template == 'staff_directory_v2') : ?>
      <?php get_template_part( 'partials/departments/content', 'staff-directory' ); ?>
    <?php endif;?>

    <?php if ($user_selected_template == 'department_homepage') : ?>
      <!-- Begin Department Homepage Display -->
      <section class="apply-template">
        <?php get_template_part( 'partials/departments/content', 'department-homepage' ); ?>
      </section>
      <!-- End Department Homepage Display -->

    <?php elseif ($user_selected_template == 'department_subpage') : ?>
      <!-- Begin Department Subpage Template Display -->
      <section class="apply-template">
        <?php get_template_part( 'partials/departments/content', 'department-subpage' ); ?>
      </section>
      <!-- End Department Subpage Template Display -->

    <?php elseif ($user_selected_template == 'programs_initiatives') : ?>
      <!-- Begin Department Program & Initiatives Display -->
      <section class="apply-template">
        <?php get_template_part( 'partials/departments/content', 'programs-initiatives' ); ?>
      </section>
      <!-- End Department Program & Initiatives Display -->

    <?php elseif ($user_selected_template == 'resource_list') : ?>
      <!-- Begin Resource List Display -->
      <section class="apply-template">
        <?php get_template_part( 'partials/resource', 'list' ); ?>
      </section>
      <!-- End Resource List Display -->

    <?php elseif ($user_selected_template == 'staff_directory') : ?>
      <!-- Begin Staff Directory Display -->
      <section class="apply-template">
        <?php get_template_part( 'partials/departments/content', 'staff-directory' ); ?>
      </section>
      <!-- End Staff Directory Display -->
      <?php //else it's an old v1 department page and we still need to render the row one, etc. content ?>

      <?php else :?>
        <?php if ($user_selected_template != 'homepage_v2') : ?>
        <?php get_template_part( 'partials/departments/content', 'row-one' ); ?>
        <?php get_template_part( 'partials/departments/v2/content', 'homepage-full-width-cta');?>
        <?php get_template_part( 'partials/departments/content', 'row-two' ); ?>

        <?php get_template_part( 'partials/departments/content', 'call-to-action-multi' ); ?>
      <?php endif; ?>


    <?php endif; ?>

    <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

  </div> <!-- End .entry-content -->
</article>
