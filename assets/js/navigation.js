/**
 * Mobile navigation disclosure: toggles the menu, keeps aria-expanded in
 * sync, and closes on Escape (returning focus to the toggle), when a menu
 * link is followed (so same-page links like /#home-services-heading don't
 * leave the menu covering the section), and on a click outside the menu.
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

	nav.addEventListener( 'click', ( event ) => {
		if ( event.target.closest( 'a[href]' ) ) {
			setOpen( false );
		}
	} );

	document.addEventListener( 'click', ( event ) => {
		if (
			nav.classList.contains( 'is-open' ) &&
			! nav.contains( event.target ) &&
			! toggle.contains( event.target )
		) {
			setOpen( false );
		}
	} );

	document.addEventListener( 'keydown', ( event ) => {
		if ( 'Escape' === event.key && nav.classList.contains( 'is-open' ) ) {
			setOpen( false );
			toggle.focus();
		}
	} );
} )();
