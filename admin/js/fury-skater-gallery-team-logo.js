jQuery(document).ready(function($){
    var mediaUploader;

    $('.upload_image_button').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var logoField = button.closest('.team-logo-field');
        var image_display = logoField.find('img');
        var image_id_input = logoField.find('#team_logo_id');
        var remove_button = logoField.find('.remove_image_button');

        // If the uploader already exists, reopen it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create the media frame
        mediaUploader = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        // When an image is selected
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            image_id_input.val(attachment.id);

            // Explicitly remove any existing image
            logoField.find('img').remove();

            // Add the new image
            button.before('<img src="' + attachment.url + '" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px;">');

            remove_button.show();
        });

        // Open the uploader
        mediaUploader.open();
    });

    $('.remove_image_button').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var logoField = button.closest('.team-logo-field');
        var image_display = logoField.find('img');
        var image_id_input = logoField.find('#team_logo_id');

        image_id_input.val('');
        image_display.remove();
        button.hide();
    });
});