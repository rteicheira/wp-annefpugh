/**
 * Practice Info screen: photo picker (WordPress media library) and a
 * warning before leaving the page with unsaved changes.
 */
( function ( $ ) {
	const form = $( '.afp-form' );
	let dirty = false;

	form.on( 'input change', () => {
		dirty = true;
	} );
	form.on( 'submit', () => {
		dirty = false;
	} );
	window.addEventListener( 'beforeunload', ( event ) => {
		if ( dirty ) {
			event.preventDefault();
			event.returnValue = '';
		}
	} );

	$( document ).on( 'click', '.afp-image-field__select', function () {
		const button = $( this );
		const field = button.closest( '.afp-image-field' );
		const frame = wp.media( {
			title: button.data( 'title' ),
			button: { text: button.data( 'button' ) },
			library: { type: 'image' },
			multiple: false,
		} );

		frame.on( 'select', () => {
			const image = frame.state().get( 'selection' ).first().toJSON();
			const url = image.sizes && image.sizes.medium ? image.sizes.medium.url : image.url;

			field.find( '.afp-image-field__id' ).val( image.id );
			field.find( '.afp-image-field__preview' ).empty().append( $( '<img>', { src: url, alt: '' } ) );
			field.find( '.afp-image-field__remove' ).prop( 'hidden', false );
			field.find( '.afp-image-field__alt' ).prop( 'hidden', false ).find( 'input' ).val( image.alt || '' );
			dirty = true;
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.afp-image-field__remove', function () {
		const field = $( this ).closest( '.afp-image-field' );
		field.find( '.afp-image-field__id' ).val( '' );
		field.find( '.afp-image-field__preview' ).empty();
		field.find( '.afp-image-field__alt' ).prop( 'hidden', true );
		$( this ).prop( 'hidden', true );
		field.find( '.afp-image-field__select' ).trigger( 'focus' );
		dirty = true;
	} );
} )( jQuery );
