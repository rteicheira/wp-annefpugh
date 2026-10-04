/**
 * Services → Change Order. Drag rows (jQuery UI Sortable) or use the Move
 * up / Move down buttons; hidden inputs follow the visual order, so saving
 * just submits the form. Moves are announced to screen readers.
 */
( function ( $ ) {
	const { __, sprintf } = wp.i18n;
	const list = $( '#afp-order-list' );
	if ( ! list.length ) {
		return;
	}

	let dirty = false;

	const refresh = () => {
		const items = list.children( '.afp-order-item' );
		items.each( function ( index ) {
			const item = $( this );
			item.find( '.afp-order-item__position' ).text( index + 1 );
			item.find( '[data-direction="up"]' ).prop( 'disabled', index === 0 );
			item.find( '[data-direction="down"]' ).prop( 'disabled', index === items.length - 1 );
		} );
	};

	const announce = ( item ) => {
		const items = list.children( '.afp-order-item' );
		wp.a11y.speak(
			/* translators: 1: service title, 2: new position, 3: total services */
			sprintf( __( '%1$s moved to position %2$d of %3$d.', 'annefpugh' ), item.data( 'title' ), items.index( item ) + 1, items.length ),
			'assertive'
		);
	};

	list.sortable( {
		items: '> .afp-order-item',
		handle: '.afp-order-item__handle, .afp-order-item__title',
		placeholder: 'afp-order-item afp-order-item--placeholder',
		axis: 'y',
		update: ( event, ui ) => {
			dirty = true;
			refresh();
			announce( ui.item );
		},
	} );

	list.on( 'click', '.afp-move', function () {
		const button = $( this );
		const item = button.closest( '.afp-order-item' );
		const up = 'up' === button.data( 'direction' );
		const neighbor = up ? item.prev( '.afp-order-item' ) : item.next( '.afp-order-item' );
		if ( ! neighbor.length ) {
			return;
		}
		if ( up ) {
			item.insertBefore( neighbor );
		} else {
			item.insertAfter( neighbor );
		}
		dirty = true;
		refresh();
		announce( item );
		// Keep focus on the same button so it can be pressed repeatedly;
		// if it just became disabled (reached an end), use the other one.
		const again = item.find( `[data-direction="${ up ? 'up' : 'down' }"]` );
		( again.prop( 'disabled' ) ? item.find( `[data-direction="${ up ? 'down' : 'up' }"]` ) : again ).trigger( 'focus' );
	} );

	list.closest( 'form' ).on( 'submit', () => {
		dirty = false;
	} );
	window.addEventListener( 'beforeunload', ( event ) => {
		if ( dirty ) {
			event.preventDefault();
			event.returnValue = '';
		}
	} );

	refresh();
} )( jQuery );
