<?php
/*
Template Name: Blog
*/

$paged = get_query_var('paged');
query_posts('cat=-0&paged='.$paged);

global $more;
$more = 0;

load_template(TEMPLATEPATH . '/index.php');
