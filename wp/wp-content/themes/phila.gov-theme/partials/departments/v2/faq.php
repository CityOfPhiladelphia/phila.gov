<?php
    $faq = $current_row['phila_full_options']['faq'];
    $accordion_group = $faq['accordion_group'];
?>
<div class="row">
    <div class="small-24 columns results mbm">
        <div id="a-z-filter-list" class="faq-list">
            <div id="a-z-list">
                <!--1/4 Content-->
                <section class="a-z-group">
                    <div class="one-quarter-layout">
                        <div class="row one-quarter-row mvl">
                            <div class="medium-6 columns item">
                                <h3 id="<?php echo sanitize_title_with_dashes($faq['accordion_row_title']) ?>" class="phm-mu mtl mbm"><?php echo $faq['accordion_row_title'] ?></h3>
                            </div>
                            <?php
                                $accordion_title = '';
                                $accordion_group = $faq['accordion_group'];
                                $is_full_width = false;
                                $use_icon = false; 
                            ?>
                            <div class="medium-18 columns pbxl mvl phm-mu list">
                                <?php include(locate_template('partials/global/accordion.php')); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
