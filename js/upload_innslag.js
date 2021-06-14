////////////////////////////////////////////////////////////////////////////////////////
// INNSLAG: LAST OPP VIDEO
////////////////////////////////////////////////////////////////////////////////////////
jQuery(document).ready(function() {
    jQuery('#fileupload_band').fileupload({
        // Uncomment the following to send cross-domain cookies:
        xhrFields: { withCredentials: true },
        url: 'https://videoconverter.' + UKM_HOSTNAME + '/jQupload_recieve.php',
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

    if (jQuery('#fileupload_band').html() !== 'undefined' && jQuery('#fileupload_band').html() !== undefined) {
        if (jQuery.support.cors) {
            jQuery.ajax({
                url: 'https://videoconverter.' + UKM_HOSTNAME + '/jQupload_cors.php',
                type: 'HEAD'
            }).fail(function() {
                var result = {
                    'success': false,
                    'message': 'Beklager, videoserveren er ikke tilgjengelig akkurat nå'
                };
                fileUploadError(result);
            });
        }
    }
});