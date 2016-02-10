<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package phila-gov
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">

  <meta name="description" content="<?php bloginfo( 'description' ) ?>">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Swiftype -->
  <meta class="swiftype" name="title" data-type="string" content="<?php echo str_replace(' | ' . get_bloginfo('name'), '', phila_filter_title( $title ) ); ?>" />
  <?php if (is_single()) { ?>
  <meta class="swiftype" name="published_at" data-type="date" content="<?php echo get_the_time('c', $post->ID); ?>" />
  <?php } ?>


  <link rel="shortcut icon" type="image/x-icon" href="//cityofphiladelphia.github.io/patterns/images/favicon.ico">

  <?php wp_head(); ?>

  <!--[if lte IE 8]>
  <p class="browsehappy alert">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
  <![endif]-->

</head>

<body <?php body_class(); ?>>

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

  <header data-swiftype-index='false' id="masthead" class="site-header" role="banner">
    <div class="row site-branding">
      <div class="small-24 medium-12 columns">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo">
          <img src="//cityofphiladelphia.github.io/patterns/images/city-of-philadelphia-white.png"></a>
          <h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
          <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
        </div>
        <?php if(!is_front_page() && !is_page_template('search-page.php') && !is_404()) {?> <div class="search-site small-24 medium-12 columns"> <?php get_search_form(); ?> </div> <?php }?>
      <a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'phila-gov' ); ?></a>
    </div>
    <?php if ( function_exists( 'the_breadcrumb' ) && !is_front_page() ) : ?>
    <div class="row expanded">
      <div class="columns">
        <div class="row">
          <div data-swiftype-index='false' class="large-24 columns">
            <nav><?php the_breadcrumb(); ?> </nav>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  </header><!-- #masthead -->
    <?php call_user_func(array('Phila_Gov_Site_Wide_Alert_Rendering', 'create_site_wide_alerts'));
    ?>
    <?php if ( function_exists( 'phila_breadcrumbs' ) && !is_front_page() ) { ?>
      <div class="row">
        <div data-swiftype-index='false' id="breadcrumbs" class="large-24 columns">
          <nav><?php phila_breadcrumbs(); ?> </nav>
        </div>
      </div> <?php } ?>
      <div id="content" class="site-content">
