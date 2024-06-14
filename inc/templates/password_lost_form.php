<div id="password-lost-form" class="widecolumn">
  <?php if ($attributes['show_title']) : ?>
    <h3><?php _e('Forgot Your Password?', 'personalize-login'); ?></h3>
  <?php endif; ?>
  <?php if (count($attributes['errors']) > 0) : ?>
    <?php foreach ($attributes['errors'] as $error) : ?>
      <div class="login-error tw-mb-8 tw-py-4 tw-px-6 tw-rounded-md tw-bg-red-100 tw-border-red-300 tw-text-red-500">
        <?php echo $error; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
  <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
    <p class="form-row">
      <label for="user_login" class="tw-hidden"><?php _e('Email', 'personalize-login'); ?></label>
      <input type="text" name="user_login" id="user_login" class="tw-h-12" placeholder="Email address">
    </p>
    <p class="lostpassword-submit">
      <input type="submit" name="submit" class="lostpassword-button !tw-px-12" value="<?php _e('Reset Password', 'personalize-login'); ?>" />
    </p>
  </form>
</div>