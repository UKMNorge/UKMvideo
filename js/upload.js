jQuery(document).ready(function() {
    jQuery('#fileupload_reportasje').fileupload({
        // Uncomment the following to send cross-domain cookies:
        xhrFields: { withCredentials: true },
        url: 'https://videoconverter.' + UKM_HOSTNAME + '/jQupload_recieve.php',
        fileTypes: '^video\/(.)+',
        autoUpload: true,
        formData: {
            'season': jQuery('#converter_season').val(),
            'pl_id': jQuery('#converter_pl_id').val(),
            'type': jQuery('#converter_type').val(),
            'b_id': jQuery('#converter_b_id').val(),
            'blog_id': jQuery('#converter_blog_id').val()
        },
        progressall: function(e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            jQuery('#uploadprogress').attr('value', progress);
        },
    }).bind('fileuploaddone', function(e, data) {
        if (!data.result.success) {
            fileUploadError(data.result);
        } else {
            jQuery('#uploading').slideUp();
            jQuery('#uploaded').slideDown();
            jQuery('#cron_id').val(data.result.files[0].cron_id);
            jQuery('#submitbutton').attr('disabled', '').removeAttr('disabled');
        }
    }).bind('fileuploadstart', function() {
        jQuery('#filechooser').slideUp();
        jQuery('#uploading').slideDown();
        jQuery('#fileupload_dropzone').fadeOut();
    });

    if (jQuery('#fileupload_reportasje').html() !== 'undefined' && jQuery('#fileupload_reportasje').html() !== undefined) {
        if (jQuery.support.cors) {
            jQuery.ajax({
                url: 'https://videoconverter.' + UKM_HOSTNAME + '/jQupload_cors.php',
                type: 'HEAD'
            }).fail(function() {
                var result = {
                    'success': false,
                    'message': 'Videoserveren er ikke tilgjengelig akkurat nå'
                };
                fileUploadError(result);
            });
        }
    }
});


jQuery(document).on('change', '#reportasje_category', function() {
    if (jQuery(this).val() == 'new') {
        jQuery('#new_album').slideDown();
    } else {
        jQuery('#new_album').slideUp();
    }
});