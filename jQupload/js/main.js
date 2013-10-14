/*
 * jQuery File Upload Plugin JS Example 7.0
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global jQuery, window, document */

jQuery(function () {
    ////////////////////////////////////////////////////////
    //// REGULAR VIDEO UPLOAD (UNRELATED)
    ////////////////////////////////////////////////////////
    jQuery('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        xhrFields: {withCredentials: true},
        url: 'http://videoconverter.ukm.no/jQupload_recieve.php',
        fileTypes: '^video\/(.)+',
        autoUpload: true,
        formData: {pl_id: jQuery('#converter_data_pl_id').val(),
	        	   pl_type: jQuery('#converter_data_pl_type').val() 
	        	   },
        progressall: function (e, data) {
	        var progress = parseInt(data.loaded / data.total * 100, 10);
	        jQuery('#uploadprogress').attr('value', progress);
	    },
    }).bind('fileuploaddone', function(e, data){
		    console.log(data.result);
		    if(!data.result.success) {
			    alertError(data.result.message);
		    } else {
			   	jQuery('#uploading').slideUp();
			   	jQuery('#uploaded').slideDown();
			    jQuery('#submitbutton').attr('disabled','').removeAttr('disabled');
			    jQuery('#ukm_id').val(data.result.files[0].ukm);
		    }
	}).bind('fileuploadstart', function(){
		jQuery('#filechooser').slideUp();
		jQuery('#uploading').slideDown();
	});

    // Enable iframe cross-domain access via redirect option:
    jQuery('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*jQuery/,
            '/cors/result.html?%s'
        )
    );
    
	//////////////////////////////////////////////////////////
	/// BAND RELATED UPLOAD
	//////////////////////////////////////////////////////////
	    jQuery('#fileupload_band').fileupload({
        // Uncomment the following to send cross-domain cookies:
        xhrFields: {withCredentials: true},
        url: 'http://videoconverter.ukm.no/jQupload_recieve.php',
        fileTypes: '^video\/(.)+',
        autoUpload: true,
        formData: {pl_id: jQuery('#converter_data_pl_id').val(),
	        	   pl_type: jQuery('#converter_data_pl_type').val(), 
	        	   b_id: jQuery('#converter_data_b_id').val(),
	        	   blog_id: jQuery('#converter_data_blog_id').val() 
	        	   },
        progressall: function (e, data) {
	        var progress = parseInt(data.loaded / data.total * 100, 10);
	        jQuery('#uploadprogress').attr('value', progress);
	    },
    }).bind('fileuploaddone', function(e, data){
		    if(!data.result.success) {
			    alertError(data.result.message);
		    } else {
			   	jQuery('#uploading').slideUp();
			   	jQuery('#uploaded').slideDown();
			    jQuery('#submitbutton').attr('disabled','').removeAttr('disabled');
			    jQuery('#ukm_band_id').val(data.result.files[0].ukm);
			    jQuery('#submitbutton').parents('form').submit();
			}
	}).bind('fileuploadstart', function(){
		jQuery('#filechooser').slideUp();
		jQuery('#uploading').slideDown();
	});


    function alertError(error) {
	    alert('OOPS! Noe gikk galt!'
			    	+ "\r\n" + 'Videoconverteren ga følgende feilmelding:'
			    	+ "\r\n" + error);
    }
	


    // Upload server status check for browsers with CORS support:
    if(jQuery('#fileupload').html() !== 'undefined' && jQuery('#fileupload').html() !== undefined) {
	    if (jQuery.support.cors) {
	        jQuery.ajax({
	            url: 'http://videoconverter.ukm.no/jQupload_cors.php',
	            type: 'HEAD'
	        }).fail(function () {
	            jQuery('<span class="alert alert-error"/>')
	                .text('Upload server currently unavailable - ' +
	                        new Date())
	                .appendTo('#fileupload');
	        });
	    }
	}

    // Enable iframe cross-domain access via redirect option:
    jQuery('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*jQuery/,
            '/cors/result.html?%s'
        )
    );


    // Upload server status check for browsers with CORS support:
    if(jQuery('#fileupload_band').html() !== 'undefined' && jQuery('#fileupload_band').html() !== undefined) {
	    if (jQuery.support.cors) {
	        jQuery.ajax({
	            url: 'http://videoconverter.ukm.no/jQupload_cors.php',
	            type: 'HEAD'
	        }).fail(function () {
	            jQuery('<span class="alert alert-error"/>')
	                .text('Upload server currently unavailable - ' +
	                        new Date())
	                .appendTo('#fileupload');
	        });
	    }
	}
});
