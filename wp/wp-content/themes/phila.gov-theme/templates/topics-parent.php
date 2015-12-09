<?php
/*
  *
  * template for displaying 1st level topics page.
  *
  */
  ?>
<nav class="topics-nav small-24 large-8 columns">
    <?php get_parent_topics(); ?>
</nav>

<?php

$current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
$term_children = get_term_children( $current_term->term_id, $current_term->taxonomy );

echo '<div class="small-24 large-16 columns parent results">';
echo '<h2>' . $current_term->name . '</h2>';
echo '<ul class="subtopics">';

  foreach ($term_children as $term_child){
    $term = get_term_by( 'id', $term_child, $current_term->taxonomy );
    if (!$term->count == 0) :
      echo '<li><a class="h4" href="' . get_term_link( $term_child, $current_term->taxonomy ) . '">' . $term->name;
      echo '<p class="child-description">' . $term->description . '</p></a></li>';
      echo '<hr>';
    endif;
  }
echo '</ul>';
echo '</div>';
