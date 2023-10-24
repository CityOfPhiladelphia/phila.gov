<?php
/*
Partial for Advanced Blog Posts Q&A Component
*/

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
?>