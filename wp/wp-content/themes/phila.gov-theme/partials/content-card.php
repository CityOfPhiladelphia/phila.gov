<?php
/**
 * The template used for displaying a card, containing a title and link
 *
 * @package phila-gov
 */
?>
<?php $categories = get_the_category($post->ID); ?>
<?php $desc = phila_get_item_meta_desc( ); ?>

<?php
if ( isset( $label ) ) :
  switch( $label ) {
    case 'press-release':
      $label_nice = 'Press Release';
      break;
    case 'featured':
      $label_nice = 'Featured';
      break;
  }
  endif;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <a href="<?php echo the_permalink(); ?>" class="card card--<?php echo $label ?> pam">
    <div class="post-label post-label--<?php echo $label?>">
      <i class="fa fa-file-text-o" aria-hidden="true"></i> <span><?php echo $label_nice; ?></span>
    </div>
      <header class="mbm">
        <h1><?php echo get_the_title(); ?></h1>
      </header>
    <div class="align-self-bottom">
      <div class="post-meta">
        <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
        <span class="departments"><?php echo phila_get_current_department_name( $categories, $byline = false, $break_tags = false, $name_list = true ); ?></span>
      </div>
    </div>
  </a>
</article>
