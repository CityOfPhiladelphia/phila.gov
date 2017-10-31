<?php
/*
 *
 * Partial for rendering Department Row One Content
 *
 */
?>
<?php $user_selected_template = phila_get_selected_template(); ?>

<?php
// set category vars for news/blogs
$category = get_the_category();
$category_slug = $category[0]->slug;


// Set module row vars
$row_one_col_one_module = rwmb_meta( 'module_row_1_col_1' );

if ( !empty( $row_one_col_one_module ) ){
  $row_one_col_one_type = isset( $row_one_col_one_module['phila_module_row_1_col_1_type'] ) ? $row_one_col_one_module['phila_module_row_1_col_1_type'] : '';
  if ( $row_one_col_one_type == 'phila_module_row_1_col_1_blog_posts' || $row_one_col_one_type == 'phila_module_row_1_col_1_news_posts' ){
    $row_one_col_one_post_style = $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_post_style'];
  } else {
    $row_one_col_one_text_title = isset( $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_texttitle'] ) ? $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_texttitle'] : '';
    $row_one_col_one_textarea = isset( $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_textarea'] ) ? $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_textarea'] : '';
 }
}

$row_one_col_two_module = rwmb_meta( 'module_row_1_col_2' );
$row_one_col_two_action_panel = rwmb_meta( 'module_row_1_col_2_call_to_action_panel' );
$row_one_col_two_connect_panel = rwmb_meta( 'module_row_1_col_2_connect_panel' );

if ( !empty( $row_one_col_two_module ) ){
  $row_one_col_two_type = isset( $row_one_col_two_module['phila_module_row_1_col_2_type'] ) ? $row_one_col_two_module['phila_module_row_1_col_2_type'] : '';

  if ( $row_one_col_two_type == 'phila_module_row_1_col_2_blog_posts' ){
    $row_one_col_two_post_style = 'phila_module_row_1_col_2_post_style_cards';
  } elseif( $row_one_col_two_type == 'phila_module_row_1_col_2_custom_text' ) {

    $row_one_col_two_text_title = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_texttitle'];

    $row_one_col_two_textarea = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_textarea'];

  } elseif( $row_one_col_two_type == 'phila_module_row_1_col_2_call_to_action_panel' ) {
    $row_one_col_two_action_panel_title = isset( $row_one_col_two_action_panel['phila_action_section_title'] ) ? $row_one_col_two_action_panel['phila_action_section_title'] : '' ;

    $row_one_col_two_action_panel_summary = isset( $row_one_col_two_action_panel['phila_action_panel_summary'] ) ? $row_one_col_two_action_panel['phila_action_panel_summary'] : '';

    $row_one_col_two_action_panel_cta_text = isset( $row_one_col_two_action_panel['phila_action_panel_cta_text'] ) ? $row_one_col_two_action_panel['phila_action_panel_cta_text'] : '';

    $row_one_col_two_action_panel_link = isset( $row_one_col_two_action_panel['phila_action_panel_link'] ) ? $row_one_col_two_action_panel['phila_action_panel_link'] : '';

    $row_one_col_two_action_panel_link_loc  = isset(  $row_one_col_two_action_panel['phila_action_panel_link_loc'] ) ? $row_one_col_two_action_panel['phila_action_panel_link_loc'] : '';

    $row_one_col_two_action_panel_fa_circle  = isset( $row_one_col_two_action_panel['phila_action_panel_fa_circle'] ) ? $row_one_col_two_action_panel['phila_action_panel_fa_circle'] : '' ;

    $row_one_col_two_action_panel_fa = isset( $row_one_col_two_action_panel['phila_action_panel_fa'] ) ? $row_one_col_two_action_panel['phila_action_panel_fa'] : '';
  }else{
  }
}
?>

<?php if ( !empty( $row_one_col_one_module['phila_module_row_1_col_1_type'] ) && !empty( $row_one_col_two_module['phila_module_row_1_col_2_type'] ) ) :?>
<!-- Begin Row One MetaBox Modules -->
<section class="department-module-row-one mvl">
  <div class="row">
  <?php if ( $row_one_col_one_type  == 'phila_module_row_1_col_1_blog_posts' ): ?>
  <!-- Begin Column One - blog posts -->
    <div class="large-16 columns">
      <div class="row">
      <?php if ($row_one_col_one_post_style == 'phila_module_row_1_col_1_post_style_list'):?>
        <!-- Begin Column One - blog posts - list -->
        <?php echo do_shortcode('[recent-posts list posts="3"]'); ?>
      <?php else: ?>
        <!-- Begin Column One - blog posts - row -->
        <?php get_template_part( 'partials/departments/phila_full_row_blog' ); ?>
      <?php endif;?>
      </div>
    </div>
  <?php elseif ( $row_one_col_one_type  == 'phila_module_row_1_col_1_news_posts' ): ?>
  <!-- Begin Column One - news posts -->
    <div class="large-16 columns">
      <div class="row">
      <?php if ($row_one_col_one_post_style == 'phila_module_row_1_col_1_post_style_list'):?>
        <!-- Begin Column One - news posts - list -->
        <?php echo do_shortcode('[recent-news list posts="3"]'); ?>
      <?php else: ?>
        <!-- Begin Column One - news posts - no list -->
        <?php echo do_shortcode('[recent-news posts="3"]'); ?>
      <?php endif;?>
      </div>
      <div class="row mtm">
        <div class="columns">
          <?php $see_all = array(
              'URL' => '/news/' . $category_slug . '/',
              'content_type' => 'news',
              'nice_name' => 'news'
            );
            include( locate_template( 'partials/content-see-all.php' ) );?>
          </div>
        </div>
    </div>
  <?php elseif ( $row_one_col_one_type  == 'phila_module_row_1_col_1_custom_text' ): ?>
    <!-- Begin Column One - cutsom text -->
    <div class="large-16 columns">
      <h2 class="contrast"><?php echo($row_one_col_one_text_title); ?></h2>
      <div>
        <?php echo apply_filters( 'the_content', $row_one_col_one_textarea ); ?>
      </div>
      <?php if ( $row_one_col_one_textarea == '' ) :?>
        <div class="placeholder">
          Please enter content.
        </div>
      <?php endif; ?>
    </div>
    <!-- End Column One -->
  <?php endif; ?>
  <?php if ( $row_one_col_two_type  == 'phila_module_row_1_col_2_blog_posts' ): ?>
    <!-- Begin Column Two -->
    <div class="large-8 columns">
      <div class="row">
        <?php echo do_shortcode('[recent-posts posts="1"]'); ?>
      </div>
      <div class="row mtm">
        <div class="columns">
          <?php $see_all = array(
          'URL' => '/posts/' . $category_slug . '/',
          'content_type' => 'posts',
          'nice_name' => 'Posts'
        ); ?>
      <?php include( locate_template( 'partials/content-see-all.php' ) );?>
      </div>
    </div>
    </div>
  <?php elseif ( $row_one_col_two_type  == 'phila_module_row_1_col_2_news_posts' ): ?>
    <!-- Begin Column Two -->
    <div class="large-8 columns">
      <div class="row">
        <?php echo do_shortcode('[recent-news posts="1"]'); ?>
      </div>
      <div class="row mtm">
        <div class="columns">
          <?php $see_all = array(
          'URL' => '/news/' . $category_slug . '/',
          'content_type' => 'news',
          'nice_name' => 'News'
        ); ?>
      <?php include( locate_template( 'partials/content-see-all.php' ) );?>
      </div>
    </div>
    </div>
  <?php elseif ( $row_one_col_two_type  == 'phila_module_row_1_col_2_custom_text' ): ?>
    <div class="large-8 columns">
      <h2 class="contrast"><?php echo($row_one_col_two_text_title); ?></h2>
      <div class="panel no-margin">
        <div>
          <?php echo $row_one_col_two_textarea; ?>
        </div>
      </div>
    </div>
  <?php elseif ( $row_one_col_two_type  == 'phila_module_row_1_col_2_call_to_action_panel' ): ?>
    <div class="large-8 columns">
      <h2 class="contrast"><?php echo $row_one_col_two_action_panel_title; ?></h2>
      <?php if (!$row_one_col_two_action_panel_link == ''): ?>
      <a href="<?php echo $row_one_col_two_action_panel_link; ?>"  class="action-panel">
        <div class="panel">
        <header>
        <?php if ($row_one_col_two_action_panel_fa_circle): ?>
          <div>
            <span class="fa-stack fa-4x center" aria-hidden="true">
              <i class="fa fa-circle fa-stack-2x"></i>
              <i class="fa <?php echo $row_one_col_two_action_panel_fa; ?> fa-stack-1x fa-inverse"></i>
            </span>
          </div>
        <?php else: ?>
         <div>
           <span><i class="fa <?php echo $row_one_col_two_action_panel_fa; ?> fa-7x" aria-hidden="true"></i></span>
         </div>
        <?php endif; ?>
        <?php if (!$row_one_col_two_action_panel_cta_text == ''): ?>
          <span class="center <?php if ($row_one_col_two_action_panel_link_loc) echo 'external';?>"><?php echo $row_one_col_two_action_panel_cta_text; ?></span>
        <?php endif; ?>
        </header>
        <hr class="mll mrl">
          <span class="details"><?php echo $row_one_col_two_action_panel_summary; ?></span>
        </div>
      </a>
      <?php endif; ?>
    </div>
    <!-- End Column Two -->
  <?php elseif ( $row_one_col_two_type  == 'phila_module_row_1_col_2_connect_panel' ):
    ?>

    <?php if ($user_selected_template == 'homepage_v2') : ?>
      <?php get_template_part( 'partials/departments/v2/content', 'connect' ); ?>
    <?php else: ?>
      <?php
      $connect_panel = rwmb_meta('module_row_1_col_2_connect_panel');
      $connect_vars = phila_connect_panel($connect_panel);
      include(locate_template('partials/departments/content-connect.php'));
      ?>
    <?php endif; ?>

<?php endif; ?>
  </div>
</section>
<!-- End Row One MetaBox Modules -->
<?php endif; ?>
