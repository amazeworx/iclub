<?php

/** 
 * Redirects the user to the correct page depending on whether he / she 
 * is an admin or not. 
 * 
 * @param string $redirect_to An optional redirect_to URL for admin users 
 */
function iclub_redirect_logged_in_user($redirect_to = null)
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
 * Redirect the user to the custom login page instead of wp-login.php. 
 */
function iclub_redirect_to_custom_login()
{
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : null;

    if (is_user_logged_in()) {
      iclub_redirect_logged_in_user($redirect_to);
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
add_action('login_form_login', 'iclub_redirect_to_custom_login', 5);

/** 
 * Redirect the user after authentication if there were any errors. 
 * 
 * @param Wp_User|Wp_Error $user The signed in user, or the errors that have occurred during login. 
 * @param string $username The user name used to log in. 
 * @param string $password The password used to log in. 
 * 
 * @return Wp_User|Wp_Error The logged in user, or error information if there were errors. 
 */
function iclub_maybe_redirect_at_authenticate($user, $username, $password)
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
remove_filter('authenticate', 'maybe_redirect_at_authenticate', 101, 3);
add_filter('authenticate', 'iclub_maybe_redirect_at_authenticate', 101, 3);


/** 
 * Finds and returns a matching error message for the given error code. 
 * 
 * @param string $error_code The error code to look up. 
 * 
 * @return string An error message. 
 */
function iclub_get_error_message($error_code)
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
      // Lost password 
    case 'empty_username':
      return __('You need to enter your email address to continue.', 'personalize-login');
    case 'invalid_email':
    case 'invalidcombo':
      return __('There are no users registered with this email address.', 'personalize-login');
      // Reset password 
    case 'expiredkey':
    case 'invalidkey':
      return __('The password reset link you used is not valid anymore.', 'personalize-login');
    case 'password_reset_mismatch':
      return __("The two passwords you entered don't match.", 'personalize-login');

    case 'password_reset_empty':
      return __("Sorry, we don't accept empty passwords.", 'personalize-login');
    default:
      break;
  }

  return __('An unknown error occurred. Please try again later.', 'personalize-login');
}

/** 
 * Redirect to custom login page after the user has been logged out. 
 */
function iclub_redirect_after_logout()
{
  $redirect_url = home_url('login?logged_out=true');
  wp_safe_redirect($redirect_url);
  exit;
}
add_action('wp_logout', 'iclub_redirect_after_logout', 5);

/** 
 * Returns the URL to which the user should be redirected after the (successful) login. 
 * 
 * @param string $redirect_to The redirect destination URL. 
 * @param string $requested_redirect_to The requested redirect destination URL passed as a parameter. 
 * @param WP_User|WP_Error $user WP_User object if login was successful, WP_Error object otherwise. 
 * 
 * @return string Redirect URL 
 */
function iclub_redirect_after_login($redirect_to, $requested_redirect_to, $user)
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
remove_filter('login_redirect', 'redirect_after_login', 10, 3);
add_filter('login_redirect', 'iclub_redirect_after_login', 10, 3);

/** 
 * Redirects the user to the custom registration page instead 
 * of wp-login.php?action=register. 
 */
function iclub_redirect_to_custom_register()
{
  if ('GET' == $_SERVER['REQUEST_METHOD']) {
    if (is_user_logged_in()) {
      iclub_redirect_logged_in_user();
    } else {
      wp_redirect(home_url('register'));
    }
    exit;
  }
}
add_action('login_form_register', 'iclub_redirect_to_custom_register', 5);

/** 
 * Validates and then completes the new user signup process if all went well. 
 * 
 * @param string $email The new user's email address 
 * @param string $first_name The new user's first name 
 * @param string $last_name The new user's last name 
 * 
 * @return int|WP_Error The id of the user that was created, or error if failed. 
 */
function iclub_register_user($email, $password, $first_name, $last_name, $buy_or_sell, $role)
{
  $errors = new WP_Error();

  // Email address is used as both username and email. It is also the only 
  // parameter we need to validate 
  if (!is_email($email)) {
    $errors->add('email', iclub_get_error_message('email'));
    return $errors;
  }
  // if (email_exists($email)) {
  //   $errors->add('email_exists',  iclub_get_error_message('email_exists'));
  //   return $errors;
  // }
  if (username_exists($email) || email_exists($email)) {
    $errors->add('email_exists',  iclub_get_error_message('email_exists'));
    return $errors;
  }

  // Generate the password so that the subscriber will have to check email...
  if (!$password) {
    $password = wp_generate_password(12, false);
  }
  if (get_option('listeo_strong_password')) {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {

      $errors->add('strong_password', iclub_get_error_message('strong_password'));
      return $errors;
    }
  }

  if (!in_array($role, array('owner', 'guest', 'seller'))) {
    $role = get_option('default_role');
  }

  // Generate the password so that the subscriber will have to check email... 
  //$password = wp_generate_password(12, false);
  $user_data = array(
    'user_login'    => $email,
    'user_email'    => $email,
    'user_pass'     => $password,
    'first_name'    => $first_name,
    'last_name'     => $last_name,
    'role'          => $role
  );
  $user_id = wp_insert_user($user_data);

  update_user_meta($user_id, '_buy_or_sell_or_both', $buy_or_sell);

  wp_new_user_notification($user_id, $password);

  return $user_id;
}

/** 
 * Handles the registration of a new user. 
 * 
 * Used through the action hook "login_form_register" activated on wp-login.php 
 * when accessed through the registration action. 
 */
function iclub_do_register_user()
{
  if ('POST' == $_SERVER['REQUEST_METHOD']) {
    $redirect_url = home_url('register');
    if (!get_option('users_can_register')) {
      // Registration closed, display error 
      $redirect_url = add_query_arg('register-errors', 'closed', $redirect_url);
    } else {
      $email = sanitize_email($_POST['email']);
      $first_name = sanitize_text_field($_POST['first_name']);
      $last_name = sanitize_text_field($_POST['last_name']);
      $email = $_POST['email'];
      $first_name = (isset($_POST['first_name'])) ? sanitize_text_field($_POST['first_name']) : '';
      $last_name = (isset($_POST['last_name'])) ? sanitize_text_field($_POST['last_name']) : '';

      $role =  (isset($_POST['user_role'])) ? sanitize_text_field($_POST['user_role']) : get_option('default_role');
      if (!in_array($role, array('owner', 'guest', 'seller'))) {
        $role = get_option('default_role');
      }

      $password = (!empty($_POST['password'])) ? sanitize_text_field($_POST['password']) : false;

      $buy_or_sell = sanitize_text_field($_POST['_buy_or_sell']);

      $result = iclub_register_user($email, $password, $first_name, $last_name, $buy_or_sell, $role);

      if (is_wp_error($result)) {
        // Parse errors into a string and append as parameter to redirect 
        $errors = join(',', $result->get_error_codes());
        $redirect_url = add_query_arg('register-errors', $errors, $redirect_url);
      } else {
        // Success, redirect to login page. 
        wp_set_current_user($result);
        wp_set_auth_cookie($result);
        if ($role == 'owner' || $role == 'seller') {

          $redirect_page_id = get_option('listeo_owner_registration_redirect');

          if ($redirect_page_id) {
            $redirect_url = get_permalink($redirect_page_id);
          } else {
            $redirect_url = get_permalink(get_option('listeo_profile_page'));
          }
        } else if ($role == 'guest') {
          $redirect_page_id = get_option('listeo_guest_registration_redirect');
          if ($redirect_page_id) {
            $redirect_url = get_permalink($redirect_page_id);
          } else {
            $redirect_url = get_permalink(get_option('listeo_profile_page'));
          }
        } else {
          $redirect_url = get_permalink(get_option('listeo_profile_page'));
        }
        $redirect_url = add_query_arg('registered', $email, $redirect_url);
      }
    }
    wp_redirect($redirect_url);
    exit;
  }
}
remove_action('login_form_register', 'do_register_user');
add_action('login_form_register', 'iclub_do_register_user', 5);

function iclub_auto_login_new_user($user_id)
{
  wp_set_current_user($user_id);
  wp_set_auth_cookie($user_id);
  wp_redirect(home_url('dashboard'));
  exit();
}
//add_action('user_register', 'iclub_auto_login_new_user');

/** 
 * Redirects the user to the custom "Forgot your password?" page instead of 
 * wp-login.php?action=lostpassword. 
 */
function iclub_redirect_to_custom_lostpassword()
{
  if ('GET' == $_SERVER['REQUEST_METHOD']) {
    if (is_user_logged_in()) {
      iclub_redirect_logged_in_user();
      exit;
    }
    wp_redirect(home_url('lost-password'));
    exit;
  }
}
add_action('login_form_lostpassword', 'iclub_redirect_to_custom_lostpassword', 5);

/** 
 * Initiates password reset. 
 */
function iclub_do_password_lost()
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
add_action('login_form_lostpassword', 'iclub_do_password_lost');

/** 
 * Returns the message body for the password reset mail. 
 * Called through the retrieve_password_message filter. 
 * 
 * @param string $message Default mail message. 
 * @param string $key The activation key. 
 * @param string $user_login The username for the user. 
 * @param WP_User $user_data WP_User object. 
 * 
 * @return string The mail message to send. 
 */
function iclub_replace_retrieve_password_message($message, $key, $user_login, $user_data)
{
  // Create new message 
  $msg  = __('Hello!', 'personalize-login') . "\r\n\r\n";
  $msg .= sprintf(__('You asked us to reset your password for your account using the email address %s.', 'personalize-login'), $user_login) . "\r\n\r\n";
  $msg .= __("If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'personalize-login') . "\r\n\r\n";
  $msg .= __('To reset your password, visit the following address:', 'personalize-login') . "\r\n\r\n";
  //$msg .= site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n\r\n";
  $msg .= site_url("reset-password/?key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n\r\n";
  $msg .= __('Thanks!', 'personalize-login') . "\r\n";
  return $msg;
}
add_filter('retrieve_password_message', 'iclub_replace_retrieve_password_message', 10, 4);


/** 
 * Redirects to the custom password reset page, or the login page 
 * if there are errors. 
 */
function iclub_redirect_to_custom_password_reset()
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
add_action('login_form_rp', 'iclub_redirect_to_custom_password_reset');
add_action('login_form_resetpass', 'iclub_redirect_to_custom_password_reset');

/** 
 * Resets the user's password if the password reset form was submitted. 
 */
function iclub_do_password_reset()
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
add_action('login_form_rp', 'iclub_do_password_reset');
add_action('login_form_resetpass', 'iclub_do_password_reset');
