<div id="sb-list" class="postbox" style="max-width: 800px; margin-top: 20px;">
  <h2 class="hndle"><?php _e( 'デモ', THEME_NAME ) ?></h2>

  <div class="inside balloon-content">
    <div class="demo">
    <?php
      generate_speech_balloon_tag(
        $record,
        'VOICE'
      );
    ?>
    </div>

  </div>

  <?php if (0 && $record->credit): ?>
  <div class="baloon-credit">
    <?php echo $record->credit; ?>
  </div>
  <?php endif ?>
</div>