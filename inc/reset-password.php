<?php

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
  $reset_link = site_url("reset-password/?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');

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
