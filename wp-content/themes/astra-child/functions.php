<?php
// Enqueue parent and child theme styles
function astra_child_enqueue_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style('astra-parent-style', get_template_directory_uri() . '/style.css');

    // Enqueue child theme stylesheet
    wp_enqueue_style('astra-child-style', get_stylesheet_directory_uri() . '/style.css', array('astra-parent-style'));
}

// Hook the function into the wp_enqueue_scripts action
add_action('wp_enqueue_scripts', 'astra_child_enqueue_styles');
// Hook into 'the_title' filter
add_filter('the_title', 'myplugin_modify_post_title');

// Define the function to modify the title
function myplugin_modify_post_title($title) {
    if (is_admin()) {
        return $title;
    }

    if (empty($title)) {
        return $title;
    }

    return $title . ' - Custom Title';
}
