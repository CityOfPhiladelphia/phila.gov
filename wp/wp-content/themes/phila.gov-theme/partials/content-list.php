<?php
/**
 * The template used for displaying sortable list content,
 * primarily used in:
 * Department list
 *
 * @package phila-gov
 */
?>

<li>
  <?php
    //NOTE: The content-department class is used for Google Analytics and should not be removed.
  ?><a href="<?php echo get_permalink(); ?>" class="content-department item"><?php echo the_title(); ?></a>
  <p class="item-desc"><?php echo the_dept_description(); ?> </p>
</li>
