<?php  
//Don't execute this code if we're on the latest
if (is_page_template('templates/the-latest.php') ): 
  return;
endif;

  $translations  = array(
    'post_type' => array('post'),
    'order' => 'desc',
    'cat' => $press_categories,
    'orderby' => 'post_date',
    'meta_query'  => array(
      'relation'  => 'AND',
      array(
        'key' => 'phila_template_select',
        'value' => 'press_release',
        'compare' => '=',
      ),
      array(
        'relation'  => 'OR',
        array(
          'key' => 'phila_select_language',
          'value' => 'english',
          'compare' => '!=',
        ),
        array(
          'key' => 'phila_select_language',
          'value' => '',
          'compare' => '!=',
        ),
      ),
    )
  );

  $is_translated = new WP_Query( $translations );
  $langs = array();

  while ( $is_translated->have_posts() ) : $is_translated->the_post(); 
  $lang = rwmb_meta('phila_select_language', '', $post->ID);
  array_push($langs, $lang);
  endwhile;
  
  $unique_langs = phila_order_languages(array_unique($langs));

  if (!empty($tag)) {
    $term = get_term($tag[0],'post_tag');
  }else{
    $tag = '';
  }
  ?>


<?php if(count($unique_langs) > 1) : ?>
  <div class="translated-headings">
    <h2>Press Releases</h2>
    <ul class="translated-list">
    <?php foreach ($unique_langs as $lang): ?>
        <?php if ($lang === 'english') : 
          $url = '/the-latest/archives/#/?template=press_release&language=english';
          if (!empty($term)) {
            $url .= '&tag=' . $term->name;
          }else if ($slang_name) {
            $url .= '&department=' . $slang_name;
          }else if ($override_url){
            $url .= $override_url + '&langugae=english';
          }
          ?>
          <li><a href="<?php echo $url; ?>">English</a></li>
        <?php endif; ?>
        <?php if ($lang === 'spanish') : 
            $url = '/the-latest/archives/#/?template=press_release&language=spanish';
            if (!empty($term)) {
              $url .= '&tag=' . $term->name;
            }else if ($slang_name) {
              $url .= '&department=' . $slang_name;
            }else if ($override_url){
              $url .= $override_url + '&langugae=spanish';
            }
          ?>
          <li><a href="<?php echo $url; ?>">Español</a></li>
        <?php endif; ?>
        <?php if ($lang === 'chinese') : 
            $url = '/the-latest/archives/#/?template=press_release&language=chinese';
            if (!empty($term)) {
              $url .= '&tag=' . $term->name;
            }else if ($slang_name) {
              $url .= '&department=' . $slang_name;
            }else if ($override_url){
              $url .= $override_url + '&langugae=chinese';
            }
          ?>
          <li><a href="<?php echo $url; ?>">中文</a></li>
        <?php endif; ?>
        <?php if ($lang === 'vietnamese') : 
          $url = '/the-latest/archives/#/?template=press_release&language=vietnamese';
          if (!empty($term)) {
            $url .= '&tag=' . $term->name;
          }else if ($slang_name) {
            $url .= '&department=' . $slang_name;
          }else if ($override_url){
            $url .= $override_url + '&langugae=vietnamese';
          }?>
          <li><a href="<?php echo $url ?>">Tiếng Việt</a></li>
        <?php endif; ?>
        <?php if ($lang === 'russian') : 
          $url = '/the-latest/archives/#/?template=press_release&language=russian';
          if (!empty($term)) {
            $url .= '&tag=' . $term->name;
          }else if ($slang_name) {
            $url .= '&department=' . $slang_name;
          }else if ($override_url){
            $url .= $override_url + '&langugae=russian';
          }?>
          <li><a href="<?php echo $url ?>">Pусский</a></li>
        <?php endif; ?>
        <?php if ($lang === 'french') :  
          $url = '/the-latest/archives/#/?template=press_release&language=french';
          if (!empty($term)) {
            $url .= '&tag=' . $term->name;
          }else if ($slang_name) {
            $url .= '&department=' . $slang_name;
          }else if ($override_url){
            $url .= $override_url + '&langugae=french';
          }?>
          <li><a href="<?php echo $url ?>">Français</a></li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
  </div>
  <?php else: ?>
    <h2>Press Releases</h2>
  <?php endif; ?>  
  <?php wp_reset_postdata();?>
