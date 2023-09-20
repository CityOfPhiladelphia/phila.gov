<?php if ( isset($error_message_title) && isset($error_messages) && !empty($error_messages) && is_user_logged_in() )  { ?>
  <div class="phila-error-background pvm">
    <div class="grid-container">
      <div class="phila-error-card grid-x pas">
        <div class="cell medium-1">
          <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="cell medium-23 phila-error">
          <div class="phila-error-title"><?php echo $error_message_title; ?></div>
          <ul class="phila-error-list">
            <?php foreach ($error_messages as $message) { ?>
              <li class="phila-error-item">
                <?php if($message['link']) { ?>
                  <a href="<?php echo $message['link'] ?>" target="_blank">
                    <?php echo $message['text'] ?>
                  </a>
                <?php } else { ?>
                  <?php echo $message['text'] ?>
                <?php } ?>
                
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
<?php } ?>