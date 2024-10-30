<?php
namespace SliderPlugin;

class Settings {
    public static function init() {
        // Add custom admin menu
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_action('admin_init', [__CLASS__, 'settings_init']);
        add_action('admin_post_save_slider_image', [__CLASS__, 'handle_image_upload']);
        add_action('admin_post_delete_image', [__CLASS__, 'handle_image_delete']);
        add_action('admin_post_create_slider', [__CLASS__, 'handle_create_slider']);
        add_action('admin_post_delete_slider', [__CLASS__, 'handle_delete_slider']);
    }

    public static function add_admin_menu() {
        add_menu_page(
            'Slider Settings',
            'Slider Settings',
            'manage_options',
            'slider_settings',
            [__CLASS__, 'settings_page'],
            'dashicons-images-alt',
            100
        );
    }

    public static function settings_init() {
        // No need to register settings if we are not using options.php
    }

    public static function handle_create_slider() {
        // Check if the user is authorized
        if (!current_user_can('manage_options')) {
            wp_die('You are not allowed to create sliders.');
        }

        // Ensure the name is set
        if (isset($_POST['slider_name']) && !empty($_POST['slider_name'])) {
            $slider_name = sanitize_text_field($_POST['slider_name']);

            // Ensure the Slider class exists and add the slider
            if (class_exists('SliderPlugin\Slider')) {
                $slider_id = Slider::add_slider($slider_name);
                wp_safe_redirect(admin_url('admin.php?page=slider_settings&status=slider_created&slider_id=' . $slider_id));
                exit;
            } else {
                error_log('Slider class not found.');
                wp_die('Slider class not found.');
            }
        }

        wp_safe_redirect(admin_url('admin.php?page=slider_settings&status=error'));
        exit;
    }

    public static function handle_image_upload() {
        // Check if the user is authorized
        if (!current_user_can('manage_options')) {
            wp_die('You are not allowed to upload images.');
        }

        // Ensure the file is uploaded
        if (!empty($_FILES['slider_image']['name']) && isset($_POST['slider_id'])) {
            $uploaded = media_handle_upload('slider_image', 0);

            if (is_wp_error($uploaded)) {
                wp_die('Image upload failed: ' . $uploaded->get_error_message());
            } else {
                $image_url = wp_get_attachment_url($uploaded);
                $slider_id = intval($_POST['slider_id']);

                // Ensure the Slider class exists and add the image URL to the custom table
                if (class_exists('SliderPlugin\Slider')) {
                    Slider::add_image($slider_id, $image_url);
                } else {
                    error_log('Slider class not found.');
                    wp_die('Slider class not found.');
                }

                // Redirect back to the settings page with success status
                wp_safe_redirect(admin_url('admin.php?page=slider_settings&status=success'));
                exit;
            }
        }

        // Redirect back with an error status if no file is uploaded or an issue occurs
        wp_safe_redirect(admin_url('admin.php?page=slider_settings&status=error'));
        exit;
    }

    public static function handle_image_delete() {
        // Check if the user is authorized
        if (!current_user_can('manage_options')) {
            wp_die('You are not allowed to delete images.');
        }

        // Ensure the ID is set and is an integer
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $image_id = intval($_GET['id']);

            // Ensure the Slider class exists and delete the image
            if (class_exists('SliderPlugin\Slider')) {
                if (Slider::delete_image($image_id)) {
                    wp_safe_redirect(admin_url('admin.php?page=slider_settings&status=deleted'));
                } else {
                    error_log('Failed to delete image with ID: ' . $image_id);
                    wp_safe_redirect(admin_url('admin.php?page=slider_settings&status=deleted'));
                }
                exit;
            } else {
                error_log('Slider class not found.');
                wp_die('Slider class not found.');
            }
        } else {
            wp_safe_redirect(admin_url('admin.php?page=slider_settings&status=invalid'));
            exit;
        }
    }

    public static function handle_delete_slider() {
        // Check if the user is authorized
        if (!current_user_can('manage_options')) {
            wp_die('You are not allowed to delete sliders.');
        }

        // Ensure the slider ID is set
        if (isset($_GET['slider_id']) && is_numeric($_GET['slider_id'])) {
            $slider_id = intval($_GET['slider_id']);

            // Ensure the Slider class exists and delete the slider and its images
            if (class_exists('SliderPlugin\Slider')) {
                Slider::delete_slider($slider_id);
                wp_safe_redirect(admin_url('admin.php?page=slider_settings&status=slider_deleted'));
                exit;
            } else {
                error_log('Slider class not found.');
                wp_die('Slider class not found.');
            }
        } else {
            // Redirect with an error if slider ID is not valid
            wp_safe_redirect(admin_url('admin.php?page=slider_settings&status=invalid_slider_id'));
            exit;
        }
    }

    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1>Slider Settings</h1>

            <?php
            // Display status messages
            if (isset($_GET['status'])) {
                switch ($_GET['status']) {
                    case 'success':
                        echo '<div class="notice notice-success"><p>Image uploaded successfully!</p></div>';
                        break;
                    case 'deleted':
                        echo '<div class="notice notice-success"><p>Image deleted successfully!</p></div>';
                        break;
                    case 'slider_deleted':
                        echo '<div class="notice notice-success"><p>Slider deleted successfully!</p></div>';
                        break;
                    case 'invalid':
                        echo '<div class="notice notice-error"><p>Invalid image or slider ID.</p></div>';
                        break;
                    case 'slider_created':
                        echo '<div class="notice notice-success"><p>Slider created successfully!</p></div>';
                        break;
                    case 'invalid_slider_id':
                        echo '<div class="notice notice-error"><p>Invalid slider ID.</p></div>';
                        break;
                    default:
                        echo '<div class="notice notice-error"><p>Action failed. Please try again.</p></div>';
                        break;
                }
            }
            ?>

            <!-- Form to create a new slider -->
            <h2>Create New Slider</h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="create_slider">
                <input type="text" name="slider_name" placeholder="Slider Name" required>
                <?php submit_button('Create Slider'); ?>
            </form>

            <!-- Display sliders with shortcodes and upload images -->
            <h2>Available Sliders</h2>
            <?php
            // Fetch the sliders from the custom table
            $sliders = Slider::get_sliders();
            if (!empty($sliders)) {
                foreach ($sliders as $slider) {
                    echo '<h3>' . esc_html($slider['name']) . ' - Shortcode: <code>[' . esc_html($slider['shortcode']) . ']</code>';
                    echo ' <a href="' . esc_url(admin_url('admin-post.php?action=delete_slider&slider_id=' . $slider['id'])) . '" onclick="return confirm(\'Are you sure you want to delete this slider?\');">Delete Slider</a></h3>';

                    // Render image upload form for the current slider
                    self::slider_image_render([$slider]);

                    // Display uploaded images for each slider
                    $images = Slider::get_images($slider['id']);
                    if (!empty($images)) {
                        foreach ($images as $image) {
                            echo '<div>';
                            echo '<img src="' . esc_url($image['image_url']) . '" width="100" height="100">';
                            echo '<a href="' . esc_url(admin_url('admin-post.php?action=delete_image&id=' . $image['id'])) . '">Delete</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No images uploaded for this slider yet.</p>';
                    }
                }
            } else {
                echo '<p>No sliders available. Please create a slider first.</p>';
            }
            ?>
        </div>
        <?php
    }

    public static function slider_image_render($sliders) {
        ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_slider_image">
            <label for="slider_select">Select Slider:</label>
            <select name="slider_id" required>
                <?php foreach ($sliders as $slider): ?>
                    <option value="<?php echo esc_attr($slider['id']); ?>"><?php echo esc_html($slider['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="file" name="slider_image" accept="image/*" required>
            <?php submit_button('Upload Image'); ?>
        </form>
        <?php
    }
}
