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

  <header class="global-nav no-js pbm pbn-mu mbn-mu">
    <h1 class="accessible">City of Philadelphia</h1>
    <!-- Beta opt-out -->
    <?php get_template_part( 'partials/content', 'beta-alert' ); ?>
    <!-- Secondary Navigation -->
    <div class="row columns bg-ben-franklin-blue expanded secondary-nav">
      <div class="row">
        <div class="columns">
          <div class="top-bar">
            <div class="top-bar-right">
              <ul class="medium-horizontal menu show-for-medium">
                <li><a href="/departments/mayor/" aria-hidden="true">Mayor's Office</a></li>
                <li><a href="/departments/" aria-hidden="true">City government directory</a></li>
                <li>
                  <div id="google_translate_element" class="no-js"><span class="show-for-sr">Google Translate</span></div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="title-bar small-5 columns" data-responsive-toggle="mobile-nav">
      <button class="menu-icon" type="button" data-toggle>
        <i class="fa fa-bars fa-3x" aria-hidden="true"></i>
        <span class="title-bar-title">Menu</span>
      </button>
    </div>
    <!--mobile nav -->
    <div class="top-bar primary-menu medium-15 medium-push-2 small-24 columns equal no-js valign-mu" id="mobile-nav">
      <!-- Mobile Navigation -->
      <a href="#page" aria-hidden="false" class="accessible">Skip to main content</a>
      <div class="top-bar-right valign-mu show-for-small-only">
        <nav data-swiftype-index="false" class="valign-mu">
          <ul class="vertical menu pan valign-mu mobile-nav-drilldown">
            <li><a href="/"><i class="fa fa-home fa-lg" aria-hidden="true"></i> Home</a></li>

            <li class="is-drilldown-submenu-parent">
              <a href="/services/" class="no-link valign-cell" onclick="noLink(event)"><i class="fa fa-list show-for-small-only" aria-hidden="true"></i> Services</a>
              <ul class="menu vertical menu-top-offset">
                <li><a href="/services/">Service directory</a></li>
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
            <li>
              <a href="/programs-initiatives/" class="valign-cell"><i class="fa fa-info-circle" aria-hidden="true"></i> Programs &amp; initiatives</a>
            </li>
            <li>
              <a href="/news/" class="valign-cell"><i class="fa fa-microphone" aria-hidden="true"></i> News &amp; events</a>
            </li>
            <li>
              <a href="/documents/" class="valign-cell"><i class="fa fa-file-text" aria-hidden="true"></i> Publications &amp; forms</a>
            </li>
            <li class="bg-sidewalk">
              <a href="/mayor/"><i class="fa fa-university" aria-hidden="true"></i> Mayor's Office</a>
            </li>
            <li class="bg-sidewalk"><a href="/departments/"><i class="fa fa-sitemap" aria-hidden="true"></i> City government directory</a>
            </li>
          </ul>
          </nav>
        </div>
      </div>

    <!--sticky/desktop nav -->
    <div class="row expanded background-white primary-menu" data-sticky-container>
      <div class="columns sticky phn" data-sticky data-margin-top="0">
        <div class="row equal-height">
          <div class="small-16 medium-6 columns equal valign small-push-4 medium-push-0">
            <div class="valign-cell">
              <a href="<?php echo get_home_url(); ?>" class="logo">
                <img src="<?php echo get_stylesheet_directory_uri() . "/img/city-of-philadelphia-logo.svg" ?>" data-fallback="//cityofphiladelphia.github.io/patterns/images/city-of-philadelphia.png" alt="City of Philadelphia">
              </a>
            </div>
          </div>
          <div class="medium-17 columns show-for-medium equal desktop-nav">
            <div class="top-bar-right valign-mu">
              <nav data-swiftype-index="false" class="valign-mu">
                <ul class="horizontal menu pan valign-mu">
                  <li class="services-menu-link" data-toggle="services-mega-menu">
                    <a href="" class="no-link valign-cell" data-link="/services/" onclick="noLink(event)"> Services</a>
                    </li>
                  <li>
                    <a href="/programs-initiatives/" class="valign-cell">Programs &amp; initiatives</a>
                  </li>
                  <li>
                    <a href="/news/" class="valign-cell"> News &amp; events</a>
                  </li>
                  <li>
                    <a href="/documents/" class="valign-cell"> Publications &amp; forms</a>
                  </li>
                </ul>
                </nav>
              </div>
            </div>
            <div class="small-5 medium-1 columns equal">
              <button class="site-search valign" type="button"  data-toggle="search-dropdown">
                <i class="fa fa-search fa-2x" aria-hidden="true"></i>
                <span class="show-for-small-only">Search</span>
              </button>
            </div>
          </div>
        </div>
      </div>
  </header>
<div id="page" class="hfeed site">

<!-- #masthead -->
  <?php
    //create alerts when appropriate
    call_user_func(array('Phila_Gov_Site_Wide_Alert_Rendering', 'create_site_wide_alerts')); ?>
    <?php if ( !is_front_page() ) : ?>
      <div class="row mts mbm">
        <div class="columns">
          <?php echo phila_breadcrumbs(); ?>
        </div>
      </div>
    <?php endif; ?>
<div id="content" class="site-content">
