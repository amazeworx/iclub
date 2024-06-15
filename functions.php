<?php

require get_stylesheet_directory() . '/inc/shortcodes.php';
//require get_stylesheet_directory() . '/inc/register-login.php';
require get_stylesheet_directory() . '/inc/reg-login.php';


add_action('wp_enqueue_scripts', 'listeo_enqueue_styles');
function listeo_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', array('bootstrap', 'font-awesome-5', 'font-awesome-5-shims', 'simple-line-icons', 'listeo-woocommerce'));
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/assets/css/app.css', array(), '1.2');
}

function remove_parent_theme_features()
{
}
add_action('after_setup_theme', 'remove_parent_theme_features', 10);

/**
 * remove category: from category titles
 */
add_filter('get_the_archive_title_prefix', '__return_false');


/**
 * remove listeo listings post type archive
 */
add_filter('register_post_type_args', 'remove_listeo_listings_archive', 10, 2);
function remove_listeo_listings_archive($args, $post_name)
{
    if ($post_name != 'listings')
        return $args;

    $args['has_archive'] = false;

    return $args;
}
