<?php $page_rows = rwmb_meta('phila_row');

foreach ($page_rows as $page_row) {
  if ($page_row['phila_adv_posts_options']['phila_adv_posts_select_options'] == 'phila_lists') {
    $list = $page_row['phila_adv_posts_options']['phila_adv_lists']; ?>
    <?php if ($list['phila_list_type'] == "unordered") { ?>
      <<?php echo $list['phila_list_title_style'] ?>><?php echo $list['phila_list_builder_title']; ?></<?php echo $list['phila_list_title_style'] ?>>
      <ul style="padding-left: 0">
        <?php foreach ($list['phila_unordered_list_fields'] as $item) { ?>
          <li><?php echo $item["phila_unordered_list_item"] ?></li>
        <?php } ?>
      </ul>

    <?php } elseif ($list['phila_list_type'] == "ordered") { ?>
      <<?php echo $list['phila_list_title_style'] ?>><?php echo $list['phila_list_builder_title']; ?></<?php echo $list['phila_list_title_style'] ?>>
      <ol style="padding-left: 0">
        <?php foreach ($list['phila_ordered_list_fields'] as $item) { ?>
          <li><?php echo $item["phila_ordered_list_item"] ?></li>
        <?php } ?>
      </ol>
    <?php } elseif ($list['phila_list_type'] == "unordered_with_paragraph") { ?>
      <<?php echo $list['phila_list_title_style'] ?>><?php echo $list['phila_list_builder_title']; ?></<?php echo $list['phila_list_title_style'] ?>>
      <ul style="padding-left: 0">
        <?php foreach ($list['phila_unordered_with_paragraph_fields'] as $item) { ?>

          <li style="font-weight: bold;"><?php echo $item["phila_unordered_list_item"] ?></li>

          <p><?php echo $item["phila_paragraph"] ?></p>
        <?php } ?>
      </ul>
    <?php } elseif ($list['phila_list_type'] == "ordered_with_paragraph") { ?>
      <<?php echo $list['phila_list_title_style'] ?>><?php echo $list['phila_list_builder_title']; ?></<?php echo $list['phila_list_title_style'] ?>>
      <ol style="padding-left: 0">
        <?php foreach ($list['phila_ordered_with_paragraph_fields'] as $item) { ?>
          <li style="font-weight: bold;"><?php echo $item["phila_ordered_list_item"] ?></li>
          <p><?php echo $item["phila_paragraph"] ?></p>
        <?php } ?>
      </ol>
    <?php } elseif ($list['phila_list_type'] == "check_list") { ?>
      <<?php echo $list['phila_list_title_style'] ?>><?php echo $list['phila_list_builder_title']; ?></<?php echo $list['phila_list_title_style'] ?>>
      <ul style="list-style: none; padding-left: 0">
        <?php foreach ($list['phila_check_list_fields'] as $item) { ?>
          <li><i style="padding-right: 8px;" class="<?php echo $list['phila_icon_fields']['phila_check_list_icon'] ?? "fas fa-check" ?>"></i><?php echo $item['phila_check_list_item'] ?></li>
        <?php } ?>
      </ul>
    <?php }
  } elseif ($page_row['phila_adv_posts_options']['phila_adv_posts_select_options'] == 'phila_qna') {
    $qna = $page_row['phila_adv_posts_options']['phila_adv_qna'];
    if ($qna['phila_qna_title'] != "") {
    ?>
      <h2><?php echo $qna['phila_qna_title'] ?></h2>
      <?php }
    if ($qna['phila_qna_style'] == 'name') {
      foreach ($qna['phila_qna_person_repeater'] as $qa) { ?>
        <strong>
          <p><?php echo $qa['phila_qna_question_person'] ?> (Name): <?php echo $qa['phila_qna_question'] ?></p>
        </strong>
        <p><strong><?php echo $qa['phila_qna_answer_person'] ?> (Name): </strong><?php echo $qa['phila_qna_answer'] ?></p>
      <?php }
    } elseif ($qna['phila_qna_style'] == 'qa') {
      foreach ($qna['phila_qna_repeater'] as $qa) { ?>
        <strong>
          <p>Q: <?php echo $qa['phila_qna_question'] ?></p>
        </strong>
        <p><strong>A: </strong><?php echo $qa['phila_qna_answer'] ?></p>
<?php
      }
    }
  } elseif($page_row['phila_adv_posts_options']['phila_adv_posts_select_options'] == 'phila_timeline'){
    $timeline_page = $page_row['phila_adv_posts_options']['phila_adv_timeline'];
    include(locate_template('partials/timeline_stub.php')); 
  }
} ?>