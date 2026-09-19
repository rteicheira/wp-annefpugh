/**
 * AnneFPugh Therapy — base scripts.
 * Only interactive piece on the site: the mobile nav disclosure toggle.
 */
( function () {
	var toggle = document.querySelector( '.nav-toggle' );
	var nav = document.getElementById( 'primary-nav' );

	if ( ! toggle || ! nav ) {
		return;
	}

	toggle.addEventListener( 'click', function () {
		var isOpen = nav.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	} );

	nav.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && nav.classList.contains( 'is-open' ) ) {
			nav.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
			toggle.focus();
		}
	} );
} )();
