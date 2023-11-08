<?php
/*
Partial for Advanced Blog Posts Series Component
*/
$content = get_post_field('series_linking_text', $series['phila_post_picker']);
?>

<style>
blockquote {
  border-left: 2px solid black;
  padding-left: 15px;
} 
blockquote:before {
    content: ' ' !important;
}

</style>

<div>
    <blockquote><span><?php echo $content ?> <i><a href="<?php echo the_permalink($series['phila_post_picker']);?>">link to series</a></i></span></blockquote>
</div>