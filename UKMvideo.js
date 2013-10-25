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
					}
				});
}
function details_hide( innslag ) {
	innslag.find('.details_hide').hide();
	innslag.find('.details_show').show();

	innslag.find('.details').slideUp();
	innslag.find('.loader').slideUp();
}