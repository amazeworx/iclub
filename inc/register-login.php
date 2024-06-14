<?php

/** 
 * Redirects the user to the correct page depending on whether he / she 
 * is an admin or not. 
 * 
 * @param string $redirect_to An optional redirect_to URL for admin users 
 */
function redirect_logged_in_user($redirect_to = null)
{
  $user = wp_get_current_user();
  if (user_can($user, 'manage_options')) {
    if ($redirect_to) {
      wp_safe_redirect($redirect_to);
    } else {
      wp_redirect(admin_url());
    }
  } else {
    wp_redirect(home_url('dashboard'));
  }
}

/** 
 * Finds and returns a matching error message for the given error code. 
 * 
 * @param string $error_code The error code to look up. 
 * 
 * @return string An error message. 
 */
function get_error_message($error_code)
{
  switch ($error_code) {
    case 'empty_username':
      return __('You do have an email address, right?', 'personalize-login');
    case 'empty_password':
      return __('You need to enter a password to login.', 'personalize-login');
    case 'invalid_username':
      return __(
        "We don't have any users with that email address. Maybe you used a different one when signing up?",
        'personalize-login'
      );
    case 'incorrect_password':
      $err = __(
        "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
        'personalize-login'
      );
      return sprintf($err, wp_lostpassword_url());
      // Registration errors 
    case 'email':
      return __('The email address you entered is not valid.', 'personalize-login');
    case 'email_exists':
      return __('An account exists with this email address.', 'personalize-login');
    case 'closed':
      return __('Registering new users is currently not allowed.', 'personalize-login');
    default:
      break;
  }

  return __('An unknown error occurred. Please try again later.', 'personalize-login');
}

/** 
 * Redirect the user to the custom login page instead of wp-login.php. 
 */
function redirect_to_custom_login()
{
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : null;

    if (is_user_logged_in()) {
      redirect_logged_in_user($redirect_to);
      exit;
    }
    // The rest are redirected to the login page 
    $login_url = home_url('login');
    if (!empty($redirect_to)) {
      $login_url = add_query_arg('redirect_to', $redirect_to, $login_url);
    }
    wp_redirect($login_url);
    exit;
  }
}
add_action('login_form_login', 'redirect_to_custom_login');


/** 
 * Redirect the user after authentication if there were any errors. 
 * 
 * @param Wp_User|Wp_Error $user The signed in user, or the errors that have occurred during login. 
 * @param string $username The user name used to log in. 
 * @param string $password The password used to log in. 
 * 
 * @return Wp_User|Wp_Error The logged in user, or error information if there were errors. 
 */
function maybe_redirect_at_authenticate($user, $username, $password)
{
  // Check if the earlier authenticate filter (most likely, 
  // the default WordPress authentication) functions have found errors 
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (is_wp_error($user)) {
      $error_codes = join(',', $user->get_error_codes());
      $login_url = home_url('login');
      $login_url = add_query_arg('login', $error_codes, $login_url);
      wp_redirect($login_url);
      exit;
    }
  }
  return $user;
}
add_filter('authenticate', 'maybe_redirect_at_authenticate', 101, 3);

/** 
 * Returns the URL to which the user should be redirected after the (successful) login. 
 * 
 * @param string $redirect_to The redirect destination URL. 
 * @param string $requested_redirect_to The requested redirect destination URL passed as a parameter. 
 * @param WP_User|WP_Error $user WP_User object if login was successful, WP_Error object otherwise. 
 * 
 * @return string Redirect URL 
 */
function redirect_after_login($redirect_to, $requested_redirect_to, $user)
{
  $redirect_url = home_url();
  if (!isset($user->ID)) {
    return $redirect_url;
  }
  if (user_can($user, 'manage_options')) {
    // Use the redirect_to parameter if one is set, otherwise redirect to admin dashboard. 
    if ($requested_redirect_to == '') {
      $redirect_url = admin_url();
    } else {
      $redirect_url = $requested_redirect_to;
    }
  } else {
    // Non-admin users always go to their account page after login 
    $redirect_url = home_url('dashboard');
  }
  return wp_validate_redirect($redirect_url, home_url());
}
add_filter('login_redirect', 'redirect_after_login', 10, 3);

/** 
 * Redirect to custom login page after the user has been logged out. 
 */
function redirect_after_logout()
{
  $redirect_url = home_url('login?logged_out=true');
  wp_safe_redirect($redirect_url);
  exit;
}
add_action('wp_logout', 'redirect_after_logout');

/** 
 * Redirects the user to the custom registration page instead 
 * of wp-login.php?action=register. 
 */
function redirect_to_custom_register()
{
  if ('GET' == $_SERVER['REQUEST_METHOD']) {
    if (is_user_logged_in()) {
      redirect_logged_in_user();
    } else {
      wp_redirect(home_url('register'));
    }
    exit;
  }
}
add_action('login_form_register', array($this, 'redirect_to_custom_register'));

/** 
 * Redirects the user to the custom "Forgot your password?" page instead of 
 * wp-login.php?action=lostpassword. 
 */
function redirect_to_custom_lostpassword()
{
  if ('GET' == $_SERVER['REQUEST_METHOD']) {
    if (is_user_logged_in()) {
      redirect_logged_in_user();
      exit;
    }
    wp_redirect(home_url('lost-password'));
    exit;
  }
}
add_action('login_form_lostpassword', 'redirect_to_custom_lostpassword');


function do_password_lost()
{
  if ('POST' == $_SERVER['REQUEST_METHOD']) {
    $errors = retrieve_password();
    if (is_wp_error($errors)) {
      // Errors found 
      $redirect_url = home_url('lost-password');
      $redirect_url = add_query_arg('errors', join(',', $errors->get_error_codes()), $redirect_url);
    } else {
      // Email sent 
      $redirect_url = home_url('login');
      $redirect_url = add_query_arg('checkemail', 'confirm', $redirect_url);
    }
    wp_redirect($redirect_url);
    exit;
  }
}
add_action('login_form_lostpassword', 'do_password_lost');

add_action('login_form_rp', 'do_password_reset');
add_action('login_form_resetpass', 'do_password_reset');
/** 
 * Resets the user's password if the password reset form was submitted. 
 */
function do_password_reset()
{
  if ('POST' == $_SERVER['REQUEST_METHOD']) {
    $rp_key = $_REQUEST['rp_key'];
    $rp_login = $_REQUEST['rp_login'];
    $user = check_password_reset_key($rp_key, $rp_login);
    if (!$user || is_wp_error($user)) {
      if ($user && $user->get_error_code() === 'expired_key') {
        wp_redirect(home_url('login?login=expiredkey'));
      } else {
        wp_redirect(home_url('login?login=invalidkey'));
      }
      exit;
    }
    if (isset($_POST['pass1'])) {
      if ($_POST['pass1'] != $_POST['pass2']) {
        // Passwords don't match 
        $redirect_url = home_url('reset-password');
        $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
        $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
        $redirect_url = add_query_arg('error', 'password_reset_mismatch', $redirect_url);
        wp_redirect($redirect_url);
        exit;
      }
      if (empty($_POST['pass1'])) {
        // Password is empty 
        $redirect_url = home_url('reset-password');
        $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
        $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
        $redirect_url = add_query_arg('error', 'password_reset_empty', $redirect_url);
        wp_redirect($redirect_url);
        exit;
      }
      // Parameter checks OK, reset password 
      reset_password($user, $_POST['pass1']);
      wp_redirect(home_url('login?password=changed'));
    } else {
      echo "Invalid request.";
    }
    exit;
  }
}

/**
 * Filter password reset request email's body.
 *
 * @param string $message
 * @param string $key
 * @param string $user_login
 * @return string
 */
function iclub_retrieve_password_message($message, $key, $user_login)
{
  $site_name  = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
  $reset_link = site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
  //$reset_link = site_url("reset-password/?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');

  // Create new message
  $message = __('Someone has requested a password reset for the following account:' . $user_login, 'text_domain') . "\n";
  $message .= sprintf(__('Site Name: %s'), home_url('/')) . "\n";
  $message .= sprintf(__('Username: %s', 'text_domain'), $user_login) . "\n";
  $message .= __('If this was a mistake, just ignore this email and nothing will happen.', 'text_domain') . "\n";
  $message .= __('To reset your password, visit the following address:', 'text_domain') . "\n";
  $message .= $reset_link . "\n";

  return $message;
}

add_filter('retrieve_password_message', 'iclub_retrieve_password_message', 20, 3);



/** 
 * Redirects to the custom password reset page, or the login page 
 * if there are errors. 
 */
function redirect_to_custom_password_reset()
{
  if ('GET' == $_SERVER['REQUEST_METHOD']) {
    // Verify key / login combo 
    $user = check_password_reset_key($_REQUEST['key'], $_REQUEST['login']);
    if (!$user || is_wp_error($user)) {
      if ($user && $user->get_error_code() === 'expired_key') {
        wp_redirect(home_url('login?login=expiredkey'));
      } else {
        wp_redirect(home_url('login?login=invalidkey'));
      }
      exit;
    }
    $redirect_url = home_url('reset-password');
    $redirect_url = add_query_arg('login', esc_attr($_REQUEST['login']), $redirect_url);
    $redirect_url = add_query_arg('key', esc_attr($_REQUEST['key']), $redirect_url);
    wp_redirect($redirect_url);
    exit;
  }
}
add_action('login_form_rp', array($this, 'redirect_to_custom_password_reset'));
add_action('login_form_resetpass', array($this, 'redirect_to_custom_password_reset'));
