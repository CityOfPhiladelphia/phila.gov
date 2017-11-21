<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package phila-gov
 */

get_header(); ?>

<section id="primary" class="content-area archive">

  <?php if ( have_posts() ) : ?>
    <div class="row">
      <header class="columns">
        <h1 class="contrast">
          <?php
            get_the_archive_title();
          ?>
        </h1>
      </header><!-- .page-header -->
    </div>
    <?php if ( !is_category() ):?>
    <div class="row mbxl">
      <div class="small-24 columns">
        <section>
          <div class="bg-light-blue pam">
          <div class="row column">
            <div class="valign equal-height border-bottom-sidewalk">
                <div class="valign-cell equal prs">
                  <i class="fa fa-info-circle fa-3x" aria-hidden="true"></i>
                </div>
                <div class="valign-cell equal">
                  <h3>You’re on the City’s pilot website, beta.phila.gov. <br/> The <em>Publications & forms</em> section of beta.phila.gov is a work-in-progress.</h3>
                </div>
            </div>
          </div>
          <div class="row column">
            <div class="pam">
              <p>We’re in the process of creating a new City website from the ground up because we realize phila.gov isn’t as easy to use as it should be.</p>

              <p>Below you’ll find a collection of City forms, publications, and documents. In the future, we’d like to better organize the City’s publications and forms so you can find them in one place. We’ll update this page as we release enhanced designs.</p>

              <p>In the meantime, we’re happy to consider your suggestions. How can we better organize this page? Your insights might inform future design and content improvements to the site.</p>
              <ul class="list-style-none mln pln pvm">
                <li class="mbm"><i class="fa fa-arrow-right fa-lg prs" aria-hidden="true"></i> Use the site’s <i class="fa fa-lightbulb-o fa-lg" aria-hidden="true"></i> feedback links to alert us to content errors and design ideas.</li>
                <li class="mbm"><i class="fa fa-arrow-right fa-lg prs" aria-hidden="true"></i> <a href="<?php phila_util_echo_tester_url()?>">Sign up to be a beta.phila.gov tester.</a> We’ll contact you for specific feedback on the <em>Publications &amp; forms</em> section and other site features.</li>
              </ul>
            </div>
          </div>
        </div>
        </section>
      </div>
    </div><!-- .beta-preface -->
  <?php endif; ?>

    <div class="row">
      <main id="main" class="site-main small-24 columns">
        <?php while ( have_posts() ) : the_post(); ?>

          <?php get_template_part( 'partials/posts/content', 'list-image' ) ?>

        <?php endwhile; ?>

        <?php phila_gov_paging_nav(); ?>

      <?php else : ?>

        <?php get_template_part( 'partials/content', 'none' ); ?>

      <?php endif; ?>

    </main><!-- #main -->
  </div>
</section><!-- #primary -->
<?php get_footer(); ?>
