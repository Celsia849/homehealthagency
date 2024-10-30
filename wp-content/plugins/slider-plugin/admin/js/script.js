jQuery(document).ready(function($) {
    $('.delete-image').on('click', function() {
        const button = $(this);
        const id = button.data('id');
        
        $.post(sliderAjax.ajaxurl, { action: 'delete_image', id: id }, function(response) {
            if (response.success) {
                button.parent().remove();
            } else {
                alert('Failed to delete image.');
            }
        });
    });
});
