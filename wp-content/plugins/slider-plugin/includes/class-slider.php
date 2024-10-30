<?php
namespace SliderPlugin;

class Slider {
    public static function init() {
        // Initialization code here (if needed)
        // For example, you could add hooks or setup database tables
    }

    /**
     * Add a new slider to the database.
     *
     * @param string $name The name of the slider.
     */
    public static function add_slider($name) {
        global $wpdb;

        // Insert the slider into the custom table
        $wpdb->insert(
            $wpdb->prefix . 'sliders', // Your actual slider table name
            ['name' => $name]
        );
    }

    /**
     * Retrieve all sliders from the database.
     *
     * @return array An array of sliders.
     */
    public static function get_sliders() {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sliders", ARRAY_A); // Adjust the table name accordingly
    }

    /**
     * Add an image to a specific slider.
     *
     * @param int $slider_id The ID of the slider.
     * @param string $image_url The URL of the image.
     */
    public static function add_image($slider_id, $image_url) {
        global $wpdb;

        // Insert the image into the slider_images table
        $wpdb->insert(
            $wpdb->prefix . 'slider_images', // Your image table name
            [
                'slider_id' => $slider_id,
                'image_url' => $image_url,
            ]
        );
    }

    /**
     * Delete an image from a slider.
     *
     * @param int $image_id The ID of the image to delete.
     * @return int|false The number of rows deleted, or false on error.
     */
    public static function delete_image($image_id) {
        global $wpdb;

        // Delete the image from the slider_images table
        return $wpdb->delete(
            $wpdb->prefix . 'slider_images', // Your image table name
            ['id' => $image_id]
        );
    }

    /**
     * Retrieve images for a specific slider.
     *
     * @param int $slider_id The ID of the slider.
     * @return array An array of images.
     */
    public static function get_images($slider_id) {
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}slider_images WHERE slider_id = %d", $slider_id), ARRAY_A);
    }
}
