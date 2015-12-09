<?php
/*
* Template for displaying document pages
*/
?>
<header class="entry-header small-24 columns">
  <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
</header><!-- .entry-header -->
<div data-swiftype-index='true' class="entry-content small-24 medium-18 columns">
  <?php
  $document_description = rwmb_meta( 'phila_document_description', $args = array('type' => 'textarea'));
  echo '<p class="description">' . $document_description . '</p>';
  $documents = rwmb_meta( 'phila_files', $args = array('type' => 'file_advanced'));
  ?>
  <table class="no-borders">
    <thead class="light-head">
      <tr>
        <th>
          <?php echo __('<h3 class="alternate">Name</h3>'); ?>
        </th>
        <th>
          <?php echo __('<h3 class="alternate">Description</h3>'); ?>
        </th>
        <th>
          <?php echo __('<h3 class="alternate">Format</h3>'); ?>
        </th>
        <th>
        </th>
      </tr>
    </thead>
    <tbody>
<?php
  foreach ($documents as $document): ?>
    <tr class="clickable-row" data-href="<?php echo $document['url'] ?>">
      <td>
        <h3 class="h4 document-title"><a><?php echo $document['title']; ?></a>
        </h2>
      </td>
        <?php
        $id = phila_get_attachment_id_by_url($document['url']);
        $attachment_data = wp_prepare_attachment_for_js( $id[0] );
        $file_type = $attachment_data['subtype'];
        $content = $attachment_data['description'];
        ?><td><?php
        if ($content) {
          echo '<span class="small-text">' . $content . '</span>';
        }
        ?></td>
        <td><?php
        if ($file_type) {
          echo '<span class="small-text file-type">';
          phila_format_document_type($file_type);
          echo '</span>';
        }
        ?></td>
        <td><i class="fa fa-download"></i></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div><!-- .entry-content -->
<aside id="secondary" class="small-24 medium-6 columns" role="complementary">
  <?php
    if (function_exists('rwmb_meta')) {
      $document_published = rwmb_meta( 'phila_document_released', $args = array('type' => 'date'));
      echo __('<h3 class="alternate">Published</h3>');
      echo '<span class="small-text">' . $document_published . '</span>';
    }
    /* A link pointing to the category in which this content lives. We are looking at dpartment pages specifically, so a department link will not appear unless that department is associated with the category in question.  */
    $current_category = get_the_category();
    if ( !$current_category == '' ) :
      $department_page_args = array(
        'post_type' => 'department_page',
        'tax_query' => array(
          array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => $current_category[0]->slug,
          ),
        ),
        'post_parent' => 0,
        'posts_per_page' => 1,
      );
      $get_department_link = new WP_Query( $department_page_args );
      if ( $get_department_link->have_posts() ) :
        while ( $get_department_link->have_posts() ) :
          $get_department_link->the_post();
          $current_cat_slug = $current_category[0]->slug;
          //we are rendering the depatrtment link elsewhere on document pages.
          if ( $current_cat_slug != 'uncategorized' ) {
            // NOTE: the id and data-slug are important. Google Tag Manager
            // uses it to attach the department to our web analytics.
            echo __('<h3 class="alternate">From</h3>');
            echo '<span class="small-text"><a href="' . get_the_permalink() . '" id="content-modified-department"
                  data-slug="' . $current_cat_slug . '">' . get_the_title() . '</a></span>';
          }
        endwhile;
      endif;
    endif;
    wp_reset_postdata();
  $doc_terms = get_the_terms($post->ID, 'document_topics');
  if ( !$doc_terms == '' ){
    echo __('<h3 class="alternate">Topic</h3>');
    echo '<div class="small-text doc-type">';
    foreach ($doc_terms as $doc){
      echo '<span><a href="/search/#stq=' . $doc->name .'">' . $doc->name . '</a></span>';
    }
    echo '</div>';
  }
  ?>
</aside>
