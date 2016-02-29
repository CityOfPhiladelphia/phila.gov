<?php
/**
 * The content of a single post
 * @package phila-gov
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
    </header><!-- .entry-header -->
  </div>
  <div class="row">
    <div data-swiftype-index='true' class="entry-content medium-18 columns">
      <?php
      if ( has_post_thumbnail() ) : ?>
        <ul class="clearing-thumbs" data-clearing>
          <li>
            <?php
              $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
              echo '<a href="' . $large_image_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '">';
              the_post_thumbnail( 'news-thumb' );
              echo '</a>'; ?>
          </li>
        </ul>
      <?php
      endif;
      $news_desc = rwmb_meta( 'phila_news_desc', $args = array('type' => 'textrea'));
      $post_desc = rwmb_meta( 'phila_post_desc', $args = array( 'type' => 'textrea' ) );

      if ($post->post_content != ''):
        the_content();
      else :
        if ($news_desc) :
          echo '<p class="description">' . $news_desc . '</p>';
        else :
          echo '<p class="description">' . $post_desc . '</p>';
        endif;
      endif;
      ?>
    </div><!-- .entry-content -->
    <aside id="secondary" class="small-24 medium-6 columns" role="complementary">
      <?php
        phila_gov_posted_on();

        /* A link pointing to the category in which this content lives. We are looking at department pages specifically, so a department link will not appear unless that department is associated with the category in question.  */
        $current_category = get_the_category();
        if ( !$current_category == '' ) :
          $department_page_args = array(
            'post_type' => 'department_page',
            'tax_query' => array(
              array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $current_category[0]->slug,
              ),
            ),
            'post_parent' => 0,
            'posts_per_page' => 1,
          );
          $get_department_link = new WP_Query( $department_page_args );
          if ( $get_department_link->have_posts() ) :
            while ( $get_department_link->have_posts() ) :
              $get_department_link->the_post();
              $current_cat_slug = $current_category[0]->slug;
              if ( $current_cat_slug != 'uncategorized' ) {
                // NOTE: the id and data-slug are important. Google Tag Manager
                // uses it to attach the department to our web analytics.
                echo __('<h3 class="alternate">From</h3>');
                echo '<span class="small-text"><a href="' . get_the_permalink() . '" id="content-modified-department"
                      data-slug="' . $current_cat_slug . '">' . get_the_title() . '</a></span>';
              }
            endwhile;
          endif;
        endif;
        wp_reset_postdata();

        phila_gov_entry_footer();

      ?>
    </aside>
  </div><!-- .row -->
</article><!-- #post-## -->
