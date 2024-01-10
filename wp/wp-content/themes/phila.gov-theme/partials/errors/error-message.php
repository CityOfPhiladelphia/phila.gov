<?php if (isset($error_messages) && !empty($error_messages) && is_user_logged_in()) { ?>
  <div class="phila-error-background pvm">
    <div class="grid-container">
      <div class="phila-error-card grid-x pas">
        <div class="cell medium-1">
          <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="cell medium-23 phila-error">
          <ul class="phila-error-list">
            <?php foreach ($error_messages as $error) {
              $title = $error['title'];
              $messages = $error['messages'];
              $link = $error['link'];
            ?>
              <div class="phila-error-title"><?php echo $title; ?></div>
              <?php foreach ($messages as $message) { ?>
                <?php if ($link != "") { ?>
                  <a href="<?php echo $link ?>" target="_blank">
                    <?php echo $message ?>
                  </a>
                <?php } else { ?>

                  <li class="phila-error-item">
                  <?php echo $message;
                } ?>
                  </li>
                <?php } ?>
              <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
<?php } ?>