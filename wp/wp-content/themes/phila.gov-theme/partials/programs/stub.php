<!-- Stub  -->
<?php if ( null !== rwmb_meta( 'phila_stub_source' ) ) : ?>
<?php $stub_source = rwmb_meta( 'phila_stub_source' );?>
<?php $post_id = intval( $stub_source );?>
<?php $is_stub = true; ?>
  <div class="mtl mbm">
    <?php  get_template_part( 'partials/breadcrumbs' ); ?>
  </div>
  <?php $stub_args = array(
    'p' => $post_id,
    'post_type' => 'programs'
  ); ?>
    <div class="row">
      <header class="columns">
        <h1 class="contrast"><?php echo get_the_title(); ?></h1>
      </header>
    </div> 
  <?php $stub_post = new WP_Query($stub_args); ?>
  <?php if ( $stub_post->have_posts() ): ?>
    <?php while ( $stub_post->have_posts() ) : ?>
      <?php $stub_post->the_post(); ?>
      <?php $stub_id = $post_id; ?>
      <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
      <?php if( !empty( get_the_content() ) ) : ?>
        <?php include( locate_template( 'partials/content-basic.php' ) ); ?>
      <?php endif; ?>
      <?php include(locate_template( 'partials/content-custom-markup-after-wysiwyg.php' ) ); ?>
      <?php 

        $user_selected_template = rwmb_meta( 'phila_template_select', $args = array(), $stub_id );

        switch ($user_selected_template) {
          case 'collection_page_v2':
            include(locate_template( 'partials/departments/v2/collection-page.php' )); 
            break;
          case 'document_finder_v2':
            include(locate_template( 'partials/departments/v2/document-finder.php' ));
            break;
          case 'phila_one_quarter':
            include(locate_template( 'partials/departments/v2/content-one-quarter.php' ) );
            break; 
          case 'prog_association':
            include(locate_template( 'partials/content-phila-row.php' ) );
            break;
          case 'resource_list_v2':
            include(locate_template( 'partials/resource-list.php'));
            break;
          case 'timeline':
            get_template_part( 'partials/departments/v2/homepage_timeline' );
            break;
        }
        include(locate_template( 'partials/content-additional.php' ) ); 
        ?>
      <?php endwhile; ?>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
  <?php endif; ?>
  <!-- END Department Stub -->