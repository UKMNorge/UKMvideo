var timers = new Array();
////////////////////////////////////////////////////////////////////////////////////////
// LISTE OVER INNSLAG
////////////////////////////////////////////////////////////////////////////////////////
	jQuery(document).on('click', '.details_show', function(e){
		e.preventDefault();
		details_show( jQuery(this).parents('li') );
	});
	
	jQuery(document).on('click', '.details_hide', function(e){
		e.preventDefault();
		details_hide( jQuery(this).parents('li') );
	});
	jQuery(document).on('change', '#film_sort_list', function(){
		window.location.href = '?page=UKMvideo&action=innslag&filter=' + jQuery(this).val();
	});
	jQuery(document).on('click', '.facebook', function(){
			window.open('//facebook.com/sharer.php?u='+jQuery(this).attr('data-url'), 'FBSHARE', 'width=500,height=300');
	});
	jQuery(document).on('click', '#add_reportasje', function(){
		window.location.href = '?page=UKMvideo&action=lastopp_reportasje';
	});
	jQuery(document).on('change', '#reportasje_category', function(){
		if(jQuery(this).val() == 'new') {
			jQuery('#new_album').slideDown();
		} else {
			jQuery('#new_album').slideUp();
		}
	});
	
	jQuery(document).on('click','.upload', function(){
		var innslag = jQuery(this).parents('li').attr('data-innslag');
		var filter = jQuery.getUrlVar('filter').split('#')[0];
		var query_string = window.location.href.split('?')[0];
		query_string = query_string.split('#')[0];
		window.location.href = query_string +
							   '?page=UKMvideo&action=lastopp_innslag&filter='+filter+'&innslag='+innslag+'&id=new';
	});
	
	function details_show( innslag ) {
		innslag.find('.details_hide').show();
		innslag.find('.details_show').hide();
		
		innslag.find('.details').slideDown();
		innslag.find('.loader').slideDown();
		
		jQuery.post(ajaxurl,
					{'action':	'UKMvideo_load',
					 'load':	'innslag_detaljer',
					 'innslag':	innslag.attr('data-innslag')
					}, function(response) {
						if(response.related.length == 0 && response.moving.length == 0 && response.converting.length == 0) {
							console.log('Innslag '+ response.id +': Ingen video');
							var hbt_video_ingen = Handlebars.compile( jQuery('#handlebars-innslag-video-ingen').html() );
							jQuery('#innslag_'+response.id).find('.loader').slideUp();
							jQuery('#innslag_'+response.id).find('.loaded').html( hbt_video_ingen( response.related ) );
						} else if (response.success) {
							console.log('Innslag '+ response.id +': Lag liste video');
							var hbt_video_liste = Handlebars.compile( jQuery('#handlebars-innslag-video-liste').html() );
							jQuery('#innslag_'+response.id).find('.loader').slideUp();
							jQuery('#innslag_'+response.id).find('.loaded').html( hbt_video_liste( response ) );
							if(response.autoreload) {
								console.log('Set timer ' + response.id);
								timers[response.id] = setTimeout(function(){videos_check(jQuery('#innslag_'+response.id))},5000);
							} else {
								console.log('Clear timer ' + response.id);
								clearTimeout(timers[response.id]);
							}
						} else {
							alert('Beklager, en feil oppsto ved henting av informasjon fra serveren. Vennligst prøv igjen');
							details_hide( jQuery('#innslag_' + response.id) );
						}
					});
	}
	function details_hide( innslag ) {
		innslag.find('.details_hide').hide();
		innslag.find('.details_show').show();
	
		innslag.find('.details').slideUp();
		innslag.find('.loader').slideUp();
		innslag.find('.loaded').html('');
		console.log('Clear timer ' + innslag.attr('data-innslag'));
		clearTimeout(timers[innslag.attr('data-innslag')]);
	}

	function videos_check( innslag ) {
		var check = new Array();
		innslag.find('.video-working').each(function(){
			check.push(jQuery(this).attr('data-video'));
		});
		
		if(check.length > 0) {
			jQuery.post(ajaxurl,
						{'action': 'UKMvideo_load',
						 'load': 'video_status',
						 'innslag': innslag.attr('data-innslag'),
						 'check': check
						},
						function(response){
							console.group('CHECK LENGTH OF ' + response.id);
							console.log(response.check);
							console.log(response.num_working);

							if(check.length != response.num_working) {
								console.log('Something different!');
								details_show( innslag );
							} else {
								timers[response.id] = setTimeout(function(){videos_check(jQuery('#innslag_'+response.id))},5000);
								console.log('Same same');
							}
							console.groupEnd();
						});
		} else {
			console.log('Clear timer ' + innslag.attr('data-innslag'));
			clearTimeout(timers[innslag.attr('data-innslag')]);
		}
	}

////////////////////////////////////////////////////////////////////////////////////////
// VIDEOER TILHØRENDE INNSLAG
////////////////////////////////////////////////////////////////////////////////////////
	jQuery(document).on('click','.videoaction.embed', function(){
	var video = jQuery(this).parents('li.video');
		video.find('.embedhowto').slideToggle();
		video.find('.embedcode').slideToggle();
	});
	
	jQuery(document).on('click','.videoaction.delete', function(){
		var video = jQuery(this).parents('li.video');
		confirmed = confirm('Er du sikker på at du vil slette denne filmen?');
		if(confirmed) {
			jQuery.post(ajaxurl, 
				{ 'action': 'UKMvideo_action',
				  'subaction': 'delete_video',
				  'tv_id': video.attr('data-video')
				},
				function(response) {
					if(response.success) {
						jQuery('#video_' + response.id).slideUp(
															function(){
																jQuery(this).destroy();
															});
					} else {
						alert('Beklager, en feil oppsto ved sletting!');
					}
				});
		}
	});
	

////////////////////////////////////////////////////////////////////////////////////////
// INNSLAG: LAST OPP VIDEO
////////////////////////////////////////////////////////////////////////////////////////
	jQuery(document).ready(function(){
	    jQuery('#fileupload_band').fileupload({
	        // Uncomment the following to send cross-domain cookies:
	        xhrFields: {withCredentials: true},
	        url: 'http://videoconverter.ukm.no/jQupload_recieve.php',
	        fileTypes: '^video\/(.)+',
	        autoUpload: true,
	        formData: {'season': jQuery('#converter_season').val(),
		        	   'pl_id': jQuery('#converter_pl_id').val(), 
		        	   'type': jQuery('#converter_type').val(),
		        	   'b_id': jQuery('#converter_b_id').val(),
		        	   'blog_id': jQuery('#converter_blog_id').val() 
		        	   },
	        progressall: function (e, data) {
		        var progress = parseInt(data.loaded / data.total * 100, 10);
		        jQuery('#uploadprogress').attr('value', progress);
		    },
	    }).bind('fileuploaddone', function(e, data){
			    if(!data.result.success) {
				    fileUploadError( data.result );
			    } else {
				   	jQuery('#uploading').slideUp();
				   	jQuery('#uploaded').slideDown();
				    jQuery('#cron_id').val(data.result.files[0].cron_id);
				    jQuery('#submitbutton').attr('disabled','').removeAttr('disabled');
				    setTimeout(function(){jQuery('#success_one_sec_please').slideDown()}, 2000);
				    jQuery('#submitbutton').parents('form').submit();
				}
		}).bind('fileuploadstart', function(){
			jQuery('#filechooser').slideUp();
			jQuery('#uploading').slideDown();
			jQuery('#fileupload_dropzone').fadeOut();
		});
		
	   if(jQuery('#fileupload_band').html() !== 'undefined' && jQuery('#fileupload_band').html() !== undefined) {
		    if (jQuery.support.cors) {
		        jQuery.ajax({
		            url: 'http://videoconverter.ukm.no/jQupload_cors.php',
		            type: 'HEAD'
		        }).fail(function () {
		        	var result = {'success': false,
			        			  'message': 'Beklager, videoserveren er ikke tilgjengelig akkurat nå'};
		            fileUploadError( result );
		        });
		    }
		}
	});

////////////////////////////////////////////////////////////////////////////////////////
// REPORTASJE: LAST OPP VIDEO
////////////////////////////////////////////////////////////////////////////////////////
	jQuery(document).ready(function(){
	    jQuery('#fileupload_reportasje').fileupload({
	        // Uncomment the following to send cross-domain cookies:
	        xhrFields: {withCredentials: true},
	        url: 'http://videoconverter.ukm.no/jQupload_recieve.php',
	        fileTypes: '^video\/(.)+',
	        autoUpload: true,
	        formData: {'season': jQuery('#converter_season').val(),
		        	   'pl_id': jQuery('#converter_pl_id').val(), 
		        	   'type': jQuery('#converter_type').val(),
		        	   'b_id': jQuery('#converter_b_id').val(),
		        	   'blog_id': jQuery('#converter_blog_id').val() 
		        	   },
	        progressall: function (e, data) {
		        var progress = parseInt(data.loaded / data.total * 100, 10);
		        jQuery('#uploadprogress').attr('value', progress);
		    },
	    }).bind('fileuploaddone', function(e, data){
			    if(!data.result.success) {
				    fileUploadError( data.result );
			    } else {
				   	jQuery('#uploading').slideUp();
				   	jQuery('#uploaded').slideDown();
				    jQuery('#cron_id').val(data.result.files[0].cron_id);
				    jQuery('#submitbutton').attr('disabled','').removeAttr('disabled');
				}
		}).bind('fileuploadstart', function(){
			jQuery('#filechooser').slideUp();
			jQuery('#uploading').slideDown();
			jQuery('#fileupload_dropzone').fadeOut();
		});
		
	   if(jQuery('#fileupload_reportasje').html() !== 'undefined' && jQuery('#fileupload_reportasje').html() !== undefined) {
		    if (jQuery.support.cors) {
		        jQuery.ajax({
		            url: 'http://videoconverter.ukm.no/jQupload_cors.php',
		            type: 'HEAD'
		        }).fail(function () {
		        	var result = {'success': false,
			        			  'message': 'Beklager, videoserveren er ikke tilgjengelig akkurat nå'};
		            fileUploadError( result );
		        });
		    }
		}
	});


////////////////////////////////////////////////////////////////////////////////////////
// FILEUPLOAD: HJELPERE
////////////////////////////////////////////////////////////////////////////////////////
	function fileUploadError(result) {
		console.error(result);
		var hbt_lastopp_error = Handlebars.compile( jQuery('#handlebars-lastopp-error').html() );
		
		jQuery('#fileupload_container').slideUp();
		
		jQuery('#fileupload_message').html( hbt_lastopp_error( result )).slideDown();
		
	}
	
	
	
	
	
	
	
	
	
	
	


/*
	jQuery(document).on('click','.videoaction.edit', function(){
		var video = jQuery(this).parents('li.video');
		var filter = jQuery.getUrlVar('filter');
		var innslag = video.parents('li').attr('data-innslag');
		var id = video.attr('data-video');
		window.location.href = window.location.href.split('?')[0] +
							   '?page=UKMvideo&action=lastopp_innslag&filter='+filter+'&innslag='+innslag+'&id='+id;
	});
*/