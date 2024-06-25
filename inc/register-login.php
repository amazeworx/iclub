<?php

// Defaults Listeo
// add_action( 'register_form', 'listeo_register_form' ); - Ok
// add_filter( 'registration_errors', 'listeo_wp_admin_registration_errors', 10, 3 ); - Ok
// add_action( 'user_register', 'listeo_wp_admin_user_register'); - Ok
// add_action( 'init', 'submit_change_password_form', 10 ); -- Not Ok / Override this.

function iclub_submit_change_password_form()
{
  $error = false;
  if (isset($_POST['listeo_core-password-change']) && '1' == $_POST['listeo_core-password-change']) {
    $current_user = wp_get_current_user();
    if (!empty($_POST['current_pass']) && !empty($_POST['pass1']) && !empty($_POST['pass2'])) {

      if (!wp_check_password($_POST['current_pass'], $current_user->user_pass, $current_user->ID)) {
        /*$error = 'Your current password does not match. Please retry.';*/
        $error = 'error_1';
      } elseif ($_POST['pass1'] != $_POST['pass2']) {
        /*$error = 'The passwords do not match. Please retry.';*/
        $error = 'error_2';
      } elseif (strlen($_POST['pass1']) < 8) {
        if (get_option('listeo_strong_password')) {
          $password = $_POST['pass1'];
          $uppercase = preg_match('@[A-Z]@', $password);
          $lowercase = preg_match('@[a-z]@', $password);
          $number    = preg_match('@[0-9]@', $password);
          $specialChars = preg_match('@[^\w]@', $password);

          /*$error = 'Your password is weak';*/
          if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            $error = 'error_7';
          }
        } else {
          /*$error = 'A bit short as a password, don\'t you think?';*/
          $error = 'error_3';
        }
      } elseif (false !== strpos(wp_unslash($_POST['pass1']), "\\")) {
        /*$error = 'Password may not contain the character "\\" (backslash).';*/
        $error = 'error_4';
      } else {
        $user_id  = wp_update_user(array('ID' => $current_user->ID, 'user_pass' => esc_attr($_POST['pass1'])));

        if (is_wp_error($user_id)) {
          /*$error = 'An error occurred while updating your profile. Please retry.';*/
          $error = 'error_5';
        } else {
          $error = false;
          //do_action('edit_user_profile_update', $current_user->ID);
          wp_redirect(get_permalink() . '?updated_pass=true');
          exit;
        }
      }

      if (!$error) {
        //do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect(get_permalink() . '?updated_pass=true');
        exit;
      } else {
        wp_redirect(get_permalink() . '?err_pass=' . $error);
        exit;
      }
    } else {
      $error = 'error_6';
      wp_redirect(get_permalink() . '?err_pass=' . $error);
      exit;
    }
  }
}
remove_action('init', 'submit_change_password_form', 10);
add_action('init', 'iclub_submit_change_password_form', 5);

/**
 * Redirects the user to the correct page depending on whether he / she
 * is an admin or not.
 *
 * @param string $redirect_to   An optional redirect_to URL for admin users
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
    //wp_redirect(home_url(get_permalink(get_option('listeo_profile_page'))));
    wp_redirect(home_url('dashboard'));
  }
}

/**
 * Redirects the user to the custom registration page instead
 * of wp-login.php?action=register.
 * 
 * Override Listeo redirect_to_custom_register
 */
function iclub_redirect_to_custom_register()
{
  if ('GET' == $_SERVER['REQUEST_METHOD']) {
    if (is_user_logged_in()) {
      iclub_redirect_logged_in_user();
    } else {
      //wp_redirect(get_permalink(get_option('listeo_profile_page')));
      wp_redirect(home_url('register'));
    }
    exit;
  }
}
remove_action('login_form_register', 'redirect_to_custom_register');
add_action('login_form_register', 'iclub_redirect_to_custom_register', 5);

/**
 * Handles the registration of a new user.
 *
 * Used through the action hook "login_form_register" activated on wp-login.php
 * when accessed through the registration action.
 */
function iclub_do_register_user()
{
  $listeo_core_users = new Listeo_Core_Users();
  if ('POST' == $_SERVER['REQUEST_METHOD']) {
    //$redirect_url = get_permalink(get_option('listeo_profile_page')) . '#tab2';
    $redirect_url = home_url('register');

    if (!get_option('users_can_register')) {
      // Registration closed, display error
      $redirect_url = add_query_arg('register-errors', 'closed', $redirect_url);
    } else {
      $email = $_POST['email'];
      $first_name = (isset($_POST['first_name'])) ? sanitize_text_field($_POST['first_name']) : '';
      $last_name = (isset($_POST['last_name'])) ? sanitize_text_field($_POST['last_name']) : '';

      // get/create username
      if (get_option('listeo_registration_hide_username')) {
        $email_arr = explode('@', $email);
        $user_login = sanitize_user(trim($email_arr[0]), true);
      } else {
        $user_login = sanitize_user(trim($_POST['username']));
      }
      // check if email is from gmail, otherwise show error

      // $email_arr = explode('@', $email);
      // if($email_arr[1] != 'gmail.com') {
      // 	$redirect_url = add_query_arg( 'register-errors', 'gmail-only', $redirect_url );
      // 	wp_redirect( $redirect_url );
      // 	exit;
      // }

      $role =  (isset($_POST['user_role'])) ? sanitize_text_field($_POST['user_role']) : get_option('default_role');
      //$role = sanitize_text_field($_POST['role']);
      if (!in_array($role, array('owner', 'guest', 'seller'))) {
        $role = get_option('default_role');
      }

      $password = (!empty($_POST['password'])) ? sanitize_text_field($_POST['password']) : false;
      if ($role == 'owner' || $role == 'seller') {
        $fields = get_option('listeo_owner_registration_form');
      } else {
        $fields = get_option('listeo_guest_registration_form');
      }

      $custom_registration_fields = array();
      if (!empty($fields)) {
        //get fields for registration
        foreach ($fields as $key => $field) {

          $field_type = str_replace('-', '_', $field['type']);

          if ($handler = apply_filters("listeo_core_get_posted_{$field_type}_field", false)) {

            $value = call_user_func($handler, $key, $field);
          } elseif (method_exists($listeo_core_users, "get_posted_{$field_type}_field")) {

            $value = call_user_func(array($listeo_core_users, "get_posted_{$field_type}_field"), $key, $field);
          } else {

            $value = iclub_get_posted_field($key, $field);
          }

          // Set fields value
          if (isset($field['required']) && !empty($field['required'])) {

            if (!$value) {
              $redirect_url = add_query_arg('register-errors', 'required-field', $redirect_url);
              wp_redirect($redirect_url);
              exit;
            } else {
              $field['value'] = $value;
              $custom_registration_fields[] = $field;
            }
          } else {

            $field['value'] = $value;

            $custom_registration_fields[] = $field;
          }
        }
      }

      $recaptcha_status = get_option('listeo_recaptcha');

      $recaptcha_version = get_option('listeo_recaptcha_version');

      if (get_option('listeo_display_password_field')) {
        if (!$password) {
          $redirect_url = add_query_arg('register-errors', 'password-no', $redirect_url);
          wp_redirect($redirect_url);
          exit;
        }
      }

      // get custom field
      switch ($role) {
        case 'seller':
        case 'owner':
          $fields = get_option('listeo_owner_registration_form');
          break;
        case 'guest':
          $fields = get_option('listeo_guest_registration_form');
          break;
      }

      $recaptcha_pass = true;
      if ($recaptcha_status) {
        $recaptcha_pass = false;

        if ($recaptcha_version == "v2" && isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) :
          $secret = get_option('listeo_recaptcha_secretkey');
          //get verify response data

          $verifyResponse = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
          $responseData = json_decode($verifyResponse['body']);
          if ($responseData->success) :
            //passed captcha, proceed to register
            $recaptcha_pass = true;
          else :
            $redirect_url = add_query_arg('register-errors', 'captcha-fail', $redirect_url);
          endif;
        else :
          $redirect_url = add_query_arg('register-errors', 'captcha-no', $redirect_url);
        endif;

        if ($recaptcha_version == "v3" && isset($_POST['token']) && !empty($_POST['token'])) :
          //your site secret key
          $secret = get_option('listeo_recaptcha_secretkey3');
          //get verify response data
          $verifyResponse = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['token']);
          $responseData_w = wp_remote_retrieve_body($verifyResponse);
          $responseData = json_decode($responseData_w);

          if ($responseData->success == '1' && $responseData->action == 'login' && $responseData->score >= 0.5) :
            //passed captcha, proceed to register
            $recaptcha_pass = true;
          else :
            $redirect_url = add_query_arg('register-errors', 'captcha-fail', $redirect_url);
          endif;
        else :
          $redirect_url = add_query_arg('register-errors', 'captcha-fail', $redirect_url);
        endif;

        if ($recaptcha_pass == false) {
          wp_redirect($redirect_url);
        }
      }

      $privacy_policy_status = get_option('listeo_privacy_policy');
      $privacy_policy_pass = true;
      if ($privacy_policy_status) {
        $privacy_policy_pass = false;
        if (isset($_POST['privacy_policy']) && !empty($_POST['privacy_policy'])) :
          $privacy_policy_pass = true;
        else :
          $redirect_url = add_query_arg('register-errors', 'policy-fail', $redirect_url);
        endif;
      }

      $terms_and_conditions_status =  get_option('listeo_terms_and_conditions_req');
      $terms_and_conditions_pass = true;
      if ($terms_and_conditions_status) {
        $terms_and_conditions_pass = false;
        if (isset($_POST['terms_and_conditions']) && !empty($_POST['terms_and_conditions'])) :
          $terms_and_conditions_pass = true;
        else :
          $redirect_url = add_query_arg('register-errors', 'terms-fail', $redirect_url);
        endif;
      }
      $otp_pass = true;
      $phone = false;
      if (get_option('listeo_otp_status')) {
        //verify otp
        $phone = $_POST['full_phone'];
        $transient_key = 'otp_' . $phone;
        $orignal_token = get_transient($transient_key);
        if (isset($_POST['listeo_otp']['token'])) {
          $token = $_POST['listeo_otp']['token'];

          $letter_1 = $token['code_1'];
          $letter_2 = $token['code_2'];
          $letter_3 = $token['code_3'];
          $letter_4 = $token['code_4'];
          // glue all the letters togheter
          $token = $letter_1 . $letter_2 . $letter_3 . $letter_4;
          // get user phone

          // check if the token is correct

          if ($orignal_token != $token) {
            $otp_pass = false;
            $redirect_url = add_query_arg('register-errors', 'otp-fail', $redirect_url);
            wp_redirect($redirect_url);
            exit;
          }
        } else {
          $otp_pass = false;
          $redirect_url = add_query_arg('register-errors', 'otp-fail', $redirect_url);
          wp_redirect($redirect_url);
          exit;
        }
        //remove the transient
        delete_transient($transient_key);
      }

      if ($recaptcha_pass && $privacy_policy_pass && $terms_and_conditions_pass && $otp_pass) {

        $result = $listeo_core_users->register_user($email, $user_login, $first_name, $last_name, $role, $phone, $password, $custom_registration_fields);

        if (is_wp_error($result)) {
          // Parse errors into a string and append as parameter to redirect
          $errors = join(',', $result->get_error_codes());
          $redirect_url = add_query_arg('register-errors', $errors, $redirect_url);
        } else {
          // Success, redirect to login page.

          if ($role == 'owner' || $role == 'seller') {

            $redirect_page_id = get_option('listeo_owner_registration_redirect');

            if ($redirect_page_id) {
              $redirect_url = get_permalink($redirect_page_id);
            } else {
              //$redirect_url = get_permalink(get_option('listeo_profile_page'));
              $redirect_url = home_url('login');
            }
          } else if ($role == 'guest') {
            $redirect_page_id = get_option('listeo_guest_registration_redirect');
            if ($redirect_page_id) {
              $redirect_url = get_permalink($redirect_page_id);
            } else {
              //$redirect_url = get_permalink(get_option('listeo_profile_page'));
              $redirect_url = home_url('login');
            }
          } else {
            //$redirect_url = get_permalink(get_option('listeo_profile_page'));
            $redirect_url = home_url('login');
          }
          $redirect_url = add_query_arg('registered', $email, $redirect_url);
        }
      }
    }
    wp_redirect($redirect_url);
    exit;
  }
}
remove_action('login_form_register', 'do_register_user');
add_action('login_form_register', 'iclub_do_register_user', 5);


function iclub_get_posted_field($key, $field)
{
  return isset($_POST[$key]) ? iclub_sanitize_posted_field($_POST[$key]) : '';
}

/**
 * Navigates through an array and sanitizes the field.
 *
 * @param array|string $value The array or string to be sanitized.
 * @return array|string $value The sanitized array (or string from the callback).
 */
function iclub_sanitize_posted_field($value)
{
  // Santize value
  $value = is_array($value) ? array_map('iclub_sanitize_posted_field', $value) : sanitize_text_field(stripslashes(trim($value)));

  return $value;
}


// LOGIN
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
  //die(print_r($user, true));
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
//remove_filter('authenticate', 'maybe_redirect_at_authenticate', 101, 3);
add_filter('authenticate', 'iclub_maybe_redirect_at_authenticate', 100, 3);

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
    case 'email_exists':
      return __('This email is already registered', 'listeo_core');
      break;
    case 'username_exists':
      return __('This username already exists', 'listeo_core');
      break;
    case 'empty_username':
      return __('You do have an email address, right?', 'listeo_core');
      break;
    case 'empty_password':
      return __('You need to enter a password to login.', 'listeo_core');
      break;
    case 'strong_password':
      return __('You password is too weak.', 'listeo_core');
      break;
    case 'invalid_username':
      return __(
        "We don't have any users with that email address. Maybe you used a different one when signing up?",
        'listeo_core'
      );
      break;
    case 'incorrect_password':
      $err = __(
        "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
        'listeo_core'
      );
      return sprintf($err, wp_lostpassword_url());
      break;
    case 'closed':
      return __('Registering new users is currently not allowed.', 'iclub');
      // Lost password 
    case 'invalid_email':
    case 'invalidcombo':
      return __('There are no users registered with this email address.', 'iclub');
      // Reset password 
    case 'expiredkey':
      return __('The password reset link you used is expired.', 'iclub');
    case 'invalidkey':
      return __('The password reset link you used is not valid anymore.', 'iclub');
    case 'password_reset_mismatch':
      return __("The two passwords you entered don't match.", 'iclub');

    case 'password_reset_empty':
      return __("Sorry, we don't accept empty passwords.", 'iclub');
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
add_filter('login_redirect', 'iclub_redirect_after_login', 5, 3);

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
add_action('login_form_lostpassword', 'iclub_do_password_lost', 5);

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
add_filter('retrieve_password_message', 'iclub_replace_retrieve_password_message', 5, 4);

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
add_action('login_form_rp', 'iclub_redirect_to_custom_password_reset', 5);
add_action('login_form_resetpass', 'iclub_redirect_to_custom_password_reset', 5);

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
add_action('login_form_rp', 'iclub_do_password_reset', 5);
add_action('login_form_resetpass', 'iclub_do_password_reset', 5);
