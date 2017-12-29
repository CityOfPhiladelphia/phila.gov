<?php $programs_query = new WP_Query( array( 'post_type' => 'programs', 'category_name' => 'philadelphia-parks-recreation-staged') ); ?>
<?php d($programs_query); ?>
<?php if ( $programs_query->have_posts() ) : ?>
<section class="row progs-inits-grid">
    <div class="columns">
        <h2 class="contrast">Programs and initiatives</h2>
        <p>Find out about how our programs help make Philly a great place to live for everyone!</p>
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
