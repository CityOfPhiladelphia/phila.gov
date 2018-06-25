<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up until <div id="content">
 *
 * @package phila-gov
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="description" content="<?php echo ( is_archive() || is_search() || is_home() ) ? get_bloginfo('description') : phila_get_item_meta_desc(); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#2176d2">

  <!-- Swiftype -->
  <meta class="swiftype" name="title" data-type="string" content="<?php echo str_replace(' | ' . get_bloginfo('name'), '', phila_filter_title( $title ) ); ?>">
  <meta class="swiftype" name="tags" data-type="string" content="wordpress" />
  <meta class="swiftype" name="site-priority" data-type="integer" content="10" />

  <?php if (is_single()) : ?>
    <meta class="swiftype" name="published_at" data-type="date" content="<?php echo get_the_time('c', $post->ID); ?>" />
  <?php endif; ?>

  <link rel="shortcut icon" type="image/x-icon" href="//cityofphiladelphia.github.io/patterns/images/favicon.ico">

  <?php wp_head(); ?>

  <!--[if lte IE 9]>
  <p class="browsehappy alert">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience. If you can't switch browsers, turn off  compatibility mode.</p>
  <![endif]-->

  <?php if( ( !is_home() ) && ( is_single() ) ) : ?>
      <?php if (function_exists('rwmb_meta')): ?>
        <?php $append_to_head = rwmb_meta( 'phila_append_to_head', $args = array('type' => 'textarea'), $post->ID); ?>
        <?php if ( !$append_to_head == '' ): ?>
          <!-- Begin Custom Markup Metabox: Append to Head -->
          <?php echo $append_to_head; ?>
          <!-- End Custom Markup Metabox: Append to Head -->
        <?php endif;?>
      <?php endif; ?>
  <?php endif; ?>

</head>

<body <?php body_class(); ?> lang="en">

<?php if ( !is_archive() && !is_tax() && !is_home() ) : ?>
  <!-- Google Tag Manager DataLayer -->
  <?php $category = get_the_category();
    $departments = phila_get_current_department_name( $category, $byline = false, $break_tags = false, $name_list = true );
  ?>
  <script>
    dataLayer = [{
      "contentModifiedDepartment": "<?php echo $departments ?>",
      "lastUpdated": "<?php the_modified_time('Y-m-d H:i:s'); ?>",
      "templateType": "<?php echo phila_get_selected_template() ?>"
    }];
  </script>
<?php endif; ?>

<!-- Google Tag Manager [phila.gov] -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MC6CR2"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MC6CR2');</script>
<!-- End Google Tag Manager -->

<?php if (phila_util_return_is_post(get_post_type()) ) : ?>
  <?php get_template_part( 'partials/social', 'media' ); ?>
<?php endif ?>
  <header class="global-nav no-js pbm pbn-mu mbn-mu">
    <h1 class="accessible">City of Philadelphia</h1>
    <!-- Beta opt-out -->
    <?php get_template_part( 'partials/content', 'wip' ); ?>
    <!-- Utility Navigation -->
    <div class="row columns bg-ben-franklin-blue expanded utility-nav" data-swiftype-index="false">
      <div class="row">
        <div class="medium-24 columns float-right">
          <ul class="medium-horizontal menu show-for-medium float-right">
            <li><a href="/departments/mayor/">Office of the Mayor</a></li>
            <li><a href="/departments/">City government directory</a></li>
            <li>
              <div id="google_translate_element" class="no-js"><span class="show-for-sr">Google Translate</span></div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--sticky/desktop nav -->
    <div id="global-sticky-nav" class="row">
      <div class="small-24 columns">
        <div class="row primary-menu" data-sticky-container data-swiftype-index="false">
          <div class="columns phila-sticky phn" data-margin-top="0" data-sticky data-sticky-on="medium">
            <div class="row sticky-header-width">
              <div class="small-16 medium-6 columns valign small-push-4 medium-push-0">
                <div class="valign-cell">
                  <a href="<?php echo get_home_url(); ?>" class="logo" tabindex="1" aria-label="City of Philadelphia">
                    <img src="<?php echo get_stylesheet_directory_uri() . "/img/city-of-philadelphia-logo.svg" ?>" data-fallback="//cityofphiladelphia.github.io/patterns/images/city-of-philadelphia.png" alt="City of Philadelphia">
                  </a>
                </div>
              </div>
              <a href="#page" aria-hidden="false" class="accessible">Skip to main content</a>
              <div class="medium-17 columns show-for-medium desktop-nav">
                <div class="top-bar-right valign-mu">
                  <nav data-swiftype-index="false" class="valign-mu">
                    <ul class="horizontal menu pan valign-mu">
                      <li class="services-menu-link" data-toggle="services-mega-menu">
                        <a href="" class="no-link valign-cell" data-link="/services/" onclick="noLink(event)">Services</a>
                      </li>
                      <li>
                        <a href="/programs/" class="valign-cell">Programs &amp; initiatives</a>
                      </li>
                      <li>
                        <a href="/the-latest/" class="valign-cell"> News &amp; events</a>
                      </li>
                      <li>
                        <a href="/documents/" class="valign-cell"> Publications &amp; forms</a>
                      </li>
                    </ul>
                </nav>
              </div>
            </div>
            <div class="small-5 medium-1 columns valign phn-m">
              <div class="valign-cell">
                <button class="site-search" type="button"  data-toggle="search-dropdown">
                  <i class="fa fa-search fa-2x" aria-hidden="true"></i>
                  <span class="show-for-small-only">Search</span>
                </button>
              </div>
            </div>
          </div> <!-- close row -->
          <!--Begin mobile nav -->
          <div class="top-bar">
            <div class="title-bar small-5 columns" data-responsive-toggle="mobile-nav" data-swiftype-index="false" data-hide-for="medium">
              <button class="menu-icon" type="button" data-toggle>
                <i class="fa fa-bars fa-3x" aria-hidden="true"></i>
                <span class="title-bar-title">Menu</span>
              </button>
            </div>
            <div class="primary-menu medium-15 medium-push-2 small-24 columns valign-mu" id="mobile-nav">
              <div class="top-bar-right valign-mu show-for-small-only">
              <nav data-swiftype-index="false" class="valign-mu">
                <ul id="mobile-nav-drilldown" class="vertical menu pan valign-mu">
                  <li><a href="/"><i class="fa fa-home fa-lg"></i> Home</a></li>
                  <li class="is-drilldown-submenu-parent" tabindex="0">
                    <a href="#services" class="valign-cell"><i class="fa fa-list show-for-small-only"></i>Services</a>
                    <ul class="menu vertical menu-top-offset" tabindex="0">
                      <li tabindex="0"><a href="/services/">Service directory</a></li>
                      <?php
                        $args = array(
                          //TODO: only display pages with taxonomy applied
                          'post_type' => 'service_page',
                          'orderby' => 'menu_order',
                          'order' => 'ASC',
                          'title_li' => '',
                          'link_before' => '<span>',
                          'link_after'  => '</span>',
                        );
                        wp_list_pages($args);
                      ?>
                    </ul>
                    </li>
                    <li tabindex="0">
                      <a href="/programs/" class="valign-cell"><i class="fa fa-info-circle"></i> Programs &amp; initiatives</a>
                    </li>
                    <li tabindex="0">
                      <a href="/news/" class="valign-cell"><i class="fa fa-microphone"></i> News &amp; events</a>
                    </li>
                    <li tabindex="0">
                      <a href="/documents/" class="valign-cell"><i class="fa fa-file-text"></i> Publications &amp; forms</a>
                    </li>
                    <li class="bg-sidewalk" tabindex="0">
                      <a href="/mayor/"><i class="fa fa-university"></i> Mayor's Office</a>
                    </li>
                    <li class="bg-sidewalk" tabindex="0"><a href="/departments/"><i class="fa fa-sitemap"></i> City government directory</a>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div><!--End mobile nav -->
        </div><!-- close columns -->
      </div>
    </div>
  </div>
</header>
<div id="page">
  <?php
    //create alerts when appropriate
    call_user_func(array('Phila_Gov_Site_Wide_Alert_Rendering', 'create_site_wide_alerts')); ?>
    <?php
    $parent = phila_util_get_furthest_ancestor($post);
    $post_type = get_post_type();
    if ( !phila_util_is_v2_template( $parent->ID ) &&
        !is_front_page() &&
        !is_404() &&
        !is_page_template('templates/the-latest.php') &&
        $post_type != 'programs' &&
        $post_type != 'event_spotlight') :
        get_template_part( 'partials/breadcrumbs' );
      ?>
    <?php endif; ?>

  <div id="content">
