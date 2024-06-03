<?php

// Shortcode to output custom PHP in Elementor
function listeo_featured_badge_function($atts)
{
  global $post;
  echo $post->ID;
}
add_shortcode('listeo_featured_badge', 'listeo_featured_badge_function');
