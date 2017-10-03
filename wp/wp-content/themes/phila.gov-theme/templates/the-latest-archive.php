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

<section id="primary" class="content-area archive">
  <div class="row">
    <main id="main" class="site-main small-18 columns medium-centered">
      <form>
        <div class="search">
          <input id="post-search" type="text" name="search" placeholder="Search by title" class="search-field">
          <input type="submit" value="submit" class="search-submit">
        </div>
      </form>

    <div id="filter-results" class="bg-ghost-gray pam">
      <form>
        <fieldset>
          <legend class="h4">Filter results</legend>
          <input id="featured" type="checkbox" name="featured" value="featured">
          <label for="featured">Featured</label>
          <input id="posts" type="checkbox" name="posts" value="posts">
          <label for="posts">Posts</label>
          <input id="press-releases" type="checkbox" name="press-releases" value="press-releases">
          <label for="press-releases">Press releases</label>
        </fieldset>
      </form>
    </div>


      <?php phila_gov_paging_nav(); ?>

    </main><!-- #main -->
  </div>
</section><!-- #primary -->
<?php get_footer(); ?>
