<?php

 $categories = get_the_category();
  $category_slug = $categories[0]->slug;
  $programs_query = new WP_Query( array( 'post_type' => 'programs', 'category_name' => $category_slug) );
?>

<?php if ( rwmb_meta('phila_progs_inits_grid_shown') ) : ?>
<section class="row progs-inits-grid">
    <div class="columns">
        <h2 class="contrast">Programs and initiatives</h2>
    </div>

    <div class="columns">
        <div class="grid-containter">
            <div class="grid-x grid-padding-x">
                <?php while ( $programs_query->have_posts()  ) : $programs_query->the_post(); ?>
                    <a href="<?= get_the_permalink();  ?>" class="cell large-6 small-12 progs-inits-grid__item"><?= the_title(); ?></a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<?php wp_reset_query();?>
