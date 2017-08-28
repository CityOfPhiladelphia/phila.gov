<?php
/**
 * The content of a single phila_post
 * @package phila-gov
 */
?>
<?php $category = get_the_category(); ?>
<?php $posted_on_values = phila_get_posted_on(); ?>
<?php $the_title =  get_the_title();?>
<?php $email_title = urlencode(html_entity_decode($the_title)); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post img-floats'); ?>>
  <header class="post-header grid-container">
    <div class="grid-x grid-padding-x align-bottom">
      <div class="cell medium-18 post-title">
        <?php the_title( '<h1>', '</h1>' ); ?>
      </div>
      <div class="cell medium-6 align-self-bottom social-media">
        <a href="#" id="fb-share" data-analytics="social"><i class="fa fa-facebook" aria-hidden="true"></i></a>
        <a href="https://twitter.com/intent/tweet?text=<?php echo phila_encode_title($the_title)?>&url=<?php echo get_permalink()?>"><i class="fa fa-twitter" aria-hidden="true"></i></a>
        <a href="mailto:?subject=<?php echo str_replace('+', '%20', $email_title) ?>&body=<?php echo get_permalink()?>" data-analytics="social"><i class="fa fa-envelope-o" aria-hidden="true"></i></a>
        <a href="javascript:window.print()" data-analytics="social"><i class="fa fa-print" aria-hidden="true"></i></a>
      </div>
      <div class="border-bottom-fat"></div>
    </div>
    <div class="post-meta">
      <span class="date-published">
        <?php echo $posted_on_values['time_string']; ?>
      </span>
      <span class="author">
        <a href="<?php echo $posted_on_values['authorURL']; ?>"><?php echo $posted_on_values['author']; ?></a>
      </span>
      <span class="departments">
        <?php echo phila_get_current_department_name( $category, false, false ); ?>
      </span>
    </div>
  </header>
  <?php if ( has_post_thumbnail() ): ?>
    <div class="grid-container featured-image">
      <div class="grid-x medium-16 medium-centered align-middle">
        <?php echo phila_get_thumbnails(); ?>
      </div>
    </div>
  <?php endif ?>
  <div class="grid-container post-content">
    <div class="medium-18 medium-centered">
      <?php the_content(); ?>
    </div>
  </div>
  <hr />
</article>
