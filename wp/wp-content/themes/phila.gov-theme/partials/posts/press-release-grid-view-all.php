<?php if ($count == 4) : ?>
  <?php if ($user_selected_template == 'custom_content'): ?>
    <?php $see_all = array(
    'URL' => '/the-latest/archives/#/?templates=press_release&department=' . $slang_name,
    'content_type' => 'press_release',
    'nice_name' => 'Press releases',
    'is_full' => false
  ); ?>
  <?php else: ?>
    <?php $see_all = array(
    'URL' => '/the-latest/archives/#/?templates=press_release&department=' . $slang_name,
    'content_type' => 'press_release',
    'nice_name' => 'Press releases',
    'is_full' => true
  ); ?>
  <?php endif; ?>
  <?php if( !empty( $tag ) ) :
    if (gettype($tag) === 'string' ) {
      $term = get_term($tag, 'post_tag');
    }else{
      $term = get_term($tag[0], 'post_tag');
    }
    $see_all_URL = array(
      'URL' => '/the-latest/archives/#/?tag=' . $term->name,
    );
    $see_all = array_replace($see_all, $see_all_URL );
    endif;?>
<?php if (!empty($override_url)) : ?>
<?php $see_all_URL = array(
    'URL' => $override_url
  ); ?>
<?php endif; ?>
  <?php if ($user_selected_template == 'custom_content'): ?>
  <div class="custom">
    <?php include( locate_template( 'partials/custom-content-see-all.php' ) ); ?>
  </div>
  <?php else: ?>
    <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
  <?php endif; ?>
<?php endif;?>