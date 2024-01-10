<?php

/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up until <div id="content">
 *
 * @package phila-gov
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <?php get_template_part('partials/global/analytics'); ?>
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="description" content="<?php echo (is_archive() || is_search() || is_home()) ? get_bloginfo('description') : phila_get_item_meta_desc(); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#2176d2">
  <meta name="facebook-domain-verification" content="a0t35pecrjiy8ly0weq7bzyb419o42">

  <!-- Swiftype -->
  <meta class="swiftype" name="title" data-type="string" content="<?php echo str_replace(' | ' . get_bloginfo('name'), '', phila_filter_title($title)); ?>">
  <meta class="swiftype" name="tags" data-type="string" content="wordpress" />
  <meta class="swiftype" name="site-priority" data-type="integer" content="10" />
  <meta class="swiftype" name="content_type" data-type="string" content="<?php echo get_post_type($post->ID) ?>">
  <meta class="swiftype" name="weighted_search" data-type="integer" content="<?php phila_weighted_search_results() ?>">

  <?php if (is_single()) : ?>
    <meta class="swiftype" name="published_at" data-type="date" content="<?php echo get_the_time('c', $post->ID); ?>" />
  <?php endif; ?>

  <link rel="shortcut icon" type="image/x-icon" href="//www.phila.gov/assets/images/favicon.ico">

  <?php wp_head(); ?>

  <!--[if lte IE 9]>
  <p class="browsehappy alert">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience. If you can't switch browsers, turn off  compatibility mode.</p>
  <![endif]-->

  <?php if ((!is_home()) && (is_single())) : ?>
    <?php if (function_exists('rwmb_meta')) : ?>
      <?php $append_to_head = rwmb_meta('phila_append_to_head', $args = array('type' => 'textarea'), $post->ID); ?>
      <?php if (!$append_to_head == '') : ?>
        <!-- Begin Custom Markup Metabox: Append to Head -->
        <?php echo $append_to_head; ?>
        <!-- End Custom Markup Metabox: Append to Head -->
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>

</head>

<body <?php body_class(); ?> lang="en" data-clarity-unmask="True">
  <?php if (!is_user_logged_in()) : ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MC6CR2" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
  <?php endif; ?>
  <a href="#page" aria-hidden="false" class="skip-to-content">Skip to main content</a>
  <header class="global-nav no-js pbn-mu mbn-mu">
    <h1 class="accessible">City of Philadelphia</h1>

    <!-- Utility Navigation -->
    <div class="utility-nav">
      <div class="grid-container bg-ghost-gray">
        <div class="top-bar bg-ghost-gray" id="responsive-menu">
          <div class="top-bar-left valign-mu">
            <ul class="menu">
              <li class="gov-site show-for-medium">
                An official website of the City of Philadelphia government
              </li>
              <li class="gov-site show-for-medium"><a href="#" class="trusted-site-toggle">Here's how you know<i class="fas fa-solid fa-caret-down"></i></a></li>
              <li class="gov-site show-for-small-only">
                <a href="" class="trusted-site-toggle">An official website <i class="fas fa-info-circle"></i></a>
              </li>
            </ul>
          </div>
          <!-- Translations Navigation -->
          <div class="top-bar-right translations-nav">
            <ul class="translations-bar menu">
              <li class="show-for-medium"><a id="translate-english" href="<?php echo inject_translation_slug('en'); ?>">English</a></li>
              <?php if ((is_front_page() === True) or (get_post_type($post->ID) != 'post')) : ?>
                <li class="show-for-medium"><a id="translate-spanish" href="<?php echo inject_translation_slug('es'); ?>">Español</a></li>
                <li class="show-for-medium"><a id="translate-chinese" href="<?php echo inject_translation_slug('zh'); ?>">中文</a></li>
              <?php endif; ?>
            <!-- Dropdown button -->
              <button translate="no" class="translations-button show-for-medium" id="desktop-lang-button" data-toggle="lang-dropdown">
                <i class="fa fa-earth-americas"></i>
                <i class="translate-caret fas fa-solid fa-caret-down"></i>
              </button>
              <button class="translations-button show-for-small-only" id="mobile-lang-button" data-toggle="lang-dropdown">
                <i class="fa fa-earth-americas"></i>
                  <a tabindex="-1" class="show-for-small-only" href="#">Translate</a>
                <i class="translate-caret fas fa-solid fa-caret-down"></i>
              </button>
            </ul>
            <!-- Dropdown menu -->
            <div id="lang-dropdown" class="dropdown-pane" data-close-on-click="true" data-position="bottom" data-alignment="right" data-dropdown data-auto-focus="true">
              <ul id="translations-menu" class="vertical dropdown menu">
                <li class="show-for-small-only"><a id="translate-english-dropdown" href="<?php echo inject_translation_slug('en'); ?>">English</a></li>
                <?php if ((is_front_page() === True) or (get_post_type($post->ID) != 'post')) : ?>
                  <li class="show-for-small-only"><a id="translate-spanish-dropdown" href="<?php echo inject_translation_slug('es'); ?>">Español</a></li>
                  <li class="show-for-small-only"><a id="translate-chinese-dropdown" href="<?php echo inject_translation_slug('zh'); ?>">中文</a></li>
                  <li><a id="translate-arabic-dropdown" href="<?php echo inject_translation_slug('ar'); ?>">عربي</a></li>
                  <li><a id="translate-haitian-creole-dropdown" href="<?php echo inject_translation_slug('ht'); ?>">Ayisyen</a></li>
                  <li><a id="translate-french-dropdown" href="<?php echo inject_translation_slug('fr'); ?>">Français</a></li>
                  <li><a id="translate-swahili-dropdown" href="<?php echo inject_translation_slug('sw'); ?>">Kiswahili</a></li>
                  <li><a id="translate-portuguese-dropdown" href="<?php echo inject_translation_slug('pt'); ?>">Português</a></li>
                  <li><a id="translate-russian-dropdown" href="<?php echo inject_translation_slug('ru'); ?>">Pyccкий</a></li>
                  <li><a id="translate-vietnamese-dropdown" href="<?php echo inject_translation_slug('vi'); ?>">Tiếng Việt</a></li>
                <?php endif; ?>
                <li id="google_translate_element"></li>
                <li id="translations-support"><a href="<?php echo get_site_url() . "/programs/language-access-philly/translation-feedback-and-support/" ?> "><i class="fa fa-messages"></i>Feedback and support</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Trusted Site -->
    <div class="row columns expanded" data-swiftype-index="false" id="trusted-site">
      <div class="row pvm">
        <div class="medium-12 columns">
          <div class="medium-horizontal">
            <div class="trust-icon">
              <span class="icon circle-icon">
                <i class="fas fa-lock"></i>
              </span>
              <span class="icon-text">https://</span>
            </div>
            <div class="trust-details">The <strong>https://</strong> in the address bar means your information is encrypted and can not be accessed by anyone else</div>
          </div>
        </div>
        <div class="medium-12 columns dot-gov">
          <div class="medium-horizontal">
            <div class="trust-icon">
              <span class="icon circle-icon">
                <i class="fas fa-university"></i>
              </span>
              <span class="icon-text">.gov</span>
            </div>
            <div class="trust-details">Only government entities in the U.S. can end in .gov</div>
          </div>
        </div>
        <a href="javascript:void(0)" aria-label="Close Trusted Site Details" id="trusted-site-close" class="float-right">
          <i class="fas fa-times fa-2x"></i>
        </a>
      </div>
    </div>
    <!-- sticky/desktop nav -->
    <div id="global-sticky-nav" class="row">
      <div class="small-24 columns">
        <div class="row primary-menu" data-sticky-container data-swiftype-index="false">
          <div class="columns phila-sticky phn" data-margin-top="0" data-sticky data-sticky-on="medium">
            <div class="row sticky-header-width">
              <div class="small-4 columns menu-icon-container" data-responsive-toggle="mobile-nav" data-swiftype-index="false" data-hide-for="medium">
                <button class="menu-icon" type="button" data-toggle>
                  <i class="fass fa-bars" aria-hidden="true"></i>
                </button>
              </div>
              <div class="small-16 medium-4 columns valign medium-push-0 logo-container">
                <a href="<?php echo get_home_url(); ?>" class="logo" aria-label="City of Philadelphia">
                  <img src="<?php echo get_stylesheet_directory_uri() . "/img/city-of-philadelphia-logo.svg" ?>" data-fallback="//www.phila.gov/assets/images/city-of-philadelphia.png" alt="City of Philadelphia" />
                </a>
              </div>
              <div class="medium-16 columns pan show-for-medium desktop-nav">
                <div class="top-bar-right">
                  <nav data-swiftype-index="false" class="global-nav" aria-label="main-nav">
                    <ul class="menu">
                      <li class="services-menu-link" data-toggle="services-mega-menu">
                        <a href="" class="no-link " data-link="/services/" onclick="noLink(event)">Services</a>
                      </li>
                      <li class="programs-menu-link">
                        <a href="<?php echo get_site_url() ?>/programs/" class="">Programs</a>
                      </li>
                      <li class="departments-menu-link">
                        <a href="<?php echo get_site_url() ?>/departments/" class="">Departments</a>
                      </li>
                      <li class="tools-menu-link">
                        <a href="<?php echo get_site_url() ?>/tools/" class="">Tools</a>
                      </li>
                      <li class="publications-menu-link">
                        <a href="<?php echo get_site_url() ?>/documents/" class=""> Publications</a>
                      </li>
                      <li class="news-menu-link">
                        <a href="<?php echo get_site_url() ?>/the-latest/" class=""><i class="fa-solid fa-newspaper"></i> News</a>
                      </li>
                    </ul>
                  </nav>
                </div>
              </div>
              <div class="small-4 medium-1 columns phn-m prn site-search-container">
                <button id="site-search-button" class="site-search" type="button" data-toggle="search-dropdown">
                  <i class="fass fa-magnifying-glass" aria-hidden="true"></i>
                </button>
              </div>
            </div> <!-- close row -->
          </div><!-- close columns -->
        </div>
      </div>
    </div>
    <?php include(locate_template('partials/global/translations-modal.php')); ?>
    <div id="phila-mobile-menu"></div>
    <div id="phila-site-wide-alerts"></div>
  </header>
  <div id="page">
    <?php
    $parent = phila_util_get_furthest_ancestor($post);
    $post_type = get_post_type();
    if (
      !phila_util_is_new_template($parent->ID) &&
      !is_front_page() &&
      !is_404() &&
      !is_page_template('templates/the-latest.php') &&
      $post_type != 'programs' &&
      $post_type != 'guides' &&
      $post_type != 'event_spotlight'
    ) : ?>
      <div class="mtl mbm">
        <?php get_template_part('partials/breadcrumbs'); ?>
      </div>
    <?php endif; ?>

    <div id="content">