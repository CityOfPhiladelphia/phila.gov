<?php
/*
 * Applied to all post type templates in the loop. This allows us to identify what page type each page is for swiftype search results. Must be used in the loop.
*/

?>
<span id="wp-template-identifier" class="hide" data-swiftype-name="content-type" data-swiftype-type="string"><?php echo get_post_type($post->ID)?></span>
