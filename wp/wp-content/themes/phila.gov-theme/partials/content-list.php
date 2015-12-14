<?php
/**
 * The template used for displaying sortable list content
 *
 * @package phila-gov
 */
?>

<li>

    <?php
    if (is_tax('topics')){
        the_title( sprintf( '<h2 class="h3"><a href="%s" rel="bookmark" class="item">', esc_url( get_permalink() ) ), '</a></h2>' );
    }else //it's the department list so:
        {
    ?><a href="<?php echo get_permalink(); ?>" class="content-department item"><?php echo the_title(); ?></a>
    <?php
        echo '<p class="item-desc">' . the_dept_description() . '</p>';
    }

    ?>
</li>
