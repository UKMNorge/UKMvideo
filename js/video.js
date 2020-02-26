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
        urlContainer.find('input').focus();
        urlContainer.find('input').select();
    });

    if (document.execCommand("copy")) {
        urlContainer.find('input').hide();
        urlContainer.find('.copied').slideDown(120);

        // thanks to: https://stackoverflow.com/a/47421284
        var text = urlContainer.find('input').get(0); // Grab the node of the element
        var selection = window.getSelection(); // Get the Selection object
        var range = document.createRange(); // Create a new range
        range.selectNodeContents(text); // Select the content of the node from line 1
        selection.removeAllRanges(); // Delete any old ranges        
        selection.addRange(range); // Add the range to selection
        document.execCommand('copy'); // Execute the command

        setTimeout(
            function() {
                urlContainer.find('.copied').slideUp(200);
            },
            1300
        );
    }
});