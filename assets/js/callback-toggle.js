/**
 * "Request a call back" disclosure: shows the toggle button, collapses the
 * form until it's clicked, moves focus into the form when opened, and closes
 * on Escape. Without JavaScript the button stays hidden and the form shows.
 */
( function () {
	document.querySelectorAll( '.cta-band__toggle' ).forEach( ( toggle ) => {
		const panel = document.getElementById( toggle.getAttribute( 'aria-controls' ) );
		if ( ! panel ) {
			return;
		}

		const setOpen = ( open, moveFocus ) => {
			panel.hidden = ! open;
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( open && moveFocus ) {
				const first = panel.querySelector( 'input:not([type="hidden"]):not([tabindex="-1"]), textarea' );
				if ( first ) {
					first.focus();
				}
			}
		};

		toggle.hidden = false;
		setOpen( '1' === panel.dataset.open, false );

		toggle.addEventListener( 'click', () => {
			setOpen( panel.hidden, true );
		} );

		panel.addEventListener( 'keydown', ( event ) => {
			if ( 'Escape' === event.key ) {
				setOpen( false, false );
				toggle.focus();
			}
		} );
	} );
} )();
