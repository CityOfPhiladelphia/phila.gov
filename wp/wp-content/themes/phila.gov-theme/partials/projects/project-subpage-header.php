<?php

$parent = phila_util_get_furthest_ancestor($post);
$ancestors = get_post_ancestors($post);

$parent_id = $parent->ID;
$desc = rwmb_meta( 'phila_meta_desc', array(), $parent_id );

?>

<header>
    <div class="hero-content project-header">
        <div class="hero-subpage" style="background-color:#0f4d90;">
            <div class="row expanded pbs pvxxl-mu">
                <div class="medium-18 small-centered columns text-overlay">
                    <h1 class="hero-title"><?php echo $parent->post_title; ?></h1>
                </div>
            </div>
        </div><!-- END .row.expanded   -->
        <?php
        phila_get_menu();
        ?>

    </div> <!-- END .hero-wrap  -->
</header>

<div class="mtl mbm">
    <?php get_template_part('partials/breadcrumbs'); ?>
</div>

<div class="grid-container">
    <div class="grid-x">
        <div class="cell">
            <h2 class="contrast"><?php echo the_title(); ?></h2>
        </div>
    </div>
</div>