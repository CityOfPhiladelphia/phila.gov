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

  <?php global $post;
    if ( is_home() || is_search() || is_404()) {
      $title = wp_title( '|', false, 'right' );
    }else {
      global $post;
      $post_parent = $post->post_parent;
      $title = ( $post_parent ) ? phila_get_full_page_title() : wp_title( '|', false, 'right' );
    }
  ?>

  <!-- Swiftype -->
  <meta class="swiftype" name="title" data-type="string" content="<?php echo str_replace(' | ' . get_bloginfo('name'), '', $title); ?>" />
  <?php if (is_single()) { ?>
  <meta class="swiftype" name="published_at" data-type="date" content="<?php echo get_the_time('c', $post->ID); ?>" />
  <?php } ?>

  <title><?php echo $title ?></title>

  <link rel="shortcut icon" type="image/x-icon" href="//cityofphiladelphia.github.io/patterns/images/favicon.ico">

  <?php wp_head(); ?>

  <!--[if lte IE 8]>
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri();?>/phila.gov-styles/ie8.css" type="text/css" media="all">
        <p class="browsehappy alert">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
      <![endif]-->
  <!--[if IE 9]>
      <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri();?>/phila.gov-styles/ie9.css" type="text/css" media="all">
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
  <?php if (alpha_alert()){ //show the alpha alert if set to true in functions.php ?>

    <div data-swiftype-index='false' id="alpha-alert">
      <div class="row">
        <div class="large-15 columns">
          <p>This service is in <a href="http://alpha.phila.gov/about/">Alpha</a>: it is a work in progress and may contain errors or inaccuracies.</p>
          <a class="go-back small-text" href="http://www.phila.gov" target="_blank">
            <i class="fa fa-reply"></i> Take me back to Phila.gov<span class="accessible"> Opens in new window</span></a>
        </div>
        <div class="large-9 columns contact">
          <i class="fa fa-comments"></i> <a class="feedback" href="<?php get_template_part( 'partials/content', 'feedback-url' ); ?>" target="_blank">
            <?php printf( __( 'Provide Feedback', 'phila-gov' )); ?>
            <span class="accessible"> Opens in new window</span>
          </a>
          <i class="fa fa-globe"></i><div id="google_translate_element"></div>
            <script type="text/javascript">
              function googleTranslateElementInit() {
                new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
              }
            </script>
            <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
        </div>
      </div>
    </div>
<?php }  ?>
  <header data-swiftype-index='false' id="masthead" class="site-header" role="banner">
    <div class="row site-branding">
      <div class="small-24 medium-12 columns">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
          <img src="<?php echo get_stylesheet_directory_uri();?>/img/city-of-philadelphia@2x.png" alt="City of Philadelphia" height="100" class="logo"></a>
          <h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
          <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
        </div>
        <?php if(!is_front_page() && !is_page_template('search-page.php') && !is_404()) {?> <div class="search-site small-24 medium-12 columns"> <?php get_search_form(); ?> </div> <?php }?>

      <a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'phila-gov' ); ?></a>
    </div>
  </header><!-- #masthead -->
    <?php call_user_func(array('PhilaGovSiteWideAlertRendering', 'create_site_wide_alerts'));
    ?>
    <?php if ( function_exists( 'the_breadcrumb' ) && !is_front_page() ) { ?>
      <div class="row">
        <div class="small-24 columns">
          <div class="divider"></div>
        </div>
      </div>
      <div class="row">
        <div data-swiftype-index='false' id="breadcrumbs" class="large-24 columns">
          <nav><?php the_breadcrumb(); ?> </nav>
        </div>
      </div> <?php } ?>
    <div id="content" class="site-content">
