<?php
/**
 * The template used for displaying sortable list content,
 * primarily used in:
 * Topic pages
 * Department list
 *
 * @package phila-gov
 */
?>

<li>
  <?php
  if ( is_tax( 'topics' ) ) :
    the_title( sprintf( '<h2 class="h4"><a href="%s" class="item">', esc_url( get_permalink() ) ), '</a></h2>' );
  else : //it's the department list
    //NOTE: The content-department class is used for Google Analytics and should not be removed.
  ?><a href="<?php echo get_permalink(); ?>" class="content-department item"><?php echo the_title(); ?></a>
  <p class="item-desc"><?php echo the_dept_description(); ?> </p>
  <?php endif; ?>
</li>
