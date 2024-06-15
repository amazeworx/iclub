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
  // Retrieve possible errors from request parameters 
  $attributes['errors'] = array();
  if (isset($_REQUEST['register-errors'])) {
    $error_codes = explode(',', $_REQUEST['register-errors']);
    foreach ($error_codes as $error_code) {
      $attributes['errors'][] = iclub_get_error_message($error_code);
    }
  }
?>
  <div class="sign-in-form">
    <?php
    if (!get_option('users_can_register')) : ?>
      <div class="notification error closeable" style="display: block">
        <p><?php esc_html_e('Registration is disabled', 'listeo_core') ?></p>
      </div>
    <?php else : ?>
      <form enctype="multipart/form-data" class="register listeo-registration-form" id="register" action="<?php echo wp_registration_url(); ?>" method="post">
        <?php if (count($attributes['errors']) > 0) : ?>
          <?php foreach ($attributes['errors'] as $error) : ?>
            <p>
              <?php echo $error; ?>
            </p>
          <?php endforeach; ?>
        <?php endif; ?>

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

function listeo_login_form_function($attributes)
{
  global $post;

  // Error messages 
  $errors = array();
  if (isset($_REQUEST['login'])) {
    $error_codes = explode(',', $_REQUEST['login']);
    foreach ($error_codes as $code) {
      $errors[] = iclub_get_error_message($code);
    }
  }
  $attributes['errors'] = $errors;

  // Check if user just logged out 
  $attributes['logged_out'] = isset($_REQUEST['logged_out']) && $_REQUEST['logged_out'] == true;
  // Check if the user just registered 
  $attributes['registered'] = isset($_REQUEST['registered']);
  // Check if the user just requested a new password 
  $attributes['lost_password_sent'] = isset($_REQUEST['checkemail']) && $_REQUEST['checkemail'] == 'confirm';
  // Check if user just updated password 
  $attributes['password_updated'] = isset($_REQUEST['password']) && $_REQUEST['password'] == 'changed';
?>
  <div class="sign-in-form">
    <form method="post" id="login" class="login" action="<?php echo wp_login_url(); ?>">
      <?php do_action('listeo_before_login_form'); ?>
      <!-- Show errors if there are any -->
      <?php if (count($attributes['errors']) > 0) : ?>
        <?php foreach ($attributes['errors'] as $error) : ?>
          <p class="login-error">
            <?php echo $error; ?>
          </p>
        <?php endforeach; ?>
      <?php endif; ?>
      <!-- Show logged out message if user just logged out -->
      <?php if ($attributes['logged_out']) : ?>
        <p class="login-info">
          <?php _e('You have signed out. Would you like to sign in again?', 'personalize-login'); ?>
        </p>
      <?php endif; ?>
      <?php if ($attributes['registered']) : ?>
        <p class="login-info">
          <?php
          printf(
            __('You have successfully registered to <strong>%s</strong>. We have emailed your password to the email address you entered.', 'personalize-login'),
            get_bloginfo('name')
          );
          ?>
        </p>
      <?php endif; ?>
      <?php if ($attributes['lost_password_sent']) : ?>
        <p class="login-info">
          <?php _e('Check your email for a link to reset your password.', 'iclub'); ?>
        </p>
      <?php endif; ?>
      <?php if ($attributes['password_updated']) : ?>
        <p class="login-info">
          <?php _e('Your password has been changed. You can sign in now.', 'personalize-login'); ?>
        </p>
      <?php endif; ?>

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

function iclub_login_form_function($attributes)
{
  global $post;

  // Error messages 
  $errors = array();
  if (isset($_REQUEST['login'])) {
    $error_codes = explode(',', $_REQUEST['login']);
    foreach ($error_codes as $code) {
      $errors[] = iclub_get_error_message($code);
    }
  }
  $attributes['errors'] = $errors;

  // Check if user just logged out 
  $attributes['logged_out'] = isset($_REQUEST['logged_out']) && $_REQUEST['logged_out'] == true;
  // Check if the user just registered 
  $attributes['registered'] = isset($_REQUEST['registered']);
  // Check if the user just requested a new password 
  $attributes['lost_password_sent'] = isset($_REQUEST['checkemail']) && $_REQUEST['checkemail'] == 'confirm';
  // Check if user just updated password 
  $attributes['password_updated'] = isset($_REQUEST['password']) && $_REQUEST['password'] == 'changed';
?>
  <div class="sign-in-form">
    <form method="post" id="login" class="login" action="<?php echo wp_login_url(); ?>">
      <?php do_action('listeo_before_login_form'); ?>
      <!-- Show errors if there are any -->
      <?php if (count($attributes['errors']) > 0) : ?>
        <?php foreach ($attributes['errors'] as $error) : ?>
          <p class="login-error">
            <?php echo $error; ?>
          </p>
        <?php endforeach; ?>
      <?php endif; ?>
      <!-- Show logged out message if user just logged out -->
      <?php if ($attributes['logged_out']) : ?>
        <p class="login-info">
          <?php _e('You have signed out. Would you like to sign in again?', 'personalize-login'); ?>
        </p>
      <?php endif; ?>
      <?php if ($attributes['registered']) : ?>
        <p class="login-info">
          <?php
          printf(
            __('You have successfully registered to <strong>%s</strong>. We have emailed your password to the email address you entered.', 'personalize-login'),
            get_bloginfo('name')
          );
          ?>
        </p>
      <?php endif; ?>
      <?php if ($attributes['lost_password_sent']) : ?>
        <p class="login-info">
          <?php _e('Check your email for a link to reset your password.', 'iclub'); ?>
        </p>
      <?php endif; ?>
      <?php if ($attributes['password_updated']) : ?>
        <p class="login-info">
          <?php _e('Your password has been changed. You can sign in now.', 'personalize-login'); ?>
        </p>
      <?php endif; ?>

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
//add_shortcode('iclub_login_form', 'iclub_login_form_function');

/** 
 * A shortcode for rendering the login form. 
 * 
 * @param array $attributes Shortcode attributes. 
 * @param string $content The text content for shortcode. Not used. 
 * 
 * @return string The shortcode output 
 */
function iclub_render_login_form($attributes, $content = null)
{
  // Parse shortcode attributes 
  $default_attributes = array('show_title' => false);
  $attributes = shortcode_atts($default_attributes, $attributes);
  $show_title = $attributes['show_title'];

  // Error messages 
  $errors = array();
  if (isset($_REQUEST['login'])) {
    $error_codes = explode(',', $_REQUEST['login']);
    foreach ($error_codes as $code) {
      $errors[] = iclub_get_error_message($code);
    }
  }
  $attributes['errors'] = $errors;

  // Check if user just logged out 
  $attributes['logged_out'] = isset($_REQUEST['logged_out']) && $_REQUEST['logged_out'] == true;

  // Check if the user just requested a new password 
  $attributes['lost_password_sent'] = isset($_REQUEST['checkemail']) && $_REQUEST['checkemail'] == 'confirm';

  // Check if user just updated password 
  $attributes['password_updated'] = isset($_REQUEST['password']) && $_REQUEST['password'] == 'changed';

  if (!is_admin()) {
    if (is_user_logged_in()) {
      return __('You are already signed in.', 'personalize-login');
    }
  }

  // Pass the redirect parameter to the WordPress login functionality: by default, 
  // don't specify a redirect, but if a valid redirect URL has been passed as 
  // request parameter, use it. 
  $attributes['redirect'] = '';
  if (isset($_REQUEST['redirect_to'])) {
    $attributes['redirect'] = wp_validate_redirect($_REQUEST['redirect_to'], $attributes['redirect']);
  }

  // Render the login form using an external template 
  return iclub_get_template_html('login_form', $attributes);
}
add_shortcode('iclub_login_form', 'iclub_render_login_form');

/** 
 * A shortcode for rendering the new user registration form. 
 * 
 * @param array $attributes Shortcode attributes. 
 * @param string $content The text content for shortcode. Not used. 
 * 
 * @return string The shortcode output 
 */
function iclub_render_register_form($attributes, $content = null)
{
  // Parse shortcode attributes 
  $default_attributes = array('show_title' => false);
  $attributes = shortcode_atts($default_attributes, $attributes);

  if (is_admin()) {
    return iclub_get_template_html('register_form', $attributes);
  } else {
    if (is_user_logged_in()) {
      return __('You are already signed in.', 'personalize-login');
    } elseif (!get_option('users_can_register')) {
      return __('Registering new users is currently not allowed.', 'personalize-login');
    } else {
      return iclub_get_template_html('register_form', $attributes);
    }
  }
}
add_shortcode('iclub_register_form', 'iclub_render_register_form');

/** 
 * Renders the contents of the given template to a string and returns it. 
 * 
 * @param string $template_name The name of the template to render (without .php) 
 * @param array $attributes The PHP variables for the template 
 * 
 * @return string The contents of the template. 
 */
function iclub_get_template_html($template_name, $attributes = null)
{
  if (!$attributes) {
    $attributes = array();
  }
  ob_start();
  //do_action( 'personalize_login_before_' . $template_name );
  require('templates/' . $template_name . '.php');
  //do_action( 'personalize_login_after_' . $template_name );
  $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

/** 
 * A shortcode for rendering the form used to initiate the password reset. 
 * 
 * @param array $attributes Shortcode attributes. 
 * @param string $content The text content for shortcode. Not used. 
 * 
 * @return string The shortcode output 
 */
function iclub_render_password_lost_form($attributes, $content = null)
{
  // Parse shortcode attributes 
  $default_attributes = array('show_title' => false);
  $attributes = shortcode_atts($default_attributes, $attributes);

  // Retrieve possible errors from request parameters 
  $attributes['errors'] = array();
  if (isset($_REQUEST['errors'])) {
    $error_codes = explode(',', $_REQUEST['errors']);
    foreach ($error_codes as $error_code) {
      $attributes['errors'][] = iclub_get_error_message($error_code);
    }
  }

  if (!is_admin() && is_user_logged_in()) {
    return __('You are already signed in.', 'personalize-login');
  } else {
    return iclub_get_template_html('password_lost_form', $attributes);
  }
}
add_shortcode('iclub_lost_password', 'iclub_render_password_lost_form');

/** 
 * A shortcode for rendering the form used to reset a user's password. 
 * 
 * @param array $attributes Shortcode attributes. 
 * @param string $content The text content for shortcode. Not used. 
 * 
 * @return string The shortcode output 
 */
function iclub_render_password_reset_form($attributes, $content = null)
{
  // Parse shortcode attributes 
  $default_attributes = array('show_title' => false);
  $attributes = shortcode_atts($default_attributes, $attributes);
  if (!is_admin() && is_user_logged_in()) {
    return __('You are already signed in.', 'personalize-login');
  } else {
    if (!is_admin() && isset($_REQUEST['login']) && isset($_REQUEST['key'])) {
      $attributes['login'] = $_REQUEST['login'];
      $attributes['key'] = $_REQUEST['key'];
      // Error messages 
      $errors = array();
      if (isset($_REQUEST['error'])) {
        $error_codes = explode(',', $_REQUEST['error']);
        foreach ($error_codes as $code) {
          $errors[] = iclub_get_error_message($code);
        }
      }
      $attributes['errors'] = $errors;
      return iclub_get_template_html('password_reset_form', $attributes);
    } else {
      return __('Invalid password reset link.', 'personalize-login');
    }
  }
}
add_shortcode('iclub_reset_password', 'iclub_render_password_reset_form');
