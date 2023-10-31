<?php 
/*
Partial for Advanced Blog Posts Lists Component
*/
?>

<div class = "mvl">
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
    <?php } ?>
</div>