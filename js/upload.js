jQuery(document).ready(function() {
    jQuery('#fileupload_reportasje').fileupload({
        // Uncomment the following to send cross-domain cookies:
        xhrFields: { withCredentials: true },
        url: 'https://videoconverter.' + UKM_HOSTNAME + '/last_opp.php',
        fileTypes: '^video\/(.)+',
        autoUpload: true,
        maxChunkSize: 10000000, // 10 MB
        progressall: function(e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            jQuery('#uploadprogress').attr('value', progress);
        },
    }).bind('fileuploaddone', function(e, data) {
        if (!data.result.success) {
            fileUploadError(data.result);
        } else {
            jQuery('#uploading').slideUp();
            jQuery('#registering').slideDown();
            jQuery.post(
                'https://videoconverter.' + UKM_HOSTNAME + '/registrer.php',
                {
                    'season': jQuery('#converter_season').val(),
                    'pl_id': jQuery('#converter_pl_id').val(),
                    'type': jQuery('#converter_type').val(),
                    'b_id': jQuery('#converter_b_id').val(),
                    'blog_id': jQuery('#converter_blog_id').val(),
                    'file': data.result.files[0].name
                }
            ).done(function(response){
                if(response.success) {
                    jQuery('#uploaded').slideDown();
                    jQuery('#registering').slideUp();
                    jQuery('#cron_id').val(response.cron_id);
                    jQuery('#submitbutton').attr('disabled', '').removeAttr('disabled');
                } else {
                    fileUploadError({
                        'success': false,
                        'message': 'Beklager, registrering av filmen feilet. Kontakt UKM Norge'
                    });
                }
            })
            .fail(function() {
                var result = {
                    'success': false,
                    'message': 'Beklager, registrering av filmen feilet. Kontakt UKM Norge'
                };
                fileUploadError(result);
            });
        }
    }).bind('fileuploadstart', function() {
        jQuery('#filechooser').slideUp();
        jQuery('#uploading').slideDown();
        jQuery('#fileupload_dropzone').fadeOut();
    });

    if (jQuery('#fileupload_reportasje').html() !== 'undefined' && jQuery('#fileupload_reportasje').html() !== undefined) {
        if (jQuery.support.cors) {
            jQuery.ajax({
                url: 'https://videoconverter.' + UKM_HOSTNAME + '/cors.php',
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
