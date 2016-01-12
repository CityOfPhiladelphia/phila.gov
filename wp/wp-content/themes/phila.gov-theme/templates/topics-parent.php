<?php
/*
  *
  * Template for displaying L2 topic pages AND Pages tagged with "Show in browse".
  *
  */
  ?>
<nav class="topics-nav small-24 large-8 columns">
  <?php get_parent_topics(); ?>
</nav>

<?php
  //Get the normal terms.
  $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
  $term_children = get_term_children( $current_term->term_id, $current_term->taxonomy );
?>

<?php
  //Get the pages marked with 'show in browse'.
  $get_L2_pages = new WP_Query(array(
    'post_type' => 'page',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order'=> 'asc',
    'post_parent' => 0,
    'meta_key'  => 'phila_show_in_browse',
    'meta_value' => 1,
    'tax_query' => array(
      //only produce pages in the current topic
      array(
        'taxonomy' => 'topics',
        'field' => 'slug',
        'terms' => $current_term,
        'include_children' => false,
        ),
      ),
    )
  );

  $pages_and_topics = array();

  /* this loop does not actually display any posts
  TODO: reevaluate this, it should probably just be a sql query
  */
  if ( $get_L2_pages->have_posts() ) : ?>
    <?php while ( $get_L2_pages->have_posts() ) : $get_L2_pages->the_post(); ?>

      <?php
        if (function_exists('rwmb_meta')) {
          $page_desc = rwmb_meta( 'phila_page_desc', $args = array('type' => 'textarea'));
        }
        $permalink = get_permalink();
        $title = get_the_title();

        //add pages to array
        $pages_and_topics[$title][] = $page_desc;
        $pages_and_topics[$title][] = $permalink;

      ?>
    <?php endwhile; ?>
  <?php endif; ?>

<div class="small-24 large-16 columns parent results">
  <h1><?php echo $current_term->name ?></h1>

  <ul class="subtopics">
    <?php
    foreach ($term_children as $term_child) :
      $term = get_term_by( 'id', $term_child, $current_term->taxonomy );

      if ( !$term->count == 0 ) :
        $term_name = $term->name;
        $term_desc = $term->description;

        $term_link = get_term_link( $term_child, $current_term->taxonomy);

        //add taxonomy to array
        $pages_and_topics[$term_name][] = $term_desc;
        $pages_and_topics[$term_name][] = $term_link;

      endif;

    endforeach;
    //sort by name
    ksort($pages_and_topics); ?>

  <?php foreach ( $pages_and_topics as $display_name => $display_data ) : ?>
    <li>
      <?php //display_data[1] is permalink ?>
      <a href="<?php echo $display_data[1] ?>">
        <h2 class="h4"><?php echo $display_name ?></h2>
        <?php //display_data[0] is description ?>
        <p class="description"><?php echo $display_data[0] ?></p>
      </a>
    </li>
    <hr>
  <?php endforeach; ?>

  </ul>
</div>
