/**
 * Mobile navigation disclosure: toggles the menu, keeps aria-expanded in
 * sync, and closes on Escape (returning focus to the toggle).
 */
( function () {
	const toggle = document.querySelector( '.nav-toggle' );
	const nav = document.getElementById( 'site-navigation' );

	if ( ! toggle || ! nav ) {
		return;
	}

	const setOpen = ( open ) => {
		nav.classList.toggle( 'is-open', open );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	};

	toggle.addEventListener( 'click', () => {
		setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
	} );

	document.addEventListener( 'keydown', ( event ) => {
		if ( 'Escape' === event.key && nav.classList.contains( 'is-open' ) ) {
			setOpen( false );
			toggle.focus();
		}
	} );
} )();
