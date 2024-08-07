<?php
/*
*
* Template part
* for displaying on-site department content
*
*/

$DEPT_USER_TEMPLATE_PATH = 'partials/departments/v2/user_templates/';
$parent                    = phila_util_get_furthest_ancestor($post);
$user_selected_template    = phila_get_selected_template();
?>

<?php if ( phila_util_is_new_template( $parent->ID ) && ($user_selected_template !== 'homepage_v2' || $user_selected_template !== 'homepage_v3') && $is_stub != 'false'):?>
  <div class="mtl mbm">
    <?php get_template_part( 'partials/breadcrumbs' ); ?>
  </div>
<?php endif; ?>



<article>

  <?php if ($user_selected_template == 'programs_initiatives') : ?>
    <?php get_template_part( 'partials/departments/content', 'programs-initiatives-header' ); ?>
  <?php elseif ($user_selected_template != 'prog_association'): ?>
    <header class="row">
      <div class="columns">
        <?php the_title( '<h2 class="sub-page-title contrast">', '</h2>' ); ?>
      </div>
    </header>
  <?php endif; ?>


  <div data-swiftype-index='true' class="entry-content">

    <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>

    <?php if ($user_selected_template != 'homepage_v2' && $user_selected_template != 'homepage_v3') : ?>

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

  <?php
  
  switch ($user_selected_template) {

    case 'all_services_v2':
      get_template_part( 'partials/departments/v2/content', 'all-services' );
      break;

    case 'all_programs_v2':
      get_template_part( 'partials/departments/v2/content', 'all-programs' );
      break;

    case 'child_index':
      get_template_part( 'partials/departments/v2/child', 'index' );
      break;

    case 'collection_page_v2':
      get_template_part( 'partials/departments/v2/collection-page' );
      break;

    case 'contact_us_v2':
      get_template_part( 'partials/departments/v2/content', 'contact-us' );
      break;

    case 'disabled':
      break;

    case 'document_finder_v2':
      get_template_part( 'partials/departments/v2/document-finder' );
      include(locate_template( 'partials/content-phila-row.php' ) );
      break;

    case 'homepage_v2':

      $_categories         = get_the_category();
      $_news_cat_override  = rwmb_meta('phila_get_news_cats');
      $_press_cat_override = rwmb_meta('phila_get_press_cats');
      $_tags_override = rwmb_meta('phila_get_post_cats');
      $homepage_v2_data = array(
        'full_row_blog'   => array(
          'exists'=> rwmb_meta('phila_full_row_blog_selected' ),
          'tag' => !empty($_tags_override['tag']) ? $_tags_override['tag'] : ''
        ),

        'full_row_announcements'   => array(
          'exists'=> rwmb_meta('phila_full_row_announcements_selected' ),
          'tag' => !empty($_tags_override['tag']) ? $_tags_override['tag'] : ''
        ),

        'full_width_press_releases'=>array(
          'exists'=>rwmb_meta( 'phila_full_row_press_releases_selected' ),
          'category_id' => !empty($_press_cat_override) ? $_categories[0]->cat_ID : $_categories[0]->cat_ID
        ),

        'staff_directory_listing'=>rwmb_meta( 'phila_staff_directory_selected' ),

      );

      phila_get_template_part($DEPT_USER_TEMPLATE_PATH.$user_selected_template, $homepage_v2_data );

      break;

    case 'homepage_v3':
      include(locate_template('partials/departments/v2/our-services.php'));
      include( locate_template( 'partials/content-phila-row.php' ) ); 
      break;

    case 'off_site_department':
      break;

    case 'one_quarter_headings_v2':
      get_template_part( 'partials/departments/v2/content', 'one-quarter' );
      break;

    case 'our-locations':
      get_template_part( 'partials/departments/v2/content', 'our-locations' );
      break;
  
    case 'prog_association':
      include( locate_template( 'partials/content-phila-row.php' ) ) ;
    break;

    case 'repeating_rows':
      include( locate_template( 'partials/content-phila-row.php' ) ) ;
    break;

    case 'resource_list_v2':
      include( locate_template( 'partials/resource-list.php' ) );
      break;

    case 'staff_directory_v2':
      get_template_part( 'partials/departments/phila_staff_directory_listing' );
      break;

    case 'things-to-do':
      get_template_part( 'partials/departments/v2/content', 'things-to-do' );
      break;

    case 'timeline':
      get_template_part( 'partials/timeline_stub' );
    break;

    default:
      break;

  }

  ?>

    <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

  </div> <!-- End .entry-content -->
</article>
