<?php
/**
 * Template Name: The latest archives
 * Description: Custom Page template for The latest archive page
 * @package phila-gov
 */

get_header(); ?>

<div class="row">
  <header class="columns">
    <h1 class="contrast">
      <?php echo get_the_title(); ?>
    </h1>
  </header>
</div>


<section id="archive-page" class="content-area archive">

  <div class="row">
    <main id="main" class="site-main small-18 columns medium-centered">
      <form>
        <div class="search">
          <input id="post-search" type="text" name="search" placeholder="Search by title" class="search-field">
          <input type="submit" value="submit" class="search-submit">
        </div>
      </form>

      <div class="accordion bg-ghost-gray pam" data-accordion>
        <div id="filter-results" class="accordion-item is-active" data-accordion-item>
          <a class="h4" class="accordion-title">Filter results</a>
          <div class="accordion-content" data-tab-content>
            <form>
              <fieldset>
                <input id="featured" type="checkbox" name="featured" value="featured">
                <label for="featured">Featured</label>
                <input id="posts" type="checkbox" name="posts" value="posts">
                <label for="posts">Posts</label>
                <input id="press-releases" type="checkbox" name="press-releases" value="press-releases">
                <label for="press-releases">Press releases</label>
              </fieldset>
            </form>
          </div>
        </div>
      </div>

      <div class="results"></div>

      <?php phila_gov_paging_nav(); ?>

    </main><!-- #main -->
  </div>
</section><!-- #primary -->
<?php get_footer(); ?>
