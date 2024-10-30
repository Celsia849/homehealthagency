<?php
namespace SliderPlugin;

class Admin {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_admin_page']);
        add_action('admin_post_upload_image', [__CLASS__, 'handle_image_upload']);
        add_action('admin_post_delete_image', [__CLASS__, 'handle_image_delete']);
    }

    /**
     * Add an admin page for slider images.
     */
    public static function add_admin_page() {
        add_menu_page(
            'Slider Images',
            'Slider Images',
            'manage_options',
            'slider_images',
            [__CLASS__, 'render_admin_page']
        );
    }

    /**
     * Render the admin page for uploading slider images.
     */
    public static function render_admin_page() {
        $sliders = Slider::get_sliders(); // Get all sliders to select from
        ?>
        <div class="wrap">
            <h1>Upload Slider Images</h1>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_image">
                <?php wp_nonce_field('upload_image_nonce'); ?>

                <label for="slider_select">Select a Slider:</label>
                <select name="slider_id" id="slider_select" required>
                    <option value="">Select a slider</option>
                    <?php foreach ($sliders as $slider): ?>
                        <option value="<?php echo esc_attr($slider['id']); ?>"><?php echo esc_html($slider['name']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="slider_image">Choose an image to upload:</label>
                <input type="file" name="slider_image" id="slider_image" accept="image/*" required />
                <?php submit_button('Upload Image'); ?>
            </form>

            <h2>Current Images</h2>
            <ul>
                <?php
                // Display current images for all sliders
                foreach ($sliders as $slider) {
                    $images = Slider::get_images($slider['id']); // Fetch images for the selected slider
                    echo '<h3>' . esc_html($slider['name']) . '</h3>';
                    foreach ($images as $image) {
                        echo '<li>
                            <img src="' . esc_url($image['image_url']) . '" width="100" height="100" alt="Slider Image"> 
                            ' . esc_html($image['image_url']) . ' 
                            <a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_image&id=' . $image['id']), 'delete_image_nonce') . '" onclick="return confirm(\'Are you sure you want to delete this image?\');">Delete</a>
                        </li>';
                    }
                }
                ?>
            </ul>
        </div>
        <?php
    }

    /**
     * Handle the image upload.
     */
    public static function handle_image_upload() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        // Check nonce for security
        check_admin_referer('upload_image_nonce');

        if (isset($_FILES['slider_image']) && isset($_POST['slider_id'])) {
            $uploaded_file = $_FILES['slider_image'];
            $slider_id = intval($_POST['slider_id']); // Get the selected slider ID

            // Check for upload errors
            if ($uploaded_file['error'] !== UPLOAD_ERR_OK) {
                error_log('Upload error: ' . $uploaded_file['error']);
                wp_die('File upload error. Please check the file and try again.');
            }

            // Check file type
            $file_type = wp_check_filetype($uploaded_file['name']);
            if (!in_array($file_type['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
                wp_die('Unsupported file type. Please upload a JPEG, PNG, or GIF image.');
            }

            // Move the uploaded file to the uploads directory
            $upload_dir = wp_upload_dir();
            $file_name = basename($uploaded_file['name']);
            $file_path = $upload_dir['path'] . '/' . $file_name;

            // Handle file naming conflicts
            $counter = 1;
            while (file_exists($file_path)) {
                $file_name = pathinfo($uploaded_file['name'], PATHINFO_FILENAME) . "-$counter." . pathinfo($uploaded_file['name'], PATHINFO_EXTENSION);
                $file_path = $upload_dir['path'] . '/' . $file_name;
                $counter++;
            }

            if (move_uploaded_file($uploaded_file['tmp_name'], $file_path)) {
                $image_url = $upload_dir['url'] . '/' . $file_name;

                // Store image URL in your custom database table
                Slider::add_image($slider_id, $image_url); // Ensure this method handles the database insertion
            } else {
                error_log('Failed to move uploaded file to ' . $file_path);
                wp_die('Failed to save the uploaded file. Please try again.');
            }
        } else {
            wp_die('No file was uploaded or no slider selected.');
        }

        wp_redirect(admin_url('admin.php?page=slider_images'));
        exit;
    }

    /**
     * Handle the image deletion.
     */
    public static function handle_image_delete() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        // Check nonce for security
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'delete_image_nonce')) {
            wp_die('Nonce verification failed.');
        }

        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            Slider::delete_image($id);
        }

        wp_redirect(admin_url('admin.php?page=slider_images'));
        exit;
    }
}
