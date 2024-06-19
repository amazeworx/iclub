<div class="login-form-container">
  <?php if ($attributes['show_title']) : ?>
    <h2><?php _e('Sign In', 'personalize-login'); ?></h2>
  <?php endif; ?>

  <?php
  // wp_login_form(
  //   array(
  //     'label_username' => __('Email', 'personalize-login'),
  //     'label_log_in' => __('Sign In', 'personalize-login'),
  //     'redirect' => $attributes['redirect'],
  //   )
  // );
  ?>

  <div class="login-form-container">

    <!-- Show errors if there are any -->
    <?php if (count($attributes['errors']) > 0) : ?>
      <?php foreach ($attributes['errors'] as $error) : ?>
        <div class="login-error tw-mb-8 tw-py-4 tw-px-6 tw-rounded-md tw-bg-red-100 tw-border-red-300 tw-text-red-500">
          <?php echo $error; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Show logged out message if user just logged out -->
    <?php if ($attributes['logged_out']) : ?>
      <div class="login-info tw-mb-8 tw-py-3 tw-px-6 tw-rounded-md tw-text-sm tw-bg-amber-100 tw-border-amber-300 tw-text-amber-700">
        <?php _e('You have signed out. Would you like to sign in again?', 'personalize-login'); ?>
      </div>
    <?php endif; ?>

    <?php if ($attributes['lost_password_sent']) : ?>
      <div class="login-info tw-mb-8 tw-py-3 tw-px-6 tw-rounded-md tw-text-sm tw-bg-amber-100 tw-border-amber-300 tw-text-amber-700">
        <?php _e('Check your email for a link to reset your password.', 'personalize-login'); ?>
      </div>
    <?php endif; ?>

    <?php if ($attributes['password_updated']) : ?>
      <div class="login-info tw-mb-8 tw-py-3 tw-px-6 tw-rounded-md tw-text-sm tw-bg-amber-100 tw-border-amber-300 tw-text-amber-700">
        <?php _e('Your password has been changed. You can sign in now.', 'personalize-login'); ?>
      </div>
    <?php endif; ?>

    <form method="post" action="<?php echo wp_login_url(); ?>">
      <p class="login-username">
        <label for="user_login" class="tw-relative tw-block tw-m-0">
          <i class="sl sl-icon-lock tw-text-base tw-absolute tw-top-3 tw-left-4 tw-text-gray-400"></i>
          <input type="text" name="log" id="user_login" placeholder="Email Address" class="!tw-pl-11 !tw-text-base !tw-leading-normal !tw-h-12">
        </label>
      </p>
      <p class="login-password">
        <label for="user_pass" class="tw-relative tw-block tw-m-0">
          <i class="sl sl-icon-lock tw-text-base tw-absolute tw-top-3 tw-left-4 tw-text-gray-400"></i>
          <input type="password" name="pwd" id="user_pass" placeholder="Password" class="!tw-pl-11 !tw-text-base !tw-leading-normal !tw-h-12">
        </label>
      </p>
      <div class="tw-flex tw-gap-x-2 tw-justify-between">
        <div class="login-remember">
          <label class="tw-flex tw-gap-x-2 tw-items-center tw-align-middle tw-mb-0 tw-text-sm md:tw-text-base"><input name="rememberme" type="checkbox" id="rememberme" value="forever" class="!tw-inline-block tw-mb-0 tw-w-4 tw-h-4 tw-leading-normal tw-shadow-none"> Remember Me</label>
        </div>
        <a class="forgot-password tw-text-sm md:tw-text-base" href="<?php echo wp_lostpassword_url(); ?>">
          <?php _e('Forgot your password?', 'personalize-login'); ?>
        </a>
      </div>

      <p class="login-submit tw-mt-4">
        <input type="submit" value="<?php _e('Sign In', 'personalize-login'); ?>" class="!tw-px-12">
      </p>
    </form>
  </div>
</div>