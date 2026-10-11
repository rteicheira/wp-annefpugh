/**
 * Spam protection (Settings → Spam Protection): Cloudflare Turnstile or
 * Google reCAPTCHA for the contact forms, the login page, and comments.
 * Markup comes from annefpugh_captcha_field_html().
 *
 * The provider script loads the first time a visitor focuses a protected form
 * (reCAPTCHA v2: when the form nears the viewport, since its checkbox must be
 * visible). On submit, a fresh token is put in the hidden afp_captcha_token
 * field and the form is then sent for real.
 *
 * If the provider can't load (blocked or offline) the form is sent without a
 * token; the server decides (contact forms: delivered, flagged; login: refused).
 */
( function () {
	'use strict';

	var boxes = document.querySelectorAll( '.afp-captcha' );
	if ( ! boxes.length ) {
		return;
	}

	var mode = boxes[ 0 ].dataset.mode; // turnstile | recaptcha-v3 | recaptcha-v2
	var siteKey = boxes[ 0 ].dataset.sitekey;
	var l10n = window.AFPCaptchaL10n || {};
	var loading = null;

	// How long to wait for Turnstile to finish its background check on submit.
	var TURNSTILE_WAIT_MS = 15000;

	var SCRIPTS = {
		turnstile: 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit',
		'recaptcha-v3': 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent( siteKey ),
		'recaptcha-v2': 'https://www.google.com/recaptcha/api.js?render=explicit',
	};

	function load() {
		if ( ! loading ) {
			loading = new Promise( function ( resolve, reject ) {
				var script = document.createElement( 'script' );
				script.src = SCRIPTS[ mode ];
				script.async = true;
				script.onload = function () {
					if ( mode === 'turnstile' ) {
						return window.turnstile ? resolve() : reject();
					}
					return window.grecaptcha ? window.grecaptcha.ready( resolve ) : reject();
				};
				script.onerror = reject;
				document.head.appendChild( script );
			} );
		}
		return loading;
	}

	// Error flagged so the form shows the "complete the check" message.
	function needsVisitor() {
		var err = new Error( l10n.incomplete || 'Please complete the security check.' );
		err.afpCaptcha = true;
		return err;
	}

	function settle( state, token ) {
		state.token = token;
		var waiters = state.waiters;
		state.waiters = [];
		waiters.forEach( function ( fn ) {
			fn();
		} );
	}

	// Draw the widget (Turnstile, reCAPTCHA v2) once the script is ready.
	function render( state ) {
		if ( state.widget !== null || mode === 'recaptcha-v3' ) {
			return;
		}
		var slot = state.box.querySelector( '.afp-captcha__widget' );

		if ( mode === 'turnstile' ) {
			state.widget = window.turnstile.render( slot, {
				sitekey: siteKey,
				action: state.box.dataset.action,
				appearance: 'interaction-only',
				'response-field': false,
				callback: function ( token ) {
					state.interactive = state.failed = false;
					settle( state, token );
				},
				'expired-callback': function () {
					state.token = '';
				},
				'before-interactive-callback': function () {
					state.interactive = true;
				},
				'error-callback': function () {
					state.failed = true;
					settle( state, '' );
					return true;
				},
			} );
		} else {
			state.widget = window.grecaptcha.render( slot, {
				sitekey: siteKey,
				callback: function ( token ) {
					state.token = token;
				},
				'expired-callback': function () {
					state.token = '';
				},
			} );
		}
	}

	function start( state ) {
		return load().then( function () {
			render( state );
		} );
	}

	// Resolve once Turnstile has a token (or gave up), or after a timeout.
	function waitForTurnstile( state ) {
		return new Promise( function ( resolve, reject ) {
			if ( state.token ) {
				return resolve( state.token );
			}
			if ( state.failed ) {
				return resolve( '' );
			}
			if ( state.interactive ) {
				return reject( needsVisitor() );
			}
			var timer = setTimeout( function () {
				state.waiters = state.waiters.filter( function ( fn ) {
					return fn !== done;
				} );
				// Waiting on the visitor to click the challenge vs. a stuck check.
				if ( state.interactive ) {
					reject( needsVisitor() );
				} else {
					resolve( '' );
				}
			}, TURNSTILE_WAIT_MS );
			function done() {
				clearTimeout( timer );
				resolve( state.token );
			}
			state.waiters.push( done );
		} );
	}

	function fetchToken( state ) {
		return start( state ).then(
			function () {
				if ( mode === 'recaptcha-v3' ) {
					return window.grecaptcha.execute( siteKey, { action: state.box.dataset.action } );
				}
				if ( mode === 'recaptcha-v2' ) {
					var token = window.grecaptcha.getResponse( state.widget );
					if ( ! token ) {
						throw needsVisitor();
					}
					return token;
				}
				return waitForTurnstile( state );
			},
			function () {
				return ''; // Script blocked: send without a token.
			}
		);
	}

	function reset( state ) {
		state.input.value = '';
		state.token = '';
		state.failed = false;
		if ( state.widget === null ) {
			return;
		}
		if ( mode === 'turnstile' && window.turnstile ) {
			window.turnstile.reset( state.widget );
		}
		if ( mode === 'recaptcha-v2' && window.grecaptcha ) {
			window.grecaptcha.reset( state.widget );
		}
	}

	// ── WIRE UP FORMS ──────────────────────────────────────────
	boxes.forEach( function ( box ) {
		var form = box.closest( 'form' );
		if ( ! form ) {
			return;
		}

		var state = {
			box: box,
			form: form,
			input: box.querySelector( 'input[name="afp_captcha_token"]' ),
			msg: box.querySelector( '.afp-captcha__msg' ),
			widget: null,
			token: '',
			interactive: false,
			failed: false,
			waiters: [],
		};

		if ( mode === 'recaptcha-v2' && 'IntersectionObserver' in window ) {
			var io = new IntersectionObserver(
				function ( entries ) {
					if ( entries[ 0 ].isIntersecting ) {
						io.disconnect();
						start( state ).catch( function () {} );
					}
				},
				{ rootMargin: '300px' }
			);
			io.observe( box );
		} else if ( mode === 'recaptcha-v2' ) {
			start( state ).catch( function () {} );
		} else {
			form.addEventListener(
				'focusin',
				function () {
					start( state ).catch( function () {} );
				},
				{ once: true }
			);
		}

		var button = form.querySelector( '[type="submit"]' );

		// Get a token, then submit for real.
		form.addEventListener( 'submit', function ( event ) {
			if ( form.dataset.afpVerified === '1' ) {
				return;
			}
			event.preventDefault();
			state.msg.hidden = true;
			if ( button ) {
				button.disabled = true;
			}

			fetchToken( state ).then(
				function ( token ) {
					state.input.value = token || '';
					form.dataset.afpVerified = '1';
					// A submit button named "submit" (the comment form's) shadows form.submit().
					HTMLFormElement.prototype.submit.call( form );
				},
				function ( err ) {
					if ( button ) {
						button.disabled = false;
					}
					if ( err && err.afpCaptcha ) {
						state.msg.textContent = err.message;
						state.msg.hidden = false;
						return;
					}
					// Anything else: send without a token; the server decides.
					state.input.value = '';
					form.dataset.afpVerified = '1';
					HTMLFormElement.prototype.submit.call( form );
				}
			);
		} );

		// Back/forward cache can restore a submitted form with its used token
		// and disabled button; reset so the next submit gets a fresh token.
		window.addEventListener( 'pageshow', function ( event ) {
			if ( ! event.persisted ) {
				return;
			}
			delete form.dataset.afpVerified;
			if ( button ) {
				button.disabled = false;
			}
			reset( state );
		} );
	} );
} )();
