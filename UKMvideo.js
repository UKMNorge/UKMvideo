jQuery(document).on('click', '.details_show', function(e){
	e.preventDefault();
	details_show('#' + jQuery(this).parents('li') );
});

jQuery(document).on('click', '.details_hide', function(e){
	e.preventDefault();
	details_hide('#' + jQuery(this).parents('li') );
});


function details_show( selector ) {
	jQuery( selector ).find('.details_hide').show();
	jQuery( selector ).find('.details_show').hide();
	
	jQuery( selector ).find('.details').slideDown();
	jQuery( selector ).find('.loader').slideDown();
}
function details_hide( selector ) {
	jQuery( selector ).find('.details_hide').hide();
	jQuery( selector ).find('.details_show').show();

	jQuery( selector ).find('.details').slideUp();
	jQuery( selector ).find('.loader').slideUp();
}