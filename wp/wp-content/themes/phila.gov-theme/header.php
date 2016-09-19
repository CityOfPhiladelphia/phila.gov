<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up until <div id="content">
 *
 * @package phila-gov
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">

  <meta name="description" content="<?php echo phila_get_item_meta_desc(); ?>">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Swiftype -->
  <meta class="swiftype" name="title" data-type="string" content="<?php echo str_replace(' | ' . get_bloginfo('name'), '', phila_filter_title( $title ) ); ?>">

  <?php if (is_single()) : ?>
    <meta class="swiftype" name="published_at" data-type="date" content="<?php echo get_the_time('c', $post->ID); ?>" />
  <?php endif; ?>

  <link rel="shortcut icon" type="image/x-icon" href="//cityofphiladelphia.github.io/patterns/images/favicon.ico">

  <?php wp_head(); ?>

  <!--[if lte IE 9]>
  <p class="browsehappy alert">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
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

<body <?php body_class(); ?>>

<?php if ( !is_archive() && !is_tax() && !is_home() ) : ?>
  <!-- Google Tag Manager DataLayer -->
  <?php $category = get_the_category();
    $departments = phila_get_current_department_name( $category, $byline = false, $break_tags = false, $name_list = true );
  ?>
  <script>
    dataLayer = [{
      "contentModifiedDepartment": "<?php echo $departments ?>"
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

<div id="page" class="hfeed site">

  <?php get_template_part( 'partials/content', 'alpha-alert' ); ?>
  <header class="global-nav">
    <!-- Navigation -->
    <div class="title-bar" data-responsive-toggle="beta-global-nav" data-hide-for="medium">
      <button class="menu-icon" type="button" data-toggle><span class="title-bar-title">Menu</span></button>
    </div>

    <div class="top-bar" id="beta-global-nav">
      <nav class ="top-bar-right" data-swiftype-index="false">
        <ul class="medium-horizontal menu" data-responsive-menu="drilldown medium-dropdown">
          <li class="service-menu-link" data-toggle="services-mega-menu">
            <a href="#">Services</a>
            <ul class="menu vertical">
              <?php
                $args = array(
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
          <li role="menuitem">
            <a href="#programs-and-initiatives">Programs & Initiatives</a>
          </li>
          <li role="menuitem">
            <a href="#news-and-events">News &amp; Events</a>
          </li>
          <li role="menuitem">
            <a href="#publications-and-forms">Publications &amp; Forms</a>
          </li>
        </ul>
      </nav>

      <div class="dropdown-pane" id="services-mega-menu" data-dropdown data-options="closeOnClick:true; hover: true; hoverPane: true">
        <div class="row expanded mbxs collapse">
          <div class="medium-8 columns">
            <a href="">Service Cat 1</a>
          </div>
          <div class="medium-8 columns">
            <a href="">Service Cat 2</a>
          </div>
          <div class="medium-8 columns">
            <a href="">Service Cat 3</a>
          </div>
        </div>
        <div class="row expanded mbxs collapse">
          <div class="medium-8 columns">
            <a href="">Service Cat 4</a>
          </div>
          <div class="medium-8 columns">
            <a href="">Service Cat 5</a>
          </div>
          <div class="medium-8 columns">
            <a href="">Service Cat 6</a>
          </div>
        </div>
        <div class="row expanded mbxs collapse">
          <div class="medium-8 columns">
            <a href="">Service Cat 7</a>
          </div>
          <div class="medium-8 columns">
            <a href="">Service Cat 8</a>
          </div>
          <div class="medium-8 columns">
            <a href="">Service Cat 9</a>
          </div>
        </div>
        <div class="row expanded collapse bg-ghost-gray">
          <div class="float-right pam pll white bg-ben-franklin-blue">
            Services Directory
          </div>
        </div>
      </div>
    </div>
  </header>
<!-- #masthead -->
  <?php
    //create alerts when appropriate
    call_user_func(array('Phila_Gov_Site_Wide_Alert_Rendering', 'create_site_wide_alerts')); ?>

<div id="content" class="site-content">
