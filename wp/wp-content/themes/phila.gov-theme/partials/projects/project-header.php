<header>
    <div class="hero-content project-header" style="background-color:#0f4d90; height: 292px;">
        <div class="hero-subpage" style="color:blue">
            <div class="row expanded pbs pvxxl-mu">
                <div class="medium-18 small-centered columns text-overlay">
                    <h1 class="hero-title"><?php echo the_title(); ?></h1>
                    <p class="sub-title mbn-mu"><?php echo phila_get_item_meta_desc(); ?></p>
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