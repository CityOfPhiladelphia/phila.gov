<?php if( $this->link_url): ?>
<div class="cell large-7 medium-7 small-18 ppf-signature-event__card">
    <div class="columns">
        <a href="<?= $this->link_url ?>" target="_blank">
            <?php if($this->photo_url): ?>
                <img src="<?= $this->photo_url ?>" alt="">
            <?php endif; ?>
            <h3><?= $this->title ?></h3>
        </a>
    </div>
</div>
<?php endif; ?>
