<?php  
//Don't execute this code if we're on the latest
if (is_page_template('templates/the-latest.php') ): 
  return;
endif;

  $translations  = array(
    'post_type' => array('post'),
    'order' => 'desc',
    'cat' => $post_categories,
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
  $unique_langs = array_unique($langs);

  $term = get_term($tag[0],'post_tag');
  var_dump($term);

  /// TODO:
  //  links when term is set.
  //  links when see all URL is present
  ?>


<?php if(count($unique_langs) > 1) :?>
  <div class="translated-headings">
    <h2>Posts</h2>
    <ul class="translated-list">
    <?php foreach ($unique_langs as $lang): ?>
        <?php if ($lang === 'english') : ?>
        <li><a href="/the-latest/archives/#/?template=posts&language=english&department=<?php echo $slang_name ?>">English</a></li>
        <?php endif; ?>
        <?php if ($lang === 'spanish') : ?>
          <li><a href="/the-latest/archives/#/?template=posts&language=spanish&department=<?php echo $slang_name ?>">Español</a></li>
        <?php endif; ?>
        <?php if ($lang === 'chinese') : ?>
          <li><a href="/the-latest/archives/#/?template=posts&language=chinese&department=<?php echo $slang_name ?>">中文</a></li>
        <?php endif; ?>
        <?php if ($lang === 'vietnamese') : ?>
          <li><a href="/the-latest/archives/#/?template=posts&language=vietnamese&department=<?php echo $slang_name ?>">Tiếng Việt</a></li>
        <?php endif; ?>
        <?php if ($lang === 'russian') : ?>
          <li><a href="/the-latest/archives/#/?template=posts&language=russian&department=<?php echo $slang_name ?>">Pусский</a></li>
        <?php endif; ?>
        <?php if ($lang === 'french') : ?>
          <li><a href="/the-latest/archives/#/?template=posts&language=french&department=<?php echo $slang_name ?>">Français</a></li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
  </div>
  <?php else: ?>
    <h2>Posts</h2>
  <?php endif; ?>  
  <?php wp_reset_postdata();?>
