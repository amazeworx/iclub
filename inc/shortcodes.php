<?php

// Shortcode to output custom PHP in Elementor
function listeo_featured_badge_function($atts)
{
  global $post;
  $is_featured = listeo_core_is_featured($post->ID);

  $output = '';
  if ($is_featured) {
    $output .= '<div class="listing-small-badges-container">';
    $output .= '<div class="listing-small-badge featured-badge"><i class="fa fa-star"></i>' . esc_html_e('Featured', 'listeo_core') . '</div>';
    $output .= '</div>';
  }

  return $output;
}
add_shortcode('listeo_featured_badge', 'listeo_featured_badge_function');
