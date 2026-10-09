/**
 * Service "Read more" overlays. Uses the native <dialog> element, which
 * traps focus, closes on Escape, and returns focus to the button that
 * opened it. Also closes on the × button, a click on the dimmed backdrop,
 * or any link inside (e.g. "Let's talk" jumping to the call-back form).
 */
( function () {
	const buttons = document.querySelectorAll( '[data-dialog-open]' );
	if ( ! buttons.length || typeof HTMLDialogElement !== 'function' ) {
		return; // Old browsers keep the button hidden; the card title links to the Services page.
	}

	buttons.forEach( ( button ) => {
		const dialog = document.getElementById( button.dataset.dialogOpen );
		if ( ! dialog ) {
			return;
		}

		button.hidden = false;
		button.addEventListener( 'click', () => {
			dialog.showModal();
			dialog.querySelector( '.service-dialog__panel' ).scrollTop = 0;
		} );

		dialog.addEventListener( 'click', ( event ) => {
			const closeButton = event.target.closest( '[data-dialog-close]' );
			const link = event.target.closest( 'a[href]' );
			// event.target is the <dialog> itself only when the backdrop area is clicked.
			if ( closeButton || link || event.target === dialog ) {
				dialog.close();
			}
		} );
	} );
} )();
