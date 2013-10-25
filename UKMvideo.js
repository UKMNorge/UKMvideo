jQuery(document).on('click', '.details_show', function(e){
	e.preventDefault();
	details_show( jQuery(this).parents('li') );
});

jQuery(document).on('click', '.details_hide', function(e){
	e.preventDefault();
	details_hide( jQuery(this).parents('li') );
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
					if(!response.success) {
						alert('Beklager, en feil oppsto ved henting av informasjon fra serveren. Vennligst prøv igjen');
						details_hide( jQuery('#innslag_' + response.id) );
					} else if(response.related.length == 0 || response.related == undefined || response.related == null) {
						console.log('Innslag '+ response.id +': Ingen video');
						var hbt_video_ingen = Handlebars.compile( jQuery('#handlebars-innslag-video-ingen').html() );
						jQuery('#innslag_'+response.id).find('.loader').slideUp();
						jQuery('#innslag_'+response.id).find('.loaded').html( hbt_video_ingen( response.related ) );
					} else {
						console.log('Innslag '+ response.id +': Lag liste video');
						var hbt_video_liste = Handlebars.compile( jQuery('#handlebars-innslag-video-liste').html() );
						jQuery('#innslag_'+response.id).find('.loader').slideUp();
						jQuery('#innslag_'+response.id).find('.loaded').html( hbt_video_liste( response.related ) );
					}
				});
}
function details_hide( innslag ) {
	innslag.find('.details_hide').hide();
	innslag.find('.details_show').show();

	innslag.find('.details').slideUp();
	innslag.find('.loader').slideUp();
}