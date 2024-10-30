<?php 
namespace SliderPlugin;

/**
 * Plugin Name: Slider Plugin
 * Description: A simple slider plugin to manage multiple sliders and images with editing and settings options.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: slider-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

define( 'SLIDER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SLIDER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include required files
require_once SLIDER_PLUGIN_DIR . 'includes/class-settings.php';
require_once SLIDER_PLUGIN_DIR . 'includes/class-slider.php';
require_once SLIDER_PLUGIN_DIR . 'admin/admin.php';

// Initialize settings and slider classes
Settings::init();
Slider::init(); // Ensure this method exists in your Slider class

// Enqueue CSS and JavaScript
function enqueue_slider_assets() {
    wp_enqueue_style( 'slider-style', SLIDER_PLUGIN_URL . 'assets/css/slider-style.css' );
    wp_enqueue_script( 'slider-script', SLIDER_PLUGIN_URL . 'assets/js/slider-script.js', array('jquery'), null, true );

    // Enqueue Font Awesome for icons
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' );

    // Localize the script for AJAX
    wp_localize_script( 'slider-script', 'sliderAjax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
    ));
}
add_action( 'wp_enqueue_scripts', 'SliderPlugin\enqueue_slider_assets' );

// Register the shortcode
add_shortcode('slider', 'SliderPlugin\slider_shortcode');

/**
 * Shortcode function to display the slider
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output for the slider
 */
function slider_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'id' => '', // Slider ID
        ), 
        $atts
    );

    ob_start();

    // Fetch images from the Slider class for the specified slider ID
    $images = Slider::get_images($atts['id']); // Ensure this method exists

    if (!empty($images)) {
        echo '<div class="slider-container">';

        foreach ($images as $image) {
            echo '<div class="slider-item" style="display: none;">'; // Hide initially
            echo '<img src="' . esc_url($image['image_url']) . '" alt="Slider Image" />';
            echo '</div>';
        }

        // Navigation Images
        echo '<div class="slider-navigation">';
        echo '<img src="' . esc_url(SLIDER_PLUGIN_URL . 'assets/images/unnamed (1).png') . '" class="slider-prev" alt="Previous" />';
        echo '<img src="' . esc_url(SLIDER_PLUGIN_URL . 'assets/images/unnamed.png') . '" class="slider-next" alt="Next" />';
        echo '</div>';

        // Dot Indicators
        echo '<div class="slider-dots">';
        for ($i = 0; $i < count($images); $i++) {
            echo '<span class="dot" data-index="' . $i . '"></span>';
        }
        echo '</div>';

        echo '</div>'; // End slider-container
    } else {
        echo '<p>No images available in the slider.</p>';
    }

    return ob_get_clean();
}

// AJAX handler for any operations you want to perform
add_action('wp_ajax_your_ajax_action', 'your_ajax_function');
add_action('wp_ajax_nopriv_your_ajax_action', 'your_ajax_function'); // For non-logged-in users

function your_ajax_function() {
    // Your logic here, e.g., processing data or fetching more images
    wp_send_json_success('Data processed successfully'); // Or use wp_send_json_error('Error message');
    wp_die(); // Always end AJAX functions with this
}
