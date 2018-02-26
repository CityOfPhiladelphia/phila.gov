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


<?php if ( phila_util_is_v2_template( $parent->ID ) && $user_selected_template !== 'homepage_v2') :
  get_template_part( 'partials/breadcrumbs' );
endif; ?>



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

  <?php


/**
 * Adds markup around a call to get_template_part()
 * @param  string $_template HTML, ideally returned from a call to get_template_part()
 * @return HTML            template markup to be included in page
 */
function apply_template_section( $_template = '' ){
$apply_template_markup = <<<HTML
  <section class="apply-template">$_template</section>
HTML;
  return $apply_template_markup;
}

  switch ($user_selected_template) {

    case 'homepage_v2':

      $_categories         = get_the_category();
      $_news_cat_override  = rwmb_meta('phila_get_news_cats');
      $_press_cat_override = rwmb_meta('phila_get_press_cats');
      $_featured_meta      = rwmb_meta( 'phila_v2_homepage_featured' ) ;


      $homepage_v2_data = array(
        'full_row_blog'   => rwmb_meta( 'phila_full_row_blog_selected' ),

        'full_row_news'   => array(
            'exists'=> rwmb_meta( 'phila_full_row_news_selected' ),
            'category_id' => !empty($_news_cat_override) ? implode(", ", $_news_cat_override['phila_news_category']) : $_categories[0]->cat_ID
        ),

        'full_width_press_releases'=>array(
          'exists'=>rwmb_meta( 'phila_full_row_press_releases_selected' ),
          'category_id' => !empty($_press_cat_override) ? implode(", ", $_press_cat_override['phila_press_release_category']) : $_categories[0]->cat_ID
        ),

        'staff_directory_listing'=>rwmb_meta( 'phila_staff_directory_selected' ),

        'featured'=> phila_loop_clonable_metabox($_featured_meta)

      );


      phila_get_template_part($DEPT_USER_TEMPLATE_PATH.$user_selected_template, $homepage_v2_data );

      break;

    case 'one_quarter_headings_v2':
      get_template_part( 'partials/departments/v2/content', 'one-quarter' );
      break;

    case 'things-to-do':
      get_template_part( 'partials/departments/v2/content', 'things-to-do' );
      break;

    case 'our-locations':
      get_template_part( 'partials/departments/v2/content', 'our-locations' );
      break;

    case 'contact_us_v2':
      get_template_part( 'partials/departments/v2/content', 'contact-us' );
      break;


    case 'all_services_v2':
      get_template_part( 'partials/departments/v2/content', 'all-services' );
      break;


    case 'forms_and_documents_v2':
      get_template_part( 'partials/departments/v2/content', 'forms-documents' );
      break;


    case 'resource_list_v2':
      apply_template_section(get_template_part( 'partials/resource', 'list' ));
      break;


    case 'staff_directory_v2':
      get_template_part( 'partials/departments/phila_staff_directory_listing' );
      break;

    case 'document_finder_v2':
      get_template_part( 'partials/departments/v2/document-finder' );
      break;

    case 'collection_page_v2':
      get_template_part( 'partials/departments/v2/collection-page' );
      break;

    case 'disabled':
      break;


    case 'off_site_department':
      break;


    case 'department_homepage':
      apply_template_section(get_template_part( 'partials/departments/content', 'department-homepage' ));
      break;


    case 'department_subpage':
      apply_template_section(get_template_part( 'partials/departments/content', 'department-subpage' ));
      break;


    case 'programs_initiatives':
      apply_template_section(get_template_part( 'partials/departments/content', 'programs-initiatives' ));
      break;


    case 'resource_list':
      apply_template_section(get_template_part( 'partials/resource', 'list' ));
      break;


    case 'staff_directory':
      apply_template_section(get_template_part( 'partials/departments/phila_staff_directory_listing' ));
      break;


    default:
      break;


  }




  ?>




      <?php
      // it's an old v1 department page and we still need to render the row one, etc. content

      if ($user_selected_template != 'homepage_v2') :
         get_template_part( 'partials/departments/content', 'row-one' );
         get_template_part( 'partials/departments/v2/content', 'homepage-full-width-cta');
         get_template_part( 'partials/departments/content', 'row-two' );
         get_template_part( 'partials/departments/content', 'call-to-action-multi' );
       endif;
    ?>




    <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

  </div> <!-- End .entry-content -->
</article>
