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
      $output .= '<span class="save like-icon tooltip left"  title="' . esc_html_e('Login To Bookmark Items', 'listeo_core') . '"></span>';
    }
  }

  return $output;
}
add_shortcode('listeo_bookmark_button', 'listeo_bookmark_button_function');
