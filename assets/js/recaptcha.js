/**
 * reCAPTCHA v3 for the contact forms. Google's script loads the first time a
 * visitor interacts with a form (not on page load); on submit a fresh token
 * is fetched, put in the hidden field, and the form is sent.
 */
( function () {
	const forms = document.querySelectorAll( 'form[data-recaptcha="v3"]' );
	if ( ! forms.length ) {
		return;
	}

	const siteKey = forms[ 0 ].dataset.sitekey;
	let loading = null;

	const loadScript = () => {
		if ( ! loading ) {
			loading = new Promise( ( resolve, reject ) => {
				const script = document.createElement( 'script' );
				script.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent( siteKey );
				script.async = true;
				script.onload = () => window.grecaptcha.ready( resolve );
				script.onerror = reject;
				document.head.appendChild( script );
			} );
		}
		return loading;
	};

	// Back/forward cache can restore a submitted form with its used token and
	// disabled button; reset so the next submit fetches a fresh token.
	window.addEventListener( 'pageshow', ( event ) => {
		if ( ! event.persisted ) {
			return;
		}
		forms.forEach( ( form ) => {
			delete form.dataset.verified;
			form.querySelector( 'input[name="g-recaptcha-response"]' ).value = '';
			const button = form.querySelector( '[type="submit"]' );
			if ( button ) {
				button.disabled = false;
			}
		} );
	} );

	forms.forEach( ( form ) => {
		const tokenField = form.querySelector( 'input[name="g-recaptcha-response"]' );
		const button = form.querySelector( '[type="submit"]' );

		form.addEventListener( 'focusin', () => loadScript().catch( () => {} ), { once: true } );

		form.addEventListener( 'submit', ( event ) => {
			if ( form.dataset.verified === '1' ) {
				return;
			}
			event.preventDefault();
			if ( button ) {
				button.disabled = true;
			}

			const send = () => {
				form.dataset.verified = '1';
				form.submit();
			};

			loadScript()
				.then( () => window.grecaptcha.execute( siteKey, { action: 'contact' } ) )
				.then( ( token ) => {
					tokenField.value = token;
					send();
				} )
				// If Google can't load (blocked/offline), send anyway; the server
				// lets it through marked "[Unverified]" rather than losing it.
				.catch( send );
		} );
	} );
} )();
