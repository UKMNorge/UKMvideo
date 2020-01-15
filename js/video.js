////////////////////////////////////////////////////////////////////////////////////////
// FILEUPLOAD: HJELPERE
////////////////////////////////////////////////////////////////////////////////////////
function fileUploadError(result) {
    return false;
    jQuery('#fileupload_container').slideUp();
    jQuery('#fileupload_message').html(twigJS_lastopperror.render(result)).slideDown();
}



jQuery(document).on('click', '.kopierUrl', function(e) {
    e.preventDefault();
    var urlContainer = jQuery(this).parents('li.film').find('.url');
    urlContainer.find('input').show();
    urlContainer.find('.copied').hide();

    urlContainer.slideDown(200, function() {
        urlContainer.find('input').focus().select();
    });

    if (document.execCommand("copy")) {
        urlContainer.find('input').hide();
        urlContainer.find('.copied').slideDown(120);

        setTimeout(
            function() {
                urlContainer.find('.copied').slideUp(200);
            },
            1300
        );
    }
});