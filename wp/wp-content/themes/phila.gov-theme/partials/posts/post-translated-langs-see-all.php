<?php
//Don't execute this code if we're on the latest
if (is_page_template('templates/the-latest.php') ):
  return;
endif;
if (isset( $tag )) {
  $translations  = array(
    'post_type' => array('post'),
    'order' => 'desc',
    'cat' => $post_categories,
    'tag__in' => array( $tag ),
    'posts_per_page'  => -1,
    'orderby' => 'post_date',
    'meta_query'  => array(
      'relation'  => 'AND',
      array(
        'key' => 'phila_template_select',
        'value' => 'press_release',
        'compare' => '!=',
      ),
      array(
        'relation'  => 'OR',
        array(
          'key' => 'phila_select_language',
          'value' => 'english',
          'compare' => '!=',
        ),
      ),
    )
  );
} else {
  $translations  = array(
    'post_type' => array('post'),
    'order' => 'desc',
    'cat' => $post_categories,
    'posts_per_page'  => -1,
    'orderby' => 'post_date',
    'meta_query'  => array(
      'relation'  => 'AND',
      array(
        'key' => 'phila_template_select',
        'value' => 'press_release',
        'compare' => '!=',
      ),
      array(
        'relation'  => 'OR',
        array(
          'key' => 'phila_select_language',
          'value' => 'english',
          'compare' => '!=',
        ),
      ),
    )
  );
}

  $is_translated = new WP_Query( $translations );
  $langs = array();

  while ( $is_translated->have_posts() ) : $is_translated->the_post();
  $lang = rwmb_meta('phila_select_language', '', $post->ID);
  $langs[$lang] = $lang;
  endwhile;

  $unique_langs = phila_order_languages(array_unique($langs));

  if (!empty($tag)) {
    $term = get_term($tag[0],'post_tag');
    if ($term == NULL) {
      $term = get_term($tag,'post_tag');
    }
  }else{
    $tag = '';
  }
  ?>


<?php if(count($unique_langs) > 1) : ?>
  <div class="translated-headings">
    <h2 id="posts">Posts</h2>
    <ul class="translated-list">
    <?php foreach ($unique_langs as $lang): ?>
        <?php if ($lang === 'english') :
          $url = '/the-latest/archives/?templates=post&language=english';
          if (!empty($term)) {
            $url .= '&tag=' . $term->name;
          }else if ($slang_name) {
            $url .= '&department=' . $slang_name;
          }else if ($override_url){
            $url .= $override_url + '&language=english';
          }
          ?>
          <li><a href="<?php echo $url; ?>">English</a></li>
        <?php endif; ?>
        <?php if ($lang === 'spanish') :
            $url = '/the-latest/archives/?templates=post&language=spanish';
            if (!empty($term)) {
              $url .= '&tag=' . $term->name;
            }else if ($slang_name) {
              $url .= '&department=' . $slang_name;
            }else if ($override_url){
              $url .= $override_url + '&language=spanish';
            }
          ?>
          <li><a href="<?php echo $url; ?>">Español</a></li>
        <?php endif; ?>
        <?php if ($lang === 'chinese') :
            $url = '/the-latest/archives/?templates=post&language=chinese';
            if (!empty($term)) {
              $url .= '&tag=' . $term->name;
            }else if ($slang_name) {
              $url .= '&department=' . $slang_name;
            }else if ($override_url){
              $url .= $override_url + '&language=chinese';
            }
          ?>
          <li><a href="<?php echo $url; ?>">中文</a></li>
        <?php endif; ?>
        <?php if ($lang === 'vietnamese') :
          $url = '/the-latest/archives/?templates=post&language=vietnamese';
          if (!empty($term)) {
            $url .= '&tag=' . $term->name;
          }else if ($slang_name) {
            $url .= '&department=' . $slang_name;
          }else if ($override_url){
            $url .= $override_url + '&language=vietnamese';
          }?>
          <li><a href="<?php echo $url ?>">Tiếng Việt</a></li>
        <?php endif; ?>
        <?php if ($lang === 'russian') :
          $url = '/the-latest/archives/?templates=post&language=russian';
          if (!empty($term)) {
            $url .= '&tag=' . $term->name;
          }else if ($slang_name) {
            $url .= '&department=' . $slang_name;
          }else if ($override_url){
            $url .= $override_url + '&language=russian';
          }?>
          <li><a href="<?php echo $url ?>">Pусский</a></li>
        <?php endif; ?>
        <?php if ($lang === 'french') :
          $url = '/the-latest/archives/?templates=post&language=french';
          if (!empty($term)) {
            $url .= '&tag=' . $term->name;
          }else if ($slang_name) {
            $url .= '&department=' . $slang_name;
          }else if ($override_url){
            $url .= $override_url + '&language=french';
          }?>
          <li><a href="<?php echo $url ?>">Français</a></li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
  </div>
  <?php else: ?>
    <h2 id="posts">Posts</h2>
  <?php endif; ?>
  <?php wp_reset_postdata();?>
