<div id="password-reset-form" class="widecolumn">
  <?php if ($attributes['show_title']) : ?>
    <h3><?php _e('Pick a New Password', 'iclub'); ?></h3>
  <?php endif; ?>
  <div class="notification notice closeable margin-top-0 margin-bottom-20">
    <p class="!tw-leading-tight"><?php esc_html_e('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.', 'listeo_core') ?></p><a class="close" href="#"></a>
  </div>

  <form name="resetpassform" id="resetpassform" action="<?php echo site_url('wp-login.php?action=resetpass'); ?>" method="post" autocomplete="off">
    <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr($attributes['login']); ?>" autocomplete="off" />
    <input type="hidden" name="rp_key" value="<?php echo esc_attr($attributes['key']); ?>" />

    <?php if ($attributes['errors']) : ?>
      <?php if (count($attributes['errors']) > 0) : ?>
        <?php foreach ($attributes['errors'] as $error) : ?>
          <p>
            <?php echo $error; ?>
          </p>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endif; ?>
    <p>
      <label for="pass1"><?php _e('New password', 'iclub') ?></label>
      <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
    </p>
    <p>
      <label for="pass2"><?php _e('Repeat new password', 'iclub') ?></label>
      <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
    </p>

    <!-- <p class="description"><?php echo wp_get_password_hint(); ?></p> -->

    <p class="resetpass-submit">
      <input type="submit" name="submit" id="resetpass-button" class="button" value="<?php _e('Reset Password', 'iclub'); ?>" />
    </p>
  </form>
</div>