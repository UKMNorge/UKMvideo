jQuery(document).ready(
	function() {
		jQuery('.feedback').click(
			function() {
				alert(jQuery('div.feedbacktext#'+jQuery(this).attr('rel')).html());
			}
		);
	
		jQuery('.video_har').click(
			function() {
				jQuery(this).parent().parent().siblings('.innslag_left:not(.innslag_left:last)').slideToggle();
			}
		);
		
		jQuery('.videoslett').click(
			function() {
				sikker = confirm('Er du sikker?');
				if(!sikker)
					return false;
						
				jQuery(this).parent().hide(800);
				jQuery(this).parent().parent().slideUp();

				jQuery.post(ajaxurl,
							{	action: 'UKMVideo_ajax',
								cookie: encodeURIComponent(document.cookie),
								del_rel_id: jQuery(this).attr('id')
							},
							function(response){}
				);

				return false;
			}
		);
		
/*
		jQuery('.sepa').click(
			function() {
				spiller = 'video_'+jQuery(this).attr('id');
				jwplayer(spiller).setup({
					flashplayer: "/wp-content/plugins/UKMvideo/player.swf",
					file: 'http://video.ukm.no/'+jQuery('#'+spiller).attr('rel'),
					height: 270,
					width: 480,
					autostart: true,
					volume: 80
					});
			}
		);
*/
		
/*
		jQuery('.videodetaljer').click(
			function() {
				id = jQuery(this).attr('id').replace('.','');
				jQuery('#detaljvindu_'+id).slideDown(200);
				jQuery('#detaljvindu_'+id).html('<div class="loading">Vennligst vent, laster inn verkt&oslash;y</div>');	
				jQuery('#detaljvindu_'+id+' .loading').effect("pulsate", { times:3 }, 2500)
				
				jQuery.post(ajaxurl,
							{action: 'UKMVideo_ajax',cookie: encodeURIComponent(document.cookie), file: jQuery(this).attr('name'), ext: jQuery(this).attr('rel')},
							function(response){
								jQuery('#detaljvindu_'+id).html(response);
								jQuery('#detaljvindu_'+id).find('input:submit').click(
									function(){
										jQuery.post(ajaxurl,
													{action: 'UKMVideo_ajax',
													 cookie: encodeURIComponent(document.cookie),
													 data: jQuery('#detaljvindu_'+id+' #videoconverterSkjema').serialize()},
													function(saveResponse){
														jQuery('#detaljvindu_'+id).html(saveResponse);
													}
											);
										return false;
									}
								);
							}
				);
				return false;
			}
		);

*/
	jQuery('li.reportasje div.ikon-slett').live('click',function(){
		id = jQuery(this).parents('li.reportasje').attr('id');
		check = confirm('Er du sikker på at du vil slette denne?');
		if(check) {
			jQuery.post(ajaxurl, {'action': 'ukmtv_delete', 'cron_id': id}, 
				function(response){
					jQuery('li.reportasje#'+id).slideUp();
				}
			);
		}
	});

	jQuery('div.video_data div.ikon-slett').live('click',function(){
		id = jQuery(this).parents('div.video_data').attr('data-cron');
		check = confirm('Er du sikker på at du vil slette denne?');
		if(check) {
			jQuery.post(ajaxurl, {'action': 'ukmtv_delete', 'cron_id': id}, 
				function(response){
					jQuery('div.video_data#cron_'+id).slideUp();
				}
			);
		}
	});


	jQuery('li.reportasje div.ikon-rediger').live('click',function(){
		id = jQuery(this).parents('li.reportasje').attr('id');
		window.location.href = window.location.href.split("list=3&")[0] + '&id=' + id;
//				window.location.reload(); 
	});
	jQuery('li.reportasje div.icons div.ikon_detaljer').click(function(){
		id = jQuery(this).parents('li.reportasje').attr('id');
		jQuery('li#'+ id + ' div.detailed').slideDown();

		jQuery('li#'+ id + ' div.ikon_detaljer').hide();
		jQuery('li#'+ id + ' div.ikon_detaljer_skjul').show();
		
		
		jQuery.post(ajaxurl, {'action': 'load_rep_details', 'cron_id': id}, 
			function(response){
				jQuery('#details_'+id).find('div.loaded').html(response);
				jQuery('#details_'+id).find('div.loading').hide();
				jQuery('#details_'+id).find('div.loaded').show();
			}
		);
	});
	jQuery('li.reportasje div.icons div.ikon_detaljer_skjul').click(function(){
		id = jQuery(this).parents('li.reportasje').attr('id');
		jQuery('li#'+ id + ' div.detailed').slideUp();
		jQuery('li#'+ id + ' div.ikon_detaljer').show();
		jQuery('li#'+ id + ' div.ikon_detaljer_skjul').hide();
	});
	
	jQuery('.icon-embed, .related-icon-embed').live('click', function(){
		alert('Kopier denne koden:' + "\r\n\r\n" + jQuery(this).parents('div.video_data').find('div.video_embedcode').html());
	});

	jQuery('.icon-face').click(function(){
		window.open('//facebook.com/sharer.php?u='+jQuery(this).parents('li').attr('data-tv-full-url'), 'FBSHARE', 'width=500,height=300');
	});
	
	jQuery('ul.forestillingsvideo li.innslag div.ikon_detaljer').click(function(){
		id = jQuery(this).parents('li').attr('id');
		loadBandVideos(id);
		jQuery(this).hide();
		jQuery(this).parents('li').find('div.ikon_detaljer_skjul').show();

	});

	jQuery('ul.forestillingsvideo li.innslag div.ikon_detaljer_skjul').click(function(){
		jQuery(this).hide();
		jQuery(this).parents('li').find('div.ikon_detaljer').show();
		jQuery(this).parents('li').find('div.loaded').html('').hide();
	});

	jQuery('.related-icon-tv, .icon-tv').live('click',function(){
		window.open(jQuery(this).parents('div.video_data').attr('data-tv-full-url'), '_blank');
	});
	jQuery('.related-icon-face, .icon-face').live('click',function(){
		window.open('//facebook.com/sharer.php?u='+jQuery(this).parents('div.video_data').attr('data-tv-full-url'), 'FBSHARE', 'width=500,height=300');
	});
	
	
	
	jQuery('li.reportasje').each(function(){
		loadVideoData(jQuery(this).attr('id'));
		videoDataConvert(jQuery(this).attr('id'));
	});

});

function loadVideoData(video_li_id){
	update = false;
	// Not loaded any data (default HTML load result)
	data_file = jQuery('#'+video_li_id).attr('data-file');
	if(data_file == null || data_file == undefined || data_file == '' || data_file == false || data_file == 'false') {
		jQuery.ajax({
			async: true,
			type: 'POST',
			data: {'action': 'UKMVideoreportasje_data','cronid': video_li_id},
			url: ajaxurl,
			success: function(data) {
				data = jQuery.parseJSON(data);
				jQuery('#'+data.cron_id).attr('data-file', data.video);
				jQuery('#'+data.cron_id).attr('data-image', data.image);
				if(data.image != false) 
					jQuery('#'+data.cron_id).find('div.image img').attr('src', 'http://video.ukm.no/'+data.image);
				else
					jQuery('#'+data.cron_id).find('div.image img').attr('src', 'http://ico.ukm.no/grafikk/konverteres.png');
				if(!data.video) {
					setTimeout('loadVideoData('+data.cron_id+')', 10000);
				} else {
					// Video is converted and added to UKM-TV (ready to work as all others)
					loadVideoCronData(data.cron_id);
				}
			}
		});
	}
}

function loadVideoCronData(video_li_id) {
	// Not loaded tv-data
	data_cron_status = jQuery('#'+video_li_id).attr('data-cron_status');
	data_file = jQuery('#'+video_li_id).attr('data-file');
	if((data_cron_status == null || data_cron_status == undefined) && data_cron_status != '' && data_file != 'false') {
		jQuery.ajax({
			async: true,
			type: 'POST',
			data: {'cron_id': video_li_id},
			url: 'http://tv.ukm.no/finn/'+data_file,
			success: function(data) {
				data = jQuery.parseJSON(data);
				if(data.success) {
					// Video is converted and added to UKM-TV (ready to work as all others)
					videoData_loaded(data);
				} else {
					setTimeout('loadVideoCronData('+data.cron_id+')', 10000);
				}
			}
		});
	}
}

function videoData_loaded(data) {
	jQuery('li#'+data.cron_id+' div.icons div.ikon_detaljer_skjul').click();
	listItem = jQuery('li#'+data.cron_id+' div.basics');
	
	listItem.find('div.title').html(data.title);
	listItem.find('div.title').html(data.title);
	listItem.find('div.set a').attr('href', data.set_url).html(data.set);
	listItem.find('div.category a').attr('href', data.category_url).html(data.category);
}


function videoDataConvert(cron_id) {
	jQuery.ajax({
		async: true,
		type: 'GET',
		url: 'http://videoconverter.ukm.no/api/convert_status.php?c='+cron_id,
		success: function(data) {
			data = jQuery.parseJSON(data);
			if(!data.success) {
				setTimeout('videoDataConvert('+data.cron_id+')', 2000);
			} else if(data.converted) {
				jQuery('li#'+data.cron_id + ' div.status').html('');
			} else {
				jQuery('li#'+data.cron_id + ' div.status').html(data.message);
				setTimeout('videoDataConvert('+data.cron_id+')', 5000);
			}
		}
	});
}



function loadBandVideos(band_id) {
	jQuery.post(ajaxurl, {'action': 'loadBandVideos', 'b_id': band_id}, 
		function(response){
			jQuery('#details_'+band_id).find('div.loaded').html(response);
			jQuery('#details_'+band_id).find('div.loading').hide();
			jQuery('#details_'+band_id).find('div.loaded').show();
		}
	);
}


function strrpos(haystack, needle, offset) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Onno Marsman
  // +   input by: saulius
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: strrpos('Kevin van Zonneveld', 'e');
  // *     returns 1: 16
  // *     example 2: strrpos('somepage.com', '.', false);
  // *     returns 2: 8
  // *     example 3: strrpos('baa', 'a', 3);
  // *     returns 3: false
  // *     example 4: strrpos('baa', 'a', 2);
  // *     returns 4: 2
  var i = -1;
  if (offset) {
    i = (haystack + '').slice(offset).lastIndexOf(needle); // strrpos' offset indicates starting point of range till end,
    // while lastIndexOf's optional 2nd argument indicates ending point of range from the beginning
    if (i !== -1) {
      i += offset;
    }
  } else {
    i = (haystack + '').lastIndexOf(needle);
  }
  return i >= 0 ? i : false;
}

/*	//jQuery('#details_'+band_id).find('div.loading').slideDown();
	jQuery('#details_'+band_id).find('div.loaded').slideDown();
	return false;
	data = {'action': 'loadBandVideos', 'b_id': band_id};

	jQuery.post(ajaxurl, data, function(response){
		jQuery('#details_'+band_id).find('div.loaded').html(response);
		jQuery('#details_'+band_id).find('div.loading').hide();
		jQuery('#details_'+band_id).find('div.loaded').show();
		
		jQuery('#details_'+band_id+ ' div.video_preview').each(function(){
			file_id = jQuery(this).attr('id');
			jQuery.ajax({
				async: false,
				type: 'POST',
				url: 'http://tv.ukm.no/finn/'+jQuery(this).attr('data-src'),
				success: function(data) {
					data = jQuery.parseJSON(data);
					if(data.success) {
						jQuery('#'+file_id).html('<iframe src="'+data.embed_url+'" style="width:490px; height:275px;"></iframe>');
						jQuery('#'+file_id).parents('div.video_data').attr('data-tv-full-url', data.full_url);
						jQuery('#'+file_id).parents('div.video_data').attr('data-tv-embed-url', data.embed_url);
						jQuery('#'+file_id).parents('div.video_data').attr('data-tv-url', data.full_url);
						jQuery('#'+file_id).parents('div.video_data').attr('data-tv-title', data.title);
					}
				}
			});
		})
	});
}
*/