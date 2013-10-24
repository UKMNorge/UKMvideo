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
}
function details_hide( selector ) {
	innslag.find('.details_hide').hide();
	innslag.find('.details_show').show();

	innslag.find('.details').slideUp();
	innslag.find('.loader').slideUp();
}