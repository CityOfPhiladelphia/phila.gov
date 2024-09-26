<?php

$rows = MB_Relationships_API::get_connected( [
    'id'   => 'series_to_post_relationship',
    'from' => get_the_ID(),
] );

?>
<div class="series-grid grid-x grid-margin-x">
    <?php
    foreach ($rows as $row) {
        $post = get_post($row);
        $post_type = $post->post_type;
        $template_type = phila_get_selected_template();
    ?>
        <div class="series cell medium-8 flex-container flex-dir-column mam">
            <a href="<?php echo the_permalink($row); ?>" class="card card--<?php echo 'series' ?>">
                <div class="flex-child-shrink">
                    <?php if (has_post_thumbnail($row)) : ?>
                        <img class = "series-img" src="<?php echo wp_get_attachment_image_src(get_post_thumbnail_id($row), 'medium')[0] ?>">
                    <?php endif; ?>
                </div>
                <div class="card--content pam">
                    <div class="cell align-self-top post-label post-label--<?php echo 'series' ?>">
                        <header class="cell mvm">
                            <h1><?php echo get_the_title($row); ?></h1>
                        </header>
                    </div>
                    <div class="cell align-self-bottom">
                        <div class="post-meta">
                            <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d', false, $row); ?>"><?php echo get_the_date('', $row); ?></time></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php
    }
    wp_reset_postdata();
    ?>
    </div>