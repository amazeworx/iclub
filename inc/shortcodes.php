<?php

// Shortcode to output custom PHP in Elementor
function listeo_featured_badge_function($atts)
{
  global $post;
  $is_featured = listeo_core_is_featured($post->ID);

  $output = '';
  if ($is_featured) {
    $output .= '<div class="listing-small-badge featured-badge"><i class="fa fa-star"></i> Featured</div>';
  }

  return $output;
}
add_shortcode('listeo_featured_badge', 'listeo_featured_badge_function');


function listeo_bookmark_button_function()
{
  global $post;

  $output = '';
  if (listeo_core_check_if_bookmarked($post->ID)) {
    $nonce = wp_create_nonce("listeo_core_bookmark_this_nonce");
    $output .= '<span class="like-icon listeo_core-unbookmark-it liked"
          data-post_id="' . esc_attr($post->ID) . '" 
          data-nonce="' . esc_attr($nonce) . '"></span>';
  } else {
    if (is_user_logged_in()) {
      $nonce = wp_create_nonce("listeo_core_remove_fav_nonce");
      $output .= '<span class="save listeo_core-bookmark-it like-icon" 
              data-post_id="' . esc_attr($post->ID) . '" 
              data-nonce="' . esc_attr($nonce) . '"></span>';
    } else {
      // $output .= '<span class="save like-icon tooltip left"  title="' . esc_html_e('Login To Bookmark Items', 'listeo_core') . '"></span>';
    }
  }

  return $output;
}
add_shortcode('listeo_bookmark_button', 'listeo_bookmark_button_function');

function listeo_registration_form_function()
{
  global $post;
?>
  <div class="sign-in-form">
    <?php
    if (!get_option('users_can_register')) : ?>
      <div class="notification error closeable" style="display: block">
        <p><?php esc_html_e('Registration is disabled', 'listeo_core') ?></p>
      </div>
    <?php else : ?>
      <form enctype="multipart/form-data" class="register listeo-registration-form" id="register" action="<?php echo wp_registration_url(); ?>" method="post">
        <div class="listeo-register-form-fields-container">
          <?php
          $default_role = get_option('listeo_registration_form_default_role', 'guest');
          if (!get_option('listeo_registration_hide_role')) : ?>
            <div class="account-type">
              <div>
                <input type="radio" name="user_role" id="freelancer-radio" value="guest" class="account-type-radio" <?php if ($default_role == 'guest') { ?> checked <?php  } ?> />
                <label for="freelancer-radio"><i class="sl sl-icon-user"></i> <?php esc_html_e('Guest', 'listeo_core') ?></label>
              </div>
              <?php if (class_exists('WeDevs_Dokan')  && get_option('listeo_role_dokan') == 'seller') : ?>
                <div>
                  <input type="radio" name="user_role" id="employer-radio" value="seller" class="account-type-radio" <?php if ($default_role == 'owner') { ?> checked <?php  } ?> />
                  <label for="employer-radio"><i class="sl sl-icon-briefcase"></i> <?php esc_html_e('Owner', 'listeo_core') ?></label>
                </div>
              <?php else : ?>
                <div>
                  <input type="radio" name="user_role" id="employer-radio" value="owner" class="account-type-radio" <?php if ($default_role == 'owner') { ?> checked <?php  } ?> />
                  <label for="employer-radio"><i class="sl sl-icon-briefcase"></i> <?php esc_html_e('Owner', 'listeo_core') ?></label>
                </div>
              <?php endif; ?>
            </div>
            <div class="clearfix"></div>
          <?php endif; ?>
          <?php if (!get_option('listeo_registration_hide_username')) : ?>
            <p class="form-row form-row-wide">
              <label for="username2">
                <i class="sl sl-icon-user"></i>
                <input required placeholder="<?php esc_html_e('Username', 'listeo_core'); ?>" type="text" class="input-text" name="username" id="username2" value="" />
              </label>
            </p>
          <?php endif; ?>

          <p class="form-row form-row-wide">
            <label for="email">
              <i class="sl sl-icon-envelope-open"></i>
              <input required type="email" placeholder="<?php esc_html_e('Email Address', 'listeo_core'); ?>" class="input-text" name="email" id="email" value="" />
            </label>
          </p>

          <?php if (get_option('listeo_display_password_field')) : ?>
            <p class="form-row form-row-wide" id="password-row">
              <label for="password1">
                <i class="sl sl-icon-lock"></i>
                <input required placeholder="<?php esc_html_e('Password', 'listeo_core'); ?>" class="input-text" type="password" name="password" id="password1" />
                <span class="pwstrength_viewport_progress"></span>

              </label>
            </p>
          <?php endif; ?>

          <?php if (get_option('listeo_display_first_last_name')) : ?>
            <p class="form-row form-row-wide">
              <label for="first-name">
                <i class="sl sl-icon-pencil"></i>
                <input <?php if (get_option('listeo_display_first_last_name_required')) { ?>required <?php } ?> placeholder="<?php esc_html_e('First Name', 'listeo_core'); ?>" type="text" name="first_name" id="first-name"></label>
            </p>

            <p class="form-row form-row-wide">
              <label for="last-name">
                <i class="sl sl-icon-pencil"></i>
                <input <?php if (get_option('listeo_display_first_last_name_required')) { ?>required <?php } ?> placeholder="<?php esc_html_e('Last Name', 'listeo_core'); ?>" type="text" name="last_name" id="last-name">
              </label>
            </p>
          <?php endif; ?>

          <!-- //extra fields -->
          <div id="listeo-core-registration-fields">
            <?php echo listeo_get_extra_registration_fields($default_role); ?>
          </div>

          <!-- eof custom fields -->
          <?php if (!get_option('listeo_display_password_field')) : ?>
            <p class="form-row form-row-wide margin-top-30 margin-bottom-30"><?php esc_html_e('Note: Your password will be generated automatically and sent to your email address.', 'listeo_core'); ?>
            </p>
          <?php endif; ?>

          <?php $recaptcha = get_option('listeo_recaptcha');
          $recaptcha_version = get_option('listeo_recaptcha_version', 'v2');
          if ($recaptcha && $recaptcha_version == 'v2') { ?>

            <p class="form-row captcha_wrapper">
            <div class="g-recaptcha" data-sitekey="<?php echo get_option('listeo_recaptcha_sitekey'); ?>"></div>
            </p>
          <?php }

          if ($recaptcha && $recaptcha_version == 'v3') { ?>
            <input type="hidden" id="rc_action" name="rc_action" value="ws_register">
            <input type="hidden" id="token" name="token">
          <?php } ?>

          <?php
          $privacy_policy_status = get_option('listeo_privacy_policy');

          if ($privacy_policy_status && function_exists('the_privacy_policy_link')) { ?>
            <p class="form-row margin-top-10 checkboxes margin-bottom-10">
              <input type="checkbox" id="privacy_policy" name="privacy_policy">
              <label for="privacy_policy"><?php esc_html_e('I agree to the', 'listeo_core'); ?> <a target="_blank" href="<?php echo get_privacy_policy_url(); ?>"><?php esc_html_e('Privacy Policy', 'listeo_core'); ?></a> </label>

            </p>

          <?php } ?>

          <?php
          $terms_and_condition_status = get_option('listeo_terms_and_conditions_req');
          $terms_and_condition_status_page = get_option('listeo_terms_and_conditions_page');

          if ($terms_and_condition_status) { ?>
            <p class="form-row margin-top-10 checkboxes margin-bottom-10">
              <input type="checkbox" id="terms_and_conditions" name="terms_and_conditions">
              <label for="terms_and_conditions"><?php esc_html_e('I agree to the', 'listeo_core'); ?> <a target="_blank" href="<?php echo get_permalink($terms_and_condition_status_page); ?>"><?php esc_html_e('Terms and Conditions', 'listeo_core'); ?></a> </label>

            </p>
          <?php } ?>
        </div>

        <?php wp_nonce_field('listeo-ajax-login-nonce', 'register_security'); ?>

        <?php if (get_option('listeo_otp_status')) { ?>
          <a class="button fw margin-top-10" id="otp_submit" name="otp"><?php esc_html_e('Register', 'listeo_core'); ?></a>
        <?php } else { ?>
          <input type="submit" class="button border fw margin-top-10" name="register" value="<?php esc_html_e('Register', 'listeo_core'); ?>" />
        <?php } ?>

        <div class="notification error closeable" style="display: none;margin-top: 20px; margin-bottom: 0px;">
          <p></p>
        </div>
      </form>

      <div class="listeo-custom-fields-wrapper">
        <?php echo listeo_get_extra_registration_fields('owner'); ?>
        <?php echo listeo_get_extra_registration_fields('guest'); ?>
      </div>
    <?php endif; ?>
  </div>
<?php
}
add_shortcode('listeo_registration_form', 'listeo_registration_form_function');

function listeo_login_form_function()
{
  global $post;
?>
  <div class="sign-in-form">
    <form method="post" id="login" class="login" action="<?php echo wp_login_url(); ?>">
      <?php do_action('listeo_before_login_form'); ?>
      <p class="form-row form-row-wide">
        <label for="user_login">
          <i class="sl sl-icon-user"></i>
          <input placeholder="<?php esc_attr_e('Username/Email', 'listeo_core'); ?>" type="text" class="input-text" name="log" id="user_login" value="" />
        </label>
      </p>
      <p class="form-row form-row-wide">
        <label for="user_pass">
          <i class="sl sl-icon-lock"></i>
          <input placeholder="<?php esc_attr_e('Password', 'listeo_core'); ?>" class="input-text" type="password" name="pwd" id="user_pass" />
        </label>
        <span class="lost_password">
          <a href="<?php echo site_url('/lost-password'); ?>"><?php esc_html_e('Lost Your Password?', 'listeo_core'); ?></a>
        </span>
      </p>
      <div class="form-row">
        <?php wp_nonce_field('listeo-ajax-login-nonce', 'login_security'); ?>
        <input type="submit" class="button border margin-top-5" name="login" value="<?php esc_html_e('Login', 'listeo_core') ?>" />
        <div class="checkboxes margin-top-10">
          <input name="rememberme" type="checkbox" id="remember-me" value="forever" />
          <label for="remember-me"><?php esc_html_e('Remember Me', 'listeo_core'); ?></label>
        </div>
      </div>
      <div class="notification error closeable" style="display: none; margin-top: 20px; margin-bottom: 0px;">
        <p></p>
      </div>
    </form>
  </div>
<?php
}
add_shortcode('listeo_login_form', 'listeo_login_form_function');
